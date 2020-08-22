<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "white_ip".
 *
 * @property int $id
 * @property string $user_ip IP
 * @property string $ip_remark IP备注
 * @property int $ip_status IP状态 0禁用 1启用
 * @property string $insert_at 添加时间
 * @property string $update_at 修改时间
 */
class WhiteIp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'white_ip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip_status', 'user_ip', 'ip_remark'], 'required'],
            [['ip_status'], 'integer'],
            [['insert_at', 'update_at'], 'safe'],
            [['ip_remark'], 'string', 'max' => 255],
            [['user_ip'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'ID'),
            'user_ip' => Yii::t('app/model', 'User Ip'),
            'ip_remark' => Yii::t('app/model', 'Ip Remark'),
            'ip_status' => Yii::t('app/model', 'Ip Status'),
            'insert_at' => Yii::t('app/model', 'Insert At'),
            'update_at' => Yii::t('app/model', 'Update At'),
        ];
    }

    public static function getWhiteIps()
    {
        $ip = Yii::$app->redis->get('whiteIp');
        if ($ip == null) {
            $temp = WhiteIp::find()->where('ip_status=1')->select('user_ip')->indexBy('user_ip')->asArray()->all();
            $ip = json_encode($temp, JSON_UNESCAPED_UNICODE);
            Yii::$app->redis->set('whiteIp', $ip);
        }
        return $ip;
    }
}
