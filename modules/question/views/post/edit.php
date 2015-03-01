<h1>编辑 <i><?php echo $model->isQuestion() ? \yii\helpers\Html::encode($model->title) : '答案'; ?></i></h1>

<?php if ($model->isAnswer()):?>
	<div id="mainbar">
		<?php echo $this->render('_answerform', array('model'=>$model));?>
	</div>
<?php else:?>
	<div id="mainbar" class="ask-mainbar">
		<?php echo $this->render('_form', array('model'=>$model)); ?>
		<script type="text/javascript">
		$(function(){
			initFadingHelpText();
			moveScroller();
		});
		</script>
	</div>
	<?php $this->render('_ask-sidebar');?>
<?php endif;?>