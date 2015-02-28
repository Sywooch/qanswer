<div class="question-summary narrow">
	你的帖子<a href="<?php echo $item->url;?>"><?php echo $item->title;?></a>有超过<?php echo Yii::app()->params['posts']['unwikiToWikiCount'];?>个不同用户编辑，自动转换为社区wiki模式
	<p class="inboxSummary"><?php echo $item->summary;?></p>
</div>