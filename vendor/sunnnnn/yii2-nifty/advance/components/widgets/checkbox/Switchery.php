<?php
namespace components\widgets\checkbox;

use yii\helpers\Html; 
use yii\helpers\Json;
use yii\widgets\InputWidget; 

class Switchery extends InputWidget{
    /**
     * 纯净版，为true时，所有参数都需要自定义
     * @var boolean
     */
    public $_pure = false;

    public $_checked = null;

    public $_class = 'switchery';

    public $_color = '#64bd63';

    public $_secondaryColor = '#dfdfdf';

    public $_jackColor = '#fff';

    public $_jackSecondaryColor = null;

    public $_disabled = false;

    public $_disabledOpacity = 0.5;

    public $_speed = '0.1s';

    public $_size = 'default';
    /**
     * 原生配置
     * @var array
     */
    public $_options = [];
    /**
     * 当前配置和原生配置合并
     * @var boolean
     */
    public $_optionsMerge = true;
    
    public function run(){
        parent::run();
        $this->renderWidget();
    }
    
    public function renderWidget(){
        if($this->hasModel()){
            $input = Html::activeCheckbox($this->model, $this->attribute, $this->options);
        }else{
            $input = Html::checkbox($this->name, $this->_checked, $this->options);
        }
        
        $this->renderAsset();
        echo $input;
    }
    
    public function renderAsset(){
        $view = $this->getView();

        SwitcheryAsset::register($view);
        
        if($this->_pure === true){
            $options = [];
        }else{
            $options = [
                'color' => $this->_color,
                'secondaryColor' => $this->_secondaryColor,
                'jackColor' => $this->_jackColor,
                'jackSecondaryColor' => $this->_jackSecondaryColor,
                'className' => $this->_class,
                'disabled' => $this->_disabled,
                'disabledOpacity' => $this->_disabledOpacity,
                'speed' => $this->_speed,
                'size' => $this->_size,
            ];
        }
        
        if(!empty($this->_options)){
            $options = $this->_optionsMerge === true ? array_merge($options, $this->_options) : $this->_options;
        }
        
        $jsonOptions = Json::encode($options);
        
        $js = <<<JS
            $(function(){
                new Switchery(document.getElementById('{$this->options['id']}'), {$jsonOptions});
        	});
JS;
        $view->registerJs($js, $view::POS_END);
    }
    
}