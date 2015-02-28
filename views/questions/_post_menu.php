<?php
use yii\helpers\Html;

?>
	<?php echo Html::a("编辑", array('post/edit','id'=>$data->id),array('title'=>"编辑"));?>
	<span class="lsep">|</span>
	<a title="举报或提醒版主注意" id="flag-post-<?php echo $data->id;?>">举报</a>

<?php 
$currentUser = Yii::$app->user->identity;
if ($currentUser->isAdmin() || $currentUser->isMod()
		|| $currentUser->checkPerm('closeQuestions')
		|| ($currentUser->checkPerm('closeMyQuestions') && $data->isSelf())):?>
		<span class="lsep">|</span>
	<?php if ($data->poststate->isClose()):?>
		<a title="投票重新打开" id="close-question-<?php echo $data->id;?>">
   		重新打开<?php echo ($data->poststate->closecount>0) ? '('.$data->poststate->closecount.')' : "";?>
		</a>
	<?php else:?>
		<a title="投票关闭" id="close-question-<?php echo $data->id;?>">
		关闭	<?php echo ($data->poststate->closecount>0) ? '('.$data->poststate->closecount.')' : "";?>
		</a>
	<?php endif;?>
<?php endif;?>

	<span class="lsep">|</span>
	<?php
		$action = ($data->poststate->isDelete()) ?  "恢复" : "删除";
		$action = $action.($data->poststate->deletecount>0 ? "({$data->poststate->deletecount})" : "");
		echo Html::a($action, array('post/delete','id'=>$data->id),array('class'=>'delete','id'=>"delete-post-".$data->id,'title'=>$action));
	?>

	<?php
	if ($currentUser && ($currentUser->checkPerm('protect') || $currentUser->isAdmin() || $currentUser->isMod())) {
		echo '<span class="lsep">|</span>';
		if ($data->poststate->isProtect()) {
			echo Html::a("解除保护", array('post/unprotect','id'=>$data->id),array('class'=>'delete','id'=>"unprotect-post-".$data->id,'title'=>"解除保护"));
		} else {
			echo Html::a("保护", array('post/protect','id'=>$data->id),array('class'=>'delete','id'=>"protect-post-".$data->id,'title'=>"保护"));
		}
	}
	?>

	<?php
	if ($currentUser && $currentUser->isAdmin()) {
		echo '<span class="lsep">|</span>';
		if ($data->poststate->isLock()) {
			echo Html::a("解锁", array('post/unlock','id'=>$data->id),array('class'=>'delete','id'=>"unlock-post-".$data->id,'title'=>"解锁"));
		} else {
			echo Html::a("锁定", array('post/lock','id'=>$data->id),array('class'=>'delete','id'=>"lock-post-".$data->id,'title'=>"锁定"));
		}
	}
	?>
