<?php
include "/etc/yunapbx.php";

$mysqli = new mysqli($conf['database']['host'], $conf['database']['user'], $conf['database']['password']);

if ($mysqli->connect_errno) {
    exit();
}

if(!$mysqli->select_db($conf['database']['database'])) {
    $db->query("CREATE DATABASE " . $conf['database']['database']);
    exec("/usr/bin/mysql -h" . $conf['database']['host'] . " --user=" . $conf['database']['user'] . " --password=\"" . $conf['database']['password'] . "\" " . $conf['database']['database'] . " < " . dirname(__FILE__) . "/sql-full/schema.sql", $output=array(), $worked);
    exec("/usr/bin/mysql -h" . $conf['database']['host'] . " --user=" . $conf['database']['user'] . " --password=\"" . $conf['database']['password'] . "\" " . $conf['database']['database'] . " < " . dirname(__FILE__) . "/sql-full/data.sql", $output=array(), $worked);
    $mysqli->select_db($conf['database']['database']);
}

$result_schema = $db->query("SELECT Value FROM Version WHERE Name='schema'");
if($row_schema = $result_schema->fetch(PDO::FETCH_ASSOC)) {
    $version_schema = $row_schema["value"];
    
}

$result_data = $db->query("SELECT Value FROM Version WHERE Name='data'");
if($row_data = $result_schema->fetch(PDO::FETCH_ASSOC)) {
    $version_data = $row_data["value"];
    
}

$mysqli->close();

?>