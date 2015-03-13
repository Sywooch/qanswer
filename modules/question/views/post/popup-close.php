    <form id="close-question-form" action="<?php echo Yii::$app->urlManager->createUrl(['/question/post/vote', 'postid'=>$question->id, 'type'=>6]);?>">
        <div id="pane-main" class="popup-active-pane">
            <h2>关闭原因</h2>
            <ul class="action-list">
                <li>
                    <input type="radio" id="close-1" name="close-main" value="1">
                    <label for="close-1">
                        <span class="action-name">完全重复</span>
                        <span class="action-desc">这个问题和先前的问题主题完全一样，答案可以从其他相同的问题中获得</span>
                    </label>
                </li>
                <li>
                    <input type="radio" id="close-2" name="close-main" value="2">
                    <label for="close-2">
                        <span class="action-name">偏离主题</span>
                        <span class="action-desc">我们希望在"<?php echo Yii::$app->params['sitename']?>"上的问题在faq中所规定的范围中，也就是说一定程度上和编程或者软件相关</span>
                    </label>
                </li>
                <li>
                    <input type="radio" id="close-3" name="close-main" value="3">
                    <label for="close-3">
                        <span class="action-name">过于主观或争议性</span>
                        <span class="action-desc">很难客观回答该问题，此类问题过于开放一直通常导致对抗和争论.</span>
                    </label>
                </li>
                <li>
                    <input type="radio" id="close-4" name="close-main" value="4">
                    <label for="close-4">
                        <span class="action-name">不是问题</span>
                        <span class="action-desc">很难界定到底在问什么，该问题模糊，不完整，过于宽泛或者修饰过多，在当前情况下没法得恰当的答案.</span>
                    </label>
                </li>

                <li>
                    <input type="radio" id="close-7" name="close-main" value="7">
                    <label for="close-7">
                        <span class="action-name">过于地域化</span>
                        <span class="action-desc">问题可能和一个小地方，特定时刻或是过于狭隘的环境有关，无法应用于世界范围内的网络用户.</span>
                    </label>
                </li>
            </ul>
        </div>

        <div id="pane1" class="popup-subpane dno">
            <h2>该问题和另一个问题重复</h2>
            <input id="duplicate-question-id" type="hidden">
            <input id="duplicate-question" type="text" size="78">
            <span class="edit-field-overlay">重复问题的地址或ID</span>
            <div class="selected-master-preview"></div>

            <script type="text/javascript">
            function pane1() {
                $('#duplicate-question').focus();
            }
            </script>
        </div>

        <div class="popup-actions">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam;?>" value="<?= Yii::$app->request->csrfToken;?>">
            <div style="float:left; margin-top:18px;">
                <a class="popup-actions-cancel" href="javascript:void(0)">取消</a>
            </div>
            <div style="float:right">
                <span id="remaining-votes" style="padding-right:30px;">
                    还需要
                    <span class="bounty-indicator-tab" style="margin-left:3px; line-height:20px;" title="">&nbsp;<?php echo (yii::$app->params['posts']['maxCloseVotes'] - $question->poststate->closecount);?>&nbsp;</span>
                    票才能关闭该问题
                </span>
                <input type="submit" class="popup-submit" style="float:none; margin-left:5px;" value="提交" disabled="disabled">
            </div>
        </div>
    </form>