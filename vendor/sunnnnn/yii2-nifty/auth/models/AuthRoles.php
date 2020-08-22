<?php
namespace sunnnnn\nifty\auth\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "auth_roles".
 *
 * @property string $id
 * @property string $name
 * @property string $routes
 * @property string $remark
 * @property string $add_time
 * @property string $edit_time
 */
class AuthRoles extends \yii\db\ActiveRecord
{
	public $keywords;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'routes'], 'required'],
            [['add_time', 'edit_time'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['routes', 'remark'], 'string', 'max' => 1024],
            [['keywords'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'id'),
            'name' => Yii::t('app/model', 'role_name'),
            'routes' => Yii::t('app/model', 'role_routes'),
            'remark' => Yii::t('app/model', 'role_remark'),
            'add_time' => Yii::t('app/model', 'add_time'),
            'edit_time' => Yii::t('app/model', 'edit_time'),
        ];
    }
    
    public static function adpSearch($condition = [], $andFilter = [], $order = ['id' => SORT_DESC]){
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
