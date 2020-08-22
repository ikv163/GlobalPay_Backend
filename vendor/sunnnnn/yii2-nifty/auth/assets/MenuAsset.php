<?php
namespace sunnnnn\nifty\auth\assets;

use yii\web\AssetBundle;

class MenuAsset extends AssetBundle{
	
	public $sourcePath = '@sunnnnn/nifty/auth/components/assets/menu';
    public $css = [
		'css/styles.css',
    ];
    public $js = [
    	'js/stopExecutionOnTimeout.js',
    	'js/jquery.velocity.min.js',
    	'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
