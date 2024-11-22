<?php 
	include("includes/connection.php");
	include("language/app_language.php");
 	include("includes/function.php");
 	include("smtp_email.php"); 

 	$mysqli->set_charset('utf8mb4');
	 
    date_default_timezone_set("Asia/Kolkata");
    
    $sql="DELETE FROM willdev_redeemed WHERE expiration < CURDATE();";
	$res=mysqli_query($mysqli,$sql);
    ?>