<?php


// include ("includes/validator.php");
$DBhost = "db_hname";
$DBuser = "db_uname";
$DBpass = "db_password";
$DBname = "db_name";

$DBcon = new MySQLi($DBhost,$DBuser,$DBpass,$DBname);

if ($DBcon->connect_errno) {
    die("ERROR : -> ".$DBcon->connect_error);
}
?>
