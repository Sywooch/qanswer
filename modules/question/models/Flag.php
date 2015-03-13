<?php

namespace app\modules\question\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%flag}}".
 *
 * @property integer $id
 * @property string $idtype
 * @property integer $idval
 * @property integer $uid
 * @property integer $status
 * @property integer $time
 */
class Flag extends ActiveRecord
{

    const IDTYPE_P = 'P';
    const IDTYPE_C = 'C';
    const STATUS_OK = 1;
    const STATUS_NO = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%flag}}';
    }

    /**
     * @inheritdoc
     */
    protected function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->time = time();
                $this->uid = \Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }

    public static function check($postid, $uid)
    {
        return self::find()->where("idval=:idval AND uid=:uid AND status=:status", [':idval' => $postid, ':uid' => $uid, ':status' => self::STATUS_NO])->count() > 0;
    }

    public static function getFlagCount($postid)
    {
        $life = \Yii::$app->params['posts']['flagLife'] * 86400;
        return self::find()->where("idval=:idval AND time>:time AND status=:status", [':idval' => $postid, ':time' => time() - $life, ':status' => self::STATUS_NO])->count();
    }

    public function pass($postid)
    {
        self::updateAll(['status' => self::STATUS_OK], ['idval' => $postid]);
    }

}
