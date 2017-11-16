<?php

namespace app\controllers;
use app\models\Report;
use app\modules\post\models\Post;
use yii\filters\AccessControl;
use Yii;
class ReportController extends \yii\web\Controller
{
	public function behaviors()
	{
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['report-action'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['report-action'],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/login');}
            ],
        ];
    }

    public function actionReportAction()
    {
   		$request = Yii::$app->request;
		if($request->isAjax)
		{
			if($request->isPost)
			{
				$post_id = $request->post('post_id');
				$action = $request->post('action');
				$current_user = Yii::$app->user->id;
				if(!$post = Post::findOne($post_id))
				{
                    $result = 
                    [
                        "status" => "error",
                        "data" => null,
                        "message" => "Bad request, Data not found!"
                    ];
                    return json_encode($result);					
				}

				$model = new Report();
				switch ($action) {
					case 'report':
						$model->user_id = $current_user;
						$model->post_id = $post_id;
						$model->created_at = date('Y-m-d H:i:s');
						if($model->save() && $post->updateCounters(['reported' => 1]))
							$response = true;
						else $response = false;
						break;

					case 'unreport':
						if(!$report = Report::findOne(['post_id' => $post_id, 'user_id' => $current_user]))
						{
		                    $result = 
		                    [
		                        "status" => "error",
		                        "data" => null,
		                        "message" => "Bad request, Data not found!"
		                    ];
		                    return json_encode($result);							
						}	
						 if($report->delete() && $post->updateCounters(['reported' => -1]))
						 	$response = true;
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
                        "message" => "Bad request, Something went wrong!"
                    ];						
				}
				return json_encode($result);
			}
		}
    }

}
