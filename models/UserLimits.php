<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_limits}}".
 *
 * @property integer $uid
 * @property string $action
 * @property integer $period
 * @property integer $count
 */
class UserLimits extends ActiveRecord
{

    /**
     * 举报：提醒版主注意 @todo 废弃
     * @var string
     */
    const ACTION_INFORM_MOD = 'I';

    /**
     * 举报：垃圾信息，相对于提醒版主注意
     * @var string
     */
    const ACTION_SPAM_FLAG = 'S';

    /**
     * 投票：向上和向下
     */
    const ACTION_VOTE = 'V'; //投票（voteUp/voteDown）
    /**
     * 关闭投票
     * @var string
     */
    const ACTION_CLOSE_VOTE = 'C'; //关闭票

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_limits}}';
    }

    public static function remaining($uid, $action)
    {
        $period = (int) (time() / 86400);

        $limits = self::find()->where("uid=:uid AND action=:action", ['uid' => $uid, 'action' => $action])->one();
        switch ($action) {
            case self::ACTION_SPAM_FLAG:
                $userlimit = Yii::$app->params['posts']['spamFlags'];
                break;
            case self::ACTION_VOTE:
                $userlimit = Yii::$app->params['users']['votesPerDay'];
                break;
            case self::ACTION_CLOSE_VOTE:
                $userlimit = Yii::$app->params['users']['closeVotesPerDay'];
                break;
        }
        return max(0, $userlimit - (($limits['period'] == $period) ? $limits['count'] : 0));
    }

    /**
     * 增加限制数量
     * 针对每天的限制，可以调整这个时间单位
     * @param int $uid
     * @param string $action
     */
    public static function limitIncrement($uid, $action)
    {
        $period = (int) (time() / 86400);
        $count = 1;

        $sql = 'INSERT INTO {{user_limits}} (uid, action, period, count) VALUES (:uid, :action, :period, :count) ' .
                'ON DUPLICATE KEY UPDATE count=IF(period=:period, count+:count, :count), period=:period';
//		$sql = 'INSERT INTO {{user_limits}} (uid, action, period, count) VALUES (:uid, :action, :period, :count)';
        self::getDb()->createCommand($sql)
            ->bindValue(':uid', $uid)
            ->bindValue(':action', $action)
            ->bindValue(':period', $period)
            ->bindValue(':count', $count)
            ->execute();
    }

}
