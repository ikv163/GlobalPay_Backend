<?php
namespace sunnnnn\nifty\auth\components\helpers;

use Yii;
use sunnnnn\nifty\auth\models\AuthRoute;

/**
 * 路由相关帮助类
* @use: 
* @date: 2017-5-19 下午12:50:09
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class RouteHelper{
	
	/**
	 * 检测指定路由对应用户的权限
	* @date: 2017-5-19 下午12:50:28
	* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
	* @param unknown $route
	* @param string $user
	* @return boolean
	 */
	public static function checkRoute($route, $user = null){
		$r = static::normalizeRoute($route);
		$user = $user === null ? Yii::$app->getUser() : $user;
	
		$routesByRole = AuthRoute::getRoutesByRole();
		$role = isset($user->getIdentity()->role) ? $user->getIdentity()->role : 0;
		$routes = isset($routesByRole[$role]) ? $routesByRole[$role] : [];
		
		if(empty($routes)) return false;
		
		if(isset($routes[$r])) return true;
		
		while (($pos = strrpos($r, '/')) > 0) {
			$r = substr($r, 0, $pos);
			if (isset($routes[$r.'/*'])) {
				return true;
			}
		}
		return isset($routes['/*']);
	}
	
	/**
	 * 获取当前路由（格式化）
	* @date: 2017-5-19 下午12:50:59
	* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
	* @param string $route
	* @return string
	 */
	public static function normalizeRoute($route = ''){
		if ($route === '') {
			return '/' . Yii::$app->controller->getRoute();
		} elseif (strncmp($route, '/', 1) === 0) {
			return $route;
		} elseif (strpos($route, '/') === false) {
			return '/' . Yii::$app->controller->getUniqueId() . '/' . $route;
		} elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
			return '/' . $mid . '/' . $route;
		}
		return '/' . $route;
	}
	
}