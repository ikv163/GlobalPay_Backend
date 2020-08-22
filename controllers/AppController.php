<?php

namespace app\controllers;

use app\common\Common;
use app\common\DES;
use app\models\LogRecord;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\AppUpload;
use yii\web\UploadedFile;
use app\models\SystemConfig;

/**
 * CashierController implements the CRUD actions for Cashier model.
 */
class AppController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Lists all Cashier models.
     * @return mixed
     */
    public function actionUploadapk()
    {
        $model = new AppUpload();


        //获取当前app版本信息
        $appConfig = SystemConfig::getSystemConfig('APP_CONFIG');
        $appConfig = json_decode($appConfig, true);

        $androidAppInfo = array();
        $iosAppInfo = array();

        if ($appConfig) {
            foreach ($appConfig as $config) {
                if (isset($config['app_type']) && $config['app_type'] == 1) {
                    $androidAppInfo = $config;
                }

                if (isset($config['app_type']) && $config['app_type'] == 2) {
                    $iosAppInfo = $config;
                }
            }
        }


        if(\Yii::$app->request->isPost){

            /*echo "<pre>";
            var_dump($_POST);
            var_dump($_REQUEST);
            var_dump($model->errors);
            var_dump(UploadedFile::getInstance($model, 'app_file'));
            var_dump($_FILES);exit;*/

            $appType = isset($_POST['AppUpload']['app_type']) && is_numeric($_POST['AppUpload']['app_type']) && in_array($_POST['AppUpload']['app_type'], array(1,2)) ? $_POST['AppUpload']['app_type'] : 0;
            if(!in_array($appType, array(1,2))){
                return $this->render('upload', [
                    'model' => $model,
                    'msg' => 'app类型错误!',
                    'msg_type' => 0,
                    'android_app_info' => $androidAppInfo,
                    'ios_app_info' => $iosAppInfo,
                ]);
            }

            $appFile = UploadedFile::getInstance($model, 'app_file');
            if(!$appFile){
                return $this->render('upload', [
                    'model' => $model,
                    'msg' => '获取上传文件出错',
                    'msg_type' => 0,
                    'android_app_info' => $androidAppInfo,
                    'ios_app_info' => $iosAppInfo,
                ]);
            }

            $appVersion = isset($_POST['AppUpload']['app_version']) && $_POST['AppUpload']['app_version'] ? $_POST['AppUpload']['app_version'] : '';

            $updateMsg = isset($_POST['AppUpload']['update_msg']) && $_POST['AppUpload']['update_msg'] ? $_POST['AppUpload']['update_msg'] : '';

            $model->app_type = $appType;
            $model->app_file = $appFile;
            $model->app_version = $appVersion;
            $model->update_msg = $updateMsg;
            $result = $model->upload();

            $msg = $result ? '上传成功' : '上传失败';
            $msgType = $result ? 1 : 0;

            return $this->render('upload', [
                'model' => $model,
                'msg' => $msg,
                'msg_type' => $msgType,
                'android_app_info' => $androidAppInfo,
                'ios_app_info' => $iosAppInfo,
            ]);

        }

        return $this->render('upload', [
            'model' => $model,
            'msg_type' => 0,
            'msg'=>'',
            'android_app_info' => $androidAppInfo,
            'ios_app_info' => $iosAppInfo,
        ]);
    }

}
