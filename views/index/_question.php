<?php
use app\components\Formatter;
use yii\helpers\Html;
?>
<div id="question-summary-<?php echo $data->id;?>" class="question-summary narrow">
    <div class="cp" onclick="window.location.href='<?php echo $data->url;?>'">
        <div class="votes">
            <div class="mini-counts"><?php echo $data->score;?></div>
            <div>投票</div>
        </div>
        <?php
        if ($data->accepted==1) {
        	$answerClass = "answered-accepted";
        } elseif ($data->answercount==0) {
        	$answerClass = "unanswered";
        } else {
        	$answerClass = "answered";
        }
        ?>
        <div class="status <?php echo $answerClass;?>">
            <div class="mini-counts"><?php echo $data->answercount;?></div>
            <div>回答</div>
        </div>
        <div class="views">
            <div class="mini-counts"><?php echo Formatter::view($data->viewcount);?></div>
            <div>阅读</div>
        </div>
    </div>
    <div class="summary">
        <?php if ($data->bountyAmount>0):?>
    	<div title="该问题有悬赏<?php echo $data->bountyAmount;?>威望" class="bounty-indicator">+<?php echo $data->bountyAmount;?></div>
    	<?php endif;?>
        <h3>
        	<?php echo Html::a(Html::encode($data->title), $data->url,array('class'=>'question-hyperlink','title'=>$data->excerpt));?>
        </h3>
        <div class="tags<?php foreach(explode(' ',$data->replaceTags($data->tags)) as $tag) echo ' t-'.$tag;?>">
			<?php
            foreach (explode(' ',$data->tags) as $tag) {
            	echo Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
            }
            ?>
        </div>
        <div class="started">
			<span class="relativetime" title="<?php echo Formatter::time($data->createtime);?>"><?php echo Formatter::ago($data->createtime);?></span>
			<?php echo Html::a($data->author->username, array('users/view','id'=>$data->author->id),array('title'=>$data->author->username));?>
			<span title="威望" class="reputation-score"><?php echo $data->author->reputation;?></span>
        </div>
    </div>
</div>