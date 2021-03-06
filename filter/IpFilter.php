<?php


namespace app\filter;


use app\models\SystemConfig;
use app\models\WhiteIp;
use yii\base\ActionFilter;

class IpFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $isOpenWhiteIp = SystemConfig::getSystemConfig('isOpenWhiteIp');
        if ($isOpenWhiteIp == 1) {
            $ips = json_decode(WhiteIp::getWhiteIps(), 1);
            if ($ips != null) {
                $ip = $_SERVER['REMOTE_ADDR'];
                if (!isset($ips[$ip]) || $ips[$ip] == null) {
                    \Yii::info($ip, '非IP白名单');
                    echo '<script>alert("您无权访问本网站！");</script>';
                    die;
                }
            }
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }
}