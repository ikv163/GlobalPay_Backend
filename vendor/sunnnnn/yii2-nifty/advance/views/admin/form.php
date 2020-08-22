<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Admin;

$this->title = empty($model->id) ? Yii::t('app/view', 'create') : Yii::t('app/view', 'edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'administrator'), 'url' => Url::to(['/admin/index'])];
$this->params['breadcrumbs'][] = $this->title;

$profilePhotos = Admin::getProfilePhotos();
?>
<div class="panel">
    <div class="panel-heading">
        <div class="pad-all form-inline">
            <div class="table-toolbar-left">
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
        		    'data-action' => empty($model->id) ? Url::to(['/admin/add']) : Url::to(['/admin/edit', 'id' => $model->id])
        		],
        		'fieldConfig' => [
        			'template' => '{label}{input}',
        		    'labelOptions' => ['class' => 'control-label'],
        			'inputOptions' => ['class' => 'form-control'],
        		],
		    ]); ?>
				<div class="panel-body">
    				<div class="form-group">
    					<?= Html::activeHiddenInput($model, 'photo', ['id' => 'profile-photo', 'value' => empty($model->photo) ? Admin::getProfilePhotos(true) : $model->photo]); ?>
    					<label class="control-label"><?= Yii::t('app/model', 'admin_photo'); ?></label>
    					<div><img src="<?= empty($model->photo) ? Admin::getProfilePhotos(true) : $model->photo; ?>" class="img-circle img-md img-profile-photo"/></div>
    				</div>
    				<div class="form-group profile-photos">
    					<div>
    						<?php foreach($profilePhotos as $path){ ?>
                    		<img src="<?= $path; ?>" class="img-circle img-sm" />
    						<?php } ?>
    					</div>
                    </div>

    				<?= $form->field($model, 'username')->textInput(['autocomplete' => 'off', 'maxlength' => true]); ?>
					<?php if(empty($model->id)){
					    echo $form->field($model, 'password')->passwordInput(['autocomplete' => 'off', 'maxlength' => true]);
					}else{
					    echo $form->field($model, 'password')->passwordInput(['autocomplete' => 'off', 'maxlength' => true, 'value' => '', 'placeholder' => Yii::t('app/view', 'edit_password_placeholder')]);
					}?>
					<?= $form->field($model, 'role')->dropDownList(ArrayHelper::map($optionsRole, 'id', 'name')); ?>
					<?= $form->field($model, 'status')->radioList(Admin::getStatusArr(), ['class' => 'radio',
                        'item' => function($index, $label, $name, $checked, $value) {
                            $check = empty($checked) ? '' : 'checked';
                            $return = "<input type='radio' class='magic-radio' id='magix_radio_{$index}' name='{$name}' value='{$value}' {$check}>";
                            $return .= "<label for='magix_radio_{$index}'>" . ucwords($label) . "</label>";
                            return $return;
                        }
                    ]); ?>
				</div>
				<div class="panel-footer">
					<?= Html::button(Yii::t('app/view', 'submit'), ['class' => 'btn btn-primary ajax-form-submit']); ?>
					<?= Html::a(Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-default']); ?>
				</div>
				
			<?php ActiveForm::end(); ?>
				
        </div>
    </div>
</div>

<?php $this->beginBlock('photo');?>
$(function(){
	$('.img-profile-photo').click(function(){
		$('.profile-photos').toggle('slow');
	});
	
	$('.profile-photos img').click(function(){
		var src = $(this).attr('src');
		$('#profile-photo').val(src);
		$('.img-profile-photo').attr('src', src);
		$('.profile-photos').fadeOut();
	});
})
<?php $this->endBlock();?>
<?php $this->registerJs($this->blocks['photo'], yii\web\View::POS_END);?>