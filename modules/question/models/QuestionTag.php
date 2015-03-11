<?php

namespace app\modules\question\models;

use yii\db\ActiveRecord;

class QuestionTag extends ActiveRecord
{

    public $total = 0;
    public $scores = 0;
    public $questions = 0;
    public $count = 0;

    public static function tableName()
    {
        return '{{%questiontag}}';
    }

    public function rules()
    {
        return array(
            array('tag', 'length', 'max' => 40),
        );
    }

    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'postid']);
    }

    public function getTags($projectId)
    {
//		$sql = 'SELECT tag, count(*) as c FROM  TicketTag where projectId = ' . $projectId . ' group by tag';
//
//		$connection = $this->dbConnection;
//		$command=$connection->createCommand($sql);
//		$reader = $command->query();
//
//		$result = array();
//		foreach($reader as $row)
//		{
//			$result[$row['tag']] = $row['c'];
//		}
//
//		return $result;
    }

    public function updateTags($oldTags, $newTags, $postid)
    {
        $this->addTags(array_values(array_diff($newTags, $oldTags)), $postid);
        $this->removeTags(array_values(array_diff($oldTags, $newTags)));
    }

    public function addTags($tags, $postid)
    {
        foreach ($tags as $name) {
            $qt = new QuestionTag;
            $qt->postid = $postid;
            $qt->tag = $name;
            $qt->time = time();
            $qt->save();
        }
    }

    public function removeTags($tags)
    {
        if (empty($tags))
            return;
        $criteria = new CDbCriteria;
        $criteria->addInCondition('tag', $tags, 'OR');
        $this->deleteAll($criteria);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Tag::model()->updateCounters(array('frequency' => -1), "name=:name", array(":name" => $this->tag));
    }

}

?>