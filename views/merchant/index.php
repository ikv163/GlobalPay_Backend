<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MerchantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Merchants');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="merchant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('app/menu', 'Create Merchant'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'mch_name',
            'mch_code',
            'mch_key',
            [
                'attribute' => 'mch_status',
                'format' => 'raw',
                'value' => function ($model) {
                    $status0 = $status1 = '';
                    if ($model->mch_status == 1) {
                        $status1 = 'selected="selected"';
                        $style = 'style="background:#92c755;color:white"';
                    } else {
                        $status0 = 'selected="selected"';
                        $style = 'style="background:red;color:white"';
                    }
                    return '<select ' . $style . ' class="statusSelect" data-id="' . $model->id . '"><option value="0" ' . $status0 . '>禁用</option><option value="1" ' . $status1 . '>启用</option></select>';
                }
            ],
            'available_money',
            'used_money',
            'balance',
            'telephone',
            'wechat_rate',
            'alipay_rate',
            'union_pay_rate',
            'bank_card_rate',
            'insert_at',
            'update_at',
            'remark',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $(function () {
        //修改状态
        $(document).on('change', '.statusSelect', function () {
            var now = $(this);
            var statusX = now.val();
            var id = now.data('id');
            $.ajax({
                'type': 'POST',
                'url': '/merchant/change-status',
                'data': {'id': id, 'statusX': statusX},
                beforeSend: function () {
                    layer.msg('处理中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 10000,
                    });
                },
                success: function (res) {
                    if (res.result == 1) {
                        if (statusX == 1) {
                            now.css('background', '#92c755');
                        } else {
                            now.css('background', 'red');
                        }
                        now.css('color', 'white');
                    }
                    return layer.msg(res.msg);
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        });
    })
</script>