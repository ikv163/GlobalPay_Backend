<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use app\models\AdminInfo;
use sunnnnn\nifty\auth\models\AuthRoles;

/**
 * This is the model class for table "admin".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string $photo
 * @property string $auth_key
 * @property string $add_time
 * @property string $edit_time
 * @property string $login_time
 * @property string $status
 */
class Admin extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
	public $keywords;
	const STATUS_N = 0;
	const STATUS_D = 1;
	const PHOTO_PATH = 'images/profile-photos';
	
	public static function getStatusArr($status = null){
	    $array = [
	        self::STATUS_N => Yii::t('app/model', 'enabled'),
	        self::STATUS_D => Yii::t('app/model', 'disabled'),
	    ];
	    
	    if($status === null){
	        return $array;
	    }else{
	        return isset($array[$status]) ? $array[$status] : '';
	    }
	}
	
	public static function getProfilePhotos($default = false){
	    $_result = [];
	    $files = Yii::$app->helper->file()->getFiles(Yii::getAlias('@webroot/'.trim(self::PHOTO_PATH, '/').'/'));
	    if(!empty($files)){
	        foreach($files as $file){
	            $_result[] = Yii::getAlias('@web/'.trim(self::PHOTO_PATH, '/').'/'.$file);
	        }
	    }
	    return $default === true ? array_pop($_result) : $_result;
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['role', 'status', 'add_time', 'edit_time', 'login_time'], 'integer'],
            [['add_time', 'login_time'], 'safe'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 128],
            [['photo'], 'string', 'max' => 256],
            [['username'], 'unique'],
            [['keywords'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'id'),
            'username' => Yii::t('app', 'username'),
            'password' => Yii::t('app', 'password'),
            'role' => Yii::t('app/model', 'admin_role'),
            'photo' => Yii::t('app/model', 'admin_photo'),
            'auth_key' => '',
            'add_time' => Yii::t('app/model', 'add_time'),
            'edit_time' => Yii::t('app/model', 'edit_time'),
            'login_time' => Yii::t('app/model', 'login_time'),
            'status' => Yii::t('app/model', 'status'),
        ];
    }
    
	public static function adpSearch($condition = [], $andFilter = [], $order = ['id' => SORT_DESC]){
    	$query = static::find()->with(['roles'])->where($condition);
    	if(!empty($andFilter) && is_array($andFilter)){
    		foreach($andFilter as $val){
    			$query->andFilterWhere($val);
    		}
    	}
    	
    	$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
    			'pagesize' => 20,
			],
		]);
    
    	return $dataProvider;
    }
    
    public static function filterSearch($condition = [], $andFilter = [], $order = ['id' => SORT_DESC]){
        $query = static::find()->with(['roles', 'admin'])->where($condition);
        if(!empty($andFilter) && is_array($andFilter)){
            foreach($andFilter as $val){
                $query->andFilterWhere($val);
            }
        }
        
        return $query->orderBy($order)->all();
    }
    
    public static function filterOne($condition = []){
        return static::find()->with(['roles', 'admin'])->where($condition)->one();
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_N]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    public static function generateAuthKey() {
        return Yii::$app->security->generateRandomString(32);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
    	return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

    /**
     * @param     $username
     * @param int $status
     *
     * @return null|static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_N]);
    }
    
    public static function getIndexList(){
    	return static::find()->indexBy('id')->all();
    }

    /**
     * @param $password
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->getPassword());
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     *
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function getRoles(){
    	return $this->hasOne(AuthRoles::className(), ['id' => 'role']);
    }
    
    public function getAdmin(){
        return $this->hasOne(AdminInfo::className(), ['admin_id' => 'id']);
    }
}
