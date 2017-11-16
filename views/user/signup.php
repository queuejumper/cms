<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Create your account';
?>
<div class="user-create">
    <div class="sign-up-form primary-box">
	    <?= $this->render('_signup-form', [
	        'model' => $model,
	        'countriesList' => $countriesList,
	    ]) ?>
	</div>
</div>
