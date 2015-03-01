<?php

namespace app\modules\user\controllers;

use Yii;
use yii\base\Model;
use app\components\BaseController;
use app\modules\user\models\RecoveryForm;

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
        $model = new RecoveryForm;
//        if (!$this->module->enablePasswordRecovery) {
//            throw new NotFoundHttpException;
//        }
//        $model = \Yii::createObject([
//                    'class' => RecoveryForm::className(),
//                    'scenario' => 'request',
//        ]);
        $this->performAjaxValidation($model);
        if ($model->load(\Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->render('/message', [
                        'title' => \Yii::t('user', 'Recovery message sent'),
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
    public function actionReset()
    {
//        if (!$this->module->enablePasswordRecovery) {
//            throw new NotFoundHttpException;
//        }
        /** @var Token $token */
//        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();
//        if ($token === null || $token->isExpired || $token->user === null) {
//            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.'));
//            return $this->render('/message', [
//                        'title' => \Yii::t('user', 'Invalid or expired link'),
//                        'module' => $this->module,
//            ]);
//        }
//        $model = \Yii::createObject([
//                    'class' => RecoveryForm::className(),
//                    'scenario' => 'reset',
//        ]);
//        $this->performAjaxValidation($model);
//        if ($model->load(\Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
//            return $this->render('/message', [
//                        'title' => \Yii::t('user', 'Password has been changed'),
//                        'module' => $this->module,
//            ]);
//        }
        $model = new RecoveryForm;
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
