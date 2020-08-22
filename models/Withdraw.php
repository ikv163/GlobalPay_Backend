<?php

namespace app\models;

use Yii;
use app\common\Common;

/**
 * This is the model class for table "withdraw".
 *
 * @property int $id
 * @property string $system_withdraw_id 系统订单ID
 * @property string $out_withdraw_id 外部订单ID
 * @property string $username 提款人用户名
 * @property int $user_type 用户类型 1商户 2收款员
 * @property float $withdraw_money 提款金额
 * @property int $bankcard_id 银行卡ID（user_bankcard）
 * @property int $withdraw_status 提现状态 0创建 1处理中 2成功 3失败 4驳回
 * @property string|null $withdraw_remark 用户提款备注
 * @property string|null $system_remark 系统备注
 * @property string|null $insert_at 提现时间
 * @property string|null $update_at 修改时间
 */
class Withdraw extends \yii\db\ActiveRecord
{

    public $withdraw_bankcard_number;

    //定义订单状态 : 0创建  1处理中  2成功  3失败  4驳回
    public static $OrderStatusInit = 0;
    public static $OrderStatusProcessing = 1;
    public static $OrderStatusSucceed = 2;
    public static $OrderStatusFailed = 3;
    public static $OrderStatusRefused = 4;

    public static $OrderStatusRel = array(
        '0' => '创建',
        '1' => '处理中',
        '2' => '成功',
        '3' => '失败',
        '4' => '驳回',
    );

    //定义每个状态对应的可以修改的状态
    public static $AvailableStatus = array(
        '0' => array(1,2,3,4),
        '1' => array(2,3,4),
        '2' => array(),
        '3' => array(),
        '4' => array(),
    );

    //提款用户类型
    public static $UserTypeMerchant = 1;
    public static $UserTypeCashier = 2;

    public static $UserTypeRel = array(
        '1' => '商户',
        '2' => '收款员',
    );


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'withdraw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_type', 'bankcard_id', 'withdraw_status'], 'integer'],
            [['withdraw_money'], 'number'],
            [['insert_at', 'update_at', 'withdraw_bankcard_number','query_team'], 'safe'],
            [['system_withdraw_id', 'out_withdraw_id'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 50],
            [['withdraw_remark', 'system_remark'], 'string', 'max' => 255],
            [['system_withdraw_id', 'username', 'withdraw_money'], 'required'],
            [['system_withdraw_id'], 'unique'],
            [['handling_fee'], 'number', 'min'=>0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_withdraw_id' => Yii::t('app/menu','System_Withdraw_ID'),
            'out_withdraw_id' => Yii::t('app/menu','Out_Withdraw_ID'),
            'username' => Yii::t('app/menu','Username'),
            'user_type' => Yii::t('app/menu','User_Type'),
            'withdraw_money' => Yii::t('app/menu','Withdraw_Amount'),
            'bankcard_id' => Yii::t('app/menu','Withdraw_Bankcard_Number'),
            'withdraw_status' => Yii::t('app/menu','Order_Status'),
            'withdraw_remark' => Yii::t('app/menu','User_Remark'),
            'system_remark' => Yii::t('app/menu','System_Remark'),
            'query_team' => Yii::t('app/model', 'query_team'),
            'insert_at' => Yii::t('app/menu','Create_Time'),
            'update_at' => Yii::t('app/menu','Last_Update_Time'),
            'handling_fee' => Yii::t('app/menu','Handling_Fee'),
        ];
    }

    /**
     * 生成系统唯一提款订单号
     * @return string
     */
    public static function generateSystemWithdrawOrderNumber(){
        /*if (function_exists('com_create_guid')) {
            $str = com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $uuid = substr($charid, 0, 8)
                . substr($charid, 8, 4)
                . substr($charid, 12, 4)
                . substr($charid, 16, 4)
                . substr($charid, 20, 12);

            $str = $uuid;
        }*/

        return 'W'.strtoupper(md5(microtime(true)));
    }


    /**
     * 根据当前订单状态，获取可以修改的状态
     * @param int $status
     * @return array
     */
    public static function getAvailableChangeStatus($status){
        $availableStatus = array();
        if(is_numeric($status) && in_array($status, array_keys(self::$OrderStatusRel)) && isset(self::$AvailableStatus[$status])){
            $availableStatus = self::$AvailableStatus[$status];
        }

        return $availableStatus;
    }


    /**
     * 提交取款到第三方
     */
    public static function sendWithdrawOrder(array $order){
        $apiDomain = SystemConfig::getSystemConfig('ApiDomain');
        $withdrawUrl = \Yii::$app->params['typay_gateway_domain'].'/withdraw/create';

        $orderData = array(
            'merchant_id' => \Yii::$app->params['typay_merchant_id'],
            'merchant_order_id'=>$order['merchant_order_id'],
            'user_level'=>$order['user_level'],
            'pay_type'=>$order['pay_type'],  //1银行卡转账，  888备付金转账
            'pay_amt' => $order['pay_amt'],
            'notify_url' => $apiDomain.'/api/withdraw-notify',
            'return_url' => '',
            'bank_code' => $order['bank_code'],
            'bank_num' => $order['bank_num'],
            'bank_owner' => $order['bank_owner'],
            'bank_address' => $order['bank_address'],
            'user_id'=>$order['user_id'],
            'user_ip' => $order['user_ip'],
            'remark' => $order['remark'],
        );


        $key = \Yii::$app->params['typay_merchant_key'];

        $sign = md5('merchant_id=' . $orderData['merchant_id'] . '&merchant_order_id=' . $orderData['merchant_order_id'] . '&pay_type=' . $orderData['pay_type'] . '&pay_amt=' . $orderData['pay_amt'] . '&notify_url=' . $orderData['notify_url'] . '&return_url=' . $orderData['return_url'] . '&bank_code=' . $orderData['bank_code'] .'&bank_num='.$orderData['bank_num'].'&bank_owner='.$orderData['bank_owner'].'&bank_address='.$orderData['bank_address']. '&remark=' . $orderData['remark'] . '&key=' . $key);

        $orderData['sign'] = $sign;


        \Yii::info(json_encode($orderData, 256), 'withdraw_third_order_params');

        $res = Common::sendRequest($withdrawUrl, $orderData);

        \Yii::info($res, 'withdraw_third_order_res_origin_data');

        return json_decode($res,true);
    }
}
