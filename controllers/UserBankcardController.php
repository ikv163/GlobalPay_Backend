<?php

namespace app\controllers;

use app\common\Common;
use Yii;
use app\models\UserBankcard;
use app\models\UserBankcardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LogRecord;

/**
 * UserBankcardController implements the CRUD actions for UserBankcard model.
 */
class UserBankcardController extends Controller
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
     * Lists all UserBankcard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserBankcardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserBankcard model.
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
     * Creates a new UserBankcard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserBankcard();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            LogRecord::addLog('新增银行卡:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/user-bankcard/create', 0, 3);
            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】添加了收款员【' . $model->username . '】提现银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 导出excel
     */
    public function actionExport()
    {
        set_time_limit(0);
        $searchModel = new UserBankcardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=用户银行卡" . date('YmdHis') . ".xls");
        $title = [
            'Bankcard_Number' => ['name' => 'bankcard_number', 'isChange' => 0],
            'Bankcard_Owner' => ['name' => 'bankcard_owner', 'isChange' => 0],
            'Bank_Code' => ['name' => 'bank_code', 'isChange' => 1],
            'Bankcard_Address' => ['name' => 'bankcard_address', 'isChange' => 0],
            'Card_Status' => ['name' => 'card_status', 'isChange' => 1],
            'Username' => ['name' => 'username', 'isChange' => 0],
            'User_Type' => ['name' => 'user_type', 'other' => 'user_type_bank', 'isChange' => 1],
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
                    if ($vv['isChange']) {
                        echo mb_convert_encoding(Yii::t('app', $temp)[$model->$temp1], 'GBK', 'UTF-8') . $flag;
                    } else {
                        echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                    }
                }
            }
        }
        die;
    }

    /**
     * Updates an existing UserBankcard model.
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
            LogRecord::addLog('修改银行卡信息(' . $model->bankcard_number . '):修改前:' . json_encode($oriModel, JSON_UNESCAPED_UNICODE) . '--- 修改后:' . json_encode($model->toArray(), JSON_UNESCAPED_UNICODE), '/user-bankcard/update', 0, 3);

            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了收款员【' . $model->username . '】提现银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserBankcard model.
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
        $model->card_status = UserBankcard::$BankCardStatusDeleted;
        $model->update_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            LogRecord::addLog('删除银行卡(' . $model->bankcard_number . ')', '/user-bankcard/delete');
            $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】删除了收款员【' . $model->username . '】提现银行卡【' . $model->bankcard_number . '】--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserBankcard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserBankcard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserBankcard::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
