<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 19:33
 */

namespace app\common;

use app\models\SystemConfig;
use Yii;
use yii\httpclient\Client;

class Common
{
    //获取 Model 错误信息中的 第一条，无错误时 返回 null

    public static function getModelError($model)
    {
        $errors = $model->getErrors();
        //得到所有的错误信息
        if (!is_array($errors)) {
            return '';
        }
        $firstError = array_shift($errors);
        if (!is_array($firstError)) {
            return '';
        }
        return array_shift($firstError);
    }

    public static function redisLock($lockKey, $expireTime = 3)
    {
        $isContinue = Yii::$app->redis->setnx($lockKey, 1);
        if ($isContinue != true) {
            return false;
        }
        Yii::$app->redis->expire($lockKey, $expireTime);
        return true;
    }

    public static function telegramSendMsg($msg)
    {
        $isOk = SystemConfig::getSystemConfig('OpenTelegramInfo');
        if ($isOk == 1) {
            $url = 'https://api.telegram.org/bot1088652490:AAFc749_h7ts4sSCxsTpB3cnDqnZYehQbWY/sendMessage?chat_id=-385498986&text=' . $msg;
            file_get_contents($url);
            Yii::info($msg, 'telegramMsg');
        }
    }

    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @return bool|mixed
     */
    public static function curl($url, $post_data)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }


    /*
     * 二维码是否存在此金额的订单
     * type 1 存redis 0取redis
     * time 0 存永久  大于0 setex
     */
    public static function isQrCodeHasThisMoney($qr_code, $money, $type = 0, $val = 1)
    {
        $keyX = $qr_code . ($money * 100);
        if ($type == 1) {
            $exire = SystemConfig::getSystemConfig('OrderExpireTime');
            $exire = $exire == null ? 5 : $exire;
            \Yii::$app->redis->setex($keyX, $exire * 60, $val);
            return true;
        } else {
            return \Yii::$app->redis->get($keyX);
        }
    }

    /*
     * 二维码当天已收金额
     * type 1 存redis 0取redis
     * time 0 存永久  大于0 setex
     */
    public static function qrTodayMoney($qr_code, $type = 0, $val = 0, $isSuccess = 0)
    {
        if ($isSuccess == 0) {
            $allOrSuccess = 'all';
        } else {
            $allOrSuccess = 'success';
        }
        $today = date('Ymd');
        $keyX = $qr_code . $today . 'money' . $allOrSuccess;
        if ($type == 1) {
            \Yii::$app->redis->setex($keyX, 90000, \Yii::$app->redis->get($keyX) + $val);
            return true;
        } else {
            return \Yii::$app->redis->get($keyX);
        }
    }

    /*
     * 二维码当天已收笔数
     * type 1 存redis 0取redis
     * time 0 存永久  大于0 setex
     */
    public static function qrTodayTimes($qr_code, $type = 0, $val = 1, $isSuccess = 0)
    {
        if ($isSuccess == 0) {
            $allOrSuccess = 'all';
        } else {
            $allOrSuccess = 'success';
        }
        $today = date('Ymd');
        $keyX = $qr_code . $today . 'times' . $allOrSuccess;
        if ($type == 1) {
            \Yii::$app->redis->setex($keyX, 90000, \Yii::$app->redis->get($keyX) + $val);
            return true;
        } else {
            return \Yii::$app->redis->get($keyX);
        }
    }

    /*
     * 收款员当天已收金额
     * type 1 存redis 0取redis
     * time 0 存永久  大于0 setex
     */
    public static function cashierTodayMoney($username, $qr_type, $type = 0, $val = 0, $isSuccess = 0)
    {
        if ($isSuccess == 0) {
            $allOrSuccess = 'all';
        } else {
            $allOrSuccess = 'success';
        }
        $today = date('Ymd');
        $keyX = $username . $qr_type . $today . 'money' . $allOrSuccess;
        if ($type == 1) {
            Yii::info($val . '-' . \Yii::$app->redis->get($keyX), 'ccc');

            \Yii::$app->redis->setex($keyX, 90000, \Yii::$app->redis->get($keyX) + $val);
            return true;
        } else {
            return \Yii::$app->redis->get($keyX);
        }
    }

    /*
     * 收款员当天已收笔数
     * type 1 存redis 0取redis
     * time 0 存永久  大于0 setex
     */
    public static function cashierTodayTimes($username, $qr_type, $type = 0, $val = 1, $isSuccess = 0)
    {
        if ($isSuccess == 0) {
            $allOrSuccess = 'all';
        } else {
            $allOrSuccess = 'success';
        }
        $today = date('Ymd');
        $keyX = $username . $qr_type . $today . 'times' . $allOrSuccess;
        if ($type == 1) {
            \Yii::$app->redis->setex($keyX, 90000, \Yii::$app->redis->get($keyX) + $val);
            return true;
        } else {
            return \Yii::$app->redis->get($keyX);
        }
    }


    /**
     * yii自带httpclient 发送请求
     */
    public static function sendRequest($url, $data, $method = 'post')
    {
        $client = new Client();
        $res = $client->createRequest();
        if (!empty($head)) {
            $res->setHeaders($head);
            $res->setFormat(Client::FORMAT_JSON);
        }
        $res->setMethod($method);
        $res->setUrl($url);
        $res->setData($data);
        $res->setOptions([
            'timeout' => 30,
            CURLOPT_TIMEOUT => 30,
        ]);

        $repose = $res->send();
        return $repose->getContent();
    }
}