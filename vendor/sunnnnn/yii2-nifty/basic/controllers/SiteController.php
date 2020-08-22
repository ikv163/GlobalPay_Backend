<?php
namespace app\controllers;

use Yii;
use yii\helpers\Url;
use components\Controller;
use app\models\LoginForm;
/**
 * 
* @use: 后台首页以及登录相关
* @date: 2017-4-19 下午1:17:25
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class SiteController extends Controller{
	
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(){
        return $this->render('index');
    }

    public function actionLogin(){
    	$this->layout = '/login';
        $model = new LoginForm();
		return $this->render('login', ['model' => $model]);
    }
    
    public function actionAjaxLogin(){
    	$model = new LoginForm();
    	if ($model->load(Yii::$app->request->post()) && $model->login()) {
    		$this->response(Url::to(['/site/index']));
    	}else{
    		$error = $model->getErrors();
    		if(!empty($error)){
    			foreach($error as $err){
    				$err = is_array($err) ? array_pop($err) : $err;
    				$this->response(false, $err);
    				break;
    			}
    		}
    	}
    }

    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }
    
    public function actionLanguage(){
        $language =  $this->getGetValue('lang', 'en', 'trim');
        $this->switchLanguage($language);
        $this->redirect(Yii::$app->request->referrer);
    }
}
