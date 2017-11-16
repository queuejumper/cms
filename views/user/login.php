 <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Sign in';
?>
    	<?php $form = ActiveForm::begin(); ?>
<div class="user-sign-in">
	    <div class="login-form primary-box">
	    	<?= Html::tag('p', Html::encode($this->title), ['class' => 'form-title']) ?>
	    	<?= $form->field($model, 'username')->textInput(['placeHolder' => 'username'])->label(false) ?>
	    	<?= $form->field($model, '_password')->passwordInput(['placeHolder' => 'password', 'value'=>''])->label(false) ?>
		    <?= Html::submitButton( 'Sign in', ['class' => 'user-form-btn']) ?>
		    <?= Html::tag('div', Html::a('forget password?', ['/forget-password']), ['class' => 'forget-pass']) ?><hr>
		    <?= Html::tag('div', Html::encode("Don't have an account?"), ['class' => 'donot-have-acc']) ?>
		    <?= Html::tag('div' , Html::a('Create Account', ['/signup']) , ['class' => 'go-signup']) ?>
		</div>
</div>
    	<?php ActiveForm::end(); ?> 