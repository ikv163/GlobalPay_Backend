<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model{
    
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user = false;

    public function rules(){
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }
    
    public function attributeLabels(){
    	return [
	    	'username' => Yii::t('app', 'username'),
    	    'password' => Yii::t('app', 'password'),
    	    'rememberMe' => Yii::t('app', 'remember_me'),
    	];
    }

    public function validatePassword($attribute, $params){
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if(empty($user)){
                $this->addError($attribute, Yii::t('app', 'invalid_username'));
            }else{
            	if(!$user->validatePassword($this->password)){
            	    $this->addError($attribute, Yii::t('app', 'invalid_password'));
            	}
            }
        }
    }

    public function login(){
        if ($this->validate()) {
        	$User = $this->getUser();
        	$User->auth_key = Admin::generateAuthKey();
        	$User->login_time = time();
        	$User->save();
            return Yii::$app->user->login($User, $this->rememberMe ? 3600*24*7 : 0);
        }
        return false;
    }

    public function getUser(){
        if ($this->_user === false) {
            $this->_user = Admin::findByUsername($this->username);
        }

        return $this->_user;
    }
}
