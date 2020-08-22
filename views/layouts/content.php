<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
?>
<div id="content-container">
    <div id="page-head" class="default-page-head">
        
        <!--Page Title-->
        <div id="page-title">
            <h1 class="page-header text-overflow"><?= $this->title; ?></h1>
        </div>
        <!--End page title-->


        <!--Breadcrumb-->
        <?= Breadcrumbs::widget([
            'tag' => 'ol',
            'encodeLabels' => false,
    		'homeLink'=>[
    		   'label' => Yii::t('app/menu', 'home'),
    		   'url' => Url::to(['/site/index']),
    		],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <!--End breadcrumb-->

    </div>

    
    <!--Page content-->
    <!--===================================================-->
    <div id="page-content" class="default-page-content">
    	<?= $content; ?>
    </div>
</div>