<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_tags}}".
 *
 * @property integer $uid
 * @property string $tag
 * @property integer $totalcount
 * @property integer $unwikicount
 */
class UserTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tag', 'totalcount', 'unwikicount'], 'required'],
            [['uid', 'totalcount', 'unwikicount'], 'integer'],
            [['tag'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => Yii::t('app', 'Uid'),
            'tag' => Yii::t('app', 'Tag'),
            'totalcount' => Yii::t('app', 'Totalcount'),
            'unwikicount' => Yii::t('app', 'Unwikicount'),
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne('User', ['id'=>'uid']);
    }
    
	public static function processTags($question) 
    {
        $tags = $question->_oldTags;
        $uid = Yii::$app->user->id;
		foreach($tags as $tag) {
            $ut = UserTags::find()->where('uid=:uid AND tag=:tag', [':uid'=>$uid,':tag'=>$tag])->one();
			if($ut === NULL){
				$userTag = new UserTags;
				$userTag->uid = $uid;
				$userTag->tag = $tag;
				$userTag->totalcount = 1;
				$userTag->save();
            } else {
                $ut->updateCounters(['totalcount'=>1]);
            }
//			if(!$this->exists('uid=:uid AND tag=:tag',array(':uid'=>$uid,':tag'=>$tag))){
//				$userTag = new UserTags;
//				$userTag->uid = $uid;
//				$userTag->tag = $tag;
//				$userTag->totalcount = 1;
//				$userTag->save();
//			} else {
//				UserTags::updateCounters(array('totalcount'=>1),'uid=:uid AND tag=:tag',array(':uid'=>$uid,':tag'=>$tag));
//			}
		}
	}    
}
