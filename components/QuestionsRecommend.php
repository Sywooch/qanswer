<?php
namespace app\components;
use Yii;
use app\models\Post;
class QuestionsRecommend {


	public static function recentHotQuestions() {
		$pagesize = Yii::$app->params['pages']['hotQuestionsPagesize'];
		return self::_hotQuestions(365,$pagesize);
	}

	public static function weekHotQuestions() {
		$pagesize = Yii::$app->params['pages']['hotQuestionsPagesize'];
		return self::_hotQuestions(7,$pagesize);
	}

	public static function monthHotQuestions() {
		$pagesize = Yii::$app->params['pages']['hotQuestionsPagesize'];
		return self::_hotQuestions(30,$pagesize);
	}

	/**
	 * 热门问题算法规则
	 * @param int $Qviews 显示次数
	 * @param int $Qanswers 答案数量
	 * @param int $Qscore 投票分数
	 * @param int $Ascores 所有答案的分数
	 * @param int $date_ask 提问时间
	 * @param int $date_active 最后活动时间
	 */
	private static function hotRule($Qviews, $Qanswers, $Qscore, $Ascores, $date_ask, $date_active)
	{
	    $Qage = (time() - strtotime(gmdate("Y-m-d H:i:s",$date_ask))) / 3600;
	    $Qage = round($Qage, 1);
	    $Qupdated = (time() - strtotime(gmdate("Y-m-d H:i:s",$date_active))) / 3600;
	    $Qupdated = round($Qupdated, 1);

	    $dividend = (log10($Qviews)*4) + (($Qanswers * $Qscore)/5) + $Ascores;
	    $divisor = pow((($Qage + 1) - ($Qage - $Qupdated)/2), 1.5);
	    return $dividend/$divisor;
	}

	public static function _hotQuestions($days,$n) 
    {
		$time = time() - 86400*$days;
//		$ids = Yii::$app->db->createCommand()
//        	->select("*")
//        	->from("{{post}} p")
//        	->join("{{post_state}} ps","p.id=ps.id")
//        	->where("p.createtime>:time AND p.idtype=:idtype AND ps.delete=:delete",array(":time"=>$time,":idtype"=>Post::IDTYPE_Q,":delete"=>0))
//        	->queryAll();
        
        $ids = Post::find()->select('*')->leftJoin('post_state', 'post.id=post_state.id')
                    ->where('post.createtime>:time AND post.idtype=:idtype AND post_state.delete=:delete',[":time"=>$time,":idtype"=>Post::IDTYPE_Q,":delete"=>0])
                    ->all();
        $qs = array();
		foreach($ids as $q) {
			$qs[$q['id']] = self::hotRule($q['viewcount'],$q['answercount'],$q['score'],$q['aupvotes'],$q['createtime'],$q['activity']);
		}
		arsort($qs);
		return array_keys(array_slice($qs,0,$n,true));
	}

	private function hotRuleBackup($Qviews, $Qanswers, $Qscore, $Ascores, $date_ask, $date_active) {
	    $Qage = (time() - strtotime(gmdate("Y-m-d H:i:s",strtotime($date_ask)))) / 3600;
	    $Qage = round($Qage, 1);
	    $Qupdated = (time() - strtotime(gmdate("Y-m-d H:i:s",strtotime($date_active)))) / 3600;
	    $Qupdated = round($Qupdated, 1);

	    $dividend = (log10($Qviews)*4) + (($Qanswers * $Qscore)/5) + $Ascores;
	    $divisor = pow((($Qage + 1) - ($Qage - $Qupdated)/2), 1.5);
	    return $dividend/$divisor;
	}
}