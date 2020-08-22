<?php
namespace components;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use sunnnnn\nifty\auth\components\helpers\RouteHelper;

class Auth {

    const MODE_DISPLAY = 1;
    const MODE_DISABLED = 2;

    /**
     * @use: 检查路由是否有权限
     * @date: 2018/5/3 13:07
     * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
     * @return bool : bool
     * @param string $route
     */
    public static function check($route = ''){
        return $route === null ? true : RouteHelper::checkRoute($route, Yii::$app->getUser());
    }

    public static function tag($name, $content = '', $options = [], $auth = []){
        if(self::check(isset($auth['router']) ? $auth['router'] : null)){
            if ($name === null || $name === false) {
                return $content;
            }
            $html = "<$name" . Html::renderTagAttributes($options) . '>';
            return isset(Html::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
        }else{
            if(isset($auth['mode']) && $auth['mode'] === self::MODE_DISABLED){
                if ($name === null || $name === false) {
                    return $content;
                }
                $_options = [
                    'href' => null,
                    'disabled' => 'disabled',
                ];
                $options = ArrayHelper::merge($options, $_options);


                $html = "<$name" . Html::renderTagAttributes($options) . '>';
                return isset(Html::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
            }

            return '';
        }

    }

    public static function a($text, $url = null, $options = []){
        $route = isset($options['auth']['route']) ? $options['auth']['route'] : (
            $url === null ? null : (is_array($url) ? $url[0] : null)
        );

        $mode = isset($options['auth']['mode']) ? $options['auth']['mode'] : self::MODE_DISPLAY;

        unset($options['auth']);

        $options['href'] = $url === null ? 'javascript:;' : Url::to($url);

        return self::tag('a', $text, $options, [
            'route'  => $route,
            'mode'   => $mode
        ]);
    }

    public static function button($text, $options = []){
        $route = isset($options['auth']['route']) ? $options['auth']['route'] : null;

        $mode = isset($options['auth']['mode']) ? $options['auth']['mode'] : self::MODE_DISPLAY;

        unset($options['auth']);

        return self::tag('button', $text, $options, [
            'route'  => $route,
            'mode'   => $mode
        ]);
    }

}