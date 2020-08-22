<?php
use yii\helpers\Html;
use yii\helpers\Url;
use sunnnnn\nifty\auth\assets\MenuAsset;
MenuAsset::register($this);

$this->title = Yii::t('app/menu', 'menu');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="panel">
    <div class="panel-body">
        <div class="pad-btm text-right">
            <?= Html::a('<i class="fa fa-plus btn-label"></i> '.Yii::t('app/view', 'create'), ['/auth/menu/add'], ['class' => 'btn btn-success btn-labeled']) ?>
        </div>
        
        <div class="table-responsive">
        	<ul class="mtree bubba">
				<?= $items; ?>
			</ul>
        </div>
    </div>
</div>
<?php $this->beginBlock('menu');?>
	$('.btn-edit').click(function(){
		var href = $(this).data('href');
		location.href = href;
	});

	$('.btn-delete').click(function(){
		var that = $(this);
		var father = that.parent().parent();
		showConfirm(_message.confirmDeleteTitle, _message.confirmDeleteTip, function (isConfirm) {
            if (isConfirm) {
            	showLoading(_message.loading);
            	$.ajax({
    				url: '<?= Url::to(['/auth/menu/delete']); ?>',
    				type: 'post',
    				data: {'id':that.data('key'), '_csrf':_csrf},
    				dataType: 'json',
    				success: function(result){
    					hideLoading();
    					if(result.result){
    						father.fadeOut('slow');
    					}else{
    						showError(result.message);
    					}  
    				},  
    				error: function(xhr) {
    					hideLoading();
    					if(xhr.status == '403'){
    						showError(_message.errorHttp403);
    					}else if(xhr.status == '404'){
    						showError(_message.errorHttp404);
    					}else{
    						showError(_message.errorHttp500);
    					}
    				}  
    			});
            } else {
                
            }
        }, {confirmButtonText: _message.confirmDelete, cancelButtonText: _message.confirmCancel, width: 400});
	});
<?php $this->endBlock();?>
<?php $this->registerJs($this->blocks['menu'], yii\web\View::POS_END);?>