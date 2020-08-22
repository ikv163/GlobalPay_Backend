<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Admin;

$this->title = Yii::t('app/menu', 'user');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row pad-btm">
    <div class="col-sm-9 toolbar-left">
    	<?php $form = ActiveForm::begin([
			'action' => Url::to(['/user/index']),
			'method' => 'get',
			'options'=>['class' => 'form-inline search-inline'],
			'enableClientValidation' => false,
		]); ?>
			<div class="form-group">
				<?= Html::activeTextInput($searchModel, 'keywords', ['class' => 'form-control', 'placeholder' => Yii::t('app/view', 'type_for_search')]); ?>
			</div>
			<div class="form-group">
				<?= \components\widgets\select\BsSelect::widget([
				    'model' => $searchModel,
				    'attribute' => 'status',
				    '_data' => Admin::getStatusArr(),
				    '_search' => false,
				    'options' => ['class' => 'form-control']
				]); ?>
			</div>
			<?= Html::submitButton('<i class="pli-magnifi-glass"></i> '.Yii::t('app/view', 'search'), ['class' => 'btn btn-default']); ?>
	    <?php ActiveForm::end(); ?>
    </div>
    <div class="col-sm-3 toolbar-right">
        <?= Html::a('<i class="fa fa-plus btn-labeled"></i> '.Yii::t('app/view', 'create'), ['/user/add'], ['class' => 'btn btn-success btn-labeled']) ?>
    </div>
</div>

<div class="row">
	<?php foreach($dataProvider as $key => $model){ ?>
    <div class="col-sm-4 col-md-3">
        <div class="panel pos-rel">
        	<div class="widget-control text-right">
        		<label class="label label-<?= $model->status == Admin::STATUS_N ? 'success' : 'danger'; ?>"><?= Admin::getStatusArr($model->status); ?></label>
        		<?= Html::a('<i class="fa fa-pencil"></i>', ['/user/edit', 'id' => $model->id], [
					"class" => "btn btn-sm btn-default"
				]); ?>
            </div>
            <div class="pad-all">
                <div class="media pad-ver">
                    <div class="media-left">
                        <img alt="Profile Picture" class="img-md img-circle" src="<?= empty($model->photo) ? Admin::getProfilePhotos(true) : $model->photo; ?>">
                    </div>
                    <div class="media-body" style="padding-top: 10px;">
                        <div class="box-inline">
                            <span class="text-lg text-semibold text-main"><?= isset($model->username) ? $model->username : '-'; ?></span>
                            <p class="text-sm" style="margin-top: 5px;"><?= isset($model->roles->name) ? Yii::t('app/menu', $model->roles->name) : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="pad-btm bord-bt text-sm">
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-id-card icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->name) ? Yii::t('app', 'not_set') : $model->admin->name; ?></div>
                	</div>
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-smartphone-3 icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->mobile) ? Yii::t('app', 'not_set') : $model->admin->mobile; ?></div>
                	</div>
                	<div class="row pad-top-5">
    					<div class="col-xs-2 text-right text-main text-semibold"><i class="pli-email icon-lg icon-fw"></i></div>
    					<div class="col-xs-10"><?= empty($model->admin->email) ? Yii::t('app', 'not_set') : $model->admin->email; ?></div>
                	</div>
				</div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>