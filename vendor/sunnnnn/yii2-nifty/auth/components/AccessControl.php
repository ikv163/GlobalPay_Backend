<?php
namespace sunnnnn\nifty\auth\components;

use Yii;
use yii\web\User;
use yii\web\ForbiddenHttpException;
use yii\di\Instance;
use yii\base\Module;
use sunnnnn\nifty\auth\components\helpers\RouteHelper;

/**
* @use: RBAC控制类
* @date: 2017-5-19 下午12:46:57
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class AccessControl extends \yii\base\ActionFilter{
	
    private $_user = 'user';
    
    public $allowActions = [];

    public function getUser(){
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    public function setUser($user){
        $this->_user = $user;
    }

    public function beforeAction($action){
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if (RouteHelper::checkRoute('/' . $actionId, $user)) {
            return true;
        }
        $this->denyAccess($user);
    }

    protected function denyAccess($user){
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

    protected function isActive($action){
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        $user = $this->getUser();
        if ($user->getIsGuest() && is_array($user->loginUrl) && isset($user->loginUrl[0]) && $uniqueId === trim($user->loginUrl[0], '/')) {
            return false;
        }

        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $uniqueId;
            if ($mid !== '' && strpos($id, $mid . '/') === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        foreach ($this->allowActions as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim($route, "*");
                if ($route === '' || strpos($id, $route) === 0) {
                    return false;
                }
            } else {
                if ($id === $route) {
                    return false;
                }
            }
        }

        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return false;
        }

        return true;
    }
}
