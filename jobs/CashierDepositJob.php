<?php

namespace app\jobs;

use app\models\Orders\BankTrans;
use app\models\Sys\ServiceAddress;
use Interop\Queue\Exception\TemporaryQueueNotSupportedException;
use yii\base\BaseObject;
use yii\httpclient\Client;

class CashierDepositJob extends BaseObject implements \yii\queue\RetryableJobInterface
{

    public $orderModel;


    public function execute($queue)
    {
        //@todo 提交充值订单到TyPay

        return true;
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr()
    {
        return 2 * 60;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        \Yii::error($error);
        return ($attempt < 3) && ($error instanceof TemporaryException);
    }







}