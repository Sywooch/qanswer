<?php

namespace app\modules\user\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Token Active Record model.
 *
 * @property integer $id
 * @property string  $code
 * @property integer $created_at
 * @property integer $type
 * @property string  $url
 * @property bool    $isExpired
 * @property User    $user
 *
 */
class Token extends ActiveRecord
{
    const TYPE_CONFIRMATION      = 0;
    const TYPE_RECOVERY          = 1;

    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = \Yii::$app->getModule('user');
    }
    
    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%token}}';
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
                $route = '/user/registration/confirm';
                break;
            case self::TYPE_RECOVERY:
                $route = '/user/recovery/reset';
                break;
            default:
                throw new \RuntimeException;
        }

        return Url::to([$route, 'id' => $this->id, 'code' => $this->code], true);
    }

    /**
     * @return bool Whether token has expired.
     */
    public function getIsExpired()
    {
        switch ($this->type) {
            case self::TYPE_CONFIRMATION:
                $expirationTime = $this->module->confirmWithin;
                break;
            case self::TYPE_RECOVERY:
                $expirationTime = $this->module->recoverWithin;
                break;
            default:
                throw new \RuntimeException;
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', \Yii::$app->security->generateRandomString());
        }

        return parent::beforeSave($insert);
    }



    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['id', 'code', 'type'];
    }
}