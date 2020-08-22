<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use components\assets\SelectAsset;
SelectAsset::register($this);
$this->registerJs("$('.select2').select2();", yii\web\View::POS_END);

$this->title = empty($model->id) ? Yii::t('app/view', 'create') : Yii::t('app/view', 'edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'menu'), 'url' => Url::to(['/auth/menu/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel">
    <div class="panel-heading">
        <div class="pad-all form-inline">
            <div class="pull-left">
                <?= Html::a('<i class="fa fa-chevron-left btn-labeled"></i> '.Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default btn-label']); ?>
            </div>
        </div>
    </div>
    <div class="row">
		<div class="col-md-8 col-md-offset-1">
            <?php $form = ActiveForm::begin([
        		'action' => null,
        		'options' => [
        		    'class' => 'panel-body form-horizontal form-padding ajax-form', 
        		    'data-action' => empty($model->id) ? Url::to(['/auth/menu/add']) : Url::to(['/auth/menu/edit', 'id' => $model->id])
        		],
        		'fieldConfig' => [
        			'template' => '{label}{input}',
        		    'labelOptions' => ['class' => 'control-label'],
        			'inputOptions' => ['class' => 'form-control'],
        		],
		    ]); ?>
				<div class="panel-body">
					<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
					<?= $form->field($model, 'parent')->dropDownList(ArrayHelper::map($optionsMenu, 'id', 'name'), ['prompt' => ['text' => Yii::t('app/view', 'top_menu'), 'options' => ['value' => 0]], 'class' => 'form-control select2']); ?>
					<?= $form->field($model, 'route')->dropDownList(ArrayHelper::map($optionsRoute, 'id', 'label'), ['class' => 'form-control select2']); ?>
					<?= $form->field($model, 'order')->textInput(['maxlength' => true]); ?>
					<?= $form->field($model, 'icon')->textInput(['maxlength' => true]); ?>
				</div>
				<div class="panel-footer">
					<?= Html::button(Yii::t('app/view', 'submit'), ['class' => 'btn btn-primary ajax-form-submit']); ?>
					<?= Html::a(Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default']); ?>
				</div>
				
			<?php ActiveForm::end(); ?>
				
        </div>
    </div>
</div>