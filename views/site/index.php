<?php
use yii\helpers\Html;
$this->registerJs('$(function(){$(".default-page-head").remove();$(".default-page-content").attr("id", null);})');

$this->title = Yii::t('app/menu', 'home');
?>
<div id="page-head">
    <div class="pad-all text-center">
        <h3>欢迎使用 <?= Yii::t('app', 'web_name'); ?> 平台</h3>
    </div>
</div>

<div id="page-content">

</div>
