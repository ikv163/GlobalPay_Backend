<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\AppUpload;

$this->title = Yii::t('app/menu', 'Upload_App');
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">

    .upload-app{
        margin-top: 80px;
    }

    .col-sm-3 {
        height: 75px !important;
    }

    .layui-upload-file {
        display: none !important;
    }
    .red{
        color:red;
    }
    .green{
        color:green
    }

    .tips, .app_info{
        margin-bottom: 20px;
    }

</style>

<div class="upload-app">


    <div class="tips">
        <p>重要提示:</p>
        <p>1. App类型必选 ， App文件必须选择</p>
        <p>2. App版本非必填 ， 如果需要强制用户更新下载使用此版本， 则需要填写， 填写的版本号与当前版本号不同即可</p>
        <p>3. 更新信息非必填 ， 如果需要强制用户更新下载使用此版本， 则需要填写。 特别强调：各条更新内容以 '@'分隔， 如：1. 更新了xxxx  @  2.优化了xxxx</p>
    </div>

    <div class="app_info">
        <b>当前Android App版本 ：<?php echo $android_app_info['app_version']; ?>  &nbsp;&nbsp;&nbsp;&nbsp;  IOS App版本：<?php echo $ios_app_info['app_version']; ?>  </b>
    </div>


    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'app_type')->dropDownList(['0'=>'请选择'] + AppUpload::$appTypeRel)->label(\Yii::t('app/menu', 'App_Type')) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'app_file')->fileInput()->label(\Yii::t('app/menu', 'Upload_App')) ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'app_version')->label(\Yii::t('app/menu', 'App_Version')) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'update_msg')->label(\Yii::t('app/menu', 'Update_Msg')) ?>
        </div>
    </div>

    <?php  if($msg){ ?>
    <div class="row notice">
        <div class="col-sm-3 <?php if($msg_type == 1){echo 'green';}else{echo 'red';} ?>">
            <?php echo $msg ?>
        </div>
    </div>
    <?php } ?>

    <button class="submit-btn btn-primary"><?php echo \Yii::t('app/menu', 'Submit'); ?></button>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    var msgType = "<?php echo $msg_type; ?>";
    if(msgType == 1){
        setTimeout(function(){
            window.location.href = '/app/uploadapk';
        }, 2000);
    }

</script>

