<?php

namespace app\modules\user\controllers;

use app\components\BaseController;
use Yii;
use app\modules\user\models\User;
use app\modules\user\models\UserProfile;
use app\components\DateFormatter;
use yii\data\Pagination;
use app\models\Post;
use app\models\UserTags;
use app\models\Award;
use app\models\Activity;
use app\models\Repute;
use app\models\Vote;

class ViewController extends BaseController
{
    public $layout = '//column1';

    public function actionIndex()
    {
        $id = Yii::$app->request->get('id');
        $user = User::find()->with('stats', 'profile')->where('id=:id', [':id' => Yii::$app->request->get('id')])->one();

        $this->title = $user->username;

        $ip = ip2long(Yii::$app->request->userIP);
        $cacheId = "U_" . $ip . "_" . $user->id;
        if (!Yii::$app->cache->get($cacheId)) {
            Yii::$app->cache->set($cacheId, 1, 5 * 60);
            $user->stats->updateCounters(array('viewcount' => 1), 'id=:id', array(':id' => $user->id));
        }

        $tab = Yii::$app->request->get('tab','stats');
        switch ($tab) {
            case 'activity':
                $url = array('/user/view/activity', 'do' => 'show', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userActivityPagesize'], 'uid' => $id);
                $submenu = array(
                    'items' => array(
                        array('label' => '所有', 'url' => array_merge($url, array('filter' => 'all')), 'options' => array('class' => 'active', 'title' => '所有')),
                        array('label' => '采纳', 'url' => array_merge($url, array('filter' => 'accepts')), 'options' => array('title' => '采纳答案')),
                        array('label' => '问题', 'url' => array_merge($url, array('filter' => 'posts')), 'options' => array('title' => '提问或答案帖子')),
                        array('label' => '徽章', 'url' => array_merge($url, array('filter' => 'badges')), 'options' => array('title' => '徽章')),
                        array('label' => '评论', 'url' => array_merge($url, array('filter' => 'comments')), 'options' => array('title' => '评论')),
                        array('label' => '修订', 'url' => array_merge($url, array('filter' => 'revisions')), 'options' => array('title' => '版本')),
                    ),
                    'options' => ['id'=>'tabs-activity', 'class' => 'subtabs'],
                );

                $activityQuery = Activity::find()->where('uid=:uid', [':uid' => $id])
                                                 ->orderBy(['time' => SORT_DESC])
                                                 ->andWhere(['type' => ['comment', 'ask', 'answer', 'posts', 'accept', 'revise', 'award']]);
                $totalCount = $activityQuery->count();
                
                $filter = Yii::$app->request->get('filter', 'all');
                $pages = new Pagination(['totalCount' => $totalCount]);
                $pages->pageSize = Yii::$app->params['pages']['userActivityPagesize'];
                $pages->params = ['do'=>'show', 'filter' => $filter, 'uid' => $id];
                $pages->route = 'user/view/activity';

                $activities = $activityQuery->all();
                $params = [
                    'user' => $user,
                    'activities' => $activities,
                    'submenu' => $submenu,
                    'pages' => $pages,
                    'tab' => $tab
                ];
                break;
            
            case 'reputation':
                $reputeQuery = Repute::find()->where(['{{%repute}}.uid' => $id])->orderBy(['time' => SORT_DESC]);
                
                $startDate = Yii::$app->request->get('startDate');
                if ($startDate) {
                    $reputeQuery->andWhere('time>:time', [':time' => strtotime($startDate)]);
                }

                $total = $reputeQuery->count();

                $pages = new Pagination(['totalCount' => $total]);
                $pages->pageSize = Yii::$app->params['pages']['userReputationPagesize'];
                $pages->params = ['do'=>'show', 'sort' => 'time', 'uid' => $id];
                $pages->route = 'user/view/reputation';

                $reputes = $reputeQuery->joinWith('question')->offset($pages->offset)->limit($pages->limit)->all();
                $formattedReputes = Repute::formatReputes($reputes);
                $params = [
                    'user' => $user,
                    'reputes' => $formattedReputes,
                    'pages' => $pages,
                    'tab' => $tab
                ];
                break;
                
            case 'favorites':
                $url = array('users/activity', 'do' => 'favorites', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userFavoritePagesize'], 'uid' => $id);
                $submenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($url, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($url, array('sort' => 'newest')), 'options' => array('title' => '按照创建日期')),
                        array('label' => '查看', 'url' => array_merge($url, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($url, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动')),
                        array('label' => '时间', 'url' => array_merge($url, array('sort' => 'added')), 'options' => array('title' => '按照加入收藏时间')),
                    ),
                    'options' => ['id'=>'tabs-favorite-user', 'class' => 'subtabs'],
                );
                $sort = Yii::$app->request->get('sort', 'added');
                switch ($sort) {
                    case 'votes':
                        $submenu['items'][0]['options']['class'] = 'active';
                        $order = "{{%post}}.score DESC";
                        break;
                    case 'newest':
                        $submenu['items'][1]['options']['class'] = 'active';
                        $order = "{{%post}}.createtime DESC";
                        break;
                    case 'views':
                        $submenu['items'][2]['options']['class'] = 'active';
                        $order = "{{%post}}.viewcount DESC";
                        break;
                    case 'recent':
                        $submenu['items'][3]['options']['class'] = 'active';
                        $order = "{{%post}}.activity DESC";
                        break;
                    case 'added':
                        $submenu['items'][4]['options']['class'] = 'active';
                        $order = "favtime DESC";
                        break;
                }

                $voteQuery = Vote::find()->where('{{%vote}}.uid=:uid AND {{%vote}}.fav=:fav', [':uid' => $id, ':fav' => 1])
                                         ->orderBy($order);
                $total = $voteQuery->count();
                $pages = new Pagination(['totalCount' => $total]);
                $pages->pageSize = 3;//Yii::$app->params['pages']['userFavoritePagesize'];

                $pages->params = ['do' => 'favorites', 'uid' => $id, 'sort' => $sort];
                $pages->route = 'user/view/stats';
                $favs = $voteQuery->joinWith('question')->offset($pages->offset)->limit($pages->limit)->all();
                
                $params = [
                    'user' => $user,
                    'submenu' => $submenu,
                    'favs' => $favs,
                    'pages' => $pages,
                    'tab' => $tab
                ];
                break;
            
            default:
                //answers
                $aUrl = array('users/stats', 'do' => 'answers', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userAnswerPagesize'], 'uid' => $id);
                $aSubmenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($aUrl, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($aUrl, array('sort' => 'newest')), 'options' => array('title' => '按照创建日期')),
                        array('label' => '查看', 'url' => array_merge($aUrl, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($aUrl, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动时间')),
                    ),
                    'options' => ['id' => 'tabs-answer-user','class' => 'subtabs']
                );
                $aSubmenu['items'][0]['options']['class'] = 'active';

                $answerQuery = Post::find()->orderBy(['post.score' => SORT_DESC])
                                           ->where('post.idtype=:idtype AND post.uid=:uid', [':idtype' => Post::IDTYPE_A, ':uid' => $user->id]);
                $answersPages = new Pagination(['totalCount' => $answerQuery->count()]);
			    $answersPages->pageSize = Yii::$app->params['pages']['userAnswerPagesize'];
                $answers = $answerQuery->with('question')->offset($answersPages->offset)->limit($answersPages->limit)->all();
                
                //questions
                $qUrl = array('users/stats', 'do' => 'questions', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userQuestionPagesize'], 'uid' => $id);
                $qSubmenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($qUrl, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($qUrl, array('sort' => 'newest')), 'options' => array('title' => '按照创建日期')),
                        array('label' => '查看', 'url' => array_merge($qUrl, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($qUrl, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动时间')),
                    ),
                    'options' => ['id' => 'tabs-question-user','class' => 'subtabs']
                );
                $qSubmenu['items'][0]['options']['class'] = 'active';

                $questionQuery = Post::find()->orderBy(['post.score' => SORT_DESC])
                                             ->where('post.idtype=:idtype AND post.uid=:uid', [':idtype' => Post::IDTYPE_Q, ':uid' => $user->id]);

                $qPages = new Pagination(['totalCount' => $questionQuery->count()]);
			    $qPages->pageSize= Yii::$app->params['pages']['userQuestionPagesize'];
                $questions = $questionQuery->offset($qPages->offset)->limit($qPages->limit)->all();

                //usertags
                $userTagsQuery = UserTags::find()->where('uid=:uid', [':uid' => $user->id])
                                                 ->orderBy(['totalcount' => SORT_DESC]);

                $tPages = new Pagination(['totalCount' => $userTagsQuery->count()]);
			    $tPages->pageSize = Yii::$app->params['pages']['userTagsPagesize'];
                $usertags = $userTagsQuery->all();
                //徽章
                $awards = Award::find()->select('count(*) as badgecount,uid,badgeid')
                        ->where('uid=:uid', [':uid' => $user->id])
                        ->groupBy('badgeid')
                        ->with('badge')
                        ->all();

                $favs = array();
                $params = [
                    'user' => $user,
                    'favs' => $favs,
                    'answers' => $answers,
                    'aSubmenu' => $aSubmenu,
                    'aPages' => $answersPages,
                    'questions' => $questions,
                    'qSubmenu' => $qSubmenu,
                    'qPages' => $qPages,
                    'tPages' => $tPages,
                    'usertags' => $usertags,
                    'awards' => $awards,
                    'tab' => $tab,
                ];
                break;
        }
        
        return $this->render('index',$params);
    }

    /**
     * 统计信息
     */
    public function actionStats()
    {
        $do = Yii::$app->request->get('do');
        $sort = Yii::$app->request->get('sort');
        $uid = Yii::$app->request->get('uid');

        switch ($do) {
            case 'favorites' :
                $sql = array('users/stats', 'do' => 'favorites', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userFavoritePagesize'], 'uid' => $uid);
                $submenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($sql, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($sql, array('sort' => 'newest')), 'options' => array('title' => '按照提问时间排序')),
                        array('label' => '查看', 'url' => array_merge($sql, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($sql, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动')),
                        array('label' => '时间', 'url' => array_merge($sql, array('sort' => 'added')), 'options' => array('title' => '按照加入收藏时间')),
                    ),
                    'options' => ['id' => 'tabs-favorite-user', 'class' => 'subtabs']
                );

                switch ($sort) {
                    case 'votes':
                        $submenu['items'][0]['options']['class'] = 'active';
                        $order = "{{%post}}.score DESC";
                        break;
                    case 'newest':
                        $submenu['items'][1]['options']['class'] = 'active';
                        $order = "{{%post}}.createtime DESC";
                        break;
                    case 'views':
                        $submenu['items'][2]['options']['class'] = 'active';
                        $order = "{{%post}}.viewcount DESC";
                        break;
                    case 'recent':
                        $submenu['items'][3]['options']['class'] = 'active';
                        $order = "{{%post}}.activity DESC";
                        break;
                    case 'added':
                        $submenu['items'][4]['options']['class'] = 'active';
                        $order = "{{%vote}}.favtime DESC";
                        break;
                }

                $voteQuery = Vote::find()->where('{{%vote}}.uid=:uid AND {{%vote}}.fav=1', [':uid' => $uid])
                                         ->orderBy($order);
                
                $total = $voteQuery->count();
                $pages = new Pagination(['totalCount' => $total]);
                $pages->pageSize = 3; //Yii::$app->params['pages']['userFavoritePagesize'];
                $sort = Yii::$app->request->get('sort', 'added');
                $pages->params = ['do' => $do, 'sort' => $sort, 'uid' => $uid];
                $pages->route = 'user/view/stats';

                $favs = $voteQuery->joinWith('question')->offset($pages->offset)->limit($pages->limit)->all();
                return $this->renderPartial('_stats-favs', array('submenu' => $submenu, 'favs' => $favs, 'pages' => $pages));
            case 'questions' :
                $url = array('users/stats', 'do' => 'questions', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userQuestionPagesize'], 'uid' => $uid);
                $submenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($url, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($url, array('sort' => 'newest')), 'options' => array('title' => '按照创建日期')),
                        array('label' => '查看', 'url' => array_merge($url, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($url, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动时间')),
                    ),
                    'options' => ['id' => 'tabs-question-user', 'class' => 'subtabs']
                );
                switch ($sort) {
                    case 'votes':
                        $submenu['items'][0]['options']['class'] = 'active';
                        $order = 'score DESC';
                        break;
                    case 'newest':
                        $submenu['items'][1]['options']['class'] = 'active';
                        $order = 'createtime DESC';
                        break;
                    case 'views':
                        $submenu['items'][2]['options']['class'] = 'active';
                        $order = 'viewcount DESC';
                        break;
                    case 'recent':
                    default:
                        $submenu['items'][3]['options']['class'] = 'active';
                        $order = 'activity DESC';
                        break;
                }
                $queryQuestion = Post::find()->where(['idtype' => Post::IDTYPE_Q, 'uid' => $uid]);
                $pages = new Pagination(['totalCount' => $queryQuestion->count()]);
                $pages->pageSize = Yii::$app->params['pages']['userQuestionPagesize'];

                $questions = $queryQuestion->orderBy($order)->offset($pages->offset)->limit($pages->limit)->all();

                echo $this->renderPartial('_stats-questions', array('submenu' => $submenu, 'questions' => $questions, 'pages' => $pages));
                break;

            case 'answers':
                $url = array('users/stats', 'do' => 'answers', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userAnswerPagesize'], 'uid' => $uid);
                $submenu = array(
                    'items' => array(
                        array('label' => '投票', 'url' => array_merge($url, array('sort' => 'votes')), 'options' => array('title' => '按照投票从高到低')),
                        array('label' => '提问', 'url' => array_merge($url, array('sort' => 'newest')), 'options' => array('title' => '按照创建日期')),
                        array('label' => '查看', 'url' => array_merge($url, array('sort' => 'views')), 'options' => array('title' => '按照查看数量')),
                        array('label' => '活动', 'url' => array_merge($url, array('sort' => 'recent')), 'options' => array('title' => '按照最近活动时间')),
                    ),
                    'options' => ['id' => 'tabs-answer-user', 'class' => 'subtabs']
                );
                switch ($sort) {
                    case 'votes':
                        $submenu['items'][0]['options']['class'] = 'active';
                        $order = 'score DESC';
                        break;
                    case 'newest':
                        $submenu['items'][1]['options']['class'] = 'active';
                        $order = 'createtime DESC';
                        break;
                    case 'views':
                        $submenu['items'][2]['options']['class'] = 'active';
                        $order = 'viewcount DESC';
                        break;
                    case 'recent':
                    default:
                        $submenu['items'][3]['options']['class'] = 'active';
                        $order = 'activity DESC';
                        break;
                }

                $answerQuery= Post::find()->where(['idtype' => Post::IDTYPE_A, 'uid' => $uid]);
                $answersPages = new Pagination(['totalCount' => $answerQuery->count()]);
                $answersPages->pageSize = Yii::$app->params['pages']['userAnswerPagesize'];
                $answers = $answerQuery->orderBy($order)->offset($answerQuery->offset)->limit($answerQuery->limit)->all();
                return $this->renderPartial('_stats-answers', array('submenu' => $submenu, 'answers' => $answers, 'pages' => $answersPages), true);
            case 'tags':
                $order = 'totalcount DESC';
                $criteria = new CDbCriteria(array(
                    'condition' => 'uid=' . $uid,
                    'order' => $order,
                ));
                $total = UserTags::Model()->count($criteria);
                $tPages = new CPagination($total);
                $tPages->pageSize = Yii::$app->params['pages']['userTagsPagesize'];
                $tPages->applyLimit($criteria);
                $usertags = UserTags::Model()->findAll($criteria);
                echo $this->renderPartial('_stats-tags', array('usertags' => $usertags, 'pages' => $tPages), true);
                break;
        }
    }

    public function actionActivity()
    {
        $uid = Yii::$app->request->get('uid');
        $url = array('users/activity', 'do' => 'show', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userActivityPagesize'], 'uid' => $uid);
        $submenu = array(
            'items' => array(
                array('label' => '所有', 'url' => array_merge($url, array('filter' => 'all')), 'options' => array('title' => '所有')),
                array('label' => '采纳', 'url' => array_merge($url, array('filter' => 'accepts')), 'options' => array('title' => '采纳答案')),
                array('label' => '帖子', 'url' => array_merge($url, array('filter' => 'posts')), 'options' => array('title' => '提问或答案帖子')),
                array('label' => '徽章', 'url' => array_merge($url, array('filter' => 'badges')), 'options' => array('title' => '徽章')),
                array('label' => '评论', 'url' => array_merge($url, array('filter' => 'comments')), 'options' => array('title' => '评论')),
                array('label' => '修订', 'url' => array_merge($url, array('filter' => 'revisions')), 'options' => array('title' => '版本')),
            ),
            'options' => ['id' => 'tabs-activity', 'class' => 'subtabs'],
        );

        $activityQuery = Activity::find()->where(['uid' => $uid])->orderBy(['time' => SORT_DESC]);
        
        switch ($_GET['filter']) {
            case 'comments':
                $submenu['items'][4]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['comment']]);
                break;
            case 'posts':
                $submenu['items'][2]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['ask', 'answer']]);
                break;
            case 'accepts':
                $submenu['items'][1]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['accept']]);
                break;
            case 'badges':
                $submenu['items'][3]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['award']]);
                break;
            case 'revisions':
                $submenu['items'][5]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['revise']]);
                break;
            default:
                $submenu['items'][0]['options']['class'] = 'active';
                $activityQuery->andWhere(['type' => ['comment', 'ask', 'answer', 'posts', 'revise']]);
                break;
        }
        $total = $activityQuery->count();
        $pages = new Pagination(['totalCount' => $total]);
        $pages->pageSize = Yii::$app->params['pages']['userActivityPagesize'];

        $activities = $activityQuery->offset($pages->offset)->limit($pages->limit)->all();
        return $this->renderPartial('_activity', array('activities' => $activities, 'pages' => $pages, 'submenu' => $submenu));
    }

    public function actionReputation()
    {
        $id = Yii::$app->request->get('uid');
        $reputeQuery = Repute::find()->where(['{{%repute}}.uid' => $id])->orderBy(['time' => SORT_DESC]);
        $total = $reputeQuery->count();

        $pages = new Pagination(['totalCount' => $total]);
        $pages->pageSize = Yii::$app->params['pages']['userReputationPagesize'];

        $reputes = $reputeQuery->joinWith('question')->limit($pages->limit)->offset($pages->offset)->all();
        $formattedReputes = Repute::formatReputes($reputes);
        return $this->renderPartial('_reputation', [
            'reputes' => $formattedReputes,
            'pages' => $pages
         ]);
    }

    public function actionFilter()
    {
        $tab = Yii::$app->request->get('tab', 'reputation');

        $votesTh = Yii::$app->params['pages']['userIndexVoters'];
        $editsTh = Yii::$app->params['pages']['userIndexEditors'];
        $repsTh = Yii::$app->params['pages']['userIndexReps'];
        $userQuery = User::find();
        switch ($tab) {
            case 'newusers':
                $sort = Yii::$app->request->get('sort', 'reputation');
                $submenu = $this->_getNewusersSubmenu();
                $order = $this->_getOrder($tab, $sort);

                $registertime = time() - 30 * 86400;
                $userQuery->where('registertime>:time', [':time' => $registertime])->joinWith('stats')->orderBy($order);
                break;

            case 'voters':
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->_getSubmenu($tab);
                $order = $this->_getOrder($tab, $filter);

                $userQuery->joinWith(['stats' => function($query) use ($votesTh) {
                    $query->where('upvotecount>:votes', [':votes' => $votesTh]);
                }])->orderBy($order);
                break;
            case 'editors':
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->_getSubmenu($tab);
                $order = $this->_getOrder($tab, $filter);

                $userQuery->joinWith(['stats' => function($query) use ($editsTh) {
                    $query->where('editcount>:edits', [':edits' => $editsTh]);
                }])->orderBy($order);
                break;
            case 'reputation':
            default:
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->_getSubmenu($tab);
                $order = $this->_getOrder($tab, $filter);
                $userQuery->where('reputation>:reputation', [':reputation' => $repsTh]);
                $userQuery->joinWith('stats')->with('profile')->orderBy($order);
                break;
        }
        
        //@todo 待完善策略
//        if (Yii::$app->request->get('show') && Yii::$app->request->get('search')) {
//            $userQuery->andWhere('username=:q',[':q' => Yii::$app->request->get('search')]);
//        } else {
//            $userQuery->andFilterWhere(['like','username',Yii::$app->request->get('search')]);
//        }
        $userQuery->andFilterWhere(['like','username',Yii::$app->request->get('search')]);

        $totalCount = $userQuery->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->pageSize = Yii::$app->params['pages']['userIndexPagesize'];
        $pages->route = 'users/index';
        $users = $userQuery->all();
        echo $this->renderPartial('_filter', [
            'submenu' => $submenu, 
            'users' => $users, 
            'pages' => $pages
        ]);
    }

    public function actionAvatar()
    {
        $do = $_GET['do'];
        $base = Yii::getPathOfAlias('webroot');  //dirname(Yii::$app->BasePath)
        $avatarDir = $base . "/data/avatar/";
        $tempDir = $base . "/data/avatar/tmp/";

        $uid = Yii::$app->user->getId();

        $tempSrcFile = $tempDir . "{$uid}_src.jpg";
        $tempFile = $tempDir . "{$uid}.jpg";

        $this->pageTitle = "更新头像";

        if ($do == 'upload') {
            $image = CUploadedFile::getInstanceByName("photo");

            file_exists($tempSrcFile) && unlink($tempSrcFile);
            file_exists($tempFile) && unlink($tempFile);
            $image->saveAs($tempSrcFile);
            $defaults = array(
                'mode' => Image::MODE_NO,
                'suffix' => '',
                'w' => 600,
                'h' => 600,
                'file' => $tempSrcFile,
                'out' => $tempFile
            );
            Image::createThumb($defaults);
            $rand = time();
            echo Yii::$app->baseUrl . "/data/avatar/tmp/{$uid}.jpg?{$rand}";
            exit;
        } elseif ($do == 'crop') {
            $dir = $avatarDir . $this->getDirAvatar($uid);
            Dir::mkdir($dir);
            $big = $avatarDir . $this->getAvatar($uid, 'big');
            $small = $avatarDir . $this->getAvatar($uid, 'small');
            $middle = $avatarDir . $this->getAvatar($uid, 'middle');
            file_exists($big) && unlink($big);
            file_exists($small) && unlink($small);
            file_exists($middle) && unlink($middle);

            $out = $avatarDir . $this->getAvatar($uid, 'big');
            $tempFile = $tempDir . "{$uid}.jpg";
            $defaults = array(
                'mode' => Image::MODE_WH,
                'suffix' => '',
                'w' => 128,
                'h' => 128,
                'srcRect' => array($_POST['w'], $_POST['h']),
                'srcXY' => array($_POST['x'], $_POST['y']),
                'file' => $tempFile,
                'out' => $out
            );
            Image::createThumb($defaults);

            $defaults = array(
                'mode' => Image::MODE_WH,
                'suffix' => '',
                'w' => 32,
                'h' => 32,
                'file' => $out,
                'out' => $avatarDir . $this->getAvatar($uid, 'small')
            );
            Image::createThumb($defaults);

            $defaults = array(
                'mode' => Image::MODE_WH,
                'suffix' => '',
                'w' => 48,
                'h' => 48,
                'file' => $out,
                'out' => $avatarDir . $this->getAvatar($uid, 'middle')
            );
            Image::createThumb($defaults);

            file_exists($tempSrcFile) && unlink($tempSrcFile);
            file_exists($tempFile) && unlink($tempFile);

            $this->redirect($this->createUrl('users/view', array('id' => $uid)));
        }

        $this->render('avatar');
    }

    public function actionSavepreference()
    {
        $key = intval($_POST['key']);
        $uid = Yii::$app->user->getId();
        $userProfile = UserProfile::Model()->findByPk($uid);

        if ($userProfile) {
            if ($key == 20) {
                $value = trim($_POST['value']);
                $preference = Formatter::filterTags($value);
                $userProfile->preference = implode(" ", $preference);
                $userProfile->update(array('preference'));
                echo $userProfile->preference;
            } elseif ($key == 25) {
                $value = trim($_POST['value']);
                $unpreference = Formatter::filterTags($value);
                $userProfile->unpreference = implode(" ", $unpreference);
                $userProfile->update(array('unpreference'));
            }
        }
    }

    /**
     * 获取用户头像在目录	，格式：000/000
     * @param $uid
     */
    function getDirAvatar($uid)
    {
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 3);
        return $dir1 . '/' . $dir2 . '/';
    }

    function getAvatar($uid, $size = 'middle')
    {
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir = $this->getDirAvatar($uid);
        return $dir . substr($uid, -3) . "_avatar_$size.jpg";
    }

    private function _getSubmenu($tab)
    {
        $week = DateFormatter::weekFirstDay();
        $month = DateFormatter::monthFirstDay();
        $quarter = DateFormatter::quarterFirstDay();
        $year = DateFormatter::yearFirstDay();

        $submenu = array(
            'items' => array(
                array('label' => '所有', 'url' => array('users/index', 'tab' => $tab, 'filter' => 'all'), 'options' => array('title' => "所有用户列表")),
                array('label' => '年', 'url' => array('users/index', 'tab' => $tab, 'filter' => 'year'), 'options' => array('title' => "{$year}至今天")),
                array('label' => '季', 'url' => array('users/index', 'tab' => $tab, 'filter' => 'quarter'), 'options' => array('title' => "{$quarter}至今天")),
                array('label' => '月', 'url' => array('users/index', 'tab' => $tab, 'filter' => 'month'), 'options' => array('title' => "{$month}至今天")),
                array('label' => '周', 'url' => array('users/index', 'tab' => $tab, 'filter' => 'week'), 'options' => array('title' => "{$week} 至今天")),
            ),
            'options' => ['id' => 'tabs-interval', 'class' => 'subtabs'],
        );
        $filter = (!isset($_GET['filter'])) ? 'week' : $_GET['filter'];
        switch ($filter) {
            case 'all':
                $submenu['items'][0]['options']['class'] = 'active';
                break;
            case 'year':
                $submenu['items'][1]['options']['class'] = 'active';
                break;
            case 'quarter':
                $submenu['items'][2]['options']['class'] = 'active';
                break;
            case 'month':
                $submenu['items'][3]['options']['class'] = 'active';
                break;
            case 'week':
                $submenu['items'][4]['options']['class'] = 'active';
                break;
        }
        if (isset($_GET['search'])) {
            for ($i = 0; $i < 5; $i++) {
                $submenu['items'][$i]['url']['search'] = $_GET['search'];
            }
        }
        return $submenu;
    }

    private function _getNewusersSubmenu()
    {
        $submenu = array(
            'items' => array(
                array('label' => '注册日期', 'url' => array('users/index', 'tab' => 'newusers', 'sort' => 'registertime'), 'options' => array('title' => '注册日期')),
                array('label' => '威望', 'url' => array('users/index', 'tab' => 'newusers', 'sort' => 'reputation'), 'options' => array('title' => '威望从高到低')),
            ),
            'options' => ['id' => 'tabs-interval', 'class' => 'subtabs'],
        );
        if (isset($_GET['search'])) {
            for ($i = 0; $i < 2; $i++) {
                $submenu['items'][$i]['url']['search'] = $_GET['search'];
            }
        }
        $sort = (!isset($_GET['sort'])) ? 'reputation' : $_GET['sort'];
        switch ($sort) {
            case 'registertime' :
                $submenu['items'][0]['options']['class'] = 'active';
                break;
            case 'reputation' :
            default:
                $submenu['items'][1]['options']['class'] = 'active';
                break;
        }
        return $submenu;
    }

    private function _getOrder($tab, $filter)
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

    private function _countFavsAnswers($days, $ids)
    {
        $ids = implode(',', $ids);
        $time = time() - 86400 * $days;
        $list = Yii::$app->db->createCommand()
                ->select("count(*) as count")
                ->from("{{post}}")
                ->where("createtime>:time AND idtype=:idtype and idv in ($ids)")
                ->bindValues(array(":time" => $time, ":idtype" => Post::IDTYPE_A))
                ->queryRow();
        return $list['count'];
    }

}