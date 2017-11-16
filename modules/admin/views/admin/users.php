<?php
use yii\helpers\Html;
use yii\bootstrap\ButtonDropdown;
use app\components\Helper;
$this->title = 'Site Overview';
?>
<div class="primary-box">
	<?= $this->title?>
	<div class="users-table">
		<table class="table table-hover">
		  <thead>
		    <tr>
		      <th>#</th>
		      <th>username</th>
		      <th>email</th>
		      <th>Joined</th>
		      <th>Posts</th>
		      <th>Status</th>
		      <th>Access</th>
		      <th>Actions</th>
		    </tr>
		  </thead>
		  <tbody>
		  	<?php foreach($users as $index => $user): 
		  		 if($user['isBanned']) $row_class = 'banned-row';
		  		  elseif(!$user['isActive']) $row_class = 'disabled-row';
		  		  else $row_class = "";
		  			echo "<tr class='user-row-{$index} {$row_class}'>";
		  		?>
		      <th scope="row"><?=$index+1?></th>
		      <td><?=$user['username']?></td>
		      <td><?=$user['isActive']?></td>
		      <td><?=$user['created_at']?></td>
		      <td><?=$user['posts_num']?></td>
		      
				<td>
					<?php
					 if($user['isActive']){
					 	$status_class = 'glyphicon glyphicon-ok';
					 	$status_option = 'deactivate';
					 	$status_title = 'active';
					 }else{
					 	$status_class = 'glyphicon glyphicon-remove';
					 	$status_option = 'activate';
					 	$status_title = 'Inactive';
					 }
					 ?>
					 <div class="user-status-menu" title = <?=$status_title?> >
					 <?php
						echo ButtonDropdown::widget([
							'encodeLabel' => false,
						    'label' => "<span class='{$status_class}'></span>",
						    'dropdown' => [
						        'items' => [
						            ['label' => $status_option , 'url' => false, 'options' => 
						            ['class' => 'action-btn','data-on' =>'user','data-option' => 'u-status','data-type' =>$status_option , 'data-value' => $user['id']]
						        	],
						        ],
						    ],
						]);?>
					</div>
				</td>

				<td>
					<?php
					 if($user['isBanned']){
					 	$access_class = 'glyphicon glyphicon-thumbs-down';
					 	$access_option = 'unban';
					 	$access_title = 'banned';
					 }else{
					 	$access_class = 'glyphicon glyphicon-thumbs-up';
					 	$access_option = 'ban';
					 	$access_title = 'unbanned';
					 }
					 ?>
					 <div class="access_menu" title = <?=$access_title?> >
					 <?php
						echo ButtonDropdown::widget([
							'encodeLabel' => false,
						    'label' => "<span class='{$access_class}'></span>",
						    'dropdown' => [
						        'items' => [
						            ['label' => $access_option , 'url' => false, 'options' => 
						            ['class' => 'action-btn','data-on' =>'user','data-option' => 'access','data-type' =>$access_option , 'data-value' => Helper::encrypt($user['id'])]
						        	],
						        ],
						    ],
						]);?>
					</div>
				</td>
				<td>
					<span class="glyphicon glyphicon-trash action-btn" data-on= 'user' data-option="actions" data-type="delete" data-index=<?=$index?> data-value=<?= Helper::encrypt($user['id'])?> title="Delete"></span>
				</td>
		    </tr>
		<?php endforeach;?>
		  </tbody>
		</table>
	</div>
</div>


