<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Activity extends ActiveRecord
{

    public $TYPE = array(
        'comment' => '评论',
        'ask' => '提问',
        'answer' => '回答',
        'voteup' => '有用票',
        'votedown' => '无用票',
        'edit' => '编辑',
        'award' => '授予徽章',
        'revise' => '修订',
        'fav' => '收藏',
        'accept' => '采纳',
    );

    public static function tableName()
    {
        return '{{%activity}}';
    }

    public function rules()
    {
        return array(
        );
    }

    public function getCntype()
    {
        return $this->TYPE[$this->type];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->data = addslashes(serialize($this->data));
                $this->time = time();
            }
            return true;
        } else
            return false;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->data = unserialize(stripslashes($this->data));
    }

    public function getUrl()
    {
        if (in_array($this->type, ['answer', 'ask', 'revise', 'comment'])) {
            return Yii::$app->urlManager->createUrl(['questions/view','id' => $this->data['qid']]);
        } elseif ($this->type == 'accept') {
            return Yii::$app->urlManager->createUrl(['questions/view','id' => $this->typeid]);
        }
    }

}
