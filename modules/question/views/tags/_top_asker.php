<?php foreach($users as $item):?>
<tr>
	<td style="text-align: right; vertical-align: top;">
		<span class="top-count" style="color: rgb(204, 204, 204);" title="总分：<?php echo $item['scores'];?> "><?php echo $item['scores'];?></span>
	</td>
	<td style="text-align: right; vertical-align: top; padding-left: 10px;">
		<span class="top-count" title="<?php echo $item['questions'];?>个问题">
			<?php echo \yii\helpers\Html::a($item['questions'],array('search/index','q'=>'user:'.$item['id'].' type:q ['.$tag->name.']'));?>
		</span>
	</td>
	<td style="padding-left: 10px;">
		<div class="user-info">
			<div class="user-action-time"> </div>
			<?php $this->render('/common/_user32_array',array('user'=>$item));?>
		</div>
	</td>
</tr>
<?php endforeach;?>