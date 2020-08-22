<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>
namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use components\Controller;
use yii\web\NotFoundHttpException;
use <?= ltrim($generator->modelClass, '\\') ?>;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends Controller{

    public function actionIndex(){
    	$searchModel = new <?= $modelClass ?>();
    	$searchModel->load(Yii::$app->request->queryParams);
    	$condition = ['status' => <?= $modelClass ?>::STATUS_N];
    	$andFilter = [];
    	if(!empty($searchModel->keywords)){
			$andFilter[] = ['like', 'id', $searchModel->keywords];
		}
		
		$dataProvider  = $searchModel->adpSearch($condition, $andFilter);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }
    
    public function actionAdd(){
		$model = new <?= $modelClass ?>();
		if(Yii::$app->request->isPost){
			$model->load(Yii::$app->request->post());
			if ($model->save()) {
				return $this->response('@');
			} else {
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
		$model = <?= $modelClass ?>::findOne(['id' => $this->getGetValue('id')]);
		if(empty($model)){
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		
		if(Yii::$app->request->isPost){
			$model->load(Yii::$app->request->post());
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
		return $this->render('form', ['model' => $model]);
	}
	
	public function actionDelete(){
		$model = <?= $modelClass ?>::findOne(['id' => $this->getPostValue('id')]);
		if(empty($model)){
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		
		if($model->status == <?= $modelClass ?>::STATUS_D){
			$this->response(true);
		}
		
		$model->status = <?= $modelClass ?>::STATUS_D;
		
		if($model->save()){
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
	
	public function actionDeleteTrue(){
		$model = <?= $modelClass ?>::findOne(['id' => $this->getPostValue('id')]);
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
