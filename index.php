<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 07/09/2015
 * Time: 10:19
 */
$twentyfour = '0333150xxxu';
$extended = '0333150yyyy';
$eight2eight = '0333150zzzz';

if (isset($_GET['callednumber']) && $_GET['callednumber'] != '') {
    $callednumber = trim($_GET['callednumber']);
    date_default_timezone_set('Europe/London');
    $dbfilename = './oncall.db';
    if (file_exists($dbfilename)) {
        $db = new PDO("sqlite:$dbfilename");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } else {
        $db = new PDO("sqlite:$dbfilename");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $db->exec("create table oncall(engineer VARCHAR(64) , phonenumber VARCHAR (32), oncall SMALLINT )");
        $db->exec("CREATE TABLE incomingnumbers(number VARCHAR(32) PRIMARY KEY NOT NULL,team VARCHAR(32) NOT NULL,support_cat VARCHAR(32) NOT NULL)");

    }

    $sqli = "SELECT * FROM incomingnumbers WHERE number='$callednumber'";
    $resi = $db->query($sqli);
    $rowi = $resi->fetch(PDO::FETCH_ASSOC);
    $support_cat = $rowi['support_cat'];
    $team = $rowi['team'];

    include('bankholiday.php');
    $year = date('Y');
    $today = date('Y-m-d');

    $hols = calculateBankHolidays($year);
    $bankhol = in_array($today, $hols);
    $status = '';

    if ($support_cat == 'twentyfour') {
        //echo "Got 24\r\n";
        if ($bankhol) {
            $status = 'call engineer';
        } else {
            if (officeHours()) {
                $status = 'call support';
            } else {
                $status = 'call engineer';
            }
        }
    }
    if ($support_cat == 'extended') {
        //echo "Got Extended\r\n";
        if ($bankhol) {
            $status = 'out of hours';
        } else {
            if (officeHours()) {
                $status = 'call support';
            } else {
                if (date('N') < 6) {
                    if (date('G:i') >= '06:00' && date('G:i') <= '22:00') {
                        $status = 'call engineer';
                    } else {
                        $status = 'out of hours';
                    }
                } else {
                    if (date('G:i') >= '09:00' && date('G:i') <= '18:00') {
                        $status = 'call engineer';
                    } else {
                        $status = 'out of hours';
                    }
                }
            }
        }
    }
    if ($support_cat == 'eight2eight') {
        //echo "Got 8 to 8\r\n";
        if ($bankhol) {
            $status = 'out of hours';
        } else {
            if (officeHours()) {
                $status = 'call support';
            } else {
                if (date('G:i') >= '08:00' && date('G:i') <= '20:00') {
                    $status = 'call engineer';
                } else {
                    $status = 'out of hours';
                }
            }
        }
    }


    $oncallNumber = '';
    if ($status == 'call engineer') {


        $sql = "SELECT phonenumber FROM oncall WHERE oncall=1 AND team='$team'";
        $res = $db->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $oncallNumber = $row['phonenumber'];
    }

    header("Content-Type:text/xml");
    echo('<?xml version="1.0" encoding="UTF-8"?>');
    echo('<records>');
    echo('<record>');
    echo("<CalledNumber>$callednumber</CalledNumber>");
    echo("<Result>$status</Result>");
    echo("<OncallNumber>$oncallNumber</OncallNumber>");
//    echo("<sql>$sql</sql>");
    echo('</record>');
    echo('</records>');
}

function officeHours()
{
    $ret = false;
    if (date('N') < 6) {
        if (date('G:i') >= '08:00' && date('G:i') <= '18:00') {
            $ret = true;
        }
    }
    return $ret;
}