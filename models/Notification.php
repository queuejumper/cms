<?php

namespace app\models;

use Yii;
use app\common\models\User;
/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $message
 * @property string $created_at
 *
 * @property User $user
 */
class Notification extends \yii\db\ActiveRecord
{
    
    const POST_TYPE = 1,
          USER_TYPE = 2,
          COMMENT_MSG = 3,
          REPLY_MSG = 4; 

    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function ()
    {
        
    }

}
find()
                                                        ->where(['user_id' => $current_user])
                                                        ->offset(self::$offset)
                                                        ->limit(self::$limit)
                                                        ->orderBy(['seen' => 0,'created_at' => SORT_DESC])
                                                        ->all();