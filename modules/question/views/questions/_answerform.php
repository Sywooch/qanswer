<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<?php $form =  ActiveForm::begin(array('id'=>'ask-form')); ?>
    <div id="editor-textarea">
        <?= Html::activeTextArea($model,'content',array('rows'=>10, 'cols'=>92,'id'=>'editor-input', 'data-postid' => $question->id)); ?>
    </div>
    <div class="community-option">
        <?php echo Html::activeCheckbox($model,'wiki');?>
        <label title="将答案标记为社区wiki贴其目的是通过降低威望要求达到鼓励编辑之目的，当然在答案成为wiki贴之后向上投票你也不会得到威望值。该过程不可逆" for="communitymode"><?php echo Yii::t('global','community wiki');?></label>
    </div>
    <div class="draft-saved community-option" id="draft-saved">
        草稿已保存
    </div>
    <?php echo Html::error($model,'content'); ?>
	<div class="buttons">
		<?php echo Html::submitButton($model->isNewRecord ? '提交' : '保存' , ['class' => 'btn btn-primary']); ?>
	</div>
<?php ActiveForm::end();?>
