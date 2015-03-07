<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/**
 * @var app\modules\user\models\RegisterForm $model
 */
$this->title = '会员注册';
?>
<?php print_r($model->errors);?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'register-form',
//                    'enableAjaxValidation' => true,
//                    'enableClientValidation' => false
                ]);
                ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= Html::submitButton(app\modules\user\Module::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?= Html::a(app\modules\user\Module::t('user', 'Already registered? Sign in!'), ['/user/user/login']) ?>
        </p>
    </div>
</div>