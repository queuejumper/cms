<?php
use yii\helpers\Html;
use app\components\Helper;
$this->title = 'search..'
?>

<div class="primary-box  search-result">
	<?php 
	if(isset($posts) && count($posts) > 0):
			echo Html::tag('h4',($posts['search']) ? "Search result for '{$search_key}'" : 'No data found!' ,['class' => 'search-result-head']);
			if(!$posts['search'] && !empty($posts['result']))
			echo Html::tag('h4','<hr>Recommended: ',['class' => 'search-result-head']);
	foreach($posts['result'] as $post):		
		echo "<a href='/post/{$post['id']}' class='row small-post-row'>"; ?>
		<div class="" data-value=<?= $post['id']?> >
			<div class="col-xs-5 small-post-img">
				img
			</div>
			<div class="col-xs-7 small-post-info">
				<?= Html::tag('p', $post['title'], ['class' => 'small-post-title']);?>
				<?= Html::tag('div',$post['author'] , ['class' => 'small-post-author']);?>
				<?= Html::tag('div', Html::encode(Helper::formatNumber($post['viewed']).' views'), ['class' => 'small-post-views']); ?>
			</div>
		</div>
	 </a>
<?php ; endforeach;endif;?>
</div>