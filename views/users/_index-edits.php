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
			$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
			switch($filter) {
				case 'month':
					echo $user->stats->monthedits;
					break;
				case 'quarter':
					echo $user->stats->quarteredits;
					break;
				case 'year':
					echo $user->stats->yearedits;
					break;
				case 'all':
					echo $user->stats->editcount;
					break;
				case 'week':
				default:
					echo $user->stats->weekedits;
					break;

			}
			echo '次编辑';
			?>
		</div>
	</div>
</td>