<div class="user-gravatar32">
    <a href="<?= Yii::$app->urlManager->createUrl(['users/view','id'=>$user['id']]);?>">
		<img width="32" height="32" class="logo" alt="" src="<?php //echo User::model()->getGavatar('small',$user['id']);?>">
	</a>
</div>
<div class="user-details">
	<?= \yii\helpers\Html::a($user['username'], ['users/view','id'=>$user['id']],['title'=>$user['username']]);?>
	<br />
	<span title="威望" class="reputation-score"><?php echo $user['reputation'];?></span>
	<?php
	if ($user['gold'] >= 0) {
		echo '<span title="'.$user['gold'].' 金徽章 "><span class="badge3"></span><span class="badgecount">'.$user['gold'].'</span></span>';
	}
	if ($user['silver']>=0) {
		echo '<span title="'.$user['silver'].' 银徽章 "><span class="badge2"></span><span class="badgecount">'.$user['silver'].'</span></span>';
	}
	if ($user['bronze']>=0) {
		echo '<span title="'.$user['bronze'].' 铜徽章 "><span class="badge1"></span><span class="badgecount">'.$user['bronze'].'</span></span>';
	}
	?>
</div>