<?php

namespace app\controllers\api;

use Yii;
use app\models\Withdraw;
use app\models\WithdrawSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\httpclient\Client;
use app\models\LogRecord;
use yii\db\Exception;

/**
 * WithdrawController implements the CRUD actions for Withdraw model.
 */
class WithdrawController extends Controller
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
                    'create' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Creates a new Withdraw model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {


        \Yii::$app->response->format = 'json';
        $returnData = array(
            'result' => 0,
            'msg' => '',
            'data' => array()
        );

        try{
            LogRecord::addLog('api提交充值:'.json_encode(\Yii::$app->request->post(), JSON_UNESCAPED_UNICODE), '/api/deposit/create');

            $model = new Withdraw();
            $model->system_withdraw_id = Withdraw::generateSystemWithdrawOrderNumber();
            $model->deposit_status = 0;
            $model->insert_at = date('Y-m-d H:i:s');

            if ($model->load(\Yii::$app->request->post()) && $model->save()) {
                $returnData['result'] = 1;
                $returnData['msg'] = 'succeed';
            }else{
                $returnData['result'] = 0;
                $errors = $model->getFirstErrors();
                $firstError = reset($errors);
                $returnData['msg'] = $firstError ? $firstError : 'failed';
            }
            return $returnData;

        }catch(Exception $e){
            $returnData['result'] = 0;
            $returnData['msg'] = 'db error :'.$e->getMessage();
        }catch(\Exception $e){
            $returnData['result'] = 0;
            $returnData['msg'] = 'error :'.$e->getMessage();
        }
        return $returnData;
    }


    /**
     * Finds the Withdraw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Withdraw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Withdraw::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
