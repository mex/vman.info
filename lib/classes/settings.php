<?php
class Settings {
	function settings($message = '') {
		global $lang;
		global $unique_id;
		$fetch_user_info = mysql_fetch_array(mysql_query("SELECT s_future1,s_future2,s_sponsor,s_employees,s_stadium_degradation,s_stadium_average FROM v_settings WHERE s_cookie_id = ".$unique_id.";"));
		$output = '
			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$lang['page']['settings'].'</h4>
				</div>
				<div class="portlet-content">
					'.(strlen($message) > 0 ? '<p class="message message-success message-closable">'.$message.'</p>' : '').'
					<form name="settingsform" action="/settings" method="post" class="form label-inline">
						<div class="field"><label for="future1">'.$lang['settings']['future_stats'].'</label> <input id="future1" name="future1" size="3" type="text" class="xxsmall" value="'.$fetch_user_info['s_future1'].'" /> '.$lang['player']['year'].' '.$lang['page']['and'].' <input id="future2" name="future2" size="3" type="text" class="xxsmall" value="'.$fetch_user_info['s_future2'].'" /> '.$lang['player']['year'].'
							<p class="field_help">'.$lang['settings']['future_stats_desc'].'.</p>
						</div>
						<div class="field"><label for="sponsor">'.$lang['settings']['sponsor'].' </label> <input id="sponsor" name="sponsor" size="50" type="text" class="medium" value="'.$fetch_user_info['s_sponsor'].'" />
							<p class="field_help">'.$lang['settings']['sponsor_desc'].'.</p>
						</div>
						<div class="field"><label for="employees">'.$lang['settings']['employees'].' </label> <input id="employees" name="employees" size="50" type="text" class="medium" value="'.$fetch_user_info['s_employees'].'" />
							<p class="field_help">'.$lang['settings']['employees_desc'].'.</p>
						</div>
						<div class="field"><label for="stadium_degration">'.$lang['settings']['stadium_average'].' </label> <input id="stadium_average" name="stadium_average" size="5" type="text" class="medium" value="'.$fetch_user_info['s_stadium_average'].'" />
							<p class="field_help">'.$lang['settings']['stadium_average_desc'].'.</p>
						</div>
						<div class="buttonrow">
							<input type="hidden" name="ref" value="'.$_SERVER['HTTP_REFERER'].'" />
							<button onclick="document.settingsform.submit();"><span>'.$lang['settings']['save_settings'].'</span></button>
						</div>
					</form>
				</div>
			</div>';
		return $output;
	}
	function view_notes($message = '') {
		global $lang;
		global $unique_id;
		$fetch_user_info = mysql_fetch_array(mysql_query("SELECT s_notes FROM v_settings WHERE s_cookie_id = ".$unique_id.";"));
		$output = '
			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$lang['page']['notes'].'</h4>
				</div>
				<div class="portlet-content">
					'.(strlen($message) > 0 ? '<p class="message message-success message-closable">'.$message.'</p>' : '').'
					<div style="float:right;"><button onclick="parent.location.href=\'/notes/edit\';"><a href="/notes/edit"><span>'.$lang['notes']['edit_notes'].'</span></a></button></div>
					'.(strlen($fetch_user_info['s_notes']) > 0 ? nl2br($fetch_user_info['s_notes']) : $lang['notes']['no_notes']).'
				</div>
			</div>';
		return $output;
	}
	function edit_notes() {
		global $lang;
		global $fetch_user_info;
		$output = '
			<div class="portlet x12">
				<div class="portlet-header">
					<h4>'.$lang['notes']['edit_notes'].'</h4>
				</div>
				<div class="portlet-content">
					<form name="notesform" action="/notes" method="post" class="form label-top">
						<div class="field">
							<textarea id="notes" name="notes" cols="200" rows="20" class="xxxxxlarge">'.$fetch_user_info['s_notes'].'</textarea>
						</div>
						<div class="buttonrow">
							<button onclick="document.notesform.submit();"><span>'.$lang['notes']['save_notes'].'</span></button>			
						</div>
					</form>
				</div>
			</div>';
		return $output;
	}
}
?>