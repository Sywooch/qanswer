<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php echo Html::a("编辑", array('post/edit','id'=>$data->id),array('title'=>"编辑"));?>
<?= Html::a("举报",'#',['data'=>['toggle'=>'modal','target'=>'#post-menu-dialog', 'href'=> Url::to(['/question/post/popup','do'=>'flag','postid'=>$data->id])] , 'title' => '举报或提醒版主注意']); ?>

<?php $currentUser = Yii::$app->user->identity;?>

<?php
    $action = ($data->poststate->isDelete()) ?  "恢复" : "删除";
    $action = $action.($data->poststate->deletecount>0 ? "({$data->poststate->deletecount})" : "");
    echo Html::a($action, ['/question/post/delete','id'=>$data->id], ['class'=>'delete','id'=>"delete-post-".$data->id,'title'=>$action]);
?>

<?php
if ($currentUser && ($currentUser->checkPerm('protect') || $currentUser->isAdmin() || $currentUser->isMod())) {
    if ($data->poststate->isProtect()) {
        echo Html::a("解除保护", ['/question/post/unprotect','id'=>$data->id],['class'=>'delete','id'=>"unprotect-post-".$data->id,'title'=>"解除保护"]);
    } else {
        echo Html::a("保护", ['/question/post/protect','id'=>$data->id],['class'=>'delete','id'=>"protect-post-".$data->id,'title'=>"保护"]);
    }
}
?>

<?php
if ($currentUser && $currentUser->isAdmin()) {
    if ($data->poststate->isLock()) {
        echo Html::a("解锁", ['/question/post/unlock','id'=>$data->id], ['class'=>'delete','id'=>"unlock-post-".$data->id,'title'=>"解锁"]);
    } else {
        echo Html::a("锁定", ['/question/post/lock','id'=>$data->id], ['class'=>'delete','id'=>"lock-post-".$data->id,'title'=>"锁定"]);
    }
}
?>