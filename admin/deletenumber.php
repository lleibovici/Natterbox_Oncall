<?php
$custname = $_REQUEST['recname'];

$db = new PDO('sqlite:../oncall.db');
//$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$sql = "DELETE FROM oncall WHERE engineer='" . $custname . "'";
$db->exec($sql);
$db = null;
