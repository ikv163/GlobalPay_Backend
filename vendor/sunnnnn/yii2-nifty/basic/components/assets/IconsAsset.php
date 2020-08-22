<?php
namespace components\assets;

use yii\web\AssetBundle;

class IconsAsset extends AssetBundle{
	
    public $sourcePath = '@components/assets/plugins/icons';
    public $css = [
        /*
        'ionicons/css/ionicons.min.css',
        'flag-icon-css/css/flag-icon.min.css',
        'themify-icons/themify-icons.min.css',
        'weather-icons/css/weather-icons.min.css',
        'weather-icons/css/weather-icons-wind.min.css',
        'icon-sets/icons/solid-icons/premium-solid-icons.css',
        **/
        'fa/css/font-awesome.min.css',
        'icon-sets/icons/line-icons/premium-line-icons.min.css',
    ];
}
