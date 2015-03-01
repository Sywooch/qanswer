<div id="close-question-popup" class="popup" style="width:690px;">
	<div class="popup-close">
		<a title="close this popup (or hit Esc)">
			&times;
		</a>
	</div>
	<script type="text/javascript">
		var questionId = 5241868;
		var reasonIdsWithSubPanes = [1, 2];
	</script>
	<div style="overflow:hidden">
		<form id="close-question-form" action="/posts/5241868/vote/6">
			<div id="pane-main" class="popup-active-pane">
				<h2>
					This question doesn't belong here because it is
				</h2>
				<ul class="action-list">
					<li>
						<input type="radio" id="close-1" name="close-main" value="1">
						<label for="close-1">
							<span class="action-name">
								exact duplicate
							</span>
							<span class="action-desc">
								This question covers exactly the same ground as earlier questions on this
								topic; its answers may be merged with another identical question.
							</span>
						</label>
					</li>
					<li>
						<input type="radio" id="close-2" name="close-main" value="2">
						<label for="close-2">
							<span class="action-name">
								off topic
							</span>
							<span class="action-desc">
								Questions on Stack Overflow are expected to generally relate to programming
								or software development in some way, within the scope defined in the
								<a href="http://stackoverflow.com/faq">
									faq
								</a>
								.
							</span>
						</label>
					</li>
					<li>
						<input type="radio" id="close-3" name="close-main" value="3">
						<label for="close-3">
							<span class="action-name">
								subjective and argumentative
							</span>
							<span class="action-desc">
								It's impossible to objectively answer this question; questions of this
								type are too open ended and usually lead to confrontation and argument.
							</span>
						</label>
					</li>
					<li>
						<input type="radio" id="close-4" name="close-main" value="4">
						<label for="close-4">
							<span class="action-name">
								not a real question
							</span>
							<span class="action-desc">
								It's difficult to tell what is being asked here. This question is ambiguous,
								vague, incomplete, overly broad, or rhetorical and cannot be reasonably
								answered in its current form.
							</span>
						</label>
					</li>
					<li>
						<input type="radio" id="close-7" name="close-main" value="7">
						<label for="close-7">
							<span class="action-name">
								too localized
							</span>
							<span class="action-desc">
								This question would only be relevant to a small geographic area, a specific
								moment in time, or an extraordinarily narrow situation that is not generally
								applicable to the worldwide audience of the internet.
							</span>
						</label>
					</li>
				</ul>
			</div>
			<div id="pane1" class="popup-subpane dno">
				<h2>
					This question is a duplicate of which other question?
				</h2>
				<input id="duplicate-question-id" type="hidden">
				<input id="duplicate-question" type="text" size="78" style="width:654px">
				<span class="edit-field-overlay">
					type or paste a question link or numeric question id
				</span>
				<div class="selected-master-preview">
				</div>
				<script type="text/javascript">
					function pane1() {
						$('#duplicate-question').focus();
					}
				</script>
			</div>
			<div id="pane2" class="popup-subpane dno">
				<h2>
					This question&hellip;
				</h2>
				<ul class="action-list">
					<li class="action-selected" style="margin-bottom:5px;">
						<input type="radio" id="close-offtopic" name="close-offtopic" value="2"
						checked="checked">
						<label for="close-offtopic">
							<span class="action-name">
								is off topic
							</span>
							<span class="action-desc">
								Questions on Stack Overflow are expected to generally relate to programming
								or software development in some way, within the scope defined in the
								<a href="http://stackoverflow.com/faq">
									faq
								</a>
								.
							</span>
						</label>
					</li>
					<li>
						<table class="close-offtopic-sites">
							<tr>
								<td>
									<input type="radio" id="close-offtopic-4" name="close-offtopic" value="4">
								</td>
								<td>
									<label for="close-offtopic-4">
										<img class="cp" style="width:58px; height:58px;" src="http://meta.stackoverflow.com/apple-touch-icon.png">
									</label>
								</td>
								<td>
									<label for="close-offtopic-4">
										<span class="action-name">
											belongs on
											<a href="http://meta.stackoverflow.com/faq" target="_blank">
												meta.stackoverflow.com
											</a>
										</span>
										<span class="action-desc" style="margin-left:0;">
											Q&A for the Stack Exchange engine powering these sites
										</span>
									</label>
								</td>
							</tr>
						</table>
					</li>
					<li>
						<table class="close-offtopic-sites">
							<tr>
								<td>
									<input type="radio" id="close-offtopic-2" name="close-offtopic" value="2">
								</td>
								<td>
									<label for="close-offtopic-2">
										<img class="cp" style="width:58px; height:58px;" src="http://serverfault.com/apple-touch-icon.png">
									</label>
								</td>
								<td>
									<label for="close-offtopic-2">
										<span class="action-name">
											belongs on
											<a href="http://serverfault.com/faq" target="_blank">
												serverfault.com
											</a>
										</span>
										<span class="action-desc" style="margin-left:0;">
											Q&A for system administrators and desktop support professionals
										</span>
									</label>
								</td>
							</tr>
						</table>
					</li>
					<li>
						<table class="close-offtopic-sites">
							<tr>
								<td>
									<input type="radio" id="close-offtopic-3" name="close-offtopic" value="3">
								</td>
								<td>
									<label for="close-offtopic-3">
										<img class="cp" style="width:58px; height:58px;" src="http://superuser.com/apple-touch-icon.png">
									</label>
								</td>
								<td>
									<label for="close-offtopic-3">
										<span class="action-name">
											belongs on
											<a href="http://superuser.com/faq" target="_blank">
												superuser.com
											</a>
										</span>
										<span class="action-desc" style="margin-left:0;">
											Q&A for computer enthusiasts and power users
										</span>
									</label>
								</td>
							</tr>
						</table>
					</li>
					<li>
						<table class="close-offtopic-sites">
							<tr>
								<td>
									<input type="radio" id="close-offtopic-45" name="close-offtopic" value="45">
								</td>
								<td>
									<label for="close-offtopic-45">
										<img class="cp" style="width:58px; height:58px;" src="http://webmasters.stackexchange.com/apple-touch-icon.png">
									</label>
								</td>
								<td>
									<label for="close-offtopic-45">
										<span class="action-name">
											belongs on
											<a href="http://webmasters.stackexchange.com/faq" target="_blank">
												webmasters.stackexchange.com
											</a>
										</span>
										<span class="action-desc" style="margin-left:0;">
											Q&A for pro webmasters
										</span>
									</label>
								</td>
							</tr>
						</table>
					</li>
					<li>
						<table class="close-offtopic-sites">
							<tr>
								<td>
									<input type="radio" id="close-offtopic-131" name="close-offtopic" value="131">
								</td>
								<td>
									<label for="close-offtopic-131">
										<img class="cp" style="width:58px; height:58px;" src="http://programmers.stackexchange.com/apple-touch-icon.png">
									</label>
								</td>
								<td>
									<label for="close-offtopic-131">
										<span class="action-name">
											belongs on
											<a href="http://programmers.stackexchange.com/faq" target="_blank">
												programmers.stackexchange.com
											</a>
										</span>
										<span class="action-desc" style="margin-left:0;">
											Q&A for expert programmers interested in professional discussions on software
											development
										</span>
									</label>
								</td>
							</tr>
						</table>
					</li>
				</ul>
				<script type="text/javascript">
					function pane2() {
						$('#close-question-popup .popup-submit').enable($('#pane2 input[type=radio]:checked').length);
					}
				</script>
			</div>
			<div class="popup-actions">
				<input type="hidden" name="fkey" value="334d9ebf6725615115d698b46eafa972">
				<input type="hidden" name="isForFlagging" value="true">
				<div style="float:left; margin-top:18px;">
					<a class="popup-actions-cancel" href="javascript:void(0)">
						cancel
					</a>
				</div>
				<div style="float:right">
					<span id="remaining-votes" style="padding-right:30px;">
					</span>
					<input type="submit" class="popup-submit" style="float:none; margin-left:5px;"
					value="Flag Question" disabled="disabled">
				</div>
			</div>
		</form>
	</div>
</div>