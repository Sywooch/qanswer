<?php
use app\models\Post;
use yii\helpers\Html;
use app\components\Formatter;
?>
<div id="question-summary-<?php echo $data->id;?>" class="question-summary">
    <div class="statscontainer">
        <div class="statsarrow"></div>
        <div class="stats">
            <div class="vote">
                <div class="votes">
                    <span class="vote-count-post"><strong><?php echo $data->score;?></strong></span>
                    <div class="viewcount">投票</div>
                </div>
            </div>
	        <?php
			if ($data->isAccepted()) {
        		$answerClass = "answered-accepted";
        	} elseif ($data->answercount==0) {
	        	$answerClass = "unanswered";
	        } else {
	        	$answerClass = "answered";
	        }
	        ?>
            <div class="status <?php echo $answerClass;?>">
                <strong><?php echo $data->answercount;?></strong>回答
            </div>
        </div>
        <div title="<?php echo $data->viewcount;?>次" class="views">阅读 <?php echo app\components\Formatter::view($data->viewcount);?></div>
    </div>
   
    <div class="summary">
    	<?php if ($data->bountyAmount>0):?>
            <div title="该问题有悬赏<?= $data->bountyAmount;?>威望" class="bounty-indicator">+<?= $data->bountyAmount;?></div>
    	<?php endif;?>
    	<h3><?= Html::a(Html::encode($data->title), $data->url, ['class'=>'question-hypera']);?></h3>
        <div class="excerpt">
			<?php echo $data->excerpt;?>
        </div>

        <div class="tags<?php foreach(explode(' ',$data->replaceTags($data->tags)) as $tag) echo ' t-'.$tag;?>">
			<?php
            foreach (explode(' ',$data->tags) as $tag) {
            	echo Html::a($tag, ['questions/tagged','tag'=>$tag], ['class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"]);
            }
            ?>
        </div>
        <div class="started">
            <div class="user-info">
            	<div class="user-action-time">提问
            		<span class="relativetime" title="<?= Formatter::time($data->createtime);?>"><?= Formatter::ago($data->createtime);?></span>
            	</div>
				<?= $this->render('/common/_user', ['user'=>$data->author]);?>
			</div>
        </div>
    </div>
</div>