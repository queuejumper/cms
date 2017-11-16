<?php

namespace app\models;
use app\modules\post\models\Post;
use app\common\models\User;
use Yii;

/**
 * This is the model class for table "like".
 *
 * @property string $created_at
 * @property integer $user_id
 * @property integer $post_id
 *
 * @property Post $post
 * @property User $user
 */
class Like extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'like';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['user_id', 'post_id'], 'required'],
            [['user_id', 'post_id'], 'integer'],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'created_at' => 'Created At',
            'user_id' => 'User ID',
            'post_id' => 'Post ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLikesByPost($post_id)
    {
        return $this->find()
                    ->select(['COUNT(post_id) AS likes'])
                    ->where(['post_id' => $post_id])
                    ->asArray()
                    ->one();
    }


    public function checkUserLike($user_id,$post_id)
    {
        return $this->find()
                    ->select(['user_id'])
                    ->where(['and',
                        ['user_id' => $user_id],
                        ['post_id' => $post_id]
                    ])
                    ->asArray()
                    ->one();
    }

}
