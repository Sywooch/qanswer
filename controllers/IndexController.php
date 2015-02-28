<?php
namespace app\Controllers;

use app\components\BaseController;
use app\models\Post;
use yii\data\Pagination;
use Yii;
use app\components\QuestionsRecommend;

class IndexController extends BaseController
{
	public $layout='column1';
	public $h1 = '';

	private $_model;

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
				'actions'=>array('index','error'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() 
    {
		$description = "乐问是一个专业的、开放的编程问答网站，编程爱好者可以在乐问自由的提问和回答与编程相关的问题，可以浏览、搜索乐问上所有的问题和答案。通过威望的提升，获取额外的权限来参与管理乐问网。";
//		Yii::app()->clientScript->registerMetaTag($description,"description");
//		$criteria=new CDbCriteria(array(
//			'condition'=>'idtype='.'"question"',
//		));
        $questionQuery = \app\models\Post::find()->where('idtype=:idtype', [':idtype'=>  Post::IDTYPE_Q]);
		$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
		switch ($tab) {
			case 'bounty':
				$this->title = "悬赏问题";
				$criteria->select = 't.*,bounty.amount as bountyAmount';
				$criteria->order = 'score DESC';
				$criteria->join	 = "left join {{bounty}} on bounty.questionid = t.id";
				$criteria->addCondition(array('bounty.status='.Bounty::STATUS_OPEN,'bounty.endtime>'.time()));
				break;
			case 'week':
				$this->title = "本周热门问题";
				$qids = QuestionsRecommend::weekHotQuestions();
				$criteria=new CDbCriteria(array(
				));
				$criteria->addInCondition('t.id',$qids);
				break;
			case 'month':
				$this->title = "本月热门问题";
				$qids = QuestionsRecommend::monthHotQuestions();
				$criteria=new CDbCriteria(array(
				));
				$criteria->addInCondition('t.id',$qids);
				break;
			case 'interesting':		//必须登录会员才能访问
				if (!Yii::app()->user->isGuest) {
					$utags = array();
					$tags = $this->me->profile->preference;
					foreach ($this->me->tags as $tag) {
						$utags[] = $tag['tag'];
					}
					$tags = array_unique(array_merge($tags,$utags));
					$criteria=new CDbCriteria(array());
					$criteria->select = "postid";
					$criteria->distinct = true;
					$criteria->addInCondition("tag",$tags);
					$qt = QuestionTag::Model()->findAll($criteria);
					foreach($qt as $t) {
						$ids[] = $t->postid;
					}
					$criteria=new CDbCriteria(array());
					$criteria->addInCondition("t.id",$ids);
				} else {
					$this->redirect(Yii::app()->user->loginUrl);
				}
				break;
			case 'hot':
			default:
				$qids = QuestionsRecommend::recentHotQuestions();
//				$criteria=new CDbCriteria(array(
//				));
//				$criteria->addInCondition('t.id',$qids);
                $questionQuery->andWhere(['post.id'=>$qids]);
				break;
		}

		$total = $questionQuery->count();

		$pages = new Pagination(['totalCount' => $total]);
//        $pages->limit = Yii::$app->params['pages']['questionsIndex'];

        $questions = $questionQuery->limit($pages->limit)->offset($pages->offset)->with('author')->all();
//		$questions = Post::Model()->with('author')->findAll($criteria);
		return $this->render('index',array(
			'questions' =>$questions,
			'pages'		=>$pages,
		));
	}
}