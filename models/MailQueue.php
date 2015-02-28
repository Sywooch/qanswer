<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class MailQueue extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%mail_queue}}';
    }

    /**
     *    清除发送N次错误和过期的邮件
     */
    public function clear()
    {
        $criteria = new CDbCriteria(array(
            'condition' => " errnum > 3 or time < :addtime",
            'params' => array(
                'addtime' => time() - 259200
            )
        ));
        //这里使用了CDbCriteria创建查询条件，并通过指定占位符，进行变量绑定。
        return $this->deleteAll($criteria);
    }

    /**
     * 发送邮件
     */
    public function send($limit = 5)
    {
        $options = Yii::app()->cache->get('options');
        if ($options == false) {
            $models = Options::model()->findAll();
            $options = array();
            foreach ($models as $t) {
                $options[$t->name] = $t->value;
            }
            Yii::app()->cache->set('options', $options, 0);
        }
        Yii::app()->params['mail'] = $options['mail'];

        /* 清除不能发送的邮件 */
        $this->clear();

        $time = time();

        /* 取出所有未锁定的 */
        $mails = $this->findAll(array(
            'condition' => "lockexpiry < ?",
            'order' => 'time DESC, errnum ASC',
            'limit' => $limit,
            'params' => array($time)
        ));
        //这里直接使用数据代替CDbCriteria类作为条件参数，并使用？作为占位符，
        //条件中可以有多个?占位符，参数数组params中值的先后顺序与条件中占位符的顺序相同。
        if (!$mails) {
            /* 没有邮件，不需要发送 */
            return 0;
        }

        /* 锁定待发送邮件 */
        $queueIds = $this->getQueueIds($mails);
        $lockexpiry = $time + 30;    //锁定30秒

        $this->updateAll(
                array(
            "errnum" => new CDbExpression('errnum + 1'),
            'lockexpiry' => $lockexpiry
                ), "id in ( {$queueIds} ) "
        );

        $error_count = 0;
        foreach ($mails as $mail) {
            $result = Emailer::mail(array($mail->mailto), $mail->subject, $mail->body);
            if ($result) {
                $mail->delete();
            } else {
                $error_count++;
            }
        }
    }

    protected function getQueueIds($mails)
    {
        $queueIds = array();
        foreach ($mails as $mail) {
            $queueIds[] = $mail->id;
        }
        return implode(",", $queueIds);
    }

    /**
     * Add email to queue
     * @param string $mailto
     * @param string $subject
     * @param string $body
     */
    public static function addQueue($mailto, $subject, $body)
    {
        $mail = new MailQueue();
        $mail->mailto = $mailto;
        $mail->subject = $subject;
        $mail->body = $body;
        $mail->time = time();
        $mail->save();
    }

}
