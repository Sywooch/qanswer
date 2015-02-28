<td>
    <div class="user-info">
        <div class="user-gravatar48">
            <a href="<?php echo yii\helpers\Url::to('users/view', array('id' => $user->id)); ?>">
                <img width="48" height="48" class="logo" alt="" src="<?php echo $user->middleavatar; ?>">
            </a>
        </div>
        <div class="user-details">
            <?php echo \yii\helpers\Html::a($user->username, array('users/view', 'id' => $user->id), array('title' => $user->username)); ?>
            <br />
            <span class="user-location"><?php echo $user->profile->location; ?></span>
            <br />
            <span title="<?php echo '总威望'; ?>" class="reputation-score"><?php echo $user->reputation; ?></span>
        </div>
    </div>
</td>