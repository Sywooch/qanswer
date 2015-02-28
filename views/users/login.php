<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<div class="subheader">
	<h1 id="user-displayname">会员登录</h1>
</div>
<div class="form">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'rememberMe', [
        'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])->checkbox() ?>
    
	<div class="row">
		<p class="hint">
		<?php // echo CHtml::link("注册",array('users/register'));?> | <?php // echo CHtml::link("忘记密码",array('users/recovery')); ?>
		</p>
	</div>


    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

	<div id="third-login">
		<h3>你也可以通过第三方账号进行登录！</h3>
		<p>
            <a href="<?php echo Url::to(['/oauth/login/index','type'=>'sina']);?>">
			<img style="cursor: pointer;" src="<?php echo  \Yii::$app->request->baseUrl;?>/images/open/sina240.png">
		</a>
		</p>
		<p>
            <a href="<?php echo Url::to(['/oauth/login/index','type'=>'qq']);?>">
			<img style="cursor: pointer;" src="http://qzonestyle.gtimg.cn/qzone/vas/opensns/res/img/Connect_logo_3.png">
		</a>
		</p>
		<a href="<?php echo Url::to(['/oauth/login/index','type'=>'douban']);?>">
			<img style="cursor: pointer;" src="http://img3.douban.com/pics/doubanicon-24-full.png">
		</a>
	</div>
</div>
