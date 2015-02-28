<?php foreach($answers as $answer): ?>
<div class="comment" id="a<?php echo $answer->id; ?>">

	<?/*php echo CHtml::link("#{$answer->id}", $answer->getUrl($post), array(
		'class'=>'cid',
		'title'=>'Permalink to this comment',
	));*/ ?>

	<div class="author">
		<?php /*echo $answer->authorLink; */?> says:
	</div>

	<div class="time">
		<?php echo date('F j, Y \a\t h:i a',$answer->createtime); ?>
	</div>

	<div class="content">
		<?php echo nl2br(CHtml::encode($answer->content)); ?>
	</div>

</div><!-- comment -->
<?php endforeach; ?>