<?php

namespace app\models;

use yii\db\ActiveRecord;

class Inbox extends ActiveRecord
{

    public static $TYPE = array(
        'comment' => 'comment',
        'bounty' => 'bounty',
        'answer' => 'answer',
        'wiki' => 'wiki',
    );

    public static function tableName()
    {
        return '{{%inbox}}';
    }

    public function beforeSave($insert)
    {
        $this->time = time();
        return parent::beforeSave($insert);
    }

}
