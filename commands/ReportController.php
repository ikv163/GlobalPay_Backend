<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class ReportController extends Controller
{
    public function actionPlatform()
    {
        try {
            $calcDate = date('Y-m-d', strtotime('-1 day'));
        } catch (\Exception $e) {
            \Yii::error($e->getMessage(), 'Report_');
        }

    }
}
