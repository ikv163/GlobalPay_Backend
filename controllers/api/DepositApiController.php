<?php

namespace app\controllers\api;

use Yii;
use app\models\Deposit;
use app\models\DepositSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LogRecord;

/**
 * DepositController implements the CRUD actions for Deposit model.
 */
class DepositController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['*'],
                        'allow' => true,
                    ],
                ],
            ],

            /*'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['POST'],
                ],
            ],*/
        ];
    }


    /**
     * 上游回调通知
     *
     * Array
        (
            [merchant_id] => 1000002
            [merchant_order_id] => ZPY20191220161017MTSBW
            [typay_order_id] => CWYB0201912201610392710
            [pay_type] => 116
            [pay_amt] => 300.00
            [pay_paid_amt] => 300.00
            [pay_message] => 1
            [bank_code] =>
            [remark] => 微信个码
            [sign] => 44f36e1fd1764f5864def08e1a45c0a0
        )
    */
    public function actionNotify()
    {
        //接收参数
        $data = \Yii::$app->request->bodyParams;
        \Yii::info($data, 'DepositApi/notify-params');
        $data = json_decode($data, true);

        //验签
        if(!$this->checksign($data)){
            return 'failed';
        }

        //开启事务 ：  修改订单状态 ->  写入资金交易明细  ->  更新可用额度




    }


    //生成签名
    private function checksign($data){

        try{
            \Yii::info(json_encode($data,JSON_UNESCAPED_UNICODE), 'DepositApi/getsign-params');

            if(!(is_array($data) && $data)){
                return false;
            }

            if(!(isset($data['merchant_id']) && isset($data['merchant_order_id']) && isset($data['typay_order_id']) && isset($data['pay_type']) && isset($data['pay_amt']) && isset($data['pay_paid_amt']) && isset($data['sign']) )){
                return false;
            }

            if(!($data['merchant_id'] && $data['merchant_order_id'] && $data['typay_order_id'] && $data['pay_type'] && $data['pay_amt'] && $data['pay_paid_amt'] && $data['sign'])){
                return false;
            }

            $key = \Yii::$app->params['merchant_key'];

            $rawStr='merchant_id=' . $data['merchant_id'] . '&merchant_order_id=' .
                $data['merchant_order_id'] . '&typay_order_id=' . $data['typay_order_id'] .
                '&pay_type=' . $data['pay_type'] . '&pay_amt=' . $data['pay_amt'] . '&pay_paid_amt=' .
                $data['pay_paid_amt'] . '&pay_message=' . $data['pay_message'] . '&bank_code=' . $data['bank_code'] .
                '&remark=' . $data['remark'] . '&key=' . $key;
            $sign = md5($rawStr);

            \Yii::info($rawStr);
            \Yii::info($key);
            \Yii::info($sign);
            return $sign == $data['sign'];

        }catch(\Exception $e){
            \Yii::error($e->getMessage(),'DepositApi/getsign-error');
        }

        return false;
    }


    /**
     * Finds the Deposit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deposit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deposit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
