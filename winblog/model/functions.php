<?php
/**
 * UTF8截取字符串
 * 
 * @param str
 * @param len
 */
function cutwin($str, $len = 140, $prefix = '...')
{
	$i=0;$n=0;
	while($i<strlen($str)){if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str[$i])){$i+=3;}else{$i++;}$n++;}
	if($n<=$len) return $str;
	$re = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	preg_match_all($re, $str, $match);
	return join("", array_slice($match[0], 0, $len)).$prefix;
}