<?php

namespace app\common\models;

use Yii;
use app\components\Helper;
use yii\web\IdentityInterface;
use app\modules\post\models\Post;

class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const   SCENARIO_LOGIN = 'login',
            SCENARIO_REGISTER = 'signup',
            SCENARIO_UPDATE_PROFILE = 'update_profile',
            ADMINISTRATOR = 1,
            AUTHOR = 2,
            EMAIL_VERIFIED = 1,
            ACTIVE_USER = 1,
            INACTIVE_USER = 0,
            BANNED_USER = 1,
            UNBANNED_USER = 0;
    public $password_repeat,
           $_password,
           $newPassword,
           $pic_input,
           $_user = false;
    public static $profile_pic = null;   

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'username', 'email', '_password','password_repeat','password','gender','country'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['email', '_password','country'], 'string'],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 64,'on' => self::SCENARIO_REGISTER],
            [['first_name', 'last_name', 'username'], 'string', 'min' => 3,'on' => self::SCENARIO_REGISTER],
            [['_password'],'string','min' => 8,'on' => self::SCENARIO_REGISTER],
            ['password_repeat', 'compare', 'compareAttribute' => '_password','on' => self::SCENARIO_REGISTER],
            [['_password'],'match','pattern' => '^\S*(?=\S*[A-Za-z])(?=\S*[\d])\S*$^', 'message' => 'password must contains at least 1 number and 1 letter','on' => [self::SCENARIO_REGISTER]],
            [['username','email'], 'unique','on' => self::SCENARIO_REGISTER],
            ['email','email'],

            [['username','_password'],'required', 'on' => self::SCENARIO_LOGIN],
            ['_password','validatePassword','on' => self::SCENARIO_LOGIN],


            [['first_name', 'last_name', 'username'], 'string', 'min' => 3, 
            'when' =>   function ($model, $attribute) {
                    return $model->{$attribute} !== $model->getOldAttribute($attribute);},'on' => self::SCENARIO_UPDATE_PROFILE],
            // [['pic'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 
            // 'when' => function ($model, $attribute) {
            //         return $model->{$attribute} !== $model->getOldAttribute($attribute);}
            // , 'on' => [self::SCENARIO_UPDATE_PROFILE]],
            [['username','email'], 'unique','on' => self::SCENARIO_UPDATE_PROFILE],
            [['newPassword'],'match','pattern' => '^\S*(?=\S*[A-Za-z])(?=\S*[\d])\S*$^', 'message' => 'password must contains at least 1 number and 1 letter','on' => [self::SCENARIO_UPDATE_PROFILE]],
            [['_password'], 'required', 'when' =>   function ($model, $attribute) {
                return $model->{$attribute} !== $model->getOldAttribute($attribute);},'on' => self::SCENARIO_UPDATE_PROFILE ,
                'message' => 'You must verify your password to save your changes'],
            ['_password','verifyPassword','on' => self::SCENARIO_UPDATE_PROFILE],
            ['password_repeat', 'compare', 'compareAttribute' => 'newPassword','on' => self::SCENARIO_UPDATE_PROFILE],

            
        ];
    }

    public function scenarios()
    {

        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN ]= ['username','_password','password'];
        $scenario[self::SCENARIO_UPDATE_PROFILE] = ['first_name', 'last_name', 'username', 'email', '_password','gender','country'];
        $scenarios[self::SCENARIO_REGISTER] = ['first_name', 'last_name', 'username', 'email', '_password','password_repeat','gender','country'];
        return $scenarios;

    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'username' => 'Username',
            'email' => 'Email',
            '_password' => 'Password',
            'newPassword' => 'New password',
            'password_repeat' => 'Confirm password',
            'gender' => 'Gender',
        ];
    }


    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
       if(!$this->isNewRecord){
            $dirtyAttributes = $this->getDirtyAttributes();
            foreach ($dirtyAttributes as $attribute => $value) {
                if(($attribute != 'password' || $attribute != 'pic') && $this->getOldAttribute($attribute) === $value){
                    unset($dirtyAttributes[$attribute]);
                }     
            }if(empty($dirtyAttributes))return false;
                
            return true;

        }else{ return true; }
        
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->isPasswordValid($this->_password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }elseif(!$user->isVerified){
                $this->addError($attribute, 'you must verify your email first');
            }elseif ($user->isBanned) {
               $this->addError($attribute, 'Sorry!, your account has been banned.');
            }
        }
    }

    public function verifyPassword()
    {
         $user = $this->getUser();
        if(!$user->isPasswordValid($this->_password))
            $this->addError('_password', 'Incorrect password');
    }

    private function isPasswordValid($password)
    {
        if(is_null($password)) return false;
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function hashPassword($password)
    {
         return Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function generatetoken($email,$id)
    {
        $this->token = md5($email.time().$id);

    }

    private function generateActivationLink($username,$email,$token)
    {
        $link = Helper::encrypt($username).'/'.Helper::encrypt($email).'/'.$token;
        return $link;
    }

    public function sendVerification($username,$email,$token)
    {
        //if($username && $email){
            $link = $this->generateActivationLink($username,$email,$token);
            echo 'new.com/user/activate-account/'.$link; exit;
            Yii::$app->mailer->compose()
            ->setFrom('test@new.com')
            ->setTo($email)
            ->setSubject('Account Activation')
            ->setTextBody($username)
            ->setHtmlBody($link)
            ->send();
        //}

    }

    public function findBy($_array)
    {
       return $this->find()->where($_array)->one();
    }

    public function findUser($id)
    {
        return $this->find($id)->one();
    }
    private function getUser()
    {
        if($this->_user === false){
            $this->_user = $this->findBy(['username' => $this->username]);
        }
        return $this->_user;
    }

    public static function findIdentity($id)
     {
        return static::findOne($id);
     }

    public static function findIdentityByAccessToken($token, $type = null)
     {
        throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
     }

    public function getId()
     {
        return $this->id;
     }


    public function getAuthKey()
     {
        return $this->authKey;
     }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }



    public function getPost()
    {
        return $this->hasMany(Post::className(), ['author_id' => 'id']);
    }

    public function getComment()
    {
        return $this->hasMany(Post::className(), ['user_id' => 'id']);
    }

    public function findAllUsers($limit = null)
    {

        $command = (new \yii\db\Query())
            ->select(['u.id','u.first_name','u.last_name', 'u.username', 'u.email', 'u.gender', 'u.created_at', 'u.country', 'u.isActive', 'u.isBanned', 'COUNT(p.author_id) AS posts_num'])
            ->from('user AS u')
            ->leftJoin('post AS p','p.author_id = u.id')
            ->where(['role' => self::AUTHOR])
            ->orderBy(['created_at' => SORT_DESC])
            ->groupBy('p.author_id')
            ->limit($limit)
            ->createCommand();        
            return $command->queryAll();
    }
}
