<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\models\Order;

/* @var $this yii\web\View */
/* @var $model app\models\QrCode */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
    .col-sm-3 {
        height: 75px !important;
    }

    .layui-upload-file {
        display: none !important;
    }

    .field-qrcode-qr_relation {
        display: none;
    }
</style>
<div class="qr-code-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_address')->textInput(['maxlength' => true]) ?>
            <button style="top: 23px;z-index: 1;position: absolute;right: 62px;"
                    type="button" class="btn btn-success"
                    id="uploadQr">上传
            </button>
            <button style="top: 23px;z-index: 1;position: absolute;right: 10px;" type="button"
                    class="btn btn-success" id="viewQr">查看
            </button>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_nickname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_account')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'per_day_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'per_day_orders')->textInput() ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_type')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'qr_type')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'qr_status')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'is_shopowner')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'is_shopowner')) ?>
        </div>
        <div class="col-sm-3 field-qrcode-qr_relation">
            <?= $form->field($model, 'qr_relation')->dropDownList(['' => '请选择']); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'username')->widget(\kartik\select2\Select2::className(), [
                'data' => $cashiers,
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_code')->textInput(['maxlength' => true, 'placeholder' => '添加时可随意填写，后台将自动生成简码']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'alipay_uid')->textInput(['maxlength' => true, 'placeholder' => '类型为支付宝时，可根据情况填写']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'real_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_card_number')->textInput(['maxlength' => true, 'placeholder' => '银行卡卡号，类型为云闪付或银行卡需填写']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_code')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'bank_code')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_address')->textInput(['maxlength' => true, 'placeholder' => '开户行地址，类型为云闪付或银行卡需填写']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'qr_location')->textInput(['placeholder' => '省会城市填省+市级名称（如：广东广州），否则只需填写市级名称（如：深圳）', 'title' => '省会城市填省+市级名称（如：广东广州），否则只需填写市级名称（如：深圳）']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'priority')->textInput() ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'control')->widget(\kartik\select2\Select2::className(), [
                'data' => ['' => '请选择', '平台' => '平台', '自由' => '自由'] + $cashiers,
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'allow_order_type')->dropDownList(['0'=>'请选择'] + Order::getQRAllowOrderTypes($model->qr_type, 0, true)); ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<div id="qrcode" style="display: none;padding: 1rem">
</div>
<script type="text/javascript" src="/js/layui/layui.js"></script>
<script type="text/javascript">
    $(function () {
        layui.use('upload', function () {
            var upload = layui.upload;

            var uploadInst = upload.render({
                elem: '#uploadQr'
                , url: '/site/upload-qr/'
                , done: function (res) {
                    if (res.code) {
                        layer.msg('解析成功~');
                        $('#qrcode-qr_address').val(res.data);
                        $('#qrcode-qr_address').attr('title', res.data);
                    } else {
                        return layer.alert(res.msg);
                    }
                }
                , error: function () {
                    return layer.alert('上传二维码异常，请联系相关人员');
                }
            });
        });

        if ($('#qrcode-is_shopowner').find('option:selected').val() == 1) {
            $('.field-qrcode-qr_relation').show();
        }

        //选择店长码时显示店员
        $('#qrcode-is_shopowner').change(function () {
            if ($(this).find('option:selected').val() == 1) {
                $('.field-qrcode-qr_relation').show();
            } else {
                $('.field-qrcode-qr_relation').hide();
            }
        });
        //选择二维码类型时显示各自的店员二维码
        $('#qrcode-qr_type').change(function () {
            var qrType = $(this).find('option:selected').val();
            getClerk(qrType);
        });

        var qrRelation = '<?php echo $model->qr_relation;?>';

        function getClerk(qrType) {
            if (!qrType) {
                $('#qrcode-qr_relation').empty();
                return;
            }
            $.ajax({
                url: 'get-clerk',
                type: 'POST',
                data: {'qrType': qrType},
                success: function (res) {
                    $('#qrcode-qr_relation').empty();
                    $('#qrcode-qr_relation').append('<option value="">请选择</option>');
                    if (res) {
                        $.each(res, function (k, v) {
                            if (k == qrRelation) {
                                isSelected = 'selected';
                            } else {
                                isSelected = '';
                            }
                            $('#qrcode-qr_relation').append('<option value="' + k + '" ' + isSelected + '>' + v + '</option>');
                        })
                    }
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        };
        setTimeout(function () {
            getClerk($('#qrcode-qr_type').find('option:selected').val());
        }, 500);
    })
</script>
<script type='text/javascript' src='/js/jquery.js'></script>
<script type="text/javascript" src="/js/jquery.qrcode.js"></script>
<script type="text/javascript">
    var j = jQuery.noConflict();
    j(function () {
        //查看二维码
        j('#viewQr').click(function () {
            var baseurl = j('#qrcode-qr_address').val();
            if (baseurl.length <= 0) {
                return layer.alert('请先上传');
            }
            var urlX = j('#qrcode-qr_address').val();
            j('#qrcode').empty();
            j('#qrcode').qrcode(urlX);
            layer.open({
                type: 1,
                shadeClose: true,
                content: $('#qrcode'),
                end: function () {
                    $('#qrcode').hide();
                    layer.closeAll();
                }
            });
        });
    })

</script>