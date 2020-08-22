<?php

namespace app\controllers;

use app\models\PayChannelRelation;
use app\models\QrCode;
use Yii;
use app\models\PayChannel;
use app\models\PayChannelSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PayChannelController implements the CRUD actions for PayChannel model.
 */
class PayChannelController extends Controller
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
     * Lists all PayChannel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PayChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PayChannel model.
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
     * Creates a new PayChannel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PayChannel();

        if ($model->load(Yii::$app->request->post())) {

            $model->update_at = $model->insert_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetBankCard()
    {
        Yii::$app->response->format = 'json';
        $all = QrCode::find()->where(['qr_type' => 4])->andWhere(['qr_status' => 2])->select(['qr_code'])->asArray()->all();
        return ['data' => $all, 'result' => 1];
    }

    public function actionSetChannelBank()
    {
        try {
            Yii::$app->response->format = 'json';

            $id = $_POST['id'];
            $qr_codes = isset($_POST['bankcards']) ? $_POST['bankcards'] : null;

            if ($qr_codes == null) {
                $res = PayChannelRelation::deleteAll(['channel_id' => $id]);
                if (!$res) {
                    return ['msg' => '修改失败', 'result' => 0];
                }
            } else {

                $all = PayChannelRelation::find()->where(['channel_id' => $id])->all();

                foreach ($all as $v) {
                    $flag = 0;
                    $isHas = 0;
                    foreach ($qr_codes as $kk => $vv) {
                        if ($v->channel_id == $id && $v->qr_code == $vv) {
                            $flag++;
                            $isHas = 1;
                            unset($qr_codes[$kk]);
                            continue;
                        }
                    }
                    if ($flag == 0) {
                        $v->delete();
                    }
                }

                foreach ($qr_codes as $v) {
                    $payChannelRelation = new PayChannelRelation();

                    $payChannelRelation->channel_id = $id;
                    $payChannelRelation->insert_at = date('Y-m-d H:i:s');
                    $payChannelRelation->qr_code = $v;
                    $payChannelRelation->unique_mark = md5($id . $v);
                    $payChannelRelation->operator = Yii::$app->user->identity->username;
                    $res = $payChannelRelation->save();
                    if (!$res) {
                        return ['msg' => $payChannelRelation->getErrorSummary(false)[0], 'result' => 0];
                    }
                }
            }
        } catch (\Exception $e) {
            return ['msg' => $e->getMessage(), 'result' => 0];
        }
        return ['msg' => '操作成功', 'result' => 1];
    }

    /**
     * Updates an existing PayChannel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PayChannel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PayChannel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PayChannel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PayChannel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
