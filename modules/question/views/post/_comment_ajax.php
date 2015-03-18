<?php
use yii\helpers\Html;
use app\components\Formatter;
?>
<?php foreach ($comments as $i => $comment): ?>
    <?php
    if ($i >= 5 && !\Yii::$app->request->isAjax) {
        break;
    }
    ?>
    <tr id="comment-<?php echo $comment->id; ?>" class="comment">
        <td class="comment-actions">
            <table>
                <tbody>
                    <tr>
                        <td class="comment-score">
                        <?php if ($comment->upvotes > 0): ?>
                            <span class="cool" title="<?php echo Yii::t('posts', "number of 'great comment' votes received"); ?>"><?php echo $comment->upvotes; ?></span>
                        <?php else: ?>
                            <span>&nbsp;</span>
                        <?php endif; ?>
                        </td>
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <td>
                            <?php if ($comment->myvotes > 0): ?>
                                <div title="<?php echo Yii::t('posts', "you've voted for this as a great comment"); ?>" class="comment-up-on">up voted</div>
                            <?php else: ?>
                                <a title="<?php echo Yii::t('posts', 'this is a great comment'); ?>" class="comment-up comment-up-off" style="visibility: hidden;">up vote</a>
                                <?= 
                                    Html::a(
                                        Yii::t('posts', 'this is a great comment'),
                                        ['/question/post/comments', 'id' => $comment->id, 'op' => 'vote', 'typeid' => 2],
                                        ['class' => 'comment-up comment-up-off', 'style' =>"visibility: hidden;"]
                                    ); 
                                ?>
                        <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </td>

        <td class="comment-text">
            <div>
                <span class="comment-copy"><?php echo $comment->message; ?></span> &ndash;&nbsp;
                <?php echo Html::a($comment->commentauthor->username, $comment->commentauthor->getUrl(), ['class' => 'comment-user', 'title' => $comment->commentauthor->reputation . " 威望"]); ?>
                <span class="comment-date">
                    <span title="<?php echo Formatter::time($comment->time); ?>"><?php echo Formatter::ago($comment->time); ?></span>
                </span>&nbsp;
                <?php if (Yii::$app->user->identity && $comment->isNotTimeout() && $comment->isself()): ?>
                    <a class="comment-edit">编辑</a>&nbsp;
                    <span class="comment-delete delete-tag glyphicon glyphicon-remove-circle" style="visibility: hidden;" title="删除该条评论"></span>
                <?php endif; ?>
            </div>
            <form action="<?= \yii\helpers\Url::to(['/question/post/comments', 'id' => $comment->id]); ?>" id="edit-comment-<?php echo $comment->id; ?>" class="dno">
                <div class="dno"><?php echo $comment->message; ?></div>
            </form>
        </td>
    </tr>
<?php endforeach;