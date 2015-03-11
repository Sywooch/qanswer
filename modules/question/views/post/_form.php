<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<script type="text/javascript">
$(function() {
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
	<?php
	$form = ActiveForm::begin([
		'id'=>'ask-form',
		'enableClientValidation'=>true,
	]);
	?>
    <?= $form->field($model, 'title')->textInput(['size'=>80,'maxlength'=>128]);?>
    <?= $form->field($model, 'content')->textarea(['id' => 'wmd-input']);?>
    <div class="draft-saved community-option" id="draft-saved">草稿已保存</div>
    <?= $form->field($model, 'tags')->textInput(['placeholder' => '用空格分隔标签']);?>

	<?php if (!$model->isNewRecord):?>
        <?= $form->field($model, 'editComment')->textarea();?>
	<?php endif;?>
    <?= Html::submitButton($model->isNewRecord ? '提交' : '保存', ['class'=>'btn btn-default']); ?>

<?php ActiveForm::end(); ?>