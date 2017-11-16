<?php

namespace app\modules\comment\models;
use app\modules\post\models\Post;
use app\common\models\User;
use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property string $comment
 * @property string $created_at
 * @property integer $isReply
 * @property integer $reply_to
 * @property integer $post_id
 * @property integer $user_id
 *
 * @property Post $post
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment'], 'required'],
            [['comment'], 'string'],
            [['created_at' , 'parent_id'], 'safe'],
            [[ 'post_id', 'user_id'], 'integer'],
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
            'id' => Yii::t('app', 'ID'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created At'),
            'post_id' => Yii::t('app', 'Post ID'),
            'user_id' => Yii::t('app', 'User ID'),
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

    public function getChild()
    {
        return $this->hasMany(self::className(),['parent_id' => 'id'])
        ->from(self::tableName() . 'parent');
    }

    public function findCommentsByPost($post_id, $limit=null)
    {
            $command = (new \yii\db\Query())
                ->select([
                    'c.id','c.comment','u.username AS user','u.pic AS user_pic','u.id AS user_id',
                    'c.created_at AS date',
                    'COUNT(child.id) AS replies'
                ])
                ->from(['comment AS c'])
                ->leftJoin('user AS u','u.id = c.user_id')
                ->leftJoin('comment AS child','child.parent_id = c.id')
                ->where(['and',
                    ['c.post_id' => $post_id],
                    ['c.parent_id' => null]
                ])
                ->orderBy(['c.created_at' => SORT_DESC])
                ->groupBy('c.id')
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();
    }

    public function findRepliesByComment($comment_id, $limit=null)
    {
            $command = (new \yii\db\Query())
                ->select([
                    'c.id','c.comment','u.username AS user','u.pic AS user_pic',
                    'c.created_at AS date','u.id AS user_id'
                ])
                ->from(['comment AS c'])
                ->leftJoin('user AS u','u.id = c.user_id')
                ->where(['and',
                    ['c.parent_id' => $comment_id],
                    ['!=','c.id',$comment_id]
                ])
                ->orderBy(['c.created_at' => SORT_ASC])
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();
    }
    public function findCommentByID($id)
    {
            $command = (new \yii\db\Query())
                ->select(['c.id','c.comment','c.user_id','u.username AS user','u.pic AS user_pic','parent_id', 'c.created_at AS date'])
                ->from('comment AS c')
                ->leftJoin('user AS u','c.user_id = u.id')
                ->where(['c.id' => $id])
                ->createCommand();        
                return $command->queryAll();        
    }
}
