<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_bankcard".
 *
 * @property int $id
 * @property string $bankcard_number 银行卡卡号
 * @property string $bankcard_owner 银行卡所属人姓名
 * @property string $bank_code 银行类型编码
 * @property string $bankcard_address 开户行地址
 * @property string $username 用户名
 * @property int $user_type 用户类型 1商户 2收款员
 * @property int|null $card_status 是否默认使用 0删除 1可用 2默认
 * @property string|null $insert_at 添加时间
 * @property string|null $update_at 修改时间
 */
class UserBankcard extends \yii\db\ActiveRecord
{

    public static $UserTypeMerchant = 1;
    public static $UserTypeCashier = 2;
    public static $UserTypeRel = array(
        1 => '商户',
        2 => '收款员',
    );

    public static $BankCardStatusDefault = 0;
    public static $BankCardStatusOn = 1;
    public static $BankCardStatusOff = 2;
    public static $BankCardStatusDeleted = 9;
    public static $BankCardStatusRel = array(
        0 => '默认使用',
        1 => '启用',
        2 => '禁用',
        9 => '删除',
    );



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_bankcard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_type', 'card_status'], 'integer'],
            [['insert_at', 'update_at'], 'safe'],
            [['bankcard_number', 'bank_code', 'bankcard_owner', 'username', 'bankcard_address', 'card_status'], 'required'],
            [['bankcard_number', 'bank_code', 'username'], 'string', 'max' => 50],
            [['bankcard_owner'], 'string', 'max' => 30],
            [['bankcard_address'], 'string', 'max' => 255],
            [['bankcard_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bankcard_number' => \Yii::t('app/menu','Bankcard_Number'),
            'bankcard_owner' => \Yii::t('app/menu','Bankcard_Owner'),
            'bank_code' => \Yii::t('app/menu','Bank_Code'),
            'bankcard_address' => \Yii::t('app/menu','Bankcard_Address'),
            'username' => \Yii::t('app/menu','Username'),
            'user_type' => \Yii::t('app/menu','User_Type'),
            'card_status' => \Yii::t('app/menu','Card_Status'),
            'insert_at' => \Yii::t('app/menu','Insert_At'),
            'update_at' => \Yii::t('app/menu','Update_At'),
        ];
    }
}
