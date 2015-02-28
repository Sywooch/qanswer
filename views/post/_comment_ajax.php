<?php
use yii\helpers\Html;
use app\components\Formatter;
?>
<?php foreach ($comments as $i => $comment): ?>
    <?php
    if ($i >= 5 && $ajax === false) {
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
                        <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    </tr>
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <?php if (!($comment->myvotes > 0)): ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td><a title="举报" class="comment-flag flag-off" style="visibility: hidden;">flag</a></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </td>

        <td class="comment-text">
            <div>
                <span class="comment-copy"><?php echo $comment->message; ?></span> &ndash;&nbsp;
                <?php echo Html::a($comment->commentauthor->username, array('users/view', 'id' => $comment->commentauthor->id), array('class' => 'comment-user', 'title' => $comment->commentauthor->reputation . " 威望")); ?>
                <span class="comment-date">
                    <span title="<?php echo Formatter::time($comment->time); ?>"><?php echo Formatter::ago($comment->time); ?></span>
                </span>&nbsp;
                <?php if (Yii::$app->user->identity && $comment->isNotTimeout() && $comment->isself()): ?>
                    <a class="comment-edit">编辑</a>&nbsp;
                    <span class="comment-delete delete-tag" style="visibility: hidden;" title="删除该条评论"></span>
                <?php endif; ?>
            </div>
            <form id="edit-comment-<?php echo $comment->id; ?>" class="dno">
                <div class="dno"><?php echo $comment->message; ?></div>
            </form>
        </td>
    </tr>
<?php endforeach;