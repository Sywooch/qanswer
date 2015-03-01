<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%user_stat}}".
 *
 * @property integer $id
 * @property integer $qcount
 * @property integer $acount
 * @property integer $viewcount
 * @property integer $upvotecount
 * @property integer $downvotecount
 * @property integer $commentcount
 * @property integer $editcount
 * @property integer $weekvotes
 * @property integer $monthvotes
 * @property integer $quartervotes
 * @property integer $yearvotes
 * @property integer $weekedits
 * @property integer $monthedits
 * @property integer $quarteredits
 * @property integer $yearedits
 * @property integer $weekreps
 * @property integer $monthreps
 * @property integer $quarterreps
 * @property integer $yearreps
 */
class UserStat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_stat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'qcount', 'acount', 'viewcount', 'upvotecount', 'downvotecount', 'commentcount', 'editcount', 'weekvotes', 'monthvotes', 'quartervotes', 'yearvotes', 'weekedits', 'monthedits', 'quarteredits', 'yearedits', 'weekreps', 'monthreps', 'quarterreps', 'yearreps'], 'required'],
            [['id', 'qcount', 'acount', 'viewcount', 'upvotecount', 'downvotecount', 'commentcount', 'editcount', 'weekvotes', 'monthvotes', 'quartervotes', 'yearvotes', 'weekedits', 'monthedits', 'quarteredits', 'yearedits', 'weekreps', 'monthreps', 'quarterreps', 'yearreps'], 'integer'],
            [['id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'qcount' => Yii::t('app', 'Qcount'),
            'acount' => Yii::t('app', 'Acount'),
            'viewcount' => Yii::t('app', 'Viewcount'),
            'upvotecount' => Yii::t('app', 'Upvotecount'),
            'downvotecount' => Yii::t('app', 'Downvotecount'),
            'commentcount' => Yii::t('app', 'Commentcount'),
            'editcount' => Yii::t('app', 'Editcount'),
            'weekvotes' => Yii::t('app', 'Weekvotes'),
            'monthvotes' => Yii::t('app', 'Monthvotes'),
            'quartervotes' => Yii::t('app', 'Quartervotes'),
            'yearvotes' => Yii::t('app', 'Yearvotes'),
            'weekedits' => Yii::t('app', 'Weekedits'),
            'monthedits' => Yii::t('app', 'Monthedits'),
            'quarteredits' => Yii::t('app', 'Quarteredits'),
            'yearedits' => Yii::t('app', 'Yearedits'),
            'weekreps' => Yii::t('app', 'Weekreps'),
            'monthreps' => Yii::t('app', 'Monthreps'),
            'quarterreps' => Yii::t('app', 'Quarterreps'),
            'yearreps' => Yii::t('app', 'Yearreps'),
        ];
    }
}
