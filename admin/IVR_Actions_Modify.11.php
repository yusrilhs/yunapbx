<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function Extensions_Action_Modify() {
	session_start();
	$session = &$_SESSION['IVR_Action_Modify_play_sound'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	if (@$_REQUEST['submit'] == 'save') {
		$Action = formdata_from_post();
		$Errors = formdata_validate($Action);

		if (count($Errors) == 0) {
			$id = formdata_save($Action);
			header("Location: IVR_Actions.php?PK_Menu={$Action['FK_Menu']}&hilight={$id}");
			die();
		}

	} elseif (@$_REQUEST['PK_Action'] != "") {
		$Action = formdata_from_db($_REQUEST['PK_Action']);

	} else {
		$Action = formdata_from_default();
	}

	$smarty->assign('Action', $Action);

	return $smarty->fetch('IVR_Actions_Modify.play_sound.tpl');
}

function formdata_from_db($id) {
	$query  = "SELECT * FROM IVR_Actions WHERE PK_Action = '$id' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data   = mysql_fetch_assoc($result);

	$query  = "SELECT * FROM IVR_Action_Params WHERE FK_Action = '$id'";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$data['Param'][$row['Name']] = $row['Value'];
	}

	return $data;
}

function formdata_from_default() {
	$data = array();

	$data['FK_Menu'] = $_REQUEST['FK_Menu'];

	return $data;
}

function formdata_from_post() {
	return $_REQUEST;
}

function formdata_save($data) {
	if ($data['PK_Action'] == "") {
		$query  = "SELECT COUNT(*) FROM IVR_Actions WHERE FK_Menu={$data['FK_Menu']}";
		$result = mysql_query($query) or die(mysql_error().$query);
		$row    = mysql_fetch_row($result);
		$data['Order'] = $row[0]+1;

		$query = "INSERT INTO IVR_Actions (FK_Menu, `Order`, Type) VALUES({$data['FK_Menu']}, {$data['Order']}, 'play_sound')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Action'] = mysql_insert_id();
	}

	$query = "DELETE FROM IVR_Action_Params WHERE FK_Action = {$data['PK_Action']}";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Param'])) {
		foreach ($data['Param'] as $Name => $Value) {
			$query = "
				INSERT INTO
					IVR_Action_Params
				SET
					`Name`      = '".mysql_real_escape_string($Name)."',
					`Value`     = '".mysql_real_escape_string($Value)."',
					`FK_Action` = {$data['PK_Action']}
			";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	return $data['PK_Action'];
}

function formdata_validate($data) {
	$errors = array();

	return $errors;
}
admin_run('Extensions_Action_Modify', 'Admin.tpl');
?>