<?php
        [
            'label' => '<span class="glyphicon glyphicon-bell"></span>',
            'items' => [
                 ['label' => 'Dashboard', 'url' => '/dashboard'],
                 ['label' => 'Settings', 'url' => '/user-settings'],
                 ['label' => 'Logut', 'url' => '/logout']
            ],
            'visible' => !Yii::$app->user->isGuest
        ],

?>