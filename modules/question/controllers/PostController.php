<?php

namespace app\modules\question\controllers;
use app\components\BaseController;
use app\models\Revision;
use app\models\Post;
use app\models\PostState;
use app\models\Vote;
use app\models\Repute;
use app\models\UserLimits;
use app\models\Activity;
use app\models\UserStat;
use app\models\Inbox;
use app\models\Comment;
use app\models\CommentVote;
use app\models\MailQueue;
use app\models\Bounty;
use app\components\String;
use Yii;

class PostController extends BaseController
{

    public $layout = '//column2';
    private $voteTypeIds = array(
        "informModerator" => -1,
        "undoMod" => 0,
        "acceptedByOwner" => 1,
        "upMod" => 2,
        "downMod" => 3,
        "offensive" => 4,
        "favorite" => 5,
        "close" => 6,
        "reopen" => 7,
        "bountyClose" => 9,
        "deletion" => 10,
        "undeletion" => 11,
        "spam" => 12
    );
    private $_model;

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
                'actions' => array('index', 'view', 'revisions'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated users to access all actions
                'actions' => array('vote', 'comments', 'popup', 'edit', 'protect', 'unprotect', 'commenthelp', 'Validateduplicate', 'bountystart', 'lock', 'unlock', 'heartbeat'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionEdit()
    {
        if (!$this->me->isActive()) {
            $this->title = Yii::t('users', 'users no active');
            $this->render('/common/message', array('data' => array('title' => Yii::t('users', 'users no active'), 'message' => Yii::t('users', 'please active your account'))));
        }
        $model = $this->loadModel();

        if ($model->poststate->isLock()) {
            $this->title = "帖子被锁定，不能编辑";
            $this->render('/common/message', array('data' => array('title' => "提示", 'message' => "帖子被锁定，不能编辑")));
        }
        if ($model->isAnswer()) {
            $model->setScenario('answer');
            $this->title = "编辑答案";
        } elseif ($model->isQuestion()) {
            $model->setScenario('qask');
            $this->title = "编辑问题";
        }

        $allowEdit = $this->me->checkPerm('edit') || $this->me->isAdmin() || $this->me->isMod() || $model->isSelf() || ($model->isWiki() && $this->me->checkPerm('editCommunityWiki'));

        if (isset($_POST['Post'])) {
            //如果权限不够，则revision处于等待审核状态
            $oldtext = $model->lastrevision->text;

            $revision = new Revision;

            $revision->postid = $model->id;
            $revision->revtime = time();
            $revision->text = $_POST['Post']['content'];
            $revision->title = $_POST['Post']['title'];
            $revision->uid = Yii::$app->user->getId();
            $revision->status = ($allowEdit) ? Revision::STATUS_OK : Revision::STATUS_PEER;

            $comment = String::filterTitle($_POST['Revision']['comment'], 200);
            if (empty($comment)) {
                $len = mb_strlen($revision->text, 'UTF8');
                $oldlen = mb_strlen($oldtext, 'UTF8');
                $d = $len - $oldlen;
                $comment = (($d > 0) ? "增加了{$d}个字符" : "减少了" . (-$d) . "个字符");
            }
            $revision->comment = $comment;
            $revision->save();

//			UserStat::Model()->updateCounters(array('editcount'=>1),"id=:id",array(":id"=>$this->me->id));

            $activity = new Activity();
            $activity->type = 'revise';
            $activity->typeid = $model->id;
            $activity->uid = Yii::$app->user->getId();
            $activity->data = array(
                'idtype' => ($model->isAnswer()) ? 'answer' : 'question',
                'qid' => ($model->isQuestion()) ? $model->id : $model->question->id,
                'qtitle' => ($model->isQuestion()) ? $model->title : $model->question->title,
                'rid' => $revision->id,
                'comment' => $comment,
            );
            $activity->save();

            if ($allowEdit) {
                UserStat::Model()->updateCounters(array('editcount' => 1), "id=:id", array(":id" => $this->me->id));
                $model->attributes = $_POST['Post'];
                $time = time();
                $model->activity = $time;
                $model->lastedit = $time;

                $orginContent = $model->content;
                $model->content = String::markdownToHtml($model->content);
                $model->excerpt = String::filterTitle($orginContent, 200);
                $model->revisionid = $revision->id;
                if (!$model->isWiki()) {
                    if ($model->isSelf()) {
                        $model->wiki = (isset($_POST['Post']['wiki']) && $_POST['Post']['wiki'] == 1) ? Post::WIKI_MODE : Post::UNWIKI_MODE;
                    } else {
                        $model->wiki = ($model->checkToWiki()) ? Post::WIKI_MODE : Post::UNWIKI_MODE;
                        //发通知
                        if ($model->isWiki()) {
                            $inbox = new Inbox;
                            $inbox->title = ($model->isQuestion()) ? $model->title : $model->question->title;
                            if ($model->isAnswer()) {
                                $inbox->url = $this->createUrl('questions/view', array('id' => $model->idv, '#' => $model->id));
                            } else {
                                $inbox->url = $this->createUrl('questions/view', array('id' => $model->id));
                            }
                            $inbox->summary = String::filterTitle($orginContent, 100);
                            $inbox->type = Inbox::$TYPE['wiki'];
                            $inbox->uid = $model->uid;
                            $inbox->save();
                        }
                    }
                }
                if ($model->save()) {
                    $id = ($model->isAnswer()) ? $model->idv : $model->id;
                    $this->redirect(array('/questions/view', 'id' => $id));
                }
            } else {  //只产生版本
                $id = ($model->isAnswer()) ? $model->idv : $model->id;
                $this->redirect(array('/questions/view', 'id' => $id));
            }
        }

        $model->content = $model->lastrevision->text;
        return $this->render('edit', array(
                    'model' => $model,
        ));
    }

    public function actionRevisions()
    {
        if (intval($_GET['id']) == 0) {
            throw new CHttpException(404, '页面不存在.');
        }
        $revisions = Revision::find()->where(['status' => Revision::STATUS_OK, 'postid' => $_GET['id']])
                        ->orderBy(['revtime' => SORT_DESC])->all();
        if (empty($revisions)) {
            throw new \yii\base\NotSupportedException(404, '页面不存在.');
        }
        $post = Post::findOne($_GET['id']);
        $this->title = "版本历史";
        return $this->render('revisions', array('revisions' => $revisions, 'post' => $post));
    }

    public function actionVote()
    {
        $post = $this->loadModel(Yii::$app->request->get('postid'));

        $uid = Yii::$app->user->getId();
        $user = $this->me;
        $id = $post->id;
        $vote = Vote::find()->where('postid=:postid AND uid=:uid', ['postid' => $id, 'uid' => $uid])->one();

        $type = Yii::$app->request->get('type');

        $result = array(
            'Success' => false,
            'Message' => "",
            'Refresh' => false,
            'RedirectTo' => null,
            'NewScore' => 0,
            'ShowShareTip' => false
        );

        switch ($type) {
            case $this->voteTypeIds['undoMod']:
                if ($post->poststate->isLock()) {
                    $result['Message'] = "帖子被锁定，不能投票";
                    $result['NewScore'] = $post->score;
                } elseif ($vote) {
                    if ($vote['useful'] == Vote::UPVOTE) {
                        $vote->useful = Vote::NOVOTE;
                        $vote->save();
                        Post::Model()->updateCounters(array("useful" => -1, "score" => -1), "id=$id");
                        //如果是答案，则更新问题的aupvotes数量
                        if ($post->isAnswer()) {
                            Post::Model()->updateCounters(array("aupvotes" => -1), "id=:id", array(":id" => $post->idv));
                        }

                        UserStat::Model()->updateCounters(array('upvotecount' => -1), "id=" . $user->id);

                        $result['Success'] = true;
                        $result['NewScore'] = $post->score - 1;

                        $repute = new Repute;
                        $repute->updatePostReputations($post, $post->author, ($post->isQuestion()) ? Repute::Q_UPVOTE_CANCEL : Repute::A_UPVOTE_CANCEL);
                        $repute->calReputation($post->author);
                    } elseif ($vote['useful'] == Vote::DOWNVOTE) { //无用票被取消
                        $vote->useful = Vote::NOVOTE;
                        $vote->save();
                        Post::Model()->updateCounters(array("useful" => 1, "score" => 1), "id=$id");
                        UserStat::Model()->updateCounters(array('downvotecount' => -1), "id=" . $user->id);
                        $result['Success'] = true;
                        $result['NewScore'] = $post->score + 1;

                        //帖子作者威望
                        $repute = new Repute;
                        $repute->updatePostReputations($post, $post->author, Repute::DOWNVOTE_CANCELED);
                        $repute->calReputation($post->author);

                        //操作者威望
                        $repute = new Repute;
                        $repute->updatePostReputations($post, $user, Repute::CANCEL_DOWNVOTE);
                        $repute->calReputation($user);
                    }
                }
                break;

            case $this->voteTypeIds['acceptedByOwner']:
                if ($post->question->uid != $uid) {
                    $result['Message'] = "只有提问者才能操作";
                } elseif ($post->accepted == Post::ACCEPTED) {  //取消采纳
                    $post->accepted = Post::UNACCEPT;
                    $post->save(FALSE);
                    
                    Post::updateAll(['accepted' => Post::UNACCEPT], ['id' => $post->idv]);
                    Activity::deleteAll("type='accept' AND typeid=:typeid", [":typeid" => $post->idv]);

                    //更新威望
                    if (!$post->isWiki()) {
                        $repute = new Repute;
                        $repute->updatePostReputations($post, $post->author, Repute::A_ACCEPT_CANCEL);
                        $repute->calReputation($post->author);

                        $repute = new Repute;
                        $repute->updatePostReputations($post->question, $post->question->author, Repute::ACCEPT_A_CANCEL);
                        $repute->calReputation($post->question->author);
                    }

                    $result['Success'] = true;
                    $result['Message'] = "0";
                } else {
                    //查看是否有其它答案被采纳
                    $answer = Post::find()->where('idv=:idv AND idtype =:idtype AND accepted=:accepted', [':idv' => $post->idv, ':idtype' => 'answer', ':accepted' => Post::ACCEPTED])->one();
                    if ($answer) {
                        $answer->accepted = Post::UNACCEPT;
                        $answer->save(FALSE);

                        $activity = Activity::findOne(['type'=>'accpet','typeid' => $post->idv]);
                        $activity->data = array(
                            'qtitle' => $post->question->title,
                            'aid' => $post->id,
                        );
                        $activity->save();
                        $user->updateLastActivity();

                        //更新帖子作者威望
                        if (!$post->isWiki()) {
                            $repute = new Repute;
                            $repute->updatePostReputations($post, $post->author, Repute::A_ACCEPT_CANCEL);
                            $repute->calReputation($post->author);

                            $repute = new Repute;
                            $repute->updatePostReputations($post->question, $post->question->author, Repute::ACCEPT_A_CANCEL);
                            $repute->calReputation($post->question->author);
                        }
                    } else {
                        $activity = new Activity();
                        $activity->type = 'accept';
                        $activity->typeid = $post->idv;
                        $activity->uid = Yii::$app->user->getId();
                        $activity->data = array(
                            'aid' => $post->id,
                            'qtitle' => $post->question->title,
                        );
                        $activity->save();
                        $user->updateLastActivity();
                        
                        Post::updateAll(['accepted' => Post::ACCEPTED], ['id' => $post->idv]);
                    }
                    $post->accepted = Post::ACCEPTED;
                    $post->save(FALSE);

                    //更新帖子作者威望
                    if (!$post->isWiki()) {
                        $repute = new Repute;
                        $repute->updatePostReputations($post, $post->author, Repute::A_ACCEPT);
                        $repute->calReputation($post->author);

                        $repute = new Repute;
                        $repute->updatePostReputations($post->question, $post->question->author, Repute::ACCEPT_A);
                        $repute->calReputation($post->question->author);
                    }

                    $result['Success'] = true;
                    $result['Message'] = "1";
                }
                break;

            case $this->voteTypeIds['upMod']:
                //判断是否是管理员或版主
                //判断威望是否>=15
                if (!($user->isAdmin() || $user->isMod()) && !$user->checkPerm('voteUp')) {
                    $result['Success'] = false;
                    $rep = Yii::$app->params['reputations']['voteUp'];
                    $result['Message'] = "威望必须达到{$rep}才能投票";
                    $result['NewScore'] = $post->score;
                } elseif ($post->isSelf()) {
                    $result['Message'] = "不能给自己的帖子投票";
                    $result['NewScore'] = $post->score;
                } elseif ($post->poststate->isLock()) {
                    $result['Message'] = "帖子被锁定，不能投票";
                    $result['NewScore'] = $post->score;
                } elseif (($remaining = UserLimits::remaining($user->id, UserLimits::ACTION_VOTE)) == 0) {
                    $result['Message'] = "达到24小时最大投票门限，不能再投票";
                    $result['NewScore'] = $post->score;
                } else {
                    $b = false;
                    if ($vote) {
                        if ($vote['useful'] == 0 || $vote['useful'] == -1) {
                            $score = ($vote['useful'] == 0) ? 1 : 2;
                            $downvoteDelta = ($vote['useful'] == 0) ? 0 : -1;

                            $vote->useful = Vote::UPVOTE;
                            $vote->save();

                            Post::updateAllCounters(["useful" => 1, "score" => $score], ['id'=>$id]);
                            UserStat::updateAllCounters(['downvotecount' => $downvoteDelta, 'upvotecount' => 1], ['id' => $user->id]);

                            //如果是答案，则更新问题的aupvotes数量
                            if ($post->isAnswer()) {
                                Post::updateAllCounters(['aupvotes' => 1], ['id' => $post->idv]);
                            }

                            $activity = new Activity;
                            $activity->type = 'voteup';
                            $activity->typeid = $post->id;
                            $activity->uid = Yii::$app->user->getId();
                            $activity->data = array(
                                'qid' => $post->id,
                                'qtitle' => $post->title
                            );
                            $activity->save();
                            $user->updateLastActivity();

                            if ($score == 2 && !$post->isWiki()) {
                                //帖子作者的威望
                                $repute = new Repute;
                                $repute->updatePostReputations($post, $post->author, Repute::DOWNVOTE_CANCELED);
                                $repute->calReputation($post->author);

                                //操作者威望
                                $repute = new Repute;
                                $repute->updatePostReputations($post, $user, Repute::CANCEL_DOWNVOTE);
                                $repute->calReputation($user);
                            }

                            $result['Success'] = true;
                            $result['NewScore'] = $post->score + $score;
                            $b = true;
                        }
                    } else { //不存在现有投票
                        $vote = new Vote;
                        $vote->useful = Vote::UPVOTE;
                        $vote->postid = $id;
                        $vote->uid = $uid;
                        $vote->fav = 0;
                        $vote->save();

                        $activity = new Activity;
                        $activity->type = 'voteup';
                        $activity->typeid = $post->id;
                        $activity->uid = Yii::$app->user->getId();
                        $activity->data = array(
                            'qid' => $post->id,
                            'qtitle' => $post->title
                        );
                        $activity->save();

                        Post::updateAllCounters(["useful" => 1, "score" => 1], ['id'=>$id]);
                        UserStat::updateAllCounters(["upvotecount" => 1], ['id'=>$user->id]);

                        //如果是答案，则更新问题的aupvotes数量
                        if ($post->isAnswer()) {
                            Post::updateAllCounters(["aupvotes" => 1], ['id'=>$post->idv]);
                        }

                        $result['Success'] = true;
                        $result['NewScore'] = $post->score + 1;
                        $b = true;
                    }
                    if ($b) {
                        if (!$post->isWiki()) {
                            //更新帖子作者威望
                            $repute = new Repute;
                            $repute->updatePostReputations($post, $post->author, ($post->isQuestion()) ? Repute::Q_UPVOTE : Repute::A_UPVOTE);
                            $repute->calReputation($post->author);
                        }
                    }
                    UserLimits::limitIncrement($user->id, UserLimits::ACTION_VOTE);
                }
                break;
            
            case $this->voteTypeIds['downMod']:
                if (!($user->isAdmin() || $user->isMod()) && !$user->checkPerm('voteDown')) {
                    $result['Success'] = false;
                    $rep = Yii::$app->params['reputations']['voteDown'];
                    $result['Message'] = "威望必须达到{$rep}才能投票";
                    $result['NewScore'] = $post->score;
                    if ($vote && $vote['useful'] == 1) {
                        $result['LastVoteTypeId'] = 2;
                    }
                } elseif ($user->id == $post->uid) {
                    $result['Message'] = "不能给自己的帖子投票";
                    $result['NewScore'] = $post->score;
                } elseif ($post->poststate->isLock()) {
                    $result['Message'] = "帖子被锁定，不能投票";
                    $result['NewScore'] = $post->score;
                } elseif (($remaining = UserLimits::remaining($user->id, UserLimits::ACTION_VOTE)) == 0) {
                    $result['Message'] = "达到24小时最大投票门限，不能再投票";
                    $result['NewScore'] = $post->score;
                } else {
                    if ($vote) {
                        if ($vote['useful'] == 0 || $vote['useful'] == 1) {
                            //更新vote
                            //更新帖子（post）分数
                            //更新user_stat中的数量
                            //更新问题的 aupvotes
                            //添加activity
                            //更新最后活动时间
                            //更新威望
                            $score = ($vote['useful'] == 0) ? -1 : -2;
                            $upvoteDelta = ($vote['useful'] == 0) ? 0 : -1;

                            $vote->useful = -1;
                            $vote->time = time();
                            $vote->save();

                            Post::updateAllCounters(["useless" => -1, "score" => $score], ['id' => $id]);
                            UserStat::updateAllCounters(['downvotecount' => 1, 'upvotecount' => $upvoteDelta], ['id' => $user->id]);

                            //如果是答案，则更新问题的aupvotes数量
                            if ($post->isAnswer()) {
                                $qid = $post->idv;
                                Post::updateAllCounters(["aupvotes" => -1], ['id' => $qid]);
                            }
                            $activity = new Activity;
                            $activity->type = 'votedown';
                            $activity->typeid = $post->id;
                            $activity->uid = Yii::$app->user->getId();
                            $activity->data = array(
                                'qid' => $post->id,
                                'qtitle' => $post->title
                            );
                            $activity->save();
                            $user->updateLastActivity();

                            if ($score == -2 && !$post->isWiki()) {
                                //如果是有用票->到无用票，首先计算有用票->取消无用票
                                //帖子作者威望
                                $repute = new Repute;
                                $repute->updatePostReputations($post, $post->author, ($post->isQuestion()) ? Repute::Q_UPVOTE_CANCEL : Repute::A_UPVOTE_CANCEL);
                                $repute->calReputation($post->author);
                            }

                            if (!$post->isWiki()) {
                                //帖子作者威望
                                $repute = new Repute;
                                $repute->updatePostReputations($post, $post->author, Repute::QA_DOWNVOTE);
                                $repute->calReputation($post->author);

                                //操作者威望
                                $repute = new Repute;
                                $repute->updatePostReputations($post, $user, Repute::DOWNVOTE_QA);
                                $repute->calReputation($user);
                            }

                            $result['Success'] = true;
                            $result['NewScore'] = $post->score - $score;
                        }
                    } else {
                        $vote = new Vote;
                        $vote->useful = Vote::DOWNVOTE;
                        $vote->postid = $id;
                        $vote->uid = $uid;
                        $vote->time = time();
                        $vote->fav = 0;
                        $vote->save();

                        Post::updateAllCounters(["useless" => -1, "score" => -1], ['id' => $id]);
                        UserStat::Model()->updateCounters(['downvotecount' => 1], ['id' => $user->id]);

                        $activity = new Activity;
                        $activity->type = 'votedown';
                        $activity->typeid = $post->id;
                        $activity->uid = Yii::$app->user->getId();
                        $activity->data = array(
                            'qid' => $post->id,
                            'qtitle' => $post->title
                        );
                        $activity->save();
                        $user->updateLastActivity();

                        if (!$post->isWiki()) {
                            //帖子作者威望
                            $repute = new Repute;
                            $repute->updatePostReputations($post, $post->author, Repute::QA_DOWNVOTE);
                            $repute->calReputation($post->author);

                            //操作者威望
                            $repute = new Repute;
                            $repute->updatePostReputations($post, $user, Repute::DOWNVOTE_QA);
                            $repute->calReputation($user);
                        }

                        $result['Success'] = true;
                        $result['NewScore'] = $post->score - 1;
                    }
                    UserLimits::limitIncrement($user->id, UserLimits::ACTION_VOTE);
                }
                break;
                
            case $this->voteTypeIds['favorite']:
                if ($vote && $vote['fav'] == 1) {
                    $vote->fav = 0;
                    $vote->save();
                    Post::updateAllCounters(['favcount' => -1], ['id' => $id]);
                    $result['Success'] = true;
                } else {
                    if ($vote == null) {
                        $vote = new Vote;
                    }
                    $vote->postid = $id;
                    $vote->uid = $uid;
                    $vote->fav = Vote::FAV;
                    $vote->favtime = time();
                    $vote->save();
                    Post::updateAllCounters(['favcount' => 1], ['id' => $id]);

                    $activity = new Activity;
                    $activity->type = 'fav';
                    $activity->typeid = $post->id;
                    $activity->uid = Yii::$app->user->getId();
                    $activity->data = array(
                        'qid' => $post->id,
                        'qtitle' => $post->title
                    );
                    $activity->save();
                    $user->updateLastActivity();
                    $result['Success'] = true;
                }
                break;

            case $this->voteTypeIds['close']:
                //先判断问题是否已经关闭
                //如果是管理员/版主，直接关闭
                //如果是有权限的会员，直接投票
                //如果没有权限，啥也不干
                $reason = $_POST['close-reason-id'];
                $questionId = $_GET['postid'];
                $dupId = $_POST['duplicate-question-id'];

                if ($post->poststate->isLock()) {
                    $result['Message'] = "帖子被锁定，不能关闭/打开";
                } elseif ($post->poststate->isOpen()) {
                    $post->loadBounty();
                    if ($post->openBounty !== null) {
                        $result['Message'] = "正在悬赏的问题不能关闭！";
                    } elseif ($user->isAdmin() || $user->isMod()) {
                        $post->poststate->close = PostState::POST_CLOSE;
                        $post->poststate->closecount = 0;
                        $post->poststate->closereason = $_POST['close-reason-id'];
                        $post->poststate->closetime = time();
                        $post->poststate->save();

                        PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['close']));
                        $result['Success'] = true;
                    } elseif ($user->checkPerm('closeQuestions') || ($user->checkPerm('closeMyQuestions') && $post->isSelf())) {
                        $post->loadBounty();
                        if ($user->checkPerm('closeQuestions') && $post->openBounty !== null) {
                            $result['Message'] = "正在悬赏的问题不能关闭！";
                        } else {
                            $postmod = PostMod::Model()->find("uid=:uid AND postid=:questionid AND type=:type", array(':uid' => $uid, ':questionid' => $questionId, ':type' => $this->voteTypeIds['close']));
                            if (!$postmod) {
                                $remaining = UserLimits::remaining($uid, UserLimits::ACTION_CLOSE_VOTE);

                                if ($remaining == 0) {
                                    $result['Message'] = "你已经用完了今天的关闭投票数量";
                                } else {
                                    $postmod = new PostMod;
                                    $postmod->uid = $uid;
                                    $postmod->postid = $questionId;
                                    $postmod->type = $this->voteTypeIds['close'];
                                    $postmod->typeid = $_POST['close-reason-id'];
                                    $postmod->oid = isset($_POST['duplicate-question-id']) ? intval($_POST['duplicate-question-id']) : 0;
                                    $postmod->save();

                                    UserLimits::limitIncrement($uid, UserLimits::ACTION_CLOSE_VOTE);
                                    $post->poststate->closecount++;
                                    $v = Yii::$app->params['posts']['maxCloseVotes'] - $post->poststate->closecount;
                                    if ($post->poststate->closecount >= Yii::$app->params['posts']['maxCloseVotes']) {
                                        $post->poststate->close = PostState::POST_CLOSE;
                                        $post->poststate->closecount = 0;
                                        $post->poststate->closereason = $postmod->typeid;
                                        PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['close']));
                                    }
                                    $post->poststate->save();

                                    $result['Success'] = true;
                                    $result['Message'] = ($v > 0) ? "({$post->poststate->closecount})" : null;
                                    $result['NewScore'] = $v;
                                }
                            } else {
                                $result['Message'] = "你已经投过关闭票了";
                            }
                        }
                    }
                }
                break;

            case $this->voteTypeIds['reopen']:
                $questionId = $_GET['postid'];

                if ($post->poststate->isLock()) {
                    $result['Message'] = "帖子被锁定，不能关闭/打开";
                } elseif ($post->poststate->isClose()) {
                    if ($user->isAdmin() || $user->isMod()) {
                        $post->poststate->close = PostState::POST_OPEN;
                        $post->poststate->closecount = 0;
                        $post->poststate->save();
                        PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['reopen']));
                        $result['Success'] = true;
                    } elseif ($user->checkPerm('closeQuestions') || ($user->checkPerm('closeMyQuestions') && $post->isSelf())) {
                        $postmod = PostMod::Model()->find("uid=:uid AND postid=:questionid AND type=:type", array(':uid' => $uid, ':questionid' => $questionId, ':type' => $this->voteTypeIds['reopen']));
                        if (!$postmod) {
                            $postmod = new PostMod;
                            $postmod->uid = $uid;
                            $postmod->postid = $questionId;
                            $postmod->type = $this->voteTypeIds['reopen'];
                            $postmod->save();

                            $post->poststate->closecount++;
                            $v = Yii::$app->params['posts']['maxCloseVotes'] - $post->poststate->closecount;
                            if ($post->poststate->closecount >= Yii::$app->params['posts']['maxCloseVotes']) {
                                $post->poststate->close = PostState::POST_OPEN;
                                $post->poststate->closecount = 0;
                                PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['reopen']));
                            }
                            $post->poststate->save();

                            $result['Success'] = true;
                            $result['Message'] = ($v > 0) ? "({$post->poststate->closecount})" : null;
                            $result['NewScore'] = Yii::$app->params['posts']['maxCloseVotes'] - $post->poststate->closecount;
                        } else {
                            $result['Message'] = "你已经投过重新打开票了";
                        }
                    }
                }
                break;

            case $this->voteTypeIds['bountyClose']:
                if ($post->isAnswer() && $user->checkPerm('bountyStart')) {
                    $question = $post->question;
                    if ($question->bountying && $question->bountying->uid == Yii::$app->user->getId()) {
                        $amount = $question->bountying->amount;
                        $hours = Yii::$app->params['posts']['closeBountyFreezeTime'];
                        if ($amount % 50 != 0) {
                            $result['Message'] = '悬赏必须是50的倍数';
                        } elseif (time() - $question->bountying->time < $hours * 3600) {
                            $result['Message'] = "悬赏启动{$hours}小时之后可以颁发赏金";
                        } else {
                            $touser = $post->author;

                            $question->bountying->status = Bounty::STATUS_MANUAL;
                            $question->bountying->touid = $touser->id;
                            $question->bountying->totime = time();
                            $question->bountying->answerid = $post->id;
                            $question->bountying->bonus = $question->bountying->amount;
                            $question->bountying->save();

                            $repute = new Repute;
                            $repute->updatePostReputations($post, $post->author, Repute::AWARD, $amount);
                            $repute->calReputation($post->author, $amount);

                            $inbox = new Inbox;
                            $inbox->title = $question->title;
                            $inbox->url = $this->createUrl('questions/view', array('id' => $question->id, '#' => $post->id));
                            $inbox->summary = String::filterTitle($post->content, 100);
                            $inbox->type = Inbox::$TYPE['bounty'];
                            $inbox->uid = $post->uid;
                            $inbox->save();
                            $result['Message'] = '悬赏必须是50的倍数';
                            $result['Success'] = true;
                        }
                    } else {
                        $result['Message'] = '错误操作';
                    }
                }
                break;
            case $this->voteTypeIds['deletion']:
                //先判断问题是否已经删除
                //如果是版主|管理员，直接删除
                //如果是有权限的会员，直接投票
                //如果没有权限，啥也不干
                $questionId = $_GET['postid'];

                if (!$post->poststate->isDelete()) {
                    if ($post->isSelf() || $user->isAdmin() || $user->isMod()) {
                        $post->poststate->delete = PostState::POST_DELETE;
                        $post->poststate->deletecount = 0;
                        $post->poststate->deletetime = time();
                        $post->poststate->save();
                        $result['Success'] = true;
                        $result['NewScore'] = -1;
                        $result['Message'] = "恢复";
                        PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['deletion']));
                    } elseif ($user->checkPerm('moderatorTools')) {
                        $postmod = PostMod::Model()->find("uid=:uid AND postid=:questionid AND type=:type", array(':uid' => $uid, ':questionid' => $questionId, ':type' => $this->voteTypeIds['deletion']));
                        if (!$postmod) {
                            $postmod = new PostMod;
                            $postmod->uid = $uid;
                            $postmod->postid = $questionId;
                            $postmod->type = $this->voteTypeIds['deletion'];
                            $postmod->save();

                            $post->poststate->deletecount++;
                            $v = Yii::$app->params['posts']['maxDeleteVotes'] - $post->poststate->deletecount;
                            if ($post->poststate->deletecount >= Yii::$app->params['posts']['maxDeleteVotes']) {
                                $post->poststate->delete = PostState::POST_DELETE;
                                $post->poststate->deletecount = 0;
                                $post->poststate->deletetime = time();
                            }
                            $post->poststate->save();
                            $result['Success'] = true;
                            $result['Message'] = ($v > 0) ? "删除({$post->poststate->deletecount})" : null;
                            $result['NewScore'] = $v;
                        } else {
                            $result['Message'] = "你已经投过票了";
                        }
                    } else {
                        $result['Message'] = "没有权限";
                    }
                }
                break;
            case $this->voteTypeIds['undeletion']:
                //先判断问题是否已经删除
                //如果是版主|管理员|帖子作者，直接删除
                //如果是有权限的会员，直接投票
                //如果没有权限，啥也不干
                $questionId = $_GET['postid'];

                if ($post->poststate->isDelete()) {
                    if ($post->isSelf() || $user->isAdmin() || $user->isMod()) {
                        $post->poststate->delete = PostState::POST_UNDELETE;
                        $post->poststate->deletecount = 0;
                        $post->poststate->save();
                        $result['Success'] = true;
                        PostMod::model()->deleteAll("postid=:postid AND type=:type", array(':postid' => $post->id, ':type' => $this->voteTypeIds['undeletion']));
                    } elseif ($user->checkPerm('moderatorTools')) {
                        $postmod = PostMod::Model()->find("uid=:uid AND postid=:questionid AND type=:type", array(':uid' => $uid, ':questionid' => $questionId, ':type' => $this->voteTypeIds['undeletion']));
                        if (!$postmod) {
                            $postmod = new PostMod;
                            $postmod->uid = $uid;
                            $postmod->postid = $questionId;
                            $postmod->type = $this->voteTypeIds['undeletion'];
                            $postmod->save();

                            $post->poststate->deletecount++;
                            $v = Yii::$app->params['posts']['maxDeleteVotes'] - $post->poststate->deletecount;
                            if ($post->poststate->deletecount >= Yii::$app->params['posts']['maxDeleteVotes']) {
                                $post->poststate->delete = PostState::undeletion;
                                $post->poststate->deletecount = 0;
                            }
                            $post->poststate->save();
                            $result['Success'] = true;
                            $result['Message'] = ($v > 0) ? "恢复({$post->poststate->deletecount})" : null;
                            $result['NewScore'] = $v;
                        } else {
                            $result['Message'] = "你已经投过票了";
                        }
                    }
                } else {
                    $result['Message'] = "没有权限";
                }
                break;
            case $this->voteTypeIds['spam'] :
            case $this->voteTypeIds['offensive'] : //举报
                if ($post->poststate->isLock()) {
                    $result['Success'] = true;
                    $result['Message'] = "帖子已经锁定，不能举报";
                } elseif ($post->poststate->isDelete()) {
                    $result['Success'] = true;
                    $result['Message'] = "帖子已删除，不能举报";
                } else {
                    $remaining = UserLimits::remaining($uid, UserLimits::ACTION_SPAM_FLAG);

                    if ($remaining == 0) {
                        $result['Success'] = true;
                        $result['Message'] = "已经用完了今天的举报次数";
                    } else {
                        $flag = new Flag;
                        $postid = $post->id;
                        $has = $flag->check($postid, $uid);
                        if ($has) {
                            $result['Success'] = false;
                            $result['Message'] = "你已经成功举报过了";
                        } else {
                            $flagcount = $flag->getFlagCount($postid);
                            $min = Yii::$app->params['posts']['maxFlagVotes'];
                            $max = Yii::$app->params['posts']['maxFlagLockVotes'];

                            $min = 1;
                            $max = 2;
                            if (($flagcount >= ($min - 1)) && $flagcount < $max - 1) {
                                if ($post->status == Post::STATUS_INIT) {
                                    $post->status(Post::STATUS_HIDDEN);
                                }

                                $flag->idval = $postid;
                                $flag->idtype = Flag::IDTYPE_P;
                                $flag->save();
                                UserLimits::limitIncrement($uid, UserLimits::ACTION_SPAM_FLAG);

                                $result['Success'] = true;
                                $result['Message'] = "举报成功";
                            } elseif ($flagcount >= $max - 1) {
                                $time = time();
                                $lock = $post->poststate->isLock();
                                if (!$post->poststate->isLock()) {
                                    $post->poststate->lock = PostState::POST_LOCK;
                                    $post->poststate->locktime = $time;
                                }
                                if (!$post->poststate->isDelete()) {
                                    $post->poststate->delete = PostState::POST_DELETE;
                                    $post->poststate->deletetime = $time;
                                }
                                $post->poststate->save();

                                if (!$lock) {
                                    $repute = new Repute;
                                    $repute->uid = $post->uid;
                                    $repute->postid = $post->id;
                                    $repute->type = Repute::FLAG_DELETE;
                                    if ($post->author->reputation <= Yii::$app->params['posts']['flagRepLose']) {
                                        $lostrep = $post->author->reputation - 1;
                                    } else {
                                        $lostrep = Yii::$app->params['posts']['flagRepLose'];
                                    }
                                    $repute->reputation = -$lostrep;
                                    $repute->save();
                                    $repute->calReputation($post->author, -$lostrep);

                                    //@todo 发通知，威望记录
//									$author = User::Model()->findByPk($post->uid);
//									$author->reputation = ($author->reputation<=Yii::$app->params['posts']['flagRepLose']) ? 1 : ($author->reputation-Yii::$app->params['posts']['flagRepLose']);
//									$author->save();
                                }

                                $flag->idval = $postid;
                                $flag->idtype = Flag::IDTYPE_P;
                                $flag->save();
                                UserLimits::limitIncrement($uid, UserLimits::ACTION_SPAM_FLAG);

                                $flag->pass($post->id);
                            } else {
                                $result['Success'] = true;
                                $result['Message'] = "谢谢，你已经成功举报";
                                $flag->idval = $postid;
                                $flag->idtype = Flag::IDTYPE_P;
                                $flag->save();
                                UserLimits::limitIncrement($uid, UserLimits::ACTION_SPAM_FLAG);
                            }


                            /**
                              if ($flagcount == Yii::$app->params['posts']['maxFlagVotes']-1){
                              $post->status(Post::STATUS_BAN);
                              $time = time();
                              $post->poststate->lock = PostState::POST_LOCK;
                              $post->poststate->locktime = $time;
                              $post->poststate->delete = PostState::POST_DELETE;
                              $post->poststate->deletetime = $time;
                              $post->poststate->save();
                              $author = User::Model()->findByPk($post->uid);
                              $author->reputation = ($author->reputation<=Yii::$app->params['posts']['flagRepLose']) ? 1 : ($author->reputation-Yii::$app->params['posts']['flagRepLose']);
                              $author->save();

                              $flag->pass($post->id);

                              $result['Success'] = true;
                              $result['Message'] = "举报成功";
                              }else{
                              $result['Success'] = true;
                              $result['Message'] = "谢谢，你已经成功举报";
                              $flag->idval = $postid;
                              $flag->idtype = Flag::IDTYPE_P;
                              $flag->save();
                              }* */
                        }
                    }
                }
                break;
        }
        echo \yii\helpers\Json::encode($result);
    }

    public function actionComments()
    {
        $request = Yii::$app->request;
        $op = $request->get('op');
        $commentId = $request->get('id');
        $typeid = $request->get('typeid');
        $uid = Yii::$app->user->getId();
        $result = array(
            'Success' => false,
            'NewScore' => -1,
            'Message' => null,
            'Refresh' => false,
        );
        $comment = Comment::findOne($commentId);
        if ($op == 'vote') {
            switch ($typeid) {
                case $this->voteTypeIds['deletion'] :
                    //自己或版主可以删除

                    if ($comment['uid'] == $this->me->id || $this->me->isMod() || $this->me->isAdmin()) {
                        $comment->delete();
                        $result['Success'] = true;
                    }
                    break;
                case $this->voteTypeIds['upMod']:
                    $commentVote = CommentVote::findOne(['commentid' => $commentId, 'voteTypeId' => $typeid, 'uid' => $uid]);
                    if ($commentVote) {
                        $result['Message'] = "已经投过票了";
                    } elseif ($comment->uid == Yii::$app->user->id) {
                        $result['Message'] = "不能给自己的评论投票";
                    } else {
                        $vote = new CommentVote;
                        $vote->commentid = $commentId;
                        $vote->uid = $uid;
                        $vote->voteTypeId = $typeid;
                        $vote->time = time();
                        $vote->save();

                        $comment->upvotes++;
                        $comment->update();

                        $result['Success'] = true;
                        $result['NewScore'] = $comment->upvotes;
                    }
                    break;
                case $this->voteTypeIds['offensive']:
                    $commentVote = CommentVote::findOne('commentid=:commentid AND voteTypeId=:typeid AND uid=:uid', ['commentid' => $commentId, 'typeid' => $typeid, 'uid' => $uid]);
                    if ($commentVote) {
                        $result['Message'] = "已经举报过";
                    } elseif ($comment->uid == Yii::$app->user->getId()) {
                        $result['Message'] = "不能举报自己的评论";
                    } else {
                        $vote = new CommentVote;
                        $vote->commentid = $commentId;
                        $vote->uid = $uid;
                        $vote->voteTypeId = $typeid;
                        $vote->time = time();
                        $vote->message = String::filterTitle($_POST['text']);
                        $vote->save();

                        $result['Success'] = true;
                        $result['NewScore'] = 0;
                    }

                    $has = CommentVote::check($commentId, $this->voteTypeIds['offensive'], $uid);
                    if ($has) {
                        $result['Message'] = "你已经成功举报过了";
                    } else {
                        $flagcount = CommentVote::getFlagCount($commentId, $this->voteTypeIds['offensive']);
                        if ($flagcount == Yii::$app->params['posts']['maxFlagVotes'] - 1) {
                            $comment->status = Comment::STATUS_DELETE;
                            $comment->update();

                            $result['Success'] = true;
                            $result['Message'] = "举报成功";
                        } else {
                            $result['Success'] = true;
                            $result['Message'] = "谢谢，你已经成功举报";
                        }
                        $flag = new CommentVote;
                        $flag->commentid = $commentId;
                        $flag->uid = $uid;
                        $flag->voteTypeId = $typeid;
                        $flag->time = time();
                        $flag->message = String::filterTitle($request->post('text'));
                        $flag->save();
                    }
                    break;
            }
            echo \yii\helpers\Json::encode($result);
        } elseif ($op == 'flag') {
            $flagcount = CommentVote::getFlagCount($commentId, $this->voteTypeIds['offensive']);
            echo $this->renderPartial('comments_popup_flag', array('comment' => $comment, 'flagcount' => $flagcount));
        } else {
            $comment = Comment::findOne($commentId);
            if (($comment !== null) && $comment->isNotTimeout() && $comment->isself()) {
                $comment->message = String::filterString($_POST['comment'], 300, array('in_slashes' => 0, 'out_slashes' => 0, 'html' => -1));
                $comment->save();

                $criteria = new CDbCriteria(array(
                    'condition' => 'idv=' . $comment->idv,
                ));

                $comments = Comment::findAll(['idv' => $comment->idv]);
                echo $this->renderPartial('_comment_ajax', array('comments' => $comments));
            } else {
                echo "false";
            }
        }
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $op = $request->get('op');
        if ($op == 'comments') {
            if (isset($_POST['comment'])) {
                $post = $this->loadModel($request->get('postid'));
                if ($post->isAnswer()) {
                    $question = Post::findOne($post->idv);
                    $qid = $question->id;
                    $qtitle = $question->title;
                } elseif ($post->isQuestion()) {
                    $qid = $post->id;
                    $qtitle = $post->title;
                }
                //无权编辑
                if ($post->poststate->isLock()) {
                    echo "帖子被锁定";
                    return;
                }
                if (!$this->me->checkPerm('comment') && $post->uid != $this->me->id && !$post->isAsker()) {
                    echo "帖子被锁定";
                    return;
                }
                
                $comment = new Comment;
                $comment->addComment($post);

                $activity = new Activity;
                $activity->uid = $comment->uid;
                $activity->type = 'comment';
                $activity->typeid = $comment->id;
                $activity->data = [
                    'summary' => $comment->message,
                    'qid' => $qid,
                    'qtitle' => $qtitle,
                ];
                $activity->save();

                //通知
                $inbox = new Inbox;
                $inbox->title = $qtitle;
                if ($post->isAnswer()) {
                    $inbox->url = Yii::$app->urlManager->createUrl(['questions/view', 'id' => $qid, '#' => $post->id]);
                } else {
                    $inbox->url = Yii::$app->urlManager->createUrl(['questions/view', 'id' => $qid]);
                }
                $inbox->summary = String::filterTitle($comment->message, 300);
                $inbox->type = Inbox::$TYPE['comment'];
                $inbox->uid = $post->uid;
                $inbox->save();

                //邮件 @todo 事件通知的方式实现
                /*
                $author = $post->author;
                if (!empty($author->email) && $author->notify['commented']) {
                    if ($post->isAnswer()) {
                        $postUrl = Yii::$app->urlManager->createAbsoluteUrl(['questions/view', 'id' => $qid, '#' => $post->id]);
                    } else {
                        $postUrl = Yii::$app->urlManager->createAbsoluteUrl(['questions/view', 'id' => $qid]);
                    }
                    $data = array(
                        'user' => $author,
                        'url' => $postUrl,
                        'title' => $qtitle,
                        'email' => $author->email
                    );
                    $body = $this->render('/email/new_comment', ['data' => $data]);
                    $subject = "您的帖子有新的评论：" . $qtitle;
                    MailQueue::addQueue($author->email, $subject, $body);
                }
                */
                $comments = \app\models\Comment::findAll(['idv' => $post->id]);
                echo $this->renderPartial('_comment_ajax', array('comments' => $comments));
            } else {  //显示全部评论
                $postid = Yii::$app->request->get('id');
                $comments = Comment::findAll(['idv' => $postid, 'status' => Comment::STATUS_OK]);
                echo $this->render('_comment_ajax', ['comments' => $comments]);
            }
        } elseif ($op == 'body') {
            $data = Post::Model()->findByPk($_GET['id']);
            echo $this->render('_body', array('data' => $data), true);
        }
    }

    public function actionDelete()
    {
        if (Yii::$app->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel()->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(array('index'));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionIndex()
    {
        $criteria = new CDbCriteria(array(
            'condition' => 'status=' . Post::STATUS_PUBLISHED,
            'order' => 'update_time DESC',
            'with' => 'commentCount',
        ));
        if (isset($_GET['tag']))
            $criteria->addSearchCondition('tags', $_GET['tag']);

        $dataProvider = new CActiveDataProvider('Post', array(
            'pagination' => array(
                'pageSize' => Yii::$app->params['pages']['postsPerPage'],
            ),
            'criteria' => $criteria,
        ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionPopup()
    {
        if (!($this->me->isAdmin() || $this->me->isMod()) && !$this->me->checkPerm('flag')) {
            $th = Yii::$app->params['reputations']['flag'];
            echo "威望达到{$th}才能举报.";
        } else {
            $do = $_GET['do'];
            $postid = $_GET['postid'];
            $uid = Yii::$app->user->getId();
            if ($do == 'close') {
                $question = Post::findOne($postid);
                echo $this->renderPartial('popup-close', array('question' => $question));
            } elseif ($do == 'flag') {
                $post = Post::findOne($postid);
                $remaining = UserLimits::remaining($uid, UserLimits::ACTION_INFORM_MOD);
                $spamRemaining = UserLimits::remaining($uid, UserLimits::ACTION_SPAM_FLAG);
                echo $this->renderPartial('popup_flag', array('post' => $post, 'spamRemaining' => $spamRemaining, 'remaining' => $remaining));
            } else {
                $flagcount = Flag::getFlagcount($postid, $uid);
                echo $this->renderPartial('popup', array('postid' => $postid, 'flagcount' => $flagcount));
            }
        }
    }

    public function actionValidateduplicate()
    {
        $val = $_GET['val'];
        $post = Post::Model()->findByPk($val);
        if ($post && $post['idtype'] == Post::IDTYPE_Q) {
            $data = array(
                'success' => true,
                'id' => $post->id,
                'url' => $this->createUrl('question/view', array('id' => $post->id)),
                'title' => $post->title,
                'body' => $post->content,
                'tags' => ""
            );
        } else {
            $data = array(
                'success' => false,
                'id' => 0,
                'url' => '',
                'title' => '',
                'body' => '',
                'tags' => ""
            );
        }
        echo CJSON::encode($data);
    }

    public function actionProtect()
    {
        //权限管理
        //设置
        if ($this->me->isAdmin() || $this->me->isMod() || $this->me->checkPerm('protect')) {
            $postid = Yii::$app->request->post('id');
            $post = Post::findOne($postid);
            if ($post && $post->isQuestion()) {
                $post->poststate->protect = PostState::POST_PROTECT;
                $post->poststate->protectuid = Yii::$app->user->getId();
                $post->poststate->protecttime = time();
                $post->poststate->save();
            }
        }
    }

    public function actionUnprotect()
    {
        if ($this->me->isAdmin() || $this->me->isMod() || $this->me->checkPerm('protect')) {
            $postid = Yii::$app->request->post('id');
            $post = Post::findOne($postid);
            if ($post && $post->isQuestion()) {
                $post->poststate->protect = PostState::POST_UNPROTECT;
                $post->poststate->save();
            }
        }
    }

    public function actionLock()
    {
        if ($this->me->isAdmin()) {
            $postid = Yii::$app->request->post('id');
            $post = Post::findOne($postid);
            if ($post && $post->isQuestion()) {
                $post->poststate->lock = PostState::POST_LOCK;
                $post->poststate->lockuid = Yii::$app->user->getId();
                $post->poststate->locktime = time();
                $post->poststate->save();
            }
        }
    }

    public function actionUnlock()
    {
        if ($this->me->isAdmin()) {
            $postid = Yii::$app->request->post('id');
            $post = Post::findOne($postid);
            if ($post && $post->isQuestion()) {
                $post->poststate->lock = PostState::POST_UNLOCK;
                $post->poststate->save();
            }
        }
    }

    public function loadModel($id = null)
    {
        if ($id === null) {
            $id = $_GET['id'];
        }
        if ($this->_model === null) {
            if (isset($id)) {
                $this->_model = Post::findOne($id);
            }
            if ($this->_model === null)
                throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    public function actionCommenthelp()
    {
        echo $this->renderPartial('_comment_help');
    }

    /**
     * 发布悬赏
     */
    public function actionBountystart()
    {
        $amount = intval($_POST['amount']);
        $qid = intval($_GET['qid']);
        $result = array(
            'Success' => false,
            'Message' => "",
            'Refresh' => false,
            'RedirectTo' => null,
            'NewScore' => 0,
            'ShowShareTip' => false
        );
        if (!$this->me->checkPerm('setBounties')) {
            $result['Message'] = '必须至少有75的威望才能发布悬赏';
        } elseif ($amount == 0 || $amount % 50 != 0) {
            $result['Message'] = '悬赏必须是50的倍数';
        } else {
            $post = Post::findOne($qid);
            if ($post && $post->isQuestion()) {
                $hours = Yii::$app->params['posts']['bountyFreezeTime'];
                if (time() - $post->createtime < $hours * 3600) {
                    $result['Message'] = "问题被提出{$hours}小时后可以开始悬赏。";
                } elseif ($this->me->checkPerm('bountyStart') && $this->me->reputation > $amount && !$post->hasOpenBounty()) {
                    //Post::hasOpenbounty函数未实现
                    $bounty = new Bounty();
                    $bounty->uid = Yii::$app->user->getId();
                    $bounty->questionid = $qid;
                    $bounty->amount = $amount;
                    $bounty->time = time();
                    $bounty->endtime = $bounty->time + Yii::$app->params['posts']['rewardLife'] * 86400;
                    $bounty->save();

//					$post->hasbounty = 1;
//					$post->update(array('hasbounty'));

                    $repute = new Repute;
                    $repute->uid = $this->me->id;
                    $repute->postid = $post->id;
                    $repute->type = Repute::OFFER_AWARD;
                    $repute->reputation = -$amount;
                    $repute->save();
                    $repute->calReputation($this->me, -$amount);

                    $result['Success'] = true;
                    $result['Message'] = '悬赏成功！';
                } else {
                    $result['Message'] = '没有权限！';
                }
            } else {
                $result['Message'] = '出错了';
            }
        }
        echo \yii\helpers\Json::encode($result);
    }

    public function actionHeartbeat()
    {
        $result = array("draftSaved" => false);
        $uid = Yii::$app->user->getId();
        $type = $_GET['type'];
        switch ($type) {
            case 'ask':
                if (isset($_POST['text'])) {
                    $draft = Draft::model()->find("uid=:uid AND type=:type", array(":uid" => $uid, ":type" => Draft::TYPE_ASK));
                    if ($draft == null) {
                        $draft = new Draft;
                    }
                    $draft->uid = $uid;
                    $draft->type = Draft::TYPE_ASK;
                    $draft->title = empty($_POST['title']) ? "" : $_POST['title'];
                    $draft->content = !empty($_POST['text']) ? $_POST['text'] : "";
                    $draft->tagnames = !empty($_POST['tagnames']) ? $_POST['tagnames'] : "";
                    if ($draft->save()) {
                        $result = array("draftSaved" => true);
                    }
                }
                break;
            case 'answer':
                if (isset($_POST['text'])) {
                    $postid = intval($_POST['postid']);
                    $draft = Draft::model()->find("uid=:uid AND type=:type AND postid=:postid", array(":uid" => $uid, ":type" => Draft::TYPE_ANSER, ":postid" => $postid));
                    if ($draft == null) {
                        $draft = new Draft;
                    }
                    $draft->uid = $uid;
                    $draft->type = Draft::TYPE_ANSER;
                    $draft->content = !empty($_POST['text']) ? $_POST['text'] : "";
                    $draft->postid = $postid;
                    if ($draft->save()) {
                        $result = array("draftSaved" => true);
                    }
                }
                break;
        }


        echo CJSON::encode($result);
    }

}
