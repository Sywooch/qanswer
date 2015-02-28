<div class="form">
<?php $form=$this->beginWidget('CActiveForm',array('id'=>'ask-form')); ?>
	<?php echo CHtml::errorSummary($model); ?>
	<div class="row">
		<div id="post-editor">
			<?php
				echo CHtml::activeTextArea($model,'content',array('rows'=>10, 'cols'=>92,'id'=>'wmd-input'));
				echo $form->error($model,'content');
			?>
			<?php if (!$model->isWiki() && $model->uid==Yii::app()->user->getId()):?>
			<div class="community-option">
				<?php echo CHtml::activeCheckbox($model,'wiki');?>
		        <span title="" for="communitymode"><?php echo Yii::t('global','community wiki');?></span>
		    </div>
		    <?php endif;?>
		    <br class="clear" />
		</div>

		<?php echo $form->error($model,'content'); ?>
	</div>
	<?php if (!$model->isNewRecord):?>
	<div class="row">
		<label for="Revision_comment">编辑原因</label>
		<textarea id="Revision_comment" name="Revision[comment]" cols="70" rows="3"></textarea>
	</div>
	<?php endif;?>
	<div class="row buttons">
		<?php echo CHtml::submitButton('保存'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/wmd.css');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.wmd.js');?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/autosave.js');?>
<script type="text/javascript">
$(function() {
    $("#wmd-input").wmd({
        "preview": true,
        "helpLink": "http://daringfireball.net/projects/markdown/",
        "helpHoverTitle": "Markdown Help",
    });
    $("#wmd-input").TextAreaResizer();
    bindTagFilterAutoComplete('#Post_tags');
    iAsk.navPrevention.init($('#wmd-input'));
	$("#ask-form").submit(function(){
		iAsk.navPrevention.stop();
	});
});
</script>