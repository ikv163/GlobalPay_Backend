<?php
use yii\helpers\Html;
$this->registerJs('$(function(){$(".default-page-head").remove();$(".default-page-content").attr("id", null);})');

$this->title = Yii::t('app/menu', 'home');
?>
<div id="page-head">
    <div class="pad-all text-center">
        <h3>Welcome back to the Dashboard.</h3>
        <p1>Scroll down to see quick links and overviews of your Server, To do list, Order status or get some Help using Yii2 Nifty.</p>
    </div>
</div>

<div id="page-content">

</div>
