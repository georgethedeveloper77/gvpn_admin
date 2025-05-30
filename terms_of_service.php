<?php 
	
	$page_title="Terms of Service";
	include("includes/connection.php");
?>

<!DOCTYPE html>
<html> 
<head>
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width"> 
	<title><?php echo (isset($page_title)) ? $page_title.' | ' : '' ?><?php echo APP_NAME;?></title>
	<style type="text/css">
		body{
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; padding:1em;
		}
		.header{
			border-bottom: 2px solid #000;
			box-shadow: 0px 5px 10px 0px #ddd;
			padding: 10px 20px;
		}
		.header img{
			width: 80px;height: 80px;float: left;margin-right: 20px
		}
		.header h1{
			padding: 23px;margin: 0px
		}
		@media screen and (max-width: 768px) {

			.header img{
				width: 60px;
				height: 60px;
			}
			.header > h1 {
				font-size: 20px;
			}
		}
	</style>
</head> 
<body>

<!-- <div class="header">
	<img src="images/<?php echo APP_LOGO;?>" alt="app logo"/>
	<h1><?php echo APP_NAME;?></h1>	
</div> -->

<div style="clear: both;"></div>

<h2>Terms of Service</h2>


<?php
	$query = "SELECT value FROM willdev_proper_settings where setting='app_terms_of_service'";
    $result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_assoc($result);

    echo htmlspecialchars_decode(stripslashes($row["value"]));
?>

</body>
</html>