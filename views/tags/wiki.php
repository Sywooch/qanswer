<?php
use yii\helpers\Html;
?>
<script type="text/javascript">
$(function() {
    $("#wmd-input").wmd({
        "preview": true,
        "helpLink": "http://daringfireball.net/projects/markdown/",
        "helpHoverTitle": "Markdown Help",
    });
    $("#wmd-input").TextAreaResizer();
});
</script>
<div id="mainbar" class="ask-mainbar">
    <h1>编辑标签   <?php echo Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag'));?>	</h1>

   <?php if (!Yii::$app->user->identity->checkPerm('trustedUser') && !Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->isMod()):?>
	<div class="module newuser">
        <p>你没有 <a href="<?php echo Yii::$app->urlManager->createUrl('privileges/trustedUser');?>">标签编辑权限</a>. 你的编辑处于等待审核状态.</p>
    </div>
	<?php endif;?>

    <?php    
    $form = yii\bootstrap\ActiveForm::begin(['id'=>'post-form']);
    ?>
        <input type="hidden" value="<?php echo $tag->id;?>" id="tagid">

		<div id="post-editor">
			<textarea tabindex="101" rows="15" cols="92" name="content" id="wmd-input"><?php echo isset($post) ? $post->content : '';?></textarea>
		</div>

        <div class="form-item">
            <label>备注</label>
            <input type="text" value="" class="edit-field-overlayed" id="edit-comment" maxlength="300" size="60" name="Revision[comment]" tabindex="109" style="width: 660px; opacity: 0.3; z-index: 1; position: relative;">
            <span class="edit-field-overlay">briefly describe your changes (corrected spelling, fixed grammar, improved formatting)</span>
        </div>

        <div class="form-submit cbt">
        	<input type="hidden" tabindex="110" value="true" name="submit" />
        	<input type="submit" tabindex="110" value="提交" id="submit-button" class="ptr" />
        </div>
	<?php yii\bootstrap\ActiveForm::end(); ?>
</div>

<div style="width: 270px;" id="sidebar">
</div>