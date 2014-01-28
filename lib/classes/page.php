<?php
class Page {
	function header($title,$menu = 1,$jquery = '') {
		global $lang;
		if($_GET['test'] == 'true') {
			$jquery = $jquery;
		} else {
			$jquery = '';
		}
		$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>'.$title.(strlen($title) > 0 ? ' @ ' : '').$lang['title'].'</title>
	<meta name="description" content="'.(strlen($title) > 0 ? $lang['page']['meta1'].' '.$title.'! ' : '').$lang['page']['meta2'].'" />
	<link rel="stylesheet" href="/css/reset.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" href="/css/text.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" href="/css/custom.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" href="/css/xGrid.css" type="text/css" media="screen" charset="utf-8" />	
	<link rel="stylesheet" href="/css/uniform.default.css" type="text/css" media="screen" charset="utf-8" />	
	<link rel="stylesheet" href="/css/jquery.lightbox-0.5.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" href="/css/jquery.visualize.css" type="text/css" media="screen" charset="utf-8" />
	<link rel="stylesheet" href="/css/green.css" type="text/css" media="screen"  charset="utf-8" />
	<!--[if IE 8]>	
	<link rel="stylesheet" href="/css/ie8.css" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->
	<!--[if IE 7]>	
	<link rel="stylesheet" href="/css/ie7.css" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->
	<script type="text/javascript" src="/js/jquery/jquery.1.4.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.hoverIntent.min.js"></script>
	<script type="text/javascript" src="/js/murano/murano.js"></script>
	<script type="text/javascript" src="/js/murano/murano.nav.js"></script>
	<script type="text/javascript" src="/js/murano/murano.portlet.js"></script>
	<script type="text/javascript" src="/js/murano/murano.message.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.uniform.min.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.lightbox-0.5.min.js"></script>
	<!--[if IE]><script  type="text/javascript" src="/js/misc/excanvas.min.js"></script><![endif]-->
	<script type="text/javascript" src="/js/jquery/jquery.visualize.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
	<script type="text/javascript" charset="utf-8">
	var myTextExtraction = function(node)  
	{  
	    return node.childNodes[0].innerHTML; 
	} 
	$(function(){
		megadrop.init ();
		murano.portlet.init ();
		murano.init ();
		murano.message.init ();	
	});
';
	if(strlen($jquery) > 0) {
		$header .= '	$(document).ready(function(){
		$.post(\'/lib/requests/mysql.php\', '.$jquery.', function(data) {
			alert(\'Data loaded: \'+data);
		});
	});
';
	}
	$header .= '	</script>
	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push([\'_setAccount\', \'UA-21839602-3\']);
	  _gaq.push([\'_trackPageview\']);
	
	  (function() {
	    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
	    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
	    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
</head>

<body class="layout-fixed">
<div id="topMenu" class="clearfix">
	<div class="pad">
		<div class="left">
			<a href="/">&laquo; '.$lang['title'].'</a>
		</div>
		<div class="right">
			<a href="/en'.str_replace(array('/en','/da'),'',$_SERVER['REQUEST_URI']).'"><img src="/gfx/flags/flag_34.gif" alt="English" title="English" /></a>
			<a href="/da'.str_replace(array('/en','/da'),'',$_SERVER['REQUEST_URI']).'"><img src="/gfx/flags/flag_1.gif" alt="Dansk" title="Dansk" /></a>
		</div>
	</div>
</div>
<div id="page">
	<div id="header">
		<div class="pad">
			<h1 id="title"><a href="/">'.$lang['title'].'</a></h1>
			<div id="search">
				<form method="get" action="/" onsubmit="goto(\'id\');return false;">
					<input type="text" name="id" id="id" value="'.$lang['page']['link'].'" />
					<input type="submit" class="submit" name="submit" value="" />
				</form>
			</div>
		</div>
	</div>
	<div id="megadropdown">
		<ul>
			<li'.($menu == 1 ? ' class="current"' : '').'>
				<a href="/">'.$lang['page']['home'].'</a>
			</li>
			<li'.($menu == 2 ? ' class="current"' : '').'>
				<a href="/notes">'.$lang['page']['notes'].'</a>
			</li>
			<li'.($menu == 3 ? ' class="current"' : '').'>
				<a href="/settings">'.$lang['page']['settings'].'</a>
			</li>
		</ul>		
	</div>
	<div id="body" class="clearfix">
		<div id="main">
';
		return $header;
	}
	function content() {
		global $lang;
		$content = '			<div class="portlet x8">	
				<div class="portlet-header">
					<h4>'.$lang['main']['title'].'</h4>
				</div>
				<div class="portlet-content">
					<p>'.$lang['main']['text'].'</p>
				</div>	
			</div>
			<div class="portlet x4">	
				<div class="portlet-header">
					<h4>'.$lang['main']['latest_vists'].'</h4>
				</div>
				<div class="portlet-content">';
		$get_latest = mysql_query("SELECT v_url FROM v_views WHERE v_ip = '".prepare($_SERVER['REMOTE_ADDR'])."' && (v_url LIKE '%players%' OR v_url LIKE '%clubs%') && v_url NOT LIKE 'new.%' && v_url NOT LIKE 'dev.%' && v_url NOT LIKE '%future%' ORDER BY v_timestamp DESC LIMIT 0,5;");
		while($fetch_latest = mysql_fetch_assoc($get_latest)) {
			if(preg_match('/history/',$fetch_latest['v_url'])) {
				$fetch_latest['v_url'] = str_replace('/history','',$fetch_latest['v_url']);
				$suffix = '/history';
				$print_suffix = $lang['main']['shistory'];
			} else {
				$suffix = '';
				$print_suffix = '';
			}
			$v_url = str_replace(array('www.vman.info/players/','www.vman.info/en/players/','www.vman.info/da/players/','www.vman.info/clubs/','www.vman.info/en/clubs/','www.vman.info/da/clubs/'),'',$fetch_latest['v_url']);
			$id = preg_replace('/[^0-9]*/','',(preg_match('/clubs/',$fetch_latest['v_url']) ? substr($v_url,0,6) : $v_url));
			if(preg_match('/\//',$id)) {
				$split = split('/',$id);
				$id = $split[0];
			}
			if($id > 0) {
				if(preg_match('/players/',$fetch_latest['v_url'])) {
					$fetch_info = mysql_fetch_array(mysql_query("SELECT p_name AS name FROM v_players WHERE p_id = ".$id." ORDER BY timestamp DESC LIMIT 1;"));
					$prefix = $lang['main']['player'];
				} else {
					$fetch_info = mysql_fetch_array(mysql_query("SELECT c_name AS name FROM v_clubs WHERE c_id = ".$id." ORDER BY timestamp DESC LIMIT 1;"));
					$prefix = $lang['main']['club'];
				}
				$content .= '
					<p>'.$prefix.': <a href="/'.(preg_match('/players/',$fetch_latest['v_url']) ? 'players' : 'clubs').'/'.$id.$suffix.'">'.$fetch_info['name'].$print_suffix.'</a></p>';
			}
		}
		$stats = mysql_query("SELECT v_option,v_value FROM v_stats;");
		$content .= '
				</div>	
			</div>
			<div class="portlet x4">	
				<div class="portlet-header">
					<h4>'.$lang['main']['statistics'].'</h4>
				</div>
				<div class="portlet-content">
					<p><i>'.$lang['main']['since'].'</i></p>';
		while($fetch_stats = mysql_fetch_assoc($stats)) {
			$split = split(' ',$fetch_stats['v_option']);
			$options = '';
			foreach($split AS $split) {
				$options[] = $lang['main'][$split];
			}
			$content .= '
						<p><b>'.format($fetch_stats['v_value']).'</b> '.implode(' ',$options).'</p>';
		}
		$content .= '
				</div>	
			</div>';
		return $content;
	}
	function error($cid = '',$pid = '') {
		global $lang;
		$error = '			<div class="portlet x12">	
				<div class="portlet-header">
					<h4>'.$lang['error']['title'].'</h4>
				</div>
				<div class="portlet-content">
					<p>'.$lang['error']['text'].'</p>
				</div>	
			</div>';
		return $error;
	}
	function search($query = '') {
		global $lang;
		$error = '			<div class="portlet x12">	
				<div class="portlet-header">
					<h4>'.$lang['search']['title'].' "'.$query.'"</h4>
				</div>
				<div class="portlet-content">
					<p>'.$lang['search']['text'].'</p>
				</div>	
			</div>';
		return $error;
	}
	function footer() {
		global $lang;
		global $version;
		$footer = '
		</div>
	</div>
	<div id="footer">
		<p>Version '.$version.' | '.$lang['page']['foot'].'</p>
	</div>
</div>
</body>
</html>';
		return $footer;
	}
}
?>