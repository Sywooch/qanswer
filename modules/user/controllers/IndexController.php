<?php
namespace app\modules\user\Controllers;

use app\components\BaseController;
use app\components\DateFormatter;
use app\modules\user\models\User;
use Yii;

use yii\data\Pagination;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author xuesong
 */
class IndexController extends BaseController
{
    public $layout = '/column1';
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab', 'reputation');
        $votesTh = Yii::$app->params['pages']['userIndexVoters'];
        $editsTh = Yii::$app->params['pages']['userIndexEditors'];
        $repsTh = Yii::$app->params['pages']['userIndexReps'];

        $menu = array(
            'items' => array(
                array('label' => '威望', 'url' => array('index/index', 'tab' => 'reputation'), 'options' => array('title' => '威望从高到低')),
                array('label' => '新用户', 'url' => array('index/index', 'tab' => 'newusers'), 'options' => array('title' => '最近30天加入的用户')),
                array('label' => '投票者', 'url' => array('index/index', 'tab' => 'voters'), 'options' => array('title' => "投票超过{$votesTh}次用户列表")),
                array('label' => '编辑者', 'url' => array('index/index', 'tab' => 'editors'), 'options' => array('title' => "编辑超过{$editsTh}个帖子的用户")),
            ),
            'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
        );
        $renderParams = [];
        $userQuery = User::find();
        switch ($tab) {
            case 'newusers':
                $sort = Yii::$app->request->get('sort', 'reputation');
                $submenu = $this->getNewusersSubmenu();
                $order = $this->getOrder($tab, $sort);
                $registertime = time() - 30 * 86400;

                $userQuery->where('registertime>:time', [':time' => $registertime])->joinWith('stats')->with('profile')->orderBy($order);
                break;
            case 'voters':
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->getSubmenu($tab);
                $order = $this->getOrder($tab, $filter);
                
                $userQuery->joinWith(['stats' => function($query) use ($votesTh) {
                    $query->where('upvotecount>:votes', [':votes' => $votesTh]);
                }])->with('profile')->orderBy($order);
                
                $renderParams = ['filter' => $filter];
                break;

            case 'editors':
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->getSubmenu($tab);
                $order = $this->getOrder($tab, $filter);

                $userQuery->joinWith(['stats' => function($query) use ($editsTh) {
                    $query->where('editcount>:edits', [':edits' => $editsTh]);
                }])->with('profile')->orderBy($order);
                
                $renderParams = ['filter' => $filter];
                break;
            case 'reputation' :
            default:
                $filter = Yii::$app->request->get('filter', 'week');
                $menu['items'][0]['options']['class'] = 'active';
                $submenu = $this->getSubmenu($tab);
                $order = $this->getOrder($tab, $filter);
                $userQuery->where('reputation>:reputation', [':reputation' => $repsTh]);
                $userQuery->joinWith('stats')->with('profile')->orderBy($order);
                $renderParams = ['filter' => $filter];
                break;
        }
        $this->title = "用户列表";
        
        $userQuery->andFilterWhere(['like','username',Yii::$app->request->get('search')]);
        $totalCount = $userQuery->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
	    $pages->pageSize = Yii::$app->params['pages']['userIndexPagesize'];
        $users = $userQuery->offset($pages->offset)->limit($pages->limit)->all();
        
        return $this->render('index', \yii\helpers\ArrayHelper::merge([
            'users' => $users,
            'menu' => $menu,
            'submenu' => $submenu,
            'pages' => $pages,
            'tab' => $tab
        ],['params' => $renderParams]));
    }
       
    private function getSubmenu($tab)
    {
        $week = DateFormatter::weekFirstDay();
        $month = DateFormatter::monthFirstDay();
        $quarter = DateFormatter::quarterFirstDay();
        $year = DateFormatter::yearFirstDay();

        $submenu = array(
            'items' => array(
                array('label' => '所有', 'url' => array('index/index', 'tab' => $tab, 'filter' => 'all'), 'options' => array('title' => "所有用户列表")),
                array('label' => '年', 'url' => array('index/index', 'tab' => $tab, 'filter' => 'year'), 'options' => array('title' => "{$year}至今天")),
                array('label' => '季', 'url' => array('index/index', 'tab' => $tab, 'filter' => 'quarter'), 'options' => array('title' => "{$quarter}至今天")),
                array('label' => '月', 'url' => array('index/index', 'tab' => $tab, 'filter' => 'month'), 'options' => array('title' => "{$month}至今天")),
                array('label' => '周', 'url' => array('index/index', 'tab' => $tab, 'filter' => 'week'), 'options' => array('title' => "{$week} 至今天")),
            ),
            'options' => ['id' => 'tabs-interval', 'class' => 'subtabs'],
        );
        $filter = Yii::$app->request->get('filter','week');
        if ($filter === 'week') {
             $submenu['items'][4]['options']['class'] = 'active';
        }
        if (isset($_GET['search'])) {
            for ($i = 0; $i < 5; $i++) {
                $submenu['items'][$i]['url']['search'] = $_GET['search'];
            }
        }
        return $submenu;
    }    

    private function getOrder($tab, $filter)
    {
        $orders = array(
            'voters' => array(
                'all' => ['userstat.upvotecount' => SORT_DESC],
                'year' => ['yearvotes' => SORT_DESC],
                'quarter' => ['quartervotes' => SORT_DESC],
                'month' => ['monthvotes' => SORT_DESC],
                'week' => ['weekvotes' => SORT_DESC],
            ),
            'editors' => array(
                'all' => ['editcount' => SORT_DESC],
                'year' => ['yearedits' => SORT_DESC],
                'quarter' => ['quarteredits' => SORT_DESC],
                'month' => ['monthedits' => SORT_DESC],
                'week' => ['weekedits' => SORT_DESC],
            ),
            'reputation' => array(
                'all' => ['editcount' => SORT_DESC],
                'year' => ['yearreps' => SORT_DESC],
                'quarter' => ['quarterreps' => SORT_DESC],
                'month' => ['monthreps' => SORT_DESC],
                'week' => ['user_stat.weekreps' => SORT_DESC],
            ),
            'newusers' => array(
                'registertime' => ['registertime' => SORT_DESC],
                'reputation' => ['reputation' => SORT_DESC],
            )
        );
        return $orders[$tab][$filter];
    }
    
    private function getNewusersSubmenu()
    {
        $submenu = array(
            'items' => array(
                array('label' => '注册日期', 'url' => array('index/index', 'tab' => 'newusers', 'sort' => 'registertime'), 'options' => array('title' => '注册日期')),
                array('label' => '威望', 'url' => array('index/index', 'tab' => 'newusers', 'sort' => 'reputation'), 'options' => array('title' => '威望从高到低')),
            ),
            'options' => ['id' => 'tabs-interval', 'class' => 'subtabs'],
        );
        $search = Yii::$app->request->get('search');
        if ($search) {
            for ($i = 0; $i < 2; $i++) {
                $submenu['items'][$i]['url']['search'] = $search;
            }
        }
        $sort = Yii::$app->request->get('sort', 'reputation');
        if ($sort === 'reputation') {
            $submenu['items'][1]['options']['class'] = 'active';
        }
        return $submenu;
    }    
}
