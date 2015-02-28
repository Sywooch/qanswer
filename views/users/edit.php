<?php // Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/wmd.css');?>
<?php // Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.wmd.js');?>
<?php
use yii\bootstrap\ActiveForm;
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
<div id="mainbar-full">
	<div class="subheader">
		<h1 id="edit-title"><?php echo $user->username;?> - 修改</h1>
	</div>

	<table width="100%" id="user-edit-table">
		<tbody>
			<tr>
				<td style="vertical-align: top; text-align: center; padding: 20px; width: 128px;">
					<img width="128" height="128" class="logo" alt="" src="<?php echo $user->bigavatar;?>">
					<div>
						&nbsp;
					</div>
					<p style="font-size: 200%; font-weight: bold;">
                        <a title="上传头像" href="<?php echo yii\helpers\Url::to('users/avatar');?>">
							更新头像
						</a>
					</p>
					<div style="color: rgb(119, 119, 119);" id="gravatar-info">
					</div>
				</td>
				<td style="vertical-align: top;">
					<h2>注册用户</h2>
                    <?php $form = ActiveForm::begin(['id'=>"user-edit-form",'layout' => 'horizontal']);?>
                            <?php echo $form->field($user,'username')->textInput(['size'=>80,'maxlength'=>30,'style'=>'width:260px;','tabindex'=>'1']); ?>
                            <?php echo $form->field($profile,'realname')->textInput(['size'=>80,'maxlength'=>30,'style'=>'width:260px;','tabindex'=>'7']); ?>
                            <?php echo $form->field($profile,'website')->textInput(['maxlength'=>200,'style'=>'width:260px;','tabindex'=>'8']); ?>
                            <?php echo $form->field($profile,'location')->textInput(['maxlength'=>100,'style'=>'width:260px;','tabindex'=>'9']); ?>
                            <?php echo $form->field($profile,'birthday')->textInput(['maxlength'=>100,'style'=>'width:260px;','tabindex'=>'10']); ?>
                            <div id="post-editor">
                                <?php echo $form->field($profile,'aboutme')->textarea(['rows'=>15,'cols'=>92,'tabindex'=>'11','id'=>'wmd-input']); ?>
                            </div>
                            <div class="form-submit">
                                <?php echo Html::submitButton('保存',array('id'=>"submit-button",'tagindex'=>'12')); ?>
                                <?php echo Html::button('取消',array('tagindex'=>'13','onclick'=>"location.href='".yii\helpers\Url::to('users/view',array('id'=>$user->id))."'")); ?>
                            </div>
					<?php ActiveForm::end(); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>