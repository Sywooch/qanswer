<div class="comments" id="comments-<?php echo $data->id;?>">
	<table>
		<tbody>
        	<?php echo $this->render('/post/_comment_ajax',array('comments'=>$data->comments,'ajax'=>false));?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td class="comment-form"><form id="add-comment-<?php echo $data->id;?>"></form></td>
			</tr>
		</tfoot>
	</table>
	</div>
<?php if ($data->commentcount>5):?>
<a title="" class="comments-link" id="comments-link-<?php echo $data->id;?>">显示全部 <b><?php echo $data->commentcount;?></b> 评论</a>
<?php else:?>
<?php
$currentUser = Yii::$app->user->identity;
if ($currentUser && ($currentUser->checkPerm('comment') || $currentUser->id==$data->uid || $data->isAsker())):?>
<a title="添加评论" class="comments-link" id="comments-link-<?php echo $data->id;?>">添加评论</a>
<?php endif;?>
<?php endif;?>