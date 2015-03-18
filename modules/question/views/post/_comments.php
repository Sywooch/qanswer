<?php
use yii\helpers\Html;
?>
<div class="comments" id="comments-<?php echo $data->id;?>">
	<table>
		<tbody>
        	<?php echo $this->render('/post/_comment_ajax', ['comments'=>$data->comments]);?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td class="comment-form">
                    <form action="<?= \yii\helpers\Url::to(['/question/post/view', 'id' => $data->id, 'op' => 'comments']); ?>" id="add-comment-<?php echo $data->id;?>"></form>
                </td>
			</tr>
		</tfoot>
	</table>
</div>
<?php if ($data->commentcount>5):?>
    <?= 
    Html::a(
        "显示全部<strong>{$data->commentcount}</strong> 评论",
        ['/question/post/view','id' => $data->id, 'op' => 'comments'],
        ['class' => 'comments-link', 'id' => 'comments-link-'.$data->id]
    ); 
    ?>
<?php else:?>
    <?php
    $currentUser = Yii::$app->user->identity;
    if ($currentUser && ($currentUser->checkPerm('comment') || $currentUser->id==$data->uid || $data->isAsker())) {
        echo Html::a(
            '添加评论', 
            ['/question/post/view', 'id' => $data->id, 'op' => 'comments'], 
            ['class' => 'comments-link', 'id' => 'comments-link-'.$data->id]); 
    }
    ?>
<?php endif;?>