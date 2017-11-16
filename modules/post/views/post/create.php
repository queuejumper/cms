 <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\category\models\Category;
use  yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\post\models\Post */
/* @var $form ActiveForm */
$this->title = "Create new post";
?>

	<div class="primary-box content-box">
		<?= Html::tag('p', Html::encode($this->title), ['class' => 'form-title']) ?>
		 <?php $form = ActiveForm::begin();
					$categories = ArrayHelper::map (Category::find()->all(), 'id' , 'category'); ?>
			<div class="row">
					<div class="col-sm-6">
						     <?= $form->field($model, 'title') ?>
				            <?= $form->field($model, 'description')->textArea() ?>
		                	<?= $form->field($model, 'category')->dropDownList($categories , ['id'=>'country_list','prompt'=>'Select category'])->label(false)?>
		                	<?= Html::label('Please hit Space or "," to Separate your tags', 'tags',['id' => 'input-tags-label']) ?>
		                	<?= $form->field($model, 'tags')->textInput( ['data-role' => 'tagsinput', 'class' => 'input-tags', 'placeHolder'=> 'insert tag (optional)'])->label(false) ?>
				    </div>
		            <div class="col-sm-6 ">
		    			<?= $form->field($model, 'public')->radioList([
							'1' => 'Public', 
							'0' => 'Private'
						],['class' => 'post-privacy'])->label(false);?>
		 		   	</div>
			</div>
			<div class="form-group">
				<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
			</div>	
		<?php ActiveForm::end(); ?>
	</div>
