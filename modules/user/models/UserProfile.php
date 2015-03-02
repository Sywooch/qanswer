<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property integer $id
 * @property string $realname
 * @property string $birthday
 * @property string $location
 * @property string $website
 * @property string $aboutme
 * @property integer $complete
 * @property string $preference
 * @property string $unpreference
 */
class UserProfile extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['realname', 'birthday', 'location', 'aboutme', 'complete', 'preference', 'unpreference'], 'required'],
            [['birthday'], 'safe'],
            [['aboutme', 'preference', 'unpreference'], 'string'],
            [['complete'], 'integer'],
            [['realname'], 'string', 'max' => 20],
            [['location'], 'string', 'max' => 250],
            [['website'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'realname' => Yii::t('app', 'Realname'),
            'birthday' => Yii::t('app', 'Birthday'),
            'location' => Yii::t('app', 'Location'),
            'website' => Yii::t('app', 'Website'),
            'aboutme' => Yii::t('app', 'Aboutme'),
            'complete' => Yii::t('app', 'Complete'),
            'preference' => Yii::t('app', 'Preference'),
            'unpreference' => Yii::t('app', 'Unpreference'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->preference = (is_array($this->preference)) ? implode(' ', $this->preference) : $this->preference;
        $this->unpreference = (is_array($this->unpreference)) ? implode(' ', $this->unpreference) : $this->unpreference;
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->preference = empty($this->preference) ? array() : array_map('trim', explode(' ', $this->preference));
        $this->unpreference = empty($this->unpreference) ? array() : array_map('trim', explode(' ', $this->unpreference));
        if ($this->birthday == '0000-00-00') {
            $this->birthday = '';
        }
    }

}
