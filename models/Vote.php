<?php

namespace app\models;

use yii\db\ActiveRecord;

class Vote extends ActiveRecord
{

    private $voteTypeIds = array(
        "informModerator" => -1,
        "undoMod" => 0,
        "acceptedByOwner" => 1,
        "upMod" => 2,
        "downMod" => 3,
        "offensive" => 4,
        "favorite" => 5,
        "close" => 6,
        "reopen" => 7,
        "bountyClose" => 9,
        "deletion" => 10,
        "undeletion" => 11,
        "spam" => 12
    );

    /**
     * 赞成 1
     * @var 有用
     */
    const UPVOTE = 1;

    /**
     * @var 无用
     *  -1
     */
    const DOWNVOTE = -1;

    /**
     * 默认
     */
    const NOVOTE = 0;

    /**
     * 收藏标志
     * @var tinyint 1
     */
    const FAV = 1;

    /**
     * 没有收藏
     * @var tinyint 0
     */
    const UNFAV = 0;

    public static function tableName()
    {
        return '{{%vote}}';
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    public function getQuestion()
    {
        return $this->hasOne(Post::className(), ['id' => 'postid']);
    }

    public function getFavs($criteria)
    {
        return $this->with('question')->findAll($criteria);
    }

}
