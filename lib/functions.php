<?php
function __autoload($class_name) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/lib/classes/'.strtolower($class_name).'.php');
}
function prepare($var) {
	return mysql_real_escape_string($var);
}
function clean_link($link) {
	if(preg_match('/clubs/i',$link)) {
		$split = split('clubs/',$link);
		$new_link = $split[1];
	} elseif(preg_match('/players/i',$link)) {
		$split = split('players/',$link);
		$new_link = $split[1];
	} else {
		$new_link = $link;
	}
	return $new_link;
}
function entities($string) {
	$from = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Ž','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ');
	$to = array('&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&#0142;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
	return str_replace($from,$to,$string);
}
function format($number,$decimals = 0) {
	if(is_numeric($number)) {
		return number_format($number,$decimals,',','.');
	} else {
		return $number;
	}
}
?>