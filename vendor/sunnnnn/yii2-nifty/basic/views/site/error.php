<?php
use yii\helpers\Html;
$this->registerJs('$(function(){$(".default-page-head").remove();$(".default-page-content").attr("id", null);})');
$this->title = $name;
?>
<div id="page-head">
	<h3 class="panel-title"><?= Html::a('<i class="pli-left-4"></i> '.Yii::t('app/view', 'back'), 'javascript:history.back();', ['class' => 'btn btn-mint']); ?></h3>
    <div class="text-center cls-content">
        <h1 class="error-code text-info" style="font-size: 60px;"><i class="pli-depression"></i></h1>
    </div>
</div>

<div id="page-content">

	<div class="text-center cls-content" style="padding-top: 50px;">
	    <p class="h4 text-uppercase text-bold"><?= nl2br(Html::encode($message)) ?></p>
	    <div class="pad-btm">
	        <?= Html::encode($this->title)?>
	    </div>
	</div>

</div>
