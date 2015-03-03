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
<div class="row">
    <div id="questions" class="col-md-9">
        <div class="content-inside">
            <h2 class="pull-left">热门答案&nbsp;<?php echo \yii\helpers\Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('rel'=>'tag','class'=>'post-tag','title'=>"该标签热门答案"));?></h2>
            <?= \yii\widgets\Menu::widget($subtabs);?>
            <div class="question-mini-list">
                <?php
                foreach ($answers as $item) {
                    echo $this->render('_answer',array('data'=>$item));
                }
                ?>
            </div>
        </div>
    </div>

    <div id="sidebar" class="col-md-3">
        <div class="module">
            <div class="summarycount al"><?php echo $tag->frequency;?></div>
            <p>问题使用了该标签</p>
            <div class="tagged">
                <?php echo \yii\helpers\Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>"显示包含该标签的问题"));?>
            </div>
        </div>
    </div>
</div>