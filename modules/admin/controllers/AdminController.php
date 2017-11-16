<?php

namespace app\modules\admin\controllers;
use yii\filters\AccessControl;
use yii\web\Controller;
use  app\modules\post\models\Post;
use app\common\models\User;
use app\models\Notification;
use app\components\Helper;
use Yii;
/**
 * Default controller for the `admin` module
 */
class AdminController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['user-action', 'post-action','users'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['user-action','users','site-overview'],
                        'roles' => ['manageUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['post-action','posts'],
                        'roles' => ['managePosts'],
                    ],
                ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/error');}
            ],
        ];
    }
    public function actionSiteOverview()
    {
    	$posts = Post::find()->select(['SUM(viewed) AS views', 'COUNT(*) AS posts_num'])->asArray()->one();
    	$users = User::find()->select(['COUNT(*) AS users_num'])->asArray()->one();
        return $this->render('site-overview', ['posts' => $posts, 'users' => $users]);
    }

    public function actionUsers()
    {
    	$user = new User();
    	$users = $user->findAllUsers();
    	return  $this->render('users', ['users' => $users]);
    
    }

    public function actionUserAction()
    {
        $request = Yii::$app->request;
            if($request->isAjax){
                if($request->isPost)
                {

                
                    $action_type = $request->post('type');
                    $user_id = $request->post('id');
                    $user_id = Helper::decrypt($user_id);
                    if(!$user = User::findOne($user_id))
                    {
                        $result = 
                        [
                            "status" => "error",
                            "data" => null,
                            "message" => "Bad request, Data not found!"
                        ]; 
                        return json_encode($result);                        
                    }
                        switch ($action_type) {
                            case 'delete':
                                $response =  $user->delete();
                                break;

                            case 'activate':
                                $user->isActive = User::ACTIVE_USER;
                                $response = $user->update();
                                break;

                            case 'deactivate':
                                $user->isActive = User::INACTIVE_USER;
                                $response = $user->update();
                                break;

                            case 'ban':
                                $user->isBanned = User::BANNED_USER;
                                $response = $user->update();
                                break;

                            case 'unban':
                                $user->isBanned = User::UNBANNED_USER;
                                $response = $user->update();
                                break;

                            default:
                                $response = false;
                                break;
                        }
                    if($response)
                    {
                        $result = 
                        [
                            "status" => "ok",
                            "data" => null,
                            "message" => null
                        ];                         
                    }else
                    {
                        $result = 
                        [
                            "status" => "error",
                            "data" => null,
                            "message" => "Bad request, Something went wrong!"
                        ];                          
                    }
                    return json_encode($result);
                }
            }

    }



    public function actionPosts()
    {
        $post = new Post();
        $posts = $post->findAllPosts();
        return $this->render('posts',['posts' => $posts]);
    }


    public function actionPostAction()
    {
        $request = Yii::$app->request;
            if($request->isAjax){
                if($request->isPost){
                    $action_type = $request->post('type');
                    $post_id = $request->post('id');
                    if(!$post = Post::find()->where(['id' => $post_id])->one())
                    {
                        $result = 
                        [
                            "status" => "error",
                            "data" => null,
                            "message" => "Bad request, Data not found!"
                        ];
                        return json_encode($result);                            
                    }
                    switch ($action_type) {
                        case 'delete':
                            $response = $post->delete();
                            break;

                        case 'public':
                            $post->public = Post::STATUS_PUBLIC;
                            $response = $post->update();
                            break;

                        case 'private':
                            $post->public = Post::STATUS_PRIVATE;
                            $response = $post->update();
                            break;

                        case 'approve':
                            $post->approved = Post::STATUS_ACTIVE;
                            $response = $post->update();
                            break;

                        case 'unapprove':
                            $post->approved = Post::STATUS_PENDING;
                            $response = $post->update();
                            break;

                        default:
                            $response =  false;
                            break;
                    }
                        if($response)
                        {
                            $this->sendPostNotification($action_type,$post['author_id'],$post_id);
                            $result = 
                            [
                                "status" => "ok",
                                "data" => null,
                                "message" => null
                            ];        
                        }else
                        {
                            $result = 
                            [
                                "status" => "error",
                                "data" => null,
                                "message" => "Bad request, Something went wrong!"
                            ];                          
                        }
                        return json_encode($result); 
                }
            }        
    }


    public function sendPostNotification($type,$user_id,$post_id)
    {
        if(!$user_id or $user_id == "")return false;
        $model = new Notification();
        $post = Post::find()->select(['title'])->where(['id' => $post_id])->asArray()->one();
        $model->created_at = date('Y-m-d H:i:s');
        $model->user_id = $user_id;
        $model->ref = "/post/{$post_id}";
        switch ($type) {
            case 'delete':
               $model->message = "Your post "
                            ."<strong>{$post['title']}</strong>"
                            ." has been deleted";
                break;

            case 'approve':
               $model->message = "Your post "
                            ."<strong>{$post['title']}</strong>"
                            ." has been approved";
                break;

            case 'unapprove':
               $model->message = "Your post "
                            ."<strong>{$post['title']}</strong>"
                            ." has been banned";
                break;

            case 'public':
               $model->message = "Your post "
                            ."<strong>{$post['title']}</strong>"
                            ." is now a public post";
                break;

            case 'private':
               $model->message = "Your post "
                            ."<strong>{$post['title']}</strong>"
                            ." is now a private post";
                break;                
            
            default:
               return false;
                break;
        }
        if($model->message) return $model->save();
        else return false;

    }



}
