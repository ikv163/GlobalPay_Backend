<?php
namespace sunnnnn\nifty\auth\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use sunnnnn\nifty\auth\components\Controller;
use sunnnnn\nifty\auth\models\AuthRoute;

class RouteController extends Controller{
	
	public function actionIndex(){
		$searchModel = new AuthRoute();
		$searchModel->load(Yii::$app->request->queryParams);
		$condition = $andFilter = [];
		if(!empty($searchModel->keywords)){
			$andFilter[] = ['or', ['like', 'name', $searchModel->keywords], ['like', 'route', $searchModel->keywords]];
		}
		$dataProvider  = AuthRoute::adpSearch($condition, $andFilter);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	public function actionAdd(){
	    $model = new AuthRoute();
	    if(Yii::$app->request->isPost){
	        $model->load(Yii::$app->request->post());
	        $model->add_time = time();
	        $model->edit_time = 0;
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
	    return $this->render('form', ['model' => $model]);
	}
	
	public function actionEdit(){
	    $model = AuthRoute::findOne(['id' => $this->getGetValue('id')]);
	    if(empty($model)){
	        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
	    }
	    if(Yii::$app->request->isPost){
	        $model->load(Yii::$app->request->post());
	        $model->edit_time = time();
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
	    return $this->render('form', ['model' => $model]);
	}
	
	public function actionDelete(){
		$model = AuthRoute::findOne(['id' => $this->getPostValue('id')]);
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
	
	public function actionGii(){
		$model = new AuthRoute();
		if(Yii::$app->request->isPost){
			if($model->load(Yii::$app->request->post()) && $model->validate()){
				$count = Yii::$app->db->createCommand()->batchInsert('auth_route', ['name', 'route', 'add_time', 'edit_time'], [
						[$model->name.'【所有权限】', '/'.trim($model->route, '/').'/*', time(), 0],
						[$model->name.'【首页展示】', '/'.trim($model->route, '/').'/index', time(), 0],
						[$model->name.'【添加操作】', '/'.trim($model->route, '/').'/add', time(), 0],
						[$model->name.'【编辑操作】', '/'.trim($model->route, '/').'/edit', time(), 0],
						[$model->name.'【删除操作】', '/'.trim($model->route, '/').'/delete', time(), 0],
						])->execute();
				if($count){
					$this->checkCache();
					return $this->response('@');
				}
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
		return $this->render('gii', ['model' => $model]);
	}
	
	private function checkCache(){
		if(Yii::$app->cache->exists(AuthRoute::CACHE_ROUTES_All)){
			Yii::$app->cache->delete(AuthRoute::CACHE_ROUTES_All);
		}
	}
}