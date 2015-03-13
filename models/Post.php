<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use Yii;
use app\components\String;
use app\models\UserStat;
use app\models\Revision;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property integer $idv
 * @property string $idtype
 * @property integer $revisionid
 * @property integer $uid
 * @property integer $createtime
 * @property integer $activity
 * @property integer $lastedit
 * @property integer $status
 * @property integer $accepted
 * @property integer $score
 * @property integer $answercount
 * @property integer $commentcount
 * @property integer $viewcount
 * @property integer $favcount
 * @property integer $flagcount
 * @property string $title
 * @property string $content
 * @property string $excerpt
 * @property string $tags
 * @property integer $useful
 * @property integer $useless
 * @property integer $aupvotes
 * @property integer $wiki
 */
class Post extends ActiveRecord
{

    const STATUS_PENDING = 1;

    /**
     * 帖子处于正常状态
     */
    const STATUS_INIT = 0;

    /**
     * 帖子被隐藏，在首页不显示（或理解为在列表页不显示）
     */
    const STATUS_HIDDEN = -1;
    const STATUS_BAN = -2;
    const STATUS_DELETE = -3;

    /**
     * Post类型
     * @var string
     */
    const IDTYPE_Q = 'question';

    /**
     * Post类型：答案
     * @var string
     */
    const IDTYPE_A = 'answer';

    /**
     * Post类型：标签
     * @var string
     */
    const IDTYPE_T = 'tag';

    /*
     * 悬赏标志
     */
    const BOUNTY_OPEN = 1; //当前有正在进行中的悬赏

    /**
     * 如果Post类型是问题，表示该问题已经采纳某条回答 
     * 如果Post类型是回答，表示该回答已经被采纳
     * @var integer
     */
    const ACCEPTED = 1;

    /**
     * 非最佳（未采纳）答案
     */
    const UNACCEPT = 0;

    /**
     * wiki模式:1
     * @var int
     */
    const WIKI_MODE = 1;

    /**
     * 非wiki模式
     * @var int
     */
    const UNWIKI_MODE = 0;

    public static $status = array(
        -1 => '删除',
        0 => '正常'
    );
    public $_newTags = array();
    public $_oldTags = array();

    /**
     * 原始输入标签
     */
    public $_inputTags = "";

    /**
     * 进行中的悬赏
     * @var Bounty
     */
    public $openBounty = null;
    public $toCloseBounty = null;
    public $closeBounty = array();
    public $bountyAmount = 0;
    public $hasFav;

    /**
     * 是否投票标志
     * 1 => upmod
     * -1=> downmod
     * 0 => default
     */
    public $hasVote;

    /**
     * 当前用户是否已经回答过该问题
     * @var bool
     */
    private $hasExistAnswer = NULL;
    
    public $editComment = null;
    
    /** */
    public $currentRevision = null;

    public static function tableName()
    {
        return '{{%post}}';
    }

    public function scenarios()
    {
        return [
            'qask' => ['title', 'content', 'tags'],
            'answer' => ['content'],
            'tag'
        ];
    }

    public function rules()
    {
        return array(
            array('title,content,tags', 'required', 'on' => 'qask'),
//			array('title','length','min'=>3,'max'=>100,'on'=>'qask'),
//			array('tags', 'match', 'pattern'=>'/^([\x{4e00}-\x{9fa5}]|\w|\s|[\.\+-_#]){1,60}$/u', 'message'=>'标签只能包括中文字符、字母、特殊字符(.+-_#)','on'=>'qask'),
//			array('tags', 'valtags','on'=>'qask'),
//			array('content','required','on'=>'answer,tag'),
            array('content', 'required', 'on' => 'answer'),
            ['editComment','string'],
//			array('wiki','in','range'=>array(0,1),'on'=>'answer')
        );
    }

    public function valtags($attribute, $params)
    {
        $this->_inputTags = $this->tags;
        $tags = $this->filterTags($this->tags);
        if (count($tags) == 0) {
            $this->addError('tags', '请至少添加一个标签');
        } else {
            $this->tags = implode(' ', $tags);
        }
    }

    public function scopes()
    {
        return array(
            'close' => array('condition' => 'poststate.close=1'),
            'deleted' => array('condition' => 'poststate.delete=1'),
            'question' => array('condition' => 'idtye=' . self::IDTYPE_Q),
            'unanswered' => array('condition' => 'aupvotes=0'),
        );
    }

    public function getUrl()
    {
        if ($this->isQuestion()) {
            return Url::to(['questions/view', 'id' => $this->id]);
        }
    }

    public function getAbsoluteUrl()
    {
        if ($this->isQuestion()) {
            return Yii::$app->urlManager->createAbsoluteUrl(['questions/view', 'id' => $this->id]);
        }
    }

    public function relations()
    {
        return array(
            'author' => array(self::BELONGS_TO, 'User', 'uid', 'condition' => 'author.status=' . User::STATUS_ACTIVE),
            'vote' => array(self::HAS_MANY, 'Vote', 'postid'),
            'poststate' => array(self::HAS_ONE, 'PostState', 'id'),
            'lastrevision' => array(self::BELONGS_TO, 'Revision', 'revisionid'),
            'comments' => array(self::HAS_MANY, 'Comment', 'idv', 'limit' => 3, 'condition' => 'comments.status=' . Comment::STATUS_OK),
            'question' => array(self::BELONGS_TO, 'post', 'idv', 'condition' => 'question.idtype="' . self::IDTYPE_Q . '"'),
            'tag' => array(self::HAS_ONE, 'Tag', 'postid', 'condition' => 'idtype="tag"'),
            'closeuids' => array(self::HAS_MANY, 'PostMod', 'postid', 'condition' => 'type=6'),
            'revCount' => array(self::STAT, 'Revision', 'postid', 'condition' => 'status=' . Revision::STATUS_OK),
            'bounties' => array(self::HAS_MANY, 'Bounty', 'questionid'),
            'bountying' => array(self::HAS_ONE, 'Bounty', 'questionid', 'condition' => 'status=' . Bounty::STATUS_OPEN . ' AND endtime>' . time()),
            'AScores' => array(self::STAT, 'Post', 'idv', 'condition' => 'idtype="' . self::IDTYPE_A . '"', 'select' => "sum(score)"),
        );
    }

    public function getPoststate()
    {
        return $this->hasOne(PostState::className(), ['id' => 'id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function getQuestion()
    {
        return $this->hasOne(Post::className(), ['id' => 'idv'])->where('post.idtype="' . self::IDTYPE_Q . '"');
    }

    public function getLastrevision()
    {
        return $this->hasOne(Revision::className(), ['id' => 'revisionid']);
    }

    public function getPoststatus()
    {
        $status = array(
            -1 => '删除',
            0 => '正常'
        );
        return $status[$this->status];
    }

    public function getComments()
    {
        return $this->hasMany(Comment::ClassName(),['idv' => 'id'])->where(['status' => Comment::STATUS_OK])->limit(3);
    }

    public function getRevcount()
    {
        return $this->hasMany(Revision::className(), ['postid' => 'id'])->where(['status' => Revision::STATUS_OK])->count();
    }
    
    public function getBounties()
    {
        return $this->hasMany(Bounty::className(), ['questionid' => 'id']);
    }
    
//    public function getBountying()
//    {
//                    'bountying' => array(self::HAS_ONE, 'Bounty', 'questionid', 'condition' => 'status=' . Bounty::STATUS_OPEN . ' AND endtime>' . time()),
//
//    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'createtime' => '创建时间',
            'answercount' => '回答数',
            'status' => '状态',
            'tags' => '标签',
        );
    }

    /**
     * 添加一个回答
     * @param 问题模型
     * @return boolean
     */
//    public function addAnswer($answer)
//    {
//        $answer->idv = $this->id;
//        $answer->idtype = self::IDTYPE_A;
//
//        $orginContent = $answer->content;
//        $answer->content = String::markdownToHtml($orginContent);
//        $answer->excerpt = String::filterTitle($orginContent, 200);
//        $answer->wiki = !$answer->isWiki() ? ($this->isWiki() ? self::WIKI_MODE : self::UNWIKI_MODE) : $answer->wiki;
//        $success = $answer->save();
//        if ($success) {
//            $poststate = new PostState();
//            $poststate->id = $answer->id;
//            $poststate->save();
//
//            $revision = new Revision;
//            $revision->postid = $answer->id;
//            $revision->revtime = $answer->createtime;
//            $revision->text = $orginContent;
////			$revision->title 	= $model->title;
//            $revision->uid = $answer->uid;
//            $revision->status = Revision::STATUS_OK;
//            $revision->comment = "第一个版本";
//            $revision->save();
//
//            $answer->revisionid = $revision->id;
//            $answer->update(array('revisionid'));
//
//            $this->answercount++;
//            $this->activity = time();
//            $this->setScenario('qask');
//            $this->update();
//            //更新个人统计数量
//            UserStat::updateAllCounters(['acount' => 1], 'id=:uid', array(':uid' => $answer->uid));
////			UserStat::Model()->updateCounters(array('acount'=>1),'id=:uid',array(':uid'=>$answer->uid));
//        }
//        return $success;
//    }

    /**
     * 检查当前用户是否已经存在答案
     * @param int $uid
     * @return bool
     * 		true  存在
     * 		false 不存在
     */
    public function checkExistAnswer($uid = 0)
    {
        if ($uid == 0)
            $uid = Yii::$app->user->getId();
        if ($this->hasExistAnswer === null) {
            return $this->hasExistAnswer = self::find()->where("idv=:questionid AND idtype=:idtype AND uid=:uid", [':uid' => $uid, ':questionid' => $this->id, ':idtype' => self::IDTYPE_A])->exists();
//			return $this->hasExistAnswer = $this->exists("idv=:questionid AND idtype=:idtype AND uid=:uid",array(':uid'=>$uid,':questionid'=>$this->id,':idtype'=>self::IDTYPE_A));
        } else {
            return $this->hasExistAnswer;
        }
    }

    public function status($status)
    {
        $this->status = $status;
        $this->update(array('status'));
    }

    /**
     * @return string the hyperlink display for the current comment's author
     */
    public function getAuthorLink()
    {
        if (!empty($this->url))
            return CHtml::link(CHtml::encode($this->author), $this->url);
        else
            return CHtml::encode($this->author);
    }

    /**
     * @return integer the number of comments that are pending approval
     */
    public function getPendingCommentCount()
    {
        return $this->count('status=' . self::STATUS_PENDING);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->createtime = time();
                $this->activity = $this->createtime;
                $this->uid = Yii::$app->user->getId();                
            } else {
                $time = time();
                $this->activity = $time;
                $this->lastedit = $time;
            }
            
            if ($this->isQuestion()) {
            } elseif ($this->isAnswer()) {
                if ($this->isNewRecord) {
                    $this->createtime = time();
                    $this->activity = $this->createtime;
                    $this->tags = "";
                    $this->uid = Yii::$app->user->getId();
                }
            } elseif ($this->isTag()) {
                if ($this->isNewRecord) {
                    $this->createtime = time();
                    $this->activity = $this->createtime;
                    $this->tags = "";
                    $this->uid = Yii::$app->user->getId();
                }
            }
            return true;
        } else
            return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function edit()
    {
        if ($this->save()) {
            if ($this->isNewRecord) {
                $revision = new Revision;
                $revision->postid = $this->id;
                $revision->revtime = $this->createtime;
                $revision->text = $this->content;
                $revision->title = $this->title;
                $revision->uid = $this->uid;
                $revision->status = Revision::STATUS_OK;
                $revision->comment = "第一个版本";
                $revision->save();

                $this->revisionid = $revision->id;
                $this->update(false, ['revisionid']);
            } else {
                $revision = new Revision;

                $revision->postid = $this->id;
                $revision->revtime = time();
                $revision->text = $this->content;
                $revision->title = $this->title;
                $revision->uid = Yii::$app->user->getId();
                //简化状态
                $revision->status = Revision::STATUS_OK; //($allowEdit) ? Revision::STATUS_OK : Revision::STATUS_PEER;

                $comment = String::filterTitle($this->editComment, 200);
                if (empty($comment)) {
                    $len = mb_strlen($revision->text, 'UTF8');
                    $oldlen = mb_strlen($this->lastrevision->text, 'UTF8');
                    $d = $len - $oldlen;
                    $comment = (($d > 0) ? "增加了{$d}个字符" : "减少了" . (-$d) . "个字符");
                }
                $revision->comment = $comment;
                $revision->save();            
                $this->revisionid = $revision->id;
                $this->update(false, ['revisionid']);
            }
            $this->currentRevision = $revision;
            return true;
        }
        return false;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->_oldTags = explode(' ', $this->tags);
    }

    public function filterTags($data)
    {
        $tagarr = Formatter::filterTags($data);
        if (count($tagarr) > 5) {
            $tagarr = array_slice($tagarr, 0, 5);
        }
        $this->_newTags = $tagarr;
        return $tagarr;
    }

    public function getRelatedQuestions()
    {
        //1. 首先获取标签
        //2. 查询包含该标签的所有问题
        //3. 根据数量排序
        //4. 取10个结果
        $questions = array();
        $ids = array();

        $cacheId = "question_relate_" . $this->id;
        $ids = Yii::$app->cache->get($cacheId);
        if ($ids === false) {
            $models = QuestionTag::find()->where(['tag' => $this->_oldTags])->all();

            $data = array();
            if (count($models) > 0) {
                foreach ($models as $model) {
                    if (in_array($model->tag, $this->_oldTags) && $model->postid != $this->id) {
                        if (isset($data[$model->postid])) {
                            $data[$model->postid] ++;
                        } else {
                            $data[$model->postid] = 1;
                        }
                    }
                }
                if (count($data) <= 0)
                    return array();
                arsort($data, SORT_NUMERIC);
                $data = array_slice($data, 0, 10, true);
                foreach ($data as $k => $v) {
                    $ids[] = $k;
                }
            }
            Yii::$app->cache->set($cacheId, $ids, 3600 * 24);
        }

        if (is_array($ids) && count($ids) > 0) {
//			$criteria=new CDbCriteria;
//			$criteria->addInCondition('id',$ids,'OR');
//			$questions = Post::model()->findAll($criteria);

            $questions = Post::find()->where(['id' => $ids])->all();
        }
        return $questions;
    }

    public function isQuestion()
    {
        return ($this->idtype == self::IDTYPE_Q);
    }

    public function isAnswer()
    {
        return ($this->idtype == self::IDTYPE_A);
    }

    public function isTag()
    {
        return ($this->idtype == self::IDTYPE_T);
    }

    /**
     * 帖子是否是我自己的
     * @return bool 是：返回true
     */
    public function isSelf()
    {
        return ($this->uid == Yii::$app->user->id);
    }

    /**
     * 是否是提问者
     */
    public function isAsker()
    {
        $authorId = ($this->isQuestion()) ? $this->uid : $this->question->uid;
        return ($authorId == Yii::$app->user->id);
    }

    /**
     * @todo 该函数以前未实现，需测试
     * @return type
     */
    public function hasOpenBounty()
    {
        $this->loadBounty();
        return $this->openBounty !== null;
    }

    public function loadBounty()
    {
        if (!empty($this->bounties)) {
            foreach ($this->bounties as $bounty) {
                if ($bounty->isOpen()) {
                    $this->openBounty = $bounty;
                } else {
                    $this->closeBounty[$bounty->answerid][] = $bounty;
                }
            }
        }
    }

    public function isWiki()
    {
        return ($this->wiki == self::WIKI_MODE);
    }

    /**
     * 检查问题是否可以转化为wiki模式
     * @return bool true允许转化为wiki，
     */
    public function checkToWiki()
    {
        $result = Yii::$app->db->createCommand()
                ->select("count(distinct uid) as total")
                ->from("{{revision}}")
                ->where("postid=:id AND status=1")
                ->bindValue(':id', $this->id)
                ->queryRow();

        return ($result['total'] >= Yii::$app->params['posts']['unwikiToWikiCount']);
    }

    /**
     * 替换标签中的特殊字符(#,+,.)
     * @param $tags
     */
    public static function replaceTags($tags)
    {
        $search = array('#', '+', '.');
        $replace = array('ñ', 'ç', 'û');
        return str_replace($search, $replace, $tags);
    }

    public function beforeDelete()
    {
        parent::beforeDelete();
        //删除投票
        Vote::deleteAll("postid=:id", [':id' => $this->id]);
        //删除comment
        $comments = Comment::findAll("idv=:idv", [":idv" => $this->id]);
        foreach ($comments as $comment) {
            $comment->delete();
        }

        //删除Activity
        Activity::deleteAll("type=:type AND typeid=:typeid", [':type' => 'ask', ":typeid" => $this->id]);

        //删除post state
        PostState::deleteAll(['id' => $this->id]);

        //删除repute
        Repute::deleteAll('postid=:postid', [':postid' => $this->id]);

        if ($this->isQuestion()) {
            //删除tags
            $questionTags = QuestionTag::findAll('postid =:postid', [':postid' => $this->id]);
            foreach ($questionTags as $questionTag) {
                $questionTag->delete();
            }

            //删除回答
            $answers = Post::findAll("idv=:idv", [":idv" => $this->id]);
            foreach ($answers as $answer) {
                $answer->delete();
            }

            //删除悬赏
            Bounty::deleteAll("questionid=:questionid", [":questionid" => $this->id]);
        }

        //删除Revision
        Revision::deleteAll("postid=:postid", [":postid" => $this->id]);
        
        //@todo 删除CommentVote
       
        return true;
    }

}
