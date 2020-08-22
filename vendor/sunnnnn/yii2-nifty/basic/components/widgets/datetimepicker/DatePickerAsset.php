<?php
namespace components\widgets\datetimepicker;

use yii\web\AssetBundle;

class DatePickerAsset extends AssetBundle{
    
    public $sourcePath = '@components/assets/plugins/bootstrap-datepicker';
    
    public $css = [
        'bootstrap-datepicker.css'
    ];
    
    public $js = [
        'bootstrap-datepicker.js'
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
