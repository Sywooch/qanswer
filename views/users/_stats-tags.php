<?php
use yii\helpers\Html;
?>
<table>
	<tbody>
		<tr>
			<td class="wide-tag-col">
			<?php $count = count($usertags);?>
			<?php foreach ($usertags as $i=>$usertag):?>
				<?php
				echo Html::a($usertag->tag, array('questions/tagged','tag'=>$usertag->tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 ".$usertag->tag));
				?>
				<span class="item-multiplier">×&nbsp;<?php echo $usertag->totalcount;?></span>
				<br>
				<?php if (($i+1)%3==0 && ($count-$i)>=3):?>
					</td><td class="wide-tag-col">
				<?php endif;?>
			<?php endforeach;?>
			</td>
		</tr>
	</tbody>
</table>

<?php 
//$this->widget('MLinkPager', array(
//    'pages' 	=> $pages,
//	'cssFile'	=> false,
//	'id'		=> 'tags-pager',
//	'htmlOptions'=>array('class'=>"pages fr")
//))
?>
<script type="text/javascript">
  var tagsPageSize = <?php echo Yii::$app->params['pages']['userTagsPagesize'];?>;
</script>