<?php
namespace app\modules\user\models;

use yii\base\Model;
use app\modules\user\Module;

class RegisterForm extends Model
{
    public $password;
    public $email;
    
    /** @var User */
    protected $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->user = new User;
        $this->user->setScenario('register');
    }

    /** 
     * @inheritdoc 
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className(),
                'message' => Module::t('user', 'This email address has already been taken')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }    

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'		=> Module::t('user', 'Email'),
			'password'	=> Module::t('user', 'Password'),
		);
	}
    
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->user->setAttributes([
            'email'    => $this->email,
            'password' => $this->password
        ]);

        return $this->user->register();
    }
}
