<?php
namespace sunnnnn\nifty\auth\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "auth_menu".
 *
 * @property string $id
 * @property string $name
 * @property string $parent
 * @property string $route
 * @property string $order
 * @property string $icon
 * @property string $add_time
 * @property string $edit_time
 */
class AuthMenu extends \yii\db\ActiveRecord
{
	const CACHE_MENU = 'cacheAuthMenu';
	public $keywords;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent', 'route', 'order', 'add_time', 'edit_time'], 'integer'],
            [['name', 'icon'], 'string', 'max' => 64],
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
            'name' => Yii::t('app/model', 'menu_name'),
            'parent' => Yii::t('app/model', 'menu_parent'),
            'route' => Yii::t('app/model', 'menu_route'),
            'order' => Yii::t('app/model', 'menu_order'),
            'icon' => Yii::t('app/model', 'menu_icon'),
            'add_time' => Yii::t('app/model', 'add_time'),
            'edit_time' => Yii::t('app/model', 'edit_time'),
        ];
    }
    
    public static function adpSearch($condition = [], $andFilter = [], $order = ['order' => SORT_ASC]){
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
    
    public static function getMenu(){
    	if(Yii::$app->cache->exists(self::CACHE_MENU)){
    		return Yii::$app->cache->get(self::CACHE_MENU);
    	}else{
    		$_return = [];
    		self::normalizeMenu($_return);
    		Yii::$app->cache->set(self::CACHE_MENU, $_return);
    		return $_return;
    	}
    }
    
    private static function normalizeMenu(&$_return = [], $parent = 0){
    	
    	$menus = static::find()->with(['routes'])->where(['parent' => $parent])->orderBy(['order' => SORT_ASC])->all();
    	if(!empty($menus)){
    		if(isset($_return['url'])){
    			unset($_return['url']);
    		}
    		foreach($menus as $menu){
    			$_temp = [
    				'id' => $menu->id,
    			    'parent' => $parent,
	    			'label' => $menu->name,
	    			'url' => isset($menu->routes->route) ? [$menu->routes->route] : '',
	    			'icon' => empty($menu->icon) ? '' : $menu->icon,
	    			'items' => []
    			];
    			self::normalizeMenu($_temp, $menu->id);
    			
    			if(empty($parent)){
    				$_return[] = $_temp;
    			}else{
	    			$_return['items'][] = $_temp;
    			}
    		}
    	}else{
    		if(isset($_return['items'])){
    			unset($_return['items']);
    		}
    	}
    	
    }
    
    public function getRoutes(){
    	return $this->hasOne(AuthRoute::className(), ['id' => 'route']);
    }
}
