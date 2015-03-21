<?php
use app\components\Formatter;
$currentUser = Yii::$app->user->identity;
if ($currentUser && $currentUser->checkPerm('setBounties') && !isset($data->openBounty)):?>
<div id="bounty-notification" class="row-space-top-3">
    <a title="显示/隐藏 悬赏发布窗口" class="bounty" id="bounty-link">发布悬赏</a>
    <div class="bounty" id="bounty">
        <?php
        $maxCount = min([intval($currentUser->reputation / 50), 10]);
        $range = range(50, $maxCount*50, 50);
        $items = array_combine($range,$range);
        ?>
        <p>威望<?= yii\helpers\Html::dropDownList('bounty-amount', 50, $items); ?></p>
        <input type="button" value="开始悬赏" id="bounty-start">
        <a style="margin-left: 20px;" href="<?php echo yii\helpers\Url::to('faq/index',array('#'=>'bounty'));?>">
            什么是悬赏?
        </a>
    </div>
</div>
<?php endif;?>
<?php if (isset($data->openBounty)):?>
		<div class="question-status bounty">
			<h2>
                该问题由<?php echo \yii\helpers\Html::a($data->openBounty->user->username,$data->openBounty->user->url);?>提供悬赏，<?php echo \yii\helpers\Html::a("赏金",array('faq/index',"#"=>'bounty'));?>
                <span class="bounty-award">+<?php echo $data->openBounty->amount;?></span>威望，
	        	<b title="开始 ：<?php echo Formatter::time($data->openBounty->time);?>结束：<?php echo Formatter::time($data->openBounty->endtime);?>"> 该悬赏<?php if ($data->openBounty->endtime>time()) echo "还有";?><?php echo Formatter::expire($data->openBounty->endtime);?>到期</b>.
	        </h2>
		</div>
<?php endif;?>
