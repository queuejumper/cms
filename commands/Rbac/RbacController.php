<?php

namespace app\commands\Rbac;

use yii\console\Controller;
use app\common\models\User;
use Yii;
use yii\base\InvalidParamException;
use app\commands\Rbac\AuthorRule;
/**
* 
*/
class RbacController extends Controller
{
	
	public function actionInit()
	{
        if (!$this->confirm("Are you sure? It will re-create permissions tree")) {
                    return self::EXIT_CODE_NORMAL;
                }
                
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $rule = new CreateRule;
        $auth->add($rule);
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post (upload video, etc)';
        $createPost->ruleName = $rule->name;
        $auth->add($createPost);


        $addComment= $auth->createPermission('addComment');
        $addComment->description = 'Add comments and replies';
        $addComment->ruleName = $rule->name;
        $auth->add($addComment);

        $rule = new PostPrivacyRule;
        $auth->add($rule);
        $publicPost = $auth->createPermission('publicPost');
        $publicPost->description = 'post can be viewable';
        $publicPost->ruleName = $rule->name;
        $auth->add($publicPost);

        $rule = new ManagePostRule;
        $auth->add($rule);
        $manageOwnPost = $auth->createPermission('manageOwnPost');
        $manageOwnPost->description = 'user can manage their own posts';
        $manageOwnPost->ruleName = $rule->name;
        $auth->add($manageOwnPost);

        $rule = new ManageCommentRule;
        $auth->add($rule);
        $manageOwnComment = $auth->createPermission('manageOwnComment');
        $manageOwnComment->description = 'user can manage their own comments';
        $manageOwnComment->ruleName = $rule->name;
        $auth->add($manageOwnComment);

        $rule = new ManageProfileRule;
        $auth->add($rule);
        $manageOwnProfile = $auth->createPermission('manageOwnProfile');
        $manageOwnProfile->description = 'user can manage their own profile';
        $manageOwnProfile->ruleName = $rule->name;
        $auth->add($manageOwnProfile);

        //ADMIN
        $managePosts = $auth->createPermission('managePosts');
        $managePosts->description = 'can manage all posts';
        $auth->add($managePosts);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'can manage all users';
        $auth->add($manageUsers);

        $manageComments = $auth->createPermission('manageComments');
        $manageComments->description = 'can manage all comments';
        $auth->add($manageComments);

        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);
        $auth->addChild($author, $addComment);
        $auth->addChild($author, $manageOwnPost);
        $auth->addChild($author, $manageOwnComment);



        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $author);
        $auth->addChild($admin, $managePosts); 
        $auth->addChild($admin, $manageUsers); 
        $auth->addChild($admin, $manageComments);
	}

    public function actionAddPermission($name,$description="")
    {
        $auth = Yii::$app->authManager;

        $name = $auth->createPermission($name);
        $name->description = $description;
        $auth->add($name);
    }

    public function actionAddChild($role,$permission)
    {
        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            throw new InvalidParamException("There is no role \"$role\".");
        }
        $parent = $auth->getRole($role);
        $child = $auth->getPermission($permission);
        if($auth->hasChild($parent,$child)){
         throw new InvalidParamException("\"$permission\". is already assigned to .\"$role\"");   
        }
        $auth->addChild($parent, $child);
    }
    public function actionAddRole($role)
    {
      $auth = Yii::$app->authManager;  
      $role = $auth->createRole($role);
        $auth->add($role);
        print_r($role);
    }

    public function actionAssign($role, $username)
    {
        $user = User::find()->where(['username' => $username])->one();
        if (!$user) {
            throw new InvalidParamException("There is no user \"$username\".");
        }

        $auth = Yii::$app->authManager;
        $roleObject = $auth->getRole($role);
        if (!$roleObject) {
            throw new InvalidParamException("There is no role \"$role\".");
        }

        $auth->assign($roleObject, $user->id);
    }
}