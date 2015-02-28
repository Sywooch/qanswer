<td>
	<div class="user-info">
		<div class="user-gravatar48">
            <a href="<?php echo yii\helpers\Url::to('users/view',array('id'=>$user->id));?>">
				<img width="48" height="48" class="logo" alt="" src="<?php echo $user->middleavatar;?>">
			</a>
		</div>
		<div class="user-details">
			<?php echo \yii\helpers\Html::a($user->username, array('users/view','id'=>$user->id),array('title'=>$user->username));?>
			<br />
			<span class="user-location"><?php echo $user->profile->location;?></span>
			<br />
			<?php
			switch($params['filter']) {
				case 'month':
					echo $user->stats->monthvotes;
					break;
				case 'quarter':
					echo $user->stats->quartervotes;
					break;
				case 'year':
					echo $user->stats->yearvotes;
					break;
				case 'all':
					echo $user->stats->upvotecount+$user->stats->downvotecount;
					break;
				case 'week':
				default:
					echo $user->stats->weekvotes;
					break;

			}
			echo '次投票';
			?>
		</div>
	</div>
</td>