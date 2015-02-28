<?php

namespace app\models;

use app\models\User;

use yii\db\ActiveRecord;

class PostState extends ActiveRecord
{

    /**
     * 锁定 1
     */
    const POST_LOCK = 1;

    /**
     * 未锁定 0
     */
    const POST_UNLOCK = 0;

    /**
     * 帖子状态，关闭 1
     */
    const POST_CLOSE = 1;

    /**
     * 帖子状态，打开状态 0
     */
    const POST_OPEN = 0;
    const POST_DELETE = 1;
    const POST_UNDELETE = 0;
    const POST_PROTECT = 1;
    const POST_UNPROTECT = 0;

    public static function tableName()
    {
        return '{{%post_state}}';
    }
// @todo delete
//    public function relations()
//    {
//        return array(
//            'author' => array(self::BELONGS_TO, 'User', 'lockuid'),
//            'protectauthor' => array(self::BELONGS_TO, 'User', 'protectuid'),
//        );
//    }
    
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id'=>'lockuid']);
    }
    
    public function getProtectauthor()
    {
        return $this->hasOne(User::className(), ['id'=>'protectuid']);
    }

    /**
     * 检查问题是否被关闭
     * @return bool true 关闭 false 未关闭
     */
    public function isDelete()
    {
        return ($this->delete == self::POST_DELETE);
    }

    /**
     * 问题是否关闭
     * @return bool 已经关闭，返回true
     */
    public function isClose()
    {
        return ($this->close == self::POST_CLOSE);
    }

    /**
     * 问题是否打开
     * @return bool 打开返回true
     */
    public function isOpen()
    {
        return ($this->close == self::POST_OPEN);
    }

    /**
     * 问题是否锁定，锁定则返回true
     * @return 锁定返回true
     */
    public function isLock()
    {
        return ($this->lock == self::POST_LOCK);
    }

    /**
     * 问题是否受保护
     * @return 保护返回true
     */
    public function isProtect()
    {
        return ($this->protect == self::POST_PROTECT);
    }

}
