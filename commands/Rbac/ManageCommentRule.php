<?php

namespace app\commands\Rbac;

use yii\rbac\Rule;
use app\modules\comment\models\Comment;
/**
 * Checks if authorID matches user passed via params
 */
class ManageCommentRule extends Rule
{
    public $name = 'isCommentAuthor';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if(isset($params['comment']))
        {
            $comment = Comment::findOne($params['comment']);
            return ($comment && $comment->user_id == $user) ?  true : false;
        }
        return false;
    }
}