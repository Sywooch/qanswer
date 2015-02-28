<div id="flag-popup-<?php echo $postid;?>" class="popup" style="">
	<div class="popup-close">
		<a title="关闭 ">
			&times;
		</a>
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
						<span class="action-name">
							提醒版主注意
						</span>
					</label>
					<div class="action-subform mod-attention-subform" style="display:block; margin:5px 0px; width:auto">
						<ul>
							<li>
								<label>
									<input type='radio' name='prefilled' value='not an answer' class='flag-prefilled'>
									<span>
										不是答案
									</span>
									<span class='action-desc'>
										This was posted as an answer, but it does not answer the question. It
										should possibly be an edit, a comment, another question, or deleted altogether.
									</span>
								</label>
							</li>
							<li>
								<label>
									<input type='radio' name='prefilled' value='low quality' class='flag-prefilled'>
									<span>
										低质量
									</span>
									<span class='action-desc'>
										This answer has serious formatting or content issues and might not be
										salvageable.
									</span>
								</label>
							</li>
							<li>
								<label>
									<input type='radio' name='prefilled' value='other'>
									其它
								</label>
								<div style="margin-left:18px">
									<textarea name="flag-reason" cols="80" rows="3" style="display:block">
									</textarea>
									<span class="edit-field-overlay">
										Something not quite right? Let us know about it, and please provide relevant
										links if possible.
									</span>
									<span class="text-counter">
									</span>
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
							This answer is effectively an advertisement with no disclosure. It is
							not useful or relevant, but promotional.
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
							This answer contains content that a reasonable person would consider offensive,
							abusive, or hate speech.
						</span>
					</label>
				</li>
			</ul>
		</div>
		<div class="popup-actions">
			<input type="hidden" name="fkey" value="334d9ebf6725615115d698b46eafa972">
			<div style="float:right">
				<span class="flag-remaining-inform" style="padding-right:20px">
					<span class="bounty-indicator-tab supernovabg" style="line-height:20px;"
					title="flags remaining today">
						10
					</span>
					inform moderator flags remaining
				</span>
				<span class="flag-remaining-spam" style="padding-right:20px; display:none;">
					还需要
					<span class="bounty-indicator-tab flagbg" style="line-height:20px;" title="flags remaining today">
						<?php echo (5-$flagcount);?>
					</span>
					个举报
				</span>
				<span class="spinner-container">
				</span>
				<input type="submit" class="popup-submit" style="float:none; margin-left:5px;"
				value="Flag Answer" disabled="disabled">
			</div>
			<div style="float:left; margin-top:18px;">
				<a class="popup-actions-cancel" href="javascript:void(0)">
					取消
				</a>
			</div>
		</div>
	</form>
</div>