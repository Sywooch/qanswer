<?php
use app\components\Formatter;
use yii\helpers\Html;
?>

  <?php foreach ($favs as $fav):?>
  <div class="favorites-count">
        <input type="hidden" value="<?php echo $fav->question->id;?>">
        <a class="star-off star-on"></a>
        <div class="favoritecount">
        	<b class="favoritecount-selected"><?php echo $fav->question->favcount;?></b>
        </div>
    </div>

	<div id="question-summary-<?php echo $fav->question->id;?>" class="question-summary narrow">
	    <div class="cp" onclick="window.location.href='<?php echo $fav->question->url;?>'">
	        <div class="votes">
	            <div class="mini-counts"><?php echo $fav->question->score;?></div>
	            <div>票</div>
	        </div>
	        <div class="status<?php if ($fav->question->accepted) echo " answered-accepted"; else echo " answered";?>">
	            <div class="mini-counts"><?php echo $fav->question->answercount;?></div>
	            <div>答案</div>
	        </div>
	        <div class="views">
	            <div class="mini-counts">
	            	<span title="<?php echo $fav->question->viewcount;?>次"><?php echo Formatter::view($fav->question->viewcount);?></span>
	            </div>
	            <div>阅读</div>
	        </div>
	    </div>
	    <div class="summary">
	        <h3>
				<?php echo Html::a(Html::encode($fav->question->title), $fav->question->url,array('class'=>'question-hyperlink','title'=>Html::encode($fav->question->title)));?>
	        </h3>
	        <div class="tags t-java t-interview-questions">
			<?php
            foreach (explode(' ',$fav->question->tags) as $tag) {
            	echo Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
            }
            ?>
	        </div>
	        <div class="started">
	        	<?php 
                echo Html::a(
                        Html::tag(
                            'span',
                            Formatter::ago($fav->question->activity), 
                            ['class'=>'relativetime','title'=>Formatter::time($fav->question->activity)]
                        ), 
                        ['questions/view','id'=>$fav->question->id],
                        ['class'=>'started-link']
                    );
                ?>
				<?php echo Html::a(Html::encode($fav->question->author->username), array('users/view','id'=>$fav->question->author->id));?>
				<span title="威望" class="reputation-score"><?php echo $fav->question->author->reputation;?></span>
	        </div>
	    </div>
	</div>
    <br class="cbt">
    <?php endforeach;?>