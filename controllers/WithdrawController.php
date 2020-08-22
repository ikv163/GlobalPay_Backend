<?php

namespace app\controllers;

use app\common\Common;
use app\models\UserBankcard;
use Yii;
use app\models\Withdraw;
use app\models\WithdrawSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LogRecord;
use app\models\FinanceDetail;
use app\models\Cashier;
use yii\db\Exception;

/**
 * WithdrawController implements the CRUD actions for Withdraw model.
 */
class WithdrawController extends Controller
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
     * Lists all Withdraw models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WithdrawSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new WithdrawSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=收款员提现记录" . date('YmdHis') . ".xls");
        $title = [
            'System_Withdraw_ID' => ['name' => 'system_withdraw_id', 'isChange' => 0],
            'Out_Deposit_ID' => ['name' => 'out_withdraw_id', 'isChange' => 0],
            'Username' => ['name' => 'username', 'isChange' => 0],
            'ParentName' => ['name' => 'username', 'isChange' => 0],
            'Withdraw_Amount' => ['name' => 'withdraw_money', 'isChange' => 0],
            'Order_Status' => ['name' => 'withdraw_status', 'isChange' => 1],
            'User_Type' => ['name' => 'user_type', 'other' => 'user_type_withdraw', 'isChange' => 1],
            'bankcard_id' => ['name' => 'bankcard_id', 'isChange' => 0],
            'User_Remark' => ['name' => 'withdraw_remark', 'isChange' => 0],
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
                    } else {
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
     * Displays a single Withdraw model.
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
     * Creates a new Withdraw model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
        $model = new Withdraw();
        $model->system_withdraw_id = Withdraw::generateSystemWithdrawOrderNumber();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog('新增提款订单:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/withdraw/create');
            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】添加了【' . $model->username . '】的提现订单【' . $model->system_withdraw_id . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Withdraw model.
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

            if ($oriModel['withdraw_status'] != $model->withdraw_status) {
                $availableChangeStatus = Withdraw::getAvailableChangeStatus($oriModel['withdraw_status']);
                if (!($availableChangeStatus && in_array($model->withdraw_status, $availableChangeStatus))) {
                    //return $this->render('update', ['model' => $model, 'msg'=>'不允许修改到该状态']);
                    $model->withdraw_status = $oriModel['withdraw_status'];
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }

            if ($model->save()) {
                LogRecord::addLog("修改充值订单($id):修改前:" . json_encode($oriModel, JSON_UNESCAPED_UNICODE) . '---修改后:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/withdraw/update');

                $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了【' . $model->username . '】的提现订单【' . $model->system_withdraw_id . '】的信息--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $cardNumber = '';
        $bankCard = UserBankcard::find()->where(array('id' => $model->bankcard_id))->asArray()->one();
        if ($bankCard && isset($bankCard['bankcard_number']) && $bankCard['bankcard_number']) {
            $cardNumber = $bankCard['bankcard_number'];
        }
        $model->withdraw_bankcard_number = $cardNumber;

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Withdraw model.
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
     * Finds the Withdraw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Withdraw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Withdraw::findOne($id)) !== null) {
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

        \Yii::$app->response->format = 'json';

        \Yii::info(json_encode(\Yii::$app->request->post(), JSON_UNESCAPED_UNICODE), 'deposit_setorderstatus_params');

        //定义最终的返回数据
        $returnData = array(
            'result' => 0,
            'msg' => '更新失败_1',
        );

        $orders = \Yii::$app->request->post('orders');

        $lockKey = 'WSetorderstatus';
        $isContinue = Common::redisLock($lockKey, 2);
        if ($isContinue === false) {
            return $this->redirect(['index']);
        }

        $newOrderStatus = \Yii::$app->request->post('new_status');

        //开启数据库事务
        $transaction = \Yii::$app->db->beginTransaction();

        try {

            if (count($orders) > 0 && is_numeric($newOrderStatus) && intval($newOrderStatus) == $newOrderStatus && $newOrderStatus >= 0) {
                foreach ($orders as $key => $order) {

                    //验证订单id有效性
                    if (!(isset($order['order_id']) && is_numeric($order['order_id']) && intval($order['order_id']) == $order['order_id'] && $order['order_id'] > 0)) {
                        \Yii::warning('订单id不合法：' . $order['order_id'], 'withdraw_setorderstatus-check_order_id');
                        continue;
                    }

                    //验证订单原状态的有效性
                    if (!(isset($order['old_status']) && is_numeric($order['old_status']) && intval($order['old_status']) == $order['old_status'] && in_array($order['old_status'], array_keys(Withdraw::$OrderStatusRel)))) {
                        \Yii::warning('订单原状态参数不合法：' . $order['old_status'], 'withdraw_setorderstatus_order_old_status');
                        continue;
                    }

                    //查询订单信息， 比对订单状态和传过来的原状态是否一致
                    $orderInfo = $this->findModel($order['order_id']);

                    $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】审核了【' . $orderInfo->username . '】的提现订单【' . $orderInfo->system_withdraw_id . '】的信息--' . date('Y-m-d H:i:s');
                    Common::telegramSendMsg($msg);

                    $oriOrderInfo = clone $orderInfo;
                    if (!($orderInfo && isset($orderInfo['withdraw_status']) && $orderInfo['withdraw_status'] == $order['old_status'])) {
                        \Yii::warning('订单原状态参数与db中的状态不一致：' . $order['old_status'] . '_' . $orderInfo['withdraw_status'], 'withdraw_setorderstatus_order_old_status_not_match');
                        continue;
                    }

                    //根据原状态，获取可以更新的状态， 判断新状态是否允许
                    $availableStatus = Withdraw::getAvailableChangeStatus($order['old_status']);
                    if (!($availableStatus && in_array($newOrderStatus, $availableStatus))) {
                        \Yii::warning('订单不允许修改到新状态：' . $orderInfo['withdraw_status'] . '--' . $newOrderStatus, 'withdraw/setorderstatus-order_new_status_not_allowed');
                        continue;
                    }

                    //根据新状态来处理写入资金交易明细和更新余额所需的参数
                    switch ($newOrderStatus) {
                        case Withdraw::$OrderStatusSucceed:
                            $changeMoney = $orderInfo->withdraw_money * (-1);
                            $updateAmountField = 'security_money';
                            break;

                        case Withdraw::$OrderStatusFailed:

                        case Withdraw::$OrderStatusRefused:
                            $changeMoney = $orderInfo->withdraw_money + $orderInfo->handling_fee;
                            $updateAmountField = 'security_money';
                            break;

                        default:
                            $changeMoney = 0;
                            $updateAmountField = '';
                            break;
                    }

                    if ($changeMoney === 0 || $updateAmountField === '') {
                        throw new \Exception('状态错误!');
                    }

                    //执行修改
                    $orderInfo->withdraw_status = $newOrderStatus;
                    $orderInfo->update_at = date('Y-m-d H:i:s');
                    if (!$orderInfo->save()) {
                        $errors = $orderInfo->getFirstErrors();
                        $error = reset($errors);
                        $error = $error ? $error : '更新状态失败';
                        throw new \Exception("{$error}:" . $order['order_id']);
                    }


                    //更新成功状态不需要写入资金明细和更新余额(生成取款订单时处理)
                    if (in_array($newOrderStatus, array(Withdraw::$OrderStatusFailed, Withdraw::$OrderStatusRefused))) {

                        //写入资金交易明细
                        if (!FinanceDetail::financeCalc($orderInfo->username, FinanceDetail::$FinanceTypeWithdrawReturn, $changeMoney, FinanceDetail::$UserTypeCashier, '提款==>>返还')) {
                            throw new \Exception('写入提款资金交易明细失败:' . $order['order_id']);
                        }

                        //更新收款员余额
                        if (!Cashier::updateCashierBalance($orderInfo->username, $changeMoney, 'security_money')) {
                            throw new \Exception('更新余额失败:' . $order['order_id']);
                        }

                    }

                    //写入操作日志
                    LogRecord::addLog("更新订单状态_修改前:" . json_encode($oriOrderInfo->toArray(), JSON_UNESCAPED_UNICODE) . "_修改后：" . json_encode($orderInfo->toArray(), JSON_UNESCAPED_UNICODE), '/withdraw/setorderstatus');
                }

                $transaction->commit();

                $returnData['result'] = 1;
                $returnData['msg'] = '更新成功';
            } else {
                $returnData['result'] = 0;
                $returnData['msg'] = '未选择订单或状态!';
            }

            return $returnData;

        } catch (Exception $dbException) {
            $transaction->rollBack();
            $returnData['result'] = 0;
            $returnData['msg'] = '数据库错误';
            \Yii::error('db_error:' . $dbException->getMessage(), 'withdraw-storderstatus-db-error');
        } catch (\Exception $baseException) {
            $transaction->rollBack();
            $returnData['result'] = 0;
            $returnData['msg'] = '修改失败';
            \Yii::error('error:' . $baseException->getMessage(), 'withdraw-storderstatus-error');
        }


        return $returnData;
    }


    /**
     * 人工审核订单， 提交到typay自动出款
     * @params   int           $id              自增id
     * @params   string        $withdrawNo      系统订单号
     * @return   array
     */
    public function actionAutoWithdraw(){

        \Yii::$app->response->format = 'json';

        $admin = \Yii::$app->user->identity->username;
        $id = \Yii::$app->request->post('id', 0);
        $systemWithdrawId = \Yii::$app->request->post('withdraw_no', '');

        \Yii::info(json_encode(array('admin'=>$admin, 'id'=>$id, 'system_order_id'=>$systemWithdrawId), 256), 'auto_withdraw_params');

        $returnData = array(
            'result' => 0,
            'msg' => '',
            'data' => array(),
        );


        try{

            if(!(is_numeric($id) && intval($id) == $id && $id > 0 && $systemWithdrawId)){
                $returnData['result'] = 0;
                $returnData['msg'] = '参数错误';
                return $returnData;
            }


            //防重复操作
            $redisLockKey = 'auto_withdraw_'.$id;
            if (Common::redisLock($redisLockKey, 3) === false) {
                $returnData['result'] = 0;
                $returnData['msg'] = '操作频繁，3秒后再试';
                return $returnData;
            }

            //验证订单信息
            $params = array(
                ':id' => $id,
                ':withdraw_no'=>$systemWithdrawId,
            );
            $fields = "withdraw.*, cashier.id as user_id , user_bankcard.bank_code, user_bankcard.bankcard_number, user_bankcard.bankcard_address, user_bankcard.bankcard_owner";
            $orderInfo = Withdraw::find()
                ->select($fields)
                ->leftJoin('user_bankcard', 'withdraw.bankcard_id = user_bankcard.id')
                ->leftJoin('cashier', 'withdraw.username = cashier.username')
                ->where('withdraw.id = :id and system_withdraw_id = :withdraw_no', $params)
                ->asArray()->one();

            \Yii::info(json_encode(array('db_order'=>$orderInfo, 'order_no'=>$systemWithdrawId), 256), 'auto_withdraw_db_order');

            if(!$orderInfo){
                $returnData['result'] = 0;
                $returnData['msg'] = '订单不存在';
                return $returnData;
            }

            if(!(isset($orderInfo['withdraw_status']) && is_numeric($orderInfo['withdraw_status']) && $orderInfo['withdraw_status'] == Withdraw::$OrderStatusInit && isset($orderInfo['out_withdraw_id']) && !$orderInfo['out_withdraw_id'])){
                $returnData['result'] = 0;
                $returnData['msg'] = '订单不允许自动出款_1';
                return $returnData;
            }

            //校验订单生成时间  （匹配订单中有时间限制， 所以此处也需要做时间限制）
            $createTime = isset($orderInfo['insert_at']) && $orderInfo['insert_at'] ? strtotime($orderInfo['insert_at']) : 0;
            $expireTime = $createTime + 1800;
            if($expireTime <= time()){
                $returnData['result'] = 0;
                $returnData['msg'] = '订单不允许自动出款_2';
                return $returnData;
            }


            //组合数据，提交到typay取款接口
            $postData = array(
                'merchant_id'=>\Yii::$app->params['typay_merchant_id'],
                'merchant_order_id' => $orderInfo['system_withdraw_id'],
                'user_level' => 0,
                'pay_type' => 1,  //1银行卡转账，  888备付金转账
                'pay_amt' => sprintf('%.2f', $orderInfo['withdraw_money']),
                'bank_code' => $orderInfo['bank_code'],
                'bank_num' => $orderInfo['bankcard_number'],
                'bank_owner' => $orderInfo['bankcard_owner'],
                'bank_address' => $orderInfo['bankcard_address'],
                'user_id' => $orderInfo['user_id'],
                'user_ip' => $orderInfo['user_ip'],
                'remark' => isset($orderInfo['withdraw_remark']) && $orderInfo['withdraw_remark'] ? mb_substr($orderInfo['withdraw_remark'], 0, 5) : '',
            );

            $postRes = Withdraw::sendWithdrawOrder($postData);

            \Yii::info(json_encode($postRes, 256), 'auto_withdraw_post_res');

            //提交成功， 更新订单中的三方订单号及三方创建状态
            if ($postRes && isset($postRes['pay_message']) && $postRes['pay_message'] == 1 && isset($postRes['typay_order_id']) && $postRes['typay_order_id']) {

                Withdraw::updateAll(
                    array(
                        'out_withdraw_id'=>$postRes['typay_order_id'],
                        'third_create_status' => 1,
                    ),
                    array(
                        'id'=>$id,
                        'system_withdraw_id' =>$systemWithdrawId,
                    )
                );

                $returnData['result'] = 1;
                $returnData['msg'] = '提交成功';
            }else{
                $returnData['result'] = 0;
                $returnData['msg'] = '提交自动出款失败，请手动处理';
            }

            return $returnData;

        }catch(\Exception $e){
            \Yii::info($systemWithdrawId.'---'.$e->getMessage(), 'auto_withdraw_exception');
            $returnData['result'] = 0;
            $returnData['msg'] = '服务异常:'.$e->getMessage();
        }
        return $returnData;

    }
}
