<?php
use yii\helpers\Html;
$this->title = $this->context->title;
?>
<div id="mainbar">
    <div class="subheader">
        <h1 id="h-all-questions">"<?php echo Html::encode($tag->name); ?>"标签问题</h1>
        <?= \yii\widgets\Menu::widget($submenu);?>
    </div>
    <div id="questions">
        <div style="margin-top:10px;" class="welovelewen">
            <div style="float:left;">
                <h3><?php echo $tag->name; ?></h3>
                <p><?php echo isset($tag->post) ? $tag->post->excerpt : ''; ?></p>
                <p style="margin-bottom:0;">
                <?php echo Html::a("关于", array('tags/view', 'tag' => $tag->name)); ?>
                <span class="lsep">|</span>
                <?php echo Html::a("用户", array('tags/view', 'tag' => $tag->name, 'op' => 'topusers'), array('title' => "顶级用户")); ?>
                <span class="lsep">|</span>
                <?php echo Html::a("新回答", array('tags/view', 'tag' => $tag->name, 'op' => 'topusers'), array('title' => '关于该标签的新回答')) ?>
                </p>
            </div>
        </div>
        <?php
        foreach ($tagQuestions as $tq) {
            echo $this->render('_question', array('data' => $tq->question));
        }
        ?>
    </div>
    <div class="cbt"></div>
    <?= yii\widgets\LinkPager::widget(['pagination' => $pages]);?>
</div>
<div id="sidebar">
    <div class="module">
        <div class="summarycount al"><?php echo $pages->totalCount; ?></div>
        <?php if (isset($_GET['days'])): ?>
            <p>该标签的问题（最近<strong><?php echo Html::encode($_GET['days']); ?></strong>天）</p>
        <?php else: ?>
            <p>该标签的问题 </p>
            <?php endif; ?>
        <div class="tagged">
            <?php echo Html::a($tag->name, array('questions/tagged', 'tag' => $tag->name), array('rel' => 'tag', 'class' => "post-tag")); ?>
            <?php echo Html::a("关于 »", array('tags/view', 'tag' => $tag->name, 'op' => 'info'), array('rel' => 'tag', 'class' => "ml10", 'title' => '标签基本信息，统计，FAQ')); ?>
        </div>
    </div>
    <?php // $this->render('/common/_sidebar_adv',array('position'=>'questions.tagged.side.1'))?>
<?php echo $this->render('/common/_sidebar_tags'); ?>
</div>