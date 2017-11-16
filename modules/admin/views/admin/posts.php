<?php
use yii\helpers\Html;
use app\components\Helper;
use yii\bootstrap\ButtonDropdown;
$this->title = 'All Posts';
?>
<div class="primary-box">
	<?= $this->title?>
	<div class="users-table">
		<table class="table table-hover">
			<thead>
			    <tr>
			      <th>#</th>
			      <th>Title</th>
			      <th>Username</th>
			      <th>Category</th>
			      <th>Created</th>
			      <th>Privacy</th>
			      <th>status</th>
			      <th>Reported</th>
			      <th>Actions</th>
			    </tr>
			</thead>
		  <tbody>
		  	<?php foreach($posts as $index => $post):
		  			if(!$post['approved']) $row_class = 'disabled-row';
		  			else $row_class = "";
		  			echo "<tr class='post-row-{$post['id']} {$row_class}'>";
		  		?>
				<th scope="row"><?=$index+1?></th>
				<?= "<td id='title-cell' title='{$post['title']}'>{$post['title']}</td>" ?>
				<td><?=$post['author']?></td>
				<td><?=$post['category']?></td>

				<td><?=Helper::dateFrom($post['created_at'])?></td>
				<td>
					<?php
					 if($post['public']){
					 	$privacy_class = 'glyphicon glyphicon-globe';
					 	$privacy_option = 'private';
					 	$privacy_title = 'public';
					 }else{
					 	$privacy_class = 'glyphicon glyphicon-lock';
					 	$privacy_option = 'public';
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
						            ['label' => $privacy_option , 'url' => false, 'options' => 
						            ['class' => 'action-btn','data-on' =>'post','data-option' => 'privacy','data-type' =>$privacy_option , 'data-value' => $post['id']]
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
					 	$status_option = 'unapprove';
					 	$status_title = 'approved';
					 }else{
					 	$status_class = 'glyphicon glyphicon-remove';
					 	$status_option = 'approve';
					 	$status_title = 'unapproved';
					 }
					 ?>
					 <div class="status_menu" title = <?=$status_title?> >
					 <?php
						echo ButtonDropdown::widget([
							'encodeLabel' => false,
						    'label' => "<span class='{$status_class}'></span>",
						    'dropdown' => [
						        'items' => [
						            ['label' => $status_option , 'url' => false, 'options' => 
						            ['class' => 'action-btn','data-on' =>'post','data-option' => 'status','data-type' =>$status_option , 'data-value' => $post['id']]
						        	],
						        ],
						    ],
						]);?>
					</div>
				</td>
				<td><?=$post['reported']?></td>
				<td>
					<span class="glyphicon glyphicon-trash action-btn" data-on="post" data-option="actions" data-type="delete" data-value=<?=$post['id']?> title="Delete"></span>
				</td>
		    </tr>
		<?php endforeach;?>
		  </tbody>
		</table>
	</div>
</div>


