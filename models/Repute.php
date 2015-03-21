<?php
namespace app\models;

use yii\db\ActiveRecord;

class Repute extends ActiveRecord
{

    /**
     * 问题支持票
     */
    const Q_UPVOTE = 1;

    /**
     * 答案支持票
     */
    const A_UPVOTE = 2;

    /**
     * @var 答案被采纳
     */
    const A_ACCEPT = 3;
    const ACCEPT_A = 4;

    /**
     * @var 反对票被撤销，被操作者
     */
    const DOWNVOTE_CANCELED = 5;
    const REVISE_ACCEPT = 6;

    /**
     * @var 撤销反对票，操作者
     */
    const CANCEL_DOWNVOTE = 7;
    const AWARD = 8;
    const AWARD_HALF = 9;

    /**
     * @var 答案/问题被投反对票
     */
    const QA_DOWNVOTE = 10;

    /**
     * @var 反对票
     */
    const DOWNVOTE_QA = 11;
    const Q_UPVOTE_CANCEL = 12;
    const A_UPVOTE_CANCEL = 13;

    /**
     * 答案采纳被取消
     */
    const A_ACCEPT_CANCEL = 14;

    /**
     * 取消采纳的答案
     */
    const ACCEPT_A_CANCEL = 16;

    /**
     * 提供悬赏
     */
    const OFFER_AWARD = 15;

    /**
     * 举报删除(达到举报门限被删除）
     */
    const FLAG_DELETE = 17;

    public $reputations;
    public $lng = "";
    
    //@todo 重新设计规则实现方式
    public $REPUTE_RULE = array(
        1 => array('s' => 5, 'lng' => '有用票'),
        2 => array('s' => 10, 'lng' => '有用票'),
        3 => array('s' => 15, 'lng' => '答案被采纳'),
        4 => array('s' => 2, 'lng' => '采纳答案'),
        5 => array('s' => 2, 'lng' => '无用票被撤销'),
        6 => array('s' => 2, 'lng' => '版本被采纳'),
        7 => array('s' => 1, 'lng' => '撤销无用票'),
        8 => array('s' => 0, 'lng' => '悬赏奖励'),
        9 => array('s' => 0, 'lng' => '悬赏'),
        10 => array('s' => -2, 'lng' => '被投无用票'),
        11 => array('s' => -1, 'lng' => '无用票'),
        12 => array('s' => -5, 'lng' => '有用票被撤销'),
        13 => array('s' => -10, 'lng' => '答案有用票被撤销'),
        14 => array('s' => -15, 'lng' => '答案采纳被取消'),
        15 => array('s' => 0, 'lng' => '提供悬赏'),
        16 => array('s' => -2, 'lng' => '取消采纳的答案'),
        17 => array('s' => 0, 'lng' => '举报删除')
    );
    
    public static function tableName()
    {
        return '{{%repute}}';
    }

    public function relations()
    {
        return array(
            'question' => array(self::BELONGS_TO, 'Post', 'postid'),
        );
    }
    
    public function getQuestion() 
    {
        return $this->hasOne(Post::className(), ['id' => 'postid']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->time = time();
                $score = $this->REPUTE_RULE[$this->type]['s'];
                if ($score != 0)
                    $this->reputation = $score;
            }
            return true;
        } else
            return false;
    }

    /**
     * 
     * @param User $user
     * @param type $score
     */
    public function calReputation($user, $score = 0)
    {
        if ($score == 0)
            $score = $this->REPUTE_RULE[$this->type]['s'];

        $user->reputation = $user->reputation + $score;
        $user->setScenario('update');
        $user->update(['reputation']);
    }

    // @todo 废弃
    public function getReputes($criteria)
    {

        $data = $this->with('question')->findAll($criteria);
        //今天时间开始线
        $today = strtotime(gmdate('Y-m-d'));
        $list = array();

        foreach ($data as $item) {
            $item->lng = $this->REPUTE_RULE[$item->type]['lng'];
            if ($item->time >= $today) {
                $theday = '今天';
            } elseif ($item->time >= $today - 3600 * 24) {
                $theday = '昨天';
            } elseif ($item->time >= $today - 3600 * 24 * 2) {
                $theday = '前天';
            } else {
                $theday = gmdate('m-d', $item->time);
            }
            $list[$theday]['list'][] = $item;
            if (!isset($list[$theday]['total'])) {
                $list[$theday]['total'] = 0;
            }
            $list[$theday]['total'] += $item->reputation;
        }
        return $list;
    }
    
    public static function formatReputes($reputes)
    {
        //今天时间开始线
        $today = strtotime(gmdate('Y-m-d'));
        $list = array();

        foreach ($reputes as $item) {
            $item->lng = $item->REPUTE_RULE[$item->type]['lng'];
            if ($item->time >= $today) {
                $theday = '今天';
            } elseif ($item->time >= $today - 3600 * 24) {
                $theday = '昨天';
            } elseif ($item->time >= $today - 3600 * 24 * 2) {
                $theday = '前天';
            } else {
                $theday = gmdate('m-d', $item->time);
            }
            $list[$theday]['list'][] = $item;
            if (!isset($list[$theday]['total'])) {
                $list[$theday]['total'] = 0;
            }
            $list[$theday]['total'] += $item->reputation;
        }
        return $list;        
    }

    /**
     * 更新帖子相关威望
     * @param model 帖子 $post
     * @param model 用户 更改威望的用户
     * @param int 类型 $type
     * @param int 更新威望数量
     */
    public function updatePostReputations($post, $user, $type, $amount = 0)
    {
        $this->uid = $user->id;
        $this->reputation = $amount;
        if ($post->isQuestion()) {
            $this->postid = $post->id;
        } else {
            $this->postid = $post->idv;
            $this->apostid = $post->id;
        }
        $this->type = $type;
        $this->save();
    }

}
