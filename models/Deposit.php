<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "deposit".
 *
 * @property int $id
 * @property string $system_deposit_id 系统订单ID
 * @property string $out_deposit_id 外部订单ID
 * @property string $username 提款人用户名
 * @property float $deposit_money 充值金额
 * @property int $deposit_status 充值状态 0创建 1处理中 2成功 3失败 4驳回
 * @property string|null $deposit_remark 用户重置备注
 * @property string|null $system_remark 系统备注
 * @property string|null $insert_at 充值时间
 * @property string|null $update_at 修改时间
 */
class Deposit extends \yii\db\ActiveRecord
{
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
        '0' => array(1, 2, 3, 4),
        '1' => array(2, 3, 4),
        '2' => array(),
        '3' => array(),
        '4' => array(),
    );


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deposit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deposit_money'], 'number'],
            [['deposit_status'], 'integer'],
            [['insert_at', 'update_at', 'query_team'], 'safe'],
            [['system_deposit_id', 'out_deposit_id'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 50],
            [['deposit_remark', 'system_remark'], 'string', 'max' => 255],
            [['system_deposit_id', 'username', 'deposit_money'], 'required'],
            [['system_deposit_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_deposit_id' => \Yii::t('app/menu', 'System_Deposit_ID'),
            'out_deposit_id' => \Yii::t('app/menu', 'Out_Deposit_ID'),
            'username' => \Yii::t('app/menu', 'Username'),
            'deposit_money' => \Yii::t('app/menu', 'Deposit_Amount'),
            'deposit_status' => \Yii::t('app/menu', 'Order_Status'),
            'deposit_remark' => \Yii::t('app/menu', 'User_Remark'),
            'system_remark' => \Yii::t('app/menu', 'System_Remark'),
            'query_team' => Yii::t('app/model', 'query_team'),
            'insert_at' => \Yii::t('app/menu', 'Create_Time'),
            'update_at' => \Yii::t('app/menu', 'Last_Update_Time'),
        ];
    }


    /**
     * 生成系统唯一充值订单号
     * @return string
     */
    public static function generateSystemDepositOrderNumber()
    {
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

        return 'D' . strtoupper(md5(microtime(true)));
    }


    /**
     * 根据当前订单状态，获取可以修改的状态
     * @param int $status
     * @return array
     */
    public static function getAvailableChangeStatus($status)
    {
        $availableStatus = array();
        if (is_numeric($status) && in_array($status, array_keys(self::$OrderStatusRel)) && isset(self::$AvailableStatus[$status])) {
            $availableStatus = self::$AvailableStatus[$status];
        }

        return $availableStatus;
    }

}
