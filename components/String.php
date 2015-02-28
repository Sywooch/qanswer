<?php
namespace app\components;

class String {

	/**
	 * addslashes过滤，可以过滤数组
	 * @param mixid $string
	 */
	function addslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				unset($string[$key]);
				$string[addslashes($key)] = string::addslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
		return $string;
	}

	/**
	 * 去掉slassh
	 * @param mixed $string
	 */
	function stripslashes($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = string::stripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}

	/**
	 * 校验Email
	 * @param $email
	 */
	function isemail($email) {
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	/**
	 * 校验手机号码
	 * @param $phone
	 * @return bool
	 */
	function isphone($phone) {
		return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone);
	}

	function istelephone($photo) {
		return preg_match('/^((\\(?\\d{3,4}\\)?)|(\\d{3,4}-)?)\\d{7,8}$/',$photo);
	}

	/**
	 * 数字
	 * @param $p
	 */
	function isnum($p) {
		if (preg_match("/^[1-9]\d*$/",$p)) {
			return true;
		}
		return false;
	}

	/**
	 * 名称 格式：第一个字符必须为字母，其余可为字母和数字
	 * @param $p
	 */
	function isname($p) {
		if (preg_match('/^[a-zA-z]{1}[\w\-\.]*$/',$p)) {
			return true;
		}
		return false;
	}

	/**
	 * 过滤字符串 基本实现函数
	 * @param string $string
	 * @param int $length	长度
	 * @param array $options
	 * 			-- in_slashes 	0  	没有slashes（不需要调用stripslashes）
	 * 			-- out_slashes 	0 	不要调用addslashes()
	 * 			-- html			<0 	不允许html	=0 允许html
	 * @return string
	 */
	public static function filterString($string, $length,$options = array()) {
		$defaults = array(
			'in_slashes'	=> 0,
			'out_slashes'	=> 0,
			'html'			=> 1	//关系很大，再验证功能 @todo
		);

		$options = array_merge($defaults,$options);
		$string = trim($string);
		if($options['in_slashes']) {
			$string = self::stripslashes($string);
		}

		if($options['html'] < 0) {
			$string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
		} elseif ($options['html'] == 0) {
			$string = htmlspecialchars($string);
		}

		if($length) {
			$string = self::cutstring($string, $length);
		}

		if($options['out_slashes']) {
			$string = self::addslashes($string);
		}
		return trim($string);
	}

	/**
	 * 截取字符串
	 * @param $string
	 * @param $length
	 * @param $append
	 * @return string
	 */
	public static function cutString($string, $length, $append = ' ...',$charset='utf-8') {
		if(strlen($string) <= $length) {
			return $string;
		}

		$pre = chr(1);
		$end = chr(1);
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		if($charset == 'utf-8') {
			$n = $tn = $noc = 0;
			while($n < strlen($string)) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc += 2;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc += 2;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc += 2;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc += 2;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc += 2;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);

		} else {
			for($i = 0; $i < $length; $i++) {
				$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			}
		}

		$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		$pos = strrpos($strcut, chr(1));
		if($pos !== false) {
			$strcut = substr($strcut,0,$pos);
		}
		return $strcut.$append;
	}

	/**
	 * 过滤标题，禁用任何HTML
	 * @param $string
	 * @param $length
	 */
	public static function filterTitle($string, $length=80) {
		return self::filterString($string, $length, array('in_slashes'=>0, 'out_slashes'=>0,'html'=>0));
	}

	function filtermessage($string,$length=30000) {
		$string = self::checkhtml($string);
		$string = self::filterString($string,$length, array('in_slashes'=>1, 'out_slashes'=>0));
		$string = preg_replace(array(
			"/\<div\>\<\/div\>/i",
			"/\<a\s+href\=\"([^\>]+?)\"\>/i"
		), array(
			'',
			'<a href="\\1" target="_blank">'
		),$string);
		$string = addslashes($string);
		return $string;
	}

	function checkhtml($html) {

		preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

		$searchs[] = '<';
		$replaces[] = '&lt;';
		$searchs[] = '>';
		$replaces[] = '&gt;';

		if($ms[1]) {
			$allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|object|param|embed';
			$ms[1] = array_unique($ms[1]);
			foreach ($ms[1] as $value) {
				$searchs[] = "&lt;".$value."&gt;";

				$value = str_replace('&', '_uch_tmp_str_', $value);
				$value = self::htmlspecialchars($value);
				$value = str_replace('_uch_tmp_str_', '&', $value);

				$value = str_replace(array('\\','/*'), array('.','/.'), $value);
				$value = preg_replace(array("/(javascript|script|eval|behaviour|expression|style|class)/i", "/(\s+|&quot;|')on/i"), array('.', ' .'), $value);
				if(!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
					$value = '';
				}
				$replaces[] = empty($value)?'':"<".str_replace('&quot;', '"', $value).">";
			}
		}
		$html = str_replace($searchs, $replaces, $html);
		return $html;
	}

	function htmlspecialchars($string) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = self::htmlspecialchars($val);
			}
		} else {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
			str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
		}
		return $string;
	}

	/**
	 * markdown转换HTMl
	 * @param string $text
	 * @return string
	 */
	public static function markdownToHtml($text) 
    {
		$html = \yii\helpers\Markdown::process($text);
		return $html;
	}
}
?>