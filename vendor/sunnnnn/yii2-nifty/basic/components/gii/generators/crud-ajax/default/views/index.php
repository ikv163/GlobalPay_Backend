<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\assets\EditableAsset;
EditableAsset::register($this);

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'id' => 'modal-add',
    'header' => '<h4 class="modal-title">添加</h4>',
]);
Modal::end();
?>
<section class="content">
	<div class="row">
        <div class="col-md-6">
			<?= '<?php ' ?> $form = ActiveForm::begin([
				'action' => Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>/index']),
				'method' => 'get',
				'options'=>['class' => 'table-search'],
				'enableClientValidation' => false,
			]); ?>
			<div class="input-group">
				<?= '<?= ' ?> Html::activeTextInput($searchModel, 'keywords', ['class' => 'form-control', 'placeholder' => '输入关键词进行搜索']); ?>
	        	<span class="input-group-btn">
	        		<?= '<?= ' ?> Html::submitButton('<i class="fa fa-search"></i> 搜索', ['class' => 'btn btn-info btn-flat']); ?>
	        	</span>
			</div>
		    <?= '<?php ' ?> ActiveForm::end(); ?>
        </div>
    </div>
    
	<div class="row">
        <div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title"><?= '<?= ' ?> Html::encode($this->title) ?></h3>
					
					<div class="box-tools">
						<?= '<?= ' ?> Html::a('<i class="fa fa-plus btn-label"></i> 添加', 'javascript:;', [
						    'class' => 'btn btn-success btn-labeled btn-add-modal',
						    'data-action' => Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>/add'])
						]) ?>
					</div>
				</div>
				
				<div class="box-body table-responsive no-padding">
					<?= '<?= ' ?> GridView::widget([
						'summary' => false,
						'tableOptions' => ['class' => 'table table-hover'],
						'dataProvider' => $dataProvider,
						'columns' => [ 
							[
								'attribute' => 'id',
								'headerOptions' => ['width' => '80%', 'style' => 'text-align:center'],
								'contentOptions' => ['align' => 'center', 'style' => 'font-size:18px;'],
								'format' => 'raw',
								'value' => function($model) {
    								return Html::a($model->id, 'javascript:;', [
    								    'class' => 'editable',
    								    'data-type' => 'text',
    								    'data-pk'   => $model->id,
    								    'data-name'   => 'id',
    								    'data-url' => Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false) ?>/edit']),
    								    'data-title' => '',
    								]);
								},
							],
							<?php
							$count = 0;
							if (($tableSchema = $generator->getTableSchema()) === false) {
							    foreach ($generator->getColumnNames() as $name) {
							        if (++$count < 6) {
							            echo "            '" . $name . "',\n";
							        } else {
							            echo "            // '" . $name . "',\n";
							        }
							    }
							} else {
							    foreach ($tableSchema->columns as $column) {
							        $format = $generator->generateColumnFormat($column);
							        if (++$count < 6) {
							            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
							        } else {
							            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
							        }
							    }
							}
							?>
            				[
								'class' => 'yii\grid\ActionColumn',
								"template" => "{delete}",
								"header" => "操作",
								"buttons" => [
									"delete" => function($url, $model, $key){
										return Html::a('<i class="fa fa-trash-o"></i> 删除', 'javascript:;', [
											'class' => 'btn btn-sm btn-default btn-hover-danger ajax-table-delete',
											'data-action' => Url::to(['/<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), false) ?>/delete'])
										]);
									}
								]
							],
        				],
    				]); ?>
    			</div>
    		</div>
    	</div>
    </div>
</section>
<?= '<?php' ?> $this->beginBlock('modal') ?> 
$(function(){
    $('.editable').editable();
    
	$('.btn-add-modal').click(function(){
		var that = $(this);
		var load = layer.load();
		$.get(that.data('action'), {},
            function(result){
            	layer.close(load);
            	$('.modal-body').html(result);
                $('#modal-add').modal('show');
            }  
        );
	});
})
<?= '<?php' ?> $this->endBlock() ?>  
<?= '<?php' ?> $this->registerJs($this->blocks['modal'], \yii\web\View::POS_END); ?> 