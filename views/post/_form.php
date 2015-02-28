<?php // Yii::$app->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/wmd.css');?>
<?php // Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.wmd.js');?>
<?php // Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/autosave.js');?>
<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<script type="text/javascript">
$(function() {
    $("#wmd-input").wmd({
        "preview": true,
        "helpLink": "http://daringfireball.net/projects/markdown/",
        "helpHoverTitle": "Markdown Help",
    });
    $("#wmd-input").TextAreaResizer();
    bindTagFilterAutoComplete('#Post_tags');
    <?php if (isset($type) && $type=='ask'):?>
    iAsk.editor.init("<?php echo yii\helpers\Url::to('post/heartbeat');?>",'ask');
    <?php endif;?>
    iAsk.navPrevention.init($('#wmd-input'));
	$("#ask-form").submit(function(){
		iAsk.navPrevention.stop();
	});
});
</script>
<div class="form">
	<?php
	$form = ActiveForm::begin([
		'id'=>'ask-form',
		'enableClientValidation'=>true,
	]);
	?>

	<p class="note"><span class="required">*</span> 必填.</p>


    <?= $form->field($model, 'title')->textInput(['size'=>80,'maxlength'=>128]);?>

    <div id="post-editor" class="form-group">
        <?php
            echo Html::activeTextArea($model,'content',array('rows'=>10, 'cols'=>92,'id'=>'wmd-input'));
        ?>
    </div>
    <div class="draft-saved community-option" id="draft-saved">
    草稿已保存
    </div>
    <?= $form->field($model, 'tags')->textInput(['size'=>80,'maxlength'=>128]);?>
    <p class="hint">用空格分隔标签</p>

	<?php if (!$model->isNewRecord):?>
	<div class="row">
		<label for="Revision_comment">编辑原因</label>
		<textarea id="Revision_comment" name="Revision[comment]" cols="70" rows="3"></textarea>
	</div>
	<?php endif;?>
    <?php echo Html::submitButton($model->isNewRecord ? '提交' : '保存', ['class'=>'btn btn-default']); ?>

<?php ActiveForm::end(); ?>

</div>