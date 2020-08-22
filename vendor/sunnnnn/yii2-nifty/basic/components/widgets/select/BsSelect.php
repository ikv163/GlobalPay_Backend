<?php
namespace components\widgets\select;

use yii\helpers\Html; 
use yii\helpers\Json;
use yii\widgets\InputWidget; 

/**
 * http://silviomoreto.github.io/bootstrap-select/
* @use:  Bootstrap Select Widget
* @date: 2018年3月26日 下午2:03:52
* @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class BsSelect extends InputWidget{
    /**
     * 纯净版，为true时，所有参数都需要自定义
     * @var boolean
     */
    public $_pure = false;
    /**
     * options数据
     * @var array
     */
    public $_data = [];
    /**
     * 是否支持多文件上传
     * @var boolean
     */
    public $_multiple = false;
    /**
     * 占位符
     * @var string
     */
    public $_placeholder = null;
    /**
     * 自定义样式类
     * @var string
     */
    public $_class = 'btn-default';
    /**
     * 是否显示搜索框
     * @var string
     */
    public $_search = true;
    /**
     * 搜索框占位符
     * @var string
     */
    public $_searchPlaceholder = '';
    /**
     * 是否显示“全选/清除”按钮
     * @var string
     */
    public $_button = false;
    /**
     * 多选时的显示格式
     * @var string
     */
    public $_searchFormat = 'count > 5';
    /**
     * 下拉框顶部说明条
     * @var string
     */
    public $_header = false;
    /**
     * 最多可以选择几项
     * @var string
     */
    public $_max = false;
    /**
     * 语言
     * @var string
     */
    public $_language = 'en';
    public $_textSelectAll = 'Select All';
    public $_textDeselectAll = 'Deselect All';
    public $_textNoneSelected = 'Nothing selected';
    public $_textCountSelected = '{0} items selected';
    public $_textMaxOptions = ['Limit reached ({n} items max)', 'Group limit reached ({n} items max)'];
    public $_textNoneResults = 'No results matched {0}';
    public $_textDoneButton = 'Close';
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

    public function init(){
        parent::init();

        if(in_array($this->_language, ['zh', 'zh-cn', 'zh-CN', 'zh-tw', 'zh-TW'])){
            $this->_textSelectAll = '全选';
            $this->_textDeselectAll = '清除';
            $this->_textNoneSelected = '未选择';
            $this->_textCountSelected = '已选中{0}个选项';
            $this->_textMaxOptions = ['最多选择{n}个', '每组最多选择{n}个'];
            $this->_textNoneResults = '没有符合{0}的选项';
            $this->_textDoneButton= '关闭';
        }
    }

    public function run(){
        parent::run();
        $this->renderWidget();
    }
    
    public function renderWidget(){
        
        if($this->_multiple === true){
            $this->options['multiple'] = true;
        }
        
        if($this->hasModel()){
            $input = Html::activeDropDownList($this->model, $this->attribute, $this->_data, $this->options);
        }else{
            $input = Html::dropDownList($this->name, null, $this->_data, $this->options);
        }
        
        $this->renderAsset();
        echo $input;
    }
    
    public function renderAsset(){
        $view = $this->getView();
        
        BsSelectAsset::register($view);
        
        if($this->_pure === true){
            $options = [];
        }else{
            $options = [
                'liveSearch' => $this->_search,
                'liveSearchPlaceholder' => $this->_searchPlaceholder,
                'title' => $this->_placeholder,
                'actionsBox' => $this->_button,
                'selectedTextFormat' => $this->_searchFormat,
                'header' => $this->_header,
                'maxOptions' => $this->_max,
                'style' => $this->_class,
                'deselectAllText' => $this->_textDeselectAll,
                'noneSelectedText' => $this->_textNoneSelected,
                'selectAllText' => $this->_textSelectAll,
                'countSelectedText' => $this->_textCountSelected,
                'maxOptionsText' => $this->_textMaxOptions,
                'noneResultsText' => $this->_textNoneResults,
                'doneButtonText' => $this->_textDoneButton,
            ];
        }
        
        if(!empty($this->_options)){
            $options = $this->_optionsMerge === true ? array_merge($options, $this->_options) : $this->_options;
        }
        
        $jsonOptions = Json::encode($options);
        
        $js = <<<JS
            $(function(){
                $('#{$this->options['id']}').selectpicker({$jsonOptions});
        	});
JS;
        $view->registerJs($js, $view::POS_END);
    }
    
}