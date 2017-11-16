
	<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
use app\common\models\User;
$this->title = 'My Profile';
?>
 
<div class="primary-box row profile-form">
    <?= Html::tag('p', Html::encode($this->title), ['class' => 'form-title']) ?>
     <?php $form = ActiveForm::begin([
          'options'=>['enctype'=>'multipart/form-data']]); ?>
    <div class="col-md-5 upload-image">
    	<span>Profile Picture</span>
       	<?php $rm_btn = ($model->pic) ? 'block' : 'none';
   			echo Html::tag('span','',[
   				'class' => 'glyphicon glyphicon-trash remove-img','style' => "display:{$rm_btn}", 'data-g' => $model->gender,
   				'data-action'=>'rm-pic'
   				]);
		?>
		<div class="img-holder">
       		<div class="img-preview">
       			<?= Html::img(User::$profile_pic,['class' => 'prev-profile-pic']);?>
       		</div>
			<div class="file-class">Change
				<?= $form->field($model, 'pic_input')->fileInput(['class' => 'uploadImage'])->label(false); ?>
			</div>
       	</div>
       	
    </div>
	<div class="col-md-7 col col-md-offset-1" >
	      <div class="row">
	        <div class="col-sm-6">
	          <?= $form->field($model, 'first_name')->textInput(['maxlength' => true ])?>
	        </div>
	        <div class="col-sm-6">
	          <?= $form->field($model, 'last_name')->textInput(['maxlength' => true ])?>
	        </div>
	      </div>

	      <div class="row">
	        <div class="col-sm-6">
	          <?= $form->field($model, 'username')->textInput(['maxlength' => true ]) ?>
	        </div>
	        <div class="col-sm-6 gender">
	          <?= $form->field($model, 'gender')->radioList([
	                  'm' => 'Male', 
	                  'f' => 'Female'
	              ]);?>
	        </div>
	      </div>

	      <?= $form->field($model, 'email')->textInput(['rows' => 6])?>
		<div class="row">
			<div class="col-sm-6">
 				<?= $form->field($model, 'newPassword')->passwordInput(['rows' => 6 , 'value'=>''])?>
			</div>
	        <div class="col-sm-6">
	     		 <?= $form->field($model, 'password_repeat')->passwordInput(['rows' => 6, 'value'=>'' ])->label('Confirm new password')?>
	        </div>
      	</div>	

	      <div class="row">
			<div class="col-sm-6">
			<?= $form->field($model, 'country')->dropDownList(
			  $countriesList,
			  ['id'=>'country_list',
			  'prompt'=>'Select your country']
			  )?>
			</div>
	        <div class="col-sm-6">
	      <?= $form->field($model, '_password')->passwordInput(['rows' => 6 , 'value'=>''])->label('Your current password')?>
	        </div>
	      </div>	  
	        <?= Html::submitButton( 'Update', ['class' =>'btn btn-primary']) ?>
	    <?php ActiveForm::end(); ?>
	</div>
</div>