<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/config.php');


function age($birthday,$altdate = 0) {
	$difference = ($altdate > 0 ? $altdate : time())-strtotime($birthday);
	return $difference/(86400*30);
}
function position($position,$special = 'short') {
	$from = array('K','DL','DC','DR','MD','ML','MC','MR','MO','FL','FC','FR');
	$to = array('DL' => 'FV','DC' => 'FC','DR' => 'FH','ML' => 'MV','MR' => 'MH','FL' => 'AV','FC' => 'AC','FR' => 'AH');
	return strtr($position,$to);
}

$data = '';

$get = mysql_query("SELECT p_id, p_name, c_id, p_birthday, UNIX_TIMESTAMP(timestamp) AS timestamp, p_description, p_position, p_value, p_finishing, p_dribling, p_passing, p_tackling, p_marking, p_penalty_taking, p_bravery, p_creativity, p_determination, p_influence, p_morale, p_off_the_ball, p_acceleration, p_balance, p_fitness, p_jump, p_strength, p_stamina FROM v_players ORDER BY id DESC LIMIT 0,100000");
while($fetch = mysql_fetch_assoc($get)) {
	$data .= $fetch['p_id'].",".$fetch['p_name'].",".$fetch['c_id'].",".round(age($fetch['p_birthday'],$fetch['timestamp']),2).",".$fetch['p_description'].",".$fetch['p_position'].",".$fetch['p_value'].",".$fetch['p_finishing'].",".$fetch['p_dribling'].",".$fetch['p_passing'].",".$fetch['p_tackling'].",".$fetch['p_marking'].",".$fetch['p_penalty_taking'].",".$fetch['p_bravery'].",".$fetch['p_creativity'].",".$fetch['p_determination'].",".$fetch['p_influence'].",".$fetch['p_morale'].",".$fetch['p_off_the_ball'].",".$fetch['p_acceleration'].",".$fetch['p_balance'].",".$fetch['p_fitness'].",".$fetch['p_jump'].",".$fetch['p_strength'].",".$fetch['p_stamina'].";\n";
}

$handle = fopen('data.txt', 'w+');
fwrite($handle, $data);
?>