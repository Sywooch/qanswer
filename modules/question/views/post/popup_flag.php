<form action="<?= yii\helpers\Url::to(['/question/post/vote','postid' => $post->id, 'type' => 12]); ?>">
    <h2>举报该帖子，原因：</h2>
    <ul class="action-list">
        <li class="action-selected">
            <input type="radio" id="flag--1" name="flag-post" value="-1" checked>
            <label for="flag--1">
                <span class="action-name">提醒版主注意</span>
            </label>
            <div class="action-subform">
                <textarea name="flag-reason" cols="80" rows="3" placeholder="原因？"></textarea>
                <span class="text-counter"></span>
            </div>
        </li>
        <li>
            <input type="radio" id="flag-12" name="flag-post" value="12">
            <label for="flag-12">垃圾信息 (广告、营销等)</label>
        </li>
        <li>
            <input type="radio" id="flag-4" name="flag-post" value="4">
            <label for="flag-4">不友好言论 (本帖包含攻击，辱骂或仇恨言论)</label>
        </li>
    </ul>
    <div class="popup-actions">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam;?>" value="<?= Yii::$app->request->csrfToken;?>">
        <div>
            <span class="flag-remaining-spam">
                <span class="bounty-indicator-tab flagbg" title="今天剩余’垃圾帖子举报‘次数">
                    <?php echo $spamRemaining;?>
                </span>今天剩余"垃圾帖子举报"次数
            </span>
            <span class="spinner-container"></span>
        </div>
    </div>
</form>