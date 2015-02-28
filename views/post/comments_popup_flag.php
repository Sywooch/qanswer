<div id="flag-popup-<?php echo $comment->id;?>" class="popup">
    <div class="popup-close"><a title="<?php echo Yii::t('global','close this popup (or hit Esc)');?>">&times;</a></div>
    <form action="post">
        <h2 style="margin-bottom:12px;">举报评论</h2>
        <div><input id="comment-popup-rude" type="radio" name="comment-reason" value="粗鲁或有攻击性言论"><label for="comment-popup-rude"> 粗鲁或有攻击性言论</label></div>
        <div><input id="comment-popup-not-constructive" type="radio" name="comment-reason" value="非建设性的/偏题"><label for="comment-popup-not-constructive"> 非建设性的/偏题</label></div>
        <div><input id="comment-popup-obsolete" type="radio" name="comment-reason" value="过时的"><label for="comment-popup-obsolete"> 过时的</label></div>
        <div><input id="comment-popup-chatty" type="radio" name="comment-reason" value="太水了"><label for="comment-popup-chatty"> 太水了</label></div>
        <div><input id="comment-popup-other" type="radio" name="comment-reason" value="其它"><label for="comment-popup-other"> 其它&hellip;</label><br />
        <textarea id="comment-popup-other-text" name="other-text" rows="5" cols="30" style="display: none;"></textarea>
        </div>
        <div class="popup-actions">
            <div style="float:right">
                <span class="flag-remaining-spam">
					还需要
					<span class="bounty-indicator-tab flagbg" style="line-height:20px;" title="flags remaining today">
						<?php echo (Yii::$app->params['posts']['maxFlagVotes']-$flagcount);?>
					</span>个举报
                </span>
                <span class="spinner-container"></span>
                <input type="submit" class="popup-submit" style="float:none; margin-left:5px;" value="<?php echo Yii::t('global','Flag Comment');?>" disabled="disabled">
            </div>
            <div style="float:left; margin-top:12px;">
                <a class="popup-actions-cancel" href="javascript:void(0)"><?php echo Yii::t('global','cancel');?></a>
            </div>
        </div>
    </form>
</div>