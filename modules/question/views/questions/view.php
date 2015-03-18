<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Formatter;

\app\assets\EditorAsset::register($this);
$this->title = $model->title;
?>
<script type="text/javascript">
$(function(){
    iAsk.question.init({
        hasOpenBounty:<?php if (Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,
        canOpenBounty:<?php if (Yii::$app->controller->me && Yii::$app->controller->me->checkPerm('setBounties') && !Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,            
    });
    styleCode();
});
 </script>
<div id="question-header">
    <h1 class="title">
        <?= Html::a($model->formattedTitle, $model->url, array('class' => "question-hypera"));?>
    </h1>
</div>
<div class="row">
    <div id="mainbar" class="col-md-9">
        <div id="question">
            <?php echo $this->render('_view', ['data' => $model]); ?>
        </div>

        <?= $this->render('_question_status', ['model' => $model]);?>

        <div id="answers">
            <a name="tab-top"></a>
            <div id="answers-header">
                <div class="subheader answers-subheader">
                    <h2><?php echo $model->answercount; ?>个回答</h2>
                    <?php
                    $submenu = array(
                        'items' => array(
                            array('label' => '活跃', 'url' => $model->url . '?tab=activity#tab-top', 'options' => array('title' => '按活跃度排序')),
                            array('label' => '时间', 'url' => $model->url . '?tab=oldest#tab-top', 'options' => array('title' => '按时间排序')),
                            array('label' => '投票', 'url' => $model->url . '?tab=votes#tab-top', 'options' => array('title' => '按投票排序')),
                        ),
                        'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
                    );
                    switch ($tab) {
                        case 'activity':
                            $submenu['items'][0]['options']['class'] = 'active';
                            break;
                        case 'oldest':
                            $submenu['items'][1]['options']['class'] = 'active';
                            break;
                        case 'votes':
                            $submenu['items'][2]['options']['class'] = 'active';
                            break;
                    }
                    echo \yii\widgets\Menu::widget($submenu);
                    ?>
                </div>
            </div>

            <?php
            foreach ($answers as $ans) {
                if (!$ans->poststate->isDelete() || ($this->me && ($this->me->isAdmin() || $this->me->isMod() || $ans->isSelf() || $this->me->checkPerm('moderatorTools')))) {
                    echo $this->render('_answer', array('data' => $ans, 'question' => $model));
                }
            }
            ?>
            <?php
            echo yii\widgets\LinkPager::widget(['pagination' => $pages]);
            ?>
        </div>

        <?php if ($answer != null && !$model->checkExistAnswer()): ?>
            <div id="post-form">
                <h2 class="space">我来回答</h2>
                    <?php if (Yii::$app->session->hasFlash('commentSubmitted')): ?>
                    <div class="flash-success">
                    <?php echo Yii::$app->session->getFlash('commentSubmitted'); ?>
                    </div>
                    <?php else: ?>
                    <div id="post-editor">
                        <?=
                        $this->render('/questions/_answerform', [
                            'model' => $answer,
                            'question' => $model,
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
                <script type="text/javascript">
                    /*
                    $(function() {
                        editormd("editor-textarea", {
                            path : '<?= Yii::$app->assetManager->getPublishedUrl("@bower/editor.md"); ?>/lib/', // codemirror、marked等依赖的库的路径
                            height: 300
                        });
                    });
                    */
                </script>
            </div>
    <?php endif; ?>
    </div>
    <div id="sidebar" class="col-md-3">
        <div class="module">
            <p class="label-key">标签</p>
            <div class="tagged">
                <?php foreach ($tags as $tag): ?>
                    <?php echo Html::a($tag->name, array('questions/tagged', 'tag' => $tag->name), array('class' => "post-tag")); ?>
                    &nbsp;<span class="item-multiplier">×&nbsp;<?php echo $tag->frequency; ?></span>
                    <br/>
                <?php endforeach; ?>
                <br>
            </div>
            <div id="qinfo">
                <p><span class="label-key">提问于</span><span title="<?php echo Formatter::time($model->createtime); ?>" class="label-value"><?php echo Formatter::ago($model->createtime); ?></span></p>
                <p><span class="label-key">查看</span><span class="label-value"><?php echo $model->viewcount; ?> 次</span></p>
                <p><span class="label-key">最后活动</span><span class="label-value"><?php echo Formatter::ago($model->activity); ?></span></p>			
            </div>
        </div>
        <div class="module">
            <h4 id="h-related">相关问题</h4>
            <div class="related">
                <?php foreach ($relatedQuestions as $q): ?>
                <div class="spacer">
                <?= Html::a(Html::encode($q->title), $q->url, array('class' => 'question-hyperlink')); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>