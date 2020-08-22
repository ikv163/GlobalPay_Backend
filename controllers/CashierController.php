<?php

namespace app\controllers;

use app\common\Common;
use app\common\DES;
use app\models\LogRecord;
use app\models\QrCode;
use Yii;
use app\models\Cashier;
use app\models\CashierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CashierController implements the CRUD actions for Cashier model.
 */
class CashierController extends Controller
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
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Lists all Cashier models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CashierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $totalAlipay = $dataProvider->query->sum('alipay_amount');
        $totalWechat = $dataProvider->query->sum('wechat_amount');
        $totalSecurity = $dataProvider->query->sum('security_money');
        $totalUnionPay= $dataProvider->query->sum('union_pay_amount');
        $totalBankCard= $dataProvider->query->sum('bank_card_amount');
        $totalIncome = $dataProvider->query->sum('income');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalAlipay' => $totalAlipay,
            'totalWechat' => $totalWechat,
            'totalSecurity' => $totalSecurity,
            'totalUnionPay' => $totalUnionPay,
            'totalIncome' => $totalIncome,
            'totalBankCard' => $totalBankCard,
            'totalAmount' => $totalAlipay+$totalWechat+$totalUnionPay+$totalBankCard,
        ]);
    }

    /**
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new CashierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=收款员" . date('YmdHis') . ".xls");
        $title = [
            'Username' => ['name' => 'username', 'isChange' => 0],
            'Income' => ['name' => 'income', 'isChange' => 0],
            'Security Money' => ['name' => 'security_money', 'isChange' => 0],
            'Wechat Rate' => ['name' => 'wechat_rate', 'isChange' => 0],
            'Alipay Rate' => ['name' => 'alipay_rate', 'isChange' => 0],
            'union_pay_rate' => ['name' => 'union_pay_rate', 'isChange' => 0],
            'bank_card_rate' => ['name' => 'bank_card_rate', 'isChange' => 0],
            'Wechat Amount' => ['name' => 'wechat_amount', 'isChange' => 0],
            'Alipay Amount' => ['name' => 'alipay_amount', 'isChange' => 0],
            'union_pay_amount' => ['name' => 'union_pay_amount', 'isChange' => 0],
            'bank_card_amount' => ['name' => 'bank_card_amount', 'isChange' => 0],
            'Parent Name' => ['name' => 'parent_name', 'isChange' => 0],
            'top_parent_name' => ['name' => 'top_parent_name', 'isChange' => 0],
            'Wechat' => ['name' => 'wechat', 'isChange' => 0],
            'Alipay' => ['name' => 'alipay', 'isChange' => 0],
            'Priority' => ['name' => 'priority', 'isChange' => 0],
            'Telephone' => ['name' => 'telephone', 'isChange' => 0],
            'Agent Class' => ['name' => 'agent_class', 'isChange' => 0],
            'invite_code' => ['name' => 'invite_code', 'isChange' => 0],
            'Cashier Status' => ['name' => 'cashier_status', 'isChange' => 1],
            'Remark' => ['name' => 'remark', 'isChange' => 0],
            'Login At' => ['name' => 'login_at', 'isChange' => 0],
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
                foreach ($title as $kk => $vv) {

                    if($kk == 'top_parent_name'){
                        //取一级代理用户名
                        $firtAgent = Cashier::getFirstClass($model->username);
                        echo mb_convert_encoding($firtAgent, 'GBK', 'UTF-8') . $flag;

                    }else{
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
     * 批量修改收款员字段
     */
    public function actionUpdateColumn()
    {
        $admin = Yii::$app->user->identity->username;
        Yii::info(json_encode($_POST, 256), 'CashierOrder_UpdateColumn_Params_' . $admin);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $columnName = Yii::$app->request->post('columnName');
        $columnValue = Yii::$app->request->post('columnValue');
        $ids = Yii::$app->request->post('ids');
        if (!$columnName || !$ids || !isset($columnValue)) {
            return ['msg' => '参数不符合要求', 'result' => 2];
        }

        if ($columnName == 'canOrder') {
            $cashiers = Cashier::find()->where(['in', 'id', $ids])->select(['username'])->all();
            foreach ($cashiers as $v) {
                if ($columnValue == 1) {
                    Yii::$app->redis->set('canOrder' . $v->username, $columnValue);
                    QrCode::updateAll(['qr_status' => 1], ['username' => $v->username]);
                } else {
                    Yii::$app->redis->del('canOrder' . $v->username);
                }
            }
            $res = 1;
        } elseif ($columnName == 'depositLimit') {
            $cashiers = Cashier::find()->where(['in', 'id', $ids])->select(['username'])->all();
            foreach ($cashiers as $v) {
                Yii::$app->redis->set('depositLimit' . $v->username, $columnValue);
            }
            $res = 1;
        } else {
            $res = Cashier::updateAll([$columnName => $columnValue], ['in', 'id', $ids]);
        }
        if ($res) {
            Yii::info(json_encode($_POST, 256), 'CashierOrder_UpdateColumn_Success_' . $admin);
            $cashiers = Cashier::find()->where(['in', 'id', $ids])->select(['username'])->asArray()->all();
            $cashiers = array_column($cashiers, 'username');
            $infos = [
                'income' => '收益',
                'security_money' => '保证金',
                'wechat_amount' => '微信额度',
                'alipay_amount' => '支付宝额度',
                'union_pay_amount' => '云闪付额度',
                'bank_card_amount' => '银行卡额度',
                'priority' => '优先级',
                'canOrder' => '是否允许接单',
                'depositLimit' => '充值金额限制',
                'alipay_rate' => '支付宝费率',
                'wechat_rate' => '微信费率',
                'union_pay_rate' => '云闪付费率',
                'bank_card_rate' => '银行卡费率',
                'cashier_status' => '收款员状态',
            ];
            $msg = '！！！请注意！！！后台账号【' . $admin . '】批量修改了收款员【' . implode(' , ', $cashiers) . '】的【' . $infos[$columnName] . '】为【' . $columnValue . '】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['msg' => '修改成功', 'result' => 1];
        } else {
            return ['msg' => '修改失败', 'result' => 2];
        }
    }

    /**
     * 一键修改收款员字段
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
        $columnName = $allCondition['CashierSearch']['columnName'];
        $columnValue = $allCondition['CashierSearch']['columnValue'];
        unset($allCondition['CashierSearch']['columnName'], $allCondition['CashierSearch']['columnText']);
        if (empty($columnName) || !isset($columnValue)) {
            $returnData['msg'] = '请选择修改字段并填写修改字段的值';
            return $returnData;
        }

        $searchModel = new CashierSearch();
        $dataProvider = $searchModel->search($allCondition);
        $cashiers = $dataProvider->query->asArray()->all();

        if (!$cashiers) {
            $returnData['result'] = 1;
            $returnData['msg'] = '无收款员可修改';
            return $returnData;
        }

        //执行修改
        $cashierIds = array_column($cashiers, 'id');
        if ($columnName == 'canOrder') {
            $usernames = array_column($cashiers, 'username');
            foreach ($usernames as $v) {
                if ($columnValue == 1) {
                    Yii::$app->redis->set('canOrder' . $v, $columnValue);
                    QrCode::updateAll(['qr_status' => 1], ['username' => $v]);
                } else {
                    Yii::$app->redis->del('canOrder' . $v);
                }
            }
            $res = 1;
        } else if ($columnName == 'depositLimit') {
            $usernames = array_column($cashiers, 'username');
            foreach ($usernames as $v) {
                Yii::$app->redis->setex('depositLimit' . $v, 432000, $columnValue);
            }
            $res = 1;
        } else {
            $res = Cashier::updateAll([$columnName => $columnValue], ['in', 'id', $cashierIds]);
        }
        if ($res) {
            $admin = Yii::$app->user->identity->username;
            \Yii::info(json_encode(['params' => $allCondition, 'cashier_username' => implode(',', array_column($cashiers, 'username'))], 256), 'Cashier_fastchangestatus_' . $admin);
            $cashiers = array_column($cashiers, 'username');
            $infos = [
                'income' => '收益',
                'security_money' => '保证金',
                'wechat_amount' => '微信额度',
                'alipay_amount' => '支付宝额度',
                'union_pay_amount' => '云闪付额度',
                'bank_card_amount' => '银行卡额度',
                'priority' => '优先级',
                'canOrder' => '是否允许接单',
                'depositLimit' => '充值金额限制',
                'alipay_rate' => '支付宝费率',
                'wechat_rate' => '微信费率',
                'union_pay_rate' => '云闪付费率',
                'bank_card_rate' => '银行卡费率',
                'cashier_status' => '收款员状态',
            ];
            $msg = '！！！请注意！！！后台账号【' . $admin . '】一键修改了收款员【' . implode(' , ', $cashiers) . '】的【' . $infos[$columnName] . '】为【' . $columnValue . '】' . '--' . date('Y-m-d H:i:s');
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
     * 查看收款员当天收款详情
     */
    public function actionTodayOrdersDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $username = Yii::$app->request->post('username', '');
        $cashier = Cashier::find()->select(['wechat_rate', 'alipay_rate'])->where(['username' => $username])->one();
        if (!$username || !$cashier) {
            return ['msg' => '用户名不能为空或未查询到指定用户', 'result' => 0];
        }
        $alipayTotalMoney = Common::cashierTodayMoney($username, 1);
        $alipaySuccessMoney = Common::cashierTodayMoney($username, 1, 0, 0, 1);
        $wechatTotalMoney = Common::cashierTodayMoney($username, 2);
        $wechatSuccessMoney = Common::cashierTodayMoney($username, 2, 0, 0, 1);

        $alipayTotalTimes = Common::cashierTodayTimes($username, 1);
        $alipaySuccessTimes = Common::cashierTodayTimes($username, 1, 0, 0, 1);
        $wechatTotalTimes = Common::cashierTodayTimes($username, 2);
        $wechantSuccessTimes = Common::cashierTodayTimes($username, 2, 0, 0, 1);

        $data['支付宝-总金额'] = $alipayTotalMoney == 0 ? '0.00' : $alipayTotalMoney;
        $data['支付宝-成功金额'] = $alipaySuccessMoney == 0 ? '0.00' : $alipaySuccessMoney;
        $data['支付宝-总次数'] = $alipayTotalTimes == 0 ? '0.00' : $alipayTotalTimes;
        $data['支付宝-成功次数'] = $alipaySuccessTimes == 0 ? '0.00' : $alipaySuccessTimes;
        $data['支付宝-预计收益'] = bcmul(($alipaySuccessMoney / 100), $cashier->alipay_rate, 2);

        $data['微信-总金额'] = $wechatTotalMoney == 0 ? '0.00' : $wechatTotalMoney;
        $data['微信-成功金额'] = $wechatSuccessMoney == 0 ? '0.00' : $wechatSuccessMoney;
        $data['微信-总次数'] = $wechatTotalTimes == 0 ? '0.00' : $wechatTotalTimes;
        $data['微信-成功次数'] = $wechantSuccessTimes == 0 ? '0.00' : $wechantSuccessTimes;
        $data['微信-预计收益'] = bcmul(($wechatSuccessMoney / 100), $cashier->alipay_rate, 2);

        $data['预计总收益'] = bcadd($data['支付宝-预计收益'], $data['微信-预计收益'], 2);

        return ['msg' => '查询成功', 'result' => 1, 'data' => $data];
    }

    /**
     * Displays a single Cashier model.
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
     * 修改密码
     */
    public function actionChangePassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $login_password = Yii::$app->request->post('login_password');
        $security_password = Yii::$app->request->post('security_password');
        $id = Yii::$app->request->post('id');
        if (($login_password == null && $security_password == null) || $id == null) {
            return ['result' => 0, 'msg' => '缺少必要的参数，请检查'];
        }
        $cashier = Cashier::findOne(['id' => $id]);
        if (!$cashier) {
            return ['result' => 0, 'msg' => '此收款员不存在'];
        }
        if ($login_password) {
            $cashier->login_password = md5($login_password);
        }
        if ($security_password) {
            $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
            $cashier->pay_password = $des->encrypt($security_password);
        }
        if ($cashier->save()) {
            LogRecord::addLog(['修改收款员密码' => $_POST], Yii::$app->controller->route, 0, 3);
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了收款员【' . $cashier->username . '】的【密码】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['result' => 1, 'msg' => '修改成功'];
        } else {
            return ['result' => 0, 'msg' => '修改失败'];
        }
    }

    /**
     * Creates a new Cashier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cashier();
        $model->priority = 0;
        $msg = null;
        if ($model->load(Yii::$app->request->post())) {
            //判断代理等级、费率是否设置正确
            if ($model->agent_class && $model->parent_name) {
                $msg = Cashier::feeOrClassIsOk($model);
            }
            if ($msg == null) {
                if ($model->pay_password) {
                    $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                    $model->pay_password = $des->encrypt($model->pay_password);
                }
                $model->invite_code = Cashier::generateCashierInviteCode();
                $model->login_password = md5($model->login_password);
                if ($model->save()) {

                    $ga = new \PHPGangsta_GoogleAuthenticator();
                    $googleKey = $ga->createSecret();
                    \Yii::$app->redis->set($model->username . 'GoogleC', $googleKey);

                    LogRecord::addLog(['添加收款员' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                    $admin = Yii::$app->user->identity->username;
                    $msg = '！！！请注意！！！后台账号【' . $admin . '】添加了收款员【' . $model->username . '】' . '--' . date('Y-m-d H:i:s');
                    Common::telegramSendMsg($msg);
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $model->login_password = '';
                    $model->pay_password = '';
                }
            }
        }

        $cashiers = Cashier::getAllCashier();
        if ($cashiers && Yii::$app->request->isGet) {
            unset($cashiers[$model->username]);
        }
        return $this->render('create', [
            'model' => $model,
            'cashiers' => $cashiers,
            'msg' => $msg
        ]);
    }

    /**
     * Updates an existing Cashier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $temp = $model;
        $msg = null;
        $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);

        if (!(\Yii::$app->redis->get($model->username . 'GoogleC'))) {
            $ga = new \PHPGangsta_GoogleAuthenticator();
            $googleKey = $ga->createSecret();
            \Yii::$app->redis->set($model->username . 'GoogleC', $googleKey);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (in_array($model->cashier_status, [0, 2])) {
                Cashier::byebye($model->username);
            }

            $msg = Cashier::feeOrClassIsOk($model);

            if ($msg == null) {
                if ($model->save()) {
                    LogRecord::addLog(['修改收款员' => ['前' => $temp->toArray(), '后' => $model->toArray()]], Yii::$app->controller->route, 0, 3);
                    $admin = Yii::$app->user->identity->username;
                    $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了收款员【' . $model->username . '】的信息' . '--' . date('Y-m-d H:i:s');
                    Common::telegramSendMsg($msg);
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::info(Common::getModelError($model), 'Cashier_GetModelError_' . $id);
                }
            }
            Yii::info($msg, 'Cashier_Update_' . $id);
        }

        $model->pay_password = $des->decrypt($model->pay_password);
        $cashiers = Cashier::getAllCashier();
        if ($cashiers) {
            unset($cashiers[$model->username]);
        }
        return $this->render('update', [
            'model' => $model,
            'cashiers' => $cashiers,
            'msg' => $msg,
        ]);
    }

    /**
     * Deletes an existing Cashier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $cashier = $this->findModel($id);
        $team = Cashier::calcTeam($cashier->toArray());
        array_push($team, $cashier);
        $team = array_column($team, 'username');
        Cashier::updateAll(['cashier_status' => 2], ['in', 'username', $team]);
        Cashier::byebye($cashier->username);
        LogRecord::addLog(['删除收款员' => $cashier->toArray(), 'team' => $team], Yii::$app->controller->route, 0, 3);
        $admin = Yii::$app->user->identity->username;
        $msg = '！！！请注意！！！后台账号【' . $admin . '】删除了收款员【' . implode(' , ', $team) . '】的信息' . '--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        return $this->redirect(['index']);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * 修改收款员状态
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $statusX = Yii::$app->request->post('statusX');
        if (!in_array($statusX, [0, 1])) {
            return ['result' => 0, 'msg' => '传递的状态值不正确'];
        }

        $cashier = $this->findModel($id);
        $cashier->cashier_status = $statusX;
        if ($cashier->save()) {
            //若是禁用、删除下级， 则将下级踢下线
            if (in_array($cashier->cashier_status, array(0, 2))) {
                Cashier::byebye($cashier->username);
            }
            LogRecord::addLog(['收款员状态' => $cashier->toArray(), 'status' => $statusX], Yii::$app->controller->route, 0, 3);

            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了收款员【' . $cashier->username . '】的状态' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['result' => 1, 'msg' => '操作成功'];
        } else {
            return ['result' => 0, 'msg' => '操作失败'];
        }
    }

    /**
     * Finds the Cashier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cashier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cashier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
