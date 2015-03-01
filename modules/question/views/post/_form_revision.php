<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/wmd.css');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.wmd.js');?>
<script type="text/javascript">
$(function() {
    $("#wmd-input").wmd({
        "preview": true,
        "helpLink": "http://daringfireball.net/projects/markdown/",
        "helpHoverTitle": "Markdown Help",
    });
    $("#wmd-input").TextAreaResizer();
    bindTagFilterAutoComplete('#Post_tags');
});
</script>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>

	<p class="note"><span class="required">*</span> 必填.</p>

	<?php echo CHtml::errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>80,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<div id="post-editor">
			<?php
			if (!$model->isNewRecord) {
				echo CHtml::activeTextArea($model->lastrevision,'text',array('rows'=>10, 'cols'=>92,'id'=>'wmd-input'));
				echo $form->error($model,'text');
			} else {
				echo CHtml::activeTextArea($model,'content',array('rows'=>10, 'cols'=>92,'id'=>'wmd-input'));
				echo $form->error($model,'content');
			}
			?>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php echo $form->textField($model,'tags',array('size'=>60,'maxlength'=>128,'autocomplete'=>"off",'class'=>'ac_input')); ?>
		<p class="hint">用空格分隔标签</p>
		<?php echo $form->error($model,'tags'); ?>
	</div>

	<?php if (!$model->isNewRecord):?>
	<div class="row">
		<label for="Revision_comment">编辑原因</label>
		<textarea id="Revision_comment" name="Revision[comment]" cols="70" rows="3"></textarea>
	</div>
	<?php endif;?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '提交' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->