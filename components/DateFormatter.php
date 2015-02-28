<?php
namespace app\components;
class DateFormatter
{
	public function ago($time, $since=null)
	{
		$timeoffset = Yii::app()->params['timeoffset'];
		echo $timeoffset;die;
		$patterns = array(
			'seconds'   	=> yii::t('yii','less than a minute'),
			'minute'        => yii::t('yii','about a minute'),
			'minutes'   	=> yii::t('yii','%d minutes'),
			'hour'          => yii::t('yii','about an hour'),
			'hours'         => yii::t('yii','about %d hours'),
			'day'           => yii::t('yii','a day'),
			'days'          => yii::t('yii','%d days'),
			'month'         => yii::t('yii','about a month'),
			'months'        => yii::t('yii','%d months'),
			'year'          => yii::t('yii','about a year'),
			'years'         => yii::t('yii','%d years'),
		);
		if($since===null)
			$since=time();

		if(!is_int($since) && !ctype_digit($time))
			$since = strtotime($since);

		if(!is_int($time) && !ctype_digit($time))
			$time = strtotime($time);

		$seconds = abs($since - $time);
		$minutes = $seconds/60;
		$hours = $minutes/60;
		$days = $hours/24;
		$weeks = $days/7;
		$months = $days/30;
		$years = $days/365;

		if($seconds < 45)
			$words = $patterns['seconds'];
		else if($seconds < 90)
			$words = $patterns['minute'];
		else if($minutes < 45)
			$words = sprintf($patterns['minutes'], $minutes);
		else if($minutes < 90)
			$words = $patterns['hour'];
		else if($hours < 24)
			$words = sprintf($patterns['hours'], $hours);
		else if($hours < 48)
			$words = $patterns['day'];
		else if($days < 30)
			$words = sprintf($patterns['days'], $days);
		else if($days < 60)
			$words = $patterns['month'];
		else if($days < 365)
			$words = sprintf($patterns['months'], $months);
		else if($years < 2)
			$words = $patterns['year'];
		else
			$words = sprintf($patterns['years'], $years);
			$suffix = $since - $time > 0 ? 'ago' : 'from now';
		if($since - $time > 0)
			return $words.' ago';
		else
			return $words.' from now';
	}

	public function time($time)
	{
		$dateformat = 'Y-m-d H:i:s';
		$result = gmdate($dateformat, $time);
		return $result;
	}

	public function age($birth)
	{

		list($by,$bm,$bd)=explode('-',$birth);
		$cm=date('n');
		$cd=date('j');
		$age=date('Y')-$by-1;
		if ($cm>$bm || $cm==$bm && $cd>$bd) $age++;
		return $age;
	}

	public function view($viewcount)
	{
		if ($viewcount>=1000)
		{
			$k = round($viewcount/1000);
			return $k."K";
		}
		return $viewcount;
	}

	public static function weekFirstDay($time=null) {
		$time = ($time==null) ? time() : $time;
		return date('Y-m-d', $time-86400*(date('N',$time)-1));
	}

	public static function monthFirstDay($time=NULL) {
		return date('Y-m-d', mktime(0,0,0,date('n'),1,date('Y')));
	}

	public static function quarterFirstDay() {
		return date('Y-m-d', mktime(0,0,0,date('n')-(date('n')-1)%3,1,date('Y')));
	}

	public static function yearFirstDay() {
		return date('Y').'-01-01';
	}
}