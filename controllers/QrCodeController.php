<?php

namespace app\controllers;

use app\common\Common;
use app\models\Cashier;
use app\models\LogRecord;
use app\models\SystemConfig;
use Yii;
use app\models\QrCode;
use app\models\QrCodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * QrCodeController implements the CRUD actions for QrCode model.
 */
class QrCodeController extends Controller
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
     * Lists all QrCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QrCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 批量修改状态
     */
    public function actionUpdateStatus()
    {
        $admin = Yii::$app->user->identity->username;

        Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateStatus_Params_' . $admin);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $statusX = Yii::$app->request->post('statusX');
        $ids = Yii::$app->request->post('ids');
        if (!isset($statusX) || !$ids) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }
        $res = QrCode::updateAll(['qr_status' => $statusX], ['and', ['!=', 'qr_status', 9], ['in', 'id', $ids]]);
        if ($res) {
            Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateStatus_Success_' . $admin);

            $qrs = QrCode::find()->where(['in', 'id', $ids])->asArray()->all();
            $qrs = array_column($qrs, 'qr_code');
            $msg = '！！！请注意！！！后台账号【' . $admin . '】批量修改了二维码【' . implode(' , ', $qrs) . '】的状态' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['msg' => '修改成功', 'result' => 1];
        } else {
            return ['msg' => '修改失败', 'result' => 2];
        }
    }

    /**
     * 批量修改二维码字段
     */
    public function actionUpdateColumn()
    {
        $admin = Yii::$app->user->identity->username;
        Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateColumn_Params_' . $admin);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $columnName = Yii::$app->request->post('columnName');
        $columnValue = Yii::$app->request->post('columnValue');
        $ids = Yii::$app->request->post('ids');
        if (!$columnName || !$ids || !isset($columnValue)) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }
        $res = QrCode::updateAll([$columnName => $columnValue], ['in', 'id', $ids]);
        if ($res) {
            Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateColumn_Success_' . $admin);

            $infos = [
                'per_min_amount' => '每笔最小金额',
                'per_max_amount' => '每笔最大金额',
                'per_day_amount' => '每日可收总额',
                'per_day_orders' => '每日总收笔数',
                'priority' => '优先等级',
                'allow_order_type' => '允许接单类型',
            ];
            $qrs = QrCode::find()->where(['in', 'id', $ids])->asArray()->all();
            $qrs = array_column($qrs, 'qr_code');
            $msg = '！！！请注意！！！后台账号【' . $admin . '】批量修改了二维码【' . implode(' , ', $qrs) . '】的【' . $infos[$columnName] . '】值为【' . $columnValue . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            return ['msg' => '修改成功', 'result' => 1];
        } else {
            return ['msg' => '修改失败', 'result' => 2];
        }
    }

    /**
     * 批量修改所在地
     */
    public function actionUpdateLocation()
    {
        Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateLocation_Params_' . Yii::$app->user->identity->username);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $locationX = Yii::$app->request->post('locationX');
        $names = Yii::$app->request->post('names');
        if (!$names) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }

        foreach ($names as $v) {
            if ($locationX == null) {
                Yii::$app->redis->del($v . '_redis');
            } else {
                Yii::$app->redis->set($v . '_redis', $locationX);
            }
        }
        Yii::info(json_encode($_POST, 256), 'QrOrder_UpdateLocation_Success_' . Yii::$app->user->identity->username);

        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】批量修改了二维码【' . implode(' , ', $names) . '】的【所在地】为【' . $locationX . '】--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        return ['msg' => '修改成功', 'result' => 1];
    }

    /**
     * 查看二维码当天收款详情
     */
    public function actionTodayQrDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $qrcode = Yii::$app->request->post('qrcode', '');
        $username = Yii::$app->request->post('username', '');
        $cashier = Cashier::find()->select(['wechat_rate', 'alipay_rate'])->where(['username' => $username])->one();
        if (!$username || !$cashier) {
            return ['msg' => '用户名不能为空或未查询到指定用户', 'result' => 0];
        }
        $totalMoney = Common::qrTodayMoney($qrcode);
        $successMoney = Common::qrTodayMoney($qrcode, 0, 0, 1);

        $totalTimes = Common::qrTodayTimes($qrcode);
        $successTimes = Common::qrTodayTimes($qrcode, 0, 0, 1);

        $data['总&nbsp;&nbsp;金&nbsp;&nbsp;额'] = ($totalMoney == 0 ? '0.00' : $totalMoney) . ' 元';
        $data['成功金额'] = ($successMoney == 0 ? '0.00' : $successMoney) . ' 元';
        $data['总&nbsp;&nbsp;次&nbsp;&nbsp;数'] = ($totalTimes == 0 ? '0.00' : $totalTimes) . ' 次';
        $data['成功次数'] = ($successTimes == 0 ? '0.00' : $successTimes) . ' 次';
        $data['成&nbsp;&nbsp;功&nbsp;&nbsp;率'] = ($totalTimes == 0 ? 0 : round(($successTimes / $totalTimes * 100), 2)) . '%';
        $data['预计收益'] = bcmul(($successMoney / 100), $cashier->alipay_rate, 2) . ' 元';

        return ['msg' => '查询成功', 'result' => 1, 'data' => $data];
    }

    /**
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new QrCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=二维码" . date('YmdHis') . ".xls");
        $title = [
            'ID' => ['name' => 'id', 'isChange' => 0],
            'Username' => ['name' => 'username', 'isChange' => 0],

            //加入二维码今日接单统计数据
            'Total Money' => ['name' => 'total_money', 'isChange' => 0],
            'Total Success Money' => ['name' => 'total_success_money', 'isChange' => 0],
            'Total Order' => ['name' => 'total_order', 'isChange' => 0],
            'Total Success Order' => ['name' => 'total_success_order', 'isChange' => 0],
            'Success Rate' => ['name' => 'success_rate', 'isChange' => 0],
            'Income' => ['name' => 'income', 'isChange' => 0],

            'Qr Code' => ['name' => 'qr_code', 'isChange' => 0],
            'Qr Address' => ['name' => 'qr_address', 'isChange' => 0],
            'Qr Nickname' => ['name' => 'qr_nickname', 'isChange' => 0],
            'Qr Account' => ['name' => 'qr_account', 'isChange' => 0],
            'Per Max Amount' => ['name' => 'per_max_amount', 'isChange' => 0],
            'Per Min Amount' => ['name' => 'per_min_amount', 'isChange' => 0],
            'Per Day Amount' => ['name' => 'per_day_amount', 'isChange' => 0],
            'Per Day Orders' => ['name' => 'per_day_orders', 'isChange' => 0],
            'Qr Type' => ['name' => 'qr_type', 'isChange' => 1],
            'Qr Status' => ['name' => 'qr_status', 'isChange' => 1],
            'Priority' => ['name' => 'priority', 'isChange' => 0],
            'Last Money Time' => ['name' => 'last_money_time', 'isChange' => 0],
            'Last Code Time' => ['name' => 'last_code_time', 'isChange' => 0],
            'Control' => ['name' => 'control', 'isChange' => 0],
            'Is Shopowner' => ['name' => 'is_shopowner', 'isChange' => 1],
            'Qr Location' => ['name' => 'qr_location', 'isChange' => 0],
            'Qr Relation' => ['name' => 'qr_relation', 'isChange' => 0],
            'alipay_uid' => ['name' => 'alipay_uid', 'isChange' => 0],
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
            $header .= Yii::t('app/model', $k) . $flag;
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(5000) as $values) {
            foreach ($values as $model) {

                //获取码今日接单统计数据
                $data = QrCode::getQrCodeDailyStatistics($model->qr_code);
                /*$model->total_money = $data['total_money'];
                $model->success_money = $data['success_money'];
                $model->total_times = $data['total_order'];
                $model->total_success_times = $data['total_success_order'];
                $model->success_rate = $data['success_rate'];
                $model->income = $data['income'];*/

                foreach ($title as $kk => $vv) {
                    $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                    $temp1 = $vv['name'];
                    if ($kk == 'Update_At') {
                        $flag = "\t\n";
                    } else {
                        $flag = "\t";
                    }
                    if ($vv['isChange']) {
                        //echo mb_convert_encoding(Yii::t('app', $temp)[$model->$temp1], 'GBK', 'UTF-8') . $flag;

                        $key = isset($model->$temp1) ? $model->$temp1 : $data[$vv['name']];
                        echo mb_convert_encoding(Yii::t('app', $temp)[$key], 'GBK', 'UTF-8') . $flag;
                    } else {
                        //echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;

                        $key = isset($model->$temp) ? $model->$temp : (isset($data[$vv['name']]) ? $data[$vv['name']] : '');
                        echo mb_convert_encoding($key, 'GBK', 'UTF-8') . $flag;
                    }
                }
            }
        }
        exit();
    }

    /*
     * 获取店员码
     * 需传递二维码类型 1支付宝 2微信
     */
    public function actionGetClerk()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return QrCode::getAllClerkQr(0, $_POST['qrType']);
    }

    /**
     * Displays a single QrCode model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * 查看收款员详情
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new QrCode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QrCode();
        $model->control = 0;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->qr_type == 3) {
                $maxMin = json_decode(SystemConfig::getSystemConfig('UnionPay_PerMaxMin'), 1);
            } else {
                $maxMin = json_decode(SystemConfig::getSystemConfig('PerMaxMin'), 1);
            }
            $model->qr_code = $model->qr_account . '_0' . $model->qr_type;
            $model->per_max_amount = $maxMin['max'];
            $model->per_min_amount = $maxMin['min'];
            $model->last_money_time = $model->last_code_time = date('Y-m-d H:i:s', time());

            if ($model->save()) {
                LogRecord::addLog(['添加二维码' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                $admin = Yii::$app->user->identity->username;
                $msg = '！！！请注意！！！后台账号【' . $admin . '】添加了二维码【' . $model->qr_code . '】' . '--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $cashiers = Cashier::getAllCashier();

        return $this->render('create', [
            'model' => $model,
            'cashiers' => $cashiers,
        ]);
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
        $model = $this->findModel($id);
        $temp = $model;
        if ($model->load(Yii::$app->request->post())) {
            $model->update_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                LogRecord::addLog(['修改二维码' => ['前' => $temp->toArray(), '后' => $model->toArray()]], Yii::$app->controller->route, 0, 3);
                $admin = Yii::$app->user->identity->username;
                $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了二维码【' . $model->qr_code . '】' . '--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $cashiers = Cashier::getAllCashier();
        return $this->render('update', [
            'model' => $model,
            'cashiers' => $cashiers,
        ]);
    }

    /**
     * Deletes an existing QrCode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $qr = $this->findModel($id);
        $qr->qr_status = 9;
        $qr->save();
        Yii::$app->redis->del($qr->qr_code . '_redis');
        LogRecord::addLog(['删除二维码' => $qr->toArray()], Yii::$app->controller->route, 0, 3);
        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】删除了二维码【' . $qr->qr_code . '】' . '--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        return $this->redirect(['index']);
    }

    /**
     * Finds the QrCode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return QrCode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QrCode::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }

    /**
     * 一键修改二维码状态
     */
    public function actionFastchangestatus()
    {
        \Yii::$app->response->format = 'json';
        $returnData = array(
            'result' => 0,
            'msg' => '',
            'data' => array(),
        );
        $conditions = \Yii::$app->request->post();

        $newStatus = $conditions && isset($conditions['QrCodeSearch']['statusX']) && is_numeric($conditions['QrCodeSearch']['statusX']) ? $conditions['QrCodeSearch']['statusX'] : -1;

        if (!in_array($newStatus, array(0, 1, 2, 9))) {
            $returnData['msg'] = '状态错误';
            return $returnData;
        }

        if (isset($conditions['QrCodeSearch']['statusX'])) {
            unset($conditions['QrCodeSearch']['statusX']);
        }
        $searchModel = new QrCodeSearch();
        $dataProvider = $searchModel->search($conditions);
        $qrCodes = $dataProvider->query->asArray()->all();

        if (!$qrCodes) {
            $returnData['result'] = 1;
            $returnData['msg'] = '无二维码可修改';
            return $returnData;
        }

        //执行修改
        $qrIds = array_column($qrCodes, 'id');
        $res = QrCode::updateAll(['qr_status' => $newStatus], ['in', 'id', $qrIds]);
        if ($res) {
            \Yii::info(json_encode(array('new_status' => $newStatus, 'qr_ids' => implode(',', $qrIds)), 256), 'QrCode_fastchangestatus_' . Yii::$app->user->identity->username);
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】一键修改了二维码【' . implode(' , ', array_column($qrCodes, 'qr_code')) . '】的状态为' . Yii::t('app', 'qr_status')[$newStatus] . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            $returnData['result'] = 1;
            $returnData['msg'] = '修改成功';
        } else {
            $returnData['result'] = 0;
            $returnData['msg'] = '修改失败';
        }
        return $returnData;
    }

    /**
     * 一键修改二维码各参数
     */
    public function actionFastChangeColumn()
    {
        \Yii::$app->response->format = 'json';
        $returnData = array(
            'result' => 0,
            'msg' => '',
            'data' => [],
        );
        $allCondition = \Yii::$app->request->post();
        $columnName = $allCondition['QrCodeSearch']['columnName'];
        $columnValue = $allCondition['QrCodeSearch']['columnValue'];
        unset($allCondition['QrCodeSearch']['columnName'], $allCondition['QrCodeSearch']['columnText']);
        if (empty($columnName) || !isset($columnValue)) {
            $returnData['msg'] = '请选择修改字段并填写修改字段的值';
            return $returnData;
        }

        $searchModel = new QrCodeSearch();
        $dataProvider = $searchModel->search($allCondition);
        $qrs = $dataProvider->query->asArray()->all();

        if (!$qrs) {
            $returnData['result'] = 1;
            $returnData['msg'] = '无二维码可修改';
            return $returnData;
        }

        //执行修改
        $qrIds = array_column($qrs, 'id');
        $res = QrCode::updateAll([$columnName => $columnValue], ['in', 'id', $qrIds]);
        if ($res) {
            \Yii::info(json_encode(['qr_code' => implode(',', array_column($qrs, 'username'))], 256), 'QrCode_FastChangeColumn_' . Yii::$app->user->identity->username);
            $returnData['result'] = 1;
            $admin = Yii::$app->user->identity->username;
            $infos = [
                'per_min_amount' => '每笔最小金额',
                'per_max_amount' => '每笔最大金额',
                'per_day_amount' => '每日可收总额',
                'per_day_orders' => '每日总收笔数',
                'priority' => '优先等级',
            ];
            $msg = '！！！请注意！！！后台账号【' . $admin . '】一键修改了二维码【' . implode(' , ', array_column($qrs, 'qr_code')) . '】的【' . $infos[$columnName] . '】为' . $columnValue . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            $returnData['msg'] = '修改成功';
        } else {
            $returnData['result'] = 0;
            $returnData['msg'] = '修改失败';
        }
        return $returnData;
    }


    /**
     * 二维码迁移 (将二维码移到另一码商名下)
     */
    public function actionMoveQrcodes(){

        \Yii::$app->response->format = 'json';

        $returnData = array(
            'result' => 0,
            'msg' => '迁移失败',
        );

        //接收参数
        $newCashierName = \Yii::$app->request->post('new_cashier_name');
        $qrCodes = \Yii::$app->request->post('qr_codes');

        if(!$newCashierName || !$qrCodes){
            $returnData['msg'] = '请检查参数！';
            return $returnData;
        }


        //迁移详情
        $str = '';
        foreach($qrCodes as $k=>$v){
            $str .= $str ? ',' : '';
            $str .= $v['qr_code'].'(原收款员:'.$v['username'].')';
        }


        //执行迁移
        $res = QrCode::updateAll(['username'=>$newCashierName], ['qr_code'=>$qrCodes]);
        if(is_numeric($res) && $res >= 0){
            $returnData['result'] = 1;
            $returnData['msg'] = '迁移成功';

            //实际更新条数大于0， 则记录日志， 发Telegram通知
            if($res > 0){
                $admin = Yii::$app->user->identity->username;
                LogRecord::addLog("后台用户{$admin}将码 {$str} 迁移到收款员 {$newCashierName} 名下", Yii::$app->controller->route, 0, 3);
                $msg = '！！！请注意！！！后台账号【' . $admin . '】将二维码【' . $str . '】迁移到收款员'.$newCashierName.'名下' . '--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
            }
        }else{
            //更新失败，记录异常
            \Yii::info(Common::getModelError(new QrCode()), 'qrcode/moveQrcodes-db_error');
        }

        return $returnData;

    }
}
