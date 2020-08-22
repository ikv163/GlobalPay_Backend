<?php
namespace components\widgets\html;

use yii\helpers\Html;
use yii\base\Widget;

/**
 * @use: Navbar 上的 dropdown 插件
 * @date: 2018/4/4 14:31
 * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class NavbarDropdown extends Widget{
    /**
     * 主标签
     * @var string
     */
    public $tag = 'li';
    /**
     * 主标签属性
     * @var array
     */
    public $options = ['class' => 'dropdown'];
    /**
     * 显示图标
     * @var sring
     */
    public $icon = 'pli-arrow-down-in-circle'; //通知pii-bell  用户pli-male
    /**
     * 通知数量，false表示无通知，true只显示圆点，不显示具体数字
     * @var bool
     */
    public $notify = false;
    /**
     * dropdown 宽度
     * @var string
     */
    public $size = 'sm'; //xs,sm,md,lg
    /**
     * dropdown设置为滚动框
     * @var bool
     */
    public $scroll = false;
    /**
     * [
     *   [
     *      'media' => true, //media模式
     *      'image' => '',
     *      'icon'  => '',  image和icon两者选一，image优先
     *      'url'   => '',
     *      'label' => '',
     *      'time'  => '', //media模式才存在
     *    ]
     *    ···
     * ]
     * @var array
     */
    public $items = [];
    /**
     * 跳转到通知页面
     * [
     *      'text' => '',
     *      'url'  => '',
     * ]
     * @var array
     */
    public $link = [];

    public function init(){
        parent::init();

    }

    public function run(){
        parent::run();

        $this->renderWidget();
    }

    public function renderWidget(){

        if($this->notify === true){
            $notify = Html::tag('span', '', ['class' => 'badge badge-header badge-danger']);
        }elseif(preg_match('/^[1-9]d*$/', $this->notify)){
            $notify = Html::tag('span', $this->notify, ['class' => 'badge badge-header badge-danger']);
        }else{
            $notify = '';
        }
        $navbar = Html::a('<i class="'.$this->icon.'"></i>'.$notify, 'javascript:;', ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']);


        if($this->scroll === true){
            $itms = Html::tag('div', Html::tag('div', Html::tag('ul', $this->renderItems($this->items), ['class' => 'head-list']), ['class' => 'nano-content']), ['class' => 'nano scrollable']);
        }else{
            $itms = Html::tag('ul', $this->renderItems($this->items), ['class' => 'head-list']);
        }

        if(!empty($this->link) && is_array($this->link)){
            $link = Html::tag('div', Html::a(
            '<i class="pci-chevron chevron-right pull-right"></i>'.(empty($this->link['text']) ? 'Show All' : $this->link['text']),
            empty($this->link['url']) ? 'javascript:;' : $this->link['url'],
                ['class' => 'btn-link text-main box-block']
            ), ['class' => 'pad-all bord-top']);
        }else{
            $link = '';
        }

        $dropdown = Html::tag('div', $itms.$link, ['class' => 'dropdown-menu dropdown-menu-right dropdown-menu-'.$this->size]);


        $input = Html::tag($this->tag, $navbar.$dropdown, $this->options);

        echo $input;
    }

    private function renderItems($items){
        $result = '';

        foreach($items as $item){
            //media模式
            if(isset($item['media']) && $item['media'] === true){
                //icon
                if(!empty($item['image'])){
                    $icon = '<img src="'.$item['image'].'" class="img-circle img-sm">';
                }elseif(!empty($item['icon'])){
                    $icon = Html::tag('i', '', ['class' => 'icon-2x '.$item['icon']]);
                }else{
                    $icon = '';
                }
                $left = Html::tag('div', $icon, ['class' => 'media-left']);

                $label = Html::tag('p', empty($item['label']) ? '' : $item['label'], ['class' => 'mar-no text-nowrap text-main text-semibold']);

                $time  = Html::tag('small', empty($item['time']) ? '' : $item['time']);

                $body = Html::tag('div', $label.$time, ['class' => 'media-body']);

                $content = Html::a($left.$body, empty($item['url']) ? 'javascript:;' : $item['url'], ['class' => 'media']);
            }else{
                if(!empty($item['image'])){
                    $icon = '<img src="'.$item['image'].'" class="img-circle img-xs mar-rgt">';
                }elseif(!empty($item['icon'])){
                    $icon = Html::tag('i', '', ['class' => 'icon-lg icon-fw '.$item['icon']]);
                }else{
                    $icon = '';
                }

                $label = empty($item['label']) ? ' ' : ' '.$item['label'];

                $content = Html::a($icon.$label, empty($item['url']) ? 'javascript:;' : $item['url']);
            }

            $result .= Html::tag('li', $content);
        }

        return $result;
    }

}