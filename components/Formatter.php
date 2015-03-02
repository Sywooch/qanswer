<?php

namespace app\components;

use Yii;

class Formatter
{
    public static function ago($time, $since = null)
    {
        $timeoffset = Yii::$app->params['timeoffset'];
        $patterns = array(
            'seconds' => yii::t('formatter', 'less than a minute'),
            'minute' => yii::t('formatter', 'about a minute'),
            'minutes' => yii::t('formatter', '%d minutes'),
            'hour' => yii::t('formatter', 'about an hour'),
            'hours' => yii::t('formatter', 'about %d hours'),
            'day' => yii::t('formatter', 'a day'),
            'days' => yii::t('formatter', '%d days'),
            'month' => yii::t('formatter', 'about a month'),
            'months' => yii::t('formatter', '%d months'),
            'year' => yii::t('formatter', 'about a year'),
            'years' => yii::t('formatter', '%d years'),
        );
        if ($since === null)
            $since = time();

        if (!is_int($since) && !ctype_digit($time))
            $since = strtotime($since);

        if (!is_int($time) && !ctype_digit($time))
            $time = strtotime($time);

        $seconds = abs($since - $time);
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        $days = $hours / 24;
        $weeks = $days / 7;
        $months = $days / 30;
        $years = $days / 365;

        if ($seconds < 45)
            $words = $patterns['seconds'];
        else if ($seconds < 90)
            $words = $patterns['minute'];
        else if ($minutes < 45)
            $words = sprintf($patterns['minutes'], $minutes);
        else if ($minutes < 90)
            $words = $patterns['hour'];
        else if ($hours < 24)
            $words = sprintf($patterns['hours'], $hours);
        else if ($hours < 48)
            $words = $patterns['day'];
        else if ($days < 30)
            $words = sprintf($patterns['days'], $days);
        else if ($days < 60)
            $words = $patterns['month'];
        else if ($days < 365)
            $words = sprintf($patterns['months'], $months);
        else if ($years < 2)
            $words = $patterns['year'];
        else
            $words = sprintf($patterns['years'], $years);
        $suffix = $since - $time > 0 ? yii::t('formatter', ' ago') : yii::t('formatter', ' from now');
        if ($since - $time > 0)
            return $words . yii::t('formatter', ' ago');
        else
            return $words . yii::t('formatter', ' from now');
    }

    public static function expire($time, $since = null)
    {
        $patterns = array(
            'seconds' => yii::t('formatter', 'less than a minute'),
            'minute' => yii::t('formatter', 'about a minute'),
            'minutes' => yii::t('formatter', '%d minutes'),
            'hour' => yii::t('formatter', 'about an hour'),
            'hours' => yii::t('formatter', 'about %d hours'),
            'day' => yii::t('formatter', 'a day'),
            'days' => yii::t('formatter', '%d days'),
            'month' => yii::t('formatter', 'about a month'),
            'months' => yii::t('formatter', '%d months'),
            'year' => yii::t('formatter', 'about a year'),
            'years' => yii::t('formatter', '%d years'),
        );
        if ($since === null)
            $since = time();

        if (!is_int($since) && !ctype_digit($time))
            $since = strtotime($since);

        if (!is_int($time) && !ctype_digit($time))
            $time = strtotime($time);

        $seconds = abs($since - $time);
        $minutes = $seconds / 60;
        $hours = $minutes / 60;
        $days = $hours / 24;
        $weeks = $days / 7;
        $months = $days / 30;
        $years = $days / 365;

        if ($seconds < 45)
            $words = $patterns['seconds'];
        else if ($seconds < 90)
            $words = $patterns['minute'];
        else if ($minutes < 45)
            $words = sprintf($patterns['minutes'], $minutes);
        else if ($minutes < 90)
            $words = $patterns['hour'];
        else if ($hours < 24)
            $words = sprintf($patterns['hours'], $hours);
        else if ($hours < 48)
            $words = $patterns['day'];
        else if ($days < 30)
            $words = sprintf($patterns['days'], $days);
        else if ($days < 60)
            $words = $patterns['month'];
        else if ($days < 365)
            $words = sprintf($patterns['months'], $months);
        else if ($years < 2)
            $words = $patterns['year'];
        else
            $words = sprintf($patterns['years'], $years);
        $suffix = $since - $time > 0 ? yii::t('formatter', ' ago') : "";
        return $words . $suffix;
    }

    public static function time($time)
    {
//		$timeoffset = Yii::app()->params['timeoffset'];
        $timeoffset = 8;
        $dateformat = 'Y-m-d H:i:s';
        $time += $timeoffset * 3600;
        $result = gmdate($dateformat, $time);
        return $result;
    }

    public static function month($time)
    {
        $seconds = abs(time() - $time);
        $minutes = intval($seconds / 60);
        $hours = $minutes / 60;

        if ($seconds < 60) {
            $result = $seconds . "秒前";
        } elseif ($minutes < 60) {
            $result = $minutes . "分钟前";
        } else {
            $time += Yii::$app->params['timeoffset'] * 3600;
            $dateformat = 'n月j日';
            $m = 'n月';
            $mm = gmdate($m, $time);
            $d = 'j日';
            $dd = gmdate($d, $time);
            $result = $mm . "<br/>" . $dd;
            $result = gmdate($dateformat, $time);
        }
        return $result;
    }

    public static function age($birth)
    {
        if (empty($birth)) {
            return '';
        }
        list($by, $bm, $bd) = explode('-', $birth);
        $cm = date('n');
        $cd = date('j');
        $age = date('Y') - $by - 1;
        if ($cm > $bm || $cm == $bm && $cd > $bd)
            $age++;
        return $age;
    }

    public static function view($viewcount)
    {
        if ($viewcount >= 1000) {
            $k = round($viewcount / 1000);
            return $k . "K";
        }
        return $viewcount;
    }

    /**
     * 过滤tag函数
     * @param string $data
     * @return array
     */
    public static function filterTags($data)
    {
        $data = htmlspecialchars(trim(strtolower($data)));
        $tagarr = array();
        $tagnames = empty($data) ? array() : array_unique(explode(' ', $data));
        if (empty($tagnames))
            return $tagarr;
        foreach ($tagnames as $v) {
            if (!preg_match('/^\.?([\x{4e00}-\x{9fa5}]|\w){1}([\x{4e00}-\x{9fa5}]|\w|[-+#]){0,19}$/u', $v))
                continue;
//			if(!preg_match('/([\x7f-\xff_-]|\w|[+#]){1,20}$/', $v)) continue;
            $tagarr[] = $v;
        }
        return $tagarr;
    }
}
