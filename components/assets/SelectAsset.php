<?php
namespace components\assets;

use yii\web\AssetBundle;

class SelectAsset extends AssetBundle{
	
	public $sourcePath = '@components/assets/plugins/select2';
    public $css = [
    	'css/select2.min.css',
    ];
    public $js = [
    	'js/select2.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
