<?php

namespace app\modules\post\models;
use app\common\models\User;
use  app\modules\category\models\Category;
use Yii;
use yii\helpers\ArrayHelper;

class Post extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;
    const STATUS_PENDING = 0;
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $category;

    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'title' , 'category'], 'required' , 'on' =>[self::SCENARIO_CREATE]],
            [['title'], 'string', 'max' => 64 ,'on' =>[self::SCENARIO_CREATE]],
            [['description'], 'string', 'max' => 512, 'on' =>[self::SCENARIO_CREATE]],
            [['created_at','tags'], 'safe'],
            [['approved', 'reported', 'public', 'viewed', 'author_id'], 'integer'],
            [['post_id'], 'unique'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'category' => Yii::t('app', 'Category'),
            'tags' => Yii::t('app', 'Tags'),
            'approved' => Yii::t('app', 'Is Approved'),
            'isReported' => Yii::t('app', 'Is Reported'),
            'public' => Yii::t('app', 'Public'),
            'viewed' => Yii::t('app', 'Viewed'),
            'author_id' => Yii::t('app', 'Author ID'),
        ];
    }

    public function scenarions()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['title' , 'description', 'category','public'];
        return $scenarios;
    }
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
         }
       if(!$this->isNewRecord){
            $dirtyAttributes = $this->getDirtyAttributes();
            foreach ($dirtyAttributes as $attribute => $value) {
                if($this->getOldAttribute($attribute) === $value){
                    unset($dirtyAttributes[$attribute]);
                }     
            } if(empty($dirtyAttributes))return false;
                
            return true;

        }else{ return true; }
        
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    public static function findAuthor($id)
    {
        return User::findOne($id);
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    public function getComment()
    {
        return $this->hasMany(Post::className(), ['post_id' => 'id']);
    }

        public function findPost($id,$limit = null)
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','p.public','p.approved','u.username AS author'
                    ,'p.author_id','p.category_id','p.tags','p.description','u.pic AS author_pic','p.title'
                    ,'p.viewed', 'p.created_at','c.category'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->leftJoin('category AS c','p.category_id = c.id')
                ->where(['p.id' => $id])
                ->createCommand();        
                return $command->queryOne();        
    }

    //FIND ALL ACTIVE & INACTIVE & APPROVED & PENDING POSTS
    public function findAllPosts($where = null,$limit = null)
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed', 'p.created_at','p.public','p.approved','reported','c.category'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->leftJoin('category AS c','p.category_id = c.id')
                ->where($where)
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();   
    }

    public function find_all_active_public_posts($limit = null)
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed', 'p.created_at','c.category'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->leftJoin('category AS c','p.category_id = c.id')
                ->where(['and',
                    ['public' => self::STATUS_PUBLIC],
                    ['approved' => self::STATUS_ACTIVE],
                ])
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();        
    }

    public function findPendingPosts($limit = null)
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.created_at','c.category'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->leftJoin('category AS c','p.category_id = c.id')
                ->where(['and',
                    ['approved' => self::STATUS_INACTIVE]
                ])
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();         
    }

    public function findPostsBy($by,$andBy,$orderBy,$limit = null)
    {
        return $this->find()->where($by)
                    ->select(['title'])
                    ->andWhere($andBy)
                    ->orderBy([$orderBy => SORT_DESC])
                    ->limit($limit)
                    ->asArray()
                    ->all();
    }

    public static function getPopularPosts($limit = null)
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed', 'p.created_at'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->where(['and',
                    ['>','viewed',10],
                    ['public' => self::STATUS_PUBLIC],
                    ['approved' => self::STATUS_ACTIVE],
                ])
                ->orderBy(['viewed' => SORT_DESC])
                ->limit($limit)
                ->createCommand();        
                return $command->queryAll();
    }



    public function searchPosts($searchKey, $offset = null, $limit = null)
    {
            $searchKey = strtolower($searchKey);
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->where(['or',
                    ['like','LOWER(p.title)', $searchKey],
                    ['like','LOWER(p.description)', $searchKey],
                    ['like','LOWER(p.tags)',$searchKey],
                ])
                ->andWhere(['and',
                    ['p.public' => self::STATUS_PUBLIC],
                    ['p.approved' => self::STATUS_ACTIVE],
                ])
                ->offset($offset)
                ->limit($limit)
                ->createCommand();
               $result = $command->queryAll();
               if(count($result) > 0){
                return [
                            'search' => true,
                            'result' => $result
                        ];
               }else{
                $result = $this->getPopularPosts($limit);
                return [
                            'search' => false,
                            'result' => $result
                        ];                
               }
    }
    public function relatedPosts($author_id, $title,$category_id, $post_id, $offset = null, $limit = null,$related_posts_IDs = array())
    {
            $title = strtolower($title);
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->where(['or',
                    ['like','LOWER(p.title)',$title],
                    ['like','LOWER(p.description)',$title],
                    ['like','LOWER(p.tags)',$title],
                    ['=','p.category_id',$category_id],
                    ['=','p.author_id',$author_id]
                ])
                ->andWhere(['and',
                    ['!=','p.id', $post_id],
                    ['not in', 'p.id',$related_posts_IDs],
                    ['p.public' => self::STATUS_PUBLIC],
                    ['p.approved' => self::STATUS_ACTIVE],
                ])
                ->offset($offset)
                ->limit($limit)
                ->createCommand();
               return $command->queryAll();
              
    }

    public function recommendedPosts($author_id,$post_id, $offset = null, $limit = null, $related_posts_IDs = array())
    {
            $command = (new \yii\db\Query())
                ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed'])
                ->from('post AS p')
                ->leftJoin('user AS u','p.author_id = u.id')
                ->where(['and',
                    ['=','p.author_id',$author_id],
                    ['!=','p.id', $post_id],
                    ['not in', 'p.id',$related_posts_IDs],
                    ['p.public' => self::STATUS_PUBLIC],
                    ['p.approved' => self::STATUS_ACTIVE],
                ])
                ->offset($offset)
                ->limit($limit)
                ->createCommand();
               return $command->queryAll();
    }

    public function relatedPostsByTags($_tags , $post_id, $offset = null, $limit = null , $related_posts_IDs = array())
    {  
        if(empty(trim($_tags))){
            return null;
        }
        $_tags = explode(',', $_tags);
        $found = 0;
        $IDs = null;
        $otherTags = $this->find()->where(['and',
            ['!=','id', $post_id],
            ['not in', 'id',$related_posts_IDs],
            ['public' => self::STATUS_PUBLIC],
            ['approved' => self::STATUS_ACTIVE],
            ])->all();
        foreach ($otherTags as $tag) {
            if(!empty($tag['tags'])){
                $tags = explode(',' , $tag['tags']);
                if(array_intersect($tags, $_tags)){
                    $found ++;
                    $IDs [] = $tag['id'];
                }
            }
        }
        if($found > 0){
            $command = (new \yii\db\Query())
                        ->select(['p.id','u.username AS author','u.pic AS user_pic','p.title','p.viewed'])
                        ->from('post AS p')
                        ->leftJoin('user AS u','p.author_id = u.id')
                        ->where(['p.id' => $IDs])
                        ->offset($offset)
                        ->limit($limit)
                        ->createCommand();

        }
        return null;
    }



}
