<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

use app\components\String;

class Bounty extends ActiveRecord
{

    /**
     * 未发放的悬赏（正在进行中）
     * @var integer 0
     */
    const STATUS_OPEN = 0;  //未发放
    
    /**
     * 手动颁发赏金 1
     */
    const STATUS_MANUAL = 1;  //手动发放
    
    /**
     * 系统自动发放，赏金减半
     */
    const STATUS_SYS = 2;

    /**
     * 过期未授予（无合适答案）
     */
    const STATUS_EXPIRED = 3;

    public static function tableName()
    {
        return '{{%bounty}}';
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    public function getTouser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function isOpen()
    {
        return ($this->status == self::STATUS_OPEN);
    }

    public function isManualClose()
    {
        return ($this->status == self::STATUS_MANUAL);
    }

    public function isSysClose()
    {
        return ($this->status == self::STATUS_SYS);
    }

    public function isExpire()
    {
        return ($this->endtime <= time());
    }

    public function isMine()
    {
        return (!Yii::$app->user->isGuest && $this->uid == Yii::$app->user->getId());
    }

    public function afterFind()
    {
        $this->endtime = $this->time + Yii::$app->params['posts']['rewardLife'] * 86400;
        parent::afterFind();
    }

    public static function hasOpenBounty($postid)
    {
        return self::find()->where('questionid=:postid,status=:status', [':postid' => $postid, ':status' => self::STATUS_OPEN])->exists();
    }

    /**
     * 处理过期悬赏
     */
    public static function expiredBounties()
    {
        $time = time() - Yii::$app->params['posts']['rewardLife'] * 86400;
//        $bounties = Bounty::model()->findAll("status=:status AND time<=:time", array(":status" => self::STATUS_OPEN, ":time" => $time));
        $bounties = Bounty::findAll("status=:status AND time<=:time", [":status" => self::STATUS_OPEN, ":time" => $time]);
        
        foreach ($bounties as $bounty) {
            $qid = $bounty->questionid;
            $bountyTime = $bounty->time;
            //1、答案必须在悬赏启动之后产生的
            //2、答案必须得分2分以上
            //3、如果多个最佳答案得分相同，最早的答案得到赏金。
            //4、如果没有答案符合最佳答案条件，赏金自动失效，而且也不会返还给悬赏者！
            $post = Post::find()->where("idv=:qid AND score>=:score AND createtime>=:time")
                                ->params([':qid' => $qid, ':time' => $bountyTime, ':score' => Yii::$app->params['posts']['expiredBountyScores']])
                                ->orderBy("score DESC, createtime ASC")
                                ->one();
            
            if ($post != null) {
                $bounty->status = Bounty::STATUS_SYS;
                $bounty->answerid = $post->id;
                $bounty->touid = $post->uid;
                $bounty->totime = time();
                $bounty->bonus = $bounty->amount / 2;
                $bounty->save();

                $repute = new Repute;
                $repute->uid = $post->uid;
                $repute->postid = $post->idv;
                $repute->apostid = $post->id;
                $repute->type = Repute::AWARD;
                $repute->reputation = $bounty->bonus;
                $repute->save();
                $repute->calReputation($post->author, $bounty->bonus);

                $inbox = new Inbox;
                $inbox->title = $post->question->title;
                $inbox->url = Yii::$app->urlManager->createUrl(['questions/view', 'id' => $post->question->id, '#' => $post->id]);
                $inbox->summary = String::filterTitle($post->content, 100);
                $inbox->type = Inbox::$TYPE['bounty'];
                $inbox->uid = $post->uid;
                $inbox->save();
            } else {
                $bounty->status = Bounty::STATUS_EXPIRED;
                $bounty->bonus = 0;
                $bounty->save();
            }
        }
    }

}
