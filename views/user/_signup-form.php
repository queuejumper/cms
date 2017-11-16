<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::tag('p', Html::encode($this->title), ['class' => 'form-title']) ?>

      <div class="row">
        <div class="col-sm-6">
           <?= $form->field($model, 'first_name')->textInput(['maxlength' => true , 'placeHolder' => 'First name'])->label(false) ?>
        </div>
        <div class="col-sm-6">
           <?= $form->field($model, 'last_name')->textInput(['maxlength' => true , 'placeHolder' => 'Last name'])->label(false) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true , 'placeHolder' => 'Username'])->label(false) ?>      
        </div>
        <div class="col-sm-6">
          <?= $form->field($model, 'email')->textInput(['rows' => 6 , 'placeHolder' => 'Email'])->label(false)?>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
      <?= $form->field($model, '_password')->passwordInput(['rows' => 6 , 'placeHolder' => 'Password' ,'value'=>''])->label(false) ?>
        </div>
        <div class="col-sm-6">
      <?= $form->field($model, 'password_repeat')->passwordInput(['rows' => 6 , 'placeHolder' => 'Confirm password', 'value'=>''])->label(false) ?>
        </div>
      </div>

      <div class="row">
          <div class="col-sm-6">
            <?= $form->field($model, 'country')->dropDownList(
              $countriesList,
              ['id'=>'country_list',
              'prompt'=>'Select your country']
              )->label(false)?>
          </div>
          <div class="col-sm-6 gender">
              <?= $form->field($model, 'gender')->radioList([
                      'm' => 'Male', 
                      'f' => 'Female'
                  ]);?>
          </div>
      </div>
  
        <?= Html::submitButton($model->isNewRecord ? 'Sign up' : 'Update', ['class' => $model->isNewRecord ? 'user-form-btn' : 'btn btn-primary']) ?>
        <?= Html::tag('div', Html::a('Already a user?', ['login']), ['class' => 'go-login']) ?>
    <?php ActiveForm::end(); ?>

