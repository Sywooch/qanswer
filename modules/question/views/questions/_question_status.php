<?php

use yii\helpers\Html;
use app\components\Formatter;

?>
<?php if ($model->poststate->isLock()): ?>
<div class="question-status">
    <h2>
        <?php if ($model->poststate->lockuid == 0): ?>
            <span>社区管理员</span>
        <?php else: ?>
            <?php echo Html::a($model->poststate->author->username, $model->poststate->author->getUrl()); ?>
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
        <?= Html::a($model->poststate->protectauthor->username, $model->poststate->protectauthor->getUrl()); ?>
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
            <?= Html::a($u->user->username, $u->user->getUrl()); ?>
            <?php if ($u->user->isMod()): ?><span title="moderator" class="mod-flair">♦</span><?php endif; ?>
        <?php endforeach; ?>
        关闭，因为：<?= PostMod::$closeReason[$model->poststate->closereason]['name']; ?>
        <span class="relativetime" title="<?php echo Formatter::ago($model->poststate->closetime); ?>"><?php echo Formatter::ago($model->poststate->closetime); ?></span>
    </h2>
    <p><?= PostMod::$closeReason[$model->poststate->closereason]['desc']; ?></p>
</div>
<?php endif; ?>