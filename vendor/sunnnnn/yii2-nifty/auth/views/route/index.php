<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

$this->title = Yii::t('app/menu', 'route');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="panel">
    <div class="panel-body">
        <div class="pad-btm form-inline">
            <div class="row">
                <div class="col-sm-9 table-toolbar-left">
                    <?php $form = ActiveForm::begin([
        				'action' => Url::to(['/auth/route/index']),
        				'method' => 'get',
        				'options'=>['class' => 'form-inline search-inline'],
        				'enableClientValidation' => false,
        			]); ?>
        				<div class="form-group">
                            <div class="input-group">
        					    <?= Html::activeTextInput($searchModel, 'keywords', ['class' => 'form-control', 'placeholder' => Yii::t('app/view', 'type_for_search')]); ?>
                                <div class="input-group-btn">
    					            <?= Html::submitButton('<i class="pli-magnifi-glass"></i> '.Yii::t('app/view', 'search'), ['class' => 'btn btn-default']); ?>
                                </div>
                            </div>
        				</div>
        		    <?php ActiveForm::end(); ?>
                </div>
                
                <div class="col-sm-3 text-right">
                	<?= Html::a('<i class="fa fa-plus btn-label"></i> '.Yii::t('app/view', 'create'), ['/auth/route/add'], ['class' => 'btn btn-success btn-labeled']) ?>
                </div>
            </div>
        </div>
        <div class="table-responsive">
			<?= GridView::widget([
                'layout' => "{items}\n{summary}\n{pager}",
				'tableOptions' => ['class' => 'table table-hover', 'style' => 'min-width:630px;'],
				'dataProvider' => $dataProvider,
				'columns' => [
					[
						"attribute" => "id",
						"headerOptions" => ["width" => "10%"],
						"contentOptions" => [],
					],
					[
						"attribute" => "route",
						"headerOptions" => ["width" => "35%"],
					],
					[
						"attribute" => "name"
					],
					[
						'class' => 'yii\grid\ActionColumn',
					    "headerOptions" => ["width" => "160px"],
						"template" => "{update} {delete}",
						"header" => Yii::t('app/view', 'actions'),
						"buttons" => [
							"update" => function ($url, $model, $key){
							    return Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app/view', 'edit'), ['/auth/route/edit', 'id' => $model->id], [
									"class" => "btn btn-sm btn-default"
								]);
							},
							"delete" => function($url, $model, $key){
							    return Html::a('<i class="fa fa-trash-o"></i> '.Yii::t('app/view', 'remove'), 'javascript:;', [
									'class' => 'btn btn-sm btn-default btn-hover-danger ajax-table-delete',
								    'data-action' => Url::to(['/auth/route/delete'])
								]);
							}
						]
					],
				],
			]); ?>
        </div>
    </div>
</div>