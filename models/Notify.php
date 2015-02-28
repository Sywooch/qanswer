<?php
namespace app\models;

use yii\db\ActiveRecord;

class Notify extends ActiveRecord
{
	/**
	 * id
	 * uid
	 * message
	 * type
	 * 		badge
	 * time
	 * new
	 *
	 */

	public $formatMessage;

	public static function tableName()
	{
		return '{{%notify}}';
	}


	public function rules()
	{
		return array(
		);
	}

	public function beforeSave()	
    {
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->time= time();
				$this->new = 1;
			}
			return true;
		}else
			return false;
	}

	public function afterFind() 
    {
		parent::afterFind();

		switch($this->typeid) {
			case 5:
				$this->formatMessage = "你获得了".$this->message."徽章";
				break;
		}
	}

	public function send($message,$uid) {
		$this->message = $message;
		$this->uid = $uid;
		$this->save();
	}

	/**
	 *
	 * @param Badge $badge
	 * @param mix $uids
	 *				int|array
	 */
	public function sendBadge($badge,$uids) {

	}

	public function read() {
		$this->new = 0;
		$this->save();
	}

}