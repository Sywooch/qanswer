<?php

namespace app\Controllers;

use app\components\BaseController;
use app\models\Tag;
use app\models\QuestionTag;
use app\models\Post;

class FilterController extends BaseController
{

    private $_model;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('tagindex', 'tags', 'diff'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated users to access all actions
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionTags()
    {
        $s = '';
        $tags = Tag::Model()->suggestTags($_GET['q']);
        foreach ($tags as $tag) {
            $s .= "{$tag['name']}|{$tag['frequency']}\n";
        }
        echo $_GET['callback'] . "(" . CJSON::encode($s) . ")";
    }

    public function actionDiff()
    {
        $a = array(
            "eee",
            "bbb",
            "ccc"
        );
        $b = array(
            "eeee",
            "bbb",
            "cc",
        );
        echo TextDiff::compare($a, $b);
    }

    public function actionTagindex()
    {
        $filter = $_POST['filter'];
//        $criteria = new CDbCriteria(array(
//            'limit' => 70,
//            'order' => 'frequency',
//        ));
//        $criteria->addSearchCondition("name", $filter);
//        $tags = Tag::Model()->findAll($criteria);
        $t = [];
        $tags = Tag::find()->where(['like', 'name', $filter])->orderBy(['frequency' => SORT_DESC])->limit(70)->all();
        foreach ($tags as $tag) {
            $t[] = $tag->name;
        }

        $time = time();
        $day7 = $time - 86400 * 7;
//        $criteria = new CDbCriteria(array(
//            'select' => "tag,count(*) as total",
//            'join' => "left join post on post.id = t.postid",
//            'group' => "t.tag",
//        ));
//        $criteria->addInCondition("t.tag", $t)->addCondition("post.createtime>" . $day7);
//        $weekTags = QuestionTag::Model()->findAll($criteria);
        $weekTags = QuestionTag::find()->select(['tag', 'count(*) as total'])
                                       ->leftJoin(Post::tableName(), ['id' => 'postid'])
                                       ->groupBy('{{%questiontag}}.tag')
                                       ->where(['tag' => $t])
                                       ->andWhere('{{%post}}.createtime>:time', [':time' => $day7])
                                       ->all();
        $weeks = [];
        foreach ($weekTags as $w) {
            $weeks[$w->tag] = $w->total;
        }

        $day = $time - 86400;
//        $criteria = new CDbCriteria(array(
//            'select' => "tag,count(*) as total",
//            'join' => "left join post on post.id = t.postid",
//            'group' => "t.tag",
//        ));
//        $criteria->addInCondition("t.tag", $t)->addCondition("post.createtime>" . $time);
//        $dayTags = QuestionTag::Model()->findAll($criteria);
        $dayTags = QuestionTag::find()->select(['tag', 'count(*) as total'])
                                       ->leftJoin(Post::tableName(), ['id' => 'postid'])
                                       ->groupBy(QuestionTag::tableName().'.tag')
                                       ->where(['tag' => $t])
                                       ->andWhere(Post::tableName().'.createtime>:time', [':time' => $day])
                                       ->all();
        $days = [];
        foreach ($dayTags as $w) {
            $days[$w->tag] = $w->total;
        }
        echo $this->renderPartial('tag-index', array(
            'tags' => $tags,
            'weeks' => $weeks,
            'days' => $days
        ));
    }

}
