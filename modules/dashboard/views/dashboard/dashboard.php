<?php
use yii\bootstrap\Tabs;
use yii\widgets\Pjax;
use yii\helpers\Html;
?>

<div class="primary-box">
	<?php Pjax::begin() ?>
<span id="overview-tab"><?= Html::a("overview", ['/overview'], ['class' => 'btn btn-lg btn-primary']) ?></span>
<span id="my-posts-tab"><?= Html::a("My posts", ['/my-posts'], ['class' => 'btn btn-lg btn-primary']) ?></span>
	<?php Pjax::end() ?>
</div>