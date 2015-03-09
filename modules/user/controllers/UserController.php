<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\HttpException;
use app\components\BaseController;
use app\components\Formatter;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;
use app\modules\user\Module;
use app\modules\user\models\User;
use app\modules\user\models\UserProfile;

class UserController extends BaseController
{
    public $layout = '//column1';
    
    /**
     *
     * @var User $model
     */
    private $model = null;


    public function actionLogin()
    {
        if (Yii::$app->user->isGuest) {
            $model = new LoginForm;

            $this->performAjaxValidation($model);

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            } else {
                return $this->render('login', ['model' => $model]);
            }
        } else {
            return $this->goBack();
        }
    }
    
    public function actionRegister()
    {
        $model = new RegisterForm;

        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->user->returnUrl);
        } elseif ($model->load(Yii::$app->request->post()) && $model->register()) {
            return $this->render('/message', [
                'title' => Module::t('user', 'Your account has been created'),
            ]);
        }
        return $this->render('register', array('model' => $model));
    }  
    
    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->redirect(Yii::$app->homeUrl);
    }    
   
     /**
     * 编辑用户资料
     */
    public function actionEdit($id)
    {
        $this->title = "更新用户资料";

        $user = $this->loadModel($id);
        $user->scenario = 'update';
        $profile = UserProfile::findOne($id);

        $post = Yii::$app->request->post();
        if ($user->load($post) && $profile->load($post) && $user->save() && $profile->save()) {
            \Yii::$app->getSession()->setFlash('success', \app\modules\user\Module::t('user', 'User has been updated'));
            return $this->refresh();
        }     
        return $this->render('edit', [
            'profile' => $profile, 
            'user' => $user
        ]);
    }
    
    public function actionSavepreference()
    {
        $key = intval(Yii::$app->request->post('key'));
        $uid = Yii::$app->user->id;
        $userProfile = \app\modules\user\models\UserProfile::findOne($uid);

        if ($userProfile) {
            if ($key == 20) {
                $value = Yii::$app->request->post('value');
                $preference = Formatter::filterTags($value);
                $userProfile->preference = implode(" ", $preference);
                $userProfile->update(false, ['preference']);
                echo $userProfile->preference;
            } elseif ($key == 25) {
                $value = Yii::$app->request->post('value');
                $unpreference = Formatter::filterTags($value);
                $userProfile->unpreference = implode(" ", $unpreference);
                $userProfile->update(false, ['preference']);
            }
        }
    }

    /**
     * Performs ajax validation.
     * @param Model $model
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation($model)
    {
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            \Yii::$app->end();
        }
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer               $id
     * @return User                  the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function loadModel($id)
    {
        if ($this->model === null) {
            $this->model = User::findOne($id);
            if ($this->model === null) {
                throw new NotFoundHttpException('The requested page does not exist');
            }
        }
        
        return $this->model;
    }    
}
