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