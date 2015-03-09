<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = \app\modules\user\Module::t('user', 'Update user account');
?>
<?= $this->render('/alert');?>
<div id="mainbar-full">
    
	<div class="subheader">
		<h1 id="edit-title"><?php echo $user->username;?> - 修改</h1>
	</div>
    <div class="row" id="user-edit-table">
        <div class="col-md-8">
            <?php $form = ActiveForm::begin(['id'=>"user-edit-form",'layout' => 'horizontal']);?>
                <?php echo $form->field($user,'username')->textInput(['maxlength'=>30]); ?>
                <?php echo $form->field($profile,'realname')->textInput(['maxlength'=>30]); ?>
                <?php echo $form->field($profile,'website')->textInput(['maxlength'=>200]); ?>
                <?php echo $form->field($profile,'location')->textInput(['maxlength'=>100]); ?>
                <?php echo $form->field($profile,'birthday')->textInput(['maxlength'=>100]); ?>
                <div id="post-editor">
                    <?php echo $form->field($profile,'aboutme')->textarea(['rows'=>8,'id'=>'wmd-input']); ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-6 col-sm-offset-3">
                        <?= Html::submitButton('保存',['id'=>"submit-button", 'class' => 'btn btn-primary']); ?>
                        <?= Html::a('取消', yii\helpers\Url::to(['view/index','id'=>$user->id]), ['onclick' => 'history(-1)']); ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-4">
            <img width="128" height="128" class="logo" alt="" src="<?php echo $user->bigavatar;?>">
            <p>
                <a title="上传头像" href="<?php echo yii\helpers\Url::to('user/avatar');?>">
                    更新头像
                </a>
            </p>
        </div>
    </div>
</div>