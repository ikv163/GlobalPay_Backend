<?php
namespace app\models;

use Yii;
use yii\base\Model;

class PasswordForm extends Model
{
    public $oldPassword;
    public $newPassword;
    public $confirmPassword;

    private $_user = false;

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'confirmPassword'], 'required'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'operator' => '===', 'message' => Yii::t('app/model', 'confirm_password_error')]  ,
            ['oldPassword', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params){
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            
            if(empty($user)){
            	$this->addError($attribute, Yii::t('app', 'invalid_username'));
            }else{
            	if(!$user->validatePassword($this->oldPassword)){
            	    $this->addError($attribute, Yii::t('app', 'invalid_password'));
            	}
            }
        }
    }
    
    public function updatePassword(){
    	if ($this->validate()) {
    		$user = $this->getUser();
    		$user->setPassword($this->newPassword);
    		$user->edit_time = time();
    		return $user->save();
    	}
    	return false;
    }


    public function getUser(){
        if ($this->_user === false) {
            $this->_user = Admin::findIdentity(Yii::$app->user->identity->id);
        }

        return $this->_user;
    }
    
    public function attributeLabels(){
    	return [
	    	'oldPassword' => Yii::t('app/model', 'old_password'),
    	    'newPassword' => Yii::t('app/model', 'new_password'),
    	    'confirmPassword' => Yii::t('app/model', 'confirm_password'),
    	];
    }
}
