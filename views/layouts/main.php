<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="nav-bar">
        <?php  
            $this->beginContent('@app/views/layouts/navbar.php'); 
            $this->endContent();
        ?>
    </div>

    <div class="main-content-wrapper">
        <div class="main-content col-md-12 ">
            <?php
            if(!Yii::$app->session->hasFlash('thank-you')):
            foreach (Yii::$app->session->getAllFlashes() as $type => $message):
                echo "<div class='alert alert-{$type} alert-dismissible show alert-message' role='{$type}'>"
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                    <?=$message?>
                </div>
            <?php endforeach;endif;?>
                    <?= Html::img('@web/img/site/loading.gif',['class' => 'loading-img']);?>
           <?= $content; ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


