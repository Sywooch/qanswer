<div id="flag-popup-<?php echo $post->id;?>" class="popup">
	<div class="popup-close">
		<a title="关闭 ">&times;</a>
	</div>
	<form>
		<div>
			<h2 style="margin-bottom:12px;">
				举报该帖子，原因：
			</h2>
			<ul class="action-list">
				<li class="action-selected">
					<input type="radio" id="flag--1" name="flag-post" value="-1" checked>
					<label for="flag--1">
						<span class="action-name">提醒版主注意</span>
					</label>
					<div class="action-subform mod-attention-subform" style="display:block; margin:5px 0px; width:auto">
						<ul>
							<?php if ($post->isAnswer()):?>
							<li>
								<label>
									<input type='radio' name='prefilled' value='不是答案' class='flag-prefilled'>
									<span>不是答案</span>
								</label>
							</li>
							<?php endif;?>
							<li>
								<label>
									<input type='radio' name='prefilled' value='低质量' class='flag-prefilled'>
									<span>低质量</span>
								</label>
							</li>
							<li>
								<label>
									<input type='radio' name='prefilled' value='other' />其它
								</label>
								<div style="margin-left:18px">
									<textarea name="flag-reason" cols="80" rows="3" style="display:block"></textarea>
									<span class="edit-field-overlay">有其它原因？如能提供相链接就更好了.</span>
									<span class="text-counter"></span>
								</div>
							</li>
						</ul>
					</div>
				</li>
				<li>
					<input type="radio" id="flag-12" name="flag-post" value="12">
					<label for="flag-12">
						<span class="action-name">
							垃圾信息
						</span>
						<span class="action-desc">
							广告、不相关内容、垃圾
						</span>
					</label>
				</li>
				<li>
					<input type="radio" id="flag-4" name="flag-post" value="4">
					<label for="flag-4">
						<span class="action-name">
							 不受欢迎
						</span>
						<span class="action-desc">
							本帖包含攻击，辱骂或仇恨言论
						</span>
					</label>
				</li>
			</ul>
		</div>
		<div class="popup-actions">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam;?>" value="<?= Yii::$app->request->csrfToken;?>">
			<div style="float:right">
				<span class="flag-remaining-inform" style="padding-right:20px">
					<span class="bounty-indicator-tab supernovabg" style="line-height:20px;" title="今天剩余'提醒版主注意'次数">
						<?php echo $remaining;?>
					</span>今天剩余"提醒版主注意"次数
				</span>
				<span class="flag-remaining-spam" style="padding-right:20px; display:none;">
					<span class="bounty-indicator-tab flagbg" style="line-height:20px;" title="今天剩余’垃圾帖子举报‘次数">
						<?php echo $spamRemaining;?>
					</span>今天剩余"垃圾帖子举报"次数
				</span>
				<span class="spinner-container">
				</span>
				<input type="submit" class="popup-submit" style="float:none; margin-left:5px;" value="举报" disabled="disabled">
			</div>
			<div style="float:left; margin-top:18px;">
				<a class="popup-actions-cancel" href="javascript:void(0)">
					<?php echo Yii::t('global','cancel');?>
				</a>
			</div>
		</div>
	</form>
</div>