<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

$this->registerJs($this->render('role.js'), yii\web\View::POS_END);

$this->title = empty($model->id) ? Yii::t('app/view', 'create') : Yii::t('app/view', 'edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'role'), 'url' => Url::to(['/auth/role/index'])];
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
		<div class="col-md-10 col-md-offset-1">
            <?php $form = ActiveForm::begin([
                'id' => 'route-form',
        		'action' => null,
        		'options' => [
        		    'class' => 'panel-body form-horizontal form-padding route-form', 
        		    'data-action' => empty($model->id) ? Url::to(['/auth/role/add']) : Url::to(['/auth/role/edit', 'id' => $model->id])
        		],
        		'fieldConfig' => [
        			'template' => '{label}{input}',
        		    'labelOptions' => ['class' => 'control-label'],
        			'inputOptions' => ['class' => 'form-control'],
        		],
		    ]); ?>
				<div class="panel-body">
    				<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
    				
					<?= $form->field($model, 'remark')->textarea(['maxlength' => true]); ?>
					
					<div class="row">
				        <div class="col-md-5">
				        	<label><?= Yii::t('app/view', 'role_unassigned_permissions'); ?></label>
				        	<?= Html::textInput(null, null, ['class' => 'form-control search', 'data-target' => 'avaliable', 'placeholder' => Yii::t('app/view', 'type_for_search')]); ?>
				        	<?= Html::ListBox(null, null, [], ['multiple' => 'multiple', 'size' => '20', 'class' => 'form-control list', 'data-target' => 'avaliable']); ?>
				        </div>
				        <div class="col-md-2">
				            <br><br>
				            <?= Html::a('&gt;&gt; '.Yii::t('app/view', 'role_assign'), 'javascript:;', [
				                'class' => 'btn btn-success btn-assigned',
				            ]) ?><br><br>
				            <?= Html::a('&lt;&lt; '.Yii::t('app/view', 'role_remove'), 'javascript:;', [
				                'class' => 'btn btn-danger btn-avaliable',
				            ]) ?>
				        </div>
				        <div class="col-md-5">
				        	<label><?= Yii::t('app/view', 'role_assigned_permissions'); ?></label>
				        	<?= Html::textInput(null, null, ['class' => 'form-control search', 'data-target' => 'assigned', 'placeholder' => Yii::t('app/view', 'type_for_search')]); ?>
				            <?= Html::activeListBox($model, 'routes', [], ['multiple' => 'multiple', 'size' => '20', 'class' => 'form-control list', 'data-target' => 'assigned']); ?>
				        </div>
				    </div>
				</div>
				
				<div class="panel-footer">
					<?= Html::button(Yii::t('app/view', 'submit'), ['class' => 'btn btn-primary route-form-submit']); ?>
					<?= Html::a(Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default']); ?>
				</div>
				
			<?php ActiveForm::end(); ?>
				
        </div>
    </div>
</div>
<script>
	var opts = <?= Json::htmlEncode($options); ?>;
	var _opts = new Object;
	_opts.avaliable = opts.avaliable || {};
	_opts.assigned  = opts.assigned || {};
</script>