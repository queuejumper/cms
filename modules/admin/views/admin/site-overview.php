
<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Helper;

$this->title = 'Site Overview';
?>
	<div class="row primary-box overview-wrapper">
		<div class="col-md-12 statistics">
			<h4>Statistics</h4>
			<div class="col-md-4 col">
				<div class="g-widget">
					<h4>Users</h4>
					<h4><?=$users['users_num']?></h4>
				</div>
			</div>
			<div class="col-md-4 col">
				<div class="y-widget">
					<h4>Posts</h4>
					<h4><?= $posts['posts_num']?></h4>
				</div>
			</div>
			<div class="col-md-4 col">
				<div class="b-widget">
					<h4>Views</h4>
					<h4><?= $posts['views']?></h4>
				</div>
			</div>

		</div>
	</div>

