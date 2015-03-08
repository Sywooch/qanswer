<?php

namespace app\models;

use app\modules\user\models\User as BaseUser;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password1
 * @property string $password
 * @property integer $active
 * @property string $activekey
 * @property string $salt
 * @property integer $groupid
 * @property integer $lastlogin
 * @property integer $registertime
 * @property integer $gold
 * @property integer $silver
 * @property integer $bronze
 * @property integer $reputation
 * @property integer $votedcount
 * @property integer $editedcount
 * @property integer $messagecount
 * @property integer $lastseen
 * @property integer $status
 * @property integer $setting
 */
class User extends BaseUser
{

}
