<?php


// include ("includes/validator.php");
// $DBhost = "localhost";
// $DBuser = "root";
// $DBpass = "";
// $DBname = "will_ten_update";

// include ("includes/validator.php");
$DBhost = "localhost";
$DBuser = "gvpn_user";
$DBpass = "m6p^m18!^4Yo";
$DBname = "gvpn_db";

$DBcon = new MySQLi($DBhost,$DBuser,$DBpass,$DBname);

if ($DBcon->connect_errno) {
    die("ERROR : -> ".$DBcon->connect_error);
}
?>

