<?php
require_once($_SERVER['DOCUMENT_ROOT'].'lib/config.php');

$future1 = ($fetch_user_info['s_future1'] > 0 ? $fetch_user_info['s_future1'] : 25);
$future2 = ($fetch_user_info['s_future2'] > 0 ? $fetch_user_info['s_future2'] : 30);

if(is_array($_POST)) {
	$feed = $_POST;
	if(is_array($feed)) {
		mysql_query("INSERT INTO v_clubs (c_id,c_name,c_vifa,c_supporters,c_training_facility,c_physio,c_stadium,c_capacity,c_ticket_price) VALUES (".$cid.",'".$feed['name']."',".$feed['vifa_points'].",".$feed['supporters'].",".$feed['training_facility_id'].",".$feed['physio'].",'".$feed['stadium_name']."',".$feed['stadium_capacity'].",".$feed['ticket_price'].");");
		foreach($feed['players'] AS $key => $dbp) {
			mysql_query("INSERT INTO v_players (c_id,p_id,p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name) VALUES (".$cid.",".$dbp['id'].",".$dbp['age'].",'".$dbp['leg']."',".$dbp['country_id'].",".$dbp['height'].",".$dbp['weight'].",".$dbp['value'].",'".$dbp['birthday']."','".$dbp['position']."','".$dbp['description']."',".$dbp['contract_expiry'].",".$dbp['wage'].",".$dbp['energy'].",".$dbp['finishing'].",".$dbp['dribling'].",".$dbp['passing'].",".$dbp['tackling'].",".$dbp['marking'].",".$dbp['penalty_taking'].",".$dbp['bravery'].",".$dbp['creativity'].",".$dbp['determination'].",".$dbp['influence'].",".$dbp['morale'].",".$dbp['off_the_ball'].",".$dbp['acceleration'].",".$dbp['balance'].",".$dbp['fitness'].",".$dbp['jump'].",".$dbp['strength'].",".$dbp['stamina'].",'".$dbp['name']."');");
			$get_player_history = mysql_query("SELECT p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$dbp['id']." ORDER BY TIMESTAMP DESC;");
			$s = 0;
			while($fetch_player = mysql_fetch_assoc($get_player_history)) {
				if($s == 0) {
					$feed['players'][$key]['date2'] = $fetch_player['date'];
					$feed['players'][$key]['last'] = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
				}
				$feed['players'][$key]['date1'] = $fetch_player['date'];
				$feed['players'][$key]['first'] = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
				$s++;
			}
			$stats = $dbp['finishing']+$dbp['dribling']+$dbp['passing']+$dbp['tackling']+$dbp['marking']+$dbp['penalty_taking']+$dbp['bravery']+$dbp['creativity']+$dbp['determination']+$dbp['influence']+$dbp['morale']+$dbp['off_the_ball']+$dbp['acceleration']+$dbp['balance']+$dbp['fitness']+$dbp['jump']+$dbp['strength']+$dbp['stamina'];
			$ascension = ascension($dbp['first'],$dbp['last'],$dbp['date1'],$dbp['date2']);
			$value[$dbp['id']] = array(format($ascension,2),future($future1,$dbp['birthday'],$stats,$ascension),future($future2,$dbp['birthday'],$stats,$ascension),mark($dbp['birthday'],$stats,$ascension,true));
		}
	}
	$values = json_encode($value);
	echo $values;
}

function age($birthday,$exact = false,$altdate = 0) {
	$difference = ($altdate > 0 ? $altdate : time())-strtotime($birthday);
	if($exact == false) {
		$years = round($difference/(86400*30)-0.5);
		$days = str_replace('-0',0,round(($difference-$years*(86400*30))/86400-0.5));
		return array($years,$days);
	} else {
		return $difference/(86400*30);
	}
}
function ascension($first,$last,$date1,$date2) {
	$stats = $last-$first;
	$time = $date2-$date1;
	$days = ($time > 0 ? ($time/86400) : 0);
	$ascension = ($days > 0 ? ($days < 1 ? '?' : $stats/$days) : '?');
	return $ascension;
}
function future($age,$birthday,$stats,$ascension) {
	if(($age-age($birthday,true)) > 0) {
		if(is_numeric($ascension)) {
			$future = round(round(($age-age($birthday,true))*30)*$ascension)+$stats;
		} else {
			$future = '?';
		}
	} else {
		$future = '-';
	}
	return $future;
}
function mark($birthday,$stats,$ascension,$small = false) {
	if(is_numeric($ascension)) {
		$level = round(round((42-age($birthday,true))*30)*$ascension)+$stats;
		$mark = round($level/4860*100);
		return ($mark > 100 ? 100 : $mark).($small == false ? '/100' : '');
	} else {
		return '?';
	}
}
?>