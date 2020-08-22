<?php

namespace app\controllers;

use app\common\Common;
use app\models\Cashier;
use app\models\FinanceDetail;
use app\models\Order;
use app\models\SystemConfig;
use Yii;
use app\models\Refund;
use app\models\RefundSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * RefundController implements the CRUD actions for Refund model.
 */
class RefundController extends Controller
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
     * 驳回返款
     */
    public function actionRefundNo()
    {
        $admin = Yii::$app->user->identity->username;
        Yii::info(json_encode($_POST, 256), 'Refund_RefundNo_Params_' . $admin);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }
        $res = Refund::updateAll(['refund_status' => 3, 'operator' => $admin, 'update_at' => date('Y-m-d H:i:s')], ['id' => $id]);
        if ($res) {
            Yii::info(json_encode($_POST, 256), 'Refund_RefundNo_Success_' . $admin);
            return ['msg' => '修改成功', 'result' => 1];
        } else {
            return ['msg' => '修改失败', 'result' => 2];
        }
    }

    /**
     * 确认返款
     */
    public function actionRefundOk()
    {
        $admin = Yii::$app->user->identity->username;
        Yii::info(json_encode($_POST, 256), 'Refund_RefundOk_Params_' . $admin);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $refundType = Yii::$app->request->post('refundType');
        $systemRemark = Yii::$app->request->post('systemRemark');
        if (!$id || !$refundType || !in_array($refundType, [1, 2, 3])) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }

        //修改返款记录
        $refund = Refund::find()->where(['id' => $id])->one();
        if (!$refund) {
            return ['msg' => '未找到此笔返款信息', 'result' => 2];
        }

        $order = Order::find()->where(['order_id' => $refund->order_id])->one();
        if (!$order) {
            return ['msg' => '未找到此笔返款的订单信息', 'result' => 2];
        }

        $cashier = Cashier::find()->where(['username' => $order->username])->one();
        if (!$cashier) {
            return ['msg' => '未找到此笔返款的收款员信息', 'result' => 2];
        }

        $refund->refund_status = 2;
        $refund->system_remark = $systemRemark;
        $refund->refund_type = $refundType;
        $refund->operator = $admin;
        $refund->update_at = date('Y-m-d H:i:s');
        $res = $refund->save();

        $transaction = Yii::$app->db->beginTransaction();

        if ($order->order_type == 1) {
            $financeType = 4;
            $msg = '支付宝';
            $qr_type_amount = 'alipay_amount';
            $feeType = 'alipay_rate';
        } elseif ($order->order_type == 2) {
            $financeType = 3;
            $msg = '微信';
            $qr_type_amount = 'wechat_amount';
            $feeType = 'wechat_rate';
        }elseif ($order->order_type == 3) {
            $financeType = 16;
            $msg = '云闪付';
            $qr_type_amount = 'union_pay_amount';
            $feeType = 'union_pay_rate';
        }elseif ($order->order_type ==4) {
            $financeType = 19;
            $msg = '银行卡';
            $qr_type_amount = 'bank_card_amount';
            $feeType = 'bank_card_rate';
        }
        //返还额度
        $amountRes = FinanceDetail::financeCalc($order->username, $financeType, $order->order_amount, 3, $order->order_id . '返款，返还' . $msg . '额度');
        $cashierResult = Cashier::updateAllCounters([
            $qr_type_amount => $order->order_amount,
        ], ['username' => $order->username]);

        //如果是扣除佣金返款，则要减去收款员的佣金
        $resCashierfinance = 1;
        $cashierIncome = 1;
        if ($refundType == 2) {
            $fee = bcdiv(bcmul($order->order_amount, $cashier->$feeType), 100, 2);
            $resCashierfinance = FinanceDetail::financeCalc($order->username, 2, $fee * -1, 3, $order->order_id . '扣除佣金返款');
            $cashierIncome = Cashier::updateAllCounters([
                'income' => $fee * -1,
            ], ['username' => $order->username]);
        }
        Yii::info(json_encode([$res, $amountRes, $cashierResult, $resCashierfinance, $cashierIncome]), 'Refund_RefundOk_Result_' . $admin);

        $msg = '！！！请注意！！！后台账号【' . $admin . '】审核了返款【' . $refund->order_id. '】--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);

        if ($res && $amountRes && $cashierResult && $resCashierfinance && $cashierIncome) {
            $transaction->commit();
            Yii::info(json_encode($_POST, 256), 'Refund_RefundNo_Success_' . $admin);
            return ['msg' => '修改成功', 'result' => 1];
        } else {
            $transaction->rollBack();
            return ['msg' => '修改失败', 'result' => 2];
        }
    }

    /**
     * Lists all Refund models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RefundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $ApiDomain = SystemConfig::getSystemConfig('ApiDomain');
        $view = Yii::$app->view;
        $view->params['ApiDomain'] = $ApiDomain;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Refund model.
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
     * Creates a new Refund model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
        $model = new Refund();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Refund model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        die;
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Refund model.
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
     * Finds the Refund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Refund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Refund::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
