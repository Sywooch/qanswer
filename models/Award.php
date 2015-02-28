<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\User;

class Award extends ActiveRecord
{
	public $badgecount;
	const IDTYPE_Q = 'question';
	const IDTYPE_A = 'answer';
	const IDTYPE_T = 'tag';
	const IDTYPE_D = 'day';
	const IDTYPE_DEFAULT = '';


	public static function tableName()
	{
		return '{{%award}}';
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'uid'),
			'badge'=> array(self::BELONGS_TO, 'Badge','badgeid')
		);
	}
    
    public function getBadge()
    {
        return $this->hasOne(Badge::className(),['id'=>'badgeid']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'uid']);
    }

	public function beforeSave()
	{
		$this->time = time();
		return parent::beforeSave();
	}

	public function awardBadge($user,$badge)
	{
		if ($badge->multiple == 1)
		{
			$this->uid = $user->id;
			$this->badgeid = $badge->id;
			$this->save();

			$badge->awardcount++;
			$badge->save();
			switch ($badge->type)
			{
				case Badge::GOLD :
					$user->gold++;
					break;
				case Badge::SILVER :
					$user->silver++;
					break;
				case Badge::BRONZE :
					$user->bronze++;
					break;
			}
			$user->save();
		}
		else
		{
			$model = Award::Model()->find("uid=:uid AND badgeid=:badgeid",array(':uid'=>$uid,':badgeid'=>$badge->id));
			if ($model === null)
			{
				$this->uid = $user->id;
				$this->badgeid = $badge->id;
				$this->save();
				$badge->awardcount++;
				$badge->save();
				switch ($badge->type)
				{
					case Badge::GOLD :
						$user->gold++;
						break;
					case Badge::SILVER :
						$user->silver++;
						break;
					case Badge::BRONZE :
						$user->bronze++;
						break;
				}
				$user->save();
			}
		}
	}
}