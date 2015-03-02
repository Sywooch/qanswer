<?php
use yii\helpers\Html;
?>
<?php if (!Yii::$app->user->isGuest):?>
    <div id="interesting-tags" class="module">
        <h4 id="h-interesting-tags">收藏标签</h4>
        <div id="interestingTags">
        	<?php
        	foreach(Yii::$app->user->identity->profile->preference as $tag) {
				echo Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
				echo " ";
        	}
			?>
        </div>
        <table>
            <tbody>
	            <tr>
	            	<td class="vt"><input type="text" name="interestingTag" id="interestingTag" autocomplete="off" class="ac_input"></td>
	            	<td class="vt"><input type="button" value="添加" id="interestingAdd"></td>
	            </tr>
        	</tbody>
        </table>
        <h4 id="h-ignored-tags">忽略标签</h4>
        <div id="ignoredTags">
			<?php
        	foreach(Yii::$app->user->identity->profile->unpreference as $tag) {
				echo Html::a($tag, array('questions/tagged','tag'=>$tag),array('class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"));
        		echo " ";
        	}
			?>
        </div>
        <table>
            <tbody>
	            <tr>
		            <td class="vt"><input type="text" name="ignoredTag" id="ignoredTag" autocomplete="off" class="ac_input"></td>
		            <td class="vt"><input type="button" value="添加" id="ignoredAdd"></td>
	            </tr>
			</tbody>
		</table>
        <div class="dno">
            <input type="checkbox" title="hide ignored tags" id="hideIgnored"><label for="hideIgnored"> hide ignored tags</label>
            <?= Html::a("保存链接", ['/user/user/savepreference'], ['class'=>'hidden', 'id' => 'user-save-preference']); ?>
        </div>
	</div>
	<script type="text/javascript">
	$(function() {
		iAsk.tags.applyPrefs(true, []);
	});
	</script>
	<?php endif;?>