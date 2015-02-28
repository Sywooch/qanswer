


<div class="user-info">
	<div class="user-details">
		<span title="wiki帖子，投票不产生威望，至少有100点威望才能编辑" class="community-wiki"><?php echo Yii::t('global','community wiki')?></span>
	</div>
	<br>
	<div class="user-details">
		<?php echo CHtml::link($data->revCount."个版本", array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
	</div>
</div>
