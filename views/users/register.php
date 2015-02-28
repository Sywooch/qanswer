<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="subheader">
    <h1 id="user-displayname">会员注册</h1>
</div>

<div id="mainbar" class="form">

        <?php if (Yii::$app->session->hasFlash('registration')): ?>
        <div class="success">
    <?php echo Yii::$app->session->getFlash('registration'); ?>
        </div>

    <?php else: ?>
        <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>
        <?= $form->field($model, 'email'); ?>
        <?= $form->field($model, 'password')->passwordInput(); ?>
    <?= $form->field($model, 'password2')->passwordInput(); ?>
    </div>
    <div class="row submit">
    <?= Html::submitButton('注册'); ?>
    </div>

    <?php ActiveForm::end(); ?>
<?php endif; ?>
</div>
