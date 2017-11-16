<?php

namespace app\controllers;
use Yii;
use app\models\Like;
use app\modules\post\models\Post;
use yii\filters\AccessControl;
class LikeController extends \yii\web\Controller
{

	public function behaviors()
	{
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['like-action'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['like-action'],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/login');}
            ],
        ];
    }
 public function beforeAction($action) 
    {
      $this->enableCsrfValidation = true;
      return parent::beforeAction($action);
    }
    public function actionLikeAction()
    {
   		$request = Yii::$app->request;
		if($request->isAjax)
		{
			if($request->isPost)
			{
				$post_id = $request->post('post_id');
				$action = $request->post('action');
				$current_user = Yii::$app->user->id;
				//echo $post_id; echo $action; echo $current_user;
				if(!Post::findOne($post_id))
				{
                    $result = 
                    [
                        "status" => "error",
                        "data" => null,
                        "message" => "Bad request, Data not found!"
                    ];
                    return json_encode($result);						
				}

					$model = new Like();
				switch ($action) {
					case 'like':
						$model->user_id = $current_user;
						$model->post_id = $post_id;
						$model->created_at = date('Y-m-d H:i:s');
						$response = $model->save();
						break;

					case 'unlike':
						if($like = Like::findOne(['post_id' => $post_id, 'user_id' => $current_user]))
						$response = $like->delete();
						else $response = false;
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
                        "message" => "Bad request, Data not found!"
                    ];					
				}return json_encode($result);
			}
		}
    }

}
