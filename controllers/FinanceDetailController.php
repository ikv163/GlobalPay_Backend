<?php

namespace app\controllers;

use app\models\Cashier;
use Yii;
use app\models\FinanceDetail;
use app\models\FinanceDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FinanceDetailController implements the CRUD actions for FinanceDetail model.
 */
class FinanceDetailController extends Controller
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
     * Lists all FinanceDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FinanceDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FinanceDetail model.
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
     * Creates a new FinanceDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        die;
        $model = new FinanceDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FinanceDetail model.
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
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FinanceDetail model.
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
        $searchModel = new FinanceDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=资金交易明细" . date('YmdHis') . ".xls");
        $title = [
            'Before_Amount' => ['name' => 'before_amount', 'isChange' => 0],
            'Change_Amount' => ['name' => 'change_amount', 'isChange' => 0],
            'After_Amount' => ['name' => 'after_amount', 'isChange' => 0],
            'User_Type' => ['name' => 'user_type', 'isChange' => 1],
            'Username' => ['name' => 'username', 'isChange' => 0],
            'ParentName' => ['name' => 'username', 'isChange' => 0],
            'Finance_Type' => ['name' => 'finance_type', 'isChange' => 1],
            'Remark' => ['name' => 'remark', 'isChange' => 0],
            'Insert_At' => ['name' => 'insert_at', 'isChange' => 0],
        ];

        $header = '';
        foreach ($title as $k => $v) {
            if ($k == 'Insert_At') {
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
                        if ($kk == 'Insert_At') {
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
     * Finds the FinanceDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FinanceDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FinanceDetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
