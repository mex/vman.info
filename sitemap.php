<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');

$view = ($_GET['view'] > 0 ? mysql_real_escape_string($_GET['view']) : 0);
$offset = 50000;

function remove_accent($str) {
	$a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
	$b = array('A','A','A','A','A','AA','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','Oe','U','U','U','U','Y','s','a','a','a','a','a','aa','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','oe','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
	
	return str_replace($a, $b, $str);
}

function format_link($str) {
	return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($str)));
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

if($_GET['type'] == 'clubs') {
$get_clubs = mysql_query("SELECT c_id, c_name FROM v_clubs GROUP BY c_id ORDER BY c_id ASC LIMIT {$view}, {$offset};")or die(mysql_error());
	while($fetch_clubs = mysql_fetch_assoc($get_clubs)) {
		echo '
   <url>
      <loc>http://www.vman.info/clubs/'.$fetch_clubs['c_id'].'-'.format_link($fetch_clubs['c_name']).'.html</loc>
   </url>';
	}
}

if($_GET['type'] == 'players') {
$get_players = mysql_query("SELECT p_id, p_name FROM v_players GROUP BY p_id ORDER BY p_id ASC LIMIT {$view}, {$offset};")or die(mysql_error());
	while($fetch_players = mysql_fetch_assoc($get_players)) {
		echo '
   <url>
      <loc>http://www.vman.info/players/'.$fetch_players['p_id'].'-'.format_link($fetch_players['p_name']).'.html</loc>
   </url>';
	}
}

echo '
</urlset>';
?>