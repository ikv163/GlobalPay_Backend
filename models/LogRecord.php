<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "log_record".
 *
 * @property int $id
 * @property string $route 路由
 * @property string $info 日志信息
 * @property string $username 用户名
 * @property int $user_type 用户类型 1平台 2商户 3收款员
 * @property string $log_time 添加时间
 */
class LogRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['info'], 'string'],
            [['user_type'], 'integer'],
            [['log_time'], 'safe'],
            [['route', 'username'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => 'Route',
            'info' => 'Info',
            'username' => 'Username',
            'user_type' => 'User Type',
            'log_time' => 'Log Time',
        ];
    }


    /**
     * 写入操作日志
     * @param string $info   日志内容
     * @param string $route  路由
     * @param int   $user_type  用户角色id
     * @param int   $logType  日志类型: 1系统日志   2db日志   3系统、db都写
     * @return bool
     */
    public static function addLog($infos, $route='', $user_type=0, $logType=3){
        try{
            $params = array(
                'username' => \Yii::$app->user->identity->username,
                'route' => $route,
                'info' => is_array($infos)?json_encode($infos,JSON_UNESCAPED_UNICODE):$infos,
                'user_type' => is_numeric($user_type) && $user_type > 0 ? intval($user_type) : \Yii::$app->user->identity->role,
                'log_time' => date('Y-m-d H:i:s'),
            );

            switch($logType){
                case 1:
                    \Yii::info(json_encode($params,JSON_UNESCAPED_UNICODE));
                    $res = true;
                    break;

                case 2:
                    $model = new LogRecord($params);
                    $res = (bool)$model->save();
                    break;

                case 3:
                    \Yii::info(json_encode($params,JSON_UNESCAPED_UNICODE));
                    $model = new LogRecord($params);
                    $res = (bool)$model->save();
                    break;

                default:
                    $res = false;
                    break;
            }
            return $res;
        }catch(yii\db\Exception $dbException){
            return $dbException->getMessage();
        }catch(yii\base\Exception $baseException){
            return $baseException->getMessage();
        }
    }
}
