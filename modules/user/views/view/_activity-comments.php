<?php
use app\components\Formatter;
?>
<tr>
	<td style="width: 70px;">
		<div class="date">
			<div title="<?php echo Formatter::time($activity->time);?>" class="date_brick"><?php echo Formatter::month($activity->time);?></div>
		</div>
	</td>
	<td><?php echo $activity->cntype;?></td>
	<td>
		<b>
			<?php echo \yii\helpers\Html::a(\yii\helpers\Html::encode($activity->data['qtitle']), $activity->url,array('class'=>'question-hyperlink timeline-answers','title'=>$activity->data['qtitle']));?>
		</b>
		<br>
		<span class="comments"><?php echo $activity->data['summary'];?></span>
	</td>
</tr>