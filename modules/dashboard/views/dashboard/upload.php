 <?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\dashboard\models\Video */
/* @var $form ActiveForm */
$this->title = "Create new post";
?>
<div class="dashboard-upload">
	<div class="primary-form content-form">
	<?= Html::tag('p', Html::encode($this->title), ['class' => 'form-title']) ?>
		<div class="row">
		    <div class="col-sm-4">
		        <?php $form = ActiveForm::begin(); ?>
		            <?= $form->field($model, 'title') ?>
		            <?= $form->field($model, 'description')->textArea() ?>
		            <div class="form-group">
		                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
		            </div>
		        <?php ActiveForm::end(); ?>
		    </div>
		</div>
	</div>
</div><!-- dashboard-upload -->
