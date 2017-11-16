<?php

namespace app\controllers;

use Yii;
use app\common\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\Helper;
use yii\filters\AccessControl;
class UserController extends Controller
{



    public function behaviors()
    {
        return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['signup','login','thank-you','profile'],
                            'allow' => true,
                            'roles' => ['?'],
                            'denyCallback' => function()
                            {
                                return $this->redirect('/');
                            }
                        ],
                        [
                            'actions' => ['logout'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
        ];
    }

    public function actionSignup()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_REGISTER;
        $allCountries = Helper::readJsonFile('assets/json/countries.json');
        $countriesList=ArrayHelper::map($allCountries,'code','name');
        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->created_at = date('Y-m-d H:i:s');
            $model->password = $model->hashPassword($model->_password);
            $model->generatetoken($model->email,$model->id);
            if($model->save(false))
            {
                $auth = \Yii::$app->authManager;
                $authorRole = $auth->getRole('author');
                $auth->assign($authorRole, $model->getId());
                    //$model->sendVerification($model->username,$model->email,$model->token);
                    Yii::$app->getSession()->setFlash('thank-you', ['username' => $model->username , 'email' => $model->email]);
                    return $this->redirect('thank-you');
            }else{
                 //throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
      
            }                
        } else {
                return $this->render('signup', [
                    'model' => $model,
                    'countriesList' => $countriesList,
                ]);
        }

        return null;
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) 
        {
            return $this->redirect('index');
        }
        $model = new User();
        $model->scenario = User::SCENARIO_LOGIN;
       if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            return $this->goBack();
        }else
        {
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect('/');
    }

    public function actionThankYou()
    {
        if(Yii::$app->session->hasFlash('thank-you')){
            $user = Yii::$app->session->getFlash('thank-you');
            return $this->render('thank-you' , ['user' => $user]);
        }else{
            return $this->redirect('/');
            //return $this->render('thank-you');
            // throw new NotFoundHttpException('The requested page does not exist.');
        }
        
    }


    public function actionActivateAccount()
    {
        $model = new user();
        $username = Yii::$app->request->get('username');
        $username = Helper::decrypt($username);
        $email = Yii::$app->request->get('email');
        $email = Helper::decrypt($email);
        $token = Yii::$app->request->get('token');
        $result = $model->findBy([
                'username' => $username,
                'email' => $email,
                'token' => $token,
        ]);
        if($result){
            if($result->isActive === 0){
                $result->isActive = 1;
                $result->save();
            }else{
                //already active
            }
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }



}
