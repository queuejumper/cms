<?php

namespace app\controllers;
use yii\web\Controller;
use Yii;
/**
* 
*/
class SiteController extends Controller
{
	
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    public function actionError()
	{
	    $exception = Yii::$app->errorHandler->exception;
	    if ($exception !== null) {
	        return $this->render('error', ['exception' => $exception]);
	    }
	}

    public function actionIndex()
    {
        return $this->render('index');
    }

}