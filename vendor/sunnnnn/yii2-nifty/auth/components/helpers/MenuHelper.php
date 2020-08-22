<?php
namespace sunnnnn\nifty\auth\components\helpers;

use Yii;
use yii\helpers\Url;
use sunnnnn\nifty\auth\models\AuthMenu;
use sunnnnn\nifty\auth\models\AuthRoute;

/**
* @use: 相关菜单的帮助类
* @date: 2017-5-19 下午12:48:09
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class MenuHelper{
	
	/**
	 * 生成权限控制的菜单列表
	* @date: 2017-5-19 下午12:48:31
	* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
	* @return array
	 */
	public static function getAssignedMenu(){
		$routeByRoles = AuthRoute::getRoutesByRole();
		$user = Yii::$app->getUser();
		$role = isset($user->getIdentity()->role) ? $user->getIdentity()->role : 0;
		$routes = isset($routeByRoles[$role]) ? $routeByRoles[$role] : [];
		if(empty($routes)) return [];
		
		$menus = AuthMenu::getMenu();
		self::normalizeMenu($menus, $routes);
		
		return $menus;
	} 
	
	private static function normalizeMenu(&$menus, $routes){
		if(!empty($menus) && is_array($menus)){
			foreach($menus as $key => $menu){
				if(isset($menu['url']) && is_array($menu['url']) && !empty($menu['url'][0])){
					$route = $menu['url'][0];
					if(RouteHelper::checkRoute($route)){
						$action = RouteHelper::normalizeRoute();
						if($route == $action){
							$menu['active'] = true;
						}
					}else{
						unset($menus[$key]);
					}
				}else{
					if(isset($menu['items'])){
						self::normalizeMenu($menus[$key]['items'], $routes);
					}
					if(empty($menu['items'])){
						unset($menus[$key]);
					}
				}
			}
		}
	}
	
	/**
	 * 获取全部菜单项的渲染结果，用于菜单列表首页展示
	* @date: 2017-5-19 下午12:49:27
	* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
	* @param string $itmes
	* @param string $result
	* @return string
	 */
	public static function renderMenuItmes($itmes = null, $result = ''){
		$itmes = $itmes === null ? AuthMenu::getMenu() : $itmes;
		if(!empty($itmes)){
			foreach($itmes as $key => $val){
			    $val['label'] = Yii::t('app/menu', $val['label']);
				if(!empty($val['items'])){
					$tmp = '<li><div class="mtree-items"><i class="{icon}"></i> {label}<span class="mtree-button">{edit} {delete}</span></div><ul>{items}</ul></li>';
					$replace = [
						'{label}' => $val['label'], 
						'{icon}' => $val['icon'], 
						'{items}' => self::renderMenuItmes($val['items']),
						'{edit}' => '<button class="btn btn-sm btn-default btn-edit" data-href="'. Url::to(['/auth/menu/edit', 'id' => $val['id']]) .'"><i class="fa fa-pencil"></i> '.Yii::t('app/view', 'edit').'</button>',
						'{delete}' => '<button class="btn btn-sm btn-default btn-hover-danger btn-delete" data-key="'. $val['id'] .'"><i class="fa fa-trash-o"></i> '.Yii::t('app/view', 'remove').'</button>',
					];
					$result .= strtr($tmp, $replace);
				}elseif(!empty($val['url'])){
					$tmp = '<li><div class="mtree-item"><i class="{icon}"></i> {label}<span class="mtree-button">{edit} {delete}</span></div></li>';
					$replace = [
						'{label}' => $val['label'],
						'{icon}' => $val['icon'],
						'{edit}' => '<button class="btn btn-sm btn-default btn-edit" data-href="'. Url::to(['/auth/menu/edit', 'id' => $val['id']]) .'"><i class="fa fa-pencil"></i> '.Yii::t('app/view', 'edit').'</button>',
						'{delete}' => '<button class="btn btn-sm btn-default btn-hover-danger btn-delete" data-key="'. $val['id'] .'"><i class="fa fa-trash-o"></i> '.Yii::t('app/view', 'remove').'</button>',
					];
					$result .= strtr($tmp, $replace);
				}
			}
		
			return $result;
		}
	}
}