<?php

namespace app\controllers;
use app\models\Notification;
use app\modules\post\models\Post;
use app\modules\comment\models\Comment;
use app\common\models\User;
use app\components\Helper;
use yii\helpers\ArrayHelper;
use Yii;

class NotificationController extends \yii\web\Controller
{
	public static 	$limit = 5,
					$offset = 0;

    public function actionGetNotification()
    {
		$request = Yii::$app->request;
		if($request->isAjax)
		{
			if($request->isGet)
			{
		        if($current_user = Yii::$app->user->id)
		    	{	
		    		$actionType = $request->get('action');
		    		switch ($actionType) {
		    			case 'get-notifications':
		    			case 'more-notifications':
	    				self::$limit = ($limit = $request->get('limit')) ? $limit : self::$limit;
	    				self::$offset = ($offset = $request->get('offset')) ? $offset : self::$offset;
				    		$notifications = Notification::findNotifications()

				    		foreach ($notifications as $key => $value) 
				    		{
								$notifications[$key]->created_at = Helper::dateFrom($value->created_at);
				    		}

				    		$notifications = ArrayHelper::toArray($notifications,[Notification::className()
				    						 => ['id','message','created_at','ref','seen']]);
				    		if($notifications && count($notifications) > 0)
				    		{
			                    $result = 
			                    [
			                        "status" => "ok",
			                        "data" => $notifications,
			                        "message" => null
			                    ];
				    		}else
				    		{
			                    $result = 
			                    [
			                        "status" => "error",
			                        "data" => null,
			                        "message" => "Bad request, No more data!"
			                    ];				    			
				    		}
				    		return json_encode($result);
		    				break;
		    			
		    			case 'check-notifications': 
				    		$notifications = Notification::find()
				    									->where(['and',
															['user_id' => $current_user],
															['seen' => false]
				    										])
				    									->all();
							if($notifications && count($notifications) > 0)
							{
			                    $result = 
			                    [
			                        "status" => "ok",
			                        "data" => ["notifications" => count($notifications)],
			                        "message" => null
			                    ];									
							}else
							{
			                    $result = 
			                    [
			                        "status" => "error",
			                        "data" => null,
			                        "message" => "Bad request, No more data!"
			                    ];										
							}
							return json_encode($result);			    				
		    				break;
		    			default:
			                    $result = 
			                    [
			                        "status" => "error",
			                        "data" => null,
			                        "message" => "Bad request, Something went wrong!"
			                    ];
		                    return json_encode($result);	
		    				break;
		    		}

		    	}
			}
		}
    }


    public function actionChangeNotification()
    {
    	$request = Yii::$app->request;
		if($request->isAjax)
		{
			if($request->isPost)
			{
				$notificationID = $request->post('id');
				$actionType = $request->post('actionType');
				switch ($actionType) {
					case 'one':
					$notification = Notification::findOne($notificationID);
					$notification->seen = 1;
					$response = $notification->update();
						break;
					case 'bulk':
						$notifications = Notification::find()->where(['and',
										['user_id' => Yii::$app->user->id],
										['seen' => 0]
										])
										->all();
							foreach ($notifications as $key => $notification) 
							{
								$notification->seen = 1;
								if(!$notification->update(false))
								 {
								 	$response = false; break;
								 }
							}
						$response = true;
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

    public static function commentNotification($post_id,$commentType,$user_id,$comment_parent = null)
    {
    		$notify = new Notification();
    		$post = Post::find()->select(['title','author_id'])->where(['id' => $post_id])->asArray()->one();
    		$commenter = User::find()->select(['username'])->where(['id' => $user_id])->asArray()->one();
    		if($post['author_id'] == $user_id) return false;
    		$notify->created_at = date('Y-m-d H:i:s');
    		$notify->ref = "/post/{$post_id}";
    		$notify->type = Notification::POST_TYPE;
    		$notify->type_id = $post_id;
    		if($commentType == 'comment')
    		{
    			$notify->user_id = $post['author_id'];
    			$notify->message = Notification::COMMENT_MSG; 

    			
    		}elseif($commentType == 'reply')
    		{
				$parentModel = new Comment();
				$parent_author = $parentModel->findCommentByID($comment_parent);
				$notify->user_id = $parent_author['user_id'];
    			$notify->message = Notification::REPLY_MSG; 			
    		}
    		$notify->save();
    		   	
    }
}
