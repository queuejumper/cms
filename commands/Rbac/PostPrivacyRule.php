<?php

namespace app\commands\Rbac;

use yii\rbac\Rule;
use app\modules\post\models\Post;

/**
 * Checks if authorID matches user passed via params
 */
class PostPrivacyRule extends Rule
{
    public $name = 'isPublic';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if(isset($params['post']))
        {
            $post = User::findOne($params['post']);print_r($post->public == Post::STATUS_PUBLIC);exit;
            return ($post && $post->public == Post::STATUS_PUBLIC && $post->approved == Post::STATUS_ACTIVE) ?  true : false;
        }
        return false;
    }
}