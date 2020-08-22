<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
	$safeAttributes = $model->attributes();
}

echo "<?php\n";
?>
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = empty($model->id) ? Yii::t('app/view', 'create') : Yii::t('app/view', 'edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>'), 'url' => ['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false); ?>/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
    <div class="panel-heading">
        <div class="pad-all form-inline">
            <div class="pull-left">
                <?= '<?= ' ?>Html::a('<i class="fa fa-chevron-left btn-labeled"></i> '.Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default btn-label']); ?>
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<?= '<?php ' ?>$form = ActiveForm::begin([
		        'action' => null,
        		'options' => [
        		    'class' => 'panel-body form-horizontal form-padding ajax-form', 
        		    'data-action' => empty($model->id) ? Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>/add']) : Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>/edit', 'id' => $model->id])
        		],
        		'fieldConfig' => [
        			'template' => '{label}{input}',
        		    'labelOptions' => ['class' => 'control-label'],
        			'inputOptions' => ['class' => 'form-control'],
        		],
		    ]); ?>
				
				<div class="panel-body">
					<?php foreach ($generator->getColumnNames() as $attribute) {
					    if (in_array($attribute, $safeAttributes)) {
					        echo "<?= " . $generator->generateActiveField($attribute) . " ?>\n\n        ";
					    }
					} ?>
				</div>

				<div class="panel-footer">
					<?= '<?= ' ?>Html::button(Yii::t('app/view', 'submit'), ['class' => 'btn btn-primary ajax-form-submit']); ?>
					<?= '<?= ' ?>Html::a(Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default']); ?>
				</div>
			<?= '<?php ' ?>ActiveForm::end(); ?>
		</div>
    </div>
</div>