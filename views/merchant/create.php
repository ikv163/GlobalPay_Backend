<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Merchant */

$this->title = Yii::t('app/menu', 'Create Merchant');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Merchants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
