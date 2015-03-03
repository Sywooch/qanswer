<?php
namespace app\Controllers;
use app\components\BaseController;
use app\models\Badge;
use Yii;
use yii\data\Pagination;

class BadgesController extends BaseController
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
				'actions'=>array('index','view'),
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

	public function actionView() 
    {
		$badge = Badge::findOne(Yii::$app->request->get('id'));
		$this->title = $badge->name.'徽章';
        
        $awardQuery = \app\models\Award::find()->where('badgeid=:badgeid',[':badgeid'=>$badge->id]);

		$total = $awardQuery->count();
		$pages = new Pagination(['totalCount'=>$total]);
	    $pages->pageSize= Yii::$app->params['pages']['badgeAwardsPagesize'];
        $awards = $awardQuery->orderBy(['time'=>SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all();

		return $this->render('view',array(
			'badge'		=> $badge,
			'awards'	=> $awards,
			'pages'		=> $pages
		));
	}


	public function actionIndex()
	{
		$badges = Badge::find()->all();
		return $this->render('index',array('badges'=>$badges));
	}


}
