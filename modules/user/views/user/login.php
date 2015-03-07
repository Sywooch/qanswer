<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = "会员登录";
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe', [
                    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                ])->checkbox() ?>
                <div class="form-group ">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <p>
        <span class='pull-left'>还没账号? 直接<?= Html::a('注册', ['user/register']);?></span>
        <span class='pull-right'><?= Html::a('忘记密码', ['recovery/request']);?></span>
        </p>
    </div>
</div>
