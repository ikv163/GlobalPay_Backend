<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = '生成路由';
$this->params['breadcrumbs'][] = ['label' => '路由列表', 'url' => Url::to(['/auth/route/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary">
	<?php $form = ActiveForm::begin([
				'id' => 'ajax-form',
        		'options' => ['class' => 'ajax-form', 'data-action' => Url::to(['/auth/route/gii'])],
        		'fieldConfig' => [
        			'template' => "{label}{input}",
        			'inputOptions' => ['class' => 'form-control'],
        		],
				'enableClientValidation' => false,
		    ]); ?>
	
		<div class="box-body">
			<?= $form->field($model, 'name')->textInput(['autocomplete' => 'off']); ?>
			<?= $form->field($model, 'route')->textInput(['autocomplete' => 'off']); ?>
		</div>

		<div class="box-footer">
			<?= Html::button('提交', ['type' => 'button', 'class' => 'btn btn-primary ajax-form-submit']); ?>
			<?= Html::a('返回', 'javascript:history.back();', ['class' => 'btn btn-default']); ?>
		</div>
	
	<?php ActiveForm::end(); ?>
</div>
