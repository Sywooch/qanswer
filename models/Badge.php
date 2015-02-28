<?php

namespace app\models;

use yii\db\ActiveRecord;

class Badge extends ActiveRecord
{

    const GOLD = 1;
    const SILVER = 2;
    const BRONZE = 3;

    public $classname;
    public $typename;
    public static $keyModels = array();
    public static $markerModels = array();

    public static function tableName()
    {
        return '{{%badge}}';
    }

    public function afterFind()
    {
        $this->classname = 'badge' . $this->type;
        switch ($this->type) {
            case self::GOLD :
                $this->typename = "金徽章";
                break;
            case self::SILVER :
                $this->typename = "银徽章";
                break;
            case self::BRONZE :
                $this->typename = "铜徽章";
                break;
        }
    }

    /**
     * 获取某个徽章基本信息，
     * @todo 所有徽章列表需要缓存
     * @param int $badgeid
     * @param string $by
     */
    public static function getBadge($badgeid, $by = 'id')
    {
        if (empty(self::$keyModels)) {
            $models = self::find()->all();
            foreach ($models as $model) {
                self::$keyModels[$model->id] = $model;
                self::$markerModels[$model->marker] = $model;
            }
        }
        return ($by == 'id') ? self::$keyModels[$badgeid] : self::$markerModel[$badgeid];
    }
}