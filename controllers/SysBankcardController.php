<?php

namespace app\controllers;

use app\common\Common;
use app\models\Cashier;
use Yii;
use app\models\SysBankcard;
use app\models\SysBankcardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LogRecord;

/**
 * SysBankcardController implements the CRUD actions for SysBankcard model.
 */
class SysBankcardController extends Controller
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
     * Lists all SysBankcard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysBankcardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //$cashier = Cashier::find()->where(['cashier_status' => 1])->andWhere(['agent_class' => 1])->select(['username'])->orderBy('username ASC')->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'firstCashier' => $cashier
        ]);
    }

    /**
     * Displays a single SysBankcard model.
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
     * Creates a new SysBankcard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysBankcard();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog('新增系统银行卡:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/sys-bankcard/create');

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】添加了系统银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionBindDeposit()
    {
        try {
            Yii::$app->response->format = 'json';
            $ret = ['result' => 0, 'msg' => '', 'data' => ''];
            $username = $_POST['username'];
            $bankId = $_POST['bankId'];
            if (empty($username) || empty($bankId)) {
                $ret['msg'] = '一级代理和银行卡必须选择';
                return $ret;
            }

            Yii::$app->redis->setex('bindDeposit' . $username, 172800, $bankId);

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】为 【' . $username . '】 绑定了编号为【' . $bankId . '】系统银行卡--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            $ret['msg'] = '操作成功';
            $ret['result'] = 1;
            return $ret;
        } catch (\Exception $e) {
            $ret['msg'] = '操作异常，请联系技术';
            return $ret;
        }

    }

    /**
     * Updates an existing SysBankcard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oriModel = $model->toArray();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog('修改系统银行卡信息(' . $model->bankcard_number . '):修改前:' . json_encode($oriModel, JSON_UNESCAPED_UNICODE) . '--- 修改后:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/sys-bankcard/update', 0, 3);

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了系统银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysBankcard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /*$this->findModel($id)->delete();

        return $this->redirect(['index']);*/

        $model = $this->findModel($id);
        $model->card_status = SysBankcard::$BankCardStatusDeleted;
        $model->update_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            LogRecord::addLog('删除系统银行卡(' . $model->bankcard_number . ')', '/sys-bankcard/delete', 0, 3);
            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】删除了系统银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the SysBankcard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysBankcard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysBankcard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
