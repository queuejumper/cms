<?php

namespace app\commands\Rbac;

use yii\rbac\Rule;
use app\common\User;
/**
 * Checks if authorID matches user passed via params
 */
class ManageProfileRule extends Rule
{
    public $name = 'isProfileAuthor';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if(isset($params['_user']))
        {
            $_user = User::findOne($params['_user']);
            return ($_user && $_user->user_id == $user) ?  true : false;
        }
        return false;
    }
}