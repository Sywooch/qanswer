<?php

namespace app\models;

use yii\db\ActiveRecord;

class Tag extends ActiveRecord
{
    /**
     * The followings are the available columns in table 'tbl_tag':
     * @var integer $id
     * @var string $name
     * @var integer $frequency
     */

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('frequency', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 128),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'post' => array(self::BELONGS_TO, 'Post', 'postid'),
        );
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'postid']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'name' => 'Name',
            'frequency' => 'Frequency',
        );
    }

    /**
     * Returns tag names and their corresponding weights.
     * Only the tags with the top weights will be returned.
     * @param integer the maximum number of tags that should be returned
     * @return array weights indexed by tag names.
     */
    public function findTagWeights($limit = 20)
    {
        $models = $this->findAll(array(
            'order' => 'frequency DESC',
            'limit' => $limit,
        ));

        $total = 0;
        foreach ($models as $model)
            $total+=$model->frequency;

        $tags = array();
        if ($total > 0) {
            foreach ($models as $model)
                $tags[$model->name] = 8 + (int) (16 * $model->frequency / ($total + 10));
            ksort($tags);
        }
        return $tags;
    }

    public function getTags($limit = 100)
    {
        $models = $this->with('post')->findAll(array(
            'order' => 'frequency DESC',
            'limit' => $limit,
        ));
        return $models;
    }

    public function getTagsCount($t)
    {
//		$tags =$this->findAllByAttributes(array('name'=>$t));
//		return $tags;
        return $this->findAll(['name' => $t]);
    }

    public function suggestTags($keyword, $limit = 20)
    {
        $tags = $this->findAll(array(
            'condition' => 'name LIKE :keyword',
            'order' => 'frequency DESC, Name',
            'limit' => $limit,
            'params' => array(
                ':keyword' => '%' . strtr($keyword, array('%' => '\%', '_' => '\_', '\\' => '\\\\')) . '%',
            ),
        ));
        return $tags;

//		$tags=$this->findAll(array(
//			'condition'=>'name LIKE :keyword',
//			'order'=>'frequency DESC, Name',
//			'limit'=>$limit,
//			'params'=>array(
//				':keyword'=>'%'.strtr($keyword,array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%',
//			),
//		));
//		$names=array();
//		foreach($tags as $tag)
//			$names[]=$tag->name;
//		return $names;
    }

    public function updateTags($oldTags, $newTags, $uid)
    {
        $this->addTags(array_values(array_diff($newTags, $oldTags)), $uid);
        $this->removeTags(array_values(array_diff($oldTags, $newTags)));
    }

    public function addTags($tags, $uid)
    {
//        $criteria = new CDbCriteria;
//        $criteria->addInCondition('name', $tags);
//        $this->updateCounters(array('frequency' => 1), $criteria);
        self::updateAllCounters(['frequency' => 1], ['name' => $tags]);
        foreach ($tags as $name) {
            if (!self::find()->where('name=:name', [':name'=>$name])->exists()) {
                $tag = new Tag;
                $tag->name = $name;
                $tag->uid = $uid;
                $tag->frequency = 1;
                $tag->save();
            }
        }
    }

    public function removeTags($tags)
    {
        if (empty($tags))
            return;
        self::updateAllCounters(['frequency' => 1], ['name' => $tags]);
        $this->deleteAll('frequency<=0');
    }

}
