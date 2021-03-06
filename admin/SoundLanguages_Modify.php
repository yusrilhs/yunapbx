<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundLanguages_Modify() {
    
    $session = &$_SESSION['Templates_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (@$_REQUEST['submit'] == 'save') {
        $SoundLanguage = formdata_from_post();
        $Errors = formdata_validate($Rule);

        if (count($Errors) == 0) {
            if ($SoundLanguage['PK_SoundLanguage'] == '') {
                $id = formdata_save($SoundLanguage);
                header("Location: SoundLanguages_List.php?msg=CREATE_LANGUAGE&hilight={$id}");
                die();
            } else {
                $id = formdata_save($SoundLanguage);
                header("Location: SoundLanguages_List.php?msg=MODIFY_LANGUAGE&hilight={$id}");
                die();
            }
        }
    } elseif ($_REQUEST['PK_SoundLanguage'] != "") {
        $SoundLanguage = formdata_from_db($_REQUEST['PK_SoundLanguage']);
    }

    $smarty->assign('SoundLanguage', $SoundLanguage);
    $smarty->assign('Message', $Message);

    return $smarty->fetch('SoundLanguages_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $query = "SELECT * FROM SoundLanguages WHERE PK_SoundLanguage = $id	LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if (empty($data['PK_SoundLanguage'])) {
        $query = "INSERT INTO SoundLanguages(Type) VALUES('User')";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $data['PK_SoundLanguage'] = $db->lastInsertId();
    }

    // Update 'SoundFolders'
    $query = "
		UPDATE
			SoundLanguages
		SET
			Name        = '" . $mysqli->real_escape_string($data['Name']) . "'
		WHERE
			PK_SoundLanguage = " . $mysqli->real_escape_string($data['PK_SoundLanguage']) . "
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    return $data['PK_SoundLanguage'];
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

admin_run('SoundLanguages_Modify', 'Admin.tpl');
?>
