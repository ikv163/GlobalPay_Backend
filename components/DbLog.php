<?php
namespace components;

use Yii;
use yii\helpers\Url;
use app\models\LogRecord;
/**
 * db变动记录
 * @author      Qimi
 * @copyright   Copyright (c) 2017
 * @version     V1.0
 */
class DbLog
{
    // 日志表名称
    const DB_TABLE_LOG = 'log_record';
    /**
     * 修改操作前.
     */
    public static function beforeUpdate($event)
    {

        if(!empty($event->sender) && Url::to() != '/site/ajax-login') {

            // 内容
            $arr['oldAttributes'] = $event->sender->oldAttributes;
            $description = json_encode($arr, JSON_UNESCAPED_UNICODE);

            // IP
            $ip = Yii::$app->getRequest()->getUserIP();

            // 保存
            $data = ['route' => Url::to(), 'info' => $description . '-ip:' . $ip, 'username' => \Yii::$app->user->identity->username, 'user_type' => 1, 'log_time' => date('Y-m-d H:i:s')];
            $model = new LogRecord();
            $model->setAttributes($data);
            $model->save(false);
        }

    }
    /**
     * 修改操作.
     */
    public static function afterUpdate($event)
    {

        if(!empty($event->changedAttributes) && Url::to() != '/site/ajax-login') {
            // 内容
            $arr['changedAttributes'] = $event->changedAttributes;
            $arr['oldAttributes'] = [];
            foreach($event->sender as $key => $value) {
                $arr['oldAttributes'][$key] = $value;
            }
            $description = json_encode($arr,JSON_UNESCAPED_UNICODE);

            // IP转换
            $ip = Yii::$app->getRequest()->getUserIP();

            // 保存
            $data = ['route' => Url::to(), 'info' => $description . '-ip:' . $ip, 'username' => \Yii::$app->user->identity->username, 'user_type' => 1, 'log_time' => date('Y-m-d H:i:s')];
            $model = new LogRecord();
            $model->setAttributes($data);
            $model->save(false);
        }
    }

    /**
     * 删除操作.
     */
    public static function afterDelete($event)
    {
        // 内容
        $arr = [];
        foreach($event->sender as $key => $value) {
            $arr[$key] = $value;
        }
        $description = json_encode($arr,JSON_UNESCAPED_UNICODE);

        // IP转换
        $ip = Yii::$app->getRequest()->getUserIP();

        // 保存
        // 保存
        $data = ['route' => Url::to(), 'info' => $description . '-ip:' . $ip, 'username' => \Yii::$app->user->identity->username, 'user_type' => 1, 'log_time' => date('Y-m-d H:i:s')];
        $model = new LogRecord();
        $model->setAttributes($data);
        $model->save(false);
    }

    /**
     * 插入操作.
     */
    public static function afterInsert($event)
    {
        if($event->sender->tableName() != self::DB_TABLE_LOG && Url::to() != '/site/ajax-login'){
            // 内容
            $arr = [];
            foreach($event->sender as $key => $value) {
                $arr[$key] = $value;
            }
            $description = json_encode($arr,JSON_UNESCAPED_UNICODE);

            // IP转换
            $ip = Yii::$app->getRequest()->getUserIP();

            // 保存
            $data = ['route' => Url::to(), 'info' => $description . '-ip:' . $ip, 'username' => \Yii::$app->user->identity->username, 'user_type' => 1, 'log_time' => date('Y-m-d H:i:s')];
            $model = new LogRecord();
            $model->setAttributes($data);
            $model->save(false);
        }
    }

}