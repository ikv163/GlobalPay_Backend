<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'login');
?>
<div id="container" class="cls-container">
        
	<!-- BACKGROUND IMAGE -->
	<div id="bg-overlay" class="bg-img" style="background-image: url('/images/bg-img/bg-img-<?= rand(1, 7); ?>.jpg')"></div>
		
		
	<!-- LOGIN FORM -->
	<div class="cls-content">
	    <div class="cls-content-sm panel">
	        <div class="panel-body">
	            <div class="mar-ver pad-btm">
	                <h1 class="h3">Yii2 Nifty</h1>
	                <p><?= Yii::t('app', 'account_login'); ?></p>
	            </div>
	            
	            <?php $form = ActiveForm::begin([
        	        'id' => 'login-form',
        			'action' => null,
        			'options' => ['data-action' => Url::to(['/site/ajax-login'])],
        	    ]); ?>
	                <div class="form-group">
	                	<?= Html::activeTextInput($model, 'username', ['class' => 'form-control', 'placeholder' => Yii::t('app', 'username'), 'autofocus' => true]) ?>
	                </div>
	                <div class="form-group">
	                	<?= Html::activePasswordInput($model, 'password', ['class' => 'form-control', 'placeholder' => Yii::t('app', 'password')]) ?>
	                </div>
	                <div class="checkbox pad-btm text-left">
	                	<?= Html::activeCheckbox($model, 'rememberMe', ['id' => 'rememberMe', 'label' => false, 'class' => 'magic-checkbox']); ?>
	                    <label for="rememberMe"><?= Yii::t('app', 'remember_me'); ?></label>
	                </div>
	                
	                <?= Html::Button(Yii::t('app', 'signin'), ['class'=>'btn btn-primary btn-lg btn-block login-submit']) ?>
	            <?php ActiveForm::end(); ?>
	            
	        </div>
	    </div>
	</div>
</div>

<?php $this->beginBlock('login') ?> 
$(function(){
	document.onkeydown = function(e){ 
	    var ev = document.all ? window.event : e;
	    if(ev.keyCode == 13) {
			$('.login-submit').click();
		}
	}
	
	$('.login-submit').click(function(){
    	var that = $(this);
    	showLoading('<?= Yii::t('app', 'loading'); ?>');
		$.ajax({
	        type: 'post',
	        url: $('#login-form').data('action'),
	        data: $('#login-form').serialize(),
	        dataType: 'json',
	        error: function(xhr) {
	        	hideLoading();
	        	showMessage('<?= Yii::t('app', 'error_http500'); ?>');
	        },
	        success: function(result) {
		        if(result.result){
		        	location.href = result.result;
		        }else{
		        	hideLoading();
	        		showMessage(result.message);
		        }
	        }
	    });
	});
});
<?php $this->endBlock() ?>  
<?php $this->registerJs($this->blocks['login'], \yii\web\View::POS_END); ?> 