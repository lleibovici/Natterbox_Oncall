<?php
/**
 * Created by IntelliJ IDEA.
 * User: leo
 * Date: 07/09/2015
 * Time: 10:26
 */
include('bankholiday.php');

$hols = calculateBankHolidays(2016);
print_r($hols);
