<?php
use yii\helpers\Html;
$this->title = $this->context->title;
?>
<div class="row">
    <div id="mainbar" class="col-md-9">
        <div class="subheader">
            <h1 id="h-all-questions">"<?php echo Html::encode($tag->name); ?>"标签问题</h1>
            <?= \yii\widgets\Menu::widget($submenu);?>
        </div>
        <div id="questions">
            <div class="welovelewen">
                <h3><?php echo $tag->name; ?></h3>
                <p><?php echo isset($tag->post) ? $tag->post->excerpt : ''; ?></p>
                <p>
                    <?php echo Html::a("关于", ['tags/view', 'tag' => $tag->name]); ?>
                    <?php echo Html::a("用户", ['tags/view', 'tag' => $tag->name, 'op' => 'topusers'], ['title' => "顶级用户"]); ?>
                    <?php echo Html::a("新回答", ['tags/view', 'tag' => $tag->name, 'op' => 'topusers'], ['title' => '关于该标签的新回答']) ?>
                </p>
            </div>
            <?php
            foreach ($tagQuestions as $item) {
                echo $this->render('_question', ['data' => $item->question]);
            }
            ?>
        </div>
        <?= yii\widgets\LinkPager::widget(['pagination' => $pages]);?>
    </div>
    <div id="sidebar" class="col-md-3">
        <div class="module">
            <div class="summarycount al"><?php echo $pages->totalCount; ?></div>
                <p>该标签的问题 </p>
            <div class="tagged">
                <?php echo Html::a($tag->name, ['questions/tagged', 'tag' => $tag->name], ['rel' => 'tag', 'class' => "post-tag"]); ?>
                <?php echo Html::a("关于 »", ['tags/view', 'tag' => $tag->name, 'op' => 'info'], ['rel' => 'tag', 'class' => "ml10", 'title' => '标签基本信息，统计，FAQ']); ?>
            </div>
        </div>
        <?php echo $this->render('/common/_sidebar_tags'); ?>
    </div>
</div>