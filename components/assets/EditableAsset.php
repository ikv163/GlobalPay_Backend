<?php
namespace components\assets;

use yii\web\AssetBundle;

class EditableAsset extends AssetBundle{
	
	public $sourcePath = '@components/assets/plugins/x-editable';
    public $css = [
    	'css/bootstrap-editable.css',
    ];
    public $js = [
    	'js/bootstrap-editable.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
