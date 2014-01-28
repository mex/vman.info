<?php
header('Content-Type:text/html;charset=utf-8');

define(DB_HOST, 'localhost');
define(DB_USER, 'root');
define(DB_PASS, '');
define(DB_NAME, 'vmaninfo');

$db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$db);
mysql_query('SET NAMES utf8;')or die('We are experiencing problems with the database. We are working on the issue.');

$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];

$get_new = mysql_query("SELECT MIN(v.id) AS id,x.p_id,x.p_finishing,x.p_dribling,x.p_passing,x.p_tackling,x.p_marking,x.p_penalty_taking,x.p_bravery,x.p_creativity,x.p_determination,x.p_influence,x.p_morale,x.p_off_the_ball,x.p_acceleration,x.p_balance,x.p_fitness,x.p_jump,x.p_strength,x.p_stamina,UNIX_TIMESTAMP(x.timestamp) AS date FROM v_players AS v LEFT JOIN v_players AS x ON v.id = x.id GROUP BY x.p_id;");
while($fetch_new = mysql_fetch_assoc($get_new)) {
	$stats = $fetch_new['p_finishing']+$fetch_new['p_dribling']+$fetch_new['p_passing']+$fetch_new['p_tackling']+$fetch_new['p_marking']+$fetch_new['p_penalty_taking']+$fetch_new['p_bravery']+$fetch_new['p_creativity']+$fetch_new['p_determination']+$fetch_new['p_influence']+$fetch_new['p_morale']+$fetch_new['p_off_the_ball']+$fetch_new['p_acceleration']+$fetch_new['p_balance']+$fetch_new['p_fitness']+$fetch_new['p_jump']+$fetch_new['p_strength']+$fetch_new['p_stamina'];
	mysql_query("INSERT INTO v_players_first (p_id,p_stats,timestamp) VALUES (".$fetch_new['p_id'].",".$stats.",".$fetch_new['date'].") ON DUPLICATE KEY UPDATE p_stats=".$stats.",timestamp=".$fetch_new['date'].";");
}

mysql_query("INSERT INTO v_cron (c_file) VALUES ('".$_SERVER['REQUEST_URI']."');");

$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime;
$totaltime = round($totaltime,5);
echo 'Page loaded in '.$totaltime.' seconds';
?>