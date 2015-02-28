<div id="mainbar">
	<div class="subheader">
        <h1 id="h-all-questions">标签问题</h1>
        <?php
        $this->widget('application.components.Mem4kMenu',$submenu);
		?>
    </div>
	<div id="questions">
		<?php
		foreach ($questions as $q){
			$this->renderPartial('_question',array('data'=>$q));
		}
		?>
	</div>
</div>
<div id="sidebar">
	<div class="module">
	    <div class="summarycount al"><?php echo $tag->frequency;?></div>
	    <p>标注了该标签的问题 </p>
	    <div class="tagged">
	    	<?php echo CHtml::link($tag->name, array('questions/tagged','tag'=>$tag->name),array('rel'=>'tag','class'=>"post-tag")); ?>
	    	<?php echo CHtml::link("关于 »", array('tags/view','tag'=>$tag->name,'op'=>'info'),array('rel'=>'tag','class'=>"ml10",'title'=>'标签基本信息，统计，FAQ')); ?>
		</div>
	</div>
	<?php $this->renderPartial('/common/_sidebar_tags');?>	
</div>