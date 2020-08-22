<?php

namespace app\controllers;

use app\common\Common;
use app\models\FinanceDetail;
use app\models\Order;
use app\models\SysBankcard;
use Yii;
use app\models\Deposit;
use app\models\DepositSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LogRecord;
use yii\db\Exception;
use app\models\Cashier;

/**
 * DepositController implements the CRUD actions for Deposit model.
 */
class DepositController extends Controller
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
     * Lists all Deposit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepositSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $datas = $dataProvider->query->all();
        $statusReady = $statusDoing = $statusSuccess = $statusFailed = $statusReject = 0;
        $statusReadyCount = $statusDoingCount = $statusSuccessCount = $statusFailedCount = $statusRejectCount = 0;
        foreach ($datas as $v) {
            switch ($v->deposit_status) {
                case 0:
                    $statusReady += $v->deposit_money;
                    $statusReadyCount++;
                    break;
                case 1:
                    $statusDoing += $v->deposit_money;
                    $statusDoingCount++;
                    break;
                case 2:
                    $statusSuccess += $v->deposit_money;
                    $statusSuccessCount++;
                    break;
                case 3:
                    $statusFailed += $v->deposit_money;
                    $statusFailedCount++;
                    break;
                case 4:
                    $statusReject += $v->deposit_money;
                    $statusRejectCount++;
                    break;
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusReady' => $statusReady,
            'statusDoing' => $statusDoing,
            'statusSuccess' => $statusSuccess,
            'statusFailed' => $statusFailed,
            'statusReject' => $statusReject,
            'statusReadyCount' => $statusReadyCount,
            'statusDoingCount' => $statusDoingCount,
            'statusSuccessCount' => $statusSuccessCount,
            'statusFailedCount' => $statusFailedCount,
            'statusRejectCount' => $statusRejectCount,
        ]);
    }

    /**
     * Displays a single Deposit model.
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
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new DepositSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=收款员充值记录" . date('YmdHis') . ".xls");
        $title = [
            'System_Deposit_ID' => ['name' => 'system_deposit_id', 'isChange' => 0],
            'Out_Deposit_ID' => ['name' => 'out_deposit_id', 'isChange' => 0],
            'Receiving_card_number' => ['name' => 'receiving_card_number', 'isChange' => 0],
            'Receiving_card_owner_name' => ['name' => 'receiving_card_owner_name', 'isChange' => 0],
            'Card_from' => ['name' => 'card_from', 'isChange' => 0],
            'Username' => ['name' => 'username', 'isChange' => 0],
            'ParentName' => ['name' => 'username', 'isChange' => 0],
            'Deposit_Amount' => ['name' => 'deposit_money', 'isChange' => 0],
            'Order_Status' => ['name' => 'deposit_status', 'isChange' => 1],
            'User_Remark' => ['name' => 'deposit_remark', 'isChange' => 0],
            'System_Remark' => ['name' => 'system_remark', 'isChange' => 0],
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
            } else {
                $header .= Yii::t('app/model', $k) . $flag;
            }
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(5000) as $values) {
            foreach ($values as $model) {
                foreach ($title as $kk => $vv) {
                    if ($kk == 'ParentName') {
                        $parent = Cashier::getFirstClass($model->username);
                        $parent = !$parent ? $model->username : $parent;
                        echo mb_convert_encoding($parent, 'GBK', 'UTF-8') . $flag;
                    }elseif(in_array($kk , array('Receiving_card_number', 'Receiving_card_owner_name', 'Card_from'))){
                        //查询收款卡信息
                        if($model->handle_type == 2 && $model->system_bankcard_id > 0){
                            $sysCardInfo = SysBankcard::find()->where(['id'=>$model->system_bankcard_id])->asArray()->one();
                            $receivingCardNumber = is_array($sysCardInfo) && $sysCardInfo && isset($sysCardInfo['bankcard_number']) && $sysCardInfo['bankcard_number'] ? $sysCardInfo['bankcard_number'] : '';
                            $receivingCardOwnerName = is_array($sysCardInfo) && $sysCardInfo && isset($sysCardInfo['bankcard_owner']) && $sysCardInfo['bankcard_owner'] ? $sysCardInfo['bankcard_owner'] : '';
                            $cardSource = isset($sysCardInfo['card_owner']) && isset(\Yii::t('app', 'sys_bankcard_owner')[$sysCardInfo['card_owner']]) ? \Yii::t('app', 'sys_bankcard_owner')[$sysCardInfo['card_owner']] : '';

                        }else{
                            $receivingCardNumber = isset($model->third_bank_account) && $model->third_bank_account ? $model->third_bank_account : '';
                            $receivingCardOwnerName = '';
                            $cardSource = '自动充值渠道';
                        }

                        switch($kk){
                            case 'Receiving_card_number':
                                $columnValue = "=\"{$receivingCardNumber}\"";
                                break;
                            case 'Receiving_card_owner_name':
                                $columnValue = $receivingCardOwnerName;
                                break;
                            case 'Card_from':
                                $columnValue = $cardSource;
                                break;
                            default :
                                $columnValue = '';
                                break;
                        }

                        echo mb_convert_encoding($columnValue, 'GBK', 'UTF-8') . $flag;

                    }else {
                        $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                        $temp1 = $vv['name'];
                        if ($kk == 'Update_At') {
                            $flag = "\t\n";
                        } else {
                            $flag = "\t";
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
     * Creates a new Deposit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
        $model = new Deposit();
        $model->system_deposit_id = Deposit::generateSystemDepositOrderNumber();
        $model->deposit_status = 0;
        $model->insert_at = date('Y-m-d H:i:s');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog('新增充值订单:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/deposit/create', 0, 3);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Deposit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oriModel = $model->toArray();
        $model->update_at = date('Y-m-d H:i:s');


        if ($model->load(Yii::$app->request->post())) {

            if ($oriModel['deposit_status'] != $model->deposit_status) {
                $availableChangeStatus = Deposit::getAvailableChangeStatus($oriModel['deposit_status']);
                if (!($availableChangeStatus && in_array($model->deposit_status, $availableChangeStatus))) {
                    //return $this->render('update', ['model' => $model, 'msg'=>'不允许修改到该状态']);
                    $model->deposit_status = $oriModel['deposit_status'];
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }

            if ($model->save()) {
                LogRecord::addLog("修改充值订单($id):修改前:" . json_encode($oriModel, JSON_UNESCAPED_UNICODE) . '---修改后:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/deposit/update', 0, 3);
                $admin = Yii::$app->user->identity->username;
                $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了【' . $model->username . '】的充值订单【' . $model->system_deposit_id . '】的信息' . '--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Deposit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        die;
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Deposit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deposit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deposit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 手工修改订单状态
     * @param array $orders
     * @return array
     */
    public function actionSetorderstatus()
    {

        \Yii::info(json_encode(\Yii::$app->request->get(), JSON_UNESCAPED_UNICODE), 'deposit-setorderstatus-params');

        $id = \Yii::$app->request->get('id');

        $lockKey = $id . 'Setorderstatus';
        $isContinue = Common::redisLock($lockKey, 2);
        if ($isContinue === false) {
            return $this->redirect(['index']);
        }

        $newOrderStatus = \Yii::$app->request->get('new_status');

        if (!in_array($newOrderStatus, [2, 3])) {
            return $this->redirect(['index']);
        }
        //开启数据库事务
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $order = Deposit::findOne(['id' => $id]);

            //验证订单id有效性
            if (!$order || $order->deposit_status !== 0) {
                \Yii::warning('订单id不合法：' . $id, 'deposit_setorderstatus_check_order_id');
                return $this->redirect(['index']);
            }

            //执行修改
            $res = Deposit::updateAll([
                'deposit_status' => $newOrderStatus,
                'update_at' => date('Y-m-d H:i:s'),
            ], [
                'deposit_status' => 0, 'id' => $id
            ]);

            if (!$res) {
                $transaction->rollBack();
                return $this->redirect(['index']);
            }

            if ($newOrderStatus == Deposit::$OrderStatusSucceed) {

                //写入资金交易明细
                if (!FinanceDetail::financeCalc($order->username, FinanceDetail::$FinanceTypeMargin, $order->deposit_money, FinanceDetail::$UserTypeCashier, '存款==》保证金变动')) {
                    throw new \Exception('写入存款资金交易明细失败:' . $order['order_id']);
                }

                //更新收款员余额
                if (!Cashier::updateCashierBalance($order->username, $order->deposit_money, 'security_money')) {
                    throw new \Exception('更新余额失败:' . $order['order_id']);
                }

            }

            //写入操作日志
            LogRecord::addLog("更新订单状态_修改前:" . json_encode($order->toArray(), JSON_UNESCAPED_UNICODE) . "_修改后：" . json_encode($order->toArray(), JSON_UNESCAPED_UNICODE), '/deposit/setorderstatus', 0, 3);

            $transaction->commit();
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】审批了【' . $order->username . '】的充值订单【' . $order->system_deposit_id . '】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['index']);
        } catch (\Exception $baseException) {
            $transaction->rollBack();
            \Yii::error('error:' . $baseException->getMessage(), 'deposit-storderstatus-error' . $id);
            return $this->redirect(['index']);
        }
    }

    /**
     * 手工修改订单状态
     * @param array $orders
     * @return array
     */
    public function actionSetorderstatus11()
    {
        die;
        \Yii::$app->response->format = 'json';

        \Yii::info(json_encode(\Yii::$app->request->post(), JSON_UNESCAPED_UNICODE), 'deposit-setorderstatus-params');

        //定义最终的返回数据
        $returnData = array(
            'result' => 0,
            'msg' => '更新失败_1',
        );

        $orders = \Yii::$app->request->post('orders');
        $newOrderStatus = \Yii::$app->request->post('new_status');

        //开启数据库事务
        $transaction = \Yii::$app->db->beginTransaction();

        try {

            if (count($orders) > 0 && is_numeric($newOrderStatus) && intval($newOrderStatus) == $newOrderStatus && $newOrderStatus >= 0) {
                foreach ($orders as $key => $order) {

                    //验证订单id有效性
                    if (!(isset($order['order_id']) && is_numeric($order['order_id']) && intval($order['order_id']) == $order['order_id'] && $order['order_id'] > 0)) {
                        \Yii::warning('订单id不合法：' . $order['order_id'], 'deposit_setorderstatus_check_order_id');
                        continue;
                    }

                    //验证订单原状态的有效性
                    if (!(isset($order['old_status']) && is_numeric($order['old_status']) && intval($order['old_status']) == $order['old_status'] && in_array($order['old_status'], array_keys(Deposit::$OrderStatusRel)))) {
                        \Yii::warning('订单原状态参数不合法：' . $order['order_id'] . '__' . $order['old_status'], 'deposit_setorderstatus-order_old_status');
                        continue;
                    }

                    //查询订单信息， 比对订单状态和传过来的原状态是否一致
                    $orderInfo = $this->findModel($order['order_id']);
                    $oriOrderInfo = clone $orderInfo;
                    if (!($orderInfo && isset($orderInfo->deposit_status) && $orderInfo->deposit_status == $order['old_status'])) {
                        \Yii::warning('订单原状态参数与db中的状态不一致：' . $order['order_id'] . '__' . $order['old_status'] . '__' . $orderInfo->deposit_status, 'deposit_setorderstatus_order_old_status_not_match');
                        continue;
                    }

                    //根据原状态，获取可以更新的状态， 判断新状态是否允许
                    $availableStatus = Deposit::getAvailableChangeStatus($order['old_status']);
                    if (!($availableStatus && in_array($newOrderStatus, $availableStatus))) {
                        \Yii::warning('订单不允许修改到新状态：' . $order['order_id'] . '__' . $orderInfo->deposit_status . '__' . $newOrderStatus, 'deposit_setorderstatus_order_new_status_not_allowed');
                        continue;
                    }

                    //执行修改
                    $orderInfo->deposit_status = $newOrderStatus;
                    $orderInfo->system_deposit_id = md5($orderInfo->system_remark);
                    if (!$orderInfo->save()) {
                        throw new \Exception('更新状态失败:' . $order['order_id']);
                    }

                    $orderInfo->update_at = date('Y-m-d H:i:s');
                    $orderInfo->save();

                    if ($newOrderStatus == Deposit::$OrderStatusSucceed) {

                        //写入资金交易明细
                        if (!FinanceDetail::financeCalc($orderInfo->username, FinanceDetail::$FinanceTypeMargin, $orderInfo->deposit_money, FinanceDetail::$UserTypeCashier, '存款==》保证金变动')) {
                            throw new \Exception('写入存款资金交易明细失败:' . $order['order_id']);
                        }

                        //更新收款员余额
                        if (!Cashier::updateCashierBalance($orderInfo->username, $orderInfo->deposit_money, 'security_money')) {
                            throw new \Exception('更新余额失败:' . $order['order_id']);
                        }

                    }

                    //写入操作日志
                    LogRecord::addLog("更新订单状态_修改前:" . json_encode($oriOrderInfo->toArray(), JSON_UNESCAPED_UNICODE) . "_修改后：" . json_encode($orderInfo->toArray(), JSON_UNESCAPED_UNICODE), '/deposit/setorderstatus');
                }

                $transaction->commit();

                $returnData['result'] = 1;
                $returnData['msg'] = '更新成功';
            } else {
                $returnData['result'] = 0;
                $returnData['msg'] = '未选择订单或状态!';
            }

            return $returnData;

        } catch (\Exception $baseException) {
            $transaction->rollBack();
            $returnData['result'] = 0;
            $returnData['msg'] = '修改失败';
            \Yii::error('error:' . $baseException->getMessage(), 'deposit-storderstatus-error');
        }

        return $returnData;

    }

}
