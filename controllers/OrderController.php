<?php

namespace app\controllers;

use app\common\Common;
use app\models\Cashier;
use app\models\LogRecord;
use app\models\Merchant;
use app\models\Refund;
use Yii;
use app\models\Order;
use app\models\SystemConfig;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\FinanceDetail;


/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $merchants = Merchant::find()->select(['mch_name'])->asArray()->all();
        $temp = [];
        foreach ($merchants as $v) {
            $temp[$v['mch_name']] = $v['mch_name'];
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'merchants' => $temp,
        ]);
    }

    /**
     * @return string
     * 返款详情
     */
    public function actionRefundDetail()
    {
        $params = Yii::$app->request->queryParams;
        Yii::$app->response->format = 'json';
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($params);
        $orders = $dataProvider->query->all();

        if (!$orders) {
            return ['msg' => '无订单信息', 'result' => 0];
        }

        //获取指定时间内的所有返款成功的记录
        $refunds = Refund::find()->where(['>=', 'insert_at', $params['OrderSearch']['insert_at_start']])->andWhere(['<=', 'insert_at', $params['OrderSearch']['insert_at_end']])->andWhere(['refund_status' => 2])->asArray()->all();
        if ($refunds) {
            //如果有返款记录，对比下哪些订单是没有返款的
            $refunds = array_column($refunds, 'order_id');
            foreach ($orders as $k => $v) {
                if (in_array($v->order_id, $refunds) || !(in_array($v->order_status, [2, 5]))) {
                    unset($orders[$k]);
                }
            }
        } else {
            foreach ($orders as $k => $v) {
                if (!(in_array($v->order_status, [2, 5]))) {
                    unset($orders[$k]);
                }
            }
        }

        if (!$orders) {
            return ['msg' => '当前搜索下的订单已全部返款', 'result' => 0];
        }

        return ['data' => $orders, 'result' => 1];
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * 订单成功
     */
    public function actionOrderOk()
    {
        Yii::info(json_encode($_POST, 256), 'Order_OrderOk_Params_' . Yii::$app->user->identity->username);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', '');
        $delay = Yii::$app->request->post('delay', '');
        if (!$id) {
            return ['msg' => '缺少必要参数', 'result' => 0];
        }
        $order = Order::findOne(['id' => $id]);
        if ($delay == 2) {
            if (!$order) {
                return ['msg' => '订单不存在', 'result' => 0];
            }
            $order->read_remark = $order->read_remark . '[掉单]';
            $order->save();
        }
        $res = Order::orderOk($id);

        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】手动确认了订单【' . $order->order_id . '】' . '--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);

        return $res;
    }

    /**
     * @return array
     * 稽查
     */
    public function actionChangeMoney()
    {
        Yii::info(json_encode($_POST, 256), 'Order_ChangeMoney_Params_' . Yii::$app->user->identity->username);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', '');
        $money = Yii::$app->request->post('money', '');
        if (!$id || !$money) {
            return ['msg' => '缺少必要参数', 'result' => 2];
        }
        $order = Order::findOne(['id' => $id]);
        if ($money <= 0) {
            return ['msg' => '所填写金额不能小于等于0', 'result' => 2];
        }
        if ($order->order_amount == $money) {
            return ['msg' => '所填写金额和实到金额一致，无需稽查', 'result' => 2];
        }
        if (in_array($order->order_status, [2, 4, 5])) {
            return ['msg' => '只有未支付、超时的订单才能进行稽查', 'result' => 2];
        }

        $order->actual_amount = $money;
        $order->read_remark = $order->read_remark . '[稽查掉单]';
        $order->update_at = date('Y-m-d H:i:s');

        $order_res = $order->save();

        if ($order_res) {
            Order::orderOk($id, 1);
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】手动稽查了订单【' . $order->order_id . '】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['msg' => '操作成功', 'result' => 1];
        } else {
            return ['msg' => '操作失败', 'result' => 2];
        }


    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * 订单回调
     */
    public function actionOrderNotify()
    {
        Yii::info(json_encode($_POST, 256), 'Order_OrderNotify_Params_' . Yii::$app->user->identity->username);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', '');
        if (!$id) {
            return ['msg' => '缺少必要参数', 'result' => 0];
        }
        $order = $this->findModel($id);
        if ($order == null) {
            return ['msg' => '订单不存在', 'result' => 0];
        }
        $notifyRes = Order::orderNotify($id);
        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】手动回调了订单【' . $order->order_id . '】' . '--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        if ($notifyRes === true) {
            return ['msg' => '回调成功', 'result' => 1];
        } else {
            return ['msg' => $notifyRes, 'result' => 0];
        }
    }

    /**
     * 统计
     */
    public function actionSummary()
    {
        Yii::info(json_encode(Yii::$app->request->queryParams, 256), 'allParams1');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $oriParams = \Yii::$app->request->queryParams;
        $successPost = \Yii::$app->request->queryParams;

        //总统计
        $searchModel = new OrderSearch();
        $query = $searchModel->search($oriParams, 1);
        $allMoney = $query->sum('order.order_amount');
        $successMoney = $query->sum('order.actual_amount');
        $allOrders = $query->count('order.id');

        //统计使用了多少个二维码
        $searchModel = new OrderSearch();
        $query = $searchModel->search($oriParams);
        $all = $query->query->asArray()->all();
        $qrNumbers = count(array_unique(array_column($all, 'qr_code')));


        //成功
        $searchModel = new OrderSearch();
        $successPost['OrderSearch']['order_status'] = 999;
        $querySuccess = $searchModel->search($successPost, 1);

        return [
            'allMoney' => $allMoney,
            'successMoney' => $successMoney,
            'allOrders' => $allOrders,
            'successOrders' => $querySuccess->count('order.id'),
            'qrNumbers' => $qrNumbers
        ];
    }

    /**
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Yii::info(json_encode(Yii::$app->request->queryParams, 256), 'allParams');
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=订单" . date('YmdHis') . ".xls");
        $title = [
            'order_id' => ['name' => 'order_id', 'isChange' => 0],
            'mch_order_id' => ['name' => 'mch_order_id', 'isChange' => 0],
            'Username' => ['name' => 'username', 'isChange' => 0],
            'ParentName' => ['name' => 'username', 'isChange' => 0],
            'Qr Code' => ['name' => 'qr_code', 'isChange' => 0],
            'Mch Name' => ['name' => 'mch_name', 'isChange' => 0],
            'Order Type' => ['name' => 'order_type', 'isChange' => 1],
            'Order Fee' => ['name' => 'order_fee', 'isChange' => 0],
            'CashierFee' => ['name' => 'CashierFee', 'isChange' => 0],
            'FinalMoney' => ['name' => 'FinalMoney', 'isChange' => 0],
            'Order Amount' => ['name' => 'order_amount', 'isChange' => 0],
            'Benefit' => ['name' => 'benefit', 'isChange' => 0],
            'Actual Amount' => ['name' => 'actual_amount', 'isChange' => 0],
            'Callback Url' => ['name' => 'callback_url', 'isChange' => 0],
            'Notify Url' => ['name' => 'notify_url', 'isChange' => 0],
            'Order Status' => ['name' => 'order_status', 'isChange' => 1],
            'Notify Status' => ['name' => 'notify_status', 'isChange' => 1],
            'Expire Time' => ['name' => 'expire_time', 'isChange' => 0],
            'Read Remark' => ['name' => 'read_remark', 'isChange' => 0],
            'Is Settlement' => ['name' => 'is_settlement', 'isChange' => 1],
            'Operator' => ['name' => 'operator', 'isChange' => 0],
            'Insert_At' => ['name' => 'insert_at', 'isChange' => 0],
            'Update_At' => ['name' => 'update_at', 'isChange' => 0],
        ];

        $header = '';
        foreach ($title as $k => $v) {
            if ($k == 'Update_At') {
                $flag = "\t\n";
            } else {
                $flag = "\t";
            }
            if ($k == 'ParentName') {
                $header .= '一级代理' . $flag;
            } elseif ($k == 'CashierFee') {
                $header .= '上游手续费' . $flag;
            } elseif ($k == 'FinalMoney') {
                $header .= '平台利润' . $flag;
            } else {
                $header .= Yii::t('app/model', $k) . $flag;
            }
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(1000) as $values) {
            foreach ($values as $model) {
                $fee = 0;
                foreach ($title as $kk => $vv) {
                    if ($kk == 'ParentName') {
                        $parent = Cashier::getFirstClass($model->username);
                        $parent = !$parent ? $model->username : $parent;
                        echo mb_convert_encoding($parent, 'GBK', 'UTF-8') . $flag;
                    } elseif ($kk == 'CashierFee') {
                        $parent = Cashier::getFirstClassInfos($model->username);
                        if ($model->order_type == 1) {
                            $fee = $parent['alipay_rate'];
                        } elseif ($model->order_type == 2) {
                            $fee = $parent['wechat_rate'];
                        } elseif ($model->order_type == 3) {
                            $fee = $parent['union_pay_rate'];
                        } elseif ($model->order_type == 4) {
                            $fee = $parent['bank_card_rate'];
                        }
                        $fee = bcdiv(bcmul($model->actual_amount, $fee, 2), 100, 2);
                        echo mb_convert_encoding($fee, 'GBK', 'UTF-8') . $flag;
                    } elseif ($kk == 'FinalMoney') {
                        if (in_array($model->order_status, [2, 5])) {
                            echo mb_convert_encoding(bcsub($model->order_fee, $fee, 2), 'GBK', 'UTF-8') . $flag;
                        } else {
                            echo mb_convert_encoding(0, 'GBK', 'UTF-8') . $flag;
                        }
                    } else {
                        $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                        $temp1 = $vv['name'];
                        if ($kk == 'Update_At') {
                            $flag = "\t\n";
                        } else {
                            $flag = "\t";
                        }
                        if ($kk == 'mch_order_id') {
                            $model->$temp = "'" . $model->$temp;
                        }
                        if ($vv['isChange']) {
                            echo mb_convert_encoding(Yii::t('app', $temp)[$model->$temp1], 'GBK', 'UTF-8') . $flag;
                        } else {
                            echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                        }
                    }
                }
            }
        }
        exit();
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * 订单结算
     */
    public function actionOrderIncome()
    {
        Yii::info(json_encode($_POST, 256), 'Order_OrderNotify_Params_' . Yii::$app->user->identity->username);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', '');
        if (!$id) {
            return ['msg' => '缺少必要参数', 'result' => 0];
        }
        $order = $this->findModel($id);
        if ($order == null) {
            return ['msg' => '订单不存在', 'result' => 0];
        }
        $notifyRes = Order::incomeCalc($order->username, $order->order_id, $order->order_type);
        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】手动结算了订单【' . $order->order_id . '】' . '--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        if ($notifyRes === true) {
            return ['msg' => '结算成功', 'result' => 1];
        } else {
            return ['msg' => '结算失败', 'result' => 0];
        }
    }

    /**
     * Updates an existing QrCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        die;
        $model = $this->findModel($id);
        $temp = $model;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog(['修改订单' => ['前' => $temp->toArray(), '后' => $model->toArray()]], Yii::$app->controller->route, 0, 3);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing QrCode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionNewdata($id)
    {
        $model = $this->findModel($id);
        $temp = $model;
        if ($model->load(Yii::$app->request->post())) {
            $merchant = merchant::find()->select('mch_code')->where(['mch_name' => $model->mch_name])->one();
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $orderDatas['order_id'] = self::generateOrderId($merchant->mch_code);
                $orderDatas['mch_order_id'] = $model->mch_order_id;
                $orderDatas['username'] = $model->username;
                $orderDatas['qr_code'] = $model->qr_code;
                $orderDatas['mch_name'] = $model->mch_name;
                $orderDatas['order_type'] = $model->order_type;
                $orderDatas['order_fee'] = self::calcOrderFee($merchant->mch_code, $model->order_amount, $model->order_type);
                $orderDatas['order_amount'] = $model->order_amount;
                $orderDatas['benefit'] = 0;
                $orderDatas['user_ip'] = $model->user_ip;
                $orderDatas['actual_amount'] = 0;
                $orderDatas['callback_url'] = $model->callback_url;
                $orderDatas['notify_url'] = $model->notify_url;
                $orderDatas['order_status'] = 3; //超时
                $orderDatas['notify_status'] = 1;
                $orderDatas['is_settlement'] = 0;
                $orderDatas['read_remark'] = $model->remark . $model->mch_order_id . "补单";
                $orderDatas['expire_time'] = date('Y-m-d H:i:s');
                $orderDatas['insert_at'] = empty($model->insert_at) ? date('Y-m-d H:i:s') : $model->insert_at;
                $orderDatas['operator'] = \yii::$app->user->identity->username;
                $orderDatas['update_at'] = empty($model->update_at) ? date('Y-m-d H:i:s') : $model->update_at;

                $order = new Order();
                $order->load($orderDatas, '');
                //订单入库
                $orderResult = $order->save();
                if ($orderResult) {
                    $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】手动补单【' . $order->order_id . '】' . '--' . date('Y-m-d H:i:s');
                    Common::telegramSendMsg($msg);
                } else {
                    $transaction->rollBack();
                    return '写入数据库异常' . json_encode($order->getFirstErrors(), 256);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error(json_encode(['data' => $orderDatas, 'msg' => $e->getMessage() . '-' . $e->getFile() . '-' . $e->getLine()], 256), 'Order_createOrder_Error');
                return '写入数据库异常';
            }
            $transaction->commit();
            LogRecord::addLog(['补单' => ['前' => $temp->toArray(), '后' => $order->toArray()]], Yii::$app->controller->route, 0, 3);
            return $this->redirect('index?OrderSearch[order_id]=' . $orderDatas['order_id'] . '&OrderSearch[insert_at_start]=' . $orderDatas['insert_at']);
        }

        return $this->render('newdata', [
            'model' => $model,
        ]);
    }

    //获取订单号
    public static function generateOrderId($mch_code)
    {
        return 'QMZF' . strtoupper(substr(md5(microtime() . $mch_code), 8, 16));
    }

    //计算订单的费率
    public static function calcOrderFee($mch_code, $money, $order_type)
    {
        $order_types = [1 => 'alipay_rate', 2 => 'wechat_rate', 3 => 'union_pay_rate', 4 => 'bank_card_rate'];
        $rate = $order_types[$order_type];
        $merchantRate = Merchant::find()->select([$rate])->where(['mch_code' => $mch_code])->one();
        if ($merchantRate->$rate) {
            return bcdiv(bcmul($money, $merchantRate->$rate), 100, 2);
        } else {
            return 0;
        }
    }


    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            return null;
        }
    }
}
