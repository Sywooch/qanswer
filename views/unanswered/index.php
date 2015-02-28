<?php
use yii\helpers\Html;
?>
<div id="mainbar">
	<div class="subheader">
        <h1 id="h-all-questions">无答案问题</h1>
		<?php
//		$this->widget('application.components.Mem4kMenu',$submenu);
		?>
    </div>
	<div id="questions">
		<?php
		foreach($questions as $question) {
			if (!$question->poststate->isDelete()) {
				$this->render('/questions/_question',array('data'=>$question));
			}
		}
		?>
	</div>
	<div class="cbt"></div>
	<?php 
//    $this->widget('MLinkPager', array(
//	    'pages' => $pages,
//		'cssFile'=>false,
//	))
	?>
</div>
<div id="sidebar">
	<div id="questions-count" class="module">
        <div class="summarycount al"><?php echo $pages->totalCount;?></div>
        <p>问题</p>
        <?php if (isset($_GET['tab']) && $_GET['tab']=='noanswers'):?>
        <p class="supernova">没有任何回答</p>
        <?php else:?>
        <p class="supernova">所有回答都没有获得赞成投票</p>
        <?php endif;?>

    </div>
	<?php if (!Yii::$app->user->isGuest && false):?>
     <p>相关标签</p>
     <div class="tagged module">
     	<?php foreach($this->me->tags as $tag):?>
     		<?php echo Html::a($tag['tag'],array('unanswered/tagged','tag'=>$tag['tag']),array('class'=>'post-tag'));?>
     	<?php endforeach;?>
     </div>
     <?php endif;?>
	<?php $this->render('/common/_sidebar_tags');?>
</div>