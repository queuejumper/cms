<?php
use yii\helpers\Html;
use app\components\Helper;
use yii\bootstrap\ButtonDropdown;
$this->title = 'My Posts';
?>
<div class="primary-box">
	<?= $this->title?>
	<div class="my-posts-table">
		<table class="table table-hover">
			<thead>
			    <tr>
			      <th>#</th>
			      <th>Title</th>
			      <th>Category</th>
			      <th>Created</th>
			      <th>Privacy</th>
			      <th>status</th>
			      <th>Reported</th>
			      <th>Actions</th>
			    </tr>
			</thead>
		  <tbody>
		  	<?php foreach($all_posts as $index => $post):
		  			if(!$post['approved']) $row_class = 'disabled-row';
		  			else $row_class = "";
		  			echo "<tr class='post-row-{$post['id']} {$row_class}'>";
		  		?>
				<th scope="row"><?=$index+1?></th>
				<?= "<td id='title-cell' title='{$post['title']}'>{$post['title']}</td>" ?>
				<td><?=$post['category']?></td>

				<td><?=$post['created_at']?></td>
				<td>
					<?php
					 if($post['public']){
					 	$privacy_class = 'glyphicon glyphicon-globe';
					 	$action = 'private';
					 	$privacy_title = 'public';
					 }else{
					 	$privacy_class = 'glyphicon glyphicon-lock';
					 	$action = 'public';
					 	$privacy_title = 'private';
					 }
					 ?>
					 <div class="privacy_menu" title = <?=$privacy_title?> >
					 <?php
						echo ButtonDropdown::widget([
							'encodeLabel' => false,
						    'label' => "<span class='{$privacy_class}'></span>",
						    'dropdown' => [
						        'items' => [
						            ['label' => $action , 'url' => false, 'options' => 
						            ['class' => 'post-action-btn','data-action' =>$action , 'data-value' => $post['id'] , 'title' =>$action]
						        	],
						        ],
						    ],
						]);?>
					</div>
				</td>

				<td>
					<?php
					 if($post['approved']){
					 	$status_class = 'glyphicon glyphicon-ok';
					 	$title = 'approved';
					 }else{
					 	$status_class = 'glyphicon glyphicon-remove';
					 	$title = 'unapproved';
					 }
					 echo "<span class='{$status_class}' title='{$title}'></span>"
					 ?>
				</td>
				<td><?=$post['reported']?></td>
				<td>
					<span class="glyphicon glyphicon-trash post-action-btn" data-action="delete" data-value=<?=$post['id']?> aria-label="Delete"></span>
				</td>
		    </tr>
		<?php endforeach;?>
		  </tbody>
		</table>
	</div>
</div>


