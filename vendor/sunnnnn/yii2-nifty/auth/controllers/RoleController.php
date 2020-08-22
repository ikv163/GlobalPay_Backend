<?php
namespace sunnnnn\nifty\auth\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use sunnnnn\nifty\auth\components\Controller;
use sunnnnn\nifty\auth\models\AuthRoles;
use sunnnnn\nifty\auth\models\AuthRoute;

class RoleController extends Controller{
	
	public function actionIndex(){
		$searchModel = new AuthRoles();
		$searchModel->load(Yii::$app->request->queryParams);
		$condition = $andFilter = [];
		if(!empty($searchModel->keywords)){
			$andFilter[] = ['like', 'name', $searchModel->keywords];
		}
		$dataProvider  = AuthRoles::adpSearch($condition, $andFilter);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionAdd(){
		$model = new AuthRoles();
		if(Yii::$app->request->isPost){
			$model->load(Yii::$app->request->post());
			$model->add_time = time();
			$model->edit_time = 0;
			if(!empty($model->routes) && is_array($model->routes)){
				$model->routes = implode(",", $model->routes);
			}
			if ($model->save()) {
				$this->checkCache();
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
		return $this->render('form', [
			'model' => $model, 
			'options' => $this->getOptions(),
		]);
	}
	
	public function actionEdit(){
		$model = AuthRoles::findOne(['id' => $this->getGetValue('id')]);
		if(empty($model)){
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		if(Yii::$app->request->isPost){
			$model->load(Yii::$app->request->post());
			$model->edit_time = time();
			if(!empty($model->routes) && is_array($model->routes)){
				$model->routes = implode(",", $model->routes);
			}
			if($model->save()){
				$this->checkCache();
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
		return $this->render('form', [
			'model' => $model, 
			'options' => $this->getOptions($model->id),
		]);
	}
	
	public function actionDelete(){
		$model = AuthRoles::findOne(['id' => $this->getPostValue('id')]);
		if(empty($model)){
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		
		if($model->delete()){
			$this->checkCache();
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
	
	private function getOptions($role = null){
		$_return = [];
		$routeAll = AuthRoute::getRoutesAll();
		if($role === null){
			foreach($routeAll as $route => $val){
				$_return['avaliable'][$route] = [
					'id' => $val['id'],
					'label' => $val['label'],
				];
			}
		}else{
			$routeRole = AuthRoute::getRoutesByRole();
			$routeMy = isset($routeRole[$role]) ? $routeRole[$role] : [];
			foreach($routeAll as $route => $val){
				if(isset($routeMy[$route])){
					$_return['assigned'][$route] = [
						'id' => $val['id'],
						'label' => $val['label'],
					];
				}else{
					$_return['avaliable'][$route] = [
						'id' => $val['id'],
						'label' => $val['label'],
					];
				}
			}
		}
	
		return $_return;
	}
	
	private function checkCache(){
		if(Yii::$app->cache->exists(AuthRoute::CACHE_ROUTES_ROLE)){
			Yii::$app->cache->delete(AuthRoute::CACHE_ROUTES_ROLE);
		}
	}
	
}