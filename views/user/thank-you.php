<?php
use yii\helpers\Html;
$data = Yii::$app->session->getFlash('thank-you');
$username = $data['username'];
$email = $data['email'];
?>

<div class="thank-you">
<?= Html::tag('h1', \Yii::t('app', 'Congratulation {username}!' , ['username' => $username])) ?>
<?= Html::tag('h3', \Yii::t('app', 'A verification e-mail has been sent to your e-mail address {email}!' , ['email' => $email])) ?>
<?= Html::tag('p', \Yii::t('app', 'Click the link in the e-mail to activate your account, which will enable you to publish your posts!')) ?>
 <?= Html::tag('div' , Html::a(\Yii::t('app', 'Resend verification e-mail'), ['/signup']) , ['class' => 'resend-verify-btn']) ?>
</div>