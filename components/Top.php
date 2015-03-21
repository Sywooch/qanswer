<?php

namespace app\components;

use Yii;
use app\modules\question\models\QuestionTag;

class Top
{

    /**
     * 查询top users
     * @param int $day
     */
    public static function TopTagUsers($tagName, $day = 0)
    {
        $condition = "questiontag.tag=:tag";
        $params = array(':tag' => $tagName);
        if ($day > 0) {
            $condition = $condition . " AND post.createtime>=:time";
            $params[':time'] = time() - 86400 * $day;
        }

        $users = (new \yii\db\Query)->select("{{%user}}.id as id, user.*,count({{%post}}.id) as questions,sum({{%post}}.score) as scores")
                ->from(QuestionTag::tableName())
                ->innerJoin('{{%post}}', 'post.id=questiontag.postid')
                ->innerJoin('{{%user}}', '{{%user}}.id={{%post}}.uid')
                ->where($condition, $params)
                ->groupBy('{{%user}}.id')
                ->orderBy(['sum({{%post}}.score)' => SORT_DESC])
                ->all();
        return $users;
    }

    public static function tagAnswersUsers($tagName, $day = 0)
    {
        $sql = "SELECT
		    u.*,count(a.id) as answers,sum(a.score) as scores
		FROM questiontag qt
		    JOIN post q ON q.id = qt.postid
		    JOIN post a ON a.idv = q.id
		    JOIN user u ON u.id = a.uid
		WHERE
		    qt.tag = 'regex'
		GROUP BY
		    u.id
		ORDER BY
		    sum(a.score) DESC";

//		$cmd = Yii::$app->db->createCommand();
        $condition = "questiontag.tag=:tag";
        $params = array(':tag' => $tagName);
        if ($day > 0) {
            $condition = $condition . " AND answer.createtime>=:time";
            $params[':time'] = time() - 86400 * $day;
        }
//		$cmd->select("u.*,count(a.id) as answers,sum(a.score) as scores")
//			->from('questiontag qt')
//			->join('{{post}} q','q.id=qt.postid')
//			->join('{{post}} a','a.idv=q.id')
//			->join('{{user}} u','u.id= a.uid')
//			->where($condition,$params)
//			->group('u.id')
//			->order('sum(a.score) DESC');
//		$users = $cmd->queryAll();

        $users = QuestionTag::find()->select("user.*,count(answer.id) as answers,sum(answer.score) as scores")
                ->innerJoin("{{%post}} as question", ['question.id' => 'questiontag'])
                ->innerJoin('{{%post}} as answer', ['answer.idv' => 'question.id'])
                ->innerJoin('{{%user}}', ['user.id' => 'answer.id'])
                ->where($condition, $params)
                ->groupBy('user.id')
                ->orderBy(['sum(answer.score)' => SORT_DESC])
                ->all();
        return $users;
    }

    public static function tagQuestionsAll($tagName)
    {
        $week = self::tagQuestionsCount($tagName, 7);
        $month = self::tagQuestionsCount($tagName, 30);
        $all = self::tagQuestionsCount($tagName);
//		return array('week'=>$week->count,'month'=>$month['count'],'all'=>$all['count']);
        // @todo 临时
        return array('week' => 5, 'month' => 5, 'all' => 5);
    }

    public static function tagQuestionsCount($tagName, $day = 0)
    {
//		$cmd = Yii::app()->db->createCommand();
        $condition = "questiontag.tag=:tag";
        $params = array(':tag' => $tagName);
        if ($day > 0) {
            $condition = $condition . " AND post.createtime>=:time";
            $params[':time'] = time() - 86400 * $day;
        }
//		$cmd->select("count(1) as count")
//			->from('questiontag qt')
//			->join('{{post}} p','p.id=qt.postid')
//			->where($condition,$params);
//		$users = $cmd->queryRow();

        $users = QuestionTag::find()->select('count(*) AS count')
                ->innerJoin('{{%post}}', ['post.id' => 'questiontag.postid'])
                ->where($condition, $params)
                ->all();

        return $users;
    }

    public static function thisWeekTopUsers($date, $n = 10)
    {
        $cacheId = 'week.topuser.rep.' . $date;
        $data = Yii::app()->cache->get($cacheId);
        if (!$data) {
            $data = self::weekTopUsers($date, $n);
            Yii::app()->cache->set($cacheId, $data, 60 * 15);
        }
        return $data;
    }

    /**
     * 从缓存中查询每周威望排行榜
     * @param $date
     * @param $n
     * @param $expire
     */
    public static function cacheWeekTopUsers($date, $n = 10, $expire = 0)
    {
        $cacheId = 'week.topuser.rep.' . $date;
        $data = Yii::app()->cache->get($cacheId);
        if (!$data) {
            $data = self::weekTopUsers($date, $n);
            Yii::app()->cache->set($cacheId, $data, $expire);
        }
        return $data;
    }

    /**
     * 每周威望增加排行榜
     * @param $date 格式：2011-08-14
     * @param  $n 数量
     */
    public static function weekTopUsers($date, $n = 10)
    {
        $start = strtotime($date);
        $end = $start + 86400 * 7;

        $last = self::getTopUsers($start, $end, $n);
        $uids = array_keys($last);

        $users = array();
        $criteria = new CDbCriteria(array());
        $criteria->addInCondition('id', $uids);
        $models = User::Model()->findAll($criteria);
        foreach ($models as $user) {
            $users[$user->id] = $user;
        }
        return array('reps' => $last, 'users' => $users);
    }

    public static function getTopUsers($start, $end, $n = 5)
    {
        $ids = Yii::app()->db->createCommand()
                ->select("uid,sum(reputation) as total")
                ->from("{{repute}}")
                ->group("uid")
                ->where('time>=:start AND time<=:end', array(':start' => $start, ':end' => $end))
                ->queryAll();

        $uids = array();
        foreach ($ids as $id) {
            $uids[$id['uid']] = $id['total'];
        }
        arsort($uids);
        return array_slice($uids, 0, $n, true);
    }

}
