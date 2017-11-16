<?php
use yii\helpers\Html;
use app\modules\post\models\Post;
use yii\helpers\ArrayHelper;
use app\components\Helper;
?>

			<div class="primary-box home-box">	
					<?php
					if($all_posts && !empty($all_posts)){

						$i = 0;
						foreach($all_posts as $key => $posts):
							if($key != 'categories' && !empty($posts)) {
								echo "<div class='row justify-content-center  small-post-row'>";
								echo "<h4>{$key}</h4>";
							}
						?>
						<?php
							foreach ($posts as $key1 => $post):
								if($key == 'categories' && !empty($post)){
								 echo "<div class='justify-content-xs-center row'>";
								 echo "<h4>{$key1}</h4>";
								}
							$date =Yii::$app->formatter->asRelativeTime($post['created_at']);
							echo "<div class='col-sm-6 col-xs-2  small-post-col'>";
							echo "<a href='/post/{$post['id']}'>";
							echo "<div class='small-post-img '>Image";
							echo "</div></a>";
							echo "<div class='small-post-info'>";
							echo Html::a(Html::tag('p', Html::encode($post['title']), ['class' => 'small-post-title']),["/post/{$post['id']}"],['title' => $post['title']]);
							echo Html::a(Html::encode($post['author']),["/user/{$post['author']}"], ['class' => 'small-post-author']);
							echo "<br><a href='/post/{$post['id']}'>";
							echo Html::tag('span', Html::encode(Helper::formatNumber($post['viewed']).' views'), ['class' => 'small-post-views']);
							echo Html::tag('span', Html::encode($date), ['class' => 'small-post-date']);
							echo "</a></div></div>";
							
							if($key == 'categories'){echo "</div>";$i++;}
							endforeach;
							if($key != 'categories') echo "</div><hr>"; //END OF ROW
							endforeach; 
					}else{echo "<h3>No data to display!</h3>";
				}?>
			</div>
