<?php
namespace sunnnnn\helper;

use Yii;
use yii\web\Response;
use yii\helper\Url;
use sunnnnn\helper\utils\Curl;
use sunnnnn\helper\utils\File;

/**
 * @use: 通用函数类
 * @date: 2017-5-31 上午9:35:23
 * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class Helper{

    public $getValueMethodGet = 'get';
    public $getValueMethodPost = 'post';
    public $getValueMethodHtml = 'html';

    public $outTypeJson  = Response::FORMAT_JSON;
    public $outTypeHtml  = Response::FORMAT_HTML;
    public $outTypeJsonp = Response::FORMAT_JSONP;
    public $outTypeRaw   = Response::FORMAT_RAW;
    public $outTypeXml   = Response::FORMAT_XML;

    /**
     * @date: 2017年6月9日 上午9:23:00
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $method
     * @param unknown $param
     * @param number $default
     * @param string $filter
     * @return number|NULL|unknown|mixed
     */
    public function getValue($method, $param, $default = 0, $filter = 'intval'){
        $value = null;
        switch($method){
            case $this->getValueMethodGet:
                $value = Yii::$app->request->get($param);
                break;
            case $this->getValueMethodPost:
                $value = Yii::$app->request->post($param);
                break;
            case $this->getValueMethodHtml:
                $value = (strpos($param, "\n") !== false) ? str_replace("\n", "<br/>", $param) : $param;
                break;
        }

        return empty($value) ? $default : (!empty($filter) && function_exists($filter) ? $filter($value) : $value);
    }

    /**
     * 指定格式输出数据
     * @date: 2017年6月9日 上午9:23:54
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $type
     * @param array $data
     */
    public function out($type, $data = []){
        Yii::$app->response->format = $type;
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
        exit;
    }

    public function getCloudFile($src, $domain, $default = ''){
        if(empty($src)) return $default;
        if(false === strpos($src, '://')){
            return rtrim($domain, '/').'/'.ltrim($src, '/');
        }else{
            return $src;
        }
    }

    /**
     * 获取过滤链接
     * @date: 2017年6月9日 上午9:24:28
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $link
     * @param string $domain
     * @param string $default
     * @return string|unknown
     */
    public function getLink($link, $domain = '', $default = ''){
        if(empty($link)) return $default;
        if(false === strpos($link, '://')){
            return empty($domain) ? Url::to([$link]) : rtrim($domain, '/').'/'.ltrim($link, '/');
        }else{
            return $link;
        }
    }

    /**
     * @use: 字符串替换
     * @date: 2018/12/20 12:35 PM
     * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return mixed|string|需要替换的字符串 : string
     * @param $str     需要替换的字符串
     * @param $search  替换的原字符串
     * @param $replace 替换的字符串
     * @param string $default 字符串为空的默认返回
     */
    public function replace($str, $search, $replace, $default = ''){
        if(empty($str)) return $default;

        return  strpos($str, $search) !== false ? str_replace($search, $replace, $str) : $str;
    }

    /**
     * 判断是否移动设备
     * @date: 2017年6月9日 上午9:24:47
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return boolean
     */
    public function isMobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = ['mobile', 'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh',
                'lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry',
                'meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi',
                'openwave','nexusone','cldc','midp','wap'
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])){ // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            }
        }
        return false;
    }

    /**
     * 判断是否微信浏览器
     * @date: 2017年6月9日 上午9:25:02
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return boolean
     */
    public function isWeChatBrowser(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }

    /**
     * 验证手机号码格式
     * @date: 2017年6月9日 上午9:25:23
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $mobile
     * @return boolean
     */
    public function validateMobile($mobile){
        return strlen($mobile) === 11 && preg_match('/^1[\d]{10}$/', $mobile);
    }

    /**
     * 验证邮箱格式
     * @date: 2017年6月9日 上午9:25:30
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $email
     * @return boolean
     */
    public function validateEmail($email){
        return !empty($email) && preg_match('/^[a-z\d][\w-.]{0,31}@(?:[a-z\d][a-z\d-]{0,30}[a-z\d]\.){1,4}[a-z]{2,4}$/i', $email);
    }

    /**
     * 加密字符串
     * @date: 2017年6月9日 上午9:25:40
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $data
     * @param string $key
     * @return string|unknown
     */
    public function encrypt($data, $key = ''){
        if(empty($data)) return '';
        if(empty($key))  return base64_encode($data);
        $key     = md5($key);
        $x       = 0;
        $lenData = strlen($data);
        $lenKey  = strlen($key);
        $char = $str = '';
        for ($i = 0; $i < $lenData; $i++){
            if ($x == $lenKey){
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $lenData; $i++){
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }

    /**
     * 解密字符串
     * @date: 2017年6月9日 上午9:26:17
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $data
     * @param string $key
     * @return string
     */
    public function decrypt($data, $key = ''){
        if(empty($data)) return '';
        if(empty($key))  return base64_decode($data);
        $data = base64_decode($data);
        $key  = md5($key);
        $x    = 0;
        $char = $str = '';
        $lenData = strlen($data);
        $lenKey  = strlen($key);
        for ($i = 0; $i < $lenData; $i++){
            if ($x == $lenKey){
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $lenData; $i++){
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))){
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }else{
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }

    /**
     * 转换字符串编码
     * @date: 2017年6月9日 下午3:15:00
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @param unknown $str
     * @param string $code
     * @return string|unknown
     */
    public function encode($str, $code = 'UTF-8'){
        $encode = mb_detect_encoding($str, ["ASCII",'UTF-8',"GB2312","GBK",'BIG5']);
        if($encode != $code){
            return mb_convert_encoding($str, $code, $encode);
        }
        return $str;
    }

    /**
     * 获取IP地址
     * @date: 2017年6月9日 上午9:39:17
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return NULL|unknown
     */
    public function getIp(){
        static $ip = null;
        if (!is_null($ip)) {
            return $ip;
        }

        $filter_func = function($_ip) {
            $_ip = trim($_ip);
            return !empty($_ip) && $_ip != '127.0.0.1' && preg_match('/^[\d.]{7,15}$/', $_ip);
        };

        $get_func = function($_ip_list) {
            if (count($_ip_list) > 0) {
                foreach ($_ip_list as $_ip) {
                    if (!preg_match('/^(10\.0\.10\.|192\.168\.)[\d.]+$/', $_ip)) {
                        return $_ip;
                    }
                }

                return $_ip_list[0];
            }
            return '0.0.0.0';
        };

        $ip_list = [$_SERVER['REMOTE_ADDR']];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy_ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $proxy_ip_list = array_reverse($proxy_ip_list);
            $ip_list = array_merge($ip_list, $proxy_ip_list);
        }
        $ip_list = array_values(array_filter($ip_list, $filter_func));
        $ip = $get_func($ip_list);
        return $ip;
    }

    /**
     * @use: 获取当前URL
     * @date: 2018/8/13 下午1:48
     * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return: string
     * @param bool $proto 是否需要 http:// 或 https://
     * @param bool $uri 是否需要请求参数
     */
    public function getHost($proto = true, $uri = true){
        $result = $_SERVER['HTTP_HOST'];

        if($proto === true){
            $httpType = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $result = $httpType.$result;
        }

        $uri === true && $result = $result.$_SERVER['REQUEST_URI'];

        return $result;
    }

    /**
     * 获取Curl实例对象
     * @date: 2017年6月9日 上午9:26:34
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return \sunnnnn\helper\Curl
     */
    public function curl(){
        return new Curl();
    }

    /**
     * 获取File实例对象
     * @date: 2017年6月9日 上午9:42:48
     * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return \sunnnnn\helper\utils\File
     */
    public function file(){
        return new File();
    }
}
