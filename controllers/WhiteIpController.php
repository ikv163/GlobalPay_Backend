<?php

namespace app\controllers;

use app\filter\IpFilter;
use app\models\LogRecord;
use Yii;
use app\models\WhiteIp;
use app\models\WhiteIpSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WhiteIpController implements the CRUD actions for WhiteIp model.
 */
class WhiteIpController extends Controller
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
     * Lists all WhiteIp models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WhiteIpSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WhiteIp model.
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
     * Creates a new WhiteIp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WhiteIp();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                LogRecord::addLog(['添加白名单' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                Yii::$app->redis->del('whiteIp');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WhiteIp model.
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
            $model->update_at = date('Y-m-d H:i:s', time());
            if ($model->save()) {
                LogRecord::addLog(['修改白名单' => ['前'=>$temp->toArray(),'后'=>$model->toArray()]], Yii::$app->controller->route, 0, 3);
                Yii::$app->redis->del('whiteIp');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WhiteIp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $ipModel = $this->findModel($id);
        $ipModel->ip_status = 2;
        $ipModel->save();
        LogRecord::addLog(['删除白名单' => $ipModel->toArray()], Yii::$app->controller->route, 0, 3);
        Yii::$app->redis->del('whiteIp');
        return $this->redirect(['index']);
    }

    /**
     * Finds the WhiteIp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WhiteIp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WhiteIp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
