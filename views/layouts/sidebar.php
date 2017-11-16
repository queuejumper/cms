<?php
use yii\widgets\Menu;
?>
<div class="sidebar-items-wrapper">
	<div class="sidebar-toggle">
	<span class="glyphicon glyphicon-align-justify menu-bars" aria-hidden="true"></span>
	<span id="menu-bars">Menu </span>
	</div>
		<?php
		echo Menu::widget([
			'encodeLabels' => false,
			'itemOptions' => ['class' => 'sidebar-items'],
		    'items' => [
		    	['label' => '<span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;<span id="sidebar-item">Overview</span>', 'url' => ['/overview']],
		        ['label' => '<span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;<span id="sidebar-item">Post</span>', 'url' => ['/create']],
		        ['label' => '<span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;<span id="sidebar-item">Pending</span>', 'url' => ['/pendding'],'visible' => Yii::$app->user->isGuest],
		        ['label' => '<span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;<span id="sidebar-item">Overview</span>', 'url' => ['/website-overview']],
		        ['label' => '<span class="glyphicon glyphicon-tasks"></span>&nbsp;&nbsp;<span id="sidebar-item">Pending</span>', 'url' => ['/uploaded']],	        
		    ],
		]);
		?>
</div>