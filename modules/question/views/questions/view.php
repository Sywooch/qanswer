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
        questionId:22,
        editCommentUrl:	"<?php echo Url::to('/post/comments');?>",
        addCommentUrl:	"<?php echo Url::to('/post/view');?>",
        voteCommentUrl:	"<?php echo Url::to('/post/comments');?>",
        moreCommentsUrl:"<?php echo Url::to('/post/view');?>",
        hasOpenBounty:<?php if (Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,
        canOpenBounty:<?php if (Yii::$app->controller->me && Yii::$app->controller->me->checkPerm('setBounties') && !Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,            
    });
    styleCode();
});
 </script>
<div id="question-header">
    <h1 class="title">
        <?php
        $title = Html::encode($model->title);
        if ($model->poststate->isClose()) {
            $title .= "&nbsp;[关闭]";
        }
        if ($model->poststate->isDelete()) {
            $title .= "&nbsp;[删除]";
        }
        echo Html::a($title, $model->url, array('class' => "question-hypera"));
        ?>
    </h1>
</div>
<div class="row">
    <div id="mainbar" class="col-md-9">
        <div id="question">
            <?php echo $this->render('_view', ['data' => $model]); ?>
        </div>

        <?php if ($model->poststate->isLock()): ?>
        <div class="question-status">
            <h2>
                <?php if ($model->poststate->lockuid == 0): ?>
                    <span>社区管理员</span>
                <?php else: ?>
                    <?php echo Html::a($model->poststate->author->username, array('users/view', 'id' => $model->poststate->author->id)); ?>
                    <span title="版主" class="mod-flair">♦</span>
                <?php endif; ?>
                <b>锁定</b>于
                <span class="relativetime" title="<?php echo Formatter::time($model->poststate->locktime); ?>"><?php echo Formatter::ago($model->poststate->locktime); ?></span>
            </h2>
            <p/>
        </div>
        <?php endif; ?>

        <?php if ($model->poststate->isProtect()): ?>
        <div class="question-status">
            <h2>
                <?= Html::a($model->poststate->protectauthor->username, array('users/view', 'id' => $model->poststate->protectauthor->id)); ?>
                <?php if ($model->poststate->protectauthor->isMod()): ?><span title="moderator" class="mod-flair">♦</span><?php endif; ?>设置问答保护
                <span class="relativetime" title="<?php echo Formatter::ago($model->poststate->protecttime); ?>"><?php echo Formatter::ago($model->poststate->protecttime); ?></span>
            </h2>
            <p>该问题被设置问答保护，禁止用"顶","谢谢"等回答，禁止垃圾答案，至少有10点威望才能回答该问题</p>
        </div>
        <?php endif; ?>

        <?php if ($model->poststate->close == 1): ?>
        <div class="question-status">
            <h2>
                <?php foreach ($model->closeuids as $u): ?>
                    <?= Html::a($u->user->username, array('users/view', 'id' => $u->user->id)); ?>
                    <?php if ($u->user->isMod()): ?><span title="moderator" class="mod-flair">♦</span><?php endif; ?>
                <?php endforeach; ?>
                关闭，因为：<?= PostMod::$closeReason[$model->poststate->closereason]['name']; ?>
                <span class="relativetime" title="<?php echo Formatter::ago($model->poststate->closetime); ?>"><?php echo Formatter::ago($model->poststate->closetime); ?></span>
            </h2>
            <p><?= PostMod::$closeReason[$model->poststate->closereason]['desc']; ?></p>
        </div>
        <?php endif; ?>

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
                    $tab = (!isset($_GET['tab'])) ? 'activity' : $_GET['tab'];
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
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
                <script type="text/javascript">
                    $(function() {
                        editormd("editor-textarea", {
                            path : '<?= Yii::$app->assetManager->getPublishedUrl("@bower/editor.md"); ?>/lib/', // codemirror、marked等依赖的库的路径
                            height: 300
                        });
                    });
                </script>
                <script type="text/javascript">
                    $(function () {
                        iAsk.editor.init("<?php echo yii\helpers\Url::to('post/heartbeat'); ?>", 'answer');
                        iAsk.navPrevention.init($('#wmd-input'));
                    });
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
        <?php $this->render('/common/_sidebar_adv', array('position' => 'questions.view.side.1')) ?>
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