<?php
require_once($_SERVER['DOCUMENT_ROOT'].'lib/classes/fetch.php');

function entity($str) {
	$from = array('&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
	$to = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','Þ','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ');
	return str_replace($from,$to,$str);
}

$db = mysql_connect('mysql2.gigahost.dk','ejsing','X8cETR6br5nu4eBru93drupr');
mysql_select_db('ejsing_vman',$db);

$cid = (strlen($_GET['cid']) > 0 ? $_GET['cid'] : '');

if($cid > 0) {
	require_once($_SERVER['DOCUMENT_ROOT'].'export/PHPExcel.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'export/PHPExcel/IOFactory.php');
	
	$fetch_club = mysql_fetch_array(mysql_query("SELECT c_name,UNIX_TIMESTAMP(timestamp) AS date FROM v_clubs WHERE c_id = ".$cid." ORDER BY id DESC LIMIT 1;"));
	
	// Create new PHPExcel object
	$fetch = new Fetch;
	$objPHPExcel = new PHPExcel();
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Vman.info")
								 ->setLastModifiedBy("Vman.info")
								 ->setTitle("Vman.info - ".$fetch_club['c_name'])
								 ->setSubject("Vman.info - ".$fetch_club['c_name'])
								 ->setDescription("Vman.info - ".$fetch_club['c_name']."'s klubdata eksporteret til Excel via vman.info")
								 ->setKeywords("vman info klub data ".$fetch_club['c_name'])
								 ->setCategory("Vman.info");
	
	// Set title row bold
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
	
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A1', 'Dato')
	            ->setCellValue('B1', 'Klubnavn')
	            ->setCellValue('C1', 'VIFA-point')
	            ->setCellValue('D1', 'Supportere')
	            ->setCellValue('E1', 'Træningsanlæg')
	            ->setCellValue('F1', 'Fysioafdeling')
	            ->setCellValue('G1', 'Stadion')
	            ->setCellValue('H1', 'Billetpris');
	
	$get_info = mysql_query("SELECT c_name,c_vifa,c_supporters,c_training_facility,c_physio,c_stadium,c_capacity,c_ticket_price,UNIX_TIMESTAMP(timestamp) AS date FROM v_clubs WHERE c_id = ".$cid." ORDER BY id DESC;");
	$i = 2;
	while($fetch_info = mysql_fetch_assoc($get_info)) {
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$i, date('d-m-Y H:i',$fetch_info['date']))
		            ->setCellValue('B'.$i, entity($fetch_info['c_name']))
		            ->setCellValue('C'.$i, $fetch_info['c_vifa'])
		            ->setCellValue('D'.$i, $fetch_info['c_supporters'])
		            ->setCellValue('E'.$i, entity($fetch->training($fetch_info['c_training_facility'],false)))
		            ->setCellValue('F'.$i, entity($fetch->physio($fetch_info['c_physio'])))
		            ->setCellValue('G'.$i, entity($fetch_info['c_stadium']).' ('.$fetch_info['c_capacity'].' sæder)')
		            ->setCellValue('H'.$i, $fetch_info['c_ticket_price'].' C');
		$i++;
	}
	
	// Set autosize
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Klubinformation');

	// Create new sheet
	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(1);
	
	// Set title row bold
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
	
	$value1 = 25;
	$value2 = 30;
	$value3 = 35;
	if(strlen($_COOKIE['future']) > 0) {
		$values = split('-',$_COOKIE['future']);
		$value1 = (is_numeric($values[0]) ? $values[0] : $value1);
		$value2 = (is_numeric($values[1]) ? $values[1] : $value2);
		$value3 = (is_numeric($values[2]) ? $values[2] : $value3);
	}
	
	// Add some data
	$objPHPExcel->setActiveSheetIndex(1)
	            ->setCellValue('A1', 'Navn')
	            ->setCellValue('B1', 'Land')
	            ->setCellValue('C1', 'Position')
	            ->setCellValue('D1', 'Ben')
	            ->setCellValue('E1', 'Alder')
	            ->setCellValue('F1', 'Værdi')
	            ->setCellValue('G1', 'Løn')
	            ->setCellValue('H1', 'Stats')
	            ->setCellValue('I1', 'Energi')
	            ->setCellValue('J1', 'Statsstigning')
	            ->setCellValue('K1', $value1.' år')
	            ->setCellValue('L1', $value2.' år')
	            ->setCellValue('M1', $value3.' år')
	            ->setCellValue('N1', 'Pension');
	
	$get_players = mysql_query("SELECT p_id,p_age,p_leg,p_country,p_height,p_weight,p_value,p_birthday,p_position,p_description,p_auction_bid,p_contract_expiry,p_wage,p_energy,p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,p_name FROM v_players WHERE c_id = ".$cid." && UNIX_TIMESTAMP(timestamp) >= ".$fetch_club['date'].";");
	$i = 2;
	while($fetch_players = mysql_fetch_assoc($get_players)) {
		$get_player_history = mysql_query("SELECT p_finishing,p_dribling,p_passing,p_tackling,p_marking,p_penalty_taking,p_bravery,p_creativity,p_determination,p_influence,p_morale,p_off_the_ball,p_acceleration,p_balance,p_fitness,p_jump,p_strength,p_stamina,UNIX_TIMESTAMP(timestamp) AS date FROM v_players WHERE p_id = ".$fetch_players['p_id']." ORDER BY TIMESTAMP DESC;");
		$s = 0;
		while($fetch_player = mysql_fetch_assoc($get_player_history)) {
			if($s == 0) {
				$date2 = $fetch_player['date'];
				$last = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
			}
			$date1 = $fetch_player['date'];
			$first = $fetch_player['p_finishing']+$fetch_player['p_dribling']+$fetch_player['p_passing']+$fetch_player['p_tackling']+$fetch_player['p_marking']+$fetch_player['p_penalty_taking']+$fetch_player['p_bravery']+$fetch_player['p_creativity']+$fetch_player['p_determination']+$fetch_player['p_influence']+$fetch_player['p_morale']+$fetch_player['p_off_the_ball']+$fetch_player['p_acceleration']+$fetch_player['p_balance']+$fetch_player['p_fitness']+$fetch_player['p_jump']+$fetch_player['p_strength']+$fetch_player['p_stamina'];
			$s++;
		}
		$desc = (strlen($fetch_players['p_description']) > 0 ? ' ('.ucfirst(strtolower($fetch_players['p_description'])).')' : '');
		list($years,$days) = $fetch->age($fetch_players['p_birthday']);
		$teknik = $fetch_players['p_finishing']+$fetch_players['p_dribling']+$fetch_players['p_passing']+$fetch_players['p_tackling']+$fetch_players['p_marking']+$fetch_players['p_penalty_taking'];
		$mentalitet = $fetch_players['p_bravery']+$fetch_players['p_creativity']+$fetch_players['p_determination']+$fetch_players['p_influence']+$fetch_players['p_morale']+$fetch_players['p_off_the_ball'];
		$fysik = $fetch_players['p_acceleration']+$fetch_players['p_balance']+$fetch_players['p_fitness']+$fetch_players['p_jump']+$fetch_players['p_strength']+$fetch_players['p_stamina'];
		$stats = $teknik+$mentalitet+$fysik;
		$stats_energy = $stats*($fetch_players['p_energy']/100);
		$ascension_step1 = ($last-$first);
		$ascension_step2 = ($date2-$date1);
		$ascension_step3 = ($ascension_step2 > 0 ? ($ascension_step2/86400) : 0);
		$ascension = ($ascension_step3 > 0 ? $ascension_step1/$ascension_step3 : '?');
		$future1 = (($value1-$fetch->age($fetch_players['p_birthday'],true)) > 0 ? (is_numeric($ascension) ? round(round(($value1-$fetch->age($fetch_players['p_birthday'],true))*30)*$ascension)+$stats : '?') : '-');
		$future2 = (($value2-$fetch->age($fetch_players['p_birthday'],true)) > 0 ? (is_numeric($ascension) ? round(round(($value2-$fetch->age($fetch_players['p_birthday'],true))*30)*$ascension)+$stats : '?') : '-');
		$future3 = (($value3-$fetch->age($fetch_players['p_birthday'],true)) > 0 ? (is_numeric($ascension) ? round(round(($value3-$fetch->age($fetch_players['p_birthday'],true))*30)*$ascension)+$stats : '?') : '-');
		$objPHPExcel->setActiveSheetIndex(1)
		            ->setCellValue('A'.$i, entity($fetch_players['p_name']).$desc)
		            ->setCellValue('B'.$i, entity($fetch->country($fetch_players['p_country'])))
		            ->setCellValue('C'.$i, entity($fetch->position($fetch_players['p_position'],'long')))
		            ->setCellValue('D'.$i, entity($fetch->leg($fetch_players['p_leg'])))
		            ->setCellValue('E'.$i, $years.' år'.($days > 0 ? ' og '.$days.' dag'.($days > 1 ? 'e' : '') : ''))
		            ->setCellValue('F'.$i, $fetch_players['p_value'].' C')
		            ->setCellValue('G'.$i, $fetch_players['p_wage'].' C/dag')
		            ->setCellValue('H'.$i, $stats)
		            ->setCellValue('I'.$i, round($stats_energy).' ('.$fetch_players['p_energy'].' %)')
		            ->setCellValue('J'.$i, $fetch->format($ascension,2).' (pr. dag)')
		            ->setCellValue('K'.$i, $future1)
		            ->setCellValue('L'.$i, $future2)
		            ->setCellValue('M'.$i, $future3)
		            ->setCellValue('N'.$i, $fetch->format($fetch->pension($fetch_players['p_birthday']),2).' %');
		$i++;
	}
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Spillerinformation');
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="export_'.$cid.'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output'); 
	exit;
}
?>