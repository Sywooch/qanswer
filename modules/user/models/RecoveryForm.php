<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;

/**
 * UserRecoveryForm class.
 * UserRecoveryForm is the data structure for keeping
 * user recovery form data. It is used by the 'recovery' action of 'UserController'.
 */
class RecoveryForm extends Model
{

    /** 
     * @var string 
     */
    public $email;

    /** 
     * @var string 
     */
    public $password;

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
//                'targetClass' => $this->module->modelMap['User'],
                'targetClass' => User::className(),
                'message' => \app\modules\user\Module::t('user', 'There is no user with this email address')
            ],
//            ['email', function ($attribute) {
//                $this->user = $this->finder->findUserByEmail($this->email);
//                if ($this->user !== null && $this->module->enableConfirmation && !$this->user->getIsConfirmed()) {
//                    $this->addError($attribute, \Yii::t('user', 'You need to confirm your email address'));
//                }
//            }],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /** 
     * @inheritdoc 
     */
    public function attributeLabels()
    {
        return [
            'email' => \app\modules\user\Module::t('user', 'Email'),
            'password' => \app\modules\user\Module::t('user', 'Password'),
        ];
    }
    
   /** @inheritdoc */
    public function scenarios()
    {
        return [
            'request' => ['email'],
            'reset'   => ['password']
        ];
    }
    
   /**
     * Sends recovery message.
     *
     * @return bool
     */
    public function sendRecoveryMessage()
    {
        if ($this->validate()) {
            /** @var Token $token */
            $token = \Yii::createObject([
                'class'   => Token::className(),
                'user_id' => $this->user->id,
                'type'    => Token::TYPE_RECOVERY
            ]);
            $token->save(false);
            $this->mailer->sendRecoveryMessage($this->user, $token);
            \Yii::$app->session->setFlash('info', \Yii::t('user', 'An email has been sent with instructions for resetting your password'));
            return true;
        }

        return false;
    }

    /**
     * Resets user's password.
     *
     * @param  Token $token
     * @return bool
     */
    public function resetPassword(Token $token)
    {
        if (!$this->validate() || $token->user === null) {
            return false;
        }

        if ($token->user->resetPassword($this->password)) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your password has been changed successfully.'));
            $token->delete();
        } else {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'An error occurred and your password has not been changed. Please try again later.'));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-form';
    }    
}
