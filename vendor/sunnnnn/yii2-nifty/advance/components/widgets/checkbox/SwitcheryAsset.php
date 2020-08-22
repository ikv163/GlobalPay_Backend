<?php
namespace components\widgets\checkbox;

use yii\web\AssetBundle;

class SwitcheryAsset extends AssetBundle{
    
    public $sourcePath = '@components/assets/plugins/switchery';
    
    public $css = [
        'switchery.css'
    ];
    
    public $js = [
        'switchery.js'
    ];
    
    public $depends = [
    ];
}
