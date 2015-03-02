<?php

namespace app\Controllers;

use app\components\BaseController;
use Yii;
use yii\web\HttpException;
use app\models\User;
use app\models\UserProfile;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\components\DateFormatter;
use yii\data\Pagination;
use app\models\Post;
use app\models\UserTags;
use app\models\Award;
use app\models\Activity;
use app\models\Repute;
use app\models\Vote;

class UsersController extends BaseController
{
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * 设置接入控制规则
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to access 'index' and 'view' actions.
                'actions' => array('index', 'view', 'login', 'stats', 'captcha', 'active', 'filter', 'activity', 'rep'),
                'users' => array('*'),
            ),
            array('allow', // captchas can be loaded just by guests
                'actions' => array('register', 'recovery'),
//				'expression'=>'Yii::$app->options["closeregister"]==0',
                'users' => array('?'),
            ),
            array('allow', // allow authenticated users to access all actions
                'actions' => array('logout', 'profilelink', 'edit', 'savepreference', 'avatar', 'inbox', 'checkinbox', 'setting'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionLogin()
    {
        $this->title = "登录";
        if (Yii::$app->user->isGuest) {
            $model = new LoginForm;

            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                echo CActiveForm::validate($model);
                Yii::$app->end();
            }

            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            } else {
                return $this->render('login', ['model' => $model]);
            }
        } else {
            return $this->goBack();
        }
    }

    public function actionRegister()
    {
        $this->title = "注册";
//		if ($this->options['closeregister']) {
//			$this->pageTitle = "禁止新用户注册";
//			$this->render('/common/closeregister');
//			Yii::$app->end();
//		}

        $model = new RegisterForm;
//		if (Yii::$app->request->post()-) {
//			echo CActiveForm::validate($model);
//			Yii::$app->end();
//		}

        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->user->returnUrl);
        } elseif ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $soucePassword = $model->password;
                $model->activekey = md5(microtime() . $model->password);
                $salt = substr(uniqid(rand()), -6);
                $model->password = md5(md5($model->password) . $salt);
                $model->password2 = md5(md5($model->password2) . $salt);
                $model->salt = $salt;
                $parts = explode("@", $model->email);
                $model->username = $parts[0];

                $model->registertime = time();
                $model->status = ($this->options['regverify'] == RegisterForm::EMAIL_VERIFY || $this->options['regverify'] == RegisterForm::MOD_VERIFY) ? User::STATUS_NOACTIVE : User::STATUS_ACTIVE;

                if ($model->save()) {

                    $userstat = new UserStat();
                    $userstat->id = $model->id;
                    $userstat->save();

                    $userprofile = new UserProfile;
                    $userprofile->id = $model->id;
                    $userprofile->save();

                    //更新用户名为user+ID格式
                    $model->username = 'user' . $model->id;
                    $model->update(array('username'));

                    $login = Html::a("登录", Yii::$app->user->loginUrl);

                    if ($this->options['regverify'] == RegisterForm::EMAIL_VERIFY) {
                        $body = "<h2>" . $model->username . ",您好！</h2>";
                        $body .= "<p>欢迎您注册成" . Yii::$app->name . "的用户，您可以通过点击以下地址来激活您的帐户：</p>";
                        $activeUrl = $this->createAbsoluteUrl('/users/active', array("activekey" => $model->activekey, "email" => $model->email));
                        $body .= '<p><a href="' . $activeUrl . '">激活帐号</a></p>';
                        $body .= "<p>如果您不能点击上面链接，还可以将以下链接复制到浏览器地址栏中访问：</p>";
                        $body .= $activeUrl;
                        $body .= "<div align='right' style='padding-right:10%'><p>" . Yii::$app->name . "</p>";

                        Emailer::mail(array($model->email), Yii::t('users', 'active'), $body);
                        Yii::$app->user->setFlash('registration', "感谢你的注册，激活邮件已经发到邮箱，请登录邮箱激活");
                    } elseif ($this->options['regverify'] == RegisterForm::MOD_VERIFY) {
                        Yii::$app->user->setFlash('registration', "感谢你的注册，请等待管理员的审核");
                    } else {
                        if ($this->options['autologin']) {
                            $identity = new UserIdentity($model->email, $soucePassword);
                            $identity->authenticate();
                            Yii::$app->user->login($identity, 0);
                            $this->redirect(Yii::$app->user->returnUrl);
                        } else {
                            Yii::$app->user->setFlash('registration', "感谢你的注册，请{$login}！");
                        }
                    }
                    // @todo
                    //1. 发送激活邮件
                    //2. 是否允许非激活会员登录？允许则登录
                    $this->refresh();
                }
//				$this->redirect(Yii::$app->user->returnUrl);
            }
        }
        return $this->render('register', array('model' => $model));
    }

    public function actionRecovery()
    {
        $this->pageTitle = "恢复密码";
        $form = new RecoveryForm;
        if (Yii::$app->user->id) {
            $this->redirect(Yii::$app->user->returnUrl);
        } else {
            $email = ((isset($_GET['email'])) ? $_GET['email'] : '');
            $activekey = ((isset($_GET['activekey'])) ? $_GET['activekey'] : '');
            if ($email && $activekey) {
                $form2 = new ChangePassword;
                $find = User::model()->notsafe()->findByAttributes(array('email' => $email));
                if (isset($find) && $find->activekey == $activekey) {
                    if (isset($_POST['ChangePassword'])) {
                        $form2->attributes = $_POST['ChangePassword'];
                        if ($form2->validate()) {
//							$find->password = md5($form2->password);
                            $salt = substr(uniqid(rand()), -6);
                            $find->password = md5(md5($form2->password) . $salt);
                            $find->salt = $salt;

                            $find->activekey = md5(microtime() . $form2->password);
                            if ($find->status == 0) {
                                $find->status = 1;
                            }
                            $find->save();
                            Yii::$app->user->setFlash('recoveryMessage', "新密码保存成功");
                            $this->redirect(array('users/recovery'));
                        }
                    }
                    $this->render('changepassword', array('form' => $form2));
                } else {
                    Yii::$app->user->setFlash('recoveryMessage', "链接出错");
                    $this->redirect(array('users/recovery'));
                }
            } else {
                if (isset($_POST['RecoveryForm'])) {
                    $form->attributes = $_POST['RecoveryForm'];
                    if ($form->validate()) {
                        $user = User::model()->notsafe()->findbyPk($form->user_id);
                        $activation_url = $this->createAbsoluteUrl("/users/recovery", array("activekey" => $user->activekey, "email" => $user->email));
                        $subject = Yii::$app->name . "密码恢复";
                        $message = "你请求重置密码，请点击如下链接： {$activation_url}.";

                        Emailer::mail(array($user->email), $subject, $message);

                        Yii::$app->user->setFlash('recoveryMessage', "请查看你的邮箱，一份重置密码的邮件已经发送到你的邮箱.");
                        $this->refresh();
                    }
                }
                $this->render('recovery', array('form' => $form));
            }
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->redirect(Yii::$app->homeUrl);
    }

    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab', 'reputation');
        $votesTh = Yii::$app->params['pages']['userIndexVoters'];
        $editsTh = Yii::$app->params['pages']['userIndexEditors'];
        $repsTh = Yii::$app->params['pages']['userIndexReps'];

        $menu = array(
            'items' => array(
                array('label' => '威望', 'url' => array('users/index', 'tab' => 'reputation'), 'options' => array('title' => '威望从高到低')),
                array('label' => '新用户', 'url' => array('users/index', 'tab' => 'newusers'), 'options' => array('title' => '最近30天加入的用户')),
                array('label' => '投票者', 'url' => array('users/index', 'tab' => 'voters'), 'options' => array('title' => "投票超过{$votesTh}次用户列表")),
                array('label' => '编辑者', 'url' => array('users/index', 'tab' => 'editors'), 'options' => array('title' => "编辑超过{$editsTh}个帖子的用户")),
            ),
            'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
        );
        $renderParams = [];
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
                
                $renderParams = ['filter' => $filter];
                break;

            case 'editors':
                $filter = Yii::$app->request->get('filter', 'week');
                $submenu = $this->_getSubmenu($tab);
                $order = $this->_getOrder($tab, $filter);

                $userQuery->joinWith(['stats' => function($query) use ($editsTh) {
                    $query->where('editcount>:edits', [':edits' => $editsTh]);
                }])->orderBy($order);
                
                $renderParams = ['filter' => $filter];
                break;
            case 'reputation' :
            default:
                $filter = Yii::$app->request->get('filter', 'week');
                
                $submenu = $this->_getSubmenu($tab);
                $order = $this->_getOrder($tab, $filter);
                $userQuery->where('reputation>:reputation', [':reputation' => $repsTh]);
                $userQuery->joinWith('stats')->with('profile')->orderBy($order);
                $renderParams = ['filter' => $filter];
                break;
        }
        $this->title = "用户";
        
        $userQuery->andFilterWhere(['like','username',Yii::$app->request->get('search')]);
        $totalCount = $userQuery->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
	    $pages->pageSize = Yii::$app->params['pages']['userIndexPagesize'];
        $users = $userQuery->all();
        
        return $this->render('index', \yii\helpers\ArrayHelper::merge([
            'users' => $users,
            'menu' => $menu,
            'submenu' => $submenu,
            'pages' => $pages,
            'tab' => $tab
        ],['params' => $renderParams]));
    }

    public function actionView()
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

        $tab = Yii::$app->request->get('tab','');
        switch ($tab) {
            case 'activity':
                $url = array('users/activity', 'do' => 'show', 'page' => 1, 'pagsize' => Yii::$app->params['pages']['userActivityPagesize'], 'uid' => $id);
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
                $pages->params = ['filter' => $filter, 'uid' => $id];
                $pages->route = 'users/activity';

                $activities = $activityQuery->all();
                return $this->render('view', array(
                    'user' => $user,
                    'activities' => $activities,
                    'submenu' => $submenu,
                    'pages' => $pages,
                    'tab' => $tab
                ));
            case 'reputation':
                $reputeQuery = Repute::find()->where(['{{%repute}}.uid' => $id])->orderBy(['time' => SORT_DESC]);
                
                $startDate = Yii::$app->request->get('startDate');
                if ($startDate) {
                    $reputeQuery->andWhere('time>:time', [':time' => strtotime($startDate)]);
                }

                $total = $reputeQuery->count();

                $pages = new Pagination(['totalCount' => $total]);
                $pages->pageSize = Yii::$app->params['pages']['userReputationPagesize'];
                $pages->params = ['sort' => 'time', 'uid' => $id];
                $pages->route = 'users/rep';

                $reputes = $reputeQuery->joinWith('question')->offset($pages->offset)->limit($pages->limit)->all();
                $formattedReputes = Repute::formatReputes($reputes);
                return $this->render('view', array(
                    'user' => $user,
                    'reputes' => $formattedReputes,
                    'pages' => $pages,
                    'tab' => $tab
                ));
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

                $pages->params = array('uid' => $id, 'sort' => $sort);
                $pages->route = 'users/stats';
                $favs = $voteQuery->joinWith('question')->offset($pages->offset)->limit($pages->limit)->all();
                return $this->render('view', array(
                    'user' => $user,
                    'submenu' => $submenu,
                    'favs' => $favs,
                    'pages' => $pages,
                    'tab' => $tab
                ));
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
                return $this->render('view', [
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
                ]);
        }
    }

    public function actionProfilelink()
    {
        //威望统计
        $uid = Yii::$app->user->getId();
        $cacheId = "users.profilelink." . $uid;

        $userStatics = array();
        if (!$userStatics = Yii::$app->cache->get($cacheId)) {

            $today = strtotime(date('Y-m-d'));
            $week = strtotime(DateFormatter::weekFirstDay());
            $month = strtotime(DateFormatter::monthFirstDay());
            $criteria = new CDbCriteria;
            $criteria->select = 'sum(reputation) as reputations';
            $criteria->condition = 'uid=:uid AND time>:time';
            $criteria->params = array(
                ':uid' => Yii::$app->user->getId(),
                ':time' => $today,
            );
            $todayReputations = Repute::model()->find($criteria);

            $criteria->params[':time'] = $week;
            $weekReputations = Repute::model()->find($criteria);

            $criteria->params[':time'] = $month;
            $monthReputations = Repute::model()->find($criteria);

            //收藏
            $criteria = new CDbCriteria(array(
                'select' => 't.postid',
                'condition' => "t.uid=:uid AND t.fav=1"
            ));
            $criteria->params = array(':uid' => Yii::$app->user->getId());
            $favs = array();
            $favs = Vote::Model()->getFavs($criteria);
            $ids = array();
            foreach ($favs as $i) {
                $ids[] = $i->postid;
            }

            if (count($ids) > 0) {
                $todayFavs = $this->_countFavsAnswers(1, $ids);
                $weekFavs = $this->_countFavsAnswers(7, $ids);
                $monthFavs = $this->_countFavsAnswers(30, $ids);
            }

            $userStatics = array(
                'reputations' => array(
                    'today' => array(
                        'rep' => (isset($todayReputations->reputations) ? $todayReputations->reputations : 0),
                        'day' => date('Y-m-d', $today)
                    ),
                    'week' => array(
                        'rep' => isset($weekReputations->reputations) ? $weekReputations->reputations : 0,
                        'day' => date('Y-m-d', $week)
                    ),
                    'month' => array(
                        'rep' => isset($monthReputations->reputations) ? $monthReputations->reputations : 0,
                        'day' => date('Y-m-d', $month)
                    ),
                ),
                'favs' => array(
                    'today' => isset($todayFavs) ? $todayFavs : 0,
                    'week' => isset($weekFavs) ? $weekFavs : 0,
                    'month' => isset($monthFavs) ? $monthFavs : 0,
                ),
                'uid' => Yii::$app->user->getId(),
            );

            Yii::$app->cache->set($cacheId, $userStatics, 3600);
        }

        echo $this->renderPartial('profilelink', array('userStatics' => $userStatics), true);
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
                $pages->pageSize = Yii::$app->params['pages']['userFavoritePagesize'];
                $sort = Yii::$app->request->get('sort', 'added');
                $pages->params = array('sort' => $sort, 'uid' => $uid);
                $pages->route = 'users/activity';

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

    public function actionActive()
    {
        $email = $_GET['email'];
        $activekey = $_GET['activekey'];
        $this->pageTitle = "账户激活";
        if ($email && $activekey) {
            $find = User::model()->findByAttributes(array('email' => $email));
            if (isset($find) && $find->status) {
                $this->render('message', array('title' => "账户激活", 'message' => "您的帐户已经激活了！"));
            } elseif (isset($find->activekey) && ($find->activekey == $activekey)) {
                $find->activekey = md5(microtime());
                $find->status = 1;
                $find->save();
                $this->render('message', array('title' => "账户激活", 'message' => "账户成功激活"));
            } else {
                $this->render('message', array('title' => "账户激活", 'message' => "不正确的激活码！"));
            }
        } else {
            $this->render('message', array('title' => "账户激活", 'message' => "不正确的激活码！"));
        }
    }

    /**
     * 编辑用户资料
     */
    public function actionEdit()
    {
        $this->title = "更新用户资料";
        $id = intval(Yii::$app->request->get('id'));
        if ($id != Yii::$app->user->id) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        if ($id) {
            $profile = UserProfile::findOne($id);
            $user = User::findOne($id);
        }
        if ($profile === null)
            throw new HttpException(404, 'The requested page does not exist.');

        // ajax validator
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-edit-form') {
            echo ActiveForm::validate(array($profile));
            Yii::$app->end();
        }

        $post = Yii::$app->request->post();
        if (isset($post)) {
            $user->load($post, 'User');
            $profile->load($post, 'UserProfile');
//			$user->attributes = $_POST['User'];
//			$profile->attributes = $_POST['UserProfile'];
            if ($user->validate() && $profile->validate()) {
                $user->save();
                $complete1 = $complete2 = true;
                foreach ($_POST['UserProfile'] as $p) {
                    if (empty($p)) {
                        $complete1 = false;
                        break;
                    }
                }
                foreach ($_POST['User'] as $p) {
                    if (empty($p)) {
                        $complete2 = false;
                        break;
                    }
                }
                $complete = ($complete1 & $complete2) ? 1 : 0;
                $profile->complete = $complete;
                if ($profile->save()) {
                    $user = User::Model()->findByPk($_GET['id']);
                    Yii::$app->user->setName($user->username);
                    $this->redirect(array('users/view', 'id' => $profile->id));
                }
            }
        }
        return $this->render('edit', array('profile' => $profile, 'user' => $user));
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

    public function actionRep()
    {
        $id = Yii::$app->request->get('uid');
        $reputeQuery = Repute::find()->where(['{{%repute}}.uid' => $id])->orderBy(['time' => SORT_DESC]);
        $total = $reputeQuery->count();

        $pages = new Pagination(['totalCount' => $total]);
        $pages->pageSize = Yii::$app->params['pages']['userReputationPagesize'];

        $reputes = $reputeQuery->joinWith('question')->limit($pages->limit)->offset($pages->offset)->all();
        $formattedReputes = Repute::formatReputes($reputes);
        return $this->renderPartial('_rep', [
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

    public function actionInbox()
    {

        $this->pageTitle = Yii::t('global', 'messages');

        $tab = isset($_GET['tab']) ? $_GET['tab'] : '';
        if ($tab == 'all') {
            $criteria = new CDbCriteria(array(
                'condition' => 'uid=:uid',
                'order' => 'time DESC',
                'params' => array(":uid" => Yii::$app->user->getId())
            ));
            $total = Inbox::Model()->count($criteria);

            $pages = new CPagination($total);
            $pages->pageSize = 20;
            $pages->applyLimit($criteria);
            $list = Inbox::Model()->findAll($criteria);
            $this->render('inbox', array('list' => $list, 'pages' => $pages));
        } else {
            $list = Inbox::model()->findAll("uid=:uid AND isnew=1", array(":uid" => Yii::$app->user->getId()));
            if (count($list) > 0) {
                Inbox::model()->updateAll(array('isnew' => 0), "uid=:uid AND isnew=1", array(":uid" => Yii::$app->user->getId()));
                $this->me->messagecount = 0;
                $this->me->update(array('messagecount'));
            }
            $this->me->messagecount = 0;
            $this->me->update(array('messagecount'));
            $this->render('inbox', array('list' => $list));
        }
    }

    public function actionCheckinbox()
    {
//		$uid = Yii::$app->user->getId();
//		$newmessages = Inbox::model()->count("uid=:uid AND isnew=1",array(":uid"=>$uid));
//		if ($this->me->messagecount != $newmessages) {
//			$this->me->messagecount = $newmessages;
//			$this->me->update(array('messagecount'));
//		}
//
//		$cookie = new CHttpCookie('newmessages',1);
//		$cookie->expire = time() + 30;
//		Yii::$app->request->cookies['newmessages'] = $cookie;
    }

    public function actionSetting()
    {
        $this->pageTitle = "邮件通知";
        if (isset($_POST['submit'])) {
            $bits1 = ($_POST['notify']['question_answered'] == 1) ? 1 : 0;
            $bits2 = ($_POST['notify']['commented'] == 1) ? 1 : 0;
            $this->me->setting = intval($bits1) + (intval($bits2) << 1);
            $this->me->save();
            $this->redirect(array('users/setting'));
        } else {
            $this->render('setting');
        }
    }

}