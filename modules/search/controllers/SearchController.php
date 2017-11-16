<?php

namespace app\modules\search\controllers;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use Yii;
use  app\modules\post\models\Post;
/**
 * Default controller for the `search` module
 */
class SearchController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionSearchHint()
    {
		$request = Yii::$app->request;
		if($request->isAjax){
			if($request->isGet){
				$searchInput =$request->get('searchText');
                if(trim($searchInput) == "")
                {
                    $result = 
                    [
                        "status" => "error",
                        "data" => null,
                        "message" => "No keyword found!"
                    ]; 
                    return json_encode($result);
                }
                $searchInput = strtolower($searchInput);
				$post = new Post();
				$search = $post->findPostsBy(['or',
                    ['like','LOWER(title)',$searchInput],
                    ['like','LOWER(description)',$searchInput],
                    ['like','LOWER(tags)',$searchInput],
                	],['and',
                    ['approved' => Post::STATUS_ACTIVE],
                    ['approved' => Post::STATUS_PUBLIC]
                    ], 'viewed');
                if($search && count($search) > 0)
                {
                    $result = 
                    [
                        "status" => "ok",
                        "data" => $search,
                        "message" => null
                    ];
                                         
                }else
                {
                    $result = 
                    [
                        "status" => "error",
                        "data" => null,
                        "message" => "No data found!"
                    ];                    
                }
				return json_encode($result);	
			}
		}
        
    }

    public function actionSearch()
    {
    	$search_key = Yii::$app->getRequest()->getQueryParam('search-key');
    	if(isset($search_key) && trim($search_key) != ""){
    		$post = new Post();
    		$result = $post->searchPosts($search_key);
    		return $this->render('search',['posts' => $result, 'search_key' => $search_key]);
    	}

    }
}
