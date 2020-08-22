<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\AdminInfo;

?>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
    	<hr>
        <?php $form = ActiveForm::begin([
            'action' => null,
        	'options' => [
        	    'class' => 'panel-body form-horizontal form-padding profile-form', 
        	    'data-action' => Url::to(['/user/edit-profile'])
        	],
        	'fieldConfig' => [
        		'template' => '{label}{input}',
        	    'labelOptions' => ['class' => 'control-label'],
        		'inputOptions' => ['class' => 'form-control'],
        	],
        ]); ?>
        	
        	<div class="panel-body">
        	    <?= $form->field($model, 'admin_id')->hiddenInput()->label(false); ?>
        
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                
                <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
                
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                
                <?= $form->field($model, 'gender')->radioList(AdminInfo::getGenderArr(), ['class' => 'radio',
                    'item' => function($index, $label, $name, $checked, $value) {
                        $check = empty($checked) ? '' : 'checked';
                        $return = "<input type='radio' class='magic-radio' id='magix_radio_{$index}' name='{$name}' value='{$value}' {$check}>";
                        $return .= "<label for='magix_radio_{$index}'>" . ucwords($label) . "</label>";
                        return $return;
                    }
                ]); ?>
                
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        	</div>
        
        	<div class="panel-footer">
        		<?= Html::button(Yii::t('app/view', 'submit'), ['class' => 'btn btn-primary profile-form-submit']); ?>
        	</div>
        <?php ActiveForm::end(); ?>
	</div>
</div>
<?php $this->beginBlock('profile-form');?>
$(function(){
	$('.profile-form-submit').click(function(){
    	var that = $(this);
		var form = $('.profile-form');
		showLoading(_message.loading);
		$.ajax({
	        type: 'post',
	        url: form.data('action'),
	        data: form.serialize(),
	        dataType: 'json',
	        error: function(xhr) {
	        	hideLoading();
				if(xhr.status == '403'){
					showError(_message.errorHttp403);
				}else if(xhr.status == '404'){
					showError(_message.errorHttp404);
				}else{
					showError(_message.errorHttp500);
				}
	        },
	        success: function(result) {
	        	hideLoading();
		        if(result.result){
					location.reload();
		        }else{
		        	showError(result.message);
		        }
	        }
	    });
	});
})
<?php $this->endBlock();?>
<?php $this->registerJs($this->blocks['profile-form'], yii\web\View::POS_END);?>