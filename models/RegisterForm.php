<?php
namespace app\models;

use yii\base\Model;
use app\models\User;

class RegisterForm extends Model
{
    public $password;
	public $password2;
    public $email;
	public $verifyCode;

	const EMAIL_VERIFY = 1;
	const MOD_VERIFY = 2;

	public function rules()
	{
		return [
			[['email', 'password', 'password2'], 'required'],
			['email', 'email'],
//			array('password', 'length', 'max'=>128, 'min' => 6,'message' => "密码至少4个字符"),
//			array('password2', 'compare','compareAttribute'=>'password'),
//			array('email', 'unique', 'message' => "该邮箱地址已经存在"),
//			array('verifyCode', 'captcha', 'captchaAction'=>'users/captcha', 'message' => '输入的验证码不正确'),
		];
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'		=> '电子邮箱',
			'password'	=> '密码',
			'password2'	=> '确认密码',
			'verifyCode'=> '验证码'
		);
	}
    
    public function Register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }
        return null;
    }
}
