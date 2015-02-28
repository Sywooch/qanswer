<div class="subheader">
	<h1>恢复密码</h1>
</div>


<?php if(Yii::app()->user->hasFlash('recoveryMessage')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('recoveryMessage'); ?>
</div>
<?php else: ?>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo CHtml::errorSummary($form); ?>

	<div class="row">
		<?php echo CHtml::activeLabel($form,'email'); ?>
		<?php echo CHtml::activeTextField($form,'email') ?>
		<p class="hint"><?php echo "请输入电子邮箱"; ?></p>
	</div>

	<div class="row submit">
		<?php echo CHtml::submitButton("提交"); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div>
<?php endif; ?>