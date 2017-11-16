<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\Helper;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\i18n\Formatter;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\ActiveForm;
?>
	<div class="row  post-wrap">
		<div class="col-md-6 col-md-offset-1">
			<?= Html::tag('span', $post['title'], ['class' => 'post-title']);?>
				<span class="author-options">
					 <?php if((\Yii::$app->user->can('manageOwnPost', ['post' => $post['id']]) || \Yii::$app->user->can('managePosts'))):
					 	if($post['public'])
					 	{
					 		$privacy_class = "glyphicon glyphicon-globe";
					 		$btn_title = "Public";
					 	}else
					 	{
					 		$privacy_class = "glyphicon glyphicon-lock";
					 		$btn_title = "Private";
					 	}
					   echo "<span class='privacy_menu' title='{$btn_title}'>";
						echo ButtonDropdown::widget([
							'encodeLabel' => false,
						    'label' => "<span class='{$privacy_class}'></span>",
						    'dropdown' => [
						        'items' => [
									($post['public']) ? (
										['label' => 'Private' , 'url' => false, 'options' => [
										'class' => ' post-action-btn'
										,'data-action' => 'private'
										,'title' => 'Private'
						            	,'data-value' => $post['id']]]) : (
					            		['label' => 'Public' , 'url' => false, 'options' => [
										'class' => 'post-action-btn'
										,'data-action' => 'public'
										,'title' => 'Public'
						            	, 'data-value' => $post['id']
						            	]])
						        	],
						        ],
						]);
						echo "</span>";
						echo Html::tag('span','',([
								'class' => 'glyphicon glyphicon-trash post-action-btn remove-btn',
								'data-action'=>"delete",
								'data-value'=>$post['id'],
								'data-type' => 'main'
								])); endif;?>
				</span>
			<div class="post-content">dsa
			</div>
				<div class="post-options">
					<?= Html::tag('span', Html::encode(Helper::formatNumber($post['viewed']).' views'), ['class' => 'post-views']); ?>
					<?= Html::tag('a','',(Yii::$app->user->id) ? (($userLike) ? [
								'class' => 'glyphicon glyphicon-heart hearted',
								'id' => 'heart-btn',
								'title' => 'Unlike',
								'data-action' => 'unlike',
								'data-post' =>$post['id'] ,
								] : [
								'class' => 'glyphicon glyphicon-heart-empty',
								'id' => 'heart-btn',
								'title' => 'Like',
								'data-action' => 'like',
								'data-post' =>$post['id'] ,
								]
								):(['id' => 'heart-btn','class' => 'glyphicon glyphicon-heart-empty','href' => '/login']))?>
					<?= Html::tag('span', Html::encode(Helper::formatNumber($likes['likes'])),
								 ['class' => 'post-likes']); ?>	
					 <?php Modal::begin([
						    'header' => '<h4>Share this post!</h4>',
						    'toggleButton' => ['label' => 
							 	'<div class="share-wrapper" >'
									.Html::tag('p','',([
												'class' => 'glyphicon glyphicon-share share-btn',
												'title' => 'Share',
												]))
									.Html::tag('p','share',([
												'class' => 'share-btn',
												'title' => 'Share',
												])).
								'</div>'	
								],
							]);?>
					 <?php
					 	$message = "http://new.com/post/{$post['id']}";
					 	$facebookSharer = "https://www.facebook.com/sharer/sharer.php?u={$message}";
					 	$twitterSharer = "https://twitter.com/intent/tweet?text={$message}";
					 	$googleSharer = "https://plus.google.com/share?url={$message}";
					 	$whatsappSharer = "whatsapp://send?text={$message}";		
					 ?>
					<div class="share-box">
						<a href=<?=$facebookSharer?>>
							<span class="share-btn fb-btn">
								<i class="fa fa-facebook-f"></i>
							</span>
						</a>
							<span class="messenger-btn" action-href=''>
							</span>
						<a  href=<?=$twitterSharer?>>
							<span class="share-btn twitter-btn">
								<i class="fa fa-twitter"></i>
							</span>
						</a>
						<a href=<?=$googleSharer?>>
							<span class="share-btn google-plus-btn">
								<i class="fa fa-google"></i>
							</span>
						</a>
						<a id="whatsapp-btn" href=<?=$whatsappSharer?> 
							data-action="share/whatsapp/share">
							<span class="share-btn whatsapp-btn">
								<i class="fa fa-whatsapp"></i>
							</span>
						</a>
					</div>		

					<?php Modal::end();?>

					<div class="report-wrapper">
						<?= Html::tag('p','',([
									'class' => 'glyphicon glyphicon-flag report-btn',
									'title' => 'Report inappropriate content',
									'data-action' => 'report',
									'data-post' =>$post['id'] 
									]))?>
						<?= Html::tag('p','Report',([
									'title' => 'Report inappropriate content',
									'data-action' => 'report',
									'data-post' =>$post['id'] 
									]))?>
					</div>	
				</div><br>

		<div class="post-info">
			<hr><div class="user_info">
					<?= Html::a(Html::img("@web/img/users/{$post['author_pic']}"),["/user/{$post['author']}"],['class' =>'user_pic','title' => $post['author']])?>
					<?= Html::a(Html::encode($post['author'])
						,["/user/{$post['author']}"],['class' => 'username'])?>
				</div>
				<div class="col-xs-10 col-xs-offset-1 post-info-col">
						<br>
					<?= Html::tag('div','Published on ' .\Yii::$app->formatter->asDate(
						$post['created_at'], 'long'),['class' => 'date']);?>
					<?= Html::tag('div',Html::encode($post['description'])
							,['class' => 'description ellipsis'])?>
					<div class="more-post-info">
							<br>
						<div class="category-wrapper">
							<?= Html::tag('span',Html::encode('Category: '))?>						
							<?= Html::a(Html::encode($post['category']),["/category/{$post['category']}"]
								,['class' => 'category'])?>
						</div><br>
						<div class="tags-wrapper">
							<?= Html::tag('span',Html::encode('Tags: '))?>
							<?php 
							$tags = explode(',', $post['tags']);
							foreach ($tags as $tag) 
							{
								echo Html::a(Html::encode($tag),["/tag/{$tag}"]
								,['class' => 'tag']);
								echo '&nbsp;';
							} ?>	
						</div>				
					</div>
						<?= Html::tag('div',Html::encode('Show more info'),['class' => 'more-inf-btn'])?>
					<br>
				</div>
			</div>
		</div><br>

		<div class="sidebar-posts col-md-5">
			<?php if(isset($related_posts) && count($related_posts) > 0): ?>
			<h4>Related Posts</h4>
				<div class="sidebar-post">
					<?php 
						foreach($related_posts as $related_post):		
							echo "<a href='/post/{$related_post['id']}' class='row small-post-row'>"; ?>
							<div class="" data-value=<?= $related_post['id']?> >
								<div class="col-xs-5 small-post-img">
									img
								</div>
								<div class="col-xs-7 small-post-info">
									<?= Html::tag('p', $related_post['title'], ['class' => 'small-post-title']);?>
									<?= Html::tag('div',$related_post['author'] , ['class' => 'small-post-author']);?>
									<?= Html::tag('div', Html::encode(Helper::formatNumber($related_post['viewed']).' views'), ['class' => 'small-post-views']); ?>
								</div>
							</div>
						 </a>
					<?php ; endforeach;?>
				</div>
			<br><div class="show-more related" data-value=<?=$post['id']?> >Show More</div>
		<?php endif; ?>
		</div>
		<div class="row">

		<div class="comments-wrapper col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-1">
		<?php 	if(\Yii::$app->user->can('addComment',['user' => \Yii::$app->user->id]) 
					|| \Yii::$app->user->can('manageComments'))
				{
					echo Html::textarea ('new_comment',null, [
							'class' => 'comment-input','placeHolder' => 'Add your comment',
						]) ?>
				<span><?= Html::button('Add', [
							'class' => 'btn btn-primary comment-action',
							'data-action' => 'new-comment',
							'data-type' => 'comment',
							'data-post' => $post['id'],
						]) ?>
					
				</span>
			<?php 
				}elseif(!\Yii::$app->user->id)
				{
					echo Html::a( Html::encode('Please log in to add a comment'),
					['/login'],['class' => 'comment-notify']);
				}else
				{	echo Html::tag('div', Html::encode('Your not allowed to add comments!')
					,['class' => 'comment-notify']);
				}
				?>

			<div class="comments-box">
			<?php if(isset($comments) && count($comments) > 0):
				foreach($comments as $key => $comment):
					$comment_class = 'comment-row-'.$comment['id'];?>
				<div class=<?=$comment_class?>>
				<span class="user_info">
					<?= Html::a(Html::img("@web/img/users/{$comment['user_pic']}"),["user/{$comment['user']}"],['class' =>'user_pic','title' => $comment['user']])?>
					<?= Html::a(Html::encode($comment['user'])
						,["user/{$comment['user']}"],['class' => 'username'])?>
				</span>
				<?= Html::tag('span', Html::encode(Helper::dateFrom($comment['date'])),['class' => 'date'])?>
				<?= Html::tag('p', Html::encode($comment['comment']),['class' => 'comment'])?>
				<?="<div class='comment-options-{$comment['id']}'>" ?>
					<?php if(\Yii::$app->user->can('addComment',['user' => \Yii::$app->user->id]) 
							|| \Yii::$app->user->can('manageComments')):
							echo Html::tag('a', Html::encode('Reply'),[
								'class' => 'reply-btn',
								'data-toggle' => 'popover',
								'title' => 'Add a reply',
								'data-html' => false,
								'data-action' => 'new-reply',
								'data-type' => 'reply',
								'data-comment' => $comment['id'],
								'data-post' => $post['id'],
								'data-user' => Helper::encrypt(Yii::$app->user->id)
								]); endif;?>
						<?php if(\Yii::$app->user->can('manageOwnComment', ['comment' => $comment['id']]) 
							|| \Yii::$app->user->can('manageComments')):
						echo Html::tag('span','',[
						'class' => 'delete-comment comment-action glyphicon glyphicon-trash' ,
						'data-action' => 'delete-comment',
						'data-comment' => $comment['id'],
						'data-type' => 'comment',
						'aria-label' => 'delete'
						]); 
						endif;
						 ?>
						<?php if($comment['replies'] > 0):
						echo Html::tag('a', $comment['replies'] ,[
						'class' => 'view-replies comment-action glyphicon glyphicon-comment' ,
						'id' => 'btn-'.$comment['id'], 
						'data-action' => 'loadReplies',
						'data-id' =>  Helper::encrypt(\Yii::$app->user->id),
						'data-comment' => $comment['id'],
						'data-toggle' => 'collapse', 
						'aria-expanded' => false, 
						'aria-controls' => $comment['id'],
						'href' => '#'.$comment['id']
						]); ?>
				
					<div class="collapse replies-box" id=<?=$comment['id']?> >
					<?php echo "</div>"; endif; echo '</div><hr></div>'; endforeach;  endif;?>
			</div>
		</div>
	</div>
	</div>