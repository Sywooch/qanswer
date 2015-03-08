<?php

use yii\helpers\Html;
use app\components\Formatter;
?>
<a name="questions"></a>
<table class="summary-title">
    <tr>
        <td>
            <span class="summarycount ar"><?= $pages->totalCount; ?></span>
        </td>
        <td class="summary-header">
            <h2>问题</h2>
        </td>
        <td style="width:100%">
        <?= \yii\widgets\Menu::widget($submenu); ?>
        </td>
    </tr>
</table>
<?php foreach ($questions as $question): ?>

    <?php if ($question->favcount > 0): ?>
    <div title="该问题有<?php echo $question->favcount; ?>用户收藏" class="favorites-count-off">
        <div class="star-off"></div>
        <b><?php echo $question->favcount; ?></b>
    </div>
    <?php else: ?>
    <div class="favorite-cell">
        &nbsp;
    </div>
    <?php endif; ?>
    <div class="question-summary narrow" id="question-summary-<?php echo $question->id; ?>">
        <div onclick="window.location.href = '<?php echo $question->url; ?>'" class="cp">
            <div class="votes">
                <div class="mini-counts"><?php echo $question->score; ?></div>
                <div>票</div>
            </div>
            <div class="status <?php if ($question->accepted) echo 'answered-accepted'; elseif ($question->answercount > 0) echo 'answered'; else echo 'unanswered'; ?>">
                <div class="mini-counts"><?php echo $question->answercount; ?></div>
                <div>回答</div>
            </div>
            <div class="views">
                <div class="mini-counts"><?php echo Formatter::view($question->viewcount); ?></div>
                <div>查看</div>
            </div>
        </div>
        <div class="summary">
            <h3>
                <?php echo Html::a(Html::encode($question->title), $question->url, array('class' => 'question-hyperlink', 'title' => Html::encode($question->title))); ?>
            </h3>
            <div class="tags t-authenticode">
                <?php
                foreach (explode(' ', $question->tags) as $tag) {
                    echo Html::a($tag, array('questions/tagged', 'tag' => $tag), array('class' => 'post-tag', 'rel' => 'tag', 'title' => "显示标签 '$tag'"));
                }
                ?>
            </div>
            <div class="started">
                <?php echo Html::a(Html::tag('span', Formatter::ago($question->activity), array('class' => 'relativetime', 'title' => Formatter::time($question->activity))), array('questions/view', 'id' => $question->id), array('class' => 'started-link')); ?>
                <?php echo Html::a(Html::encode($question->author->username), array('users/view', 'id' => $question->author->id)); ?>
                <span title="威望值" class="reputation-score"><?php echo $question->author->reputation; ?></span>
            </div>
        </div>
    </div>
    <br class="cbt">
<?php endforeach; ?>
<?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'question-pager', 'class' => 'pagination']]); ?>
<?php ?>
<script type="text/javascript">
    var questionsPageSize = <?php echo Yii::$app->params['pages']['userQuestionPagesize']; ?>;
    var questionsSortOrder = 'recent';
</script>