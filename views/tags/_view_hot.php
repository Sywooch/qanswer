<?php
$this->title = $tag->name. "：热门回答";
$this->registerMetaTag(['name' => 'description', 'content' => $tag->name], 'description');
?>
<div id="mainbar-full">
    <div class="subheader">
		<h1>标签：<?php echo $tag->name;?></h1>
		<?= \yii\widgets\Menu::widget($submenu);?>
    </div>
</div>
<style type="text/css">
  #questions .subtabs {
      width: auto;
      margin-top: 10px;
  }
  .answer-link {
      width: 650px;
      padding-left: 0;
      padding-bottom: 8px;
  }
  .excerpt
  {
      margin-top: 8px;
  }
</style>

<div id="questions">
	<div class="content-inside">
        <h2 class="fl">热门答案&nbsp;<?php echo \yii\helpers\Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('rel'=>'tag','class'=>'post-tag','title'=>"该标签热门答案"));?></h2>
        <?= \yii\widgets\Menu::widget($subtabs);?>
		<div style="clear: both;"></div>
		<div class="question-mini-list">
			<?php
			foreach ($answers as $item) {
				echo $this->render('_answer',array('data'=>$item));
			}
			?>
	    </div>
	     <br class="cbt">
	</div>
</div>



<div id="sidebar">
	<div class="module">
		<div class="summarycount al"><?php echo $tag->frequency;?></div>
		<p>问题使用了该标签</p>
		<div class="tagged">
			<?php echo \yii\helpers\Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>"显示包含该标签的问题"));?>
	    </div>
	</div>
</div>