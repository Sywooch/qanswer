<?php

namespace app\modules\question\controllers;

use app\components\BaseController;
use app\models\Post;
use app\models\QuestionTag;
use yii\data\Pagination;
use Yii;

class UnansweredController extends BaseController
{

    public $layout = '//column1';

    public function actionIndex()
    {
        $submenu = array(
            'items' => array(
                array('label' => '我的标签', 'url' => array('unanswered/index', 'tab' => 'mytags'), 'options' => array('title' => '我关注的标签'), 'visible' => !Yii::$app->user->isGuest),
                array('label' => '最新', 'url' => array('unanswered/index', 'tab' => 'newest'), 'options' => array('title' => '最新的')),
                array('label' => '投票', 'url' => array('unanswered/index', 'tab' => 'votes'), 'options' => array('title' => '投票从高到低')),
                array('label' => '无回答', 'url' => array('unanswered/index', 'tab' => 'noanswers'), 'options' => array('title' => '无回答')),
            ),
            'options' => ['class' => 'nav nav-tabs', 'id' => 'tabs']
        );

        $questionQuery = Post::find()->where('idtype=:idtype AND aupvotes=0', [':idtype' => Post::IDTYPE_Q]);

        $tab = Yii::$app->request->get('tab', 'mytags');
        switch ($tab) {
            case 'mytags':
                if (Yii::$app->user->isGuest) {
                    $this->redirect(Yii::$app->user->loginUrl);
                } else {
                    $utags = array();
                    $tags = Yii::$app->user->identity->tags;
                    foreach ($tags as $tag) {
                        $utags[] = $tag['tag'];
                    }
//					$tags = array_unique(array_merge($tags,$utags));
                    $tags = $utags;
//					$criteria=new CDbCriteria(array());
//					$criteria->select = "postid";
//					$criteria->distinct = true;
//					$criteria->addInCondition("tag",$tags);
//					$qt = QuestionTag::Model()->findAll($criteria);

                    $qt = QuestionTag::find()->select('postid')->distinct()->where(['tag' => $tags])->all();
                    foreach ($qt as $t) {
                        $ids[] = $t->postid;
                    }
//					$criteria=new CDbCriteria(array());
//					$criteria->addCondition('aupvotes=0');
//					$criteria->addInCondition("t.id",$ids);
                }
                break;
            case 'votes':
//				$submenu['items'][2]['itemOptions']['class'] = 'youarehere';
                $criteria->order = 'score DESC';
                break;
            case 'noanswers':
//				$submenu['items'][3]['itemOptions']['class'] = 'youarehere';
                $criteria->addCondition('answercount=0');
                break;
            case 'newest':
            default:
                $submenu['items'][1]['itemOptions']['class'] = 'youarehere';
                $criteria->order = 'createtime DESC';
                $questionQuery->orderBy(['createtime' => SORT_DESC]);
                break;
        }
        $total = $questionQuery->count();
        $pages = new Pagination(['totalCount' => $total]);
        $pages->pageSize = Yii::$app->params['pages']['questionsIndex'];

        $questions = $questionQuery->with('author', 'poststate')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('index', [
            'questions' => $questions, 
            'pages' => $pages, 
            'submenu' => $submenu,
            'tab' => $tab
        ]);
    }
}