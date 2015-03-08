<?php

use yii\helpers\Url;
use app\models\User;
use app\components\Formatter;
?>
<div class="row">
    <div class="col-md-3">
        <table>
            <tbody>
                <tr>
                <td id="user-avatar">
                    <img width="128" height="128" class="logo" alt="" src="<?php echo $user->bigavatar; ?>">
                </td>
                </tr>
                <tr>
                <td class="summaryinfo">
                    <a title="查看权限" href="<?php echo Url::to('privileges/index'); ?>"><span class="summarycount"><?php echo $user->reputation; ?></span></a>
                    <div style="margin-top: 5px; font-weight: bold;">威望</div>
                </td>
                </tr>
                <tr style="height: 30px;">
                <td style="vertical-align: bottom;" class="summaryinfo"><?php echo $user->stats->viewcount; ?> 次查看</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-5">
                <?php if ($user->id == \Yii::$app->user->id): ?>
                <?php echo yii\helpers\Html::a('编辑', ['user/edit', 'id' => $user->id], ['class' => 'pull-right']); ?>
                <?php endif; ?>
                <h2>
                    <?php echo $user->getUsergroupName(); ?>
                    <?php
                    if ($user->status == User::STATUS_NOACTIVE) {
                        echo ' - ' . '未激活';
                    } elseif ($user->status == User::STATUS_BANED) {
                        echo ' - ' . '禁止';
                    }
                    ?>
                </h2>
                <table class="user-details">
                    <tbody>
                        <tr>
                        <td style="width: 120px;">昵称</td>
                        <td class="fn nickname" style="width: 230px;"><b><?php echo $user->username; ?></b></td>
                        </tr>
                        <tr>
                        <td>注册</td>
                        <td>
                        <span title="<?php echo Formatter::time($user->registertime); ?>" class="cool"><?php echo Formatter::ago($user->registertime); ?></span>
                        </td>
                        </tr>
                        <tr>
                        <td>上次活动</td>
                        <td>
                        <span class="hot"><span class="relativetime" title="<?php echo Formatter::time($user->lastseen); ?>"><?php echo Formatter::ago($user->lastseen); ?></span></span>
                        </td>
                        </tr>

                        <tr>
                        <td>个人首页</td>
                        <td>
                            <div class="no-overflow">
                                <a class="url" href="<?php echo $user->profile->website; ?>"><?php echo $user->profile->website; ?></a>
                            </div>
                        </td>
                        </tr>
    <?php if (isset($this->me) && $user->id == $this->me->id): ?>
                            <tr>
                                <td>email</td>
                                <td>
                                    <a href="mailto:<?php echo $user->email; ?>" class="user-email"><?php echo $user->email; ?></a>
                                </td>
                            </tr>
    <?php endif; ?>
                        <tr>
                        <td>真实姓名</td>
                        <td class="label adr">
    <?php echo $user->profile->realname; ?>
                        </td>
                        </tr>
                        <tr>
                            <td>住在</td>
                            <td class="label adr">
                                <?php echo $user->profile->location; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>年龄</td>
                            <td>
                                <?php echo Formatter::age($user->profile->birthday); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
    </div>
    <div class="col-md-4">
        <div class="note" id="user-about-me">
            <?php echo $user->profile->aboutme; ?>
        </div>
    </div>
</div>
<div class="subheader">
    <div id="tabs">
        <a title="统计信息" href="#" class="youarehere">统计</a>
    </div>
</div>

<div id="questions-table" class="user-stats-table">
<?= $this->render('_stats-questions', array('questions' => $questions, 'pages' => $qPages, 'submenu' => $qSubmenu)) ?>
</div>

<div id="answers-table" class="user-stats-table">
<?= $this->render('_stats-answers', array('answers' => $answers, 'pages' => $aPages, 'submenu' => $aSubmenu)) ?>
</div>

<div class="row">
    <div class="col-md-12">
        <h2 class="summary-header text-center"><span class="summarycount"><?php echo $user->stats->upvotecount + $user->stats->downvotecount ?></span>&nbsp;<?php echo Yii::t('global', 'Votes'); ?></h2></td>
    </div>
    <div class="col-md-6">
        <div class="vote">
            <span class="vote-up-on"><?php echo Yii::t('global', 'vote up'); ?></span>
            <span title="赞成票总数" class="vote-count-post"><?php echo $user->stats->upvotecount; ?></span>
            <span style="cursor: default;" class="vote-down-off"></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="vote">
            <span style="cursor: default;" class="vote-up-off"><?php echo Yii::t('global', 'vote down'); ?></span>
            <span title="反对票总数" class="vote-count-post"><?php echo $user->stats->downvotecount ?></span>
            <span class="vote-down-on"></span>
        </div>
    </div>
</div>
    
<table id="tags-title" class="summary-title">
    <tbody>
        <tr>
        <td><div class="summarycount ar"><?php echo $tPages->totalCount; ?></div></td>
        <td class="summary-header"><h2>标签</h2></td>
        </tr>
    </tbody>
</table>

<div id="tags-table" class="user-stats-table">
<?php echo $this->render('_stats-tags', array('usertags' => $usertags, 'pages' => $tPages)); ?>
</div>

<table class="summary-title">
    <tbody>
        <tr>
        <td><div class="summarycount ar"><?php echo $user->badgeTotal; ?></div></td>
        <td class="summary-header"><h2>徽章</h2></td>
        </tr>
    </tbody>
</table>
<div class="user-stats-table">
    <table>
        <tbody>
            <tr>
            <td class="badge-col">
<?php foreach ($awards as $award): ?>
                    <a class="badge" title="<?php echo $award->badge->description; ?>" href="<?php echo Url::to("badges/view", array('id' => $award->badgeid, 'uid' => $award->uid)); ?>">
                        <span class="badge<?php echo $award->badge->type; ?>"></span>&nbsp;<?php echo $award->badge->name; ?></a>
                <span class="item-multiplier">×&nbsp;<?php echo $award->badgecount; ?></span><br>
<?php endforeach; ?>
            </td>
            </tr>
        </tbody>
    </table>
</div>