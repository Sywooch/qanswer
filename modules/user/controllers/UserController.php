<?php

namespace app\modules\user\controllers;

use Yii;
use app\components\BaseController;
use app\components\Formatter;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;
use app\modules\user\Module;

class UserController extends BaseController
{
    public $layout = '//column1';
    
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
    public function actionEdit()
    {
        $this->title = "更新用户资料";
        $id = intval(Yii::$app->request->get('id'));
        if ($id != Yii::$app->user->id) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        if ($id) {
            $profile = UserProfile::findOne($id);
            $user = User::findOne($id);
        }
        if ($profile === null)
            throw new HttpException(404, 'The requested page does not exist.');

        // ajax validator
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-edit-form') {
            echo ActiveForm::validate(array($profile));
            Yii::$app->end();
        }

        $post = Yii::$app->request->post();
        if (isset($post)) {
            $user->load($post, 'User');
            $profile->load($post, 'UserProfile');
//			$user->attributes = $_POST['User'];
//			$profile->attributes = $_POST['UserProfile'];
            if ($user->validate() && $profile->validate()) {
                $user->save();
                $complete1 = $complete2 = true;
                foreach ($_POST['UserProfile'] as $p) {
                    if (empty($p)) {
                        $complete1 = false;
                        break;
                    }
                }
                foreach ($_POST['User'] as $p) {
                    if (empty($p)) {
                        $complete2 = false;
                        break;
                    }
                }
                $complete = ($complete1 & $complete2) ? 1 : 0;
                $profile->complete = $complete;
                if ($profile->save()) {
                    $user = User::Model()->findByPk($_GET['id']);
                    Yii::$app->user->setName($user->username);
                    $this->redirect(array('users/view', 'id' => $profile->id));
                }
            }
        }
        return $this->render('edit', array('profile' => $profile, 'user' => $user));
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
}
