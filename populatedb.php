<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 07/09/2015
 * Time: 12:18
 */
date_default_timezone_set('Europe/London');
$dbfilename = './oncall.db';
if (file_exists($dbfilename)) {
    $db = new PDO("sqlite:$dbfilename");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} else {
    $db = new PDO("sqlite:$dbfilename");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $db->exec("create table oncall(engineer VARCHAR(64) , phonenumber VARCHAR (32), oncall SMALLINT )");
}
$db->exec("DELETE FROM oncall");
$db->exec('INSERT INTO oncall VALUES ("Engineer 1","+4423456789",1)');
$db->exec('INSERT INTO oncall VALUES ("Engineer 2","+4419876543",0)');
$res=$db->query("SELECT * FROM oncall");
while ($row =  $res->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
