<?php
use app\components\Formatter;
$currentUser = Yii::$app->user->identity;
if ($currentUser && $currentUser->checkPerm('setBounties') && !isset($data->openBounty)):?>
<tr id="bounty-notification">
	<td class="votecell">
	</td>
	<td>
		<div style="margin-top: 20px;">
			<a title="显示/隐藏 悬赏发布窗口" class="bounty" id="bounty-link">
				发布悬赏
			</a>
			<div style="display: none; padding: 10px 5px; line-height: 150%; color: rgb(85, 85, 85);" class="bounty" id="bounty">
				<p style="font-size: 120%;">
					威望
					<?php
					$nums = intval($currentUser->reputation / 50);
					if ($nums > 10) $nums = 10;
					?>
					<select name="bounty-amount" id="bounty-amount">
						<?php for($i=1;$i<$nums+1;$i++):?>
						<option value="<?php echo $i*50;?>">
							<?php echo $i*50;?>
						</option>
						<?php endfor;?>
					</select>
				</p>
				<input type="button" value="开始悬赏" id="bounty-start">
                <a style="margin-left: 20px;" href="<?php echo yii\helpers\Url::to('faq/index',array('#'=>'bounty'));?>">
					什么是悬赏?
				</a>
			</div>
		</div>
	</td>
</tr>
<?php endif;?>
<?php if (isset($data->openBounty)):?>
<tr>
	<td colspan="2">
		<div class="question-status bounty">
			<h2>
                该问题由<?php echo \yii\helpers\Html::a($data->openBounty->user->username,$data->openBounty->user->url);?>提供悬赏，<?php echo \yii\helpers\Html::a("赏金",array('faq/index',"#"=>'bounty'));?><span class="bounty-award">+<?php echo $data->openBounty->amount;?></span>威望，
	        	<b title="开始 ：<?php echo Formatter::time($data->openBounty->time);?>结束：<?php echo Formatter::time($data->openBounty->endtime);?>"> 该悬赏<?php if ($data->openBounty->endtime>time()) echo "还有";?><?php echo Formatter::expire($data->openBounty->endtime);?>到期</b>.
	        </h2>
		</div>
	</td>
</tr>
<?php endif;?>
