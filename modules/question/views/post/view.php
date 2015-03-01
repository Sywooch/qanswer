<?php
$this->pageTitle=$model->title;
?>

<?php $this->renderPartial('_view', array(
	'data'=>$model,
)); ?>

<div id="comments">
	<?php if($model->commentcount>=0): ?>
		<h3>
			<?php echo $model->commentcount>1 ? $model->commentcount . ' comments' : 'One comment'; ?>
		</h3>

		<?php $this->renderPartial('_answers',array(
			'post'=>$model,
			'answers'=>$model->answers,
		)); ?>
	<?php endif; ?>

	<h3>Leave a Comment</h3>

	<?php if(Yii::app()->user->hasFlash('commentSubmitted')): ?>
		<div class="flash-success">
			<?php echo Yii::app()->user->getFlash('commentSubmitted'); ?>
		</div>
	<?php else: ?>
		<?php $this->renderPartial('/questions/_answerform',array(
			'model'=>$comment,
		)); ?>
	<?php endif; ?>

</div><!-- comments -->
