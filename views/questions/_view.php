<?php
use app\models\Vote;
use yii\helpers\Html;
use app\components\Formatter;
?>
<table>
    <tbody>
    	<tr>
			<td class="votecell">
				<div class="vote">
				    <input type="hidden" value="<?php echo $data->id;?>"/>
				    <a title="<?php echo Yii::t('global','vote up');?>" class="vote-up-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::UPVOTE) echo " vote-up-on";?>"><?php echo Yii::t('global','vote up');?></a>
				    <span style="font-size: 200%;" class="vote-count-post"><?php echo $data->score;?></span>
				    <a title="<?php echo Yii::t('global','vote down');?>" class="vote-down-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::DOWNVOTE) echo " vote-down-on";?>"><?php echo Yii::t('global','vote down');?></a>

				    <a title="收藏 (取消收藏)" href="#" class="star-off <?php if ($data->hasFav!=NULL && $data->hasFav==Vote::FAV) echo "star-on";?>">收藏</a>
				    <div class="favoritecount"><b><?php echo $data->favcount;?></b></div>
				</div>
    		</td>
			<td class="postcell">
		        <div>
		            <div class="post-text">
		            	<?php
		            	//@todo 优化，保存到数据库中去
						$p = new HTMLPurifier();
						$data->content = $p->purify($data->content);
						echo $data->content;
						?>
		            </div>
		            <div class="post-taglist">
	            	<?php
	            	foreach (explode(' ',$data->tags) as $tag) {
	            		echo Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
	            	}
	            	?>
		            </div>
		            <table class="fw">
			            <tbody>
				            <tr>
					            <td class="vt">
						            <div class="post-menu">
					            	<?php
					            	if (!Yii::$app->user->isGuest) {
					            		echo $this->render('_post_menu',array('data'=>$data));
					            	}
					            	?>
					            	</div>
					            </td>
					            <?php if ($data->lastedit>0):?>
					            <td align="right" class="post-signature">
									<div class="user-info">
										<div class="user-action-time">编辑于
										<?php echo Html::a(Html::tag('span',Formatter::ago($data->lastedit),array('class'=>'relativetime')), array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
										</div>
										<div class="user-gravatar32"></div>
										<div class="user-details">
											<br>
										</div>
									</div>
								</td>
								<?php endif;?>
								<td class="post-signature<?php if ($data->author->id==Yii::$app->user->getId()) echo " owner";?>">
								<?php if (!$data->isWiki()):?>
						            <div class="user-info">
						            	<div class="user-action-time">提问于
						            		 <span class="relativetime" title="<?php echo Formatter::time($data->createtime); ?>"><?php echo Formatter::ago($data->createtime);?></span>
						            	</div>
						            	<?= $this->render('/common/_user',array('user'=>$data->author));?>
						            	<?php if ($data->revCount>1):?>
						            	<div class="user-details">
						            	<?php echo Html::a($data->revCount."个版本", array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
						            	</div>
						            	<?php endif;?>
						            </div>
									<br class="cbt">
								<?php else:?>
									<?php $this->render('/common/_user_wiki',array('data'=>$data));?>
								<?php endif;?>
								</td>
				            </tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<td class="votecell"></td>
			<td>
			<?= $this->render('/post/_comments',array('data'=>$data));?>
			</td>
		</tr>
		<?= $this->render('_bounty-notification',array('data'=>$data));?>
    </tbody>
</table>