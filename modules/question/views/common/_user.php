<div class="user-gravatar32">
    <a href="<?php echo yii\helpers\Url::to($user->getUrl());?>">
		<img width="32" height="32" class="logo" alt="" src="<?php echo $user->smallavatar;?>">
	</a>
</div>
<div class="user-details">
	<?php echo \yii\helpers\Html::a($user->username, $user->getUrl(), ['title'=>$user->username]);?>
	<br />
	<span title="威望" class="reputation-score"><?php echo $user->reputation;?></span>
	<?php
	if ($user->gold >= 0) {
		echo '<span title="'.$user->gold.' 金徽章 "><span class="badge1"></span><span class="badgecount">'.$user->gold.'</span></span>';
	}
	if ($user->silver>=0) {
		echo '<span title="'.$user->silver.' 银徽章 "><span class="badge2"></span><span class="badgecount">'.$user->silver.'</span></span>';
	}
	if ($user->bronze>=0) {
		echo '<span title="'.$user->bronze.' 铜徽章 "><span class="badge3"></span><span class="badgecount">'.$user->bronze.'</span></span>';
	}
	?>
</div>