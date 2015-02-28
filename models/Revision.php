<?php

namespace app\models;

use yii\db\ActiveRecord;

class Revision extends ActiveRecord
{

    /**
     * 等待审核
     * @var tinyint
     */
    const STATUS_PEER = 0;

    /**
     * 通过审核
     * @var tinyint
     */
    const STATUS_OK = 1;

    /**
     * 未通过审核
     * @var tinyint
     */
    const STATUS_IGNORE = -1;

    public static function tableName()
    {
        return '{{%revision}}';
    }

    public function rules()
    {
        return array(
            array('text', 'required'),
            array('title,tags', 'required', 'on' => 'qask'),
        );
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function scopes()
    {
        return array(
            'active' => array(
                'condition' => 'status=' . self::STATUS_OK,
            ),
        );
    }

    /**
     * 新建一个版本
     * @param Post $post
     * @param string $oldtext
     */
    public function newRevision($post, $oldtext = '')
    {
        $this->postid = $post->id;
        $this->revtime = time();
        $this->text = $post->content;
        $this->title = $post->title;
        $this->tags = $post->tags;
        $this->uid = $post->uid;

        //@todo 过滤处理
        $comment = $_POST['Revision']['comment'];
        if (empty($comment)) {
            $len = mb_strlen($post->content, 'UTF8');
            if (empty($oldtext)) {
                $comment = "初始版本";
            } else {
                $oldlen = mb_strlen($oldtext, 'UTF8');
                $d = $len - $oldlen;
                $comment = (($d > 0) ? "增加了{$d}个字符" : "减少了{$d}个字符");
            }
        }
        $this->comment = $comment;
        $this->save();
    }
}
