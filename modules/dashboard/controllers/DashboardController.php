<?php

namespace app\modules\dashboard\controllers;
use  yii\filters\AccessControl;
use app\modules\post\models\Post;
use app\common\models\User;
use yii\helpers\ArrayHelper;
use app\components\Helper;
use yii\web\UploadedFile;
use Yii;
class DashboardController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['profile', 'my-posts','post-action','profile-action'], 
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'my-posts','post-action','profile-action'],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action){return $this->redirect('/error');}
            ],
        ];
    }

    public function actionOverview()
    {

    }


    public function actionProfile()
    {
    	$user_id = Yii::$app->user->id;
    	if($user_id){
    		$model = User::findOne($user_id);
    		$model->scenario = User::SCENARIO_UPDATE_PROFILE;
	        $allCountries = Helper::readJsonFile('assets/json/countries.json');
			$countriesList=ArrayHelper::map($allCountries,'code','name');
            if($model->pic)
            {
                User::$profile_pic = "@web/img/users/{$model->pic}";
            }else
            {
                User::$profile_pic = '@web/img/users/default/';
                User::$profile_pic .=($model->gender == 'm') ? 'male.png' : 'female.png';
            }
            $model->pic_input = UploadedFile::getInstance($model, 'pic_input');
			if($model->load(Yii::$app->request->post()) && $model->validate())
            {
				if($model->newPassword && !empty($model->newPassword))
                {
				    $model->password = $model->hashPassword($model->newPassword);
                }
                if($model->pic_input)
                {
                    $model->pic_input->saveAs(
                        'img/users/' . $model->username.'-pic'. '.' . $model->pic_input->extension);
                    $model->pic = $model->username.'-pic'. '.' . $model->pic_input->extension;
                }
				if($model->save())
                {
					Yii::$app->getSession()->setFlash('success', 'Changes have been saved!');
					return $this->redirect('profile');
				}else{
					Yii::$app->getSession()->setFlash('danger', 'No changes to save!');
					return $this->render('profile',['model' => $model, 'countriesList' => $countriesList]);
				}
			}else{

				return $this->render('profile',['model' => $model, 'countriesList' => $countriesList]);
			}
    		
    	}else
        {
            return $this->redirect('/login');
        }

    }
    public function actionMyPosts()
    {
    	if(!$currentUser = Yii::$app->user->id) $this->redirect('/login');
		$post = new Post();
		$all_posts = $post->findAllPosts(['author_id' => $currentUser]);
		return $this->render('my-posts', ['all_posts' => $all_posts]);
    }

    public function actionMyPostAction()
    {
        $request = Yii::$app->request;
            if($request->isAjax){
                if($request->isPost){
                    $action = $request->post('action');
                    $post_id = $request->post('id');
                    if(\Yii::$app->user->can('manageOwnPost',['post' => $post_id]) 
                        ||\Yii::$app->user->can('managePosts'))
                    {
                        if(!$post = Post::find()->where(['id' => $post_id])->one())
                            {
                                $result = 
                                [
                                    "status" => "error",
                                    "data" => null,
                                    "message" => "Bad request, Post not found!"
                                ];
                                return json_encode($result);
                            }
                            switch ($action) {
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
                                "message" => "Bad request, something went wrong!"
                            ];                            
                        }
                        return json_encode($result); 
                    }
                }
            }     	
    }

    public function actionProfileAction()
    {
        $request = Yii::$app->request;
            if($request->isAjax){
                if($request->isPost){
                    $action = $request->post('action');
                    $currentUser = Yii::$app->user->id;
                    // if(\Yii::$app->user->can('manageOwnProfile',['user' => $currentUser]) 
                    //     ||\Yii::$app->user->can('manageUsers'))
                    // {
                        if(!$user = User::findOne($currentUser))
                        {
                            $result = 
                            [
                                "status" => "error",
                                "data" => null,
                                "message" => "Bad request, You must log in first!"
                            ];
                            return json_encode($result);                                
                        }
                        switch ($action) {
                            case 'rm-pic':
                                 $user->pic = null;
                                 $response = $user->save();
                                break;
                            default:
                                $response = false;
                                break;
                        }
                //}
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
                            "message" => "Bad request, something went wrong!"
                        ];                            
                    }
                    return json_encode($result);                             
                }
            }          
    }


}
