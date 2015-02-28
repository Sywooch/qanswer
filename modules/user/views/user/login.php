<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<div class="subheader">
	<h1 id="user-displayname">会员登录</h1>
</div>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
        ]); ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe', [
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ])->checkbox() ?>
        <div class="form-group ">
            <?= Html::submitButton('Login', ['class' => 'btn btn-success btn-block btn-lg', 'name' => 'login-button']) ?>
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
        </div>
    </div>
</div>
