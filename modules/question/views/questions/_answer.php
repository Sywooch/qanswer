<?php 
use app\models\Post;
use app\models\Vote;
use yii\helpers\Html;
use app\components\Formatter;
?>
<a name="<?php echo $data->id;?>"></a>
<div class="answer<?php if ($data->poststate->isDelete()) { echo " deleted-answer";} ?>" id="answer-<?php echo $data->id;?>">
    <div class="votecell pull-left">
        <div class="vote">
            <input type="hidden" value="<?php echo $data->id;?>" />
            <a title="<?php echo Yii::t('global','vote up');?>" class="vote-up-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::UPVOTE) echo " vote-up-on";?>"><?php echo Yii::t('global','vote up');?></a>
            <span style="font-size: 200%;" class="vote-count-post"><?php echo $data->score;?></span>
            <a title="<?php echo Yii::t('global','vote down');?>" class="vote-down-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::DOWNVOTE) echo " vote-down-on";?>"><?php echo Yii::t('global','vote down');?></a>


            <?php if ($data->accepted == Post::ACCEPTED):?>
                <?php if (!Yii::$app->user->isGuest && $question->isSelf()):?>
                <a class="vote-accepted-off vote-accepted-on" id="vote-accepted-<?php echo $data->id;?>">取消为最佳答案</a>
                <?php else:?>
                <span class="vote-accepted-on" id="vote-accepted-<?php echo $data->id;?>">已采纳</span>
                <?php endif;?>
            <?php else:?>
                <?php if (!Yii::$app->user->isGuest && $question->isSelf()):?>
                <a class="vote-accepted-off" id="vote-accepted-<?php echo $data->id;?>">采纳为最佳答案</a>
                <?php endif;?>
            <?php endif;?>

            <?php if (isset($question->closeBounty[$data->id]) && count($question->closeBounty[$data->id])>0):?>
            <?php foreach($question->closeBounty[$data->id] as $bounty):?>
            <div class="bounty-award-container">
                <span title="该答案由<?php if ($bounty->status==Bounty::STATUS_SYS) echo "系统自动"; else echo $bounty->user->username;?>授予了<?php echo $bounty->bonus;?>威望的奖励" class="bounty-award">+<?php echo $bounty->bonus;?></span>
            </div>
            <?php endforeach;?>
            <?php endif;?>

            <?php if (isset($question->openBounty) && $question->openBounty->isMine()):?>
            <div class="bounty-award-container">
                <a class="bounty-vote bounty-vote-off bounty-award" href="#">+<?php echo $question->openBounty->amount;?></a>
            </div>
            <?php endif;?>
        </div>
    </div>
    <div class="post-cell">
        <div class="post-text">
            <?php
            echo $data->content;
            ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="post-menu">
                    <?php
                    if (!Yii::$app->user->isGuest) {
                        $this->render('_answer_menu',array('data'=>$data));
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-3">
                <?php if ($data->lastedit>0):?>
                <div class="post-signature">
                    <div class="user-info">
                        <div class="user-action-time">编辑于
                        <?php echo Html::a(Html::tag('span',Formatter::ago($data->lastedit),array('class'=>'relativetime')), array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
                        </div>
                        <div class="user-gravatar32"></div>
                        <div class="user-details">
                            <br>
                        </div>
                    </div>
                </div>
                <?php endif;?>
            </div>
            <div class="col-md-3">
                <div class="post-signature<?php if ($data->author->id==Yii::$app->user->getId()) echo " owner";?>">
                    <?php if (!$data->isWiki()):?>
                    <div class="user-info">
                        <div class="user-action-time">回答于
                             <span class="relativetime" title="<?php echo date("Y-m-d H:i:s",$data->createtime); ?>"><?php echo Formatter::ago($data->createtime);?></span>
                        </div>
                        <?php $this->render('/common/_user',array('user'=>$data->author));?>
                        <?php if ($data->revCount>1):?>
                        <div class="user-details">
                        <?php echo Html::a($data->revCount."个版本", array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
                        </div>
                        <?php endif;?>
                    </div>
                    <?php else:?>
                        <?php $this->render('/common/_user_wiki',array('data'=>$data));?>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php $this->render('/post/_comments',array('data'=>$data));?>
    </div>
</div>