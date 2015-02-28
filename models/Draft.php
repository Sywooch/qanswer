<?php

namespace app\models;

use yii\db\ActiveRecord;

class Draft extends ActiveRecord
{

    const TYPE_ASK = 'Q';
    const TYPE_TAG = 'T';
    const TYPE_ANSER = 'A';

    public static function tableName()
    {
        return '{{%draft}}';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->dateline = time();
            }
            return true;
        } else
            return false;
    }

}
