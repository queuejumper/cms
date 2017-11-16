<?php
namespace app\modules\post\controllers;
use Yii;
use  app\modules\post\models\Post;
use  app\modules\comment\models\Comment;
use app\common\models\User;
use app\models\Like;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
class PostController extends \yii\web\Controller
{
	private static 	$limit = 3,
					$offset = 0,
					$related_posts_IDs = array();

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createPost'],
                        'roleParams' => function()
                        {
    						return ['user' => Yii::$app->user->id];
						},
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['publicPost'],
                        'roleParams' => function()
                        {
    						return ['post' => Yii::$app->request->get('id')];
						},
                    ],
                ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/error');}
            ],
        ];
    }
	public function actionIndex()
	{	
		$categories = [];
		$post = new Post();
		$result = $post->find_all_active_public_posts();
		if(!empty($result)){
			ArrayHelper::multisort($result,['viewed'], [SORT_DESC]);
			$popular = array_splice($result, 0,6);
			ArrayHelper::multisort($result,['created_at'], [SORT_ASC]);
			$new = array_splice($result, 0,2);
			ArrayHelper::multisort($result,['category'], [SORT_DESC]);
			foreach ($result as $key => $value) 
			{
				if($key == 0)
				$categories[$result[0]['category']] = $result[0];
				if($key > 0)
				{
					if($value['category'] == $result[($key-1)])
					{
						$categories[$result[($key-1)]] = $result[$key];
					}else
					{
						$categories[$value['category']] = $result[$key];	
					}
				}
			}
			$all_posts = ['popular' => $popular, 'categories' => $categories, 'new' => $new];
		}
		else {$all_posts = null;}
		return $this->render('index',['all_posts' => $all_posts]);
	}

	public function actionView()
	{
		$id = Yii::$app->getRequest()->getQueryParam('id');
		$request = Yii::$app->request;
		if(isset($id) && $id != "")
		{	
				$model = new Post();
				$post = $model->findPost($id,Yii::$app->user->id);
			if(\Yii::$app->user->can('manageOwnPost', ['post' => $id]) 
				|| \Yii::$app->user->can('managePosts')
				|| ($post['public'] == Post::STATUS_PUBLIC || $post['approved'] == Post::STATUS_ACTIVE))
			{

				$like = new Like();
				$likes = $like->getLikesByPost($id);
				if($current_user = Yii::$app->user->id)
					{
						 $userLike = ($like->checkUserLike($current_user ,$id)) ? true : false;
					}else
					{
						$userLike = false;
					}
				$comment = new Comment();
				$comments = $comment->findCommentsByPost($id);

				if($post)
				{
					//UPDATE VIEWS
					$_post = Post::findOne($id);
					$_post->updateCounters(['viewed' => 1]);

					$related_posts = $this->getRelatedPosts($post['author_id'],
					 $post['title'], $post['id'],$post['category_id'],$post['tags']);
					return $this->render('view', ['post' => $post ,
						'related_posts' => $related_posts, 'comments' => $comments, 'likes' => $likes, 'userLike' =>$userLike]);			
				}else{
					throw new \yii\web\HttpException(404, 'The requested Item could not be found.');	
				}
			}else
			{
					throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
			}
		}else
		{
			return $this->goBack();
		}
	}

	public function actionMoreRelated()
	{
		$request = Yii::$app->request;
			if($request->isAjax)
			{
				if($request->isGet)
				{
					$post_id = $request->get('post_id');
					self::$offset = (int)$request->get('offset'); 
					self::$related_posts_IDs = json_decode($request->get('rel_posts_IDs'));
					$post = Post::findOne($post_id);
					$related_posts = $this->getRelatedPosts($post['author_id']
						, $post['title'],$post['id'],$post['category_id'],$post['tags']);

					if(count($related_posts) > 0)
					{

                        $result = 
                        [
                            "status" => "ok",
                            "data" => $related_posts,
                            "message" => null
                        ];   
					}else
					{
                        $result = 
                        [
                            "status" => "error",
                            "data" => null,
                            "message" => "No more data!"
                        ]; 
					}
					return json_encode($result);		
				}
			}
	}

	private function getRelatedPosts($author_id,$title, $post_id, $category_id, $tags)
	{			
				
				$related_posts = $this->find_by_title_desc_cat_auth($author_id, $title,$category_id,$post_id);
				$related_posts_by_tags = $this->findRelatedByTags($tags,$post_id);
				$all_posts = ArrayHelper::merge($related_posts,$related_posts_by_tags);
				$result = array_map("unserialize", array_unique(array_map("serialize", $all_posts)));
				return $result = (is_null($result) || empty($result)) ? null : $result;
	}

	private function find_by_title_desc_cat_auth($author_id,$title,$category_id,$post_id)
	{
				$model = new Post();
				$result = $model->relatedPosts($author_id,
				$title,$category_id,$post_id,self::$offset,self::$limit , self::$related_posts_IDs);
				return $result = (is_null($result)) ? [] : $result;
	}

	private function findRelatedByTags($tags,$post_id)
	{
				$model = new Post();
				$result = $model->relatedPostsByTags($tags,$post_id,self::$offset,self::$limit, self::$related_posts_IDs);
				return $result = (is_null($result)) ? [] : $result;
	}

    public function actionCreate()
    {
    	$post = new Post();
		$post->scenario = Post::SCENARIO_CREATE;
    	if($post->load(Yii::$app->request->post()))
		{
    		$post->post_id = md5($post->id.time());
    		$post->category_id = $post->category;
    		$post->created_at =  date('Y-m-d H:i:s');
    		$post->author_id = Yii::$app->user->id;
    		if($post->validate())
    		{
	    		if($post->save())
	    		{
					Yii::$app->getSession()->setFlash('success', 'Post created successfuly!');
					return $this->redirect('create');
	    		}
    		}else
    		{
    			print_r($post->errors);
    		}
    	}else
    		{
	    		$post->public = 1;
	    		return $this->render('create', ['model' =>$post]);
	    	}
        
    }

}