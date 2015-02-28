<?php

namespace app\modules\user;

class Module extends \yii\base\Module
{

    public $controllerNamespace = 'app\modules\user\controllers';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/user/messages',
            'fileMap' => [
                'modules/user/user' => 'user.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('modules/user/' . $category, $message, $params, $language);
    }

}
