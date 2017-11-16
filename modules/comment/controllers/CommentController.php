<?php

namespace app\modules\comment\controllers;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\common\models\User;
use app\models\Notification;
use app\controllers\NotificationController;
use app\modules\comment\models\Comment;
use app\modules\post\models\Post;
use yii\base\InvalidParamException;
use app\components\Helper;
use Yii;
/**
 * Default controller for the `comment` module
 */
class CommentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['CommentAction'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['CommentAction'],
                        'roles' => ['@'],
                    ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/login');}
                ],
            ],
        ];
    }

    public function actionCommentAction()
    {
		$request = Yii::$app->request;
		if($request->isAjax)
		{
			if($request->isPost)
			{
				$currentUserID =  Yii::$app->user->id;
				$action = $request->post('action');
				$model = new Comment();
				switch ($action) {
					case 'new-comment':
					case 'new-reply':
						if(\Yii::$app->user->can('addComment',['user' => $currentUserID])
						 || \Yii::$app->user->can('manageComments'))
						{
							$type = $request->post('type');
							$comment =$request->post('comment');
							$post_id = (int)$request->post('post_id');
							if(empty($comment))
							{
	                            $result = 
	                            [
	                                "status" => "error",
	                                "data" => null,
	                                "message" => "Bad request, Data not found!"
	                            ];
	                            return json_encode($result);									
							}
								$model->parent_id = ($type == 'reply')
							 	? $request->post('parent_id') : null;
								$model->post_id = $post_id;
								$model->user_id = $currentUserID;
								$model->comment = $comment;
								$model->created_at = date('Y-m-d H:i:s');
								if(!$model->validate()) return false;
			 					if($model->save())
			 					{
			 						//SENDING DATA TO AJAX
									$comment = Comment::findCommentByID($model->id);
									unset($comment[0]['user_id']);
									//SENDING NOTIFICATION TO USER
									NotificationController::commentNotification($post_id,$type,$currentUserID,$model->parent_id);

	                                $result = 
	                                [
	                                    "status" => "ok",
	                                    "data" => $comment[0],
	                                    "message" => null
	                                ];
								}else
								{

	                                $result = 
	                                [
	                                    "status" => "error",
	                                    "data" => null,
	                                    "message" => "Bad request, something went wrong!"
	                                ];									
	                            }
	                                return json_encode($result);
							break;
						}
						else
						{
	                        $result = 
	                        [
	                            "status" => "error",
	                            "data" => null,
	                            "message" => "Bad request, Your not allowed to perform this action!"
	                        ];
	                        return json_encode($result);
						}
					case 'loadReplies':
						$comment_id = (int)$request->post('comment_id');
						$replies = $model->findRepliesByComment($comment_id);

						//CHECK IF COMMENT AUTHOR IS THE CURRENT USER
						foreach ($replies as $key => $value)
						{
							$replies[$key]['date'] = Helper::dateFrom($value['date']);	
							if($value['user_id'] == $currentUserID)
							{
								$replies[$key]['rm'] = true;
							}else
							{
								$replies[$key]['rm'] = false;
							}
							unset($replies[$key]['user_id']);
						}
						if($replies)
						{
	                        $result = 
	                        [
	                            "status" => "ok",
	                            "data" => $replies,
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
					break;
					case 'delete-comment':
						$comment_id = (int)$request->post('comment_id');
						$type = (int)$request->post('type');
						if(\Yii::$app->user->can('manageOwnComment',['comment' 
							=> $comment_id]) || \Yii::$app->user->can('manageComments')){
							if(!$comment = $model->findOne($comment_id)) return false;
							if($comment->delete())
							{	if($type == 'reply')
								{
									$model->deleteAll(['parent_id' => $comment_id]);
								}
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
	                                "message" => "Bad request, something went wrong!"
	                            ];									
							}
							return json_encode($result);
							break;
						}else
						{
	                        $result = 
	                        [
	                            "status" => "error",
	                            "data" => null,
	                            "message" => 
	                            "Bad request, Your not allowed to perform this action!"
	                        ];
	                        return json_encode($result);
						}

					default:
					 	$result = 
	                    [
	                        "status" => "error",
	                        "data" => null,
	                        "message" => "Bad request, something went wrong!"
	                    ];
	                    return json_encode($result);
					break;
				}
			}
		} 
    }


}
