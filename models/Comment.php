<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

use app\models\CommentVote;
use app\models\Post;
use app\models\UserStat;

use app\components\String;

class Comment extends ActiveRecord
{

    /**
     * 删除
     * @var integer
     */
    const STATUS_DELETE = 0;

    /**
     * 正常
     * @var integer
     */
    const STATUS_OK = 1;
    
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }


    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
//			'author' => array(self::BELONGS_TO, 'User', 'uid'),
            'commentauthor' => array(self::BELONGS_TO, 'User', 'uid'),
//            'myvotes' => array(self::STAT, 'CommentVote', 'commentid', 'condition' => 'voteTypeId=2 AND uid=:uid', 'params' => array(':uid' => Yii::app()->user->getId())),
        );
    }
    
    public function getCommentauthor()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    public function getMyvotes()
    {
//        return $this->hasMany(CommentVote::className(), ['voteTypeId'=>2, 'uid' => Yii::$app->user->id])->c
        return $this->hasMany(CommentVote::className(), ['commentid' => 'id'])->where(['voteTypeId'=>2, 'uid' => Yii::$app->user->id])->count();
//        return $this->find()->where(['voteTypeId'=>2, 'uid' => Yii::$app->user->id])->count();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'content' => 'Comment',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'author' => 'Name',
            'email' => 'Email',
            'url' => 'Website',
            'post_id' => 'Post',
        );
    }

    /**
     * Approves a comment.
     */
    public function approve()
    {
        $this->status = Comment::STATUS_APPROVED;
        $this->update(array('status'));
    }

    /**
     * 添加评论
     * @param ActiveRecord $post
     */
    public function addComment($post)
    {
        $this->message = String::filterTitle($_POST['comment'], 300);
        $this->idv = $post->id;
        $this->idtype = 'question';
        $this->save();
        
        Post::updateAllCounters(['commentcount' => 1], ['id' => $post->id]);
//        Post::Model()->updateCounters(array("commentcount" => 1), "id=" . $post->id);
//        UserStat::Model()->updateCounters(array("commentcount" => 1), "id=" . $this->uid);
        UserStat::updateAllCounters(['commentcount' => 1], ['id' => $this->uid]);
    }

    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->time = time();
                $this->uid = Yii::$app->user->id;
            }
            return true;
        } else
            return false;
    }

    public function beforeDelete()
    {
        parent::beforeDelete();
        CommentVote::deleteAll("commentid=:id", [':id' => $this->id]);
//        CommentVote::model()->deleteAll("commentid=:id", array(':id' => $this->id));
        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Post::updateAllCounters(["commentcount" => -1], ['id' => $this->idv]);
//        Post::Model()->updateCounters(array("commentcount" => -1), "id=" . $this->idv);
    }

    public function isNotTimeout()
    {
        $delta = time() - $this->time;
        return ($delta <= Yii::$app->params['comments']['allowEditTime']);
    }

    public function isself()
    {
        return ($this->uid == Yii::$app->user->id);
    }

}