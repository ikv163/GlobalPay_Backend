<?php
namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use components\Controller;
use app\models\Admin;
use sunnnnn\nifty\auth\models\AuthRoles;

class AdminController extends Controller{

    public function actionIndex(){
		$searchModel = new Admin();
    	$searchModel->load(Yii::$app->request->queryParams);
    	$condition = $andFilter = [];
    	if(!empty($searchModel->role)){
    	    $condition['role'] = $searchModel->role;
    	}
    	if(!empty($searchModel->keywords)){
    		$andFilter[] = ['like', 'username', $searchModel->keywords];
    	}
    	
    	$dataProvider  = $searchModel::filterSearch($condition, $andFilter);
    	
    	$optionsRole = AuthRoles::find()->all();
    	if(!empty($optionsRole)){
    	    foreach($optionsRole as $key => $val){
    	        $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
    	    }
    	}
    	return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
    	    'optionsRole' => $optionsRole
		]);
    }
    
    public function actionAdd(){
    	$model = new Admin();
    	if(Yii::$app->request->isPost){
	    	$model->load(Yii::$app->request->post());
	    	$model->setPassword($model->password);
	    	$model->auth_key = Admin::generateAuthKey();
	    	$model->add_time = time();
	    	$model->edit_time = 0;
	    	$model->login_time = 0;
	    	if ($model->save()) {
	    		return $this->response('@');
	    	}else{
	    		$errors = $model->getErrors();
	    		if(!empty($errors)){
	    			foreach($errors as $error){
	    				$error = is_array($error) ? array_pop($error) : $error;
	    				return $this->response(false, $error);
	    				break;
	    			}
	    		}
	    	}
    	}
    	$model->status = 0;
    	$optionsRole = AuthRoles::find()->all();
    	foreach($optionsRole as $key => $val){
    	    $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
    	}
    	return $this->render('form', [
    		'model' => $model, 
    	    'optionsRole' => $optionsRole
		]);
    }
    
    public function actionEdit(){
    	$model = Admin::findOne(['id' => $this->getGetValue('id')]);
    	if(empty($model)){
    		throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    	}
    	
    	if(Yii::$app->request->isPost){
    		$data = Yii::$app->request->post('Admin');
    		$data['edit_time'] = time();
    		$data['password'] = empty($data['password']) ? $model->password : Yii::$app->security->generatePasswordHash($data['password']);
    		$model->setAttributes($data);
    		if($model->save()){
    		    $this->response('@');
    		}else{
    			$errors = $model->getErrors();
    			if(!empty($errors)){
    				foreach($errors as $error){
    					$error = is_array($error) ? array_pop($error) : $error;
    					$this->response(false, $error);
    					break;
    				}
    			}
    		}
    	}
    	
    	$optionsRole = AuthRoles::find()->all();
    	foreach($optionsRole as $key => $val){
    	    $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
    	}
    	return $this->render('form', [
    		'model' => $model, 
    	    'optionsRole' => $optionsRole
		]);
    }
    
    public function actionDelete(){
    	$model = Admin::findOne(['id' => $this->getPostValue('id')]);
    	if(empty($model)){
    		throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    	}
    
    	if($model->delete()){
    	    $this->response(true);
    	}else{
    		$errors = $model->getErrors();
    		if(!empty($errors)){
    			foreach($errors as $error){
    				$error = is_array($error) ? array_pop($error) : $error;
    				$this->response(false, $error);
    				break;
    			}
    		}
    	}
    }
    
}
