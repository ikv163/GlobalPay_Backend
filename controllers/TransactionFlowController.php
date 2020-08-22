<?php

namespace app\controllers;

use Yii;
use app\models\TransactionFlow;
use app\models\TransactionFlowSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransactionFlowController implements the CRUD actions for TransactionFlow model.
 */
class TransactionFlowController extends Controller
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
     * Lists all TransactionFlow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionFlowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransactionFlow model.
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
     * Creates a new TransactionFlow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::info(json_encode($_POST, 256), 'Transaction_Create' . Yii::$app->user->identity->username);
        $model = new TransactionFlow();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TransactionFlow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        Yii::info(json_encode($_POST, 256), 'Transaction_update' . Yii::$app->user->identity->username);
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TransactionFlow model.
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
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new TransactionFlowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=交易流水" . date('YmdHis') . ".xls");
        $title = [
            'Client ID' => ['name' => 'client_id', 'isChange' => 0],
            'Client Code' => ['name' => 'client_code', 'isChange' => 0],
            'Trade Type' => ['name' => 'trade_type', 'isChange' => 1],
            'trade_cate' => ['name' => 'trade_cate', 'isChange' => 1],
            'Trans ID' => ['name' => 'trans_id', 'isChange' => 0],
            'Trans Account' => ['name' => 'trans_account', 'isChange' => 0],
            'Trans Time' => ['name' => 'trans_time', 'isChange' => 0],
            'Trans Type' => ['name' => 'trans_type', 'isChange' => 1],
            'Trans Amount' => ['name' => 'trans_amount', 'isChange' => 0],
            'Trans Status' => ['name' => 'trans_status', 'isChange' => 1],
            'Trans Fee' => ['name' => 'trans_fee', 'isChange' => 0],
            'Before Balance' => ['name' => 'before_balance', 'isChange' => 0],
            'Trans Balance' => ['name' => 'trans_balance', 'isChange' => 0],
            'Trans Remark' => ['name' => 'trans_remark', 'isChange' =>0],
            'Trans Username' => ['name' => 'trans_username', 'isChange' => 0],
            'Read Remark' => ['name' => 'read_remark', 'isChange' => 0],
            'Md5 Sign' => ['name' => 'md5_sign', 'isChange' => 0],
            'Pick At' => ['name' => 'pick_at', 'isChange' => 0],
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
                    $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                    $temp1 = $vv['name'];
                    if ($kk == 'Update_At') {
                        $flag = "\t\n";
                    } else {
                        $flag = "\t";
                    }
                    if ($temp == 'trans_id'||$temp == 'trans_account') {
                        $model->$temp = "'" . $model->$temp;
                    }
                    if ($vv['isChange']) {
                        echo mb_convert_encoding(Yii::t('app', $temp)[$model->$temp1], 'GBK', 'UTF-8') . $flag;
                    } else {
                        echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                    }
                }
            }
        }
        exit();
    }

    /**
     * Finds the TransactionFlow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransactionFlow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransactionFlow::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/menu', 'The requested page does not exist.'));
    }
}
