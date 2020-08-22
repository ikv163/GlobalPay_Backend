<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\WhiteIp */

$this->title = Yii::t('app/menu', 'Create White Ip');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'white ip'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="white-ip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
