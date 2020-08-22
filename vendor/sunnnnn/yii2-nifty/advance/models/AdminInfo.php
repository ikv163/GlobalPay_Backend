<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "admin_info".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $name
 * @property string $mobile
 * @property string $email
 * @property integer $gender
 * @property string $address
 * @property integer $add_time
 * @property integer $edit_time
 */
class AdminInfo extends \yii\db\ActiveRecord
{

	const STATUS_N = 0;
	const STATUS_D = 1;
	
	const GENDER_M = 1;
	const GENDER_F = 2;
	
	public $keywords;
	
	public static function getStatusArr($status = null){
	    $array = [
	        self::STATUS_N => Yii::t('app/model', 'enabled'),
	        self::STATUS_D => Yii::t('app/model', 'disabled'),
	    ];
	    
	    if($status === null){
	        return $array;
	    }else{
	        return isset($array[$status]) ? $array[$status] : '';
	    }
	}
	
	public static function getGenderArr($status = null){
	    $array = [
	        self::GENDER_M => Yii::t('app/model', 'male'),
	        self::GENDER_F => Yii::t('app/model', 'female'),
	    ];
	    
	    if($status === null){
	        return $array;
	    }else{
	        return isset($array[$status]) ? $array[$status] : '';
	    }
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id'], 'required'],
            [['admin_id', 'gender', 'add_time', 'edit_time'], 'integer'],
            [['name', 'email'], 'string', 'max' => 128],
            [['mobile'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 512],
            [['keywords'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'id'),
            'admin_id' => Yii::t('app/model', 'Admin ID'),
            'name' => Yii::t('app/model', 'admin_name'),
            'mobile' => Yii::t('app/model', 'admin_mobile'),
            'email' => Yii::t('app/model', 'admin_email'),
            'gender' => Yii::t('app/model', 'admin_gender'),
            'address' => Yii::t('app/model', 'admin_address'),
            'add_time' => Yii::t('app/model', 'add_time'),
            'edit_time' => Yii::t('app/model', 'edit_time'),
        ];
    }

	public function adpSearch($condition = [], $andFilter = [], $order = ['id' => SORT_DESC]){
		$query = static::find()->where($condition);
		if(!empty($andFilter) && is_array($andFilter)){
			foreach($andFilter as $val){
				$query->andFilterWhere($val);
			}
		}
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
		        'defaultOrder' => $order
		     ],
			'pagination' => [
				'pagesize' => 20,
			],
		]);
	
		return $dataProvider;
	}
}
