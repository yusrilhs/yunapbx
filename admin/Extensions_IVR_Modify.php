<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_IVR_Modify() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['IVR_Options_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (@$_REQUEST['submit'] == 'save') {
        $Extension = formdata_from_post();
        $Errors = formdata_validate($Extension);

        if (count($Errors) == 0) {
            if ($Extension['PK_Extension'] != '') {
                $msg = 'MODIFY_IVR_EXTENSION';
            } else {
                $msg = 'ADD_IVR_EXTENSION';
            }
            $id = formdata_save($Extension);

            header("Location: Extensions_List.php?hilight={$id}&msg={$msg}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $Extension = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $Extension = formdata_from_default();
    }

    // Get available menus
    $Menus = array();
    $query = "SELECT PK_Menu, Name FROM IVR_Menus ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $menu = $row;

        $query2 = "SELECT * FROM IVR_Actions WHERE FK_Menu = '{$menu['PK_Menu']}' ORDER BY `Order`";
        $result2 = $db->query($query2) or die(print_r($db->errorInfo(), true));
        while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $action = $row2;

            $query3 = "SELECT * FROM IVR_Action_Params WHERE FK_Action = {$action['PK_Action']}";
            $result3 = $db->query($query3) or die(print_r($db->errorInfo(), true));
            while ($row3 = $result3->fetch(PDO::FETCH_ASSOC)) {
                $action['Param'][$row3['Name']] = $row3['Value'];
                $action['Var'][$row3['Name']] = $row3['Variable'];
            }

            $menu['Actions'][] = $action;
        }

        $Menus[] = $menu;
    }

    $smarty->assign('Menus', $Menus);
    $smarty->assign('Extension', $Extension);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_IVR_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $query = "
		SELECT
			*
		FROM
			Ext_IVR
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_IVR.PK_Extension
		WHERE
			Ext_IVR.PK_Extension = '$id'
		LIMIT 1
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);
    return $data;
}

function formdata_from_default() {
    $data = array();

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Type, Extension) VALUES('IVR', '" . $mysqli->real_escape_string($data['Extension']) . "')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Extension'] = $db->lastInsertId();

        $query = "INSERT INTO Ext_IVR(PK_Extension) VALUES({$data['PK_Extension']})";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }

    // Update 'Ext_IVR'
    $query = "
		UPDATE
			Ext_IVR
		SET
			FK_Menu   = " . $mysqli->real_escape_string($data['FK_Menu']) . ",
			FK_Action = " . $mysqli->real_escape_string($data['FK_Action']) . "
		WHERE
			PK_Extension       = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";

    $db->query($query) or die(print_r($db->errorInfo(), true));

    // Update 'IVRDial"
    $query = "UPDATE Extensions SET IVRDial = " . ($data['IVRDial'] == 1 ? '1' : '0') . " WHERE PK_Extension = {$data['PK_Extension']}";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    return $data['PK_Extension'];
}

function formdata_validate($data) {
    $db = DB::getInstance();
    $errors = array();

    if ($data['PK_Extension'] == '') {
        $create_new = true;
    }

    if ($create_new) {
        // Check if extension is empty
        if ($data['Extension'] == "") {
            $errors['Extension']['Invalid'] = true;
            // Check if Extension is numeric
        } elseif (intval($data['Extension']) . "" != $data['Extension']) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension is proper length
        } elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension in unique
        } else {
            $query = "SELECT Extension FROM Extensions WHERE Extension = '{$data['Extension']}' LIMIT 1";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            if ($result->num_rows > 0) {
                $errors['Extension']['Duplicate'] = true;
            }
        }
    }

    return $errors;
}

admin_run('Extensions_IVR_Modify', 'Admin.tpl');
?>