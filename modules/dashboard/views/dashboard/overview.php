
<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Helper;
?>
<div class="overview-wrapper">
	<div class="row primary-box">
		<div class="col-md-3">
			<h4>Recently</h4>
			<p>Your recently posts</p>
			<table class="table table-hover">
				<tbody>
						<?php
						foreach ($recentPosts as $recentPost):
							echo "<a href='/post/{$recentPost->id}' >";
							echo "<tr>";
							echo "<td>".$recentPost->title."</td>";
							echo "<td>".Helper::dateFrom($recentPost->created_at)."</td>";
							echo "</tr></a>";
						endforeach;
						?>
				</tbody>
			</table>
		</div>

		<div class="col-md-9 statistics">
			<h4>Statistics</h4>
			<div class="col-md-3 col">
				<div class="downloads-widget">
					<h4>You've created</h4>
					<h4><?= count($allPosts);?> Posts</h4>
				</div>
			</div>
			<div class="col-md-3 col">
				<div class="downloaded-widget">
					<h4>You've created</h4>
					<h4><?= count($allPosts);?> Posts</h4>
				</div>
			</div>
			<div class="col-md-4 col">
				<div class="created-widget">
					Downloads
				</div>
			</div>
			<div class="col-md-2 col">
				<div class="pending-widget">
					<h4>You've</h4>
					<h4><?= count($pendingPosts);?> Pendings</h4>
				</div>
			</div>
		</div>
	</div>
</div>

