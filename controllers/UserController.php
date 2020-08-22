<?php

namespace app\controllers;

use app\common\Common;
use app\models\LogRecord;
use Yii;
use components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\models\Admin;
use app\models\AdminInfo;
use app\models\PasswordForm;

/**
 * UserController implements the CRUD actions for AdminInfo model.
 */
class UserController extends Controller
{

    public $role = 1;

    public function actionIndex()
    {
        $searchModel = new Admin();
        $searchModel->load(Yii::$app->request->queryParams);
        $condition = $andFilter = [];
        if (empty($searchModel->status)) {
            $condition['status'] = Admin::STATUS_N;
        } else {
            $condition['status'] = $searchModel->status;
        }

        if (!empty($searchModel->keywords)) {
            $andFilter[] = ['like', 'username', $searchModel->keywords];
        }

        $dataProvider = $searchModel::filterSearch($condition, $andFilter);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAdd()
    {
        $model = new Admin();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->setPassword($model->password);
            $model->auth_key = Admin::generateAuthKey();
            $model->role = $this->role;
            $model->add_time = time();
            $model->edit_time = 0;
            $model->login_time = 0;
            if ($model->save()) {
                LogRecord::addLog(['添加管理员' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】添加了后台账号【' . $model->username . '】--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                return $this->response('@');
            } else {
                $errors = $model->getErrors();
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $error = is_array($error) ? array_pop($error) : $error;
                        return $this->response(false, $error);
                        break;
                    }
                }
            }
        }
        $model->status = 0;
        return $this->render('form', [
            'model' => $model
        ]);
    }

    public function actionEdit()
    {
        $model = Admin::findOne(['id' => $this->getGetValue('id')]);
        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Admin');
            $data['edit_time'] = time();
            $data['password'] = empty($data['password']) ? $model->password : Yii::$app->security->generatePasswordHash($data['password']);
            $model->setAttributes($data);
            if ($model->save()) {
                LogRecord::addLog(['修改管理员' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了后台账号【' . $model->username . '】--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                $this->response('@');
            } else {
                $errors = $model->getErrors();
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $error = is_array($error) ? array_pop($error) : $error;
                        $this->response(false, $error);
                        break;
                    }
                }
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    public function actionProfile()
    {
        $model = Admin::filterOne(['id' => Yii::$app->user->identity->id]);
        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        return $this->render('profile', ['model' => $model]);
    }

    public function actionEditPhoto()
    {
        $model = Admin::findOne(['id' => Yii::$app->user->identity->id]);
        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model->photo = $this->getPostValue('src', '', 'trim');
        $model->edit_time = time();
        if ($model->save()) {
            LogRecord::addLog(['修改管理员头像' => $model->toArray()], Yii::$app->controller->route, 0, 3);
            $this->response(true);
        } else {
            $errors = $model->getErrors();
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $error = is_array($error) ? array_pop($error) : $error;
                    $this->response(false, $error);
                    break;
                }
            }
        }
    }

    public function actionEditPassword()
    {
        $model = new PasswordForm();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->updatePassword()) {
                LogRecord::addLog(['修改管理员密码' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                $msg = '！！！请注意！！！后台账号【' . Yii::$app->user->identity->username . '】修改了自己的登陆密码--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                $this->response(true, Yii::t('app', 'success'));
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
        return $this->render('password', ['model' => $model]);
    }

    public function actionEditProfile()
    {
        $model = AdminInfo::findOne(['admin_id' => Yii::$app->user->identity->id]);
        if (empty($model)) {
            $model = new AdminInfo();
            $model->admin_id = Yii::$app->user->identity->id;
            $model->gender = AdminInfo::GENDER_M;
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if (empty($model->id)) {
                $model->add_time = time();
                $model->edit_time = 0;
            } else {
                $model->edit_time = time();
            }

            if ($model->save()) {
                LogRecord::addLog(['修改管理员资料' => $model->toArray()], Yii::$app->controller->route, 0, 3);
                $this->response(Url::to(['/user/profile']));
            } else {
                $errors = $model->getErrors();
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $error = is_array($error) ? array_pop($error) : $error;
                        $this->response(false, $error);
                        break;
                    }
                }
            }
        }
        return $this->renderAjax('_form', ['model' => $model]);
    }

}
