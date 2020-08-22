<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cashier */

$this->title = Yii::t('app/menu', 'Create Cashier');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Cashiers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cashier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cashiers'=>$cashiers,
        'msg'=>$msg,
    ]) ?>

</div>
