<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cashier */

$this->title = Yii::t('app/menu', 'Update Cashier: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Cashiers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/menu', 'Update');
?>
<div class="cashier-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cashiers'=>$cashiers,
        'msg'=>$msg,
    ]) ?>

</div>
