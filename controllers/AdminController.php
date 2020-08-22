<?php

namespace app\controllers;

use app\common\Common;
use Yii;
use yii\web\NotFoundHttpException;
use components\Controller;
use app\models\Admin;
use sunnnnn\nifty\auth\models\AuthRoles;

class AdminController extends Controller
{

    public function actionIndex()
    {
        $searchModel = new Admin();
        $searchModel->load(Yii::$app->request->queryParams);
        $condition = $andFilter = [];
        if (!empty($searchModel->role)) {
            $condition['role'] = $searchModel->role;
        }
        if (!empty($searchModel->keywords)) {
            $andFilter[] = ['like', 'username', $searchModel->keywords];
        }

        $dataProvider = $searchModel::filterSearch($condition, $andFilter);

        $optionsRole = AuthRoles::find()->all();
        if (!empty($optionsRole)) {
            foreach ($optionsRole as $key => $val) {
                $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'optionsRole' => $optionsRole
        ]);
    }

    public function actionAdd()
    {
        $admin = Yii::$app->user->identity->username;
        $temp = $_POST;
        $temp['password'] = '***';
        Yii::info(json_encode($temp, 256), 'admin_add_' . $admin);
        $model = new Admin();
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if (strlen($model->password) < 8) {
                return $this->response(false, '密码长度不小于8位');
            }
            $r2 = '/[a-z]/';
            $r3 = '/[0-9]/';
            if (preg_match_all($r2, $model->password, $o) < 1) {
                return $this->response(false, '密码必须包含至少一个小写字母，请返回修改！');
            }
            if (preg_match_all($r3, $model->password, $o) < 1) {
                return $this->response(false, '密码必须包含至少一个数字，请返回修改！');
            }


            $model->setPassword($model->password);
            $model->auth_key = Admin::generateAuthKey();
            $model->add_time = time();
            $model->edit_time = 0;
            $model->login_time = 0;
            if ($model->save()) {
                $msg = '！！！请注意！！！后台账号【' . $admin . '】添加了新账号【' . $model->username . '】' . '--' . date('Y-m-d H:i:s');
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
        $optionsRole = AuthRoles::find()->all();
        foreach ($optionsRole as $key => $val) {
            $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
        }
        return $this->render('form', [
            'model' => $model,
            'optionsRole' => $optionsRole
        ]);
    }

    public function actionEdit()
    {
        $admin = Yii::$app->user->identity->username;
        $temp = $_POST;
        $temp['password'] = '***';
        Yii::info(json_encode($temp, 256), 'admin_edit_' . $admin);
        $model = Admin::findOne(['id' => $this->getGetValue('id')]);
        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Admin');

            if (!empty($data['password'])) {
                if (strlen($data['password']) < 8) {
                    return $this->response(false, '密码长度不小于8位');
                }
                $r2 = '/[a-z]/';
                $r3 = '/[0-9]/';
                if (preg_match_all($r2, $data['password'], $o) < 1) {
                    return $this->response(false, '密码必须包含至少一个小写字母，请返回修改！');
                }
                if (preg_match_all($r3, $data['password'], $o) < 1) {
                    return $this->response(false, '密码必须包含至少一个数字，请返回修改！');
                }
            }

            $data['edit_time'] = time();
            $data['password'] = empty($data['password']) ? $model->password : Yii::$app->security->generatePasswordHash($data['password']);
            $model->setAttributes($data);
            if ($model->save()) {
                $msg = '！！！请注意！！！后台账号【' . $admin . '】修改了后台账号【' . $model->username . '】的信息' . '--' . date('Y-m-d H:i:s');
                Common::telegramSendMsg($msg);
                Yii::info(json_encode($_POST, 256), 'Admin_Edit_OK_' . Yii::$app->user->identity->username);
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

        $optionsRole = AuthRoles::find()->all();
        foreach ($optionsRole as $key => $val) {
            $optionsRole[$key]['name'] = Yii::t('app/menu', $val['name']);
        }
        return $this->render('form', [
            'model' => $model,
            'optionsRole' => $optionsRole
        ]);
    }

    public function actionDelete()
    {
        $admin = Yii::$app->user->identity->username;
        Yii::info(json_encode($_POST, 256), 'admin_edit_' . $admin);
        $model = Admin::findOne(['id' => $this->getPostValue('id')]);
        if (empty($model)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        if ($model->delete()) {
            $msg = '！！！请注意！！！后台账号【' . $admin . '】删除了后台账号【' . $model->username . '】' . '--' . date('Y-m-d H:i:s');
            Common::telegramSendMsg($msg);
            Yii::info(json_encode($_POST, 256), 'Admin_Delete_OK_' . Yii::$app->user->identity->username);
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

    public function actionBindAdmin()
    {
        die;
        Yii::info(json_encode($_POST, 256), 'Admin_BindAdmin_Params');
        $ret = ['result' => 0, 'msg' => '', 'data' => ''];
        $username = \Yii::$app->request->post('username');
        //区分不同角色
        $username = $username . '[admin]';
        $client_id = \Yii::$app->request->post('client_id');

        if (!Gateway::isOnline($client_id)) {
            $ret['msg'] = 'Client_id存在';
            return $ret;
        }

        $admin = Admin::find()->where(['username' => $username])->one();
        if (!$admin) {
            $ret['msg'] = '用户不存在';
            Gateway::unbindUid($client_id, $username);
            return $ret;
        }

        //client_id绑定用户名
        Gateway::bindUid($client_id, $username . '[admin]');
        //用户 群组   1平台  2商户  3收款员
        Gateway::joinGroup($client_id, 1);
        //不同身份 群组
        Gateway::joinGroup($client_id, 'admin' . $admin->role);
        $ret['msg'] = '用户绑定成功';
        $ret['result'] = 1;
        Yii::info(json_encode($_POST, 256), 'Admin_BindAdmin_OK');
        return $ret;
    }

}
