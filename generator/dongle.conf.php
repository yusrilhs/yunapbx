<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Dongles', Get_Dongles());

$out = $smarty->fetch('dongle.conf.tpl');
$fh = fopen('/etc/asterisk/dongle.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>
