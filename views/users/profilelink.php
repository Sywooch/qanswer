<div>
	<table class="profile-recent-summary">
		<thead>
			<tr>
				<th class="profile-table-col1">
				</th>
				<th>今天	</th>
				<th>本周	</th>
				<th>当月	</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
                    <a href="<?php echo yii\helpers\Url::to('users/view',array('id'=>$userStatics['uid'],'tab'=>'reputation'));?>">
						威望
					</a>
				</td>
				<?php foreach ($userStatics['reputations'] as $date=>$rep):?>
				<td>
                    <a href="<?php echo yii\helpers\Url::to('users/view',array('id'=>$userStatics['uid'],'tab'=>'reputation','startDate'=>$rep['day']));?>">
						<?php echo $rep['rep'];?>
					</a>
				</td>
				<?php endforeach;?>
			</tr>
			<tr>
				<td>
					<?php echo CHtml::link('收藏',array('users/view','id'=>$userStatics['uid'],'tab'=>'favorites','sort'=>'recent'));?>
				</td>
				<td title="一天有<?php echo $userStatics['favs']['today']; ?>回答（收藏问题)">
					<?php echo $userStatics['favs']['today']; ?>
				</td>
				<td title="一周有<?php echo $userStatics['favs']['week']; ?>回答（收藏问题)">
					<?php echo $userStatics['favs']['week']; ?>
				</td>
				<td title="一月有<?php echo $userStatics['favs']['month']; ?>回答（收藏问题)">
					<?php echo $userStatics['favs']['month']; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>