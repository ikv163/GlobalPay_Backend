<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sys_bankcard".
 *
 * @property int $id
 * @property string $bankcard_number 银行卡卡号
 * @property string $bankcard_owner 银行卡所属人姓名
 * @property string $bank_code 银行类型编码
 * @property string $bankcard_address 开户行地址
 * @property string $balance 银行卡余额
 * @property int $card_status 状态 0默认使用 1可用 2禁用  9删除
 * @property string $insert_at 添加时间
 * @property string $update_at 修改时间
 */
class SysBankcard extends \yii\db\ActiveRecord
{

    public static $BankCardStatusDefault = 0;
    public static $BankCardStatusOn = 1;
    public static $BankCardStatusOff = 2;
    public static $BankCardStatusDeleted = 9;
    public static $BankCardStatusRel = array(
        0 => '可用',
        1 => '启用',
        2 => '禁用',
        9 => '删除',
    );

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sys_bankcard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balance'], 'number'],
            [['card_status'], 'integer'],
            [['insert_at', 'update_at', 'card_owner', 'max_balance', 'remark'], 'safe'],
            [['bankcard_number', 'bank_code'], 'string', 'max' => 50],
            [['bankcard_owner'], 'string', 'max' => 30],
            [['bankcard_address'], 'string', 'max' => 255],
            [['bankcard_number'], 'unique'],
            [['card_owner'], 'integer', 'min' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/menu', 'ID'),
            'bankcard_number' => Yii::t('app/menu', 'Bankcard_Number'),
            'bankcard_owner' => Yii::t('app/menu', 'Bankcard_Owner'),
            'bank_code' => Yii::t('app/menu', 'Bank_Code'),
            'bankcard_address' => Yii::t('app/menu', 'Bankcard_Address'),
            'balance' => Yii::t('app/menu', 'Balance'),
            'card_status' => Yii::t('app/menu', 'Card_Status'),
            'insert_at' => Yii::t('app/menu', 'Insert_At'),
            'update_at' => Yii::t('app/menu', 'Update_At'),
            'card_owner' => Yii::t('app/menu', 'card_owner'),
            'max_balance' => Yii::t('app/menu', 'max_balance'),
            'remark' => Yii::t('app/menu', 'Remark'),
        ];
    }
}
