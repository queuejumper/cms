		<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
echo Nav::widget([
	'encodeLabels' => false,
    'items' => [
        [
            'label' => '<span class="glyphicon glyphicon-search "></span>',
            'url' => false,
            'options' => ['class' => 'release-search']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-upload"><span id="navbar-pill-text">Upload</span></span>',
            'url' => ['/create'],
            'linkOptions' => [],
        ],
        [
            'label' => '<span class="glyphicon glyphicon-fire"><span id="navbar-pill-text">Popular</span></span>',
            'url' => ['/index'],
            'linkOptions' => [],
        ],
        [
            'label' => '<span class="glyphicon glyphicon-log-in"><span id="navbar-pill-text">Login</span></span>',
            'url' => ['/login'],
            'linkOptions' => [],
            'visible' => Yii::$app->user->isGuest
        ],
        [
            'label' => '<span class="glyphicon glyphicon-bell"></span><i class="notify-count"></i>',
            'items' => [
                 ['label' => 'Mark all as read', 'url' => false, 'options' => ['class' => 'mark-all']],

            ],
            'visible' => !Yii::$app->user->isGuest,
            'options' => ['class' => 'get-notification']
        ],
        [
            'label' => '<span class="glyphicon glyphicon-user"></span>',
            'items' => [
                 ['label' => 'Dashboard', 'url' => '/dashboard'],
                 ['label' => 'Settings', 'url' => '/user-settings'],
                 ['label' => 'Logut', 'url' => '/logout']
            ],
            'options' => ['class' => 'user-options'],
            'visible' => !Yii::$app->user->isGuest
        ],
    ],
    'options' => ['class' =>'nav navbar-nav'], // set this to nav-tab to get tab-styled navigation
]);

?>