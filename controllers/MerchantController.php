<?php

namespace app\controllers;

use app\common\Common;
use app\common\DES;
use app\models\LogRecord;
use Yii;
use app\models\Merchant;
use app\models\MerchantSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MerchantController implements the CRUD actions for Merchant model.
 */
class MerchantController extends Controller
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
     * Lists all Merchant models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MerchantSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     * 修改商户状态
     */
    public function actionChangeStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $statusX = Yii::$app->request->post('statusX');
        if (!in_array($statusX, [0, 1])) {
            return ['result' => 0, 'msg' => '传递的状态值不正确'];
        }

        $model = $this->findModel($id);
        $model->mch_status = $statusX;
        if ($model->save()) {
            LogRecord::addLog(['商户状态' => $model->toArray(), 'status' => $statusX], Yii::$app->controller->route, 0, 3);
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了商户【' . $model->mch_name . '】的信息' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return ['result' => 1, 'msg' => '操作成功'];
        } else {
            return ['result' => 0, 'msg' => '操作失败'];
        }
    }

    /**
     * Displays a single Merchant model.
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
     * Creates a new Merchant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Merchant();

        if ($model->load(Yii::$app->request->post())) {
            $model->update_at = $model->insert_at = date('Y-m-d H:i:s', time());
            if (isset($model->pay_password) && $model->pay_password != null) {
                $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                $model->pay_password = $des->encrypt($model->pay_password);
            }
            $model->save();
            Yii::info('管理员【' . Yii::$app->user->identity->username . '】添加商户:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE));
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】新增了商户【' . $model->mch_name . '】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Merchant model.
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
            if (isset($model->pay_password) && $model->pay_password != null) {
                $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                $model->pay_password = $des->encrypt($model->pay_password);
            }
            $model->save();
            Yii::info('管理员【' . Yii::$app->user->identity->username . '】修改商户:前-' . json_encode($temp->toArray(), JSON_UNESCAPED_UNICODE) . '-后' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE));
            $admin = Yii::$app->user->identity->username;
            $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了商户【' . $model->mch_name . '】的信息' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Merchant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Merchant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Merchant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Merchant::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
