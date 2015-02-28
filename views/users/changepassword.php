<?php $this->pageTitle="修改密码";?>

<div class="subheader">
	<h1>修改密码</h1>
</div>
<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo CHtml::errorSummary($form); ?>

	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password'); ?>
	<p class="hint">
	<?php echo "密码至少4个字符"; ?>
	</p>
	</div>

	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword'); ?>
	</div>


	<div class="row submit">
	<?php echo CHtml::submitButton("提交"); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->