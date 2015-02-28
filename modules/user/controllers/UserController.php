<?php

namespace app\modules\user\controllers;

use Yii;
use app\components\BaseController;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;

class UserController extends BaseController
{
    public $layout = '//column1';
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionLogin()
    {
        if (Yii::$app->user->isGuest) {
            $model = new LoginForm;

            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                echo CActiveForm::validate($model);
                Yii::$app->end();
            }

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
        $this->title = "注册";
//		if ($this->options['closeregister']) {
//			$this->pageTitle = "禁止新用户注册";
//			$this->render('/common/closeregister');
//			Yii::$app->end();
//		}

        $model = new RegisterForm;
//		if (Yii::$app->request->post()-) {
//			echo CActiveForm::validate($model);
//			Yii::$app->end();
//		}

        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->user->returnUrl);
        } elseif ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $soucePassword = $model->password;
                $model->activekey = md5(microtime() . $model->password);
                $salt = substr(uniqid(rand()), -6);
                $model->password = md5(md5($model->password) . $salt);
                $model->password2 = md5(md5($model->password2) . $salt);
                $model->salt = $salt;
                $parts = explode("@", $model->email);
                $model->username = $parts[0];

                $model->registertime = time();
                $model->status = ($this->options['regverify'] == RegisterForm::EMAIL_VERIFY || $this->options['regverify'] == RegisterForm::MOD_VERIFY) ? User::STATUS_NOACTIVE : User::STATUS_ACTIVE;

                if ($model->save()) {

                    $userstat = new UserStat();
                    $userstat->id = $model->id;
                    $userstat->save();

                    $userprofile = new UserProfile;
                    $userprofile->id = $model->id;
                    $userprofile->save();

                    //更新用户名为user+ID格式
                    $model->username = 'user' . $model->id;
                    $model->update(array('username'));

                    $login = Html::a("登录", Yii::$app->user->loginUrl);

                    if ($this->options['regverify'] == RegisterForm::EMAIL_VERIFY) {
                        $body = "<h2>" . $model->username . ",您好！</h2>";
                        $body .= "<p>欢迎您注册成" . Yii::$app->name . "的用户，您可以通过点击以下地址来激活您的帐户：</p>";
                        $activeUrl = $this->createAbsoluteUrl('/users/active', array("activekey" => $model->activekey, "email" => $model->email));
                        $body .= '<p><a href="' . $activeUrl . '">激活帐号</a></p>';
                        $body .= "<p>如果您不能点击上面链接，还可以将以下链接复制到浏览器地址栏中访问：</p>";
                        $body .= $activeUrl;
                        $body .= "<div align='right' style='padding-right:10%'><p>" . Yii::$app->name . "</p>";

                        Emailer::mail(array($model->email), Yii::t('users', 'active'), $body);
                        Yii::$app->user->setFlash('registration', "感谢你的注册，激活邮件已经发到邮箱，请登录邮箱激活");
                    } elseif ($this->options['regverify'] == RegisterForm::MOD_VERIFY) {
                        Yii::$app->user->setFlash('registration', "感谢你的注册，请等待管理员的审核");
                    } else {
                        if ($this->options['autologin']) {
                            $identity = new UserIdentity($model->email, $soucePassword);
                            $identity->authenticate();
                            Yii::$app->user->login($identity, 0);
                            $this->redirect(Yii::$app->user->returnUrl);
                        } else {
                            Yii::$app->user->setFlash('registration', "感谢你的注册，请{$login}！");
                        }
                    }
                    // @todo
                    //1. 发送激活邮件
                    //2. 是否允许非激活会员登录？允许则登录
                    $this->refresh();
                }
//				$this->redirect(Yii::$app->user->returnUrl);
            }
        }
        return $this->render('register', array('model' => $model));
    }    
}
