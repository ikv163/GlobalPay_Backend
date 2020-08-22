<?php
namespace components\widgets\select;

use yii\web\AssetBundle;

class BsSelectAsset extends AssetBundle{ 
    
    public $sourcePath = '@components/assets/plugins/bootstrap-select';
    
    public $css = [
        'bootstrap-select.min.css'
    ];
    
    public $js = [
        'bootstrap-select.min.js'
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
