<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function TimeFrames() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['TimeFrames'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init Message
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // If requested, create new timeframe
    if (isset($_POST['sumbit'])) {
        $data = $_POST;

        if (strlen($data['Name']) < 1 || strlen($data['Name']) > 30) {
            $errors['Name'] = true;
        }

        if (count($errors) == 0) {
            $query = "INSERT INTO Timeframes(Name) VALUES('" . $mysqli->real_escape_string($_POST['Name']) . "')";
            $db->query($query) or die(print_r($db->errorInfo(), true));

            $PK_Timeframe = $db->lastInsertId();
            header("Location: TimeFrames_Modify.php?msg=CREATE_TIMEFRAME&FK_Timeframe={$PK_Timeframe}");
            die();
        }
    }

    // Init table fields (Timeframes)
    $Timeframes = array();
    $query = "
		SELECT
			PK_Timeframe AS _PK_,
			Name         AS Name
		FROM
			Timeframes
		WHERE
			FK_Extension = 0
		ORDER BY Name
	";

    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Timeframes[] = $row;
    }

    $smarty->assign('Timeframes', $Timeframes);
    $smarty->assign('Errors', $errors);
    $smarty->assign('Message', $Message);

    return $smarty->fetch('TimeFrames.tpl');
}

admin_run('TimeFrames', 'Admin.tpl');
