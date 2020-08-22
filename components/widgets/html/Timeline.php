<?php
namespace components\widgets\html;

use yii\helpers\Html;
use yii\base\Widget;

/**
 * @use: Timeline
 * @date: 2018/3/29 16:55
 * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class Timeline extends Widget{
    /**
     * top title
     * @var null
     */
    public $title = null;
    /**
     * [
     *   [
     *      'stat' => [
     *           'image' => '/images/xxxxx.xxxxx.png',
     *           'icon' => 'pli-user',
     *           'class' => 'bg-success',
     *       ],
     *      'content' => [
     *            'html' => '',
     *            'mainTitle' => '',
     *            'subTitle' => '',
     *            'keyword' => '',
     *            'text' => 'asd',
     *       ]
     *    ]
     *
     * ]
     * @var array
     */
    public $items = [];
    /**
     * Two column model 时间轴居中，说明在两边
     * @var bool
     */
    public $center = true;

    public function init(){
        parent::init();

        if(empty($this->title)){
            $this->title = date('Y-m-d H:i');
        }
    }

    public function run(){
        parent::run();

        $this->renderWidget();
    }

    public function renderWidget(){

        $header = Html::tag('div',
            Html::tag('div', $this->title, ['class' => 'timeline-header-title bg-success']),
            ['class' => 'timeline-header']
        );

        $clearfix = Html::tag('div', '', ['class' => 'clearfix']);

        $items = $this->renderItems($this->items);

        $input = Html::tag('div', $header.$items.$clearfix, ['class' => $this->center === true ? 'timeline two-column' : 'timeline']);

        echo $input;
    }

    private function renderItems($items){
        $result = '';

        foreach($items as $item){
            //icon
            if(!empty($item['stat']['image'])){
                $icon = '<img src="'.$item['stat']['image'].'">';
            }elseif(!empty($item['stat']['icon'])){
                $icon = Html::tag('i', '', ['class' => 'icon-2x '.$item['stat']['icon']]);
            }else{
                $icon = '';
            }
            $icon = Html::tag('div', $icon, ['class' => 'timeline-icon '. (empty($item['stat']['class']) ? '' : $item['stat']['class'])]);
            //time
            $time = Html::tag('div', empty($item['stat']['time']) ? '' : $item['stat']['time'], ['class' => 'timeline-time']);
            //stat
            $stat = Html::tag('div', $icon.$time, ['class' => 'timeline-stat']);

            //content
            if(empty($item['content']['html'])){
                $text = empty($item['content']['text']) ? '' : $item['content']['text'];
                $keyword = empty($item['content']['keyword']) ? '' : Html::tag('label', $item['content']['keyword'], ['class' => 'text-info pad-rgt']);
                $subTitle = empty($item['content']['subTitle']) ? '' : Html::tag('p', $item['content']['subTitle'], ['class' => 'text-main text-semibold']);
                $mainTitle = empty($item['content']['mainTitle']) ? '' : Html::tag('h4', $item['content']['mainTitle'], ['class' => 'mar-no pad-btm text-warning']);

                $html = $mainTitle.$subTitle.$keyword.$text;
            }else{
                $html = $item['content']['html'];
            }

            $content = Html::tag('div', $html, ['class' => 'timeline-label']);

            $result .= Html::tag('div', $stat.$content, ['class' => 'timeline-entry']);

        }

        return $result;
    }

}