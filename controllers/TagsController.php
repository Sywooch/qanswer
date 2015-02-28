<?php

namespace app\controllers;
use app\components\BaseController;
use Yii;
use app\models\Tag;
use app\models\QuestionTag;
use app\models\Post;
use app\models\Revision;
use yii\data\Pagination;
use app\components\Top;
use app\components\String;

class TagsController extends BaseController
{

    public $layout = 'column1';
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

    /**
     * 设置接入控制规则
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to access 'index' and 'view' actions.
                'actions' => array('index', 'view', 'subscriber'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated users to access all actions
                'actions' => array('edit'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * 显示单个问题
     */
    public function actionView()
    {
        $tagName = Yii::$app->request->get('tag');

        $tag = Tag::findOne(['name' => $tagName]);

        $op = isset($_GET['op']) ? $_GET['op'] : '';
        $submenu = array(
            'items' => array(
                array('label' => '基本信息', 'url' => ['tags/view', 'tag' => $tag->name, 'op' => 'info'], 'options' => ['title' => '基本信息']),
                array('label' => '用户', 'url' => ['tags/view', 'tag' => $tag->name, 'op' => 'topusers'], 'options' => ['title' => '该标签优秀的提问者和回答者']),
                array('label' => '热门', 'url' => ['tags/view', 'tag' => $tag->name, 'op' => 'hot'], 'options' => ['title' => '该标签热门回答']),
                array('label' => '新回答', 'url' => ['tags/view', 'tag' => $tag->name, 'op' => 'new'], 'options' => ['title' => '该标签新的回答']),
            ),
            'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
        );
        switch ($op) {
            case 'topusers':
                $this->title = $this->title . "专家用户";
                $this->pageDescription = $this->title . "专家用户列表";
                $usersAll = Top::TopTagUsers($tag->name);
                $usersMonth = Top::TopTagUsers($tag->name, 30);
                $answerersAll = Top::tagAnswersUsers($tag->name);
                $answerersMonth = Top::tagAnswersUsers($tag->name, 30);
                $questionsCount = Top::tagQuestionsAll($tag->name);
                return $this->render('_view_topusers', [
                    'submenu' => $submenu,
                    'usersAll' => $usersAll,
                    'usersMonth' => $usersMonth,
                    'answerersAll' => $answerersAll,
                    'answerersMonth' => $answerersMonth,
                    'questionsCount' => $questionsCount,
                    'tag' => $tag
                ]);
            case 'hot':
                $url = array('tags/view', 'tag' => $tag->name, 'op' => 'hot');
                $subtabs = array(
                    'items' => array(
                        array('label' => '天', 'url' => array_merge($url, array('filter' => 'day')), 'options' => array('title' => '最近24小时')),
                        array('label' => '周', 'url' => array_merge($url, array('filter' => 'week')), 'options' => array('title' => '最近7天')),
                        array('label' => '月', 'url' => array_merge($url, array('filter' => 'month')), 'options' => array('title' => '最近30天')),
                        array('label' => '年', 'url' => array_merge($url, array('filter' => 'year')), 'options' => array('title' => '最近365天')),
                        array('label' => '全部', 'url' => array_merge($url, array('filter' => 'all')), 'options' => array('title' => '全部')),
                    ),
                    'options' =>['id' => 'tabs-favorite-user', 'class' => 'subtabs']
                );
                $postQuery = Post::find()->with('author')
                                         ->innerJoin('post question',"question.id = post.idv")
                                         ->innerJoin('questiontag qt', 'qt.postid = question.id')
                                         ->orderBy(['question.viewcount' => SORT_DESC]);

                $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                
                $postQuery->where(['qt.tag' => $tag->name])->limit(30);
                
                $now = time();
                $time = 0;
                switch ($filter) {
                    case 'day':
                        $time = $now - 86400;
                        break;
                    case 'week':
                        $time = $now - 7 * 86400;
                        break;
                    case 'month':
                        $time = $now - 30 * 86400;
                        break;
                    case 'year':
                        $time = $now - 364 * 86400;
                        break;
                    case 'all':
                    default:
                        $subtabs['items'][4]['options']['class'] = 'youarehere';
                        break;
                }
                $postQuery->andWhere('question.createtime>:time',[':time' => $time]);

                $answers = $postQuery->all();
                return $this->render('_view_hot', [
                    'submenu' => $submenu, 
                    'answers' => $answers, 
                    'tag' => $tag, 
                    'subtabs' => $subtabs
                ]);
            case 'new':
                $answers = Post::find()->with('question','author')
                                       ->innerJoin('{{%questiontag}} qt', 'qt.postid=post.idv')
                                       ->orderBy(['post.createtime' => SORT_DESC])
                                       ->where('post.idtype=:idtype AND qt.tag=:tag', [':idtype' => Post::IDTYPE_A, ':tag' => $tag->name])
                                       ->limit(30)
                                       ->all();
                return $this->render('_view_new', [
                    'submenu' => $submenu, 
                    'answers' => $answers, 
                    'tag' => $tag
                ]);
            case 'info':
            default:
                $this->pageDescription = $tag->post->excerpt;
                $submenu['items'][0]['itemOptions']['class'] = 'youarehere';
                return $this->render('view', [
                    'tag' => $tag,
                    'submenu' => $submenu
                ]);
        }
    }

    public function actionEdit()
    {
        $tag = Tag::findOne(Yii::$app->request->get('id'));
        $this->title = "编辑标签：" . $tag->name;
        if ($tag === null) {
            throw new \yii\web\NotFoundHttpException(404, '请求页面不存在.');
        }
        if (!$this->me->isActive()) {
            $this->title = Yii::t('users', 'users no active');
            return $this->render('/common/message', array('data' => array('title' => Yii::t('users', 'users no active'), 'message' => Yii::t('users', 'please active your account'))));
        }

        $allow = $this->me->checkPerm('trustedUser') || $this->me->isAdmin() || $this->me->isMod();
        if ($tag->postid == 0 && !$allow) {
            return $this->render('/common/message', array('data' => array('title' => '提示信息', 'message' => '无权访问该页面')));
        }

        $post = null;
        if ($tag->postid > 0) {  //已存在版本
            $post = Post::findOne($tag->postid);

            if (isset($_POST['submit']) && $_POST['submit'] == true) {
                if ($allow) {
                    $oldContent = $post->lastrevision->text;
                    $post->content = String::markdownToHtml($_POST['content']);
                    $post->idtype = Post::IDTYPE_T;
                    $post->excerpt = String::filterTitle($_POST['content'], 100);

                    if ($post->save(FALSE)) {
                        $post->content = $_POST['content'];
                        $revision = new Revision;
                        $revision->status = Revision::STATUS_OK;
                        $revision->newRevision($post, $oldContent);
                        $post->revisionid = $revision->id;
                        $post->update(FALSE, ['revisionid']);
                        $this->redirect(array('view', 'tag' => $tag->name));
                    }
                } else {
                    $post->content = $_POST['content'];
                    $revision = new Revision;
                    $revision->status = Revision::STATUS_PEER;
                    $revision->newRevision($post, $oldContent);
                    $this->redirect(array('view', 'tag' => $tag->name));
                }
            }
        } else {
            if (isset($_POST['submit']) && $_POST['submit'] == true) {
                $post = new Post;
                $post->setScenario('tag');
                $post->content = String::markdownToHtml($_POST['content']);
                $post->idtype = Post::IDTYPE_T;
                $post->title = $tag->name;
                $post->excerpt = String::filterTitle($_POST['content'], 100);
                if ($post->save()) {
                    $post->content = $_POST['content'];
                    $revision = new Revision;
                    $revision->status = Revision::STATUS_OK;
                    $revision->newRevision($post);
                    $post->revisionid = $revision->id;
                    $post->update(FALSE, ['revisionid']);

                    $tag->postid = $post->id;
                    $tag->save();
                }
                $this->redirect(array('view', 'tag' => $tag->name));
            }
        }
        if ($tag->postid > 0) {
            $post->content = $post->lastrevision->text;
        }
        return $this->render('wiki', array('post' => $post, 'tag' => $tag));
    }

    public function actionIndex()
    {
        $tagQuery = Tag::find();
        $_GET['tab'] = isset($_GET['tab']) ? $_GET['tab'] : '';
        switch ($_GET['tab']) {
            case 'active':
                $tagQuery->orderBy(['activity' => SORT_DESC]);
                break;
            default:
                $tagQuery->orderBy(['frequency' => SORT_DESC]);
                break;
        }

        $totalCount = $tagQuery->count();

        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->pageSize = Yii::$app->params['pages']['tagPagsize'];

        $tags = $tagQuery->offset($pages->offset)->limit($pages->limit)->with('post')->all();
        foreach ($tags as $tag) {
            $t[] = $tag->name;
        }

//		$sql = "select tag,count(*) as total from questiontag qt left join post q on qt.postid=q.id  where tag IN ('regex','ddd') and q.createtime>{$time} group by tag";

        $time = time();
        $day7 = $time - 86400 * 7;
        $questionTagQuery = QuestionTag::find()
                ->select(['tag', 'count(*) as total'])
                ->leftJoin('post', 'post.id = questiontag.postid')
                ->groupBy('questiontag.tag')
                ->andWhere(['questiontag.tag' => $t])
                ->andWhere('post.createtime>:time', [':time' => $day7]);
        $weeks = [];
        $week = $questionTagQuery->all();
        foreach ($week as $w) {
            $weeks[$w->tag] = $w->total;
        }

        $day = $time - 86400;
        $dayQuestionTagQuery = QuestionTag::find()
                ->select(['tag', 'count(*) as total'])
                ->leftJoin('post', 'post.id = questiontag.postid')
                ->groupBy('questiontag.tag')
                ->andWhere(['questiontag.tag' => $t])
                ->andWhere('post.createtime>:time', [':time' => $day]);
        $dayTags = $dayQuestionTagQuery->all();

        $days = array();
        foreach ($dayTags as $w) {
            $days[$w->tag] = $w->total;
        }
        return $this->render('index', [
            'tags' => $tags,
            'weeks' => $weeks,
            'days' => $days,
            'pages' => $pages,
        ]);
    }

    public function actionSubscriber()
    {
        $tagName = $_GET['name'];
        $tag = Tag::Model()->with('post')->findByAttributes(array('name' => $tagName));
        echo $this->renderPartial('_subscriber', array('tag' => $tag), true);
    }

}
