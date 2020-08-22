<?php

namespace app\models;

use app\common\Common;
use Faker\Provider\PhoneNumber;
use Yii;
use yii\validators\NumberValidator;

class AppUpload extends \yii\base\Model
{
    public $app_file;
    public $app_type;
    public $app_version;
    public $update_msg;

    public static $appTypeAndroid = 1;
    public static $appTypeIOS = 2;
    public static $appTypeRel = array(
        '1' => 'Android',
        '2' => 'IOS',
    );

    private $androidAppFileName = 'qm.apk';
    private $iosAppFileName = 'qm_ios.apk';
    private $appFileSavePath = 'download';

    private $initAppVersion = '1.0.0';

    public function rules()
    {
        return [
            [['app_file', 'app_type'], 'required'],
            [['app_file'], 'file','skipOnEmpty' => false, 'extensions' => 'apk, png', 'maxSize' => 1024 * 1024 * 100,  'checkExtensionByMimeType' => false],
            [['app_type'], 'number', 'min'=>1],
            [['app_version', 'update_msg'], 'string'],
            [['app_version', 'update_msg'], 'safe'],
        ];
    }

    public function upload(){

        $oriFileName = '';
        $updatedFileName = '';
        $fullPath = $_SERVER['DOCUMENT_ROOT'].'/'.$this->appFileSavePath;

        try{
            if ($this->validate()) {
                $appName = $this->app_type == 1 ? $this->androidAppFileName : $this->iosAppFileName;
                if(file_exists($fullPath.'/'.$appName)){
                    $oriFileName = $appName;
                    $newAppName = date('YmdHis').'_'.$appName;
                    if(!rename($fullPath.'/'.$appName, $fullPath.'/'.$newAppName)){
                        throw new \Exception('rename failed');
                    }
                    $updatedFileName = $newAppName;
                }

                $appConfig = SystemConfig::getSystemConfig('APP_CONFIG');
                $appConfig = json_decode($appConfig, true);

                $appConfigInfo = array();
                $appConfigKey = -1;

                if ($appConfig) {
                    foreach ($appConfig as $key => $config) {
                        if (isset($config['app_type']) && $config['app_type'] == $this->app_type) {
                            $appConfigInfo = $appConfig[$key];
                            $appConfigKey = $key;
                            break;
                        }
                    }

                    /*$appConfigInfo = $appConfig[1];
                    $appConfigKey = 1;*/
                }

                $this->app_file->saveAs($fullPath . '/' . $appName);


                //{"app_type":1, "app_version":"1.0.0", "app_file_name" : "qm.apk", "update_msg":"1.更新信息1 @2更新信息2"}
                if($appConfigInfo){
                    $appConfigInfo['update_msg'] = $this->update_msg && $this->app_version && $this->app_version != $appConfigInfo['app_version'] ? $this->update_msg : $appConfigInfo['update_msg'];
                    $appConfigInfo['app_version'] = $this->app_version && $this->app_version != $appConfigInfo['app_version'] ? $this->app_version : $appConfigInfo['app_version'];
                }else{
                    $appConfigInfo = array(
                        "app_type" => $this->app_type,
                        "app_version" => $this->initAppVersion,
                        "app_file_name" => $appName,
                        "update_msg" => '',
                    );
                }

                if($appConfigKey > -1 && isset($appConfig[$appConfigKey])){
                    $appConfig[$appConfigKey] = $appConfigInfo;
                }else{
                    $appConfig[] = $appConfigInfo;
                }

                //更新配置
                if(SystemConfig::updateAll(array('config_value'=>json_encode($appConfig, 256)), array('config_code' => 'APP_CONFIG', 'config_status' => 1)) === false){
                    throw new \Exception('update system config failed');
                }

                //更新配置对应的缓存
                \Yii::$app->redis->set('config_APP_CONFIG', json_encode($appConfig, 256));

                return true;
            } else {
                return false;
            }
        }catch(\Exception $e){

            //先把改掉的文件名改回来
            if($updatedFileName && $oriFileName){
                rename($fullPath.'/'.$updatedFileName, $fullPath.'/'.$oriFileName);
            }

            \Yii::error($e->getMessage().$e->getFile().$e->getLine(), 'updateapperror');
        }
        return false;
    }

}
