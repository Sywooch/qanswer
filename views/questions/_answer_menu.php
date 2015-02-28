<?php echo yii\helpers\Html::a("编辑", array('post/edit','id'=>$data->id),array('title'=>"编辑"));?>
<span class="lsep">|</span>
<a title="举报或提醒版主注意" id="flag-post-<?php echo $data->id;?>">举报</a>

<?php
$currentUser = Yii::$app->user->identity;
if ($data->isSelf() || $currentUser->isAdmin() || $currentUser->isMod() || $currentUser->checkPerm('moderatorTools')) {
	echo '<span class="lsep">|</span>';
	$action = ($data->poststate->isDelete()) ?  "恢复" : "删除";
	$action = $action.($data->poststate->deletecount>0 ? "({$data->poststate->deletecount})" : "");
	echo yii\helpers\Html::a($action, array('post/delete','id'=>$data->id),array('class'=>'delete','id'=>"delete-post-".$data->id,'title'=>$action));
}
?>