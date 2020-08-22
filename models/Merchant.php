<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "merchant".
 *
 * @property int $id
 * @property string $mch_name 商户名称
 * @property string $mch_code 商户英文简码
 * @property string $mch_key 商户密钥
 * @property int $mch_status 商户状态 0禁用 1启用
 * @property string $available_money 商户可提单总额，-1则表示不限额度
 * @property string $used_money 商户已提单金额
 * @property string $balance 商户余额
 * @property string $pay_password 商户资金密码（提现密码）
 * @property string $telephone 商户联系方式
 * @property string $wechat_rate 微信费率
 * @property string $alipay_rate 支付宝费率
 * @property string $insert_at 添加时间
 * @property string $update_at 更新时间
 * @property string $remark 备注
 */
class Merchant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'merchant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mch_key', 'mch_name', 'mch_code', 'mch_status', 'available_money', 'wechat_rate', 'alipay_rate'], 'required'],
            [['mch_status'], 'integer'],
            [['available_money', 'used_money', 'balance', 'wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate'], 'number'],
            [['insert_at', 'update_at'], 'safe'],
            [['mch_name', 'mch_code'], 'string', 'max' => 50],
            [['mch_key', 'pay_password', 'remark'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 20],
            [['mch_name'], 'unique'],
            [['mch_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'ID'),
            'mch_name' => Yii::t('app/model', 'Mch Name'),
            'mch_code' => Yii::t('app/model', 'Mch Code'),
            'mch_key' => Yii::t('app/model', 'Mch Key'),
            'mch_status' => Yii::t('app/model', 'Mch Status'),
            'available_money' => Yii::t('app/model', 'Available Money'),
            'used_money' => Yii::t('app/model', 'Used Money'),
            'balance' => Yii::t('app/model', 'Balance'),
            'pay_password' => Yii::t('app/model', 'Pay Password'),
            'telephone' => Yii::t('app/model', 'Telephone'),
            'wechat_rate' => Yii::t('app/model', 'Wechat Rate'),
            'alipay_rate' => Yii::t('app/model', 'Alipay Rate'),
            'union_pay_rate' => Yii::t('app/model', 'union_pay_rate'),
            'bank_card_rate' => Yii::t('app/model', 'bank_card_rate'),
            'insert_at' => Yii::t('app/model', 'Insert At'),
            'update_at' => Yii::t('app/model', 'Update At'),
            'remark' => Yii::t('app/model', 'Remark'),
        ];
    }
}
