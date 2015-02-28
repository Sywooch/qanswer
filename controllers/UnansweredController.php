<?php
namespace app\Controllers;
use app\components\BaseController;

use app\models\Post;
use app\models\QuestionTag;
use yii\data\Pagination;
use Yii;

class UnansweredController extends BaseController
{
	public $layout='column1';


	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}


	/**
	 * 设置接入控制规则
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to access 'index' and 'view' actions.
				'actions'=>array('index'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated users to access all actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	public function actionIndex() 
    {
		$this->title = "等待回答";
		$submenu = array(
			'items'=>array(
				array('label'=>'我的标签', 	'url'=>array('unanswered/index','tab'=>'mytags'),	'itemOptions'=>array('title'=>'我关注的标签'),'visible'=>!Yii::$app->user->isGuest),
				array('label'=>'最新', 		'url'=>array('unanswered/index','tab'=>'newest'),	'itemOptions'=>array('title'=>'最新的')),
				array('label'=>'投票', 		'url'=>array('unanswered/index','tab'=>'votes'),	'itemOptions'=>array('title'=>'投票从高到低')),
				array('label'=>'无回答', 	'url'=>array('unanswered/index','tab'=>'noanswers'),'itemOptions'=>array('title'=>'无回答')),
			),
			'id'	=> 'tabs'
		);

//		$criteria=new CDbCriteria(array(
//			'condition'=>'idtype="'.Post::IDTYPE_Q.'"',
//		));
//		$criteria->addCondition('aupvotes=0');
        $questionQuery = Post::find()->where('idtype=:idtype AND aupvotes=0', [':idtype'=>Post::IDTYPE_Q]);

		$tab = isset($_GET['tab']) ? $_GET['tab'] : 0;
		switch ($tab) {
			case 'mytags':
				if (Yii::$app->user->isGuest) {
					$this->redirect(Yii::app()->user->loginUrl);
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
                    
                    $qt = QuestionTag::find()->select('postid')->distinct()->where(['tag'=>$tags])->all();
					foreach($qt as $t) {
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
                $questionQuery->orderBy(['createtime'=>SORT_DESC]);
				break;
		}
		$total = $questionQuery->count();;
		$pages=new Pagination(['totalCount'=>$total]);
//	    $pages->pageSize= Yii::app()->params['pages']['questionsIndex'];
//	    $pages->applyLimit($criteria);

		$questions = $questionQuery->with('author','poststate')->all();
		return $this->render('index',array('questions'=>$questions,'pages'=>$pages,'submenu'=>$submenu));
	}
}
