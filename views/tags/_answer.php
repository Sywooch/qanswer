<?php
use app\components\Formatter;
?>
<div id="answer-id-5423187" class="answer-summary question-summary">
	<div class="statscontainer">
		<div class="vote-info">
            <div onclick="window.location.href='<?= Yii::$app->urlManager->createUrl('questions/view',array('id'=>$data->idv,'#'=>$data->id));?>'" title="该回答所获得投票" class="answer-votes<?php if ($data->accepted==1) echo ' answered-accepted';?> default"><?php echo $data->score;?></div>
		</div>
		<br class="cbt">
	</div>
	<div class="summary">
		<div class="answer-link">
			<?php echo \yii\helpers\Html::a(\yii\helpers\Html::encode($data->question->title), array('questions/view','id'=>$data->question->id,"#"=>$data->id),array('class'=>'answer-hyperlink'));?>
		</div>
		<div class="excerpt">
			<?php //echo String::cutString($data->content,400);//@todo 修正?>
			<?php echo $data->excerpt;?>
		</div>

		 <div class="tags<?php foreach(explode(' ',$data->tags) as $tag) echo ' t-'.$tag;?>">
			<?php
            foreach (explode(' ',$data->question->tags) as $tag) {
            	echo \yii\helpers\Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
            }
            ?>
        </div>
		<div class="started fr">
			<div class="user-info">
				<div class="user-action-time">回答于
            		<span class="relativetime" title="<?php echo Formatter::time($data->createtime);?>"><?php echo Formatter::ago($data->createtime);?></span>
            	</div>
				<?= $this->render('/common/_user',array('user'=>$data->author));?>
			</div>
		</div>
		<br class="cbt">
	</div>
	<br class="cbt">
</div>