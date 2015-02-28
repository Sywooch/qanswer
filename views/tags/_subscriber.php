<div>
	<div class="tm-heading">
		<span class="tm-sub-info">
			<?php echo $tag->name;?>
		</span>
	</div>
	<div class="tm-description">
		<p><?php echo isset($tag->post) ? $tag->post->excerpt : "";?></p>
	</div>
</div>
<span class="tm-links">
	<?php echo CHtml::link("关于",array('tags/view','tag'=>$tag->name));?>
	<?php echo CHtml::link("新回答",array('tags/view','tag'=>$tag->name,'op'=>'topusers'),array('title'=>'关于该标签的新回答'))?>
	<?php echo CHtml::link("用户",array('tags/view','tag'=>$tag->name,'op'=>'topusers'),array('title'=>"顶级用户"));?>
</span>