<?php
namespace sunnnnn\nifty\auth;

/**
* @use: 权限控制模块，主要由  路由、角色、权限菜单 组成
* @date: 2017-5-19 下午12:45:18
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'sunnnnn\nifty\auth\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
