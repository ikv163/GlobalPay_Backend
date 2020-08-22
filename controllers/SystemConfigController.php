<?php

namespace app\controllers;

use app\common\Common;
use app\models\LogRecord;
use Yii;
use app\models\SystemConfig;
use app\models\SystemConfigSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SystemConfigController implements the CRUD actions for SystemConfig model.
 */
class SystemConfigController extends Controller
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

    public function actionLog()
    {
        Yii::$app->response->format = 'json';
        $results = LogRecord::find()->where(['like', 'info', $_GET['info']])->asArray()->all();
        var_dump(json_encode($results, 256));
        die();
    }

    //后台手动查询redis
    public function actionRedisQuery()
    {
        Yii::info(json_encode($_POST, 256), 'RedisQuery_' . Yii::$app->user->identity->username);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $queryString = $_POST['queryString'];
        $xType = $_POST['xType'];
        $redisTimedout = $_POST['redisTimedout'];
        $redisValue = $_POST['redisValue'];
        $res = null;
        $time = '无';
        if ($xType == 2) {
            if ($queryString) {
                $res = Yii::$app->redis->get($queryString);
                $time = Yii::$app->redis->ttl($queryString);
            }
        } elseif ($xType == 1) {
            if ($redisTimedout) {
                $res = Yii::$app->redis->setex($queryString, $redisTimedout, $redisValue);
                $time = $redisTimedout;
            } else {
                $res = Yii::$app->redis->set($queryString, $redisValue);
                $time = '不过期';
            }
        }

        return ['res' => $res, 'time' => $time];
    }

    /**
     * Lists all SystemConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SystemConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SystemConfig model.
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
     * Creates a new SystemConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SystemConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog(['添加配置' => $model->toArray()], Yii::$app->controller->route, 0, 3);

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】添加了后台配置【' . $model->config_name . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SystemConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $temp = $model;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog(['修改配置' => ['前' => $temp->toArray(), '后' => $model->toArray()]], Yii::$app->controller->route, 0, 3);
            Yii::$app->redis->del('config_' . $model->config_code);

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了后台配置【' . $model->config_name . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SystemConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->config_status = 2;
        $model->save();
        LogRecord::addLog(['删除配置' => $model->toArray()], Yii::$app->controller->route, 0, 3);
        Yii::$app->redis->del('config_' . $model->config_code);

        $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】删除了后台配置【' . $model->config_name . '】--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SystemConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SystemConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SystemConfig::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
