<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
	$safeAttributes = $model->attributes();
}

echo "<?php\n";
?>
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<?= '<?php ' ?>$form = ActiveForm::begin([
	'id' => 'form',
    'action' => Url::to(['/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass), false); ?>/add']),
	'fieldConfig' => [
		'template' => '<div class="form-group">{label}{input}</div>',
		'inputOptions' => ['class' => 'form-control'],
	],
]); ?>

	<div class="box-body">
	<?php foreach ($generator->getColumnNames() as $attribute) {
	    if (in_array($attribute, $safeAttributes)) {
	        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
	    }
	} ?>
	</div>

	<div class="box-footer">
		<?= '<?= ' ?>Html::button('提交', ['class' => 'btn btn-primary', 'type' => 'submit']); ?>
	</div>

<?= '<?php ' ?> ActiveForm::end(); ?>
