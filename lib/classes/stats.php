<?php
class Stats {
	function visitors() {
		$first = mysql_fetch_array(mysql_query("SELECT v_timestamp,UNIX_TIMESTAMP(v_timestamp) AS date FROM v_views ORDER BY v_id ASC LIMIT 1;"));
		$daysago = (time()-$first['date'])/86400;
		$views = mysql_num_rows(mysql_query("SELECT v_id FROM v_views;"));
		$unique = mysql_num_rows(mysql_query("SELECT DISTINCT(v_ip) FROM v_views;"));
		$club_views = mysql_num_rows(mysql_query("SELECT v_id FROM v_views WHERE v_url LIKE '%clubs%';"));
		$clubs = mysql_num_rows(mysql_query("SELECT c_id FROM v_clubs WHERE timestamp > '".date('Y-m-d H:i:s',$first['date'])."';"));
		$unique_clubs = mysql_num_rows(mysql_query("SELECT DISTINCT(c_id) FROM v_clubs WHERE timestamp > '".date('Y-m-d H:i:s',$first['date'])."';"));
		$player_views = mysql_num_rows(mysql_query("SELECT v_id FROM v_views WHERE v_url LIKE '%players%';"));
		$players = mysql_num_rows(mysql_query("SELECT p_id FROM v_players WHERE timestamp > '".date('Y-m-d H:i:s',$first['date'])."';"));
		$unique_players = mysql_num_rows(mysql_query("SELECT DISTINCT(p_id) FROM v_players WHERE timestamp > '".date('Y-m-d H:i:s',$first['date'])."';"));
		$stats = '			<div class="portlet x12">	
				<div class="portlet-header">
					<h4>Besøgende i alt</h4>
				</div>
				<div class="portlet-content">
					<table class="data">
						<thead>
							<tr>
								<th style="width:40%;">Siden '.date('d-m-Y H:i',$first['date']).' ('.round($daysago).' dage siden)</th>
								<th style="width:15%;">Unikke</th>
								<th style="width:15%;">Besøg</th>
								<th style="width:15%;">Visninger</th>
								<th style="width:15%;">Visninger per unikke</th>
							</tr>
						</thead>
						<tbody>
							<tr class="odd">
								<td style="font-weight:bold;">Brugere</td>
								<td>'.$this->format($unique).'</td>
								<td>Ukendt</td>
								<td>'.$this->format($views).'</td>
								<td>'.$this->format($views/$unique,2).'</td>
							</tr>
							<tr class="even">
								<td style="font-weight:bold;">Klubber</td>
								<td>'.$this->format($unique_clubs).'</td>
								<td>'.$this->format($clubs).'</td>
								<td>'.$this->format($club_views).'</td>
								<td>'.$this->format($club_views/$unique_clubs,2).'</td>
							</tr>
							<tr class="odd">
								<td style="font-weight:bold;">Spillere</td>
								<td>'.$this->format($unique_players).'</td>
								<td>'.$this->format($players).'</td>
								<td>'.$this->format($player_views).'</td>
								<td>Ukendt</td>
							</tr>
						</tbody>
					</table>
				</div>	
			</div>
			<div class="portlet x12">	
				<div class="portlet-header">
					<h4>Besøgende per dag</h4>
				</div>
				<div class="portlet-content">
					<table class="data">
						<thead>
							<tr>
								<th style="width:40%;">Siden '.date('d-m-Y H:i',$first['date']).' ('.round($daysago).' dage siden)</th>
								<th style="width:15%;">Unikke</th>
								<th style="width:15%;">Besøg</th>
								<th style="width:15%;">Visninger</th>
								<th style="width:15%;">Visninger per unikke</th>
							</tr>
						</thead>
						<tbody>
							<tr class="odd">
								<td style="font-weight:bold;">Brugere</td>
								<td>'.$this->format($unique/$daysago).'</td>
								<td>Ukendt</td>
								<td>'.$this->format($views/$daysago).'</td>
								<td>'.$this->format(($views/$daysago)/($unique/$daysago),2).'</td>
							</tr>
							<tr class="even">
								<td style="font-weight:bold;">Klubber</td>
								<td>'.$this->format($unique_clubs/$daysago).'</td>
								<td>'.$this->format($clubs/$daysago).'</td>
								<td>'.$this->format($club_views/$daysago).'</td>
								<td>'.$this->format(($club_views/$daysago)/($unique_clubs/$daysago),2).'</td>
							</tr>
							<tr class="odd">
								<td style="font-weight:bold;">Spillere</td>
								<td>'.$this->format($unique_players/$daysago).'</td>
								<td>'.$this->format($players/$daysago).'</td>
								<td>'.$this->format($player_views/$daysago).'</td>
								<td>Ukendt</td>
							</tr>
						</tbody>
					</table>
				</div>	
			</div>';
		return $stats;
	}
	function count() {
		$ip = prepare($_SERVER['REMOTE_ADDR']);
		$url = prepare($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$user_agent = prepare($_SERVER['HTTP_USER_AGENT']);
		$referer = prepare($_SERVER['HTTP_REFERER']);
		mysql_query("INSERT INTO v_views (v_ip,v_url,v_user_agent,v_referer) VALUES ('".$ip."','".$url."','".$user_agent."','".$referer."');");
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