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
use yii\helpers\Url;
use app\components\Controller;
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
            if($model->save()){
                return $this->redirect(['index']);
            }
        }
        
        return $this->renderAjax('form', ['model' => $model]);
    }
    
	public function actionEdit(){
        $name  = $this->getPostValue('name', '', 'trim');
        $value = $this->getPostValue('value', '', 'trim');
        $model = <?= $modelClass ?>::findOne(['id' => $this->getPostValue('pk')]);
        if(empty($name) || empty($model)){
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        
        if($model->$name == $value){
            return true;
        }
        
        $model->$name = $value;
        $model->edit_time = time();
        
        return $model->save();
    }
    
	public function actionDelete(){
		$model = <?= $modelClass ?>::findOne(['id' => $this->getPostValue('id')]);
		if(empty($model)){
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
		
		if($model->status == <?= $modelClass ?>::STATUS_D){
			$this->outAjaxRequest(true);
		}
		
		$model->status = <?= $modelClass ?>::STATUS_D;
		
		if($model->save()){
			$this->outAjaxRequest(true);
		}else{
			$errors = $model->getErrors();
			if(!empty($errors)){
				foreach($errors as $error){
					$error = is_array($error) ? array_pop($error) : $error;
					$this->outAjaxRequest(false, $error);
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
			$this->outAjaxRequest(true);
		}else{
			$errors = $model->getErrors();
			if(!empty($errors)){
				foreach($errors as $error){
					$error = is_array($error) ? array_pop($error) : $error;
					$this->outAjaxRequest(false, $error);
					break;
				}
			}
		}
	}

}
