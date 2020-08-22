<?php
namespace sunnnnn\nifty\auth\components;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;

if(class_exists(\components\Controller::className())){
    class Controller extends \components\Controller{
        public function init(){
            parent::init();
        }
    }
}else{
    class Controller extends \yii\web\Controller{
        public $language = ['en', 'zh-cn'];
        
        public function init(){
            parent::init();
            $this->switchLanguage();
        }
        
        public function getPostValue($param, $default = 0, $filter = 'intval'){
            return Yii::$app->helper->getValue(Yii::$app->helper->getValueMethodPost, $param, $default, $filter);
        }
        
        public function getGetValue($param, $default = 0, $filter = 'intval'){
            return Yii::$app->helper->getValue(Yii::$app->helper->getValueMethodGet, $param, $default, $filter);
        }
        
        public function response($result = true, $message = '', $ext = []){
            gettype($result) == 'boolean';
            $data = [
                'result' => $result,
                'message' => $message
            ];
            if(!empty($ext) && is_array($ext)) $data = ArrayHelper::merge($data, $ext);
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $data;
            Yii::$app->response->send();
            exit;
        }
        
        public function switchLanguage($language = null){
            $cookieKey = 'language';
            if($language === null){
                if(Yii::$app->request->cookies->has($cookieKey)){
                    $language = Yii::$app->request->cookies->get($cookieKey);
                    $lang = Yii::$app->language;
                    if($language != $lang){
                        Yii::$app->language = $language;
                    }
                }
            }else{
                if(in_array($language, $this->language)){
                    $cookie = new \yii\web\Cookie();
                    $cookie->name = $cookieKey;
                    $cookie->value = $language;
                    $cookie->httpOnly = true;
                    Yii::$app->response->getCookies()->add($cookie);
                }
            }
        }
    	
    }
}
