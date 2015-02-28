<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<td>
	<div class="user-info">
		<div class="user-gravatar48">
			<a href="<?php echo Url::to('users/view',array('id'=>$user->id));?>">
				<img width="48" height="48" class="logo" alt="" src="<?php echo $user->middleavatar;?>">
			</a>
		</div>
		<div class="user-details">
			<?php echo Html::a($user->username, array('users/view','id'=>$user->id),array('title'=>$user->username));?>
			<br />
			<span class="user-location"><?php echo $user->profile->location;?></span>
			<br />
			<?php
				$rep = $user->reputation;
				$title = '总威望';
			?>
			<?php
			switch($params['filter']) {
				case 'month':
					$rep = $user->stats->monthreps;
					$title = '本月获取威望';
					break;
				case 'quarter':
					$rep = $user->stats->quarterreps;
					$title = '本季度获取威望';
					break;
				case 'year':
					$rep = $user->stats->yearreps;
					$title = '今年获取威望';
					break;
				case 'all':
					$rep = $user->reputation;
					$title = '总威望';
					break;
				case 'week':
				default:
					$rep = $user->stats->weekreps;
					$title = '本周获取威望';
					break;

			}
			?>
			<span title="<?php echo $title;?>" class="reputation-score"><?php echo $rep;?></span>
		</div>
	</div>
</td>