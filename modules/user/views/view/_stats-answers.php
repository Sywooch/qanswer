<?php
use yii\helpers\Html;
?>
<a name="answers"></a>
<table style="margin-left: -50px; width: 100%;" class="summary-title">
    <tbody>
    	<tr>
	        <td>
	            <div class="summarycount ar"><?php echo $pages->totalCount;?></div>
	        </td>
	        <td class="summary-header">
	            <h2>回答</h2>
	        </td>
			<td style="width: 100%;">
                <?= \yii\widgets\Menu::widget($submenu); ?>
			</td>
		</tr>
	</tbody>
</table>

<?php foreach ($answers as $answer):?>
<div class="answer-summary">
	<div class="answer-votes default" title="该答案得票数量" onclick="window.location.href='<?php echo $answer->question->url.'#'.$answer->id;?>'"><?php echo $answer->score;?></div>
	<div class="answer-link">
		<?php echo Html::a(Html::encode($answer->question->title), $answer->question->url.'#'.$answer->id,array('class'=>"answer-hyperlink"));?>
	</div>
</div>
<?php endforeach;?>
<?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'answer-pager', 'class' => 'pagination']]);?>
<script type="text/javascript">
    var answersPageSize = <?php echo Yii::$app->params['pages']['userAnswerPagesize'];?>;
    var answersSortOrder = 'votes';
</script>