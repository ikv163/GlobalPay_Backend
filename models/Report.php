<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_statistics".
 *
 * @property int $id
 * @property string $username 统计所属人
 * @property int $user_type 所属人类型 1平台 2商户 3收款员
 * @property string $finance_date 当天日期
 * @property string $insert_at 添加日期
 * @property string $update_at 更新日期
 */
class Report extends \yii\db\ActiveRecord
{
    public $is_team;

    //报表主体类型: 1平台  2商户  3收款员
    public static $ReportTypePlatform = 1;
    public static $ReportTypeMerchant = 2;
    public static $ReportTypeCashier = 3;

    public static $ReportTypeRel = array(
        '1' => '平台',
        '2' => '商户',
        '3' => '收款员',
    );


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_statistics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_type'], 'integer'],
            [['finance_date', 'insert_at', 'update_at', 'is_team'], 'safe'],
            [['username'], 'string', 'max' => 50],
            [['datas'], 'string', 'max' => 3000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('app/menu', 'Username'),
            'user_type' => Yii::t('app/menu', 'User_Type'),
            'finance_date' => Yii::t('app/menu', 'Finance_Date'),
            'insert_at' => Yii::t('app/menu', 'Create_Time'),
            'datas' => Yii::t('app/menu', 'datas'),
            'update_at' => Yii::t('app/menu', 'Last_Update_Time'),
        ];
    }
}
