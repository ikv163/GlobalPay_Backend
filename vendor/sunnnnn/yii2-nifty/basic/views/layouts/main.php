<?php
use yii\helpers\Html;
app\assets\AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
		<!--[if lt IE 9]>
		<script src="https://cdn.bootcss.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
    </head>
    <script>
    	var _csrf = '<?= Yii::$app->request->getCsrfToken(); ?>';
    	var _message = {
			'loading':'<?= Yii::t('app', 'loading'); ?>',
			'errorHttp403':'<?= Yii::t('app', 'error_http403'); ?>',
			'errorHttp404':'<?= Yii::t('app', 'error_http404'); ?>',
			'errorHttp500':'<?= Yii::t('app', 'error_http500'); ?>',
			'confirmDeleteTitle':'<?= Yii::t('app/view', 'confirm_delete_title'); ?>',
			'confirmDeleteTip':'<?= Yii::t('app/view', 'confirm_delete_tip'); ?>',
			'confirmDelete':'<?= Yii::t('app/view', 'confirm_delete'); ?>',
			'confirmCancel':'<?= Yii::t('app/view', 'confirm_cancel'); ?>',
			'success': '<?= Yii::t('app', 'success'); ?>',
    	};
    </script>
    <body>
    <div class="loading-screen"></div>
    <?php $this->beginBody() ?>
    <div id="container" class="effect aside-float aside-bright mainnav-lg navbar-fixed mainnav-fixed">

        <?= $this->render('header.php'); ?>
        
		<div class="boxed">
            <?= $this->render('content.php', ['content' => $content]); ?>
    
            <?php // $this->render('aside.php'); ?>
            
            <?= $this->render('left.php'); ?>
        </div>
        
        <?= $this->render('footer.php'); ?>
        
        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>
    </div>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
