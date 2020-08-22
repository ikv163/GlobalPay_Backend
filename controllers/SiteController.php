<?php

namespace app\controllers;

use app\common\Common;
use app\common\qrReader\QrReader;
use Yii;
use yii\helpers\Url;
use components\Controller;
use app\models\LoginForm;
use yii\web\Response;

/**
 *
 * @use: 后台首页以及登录相关
 * @date: 2017-4-19 下午1:17:25
 * @author: sunnnnn [www.sunnnnn.com] [mrsunnnnn@qq.com]
 */
class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = '/login';
        $model = new LoginForm();
        return $this->render('login', ['model' => $model]);
    }

    public function actionUploadQr()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (in_array($_FILES['file']['type'], ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png']) && $_FILES['file']['size'] < 1000000) {
            if ($_FILES['file']['error'] > 0) {
                return json_encode(['code' => false, 'msg' => '上传文件存在错误，请联系相关人员']);
            } else {
                $newName = time() . $_FILES['file']['name'];
                $final = 'images/qr_img/';
                move_uploaded_file($_FILES['file']['tmp_name'],
                    $final . $newName);
                $qrReader = new QrReader($final . $newName);
                $finalUrl = $qrReader->text();
                if ($finalUrl) {
                    return json_encode(['code' => true, 'data' => $finalUrl]);
                } else {
                    return json_encode(['code' => false, 'msg' => '此图片无法解析，请裁剪后再试或手动解析']);
                }
            }
        } else {
            return json_encode(['code' => false, 'msg' => '请确保图片格式为【gif,jpeg,png,pjpeg】,图片少于10M']);
        }
    }

    public function actionAjaxLogin()
    {
        Yii::info(json_encode($_POST,256),'Site_ajaxLogin');
        $model = new LoginForm();
        $ga = new \PHPGangsta_GoogleAuthenticator();
        if (!$ga->verifyCode(Yii::$app->redis->get($_POST['LoginForm']['username'].'Google'), $_POST['LoginForm']['google'], 2)) {
            return $this->response(false, '谷歌验证码错误');
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $msg = '！！！请注意！！！后台账号【' . $model->username . '】登陆上线了~~--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);

            $this->response(Url::to(['/site/index']));
        } else {
            $error = $model->getErrors();
            if (!empty($error)) {
                foreach ($error as $err) {
                    $err = is_array($err) ? array_pop($err) : $err;
                    $this->response(false, $err);
                    break;
                }
            }
        }
    }

    public function actionLogout()
    {
        $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】退出系统了~~--' . date('Y-m-d H:i:s');
        Common::telegramSendMsg($msg);
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionLanguage()
    {
        $language = $this->getGetValue('lang', 'en', 'trim');
        $this->switchLanguage($language);
        $this->redirect(Yii::$app->request->referrer);
    }
}
