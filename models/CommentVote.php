<?php

namespace app\models;

use yii\db\ActiveRecord;

class CommentVote extends ActiveRecord
{
    /**
     * 有用 （赞）
     * @var integer
     */
    const UPVOTE = 1;

    public static function tableName()
    {
        return '{{%comment_vote}}';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'author' => array(self::BELONGS_TO, 'User', 'uid'),
            'question' => array(self::BELONGS_TO, 'Post', 'postid'),
        );
    }

    /**
     * 检查$typeid对应行为是否已经存在
     * @param type $id
     * @param type $typeid
     * @param type $uid
     * @return boolean
     */
    public static function check($id, $typeid, $uid)
    {
        return self::find()->where(['commentid' => $id, 'voteTypeId' => $typeid, 'uid' => $uid])->count() > 0;
//        return $this->count("commentid=:commentid AND voteTypeId=:typeid AND uid=:uid", array(':commentid' => $id, ':typeid' => $typeid, ':uid' => $uid)) > 0;
    }

    public static function getFlagCount($id, $typeid)
    {
        $life = \Yii::$app->params['posts']['flagLife'] * 86400;
        return self::find()->where('commentid=:commentid AND voteTypeId=:typeid AND time>:time', [':commentid' => $id, ':typeid' => $typeid, ':time' => time() - $life])
                    ->count();
    }

}
