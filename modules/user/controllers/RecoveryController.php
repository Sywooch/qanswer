<?php

namespace app\modules\user\controllers;

use Yii;
use yii\base\Model;
use app\components\BaseController;
use app\modules\user\Module;
use app\modules\user\models\RecoveryForm;
use app\modules\user\models\Token;

/**
 * Description of RecoveryController
 *
 * @author xuesong
 */
class RecoveryController extends BaseController
{
    public $layout = '//column1';
    /**
     * Shows page where user can request password recovery.
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        $model = new RecoveryForm();
        $model->scenario = 'request';
        $this->performAjaxValidation($model);
        
        if ($model->load(\Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->render('/message', [
                'title' => \app\modules\user\Module::t('user', 'Recovery message sent'),
                'module' => $this->module,
            ]);
        }
        return $this->render('request', [
            'model' => $model,
        ]);
    }
    
    /**
     * 重置密码页面
     * @param integer $id
     * @param string $code
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
//    public function actionReset($id, $code)
    public function actionReset($id, $code)
    {
        /** @var Token $token */
        $token = Token::findOne(['id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY]);
        if ($token === null || $token->isExpired || $token->user === null) {
            \Yii::$app->session->setFlash('danger', Module::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.'));
            return $this->render('/message', [
                'title' => Module::t('user', 'Invalid or expired link'),
            ]);
        }
        $model = new RecoveryForm(['scenario' => 'reset']);
        $this->performAjaxValidation($model);
        if ($model->load(\Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            return $this->render('/message', [
                'title' => \Yii::t('user', 'Password has been changed'),
            ]);
        }
        return $this->render('reset', [
            'model' => $model,
        ]);
    }

    /**
     * Performs ajax validation.
     * @param Model $model
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation(Model $model)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            \Yii::$app->end();
        }
    }

}
