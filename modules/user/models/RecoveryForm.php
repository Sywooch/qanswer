<?php

namespace app\modules\user\models;

use yii\base\Model;

use app\modules\user\models\Token;
use app\modules\user\Module;

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
    
    
    /**
     * @var User
     */
    public $user;

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::className(),
                'message' => Module::t('user', 'There is no user with this email address')
            ],
            ['email', function ($attribute) {
                $this->user = User::findOne(['email' => $this->email]);
                if ($this->user !== null && !$this->user->isActive()) {
                    $this->addError($attribute, Module::t('user', 'You need to confirm your email address'));
                }
            }],
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
            'email' => Module::t('user', 'Email'),
            'password' => Module::t('user', 'Password'),
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
            $token = new Token();
            $token->id = $this->user->id;
            $token->type = Token::TYPE_RECOVERY;
            $token->save(false);
            
//            $this->mailer->sendRecoveryMessage($this->user, $token);
            \Yii::$app->session->setFlash('info', Module::t('user', 'An email has been sent with instructions for resetting your password'));
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
