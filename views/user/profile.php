<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Admin;
use app\models\AdminInfo;

$this->title = Yii::t('app/menu', 'profile');
$profilePhotos = Admin::getProfilePhotos();
?>
<div class="panel profile">
    <div class="widget-header">
        <img class="widget-bg img-responsive" src="/images/thumbs/img3.jpg">
    </div>
    <div class="widget-body text-center">
        <img class="widget-img img-circle img-border-light profile-photo" src="<?= empty($model->photo) ? Admin::getProfilePhotos(true) : $model->photo; ?>">
        <div class="profile-photos pad-btm">
    		<div>
    			<?php foreach($profilePhotos as $path){ ?>
        		<img src="<?= $path; ?>" class="img-circle img-sm" />
    			<?php } ?>
    		</div>
        </div>
        <h4 class="mar-no text-main"><?= empty($model->username) ? '-' : $model->username; ?></h4>
        <p class="text-muted" style="margin-top: 10px;">
        	<?= empty($model->roles->name) ? '' : '<label class="label label-mint">'.Yii::t('app/menu', $model->roles->name).'</label>'; ?>
        </p>
    </div>
    
    <div class="profile-append">
        <div class="panel-body">
            <div class="text-right">
                <button class="btn btn-sm btn-default profile-edit"><i class="fa fa-pencil"></i> <?= Yii::t('app/view', 'edit'); ?></button>
            </div>
        	<hr>
        	<div class="row pad-top">
        		<div class="col-xs-6 text-main text-bold text-right"><i class="pli-id-card icon-lg icon-fw"></i> <?= Yii::t('app/view', 'full_name'); ?></div>
        		<div class="col-xs-6"><?= empty($model->admin->name) ? Yii::t('app', 'not_set') : $model->admin->name; ?></div>
        	</div>
        	<div class="row pad-top">
        		<div class="col-xs-6 text-main text-bold text-right"><i class="pli-bisexual icon-lg icon-fw"></i> <?= Yii::t('app/view', 'gender'); ?></div>
        		<div class="col-xs-6"><?= empty($model->admin->gender) ? Yii::t('app', 'not_set') : AdminInfo::getGenderArr($model->admin->gender); ?></div>
        	</div>
        	<div class="row pad-top">
        		<div class="col-xs-6 text-main text-bold text-right"><i class="pli-smartphone-3 icon-lg icon-fw"></i> <?= Yii::t('app/view', 'phone_number'); ?></div>
        		<div class="col-xs-6"><?= empty($model->admin->mobile) ? Yii::t('app', 'not_set') : $model->admin->mobile; ?></div>
        	</div>
        	<div class="row pad-top">
        		<div class="col-xs-6 text-main text-bold text-right"><i class="pli-email icon-lg icon-fw"></i> <?= Yii::t('app/view', 'email'); ?></div>
        		<div class="col-xs-6"><?= empty($model->admin->email) ? Yii::t('app', 'not_set') : $model->admin->email; ?></div>
        	</div>
        	<div class="row pad-top">
        		<div class="col-xs-6 text-main text-bold text-right"><i class="pli-map-marker-2 icon-lg icon-fw"></i> <?= Yii::t('app/view', 'address'); ?></div>
        		<div class="col-xs-6"><?= empty($model->admin->address) ? Yii::t('app', 'not_set') : $model->admin->address; ?></div>
        	</div>
        </div>
    </div>
    
</div>

<?php $this->beginBlock('profile');?>
$(function(){
	$('.profile .profile-photo').click(function(){
		$('.profile-photos').toggle('slow');
	});
	
	$('.profile-photos img').click(function(){
		var that = $(this);
		var src = that.attr('src');
		
		if(src){
    		showLoading(_message.loading);
    		$.ajax({
    	        type: 'post',
    	        url: '<?= Url::to('/user/edit-photo'); ?>',
    	        data: {'_csrf':_csrf, 'src':src},
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
    					$('.profile-photo').attr('src', src);
						$('.profile-photos').fadeOut();
    		        }else{
    		        	showError(result.message);
    		        }
    	        }
    	    });
		}
	});
	
	$('.profile-edit').click(function(){
		$('.profile-append').load('<?= Url::to('/user/edit-profile'); ?>');
	});
})
<?php $this->endBlock();?>
<?php $this->registerJs($this->blocks['profile'], yii\web\View::POS_END);?>