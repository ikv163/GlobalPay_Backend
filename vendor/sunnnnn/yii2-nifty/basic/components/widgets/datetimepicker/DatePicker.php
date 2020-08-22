<?php
namespace components\widgets\datetimepicker;

use Yii;
use yii\helpers\Html; 
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * @use: 日期控件
 *
 * <?= \components\widgets\datetimepicker\DatePicker::widget([
 *      'name' => 'datepicker',
 * //   'model' => $model,
 * //   'attribute' => 'datepicker',
 *      'options' => ['class' => 'form-control'],
 *      'language' => 'zh-CN',
 *      ...
 *  ]); ?>
 *
 *  在ActiveForm中
 *  <?= $form->field($model, 'datepicker')->widget(\components\widgets\datetimepicker\DatePicker::classname(), [
 *      'options' => ['class' => 'form-control'],
 *      ...
 *  ]); ?>
 *
 * @date: 2018/3/29 10:19
 * @author: sunnnnn [http://www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class DatePicker extends InputWidget{
    /**
     * 纯净版，为true时，所有参数都需要自定义
     * @var boolean
     */
    public $_pure = false;
    /**
     * 选择一个区间
     * [
     *  'start' => 'attribute1',  start 和 end 必须至少有一个
     *  'end' => 'attribute2',
     *  'startOptions' => ['class' => 'form-control'],
     *  'endOptions' => ['class' => 'form-control'],
     * ]
     * @var bool
     */
    public $_range = false;
    /**
     * language default english
     * @var string
     */
    public $_language = 'en';
    /**
     * 显示格式
     * @var string
     */
    public $_format = 'yyyy-mm-dd';
    /**
     * 选择完自动关闭
     * @var bool
     */
    public $_close = true;
    /**
     * 清除按钮
     * @var bool
     */
    public $_clear = false;
    /**
     * 今日按钮
     * @var bool
     */
    public $_today = false; //linked
    /**
     * 高亮今天
     * @var bool
     */
    public $_todayHL = true;
    /**
     * @var int
     */
    public $_zIndex = 5001;
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

        if(in_array($this->_language, ['zh', 'zh-cn', 'zh-CN'])){
            $this->_language = 'zh-CN';
        }
    }
    
    public function run(){
        parent::run();

        if(isset($this->_range['start']) || isset($this->_range['end'])){
            $this->renderWidget(true);
        }else{
            $this->renderWidget();
        }
    }
    
    public function renderWidget($range = false){

        if($this->hasModel()){

            if($range === true){
                $input = '<div class="input-daterange input-group">';
                $input .= Html::activeTextInput($this->model, isset($this->_range['start']) ? $this->_range['start'] : $this->attribute, isset($this->_range['startOptions']) ? $this->_range['startOptions'] : []);
                $input .= '<span class="input-group-addon">~</span>';
                $input .= Html::activeTextInput($this->model, isset($this->_range['end']) ? $this->_range['end'] : $this->attribute, isset($this->_range['endOptions']) ? $this->_range['endOptions'] : []);
                $input .= '</div>';
            }else{
                $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
            }
        }else{
            if($range === true){
                $input = '<div class="input-daterange input-group">';
                $input .= Html::textInput(isset($this->_range['start']) ? $this->_range['start'] : $this->name, null, isset($this->_range['startOptions']) ? $this->_range['startOptions'] : []);
                $input .= '<span class="input-group-addon">~</span>';
                $input .= Html::textInput(isset($this->_range['end']) ? $this->_range['end'] : $this->name, null, isset($this->_range['endOptions']) ? $this->_range['endOptions'] : []);
                $input .= '</div>';
            }else{
                $input = Html::textInput($this->name, null, $this->options);
            }
        }


        $this->renderAsset($range);
        echo $input;
    }
    
    public function renderAsset($range){
        $view = $this->getView();
        
        DatePickerAsset::register($view);
        
        if($this->_pure === true){
            $options = [];
        }else{
            $options = [
                'format' => $this->_format,
                'language' => $this->_language,
                'autoclose' => $this->_close,
                'clearBtn' => $this->_clear,
                'todayBtn' => $this->_today,
                'todayHighlight' => $this->_todayHL,
                'zIndexOffset' => $this->_zIndex
            ];
        }
        
        if(!empty($this->_options)){
            $options = $this->_optionsMerge === true ? array_merge($options, $this->_options) : $this->_options;
        }
        
        $jsonOptions = Json::encode($options);

        $selector = $range === true ? '.input-daterange' : '#'.$this->options['id'];
        
        $js = <<<JS
            $(function(){
                $('{$selector}').datepicker({$jsonOptions});
        	});
JS;
        if($this->_language != 'en'){
            $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@components/assets/plugins/bootstrap-datepicker');
            $view->registerJsFile(Yii::$app->request->baseUrl.$directoryAsset.'/locales/bootstrap-datepicker.'.$this->_language.'.min.js', ["depends"=>["app\assets\AdminAsset"]]);
        }
        $view->registerJs($js, $view::POS_END);
    }
    
}