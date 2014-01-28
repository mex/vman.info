<?php
class Fetch {
	/*
	Fetch and output club information
	*/
	function club($cid) {
		global $lang;
		global $fetch_user_info;
// DEBUG
if($_GET['debug'] == 'true') {
	$starttime = microtime();
	$startarray = explode(" ", $starttime);
	$starttime = $startarray[1] + $startarray[0];
	echo "DEBUG:<br />";
}
// DEBUG
		$cid = prepare($cid);
		$fetch_club = mysql_fetch_array(mysql_query("SELECT c_name,c_vifa,c_supporters,c_training_facility,c_stadium,c_capacity,c_ticket_price,UNIX_TIMESTAMP(timestamp) AS date FROM v_clubs WHERE c_id = ".$cid." ORDER BY id DESC LIMIT 1;"));
		$time = time()-21600;
		$updatein = round(($fetch_club['date']-$time)/60);
		$update_hours = round($updatein/60-0.5);
		$update_min = $updatein-$update_hours*60;
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched timestamp<br />";
}
if($_GET['force_fetch'] == 'true') {
	$fetch_club['date'] -= 21600;
}
// DEBUG
		if($fetch_club['date'] < $time) {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://www.virtualmanager.com/clubs/'.$cid.'.json');
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_REFERER,'');
			$output = $this->entities(curl_exec($ch));
			curl_close($ch);
			$feed = json_decode($output,true);
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched from Virtual Manager<br />";
}
// DEBUG
			if(is_array($feed)) {
				mysql_query("INSERT INTO v_clubs (c_id,c_name,c_vifa,c_supporters,c_training_facility,c_stadium,c_capacity,c_ticket_price) VALUES (".$cid.",'".$feed['name']."',".$feed['vifa_points'].",".$feed['supporters'].",".$feed['training_facility_id'].",'".$feed['stadium_name']."',".$feed['stadium_capacity'].",".$feed['ticket_price'].");");
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Inserted VM club data into MySQL<br />";
}
// DEBUG
				foreach($feed['players'] AS $key => $dbp) {
					mysql_query("INSERT INTO v_players (c_id,p_id,p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name) VALUES (".$cid.",".$dbp['id'].",".$dbp['age'].",'".$dbp['leg']."',".$dbp['country_id'].",".$dbp['height'].",".$dbp['weight'].",".$dbp['value'].",'".$dbp['birthday']."','".$dbp['position']."','".$dbp['description']."',".$dbp['contract_expiry'].",".$dbp['wage'].",".$dbp['energy'].",".$dbp['finishing'].",".$dbp['dribling'].",".$dbp['passing'].",".$dbp['tackling'].",".$dbp['marking'].",".$dbp['penalty_taking'].",".$dbp['bravery'].",".$dbp['creativity'].",".$dbp['determination'].",".$dbp['influence'].",".$dbp['morale'].",".$dbp['off_the_ball'].",".$dbp['acceleration'].",".$dbp['balance'].",".$dbp['fitness'].",".$dbp['jump'].",".$dbp['strength'].",".$dbp['stamina'].",'".$dbp['name']."');");
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Inserted VM player ".$dbp['id']." into MySQL<br />";
}
// DEBUG
					$feed['players'][$key]['date2'] = $time;
					$feed['players'][$key]['last'] = $dbp['finishing']+$dbp['dribling']+$dbp['passing']+$dbp['tackling']+$dbp['marking']+$dbp['penalty_taking']+$dbp['bravery']+$dbp['creativity']+$dbp['determination']+$dbp['influence']+$dbp['morale']+$dbp['off_the_ball']+$dbp['acceleration']+$dbp['balance']+$dbp['fitness']+$dbp['jump']+$dbp['strength']+$dbp['stamina'];
					$fetch_player = mysql_fetch_assoc(mysql_query("SELECT p_stats,timestamp FROM v_players_first WHERE p_id = ".$dbp['id']." LIMIT 0,1;"));
					$feed['players'][$key]['date1'] = $fetch_player['timestamp'];
					$feed['players'][$key]['first'] = $fetch_player['p_stats'];
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched VM player ".$dbp['id']." first data MySQL<br />";
}
// DEBUG
				}
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Inserted VM player data into MySQL and fetched first player stats<br />";
}
// DEBUG
			} else {
				$error = true;
			}
		} else {
			$feed['name'] = $fetch_club['c_name'];
			$feed['vifa_points'] = $fetch_club['c_vifa'];
			$feed['supporters'] = $fetch_club['c_supporters'];
			$feed['training_facility_id'] = $fetch_club['c_training_facility'];
			$feed['stadium_name'] = $fetch_club['c_stadium'];
			$feed['stadium_capacity'] = $fetch_club['c_capacity'];
			$feed['ticket_price'] = $fetch_club['c_ticket_price'];
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Set variables from MySQL<br />";
}
// DEBUG
			$get_players = mysql_query("SELECT p_id,p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name FROM v_players WHERE c_id = ".$cid." && UNIX_TIMESTAMP(timestamp) >= ".$fetch_club['date'].";");
			while($fetch_players = mysql_fetch_assoc($get_players)) {
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched VM player ".$fetch_players['p_id']." from MySQL<br />";
}
// DEBUG
				$date2 = $time;
				$last = $fetch_players['p_finishing']+$fetch_players['p_dribling']+$fetch_players['p_passing']+$fetch_players['p_tackling']+$fetch_players['p_marking']+$fetch_players['p_penalty_taking']+$fetch_players['p_bravery']+$fetch_players['p_creativity']+$fetch_players['p_determination']+$fetch_players['p_influence']+$fetch_players['p_morale']+$fetch_players['p_off_the_ball']+$fetch_players['p_acceleration']+$fetch_players['p_balance']+$fetch_players['p_fitness']+$fetch_players['p_jump']+$fetch_players['p_strength']+$fetch_players['p_stamina'];
				$fetch_player = mysql_fetch_assoc(mysql_query("SELECT p_stats,timestamp FROM v_players_first WHERE p_id = ".$fetch_players['p_id']." LIMIT 0,1;"));
				$date1 = $fetch_player['timestamp'];
				$first = $fetch_player['p_stats'];
				$feed['players'][] = array('id' => $fetch_players['p_id'],'age' => $fetch_players['p_age'],'leg' => $fetch_players['p_leg'],'country_id' => $fetch_players['p_country'],'height' => $fetch_players['p_height'],'weight' => $fetch_players['p_weight'],'value' => $fetch_players['p_value'],'birthday' => $fetch_players['p_birthday'],'position' => $fetch_players['p_position'],'description' => $fetch_players['p_description'],'contract_expiry' => $fetch_players['p_contract_expiry'],'wage' => $fetch_players['p_wage'],'energy' => $fetch_players['p_energy'],'finishing' => $fetch_players['p_finishing'],'dribling' => $fetch_players['p_dribling'],'passing' => $fetch_players['p_passing'],'tackling' => $fetch_players['p_tackling'],'marking' => $fetch_players['p_marking'],'penalty_taking' => $fetch_players['p_penalty_taking'],'bravery' => $fetch_players['p_bravery'],'creativity' => $fetch_players['p_creativity'],'determination' => $fetch_players['p_determination'],'influence' => $fetch_players['p_influence'],'morale' => $fetch_players['p_morale'],'off_the_ball' => $fetch_players['p_off_the_ball'],'acceleration' => $fetch_players['p_acceleration'],'balance' => $fetch_players['p_balance'],'fitness' => $fetch_players['p_fitness'],'jump' => $fetch_players['p_jump'],'strength' => $fetch_players['p_strength'],'stamina' => $fetch_players['p_stamina'],'name' => $fetch_players['p_name'],'date2' => $date2,'date1' => $date1,'last' => $last,'first' => $first);
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched VM player ".$fetch_players['p_id']." first data MySQL<br />";
}
// DEBUG
			}
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Fetched players from MySQL and fetched first player stats<br />";
}
// DEBUG
		}
		
		$name = $feed['name'];
		
		$fetch_last_vifa = mysql_fetch_array(mysql_query("SELECT c_vifa,UNIX_TIMESTAMP(timestamp) AS date FROM v_clubs WHERE c_id = ".$cid." AND c_vifa != ".prepare($feed['vifa_points'])." ORDER BY timestamp DESC LIMIT 1;"));
		$fetch_last_supporters = mysql_fetch_array(mysql_query("SELECT c_supporters,UNIX_TIMESTAMP(timestamp) AS date FROM v_clubs WHERE c_id = ".$cid." AND c_supporters != ".prepare($feed['supporters'])." ORDER BY timestamp DESC LIMIT 1;"));
		$vifa_change = ($feed['vifa_points']-$fetch_last_vifa['c_vifa']);
		$supporters_change = ($feed['supporters']-$fetch_last_supporters['c_supporters']);
		
		$value1 = ($fetch_user_info['s_future1'] > 0 ? $fetch_user_info['s_future1'] : 25);
		$value2 = ($fetch_user_info['s_future2'] > 0 ? $fetch_user_info['s_future2'] : 30);
		
		$i = 0;
		if(is_array($feed['players'])) {
			foreach($feed['players'] AS $sort) {
				$order = ($_GET['sort'] == 'name' ? $sort['name'] : ($_GET['sort'] == 'position' ? $this->position($sort['position'],'numbers') : ($_GET['sort'] == 'leg' ? $sort['leg'] : ($_GET['sort'] == 'age' || $_GET['sort'] == 'pension' ? $this->age($sort['birthday'],true) : ($_GET['sort'] == 'value' ? $sort['value'] : ($_GET['sort'] == 'wage' ? $sort['wage'] : ($_GET['sort'] == 'energy' ? $sort['energy'] : $this->position($sort['position'],'numbers'))))))));
				$stats = $sort['finishing']+$sort['dribling']+$sort['passing']+$sort['tackling']+$sort['marking']+$sort['penalty_taking']+$sort['bravery']+$sort['creativity']+$sort['determination']+$sort['influence']+$sort['morale']+$sort['off_the_ball']+$sort['acceleration']+$sort['balance']+$sort['fitness']+$sort['jump']+$sort['strength']+$sort['stamina'];
				$ascension = $this->ascension($sort['first'],$sort['last'],$sort['date1'],$sort['date2']);
				$play[] = array($order,$i,$stats,$this->future($value1,$sort['birthday'],$stats,$ascension),$this->future($value2,$sort['birthday'],$stats,$ascension),$ascension);
				$i++;
			}
		}
		if(is_array($play)) {
			sort($play);
		}
		
		if($_GET['sort'] == 'stats') {
			foreach($play AS $play) {
				$players[] = array($play[2],$play[1]);
			}
			sort($players);
		} elseif($_GET['sort'] == 'future1') {
			foreach($play AS $play) {
				$players[] = array($play[3],$play[1]);
			}
			sort($players);
		} elseif($_GET['sort'] == 'future2') {
			foreach($play AS $play) {
				$players[] = array($play[4],$play[1]);
			}
			sort($players);
		} elseif($_GET['sort'] == 'ascension') {
			foreach($play AS $play) {
				$players[] = array($play[5],$play[1]);
			}
			sort($players);
		} else {
			$players = $play;
		}
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Prepared and sorted data<br />";
}
// DEBUG
		
		if(is_array($players)) {
			$players = ($_GET['order'] == 'desc' ? array_reverse($players) : $players);
			$count = count($players);
			foreach($players AS $p) {
				$player = $feed['players'][$p[1]];
				$count_wage += $player['wage'];
			}
		}
		$inc_stadium = ($fetch_user_info['s_stadium_average'] > 0 ? $fetch_user_info['s_stadium_average'] : $feed['stadium_capacity'])*$feed['ticket_price']*15;
		$inc_sponsor = ($fetch_user_info['s_sponsor'] > 0 ? $fetch_user_info['s_sponsor'] : 0);
		$exp_pla_wage = $count_wage*7;
		$exp_emp_wage = ($fetch_user_info['s_employees'] > 0 ? $fetch_user_info['s_employees'] : 0)*7;
		$exp_stadium = (round(($feed['stadium_capacity']*4.6)/1000)*1000)/3;
		if($fetch_user_info['s_stadium_average'] > 0) {
			$exp_stadium += $feed['stadium_capacity']*round($fetch_user_info['s_stadium_average']*15/$feed['stadium_capacity']/1.65);
		} else {
			$exp_stadium += $feed['stadium_capacity']*round(15/1.65);
		}
		$balance = ($inc_stadium+$inc_sponsor)-($exp_pla_wage+$exp_emp_wage+$exp_stadium);
		$balance = '<span class="'.(0 > $balance ? 'red">' : 'green">+').$this->format($balance).'</span>';
		$count_wage = null;
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Calculated balance<br />";
}
// DEBUG
		$club = '			<div class="portlet portlet-closable x8">	
				<div class="portlet-header">
					<h4>'.$feed['name'].'</h4>
				</div>
				<div class="portlet-content">
					<p>
						'.$lang['club']['latest_update'].': '.$this->present($fetch_club['date']).' ('.$lang['club']['update_available_in'].' '.($update_hours > 0 ? $update_hours.' '.$lang['club']['hours'].' '.$lang['page']['and'].' ' : '').round($update_min).' '.$lang['club']['minutes'].')<br />
						<b>VIFA-point'.($lang['lang'] == 'en' ? 's' : '').':</b> '.$this->format($feed['vifa_points']).' <span class="'.($fetch_last_vifa['c_vifa'] > $feed['vifa_points'] ? 'red">' : 'green">+').$this->format($vifa_change).'</span> ('.$lang['club']['since'].' '.strtolower($this->present($fetch_last_vifa['date'])).')<br />
						<b>'.$lang['club']['supporters'].':</b> '.$this->format($feed['supporters']).' <span class="'.($fetch_last_supporters['c_supporters'] > $feed['supporters'] ? 'red">' : 'green">+').$this->format($supporters_change).'</span> ('.$lang['club']['since'].' '.strtolower($this->present($fetch_last_supporters['date'])).')<br />
						<b>'.$lang['club']['training'].':</b> '.$this->training($feed['training_facility_id']).'<br />
						<b>'.$lang['club']['stadium'].':</b> '.$feed['stadium_name'].' '.$lang['club']['with'].' '.$this->format($feed['stadium_capacity']).' '.$lang['club']['seats'].' ('.$feed['ticket_price'].' C '.($fetch_user_info['s_stadium_average'] > 0 ? $lang['club']['around'].' '.$this->format($fetch_user_info['s_stadium_average']*$feed['ticket_price']) : $lang['club']['up_to'].' '.$this->format($feed['stadium_capacity']*$feed['ticket_price'])).' C/'.$lang['club']['match'].')<br />
						<br />
						<button><a href="/clubs/'.$cid.'/history"><span>'.$lang['club']['show_history'].'</span></a></button>
						<!--button><a href="/clubs/'.$cid.'/export"><span>'.($lang['lang'] == 'da' ? 'Eksporter til' : 'Export to').' Excel</span></a></button-->
						<button><a href="http://www.virtualmanager.com/clubs/'.$cid.'"><span>'.$feed['name'].' (Virtual Manager)</span></a></button>
					</p>
				</div>	
			</div>
			<div class="portlet portlet-closable x4">	
				<div class="portlet-header">
					<h4>'.$lang['club']['balance'].' ('.$lang['club']['estimate'].')</h4>
				</div>
				<div class="portlet-content">
					<b>'.$lang['club']['income'].':</b><br />
					'.$lang['club']['matches'].': '.$this->format($inc_stadium).' C/'.$lang['page']['week'].($fetch_user_info['s_stadium_average'] > 0 ? '' : ' ('.$lang['club']['if_full'].')').'<br />
					'.$lang['club']['sponsor'].': '.$this->format($inc_sponsor).' C/'.$lang['page']['week'].' (<a href="/settings">'.$lang['club']['edit'].'</a>)<br />
					<br />
					<b>'.$lang['club']['expenses'].':</b><br />
					'.$lang['club']['player_wages'].': '.$this->format($exp_pla_wage).' C/'.$lang['page']['week'].'<br />
					'.$lang['club']['employee_wages'].': '.$this->format($exp_emp_wage).' C/'.$lang['page']['week'].' (<a href="/settings">'.$lang['club']['edit'].'</a>)<br />
					'.$lang['club']['stadium_repair'].': '.$this->format($exp_stadium).' C/'.$lang['page']['week'].' (<a href="/settings">'.$lang['club']['edit'].'</a>)<br />
					<br />
					<b>'.$lang['club']['balance'].': '.$balance.' C/'.$lang['page']['week'].'</b>
				</div>	
			</div>';
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Finished showing of club<br />";
}
// DEBUG
		if($_GET['history'] == 'true') {
			$bg = 'even';
			$get_club_history = mysql_query("SELECT c_name,c_vifa,c_supporters,c_training_facility,c_stadium,c_capacity,c_ticket_price,UNIX_TIMESTAMP(timestamp) AS c_date FROM v_clubs WHERE c_id = ".$cid." ORDER BY timestamp DESC LIMIT 30");
			$z = 0;
			while($fetch_club_history = mysql_fetch_assoc($get_club_history)) {
				$bg = ($bg == 'odd' ? 'even' : 'odd');
				if($z < 30) {
					$date[] = date('d/m',$fetch_club_history['c_date']);
					$orig[] = $fetch_club_history['c_vifa'];
					$orig2[] = $fetch_club_history['c_supporters'];
				}
				$clubs .= '
							<tr class="'.$bg.'">
								<td><em>'.date('Y-m-d H:i',$fetch_club_history['c_date']).'</em>'.date('d-m-Y H:i',$fetch_club_history['c_date']).'</td>
								<td><em>'.$fetch_club_history['c_vifa'].'</em>'.$this->format($fetch_club_history['c_vifa']).'</td>
								<td><em>'.$fetch_club_history['c_supporters'].'</em>'.$this->format($fetch_club_history['c_supporters']).'</td>
								<td><em>'.$fetch_club_history['c_training_facility'].'</em>'.$this->training($fetch_club_history['c_training_facility']).'</td>
								<td>'.$fetch_club_history['c_stadium'].' ('.$this->format($fetch_club_history['c_capacity']).' '.$lang['club']['seats'].')</td>
								<td>'.$fetch_club_history['c_ticket_price'].($fetch_club_history['c_ticket_price'] == '-' ? '' : ' C').'</td>
							</tr>';
				$z++;
			}
			$date = array_reverse($date);
			$orig = array_reverse($orig);
			$orig2 = array_reverse($orig2);
			$max = round(max($orig)/1000+0.5);
			$max2 = round(max($orig2)/100+0.5);
			foreach($orig AS $orig) {
				$vifa[] = round($orig/($max*1000)*100,2);
			}
			foreach($orig2 AS $orig2) {
				$supporters[] = round($orig2/($max2*100)*100,2);
			}
			for($t=0;$t<=$max;$t++) {
				$yaxis[] = $t*1000;
			}
			for($t=0;$t<=$max2;$t++) {
				$yaxis2[] = $t*100;
			}
			$xdis = round(100/(count($date)-1),2);
			$ydis = round(100/(count($yaxis)-1),2);
			$xdis2 = round(100/(count($date)-1),2);
			$ydis2 = round(100/(count($yaxis2)-1),2);
			$club .= '
			<div class="portlet portlet-closable x12">
				<div class="portlet-header">
					<h4>'.$lang['club']['graph'].' VIFA-point'.($lang['lang'] == 'en' ? 's' : '').'</h4>
				</div>
				<div class="portlet-content">
					<img src="http://chart.apis.google.com/chart?chs=910x250&chf=bg,s,ffffff|c,s,ffffff&chxt=x,y&chg='.$xdis.','.$ydis.'&chxl=0:|'.implode('|',$date).'|1:|'.implode('|',$yaxis).'&cht=lc&chd=t:'.implode(',',$vifa).'&chco=258cd1" alt="" />
				</div>
			</div>
			<div class="portlet portlet-closable x12">
				<div class="portlet-header">
					<h4>'.$lang['club']['graph'].' '.strtolower($lang['club']['supporters']).'</h4>
				</div>
				<div class="portlet-content">
					<img src="http://chart.apis.google.com/chart?chs=910x250&chf=bg,s,ffffff|c,s,ffffff&chxt=x,y&chg='.$xdis2.','.$ydis2.'&chxl=0:|'.implode('|',$date).'|1:|'.implode('|',$yaxis2).'&cht=lc&chd=t:'.implode(',',$supporters).'&chco=258cd1" alt="" />
				</div>
			</div>
			<div class="portlet portlet-closable x12">
				<div class="portlet-header">
					<h4>'.$lang['club']['show_history'].'</h4>
				</div>
				<div class="portlet-content">
					<table id="dataTable3" class="data" cellpadding="0" cellspacing="0">				
						<thead>
							<tr>
								<th>'.$lang['club']['date'].'</th>
								<th>VIFA</th>
								<th>'.$lang['club']['supporters'].'</th>
								<th>'.$lang['club']['training'].'</th>
								<th>'.$lang['club']['stadium'].'</th>
								<th>'.$lang['club']['ticket_price'].'</th>
							</tr>
						</thead>
						<tbody>';
			$club .= $clubs;
			$club .= '
						</tbody>
					</table>';
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Finished calculating and showing of history<br />";
}
// DEBUG
		} else {
			if(is_array($players)) {
				$club .= '
			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$lang['club']['players'].'</h4>
				</div>
				<div class="portlet-content">
					<table id="dataTable" class="data" cellpadding="0" cellspacing="0">				
						<thead>
							<tr>
								<th>'.$lang['player']['name'].'</th>
								<th>Pos</th>
								<th>'.$lang['player']['leg'].'</th>
								<th>'.$lang['player']['age'].'</th>
								<th>'.$lang['player']['value'].'</th>
								<th>'.$lang['player']['wage'].'</th>
								<th>Stats</th>
								<th>'.$lang['player']['stats_ascension'].'</th>
								<th>'.$value1.' '.$lang['player']['year'].'</th>
								<th>'.$value2.' '.$lang['player']['year'].'</th>
								<th>'.$lang['player']['mark'].'</th>
								<th>Pension</th>
							</tr>
						</thead>
						<tbody>';
				$bg = 'even';
				$i = 0;
				foreach($players AS $p) {
					$player = $feed['players'][$p[1]];
					$teknik = $player['finishing']+$player['dribling']+$player['passing']+$player['tackling']+$player['marking']+$player['penalty_taking'];
					$mentalitet = $player['bravery']+$player['creativity']+$player['determination']+$player['influence']+$player['morale']+$player['off_the_ball'];
					$fysik = $player['acceleration']+$player['balance']+$player['fitness']+$player['jump']+$player['strength']+$player['stamina'];
					$desc = (strlen($player['description']) > 0 ? ' <img src="/gfx/'.strtolower($player['description']).'.gif" alt="" />' : '');
					$stats = $teknik+$mentalitet+$fysik;
					$stats_energy = $stats*($player['energy']/100);
					$ascension = $this->ascension($player['first'],$player['last'],$player['date1'],$player['date2']);
					$count_age += $this->age($player['birthday'],true);
					$count_value += $player['value'];
					$count_wage += $player['wage'];
					$count_stats += $stats;
					$count_stats_energy += $stats_energy;
					list($years,$days) = $this->age($player['birthday']);
					$bg = ($bg == 'odd' ? 'even' : 'odd');
					$club .= '
							<tr class="'.$bg.'">';
// DEBUG
if($_GET['debug'] == 'true') {
	$club .= '
								<td>'.$player['first'].'-'.$player['last'].'-'.($player['date2']-$player['date1']).'</td>';
}
// DEBUG
					$club .= '
								<td><a href="/players/'.$player['id'].'/history">'.$player['name'].$desc.'</a></td>
								<td><em>'.$this->position($player['position'],'numbers').'</em>'.$this->position($player['position']).'</td>
								<td>'.$this->leg($player['leg']).'</td>
								<td><em>'.$years.'.'.($days < 10 ? '0' : '').$days.'</em>'.$years.' '.$lang['player']['year'].($days > 0 ? ' '.$days.' '.($days > 1 ? $lang['page']['days'] : $lang['page']['day']) : '').'</td>
								<td><em>'.$player['value'].'</em>'.$this->format($player['value']).' C</td>
								<td><em>'.$player['wage'].'</em>'.$this->format($player['wage']).' C/'.$lang['page']['day'].'</td>
								<td class="stats" id="box_'.$i.'"><em>'.$stats.'</em>'.$this->format($stats).'</td>
								<td>'.$this->format($ascension,2).' (per '.$lang['page']['day'].')</td>
								<td><em>'.$this->future($value1,$player['birthday'],$stats,$ascension).'</em>'.$this->format($this->future($value1,$player['birthday'],$stats,$ascension)).'</td>
								<td><em>'.$this->future($value2,$player['birthday'],$stats,$ascension).'</em>'.$this->format($this->future($value2,$player['birthday'],$stats,$ascension)).'</td>
								<td><em>'.$this->mark($player['birthday'],$stats,$ascension,true).'</em>'.$this->mark($player['birthday'],$stats,$ascension).'</td>
								<td>'.$this->format($this->pension($player['birthday']),2).' %</td>
							</tr>
							<tr style="display:none;" id="statsbox_'.$i.'">
								<td colspan="4"></td>
								<td colspan="8"><div class="portlet x6" style="padding:10px;"><div class="portlet-header"><h4>Stats for '.$player['name'].'</h4></div><div class="portlet-content"><table class="data" cellpadding="0" cellspacing="0"><tbody><tr><td>'.$lang['player']['technique'].':</td><td>'.$teknik.'</td><td>'.$lang['player']['mentality'].':</td><td>'.$mentalitet.'</td><td>'.$lang['player']['physique'].':</td><td>'.$fysik.'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['handling'] : $lang['player']['finishing']).':</td><td>'.$player['finishing'].'</td><td>'.$lang['player']['bravery'].':</td><td>'.$player['bravery'].'</td><td>Acceleration:</td><td>'.$player['acceleration'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['one_on_one'] : $lang['player']['dribbling']).':</td><td>'.$player['dribling'].'</td><td>'.$lang['player']['creativity'].':</td><td>'.$player['creativity'].'</td><td>Balance:</td><td>'.$player['balance'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['goal_kick'] : $lang['player']['passing']).':</td><td>'.$player['passing'].'</td><td>'.$lang['player']['determination'].':</td><td>'.$player['determination'].'</td><td>'.$lang['player']['fitness'].':</td><td>'.$player['fitness'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['in_area'] : 'Tackling').':</td><td>'.$player['tackling'].'</td><td>'.$lang['player']['influence'].':</td><td>'.$player['influence'].'</td><td>'.$lang['player']['jumping'].':</td><td>'.$player['jump'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['aerial'] : $lang['player']['marking']).':</td><td>'.$player['marking'].'</td><td>'.$lang['player']['morale'].':</td><td>'.$player['morale'].'</td><td>'.$lang['player']['strength'].':</td><td>'.$player['strength'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['reflexes'] : $lang['player']['penalty_taking']).':</td><td>'.$player['penalty_taking'].'</td><td>'.$lang['player']['off_the_ball'].':</td><td>'.$player['off_the_ball'].'</td><td>'.$lang['player']['stamina'].':</td><td>'.$player['stamina'].'</td></tr></tbody></table></div></div></td>
							</tr>';
					$i++;
				}
				$bg = ($bg == 'odd' ? 'even' : 'odd');
				$club .= '
						</tbody>
						<tbody>
							<tr class="'.$bg.'">
								<td colspan="3">'.$lang['player']['average'].':</td>
								<td>'.$this->format($count_age/$count).' '.$lang['player']['year'].'</td>
								<td>'.$this->format($count_value/$count).' C</td>
								<td>'.$this->format($count_wage/$count).' C/'.$lang['page']['day'].'</td>
								<td>'.$this->format($count_stats/$count).'</td>
								<td colspan="5"></td>
							</tr>';
				$bg = ($bg == 'odd' ? 'row' : 'odd');
				$club .= '
							<tr class="'.$bg.'">
								<td colspan="3">'.$lang['player']['total'].':</td>
								<td>'.$this->format($count_age).' '.$lang['player']['year'].'</td>
								<td>'.$this->format($count_value).' C</td>
								<td>'.$this->format($count_wage).' C/'.$lang['page']['day'].'</td>
								<td>'.$this->format($count_stats).'</td>
								<td colspan="5"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>';
			}
		}
// DEBUG
if($_GET['debug'] == 'true') {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime; 
	$totaltime = round($totaltime,5);
	echo $totaltime." seconds: Finished calculating and showing of players<br />";
}
// DEBUG
		return array($club,$name);
	}
	/*
	Fetch and output player information
	*/
	function player($pid) {
		global $lang;
		global $fetch_user_info;
		$id = split('-',$pid);
		$short_id = prepare($id[0]);
		$fetch_player = mysql_fetch_array(mysql_query("SELECT p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_auction_bid,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$short_id." ORDER BY id DESC LIMIT 1;"));
		$time = time()-21600;
		$updatein = round(($fetch_player['date']-$time)/60);
		$update_hours = round($updatein/60-0.5);
		$update_min = $updatein-$update_hours*60;
		if($fetch_player['date'] < $time) {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://www.virtualmanager.com/players/'.$pid.'.json');
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_REFERER,'');
			$output = $this->entities(curl_exec($ch));
			curl_close($ch);
			$feed = json_decode($output,true);
			mysql_query("INSERT INTO v_players (c_id,p_id,p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name) VALUES (".$feed['club_id'].",".$short_id.",".$feed['age'].",'".$feed['leg']."',".$feed['country_id'].",".$feed['height'].",".$feed['weight'].",".$feed['value'].",'".$feed['birthday']."','".$feed['position']."','".$feed['description']."',".$feed['contract_expiry'].",".$feed['wage'].",".$feed['energy'].",".$feed['finishing'].",".$feed['dribling'].",".$feed['passing'].",".$feed['tackling'].",".$feed['marking'].",".$feed['penalty_taking'].",".$feed['bravery'].",".$feed['creativity'].",".$feed['determination'].",".$feed['influence'].",".$feed['morale'].",".$feed['off_the_ball'].",".$feed['acceleration'].",".$feed['balance'].",".$feed['fitness'].",".$feed['jump'].",".$feed['strength'].",".$feed['stamina'].",'".$feed['name']."');");
			$get_player_history = mysql_query("SELECT p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$short_id." ORDER BY TIMESTAMP DESC;");
			$s = 0;
			while($fetch_player = mysql_fetch_assoc($get_player_history)) {
				if($s == 0) {
					$feed['date2'] = $fetch_player['date'];
					$feed['last'] = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
				}
				$feed['date1'] = $fetch_player['date'];
				$feed['first'] = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
				$s++;
			}
		} else {
			$get_player_history = mysql_query("SELECT p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$short_id." ORDER BY TIMESTAMP DESC;");
			$s = 0;
			while($fetch_player_history = mysql_fetch_assoc($get_player_history)) {
				if($s == 0) {
					$date2 = $fetch_player_history['date'];
					$last = $fetch_player_history['p_finishing']+$fetch_player_history['p_dribling']+$fetch_player_history['p_passing']+$fetch_player_history['p_tackling']+$fetch_player_history['p_marking']+$fetch_player_history['p_penalty_taking']+$fetch_player_history['p_bravery']+$fetch_player_history['p_creativity']+$fetch_player_history['p_determination']+$fetch_player_history['p_influence']+$fetch_player_history['p_morale']+$fetch_player_history['p_off_the_ball']+$fetch_player_history['p_acceleration']+$fetch_player_history['p_balance']+$fetch_player_history['p_fitness']+$fetch_player_history['p_jump']+$fetch_player_history['p_strength']+$fetch_player_history['p_stamina'];
				}
				$date1 = $fetch_player_history['date'];
				$first = $fetch_player_history['p_finishing']+$fetch_player_history['p_dribling']+$fetch_player_history['p_passing']+$fetch_player_history['p_tackling']+$fetch_player_history['p_marking']+$fetch_player_history['p_penalty_taking']+$fetch_player_history['p_bravery']+$fetch_player_history['p_creativity']+$fetch_player_history['p_determination']+$fetch_player_history['p_influence']+$fetch_player_history['p_morale']+$fetch_player_history['p_off_the_ball']+$fetch_player_history['p_acceleration']+$fetch_player_history['p_balance']+$fetch_player_history['p_fitness']+$fetch_player_history['p_jump']+$fetch_player_history['p_strength']+$fetch_player_history['p_stamina'];
				$s++;
			}
			$feed = array('id' => $short_id,'age' => $fetch_player['p_age'],'leg' => $fetch_player['p_leg'],'country_id' => $fetch_player['p_country'],'height' => $fetch_player['p_height'],'weight' => $fetch_player['p_weight'],'value' => $fetch_player['p_value'],'birthday' => $fetch_player['p_birthday'],'position' => $fetch_player['p_position'],'description' => $fetch_player['p_description'],'contract_expiry' => $fetch_player['p_contract_expiry'],'wage' => $fetch_player['p_wage'],'energy' => $fetch_player['p_energy'],'finishing' => $fetch_player['p_finishing'],'dribling' => $fetch_player['p_dribling'],'passing' => $fetch_player['p_passing'],'tackling' => $fetch_player['p_tackling'],'marking' => $fetch_player['p_marking'],'penalty_taking' => $fetch_player['p_penalty_taking'],'bravery' => $fetch_player['p_bravery'],'creativity' => $fetch_player['p_creativity'],'determination' => $fetch_player['p_determination'],'influence' => $fetch_player['p_influence'],'morale' => $fetch_player['p_morale'],'off_the_ball' => $fetch_player['p_off_the_ball'],'acceleration' => $fetch_player['p_acceleration'],'balance' => $fetch_player['p_balance'],'fitness' => $fetch_player['p_fitness'],'jump' => $fetch_player['p_jump'],'strength' => $fetch_player['p_strength'],'stamina' => $fetch_player['p_stamina'],'name' => $fetch_player['p_name'],'date2' => $date2,'date1' => $date1,'last' => $last,'first' => $first);
		}
		$name = $player['name'];
		$bg = 'even';
		$player = $feed;
		$teknik = $player['finishing']+$player['dribling']+$player['passing']+$player['tackling']+$player['marking']+$player['penalty_taking'];
		$mentalitet = $player['bravery']+$player['creativity']+$player['determination']+$player['influence']+$player['morale']+$player['off_the_ball'];
		$fysik = $player['acceleration']+$player['balance']+$player['fitness']+$player['jump']+$player['strength']+$player['stamina'];
		$desc = (strlen($player['description']) > 0 ? ' <img src="/gfx/'.strtolower($player['description']).'.gif" alt="" />' : '');
		$stats = $teknik+$mentalitet+$fysik;
		$stats_energy = $stats*($player['energy']/100);
		$ascension = $this->ascension($player['first'],$player['last'],$player['date1'],$player['date2']);
		$count_age += $this->age($player['birthday'],true);
		$count_value += $player['value'];
		$count_wage += $player['wage'];
		$count_stats += $stats;
		$count_stats_energy += $stats_energy;
		list($years,$days) = $this->age($player['birthday']);
		$value1 = ($fetch_user_info['s_future1'] > 0 ? $fetch_user_info['s_future1'] : 25);
		$value2 = ($fetch_user_info['s_future2'] > 0 ? $fetch_user_info['s_future2'] : 30);
		$bg = ($bg == 'odd' ? 'even' : 'odd');
		$output = '			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$player['name'].'</h4>
				</div>
				<div class="portlet-content">
					<table class="data" cellpadding="0" cellspacing="0">				
						<thead>
							<tr>
								<th>'.$lang['player']['name'].'</th>
								<th>Pos</th>
								<th>'.$lang['player']['leg'].'</th>
								<th>'.$lang['player']['age'].'</th>
								<th>'.$lang['player']['value'].'</th>
								<th>'.$lang['player']['wage'].'</th>
								<th>Stats</th>
								<th>'.$lang['player']['stats_ascension'].'</th>
								<th>'.$value1.' '.$lang['player']['year'].'</th>
								<th>'.$value2.' '.$lang['player']['year'].'</th>
								<th>'.$lang['player']['mark'].'</th>
								<th>Pension</th>
							</tr>
						</thead>
						<tbody>
							<tr class="'.$bg.'">
								<td>'.$player['name'].$desc.'</td>
								<td><em>'.$this->position($player['position'],'numbers').'</em>'.$this->position($player['position']).'</td>
								<td>'.$this->leg($player['leg']).'</td>
								<td><em>'.$years.'.'.($days < 10 ? '0' : '').$days.'</em>'.$years.' '.$lang['player']['year'].($days > 0 ? ' '.$lang['page']['and'].' '.$days.' '.($days > 1 ? $lang['page']['days'] : $lang['page']['day']) : '').'</td>
								<td><em>'.$player['value'].'</em>'.$this->format($player['value']).' C</td>
								<td><em>'.$player['wage'].'</em>'.$this->format($player['wage']).' C/'.$lang['page']['day'].'</td>
								<td class="stats" id="box_'.$short_id.'">'.$this->format($stats).'</td>
								<td>'.$this->format($ascension,2).' (per '.$lang['page']['day'].')</td>
								<td><em>'.$this->future($value1,$player['birthday'],$stats,$ascension).'</em>'.$this->format($this->future($value1,$player['birthday'],$stats,$ascension)).'</td>
								<td><em>'.$this->future($value2,$player['birthday'],$stats,$ascension).'</em>'.$this->format($this->future($value2,$player['birthday'],$stats,$ascension)).'</td>
								<td>'.$this->mark($player['birthday'],$stats,$ascension).'</td>
								<td>'.$this->format($this->pension($player['birthday']),2).' %</td>
							</tr>
							<tr style="display:none;" id="statsbox_'.$short_id.'">
								<td colspan="4"></td>
								<td colspan="8"><div class="portlet x6" style="padding:10px;"><div class="portlet-header"><h4>Stats for '.$player['name'].'</h4></div><div class="portlet-content"><table class="data" cellpadding="0" cellspacing="0"><tbody><tr><td>'.$lang['player']['technique'].':</td><td>'.$teknik.'</td><td>'.$lang['player']['mentality'].':</td><td>'.$mentalitet.'</td><td>'.$lang['player']['physique'].':</td><td>'.$fysik.'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['handling'] : $lang['player']['finishing']).':</td><td>'.$player['finishing'].'</td><td>'.$lang['player']['bravery'].':</td><td>'.$player['bravery'].'</td><td>Acceleration:</td><td>'.$player['acceleration'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['one_on_one'] : $lang['player']['dribbling']).':</td><td>'.$player['dribling'].'</td><td>'.$lang['player']['creativity'].':</td><td>'.$player['creativity'].'</td><td>Balance:</td><td>'.$player['balance'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['goal_kick'] : $lang['player']['passing']).':</td><td>'.$player['passing'].'</td><td>'.$lang['player']['determination'].':</td><td>'.$player['determination'].'</td><td>'.$lang['player']['fitness'].':</td><td>'.$player['fitness'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['in_area'] : 'Tackling').':</td><td>'.$player['tackling'].'</td><td>'.$lang['player']['influence'].':</td><td>'.$player['influence'].'</td><td>'.$lang['player']['jumping'].':</td><td>'.$player['jump'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['aerial'] : $lang['player']['marking']).':</td><td>'.$player['marking'].'</td><td>'.$lang['player']['morale'].':</td><td>'.$player['morale'].'</td><td>'.$lang['player']['strength'].':</td><td>'.$player['strength'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['reflexes'] : $lang['player']['penalty_taking']).':</td><td>'.$player['penalty_taking'].'</td><td>'.$lang['player']['off_the_ball'].':</td><td>'.$player['off_the_ball'].'</td><td>'.$lang['player']['stamina'].':</td><td>'.$player['stamina'].'</td></tr></tbody></table></div></div></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>';
		$get_player_history = mysql_query("SELECT p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$short_id." ORDER BY TIMESTAMP DESC;");
		$feed = null;
		while($fetch_player = mysql_fetch_assoc($get_player_history)) {
			$feed[] = array('date' => $fetch_player['date'],'age' => $fetch_player['p_age'],'leg' => $fetch_player['p_leg'],'country_id' => $fetch_player['p_country'],'height' => $fetch_player['p_height'],'weight' => $fetch_player['p_weight'],'value' => $fetch_player['p_value'],'birthday' => $fetch_player['p_birthday'],'position' => $fetch_player['p_position'],'description' => $fetch_player['p_description'],'contract_expiry' => $fetch_player['p_contract_expiry'],'wage' => $fetch_player['p_wage'],'energy' => $fetch_player['p_energy'],'finishing' => $fetch_player['p_finishing'],'dribling' => $fetch_player['p_dribling'],'passing' => $fetch_player['p_passing'],'tackling' => $fetch_player['p_tackling'],'marking' => $fetch_player['p_marking'],'penalty_taking' => $fetch_player['p_penalty_taking'],'bravery' => $fetch_player['p_bravery'],'creativity' => $fetch_player['p_creativity'],'determination' => $fetch_player['p_determination'],'influence' => $fetch_player['p_influence'],'morale' => $fetch_player['p_morale'],'off_the_ball' => $fetch_player['p_off_the_ball'],'acceleration' => $fetch_player['p_acceleration'],'balance' => $fetch_player['p_balance'],'fitness' => $fetch_player['p_fitness'],'jump' => $fetch_player['p_jump'],'strength' => $fetch_player['p_strength'],'stamina' => $fetch_player['p_stamina'],'name' => $fetch_player['p_name']);
			$name = $fetch_player['p_name'];
		}
		$output .= '			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$name.$lang['main']['shistory'].'</h4>
				</div>
				<div class="portlet-content">
					<table id="dataTable2" class="data" cellpadding="0" cellspacing="0">				
						<thead>
							<tr>
								<th></th>
								<th></th>
								<th>'.$lang['player']['name'].'</th>
								<th>Pos</th>
								<th>'.$lang['player']['leg'].'</th>
								<th>'.$lang['player']['age'].'</th>
								<th>'.$lang['player']['value'].'</th>
								<th>'.$lang['player']['wage'].'</th>
								<th>Stats</th>
								<th>'.$lang['player']['energy'].'</th>
								<th>Pension</th>
							</tr>
						</thead>
						<tbody>';
		$bg = 'even';
		$i = 0;
		foreach($feed AS $player) {
			$teknik = $player['finishing']+$player['dribling']+$player['passing']+$player['tackling']+$player['marking']+$player['penalty_taking'];
			$mentalitet = $player['bravery']+$player['creativity']+$player['determination']+$player['influence']+$player['morale']+$player['off_the_ball'];
			$fysik = $player['acceleration']+$player['balance']+$player['fitness']+$player['jump']+$player['strength']+$player['stamina'];
			$desc = (strlen($player['description']) > 0 ? ' <img src="/gfx/'.strtolower($player['description']).'.gif" alt="" />' : '');
			$stats = $teknik+$mentalitet+$fysik;
			$stats_energy = $stats*($player['energy']/100);
			$count_age += $this->age($player['birthday'],true,$player['date']);
			$count_value += $player['value'];
			$count_wage += $player['wage'];
			$count_stats += $stats;
			$count_stats_energy += $stats_energy;
			list($years,$days) = $this->age($player['birthday'],false,$player['date']);
			$bg = ($bg == 'odd' ? 'even' : 'odd');
			$output .= '
							<tr class="'.$bg.'">
								<td>'.date('d-m-Y H:i',$player['date']).'</td>
								<td><img src="/gfx/flags/flag_'.$player['country_id'].'.gif" alt="'.$this->country($player['country_id']).'" title="'.$this->country($player['country_id']).'" /></td>
								<td><a href="http://www.virtualmanager.com/players/'.$pid.'" target="_blank">'.$player['name'].$desc.'</a></td>
								<td><em>'.$this->position($player['position'],'numbers').'</em>'.$this->position($player['position'],'long').'</td>
								<td>'.$this->leg($player['leg']).'</td>
								<td><em>'.$years.'.'.($days < 10 ? '0' : '').$days.'</em>'.$years.' '.$lang['player']['year'].($days > 0 ? ' '.$lang['page']['and'].' '.$days.' '.($days > 1 ? $lang['page']['days'] : $lang['page']['day']) : '').'
								<td><em>'.$player['value'].'</em>'.$this->format($player['value']).' C</td>
								<td><em>'.$player['wage'].'</em>'.$this->format($player['wage']).' C/'.$lang['page']['day'].'</td>
								<td class="stats" id="box_'.$i.'"><em>'.$stats.'</em>'.$this->format($stats).'</td>
								<td><em>'.$stats_energy.'</em>'.$this->format($stats_energy).' ('.$player['energy'].' %)</td>
								<td>'.$this->format($this->pension($player['birthday'],$player['date']),2).' %</td>
							</tr>
							<tr style="display:none;" id="statsbox_'.$i.'">
								<td colspan="5"></td>
								<td colspan="6"><div class="portlet x6" style="padding:10px;"><div class="portlet-header"><h4>Stats for '.$player['name'].'</h4></div><div class="portlet-content"><table class="data" cellpadding="0" cellspacing="0"><tbody><tr><td>'.$lang['player']['technique'].':</td><td>'.$teknik.'</td><td>'.$lang['player']['mentality'].':</td><td>'.$mentalitet.'</td><td>'.$lang['player']['physique'].':</td><td>'.$fysik.'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['handling'] : $lang['player']['finishing']).':</td><td>'.$player['finishing'].'</td><td>'.$lang['player']['bravery'].':</td><td>'.$player['bravery'].'</td><td>Acceleration:</td><td>'.$player['acceleration'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['one_on_one'] : $lang['player']['dribbling']).':</td><td>'.$player['dribling'].'</td><td>'.$lang['player']['creativity'].':</td><td>'.$player['creativity'].'</td><td>Balance:</td><td>'.$player['balance'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['goal_kick'] : $lang['player']['passing']).':</td><td>'.$player['passing'].'</td><td>'.$lang['player']['determination'].':</td><td>'.$player['determination'].'</td><td>'.$lang['player']['fitness'].':</td><td>'.$player['fitness'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['in_area'] : 'Tackling').':</td><td>'.$player['tackling'].'</td><td>'.$lang['player']['influence'].':</td><td>'.$player['influence'].'</td><td>'.$lang['player']['jumping'].':</td><td>'.$player['jump'].'</td></tr><tr class="odd"><td>'.($player['position'] == 'K' ? $lang['player']['aerial'] : $lang['player']['marking']).':</td><td>'.$player['marking'].'</td><td>'.$lang['player']['morale'].':</td><td>'.$player['morale'].'</td><td>'.$lang['player']['strength'].':</td><td>'.$player['strength'].'</td></tr><tr><td>'.($player['position'] == 'K' ? $lang['player']['reflexes'] : $lang['player']['penalty_taking']).':</td><td>'.$player['penalty_taking'].'</td><td>'.$lang['player']['off_the_ball'].':</td><td>'.$player['off_the_ball'].'</td><td>'.$lang['player']['stamina'].':</td><td>'.$player['stamina'].'</td></tr></tbody></table></div></div></td>
							</tr>';
			$i++;
		}
		$output .= '
						</tbody>
					</table>
				</div>
			</div>';
		return array($output,$name);
	}
	function explanations() {
		global $lang;
		$output = '
			<div class="portlet portlet-closable x12">
				<div class="portlet-header">
					<h4>'.$lang['explanations']['explanations'].'</h4>
				</div>
				<div class="portlet-content">
					<p>
						<b>'.$lang['explanations']['ascension'].'</b><br />
						'.$lang['explanations']['ascension_content'].'
					</p>
					<p>
						<b>'.$lang['explanations']['future'].'</b><br />
						'.$lang['explanations']['future_content'].'
					</p>
					<p>
						<b>'.$lang['explanations']['potential'].'</b><br />
						'.$lang['explanations']['potential_content'].'
					</p>
					<p>
						<b>'.$lang['explanations']['pension'].'</b><br />
						'.$lang['explanations']['pension_content'].'
					</p>
					<p>
						<b>'.$lang['explanations']['stadium_degradation'].'</b><br />
						'.$lang['explanations']['stadium_degradation_content'].'
					</p>
				</div>
			</div>';
		return $output;
	}
	
	/*
	All other functions used when fetching and outputting club and player information
	*/
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
	function country($id) {
		global $lang;
		$from = array(49,48,47,46,45,44,43,42,41,40,39,38,37,36,35,34,33,32,31,30,29,28,27,26,25,24,23,22,21,20,19,18,17,16,15,14,13,12,11,10,9,8,7,6,5,4,3,2,1);
		$to = $lang['fetch']['countries'];
		return str_replace($from,$to,$id);
	}
	function future($age,$birthday,$stats,$ascension) {
		if(($age-$this->age($birthday,true)) > 0) {
			if(is_numeric($ascension)) {
				$future = round(round(($age-$this->age($birthday,true))*30)*$ascension)+$stats;
			} else {
				$future = '?';
			}
		} else {
			$future = '-';
		}
		return $future;
	}
	function leg($leg) {
		global $lang;
		if($lang['lang'] == 'en') {
			return ucfirst(strtolower($leg));
		} else {
			$from = array('BOTH','LEFT','RIGHT');
			$to = $lang['fetch']['leg1'];
			$leg = str_replace($from,$to,$leg);
			$from = array('L','R');
			$to = $lang['fetch']['leg2'];
			return str_replace($from,$to,$leg);
		}
	}
	function mark($birthday,$stats,$ascension,$small = false) {
		if(is_numeric($ascension)) {
			$level = round(round((42-$this->age($birthday,true))*30)*$ascension)+$stats;
			$mark = round($level/4860*100);
			return ($mark > 100 ? 100 : $mark).($small == false ? '/100' : '');
		} else {
			return '?';
		}
	}
	function pension($birthday,$altdate = 0) {
		$age = round((($altdate > 0 ? $altdate : time())-strtotime($birthday))/(86400*30),1);
		if($age < 31) {
			$age = '0';
		} elseif($age > 42) {
			$age = '100';
		} else {
			$age = 'a'.$age.'a';
			$from = array('a31a','a31.1a','a31.2a','a31.3a','a31.4a','a31.5a','a31.6a','a31.7a','a31.8a','a31.9a','a32a','a32.1a','a32.2a','a32.3a','a32.4a','a32.5a','a32.6a','a32.7a','a32.8a','a32.9a','a33a','a33.1a','a33.2a','a33.3a','a33.4a','a33.5a','a33.6a','a33.7a','a33.8a','a33.9a','a34a','a34.1a','a34.2a','a34.3a','a34.4a','a34.5a','a34.6a','a34.7a','a34.8a','a34.9a','a35a','a35.1a','a35.2a','a35.3a','a35.4a','a35.5a','a35.6a','a35.7a','a35.8a','a35.9a','a36a','a36.1a','a36.2a','a36.3a','a36.4a','a36.5a','a36.6a','a36.7a','a36.8a','a36.9a','a37a','a37.1a','a37.2a','a37.3a','a37.4a','a37.5a','a37.6a','a37.7a','a37.8a','a37.9a','a38a','a38.1a','a38.2a','a38.3a','a38.4a','a38.5a','a38.6a','a38.7a','a38.8a','a38.9a','a39a','a39.1a','a39.2a','a39.3a','a39.4a','a39.5a','a39.6a','a39.7a','a39.8a','a39.9a','a40a','a40.1a','a40.2a','a40.3a','a40.4a','a40.5a','a40.6a','a40.7a','a40.8a','a40.9a','a41a','a41.1a','a41.2a','a41.3a','a41.4a','a41.5a','a41.6a','a41.7a','a41.8a','a41.9a','a42a');
			$to = array(0,0.06,0.21,0.44,0.77,1.18,1.69,2.29,2.98,3.77,4.65,5.62,6.69,7.85,9.09,10.43,11.85,13.35,14.94,16.6,18.33,20.13,22,23.92,25.91,27.94,30.02,32.13,34.28,36.46,38.67,40.88,43.11,45.34,47.58,49.8,52.01,54.21,56.37,58.51,60.62,62.69,64.71,66.69,68.62,70.49,72.31,74.06,75.76,77.39,78.95,80.45,81.88,83.24,84.53,85.76,86.91,88,89.03,89.99,90.89,91.73,92.51,93.23,93.9,94.51,95.08,95.6,96.07,96.51,96.9,97.26,97.58,97.87,98.13,98.36,98.57,98.76,98.92,99.07,99.2,99.31,99.41,99.5,99.57,99.64,99.69,99.74,99.78,99.82,99.85,99.87,99.9,99.91,99.93,99.94,99.95,99.96,99.97,99.97,99.98,99.98,99.99,99.99,99.99,99.99,99.99,100,100,100,100);
			$age = str_replace($from,$to,$age);
		}
		return $age;
	}
	function physio($id) {
		global $lang;
		$from = array('5','4','3','2','1','0');
		$to = $lang['fetch']['physio'];
		return str_replace($from,$to,$id);
	}
	function position($position,$special = 'short') {
		global $lang;
		$from = array('K','DL','DC','DR','MD','ML','MC','MR','MO','FL','FC','FR');
		if($special == 'long') {
			$to = $lang['fetch']['position'];
			return str_replace($from,$to,$position);
		} else if($special == 'numbers') {
			$to = array('1','2','3','4','5','6','7','8','9','10','11','12');
			return str_replace($from,$to,$position);
		} else {
			if($lang['lang'] == 'da') {
				$to = array('DL' => 'FV','DC' => 'FC','DR' => 'FH','ML' => 'MV','MR' => 'MH','FL' => 'AV','FC' => 'AC','FR' => 'AH');
				return strtr($position,$to);
			}
			return $position;
		}
	}
	function present($timestamp) {
		global $lang;
		$date = date('d-m-Y, H:i',$timestamp);
		$from = array(date('d-m-Y'),date('d-m-Y',time()-86400));
		$to = $lang['fetch']['date'];
		return str_replace($from,$to,$date);
	}
	function training($id,$image = true) {
		global $lang;
		$id = ($id < 10 ? '0'.$id : $id);
		$from = array('11','10','09','08','07','06','05','04','03','02','01');
		if($image == true) {
			$to = $lang['fetch']['training1'];
		} else {
			$to = $lang['fetch']['training2'];
		}
		return str_replace('<img src="/gfx/world_class.gif" alt="" />2','<img src="/gfx/world_class2.gif" alt="" />',str_replace('class1','class',preg_replace('/(class2|class1|world_class2|world_class1|world_class)/','<img src="/gfx/$1.gif" alt="" />',str_replace($from,$to,$id))));
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
}
?>