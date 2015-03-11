<?php

namespace app\modules\question\models;

use yii\helpers\Url;
use Yii;
use app\components\String;
use app\models\Post;
use app\models\UserStat;
use app\models\Bounty;


class Question extends Post
{

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

    public function init()
    {
        parent::init();
        $this->idtype = self::IDTYPE_Q;
    }
    public function scenarios()
    {
        return [
            'qask' => ['title', 'content', 'tags'],
            'answer' => ['content'],
        ];
    }

    public function rules()
    {
        return array(
            ['title,content,tags', 'required', 'on' => 'qask'],
//			array('title','length','min'=>3,'max'=>100,'on'=>'qask'),
//			array('tags', 'match', 'pattern'=>'/^([\x{4e00}-\x{9fa5}]|\w|\s|[\.\+-_#]){1,60}$/u', 'message'=>'标签只能包括中文字符、字母、特殊字符(.+-_#)','on'=>'qask'),
//			array('tags', 'valtags','on'=>'qask'),
//			array('content','required','on'=>'answer,tag'),
            array('content', 'required', 'on' => 'answer'),
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

//    public function relations()
//    {
//        return array(
//            'author' => array(self::BELONGS_TO, 'User', 'uid', 'condition' => 'author.status=' . User::STATUS_ACTIVE),
//            'vote' => array(self::HAS_MANY, 'Vote', 'postid'),
//            'poststate' => array(self::HAS_ONE, 'PostState', 'id'),
//            'lastrevision' => array(self::BELONGS_TO, 'Revision', 'revisionid'),
//            'comments' => array(self::HAS_MANY, 'Comment', 'idv', 'limit' => 3, 'condition' => 'comments.status=' . Comment::STATUS_OK),
//            'question' => array(self::BELONGS_TO, 'post', 'idv', 'condition' => 'question.idtype="' . self::IDTYPE_Q . '"'),
//            'tag' => array(self::HAS_ONE, 'Tag', 'postid', 'condition' => 'idtype="tag"'),
//            'closeuids' => array(self::HAS_MANY, 'PostMod', 'postid', 'condition' => 'type=6'),
//            'revCount' => array(self::STAT, 'Revision', 'postid', 'condition' => 'status=' . Revision::STATUS_OK),
//            'bounties' => array(self::HAS_MANY, 'Bounty', 'questionid'),
//            'bountying' => array(self::HAS_ONE, 'Bounty', 'questionid', 'condition' => 'status=' . Bounty::STATUS_OPEN . ' AND endtime>' . time()),
//            'AScores' => array(self::STAT, 'Post', 'idv', 'condition' => 'idtype="' . self::IDTYPE_A . '"', 'select' => "sum(score)"),
//        );
//    }


    public function getLastrevision()
    {
        return $this->hasOne(Revision::className(), ['id' => 'revisionid']);
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
     * @param Answer $answer
     * @return boolean
     */
    public function addAnswer($answer)
    {
        $answer->idv = $this->id;
        $answer->idtype = self::IDTYPE_A;

        $orginContent = $answer->content;
        $answer->content = String::markdownToHtml($orginContent);
        $answer->excerpt = String::filterTitle($orginContent, 200);
        $answer->wiki = !$answer->isWiki() ? ($this->isWiki() ? self::WIKI_MODE : self::UNWIKI_MODE) : $answer->wiki;
        
        if ( $answer->save()) {
            $poststate = new PostState();
            $poststate->id = $answer->id;
            $poststate->save();



            $this->answercount++;
            $this->activity = time();
            $this->setScenario('qask');
            $this->update();
            //更新个人统计数量
            UserStat::updateAllCounters(['acount' => 1], 'id=:uid', array(':uid' => $answer->uid));
        }
        return $success;
    }

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

    /**
     * @
     */
    public function save($runValidation = true, $attributes = null)
    {
        parent::save($runValidation, $attributes);
    }


    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if (!is_null($this->tags)) {
                $questionTag = new QuestionTag;
                $questionTag->addTags($this->_newTags, $this->id);

                $tag = new Tag;
                $tag->addTags($this->_newTags, $this->uid);
            }
        } else {
            $questionTag = new QuestionTag;
            $questionTag->updateTags($this->_oldTags, $this->_newTags, $this->id);

            $tag = new Tag;
            $tag->updateTags($this->_oldTags, $this->_newTags, $this->uid);
        }

        parent::afterSave($insert, $changedAttributes);
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
            $questions = Post::find()->where(['id' => $ids])->all();
        }
        return $questions;
    }
    
    public function getAnswers($tab, $offset, $limit) 
    {
        $query = Answer::find()->where('post.idv=:idv AND post.idtype=:idtype', [':idv' => $this->id, ':idtype' => Answer::IDTYPE_A]);
        switch ($tab) {
            case 'votes':
                $query->orderBy(['post.accepted' => SORT_DESC, 'post.score' => SORT_DESC]);
                break;
            case 'oldest':
                $query->orderBy(['post.accepted' => SORT_DESC, 'post.createtime' => SORT_DESC]);
                break;
            case 'activity':
            default:
                $query->orderBy(['post.accepted' => SORT_DESC, 'post.activity' => SORT_DESC]);
                break;
        }

        if (!Yii::$app->user->isGuest) {
            $query->select(["post.*", "vote.useful as hasVote", "vote.fav as hasFav"])
                  ->leftJoin('vote', 'vote.postid=post.id AND vote.uid=:uid', [':uid' => Yii::$app->user->getId()]);
        }
        $answers = $query->with('author')->offset($offset)->limit($limit)->all();
        return $answers;
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
    
    public function getFormattedTitle()
    {
        $title = $this->title;
        if ($this->poststate->isClose()) {
            $title .= "&nbsp;[关闭]";
        }
        if ($this->poststate->isDelete()) {
            $title .= "&nbsp;[删除]";
        }
        return $title;
    }

}
