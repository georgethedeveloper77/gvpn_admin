<?php
include_once("../database/config_DB.php");

$cookie_name = "_e_ll";
if(!isset($_COOKIE[$cookie_name])){
header("Location:../login.php");
} else {
    $query = $DBcon->query("SELECT * FROM admin WHERE userName='".$_COOKIE[$cookie_name]."'");
    $count = $query->num_rows;
    if($count < 1){
    header("Location:../login.php");
    }
}

//Add Servers
if(isset($_POST['addServer'])){
    $server = $_POST['server_option'];
    $serverName = $_POST['serverName'];
    $country =  $_POST['country'];
    $exploded =  explode("\n",$_POST['ovpn']);
    $ovpn = '';
    for($i = 0;$i < sizeof($exploded);$i++){
        if(strcmp($exploded[$i][0],"#")!= 0){
               $ovpn =$ovpn.$exploded[$i].'\n';
        }
    }
    
    $qryCountry = $DBcon->query("SELECT iso FROM willdev_country WHERE nicename='$country' LIMIT 1");
    $rowCountry = $qryCountry->fetch_array();
    $iso = strtolower($rowCountry['iso']);
    $hostName = $_SERVER['HTTP_HOST']; 
    
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    
    $flag_url = $protocol.'://'.$hostName.'/flag/'.$iso.".png";
    
    if ($server == "\61") { $vpnUsername = $_POST["\166\160\156\125\163\145\162\x6e\x61\155\x65"] . "\x40\x6e\141\155\145\143\150\145\141\160"; }
    else
        $vpnUsername = $_POST['vpnUsername'];
    
    $vpnPassword = $_POST['vpnPassword'];
    if(strcmp($_POST['isFree'],"free") == 0){
        $isFree = 1;
    }else{
        $isFree = 0;
    }
    if($query = $DBcon->query("INSERT INTO willdev_servers(serverName,server,country,flag_url,ovpnConfiguration,vpnUserName,vpnPassword,isFree) VALUES('".$serverName."','".$server."','".$country."','".$flag_url."','".$ovpn."','".$vpnUsername."','".$vpnPassword."',".$isFree.")")){
        header('Location:../home.php?status=success&message=Server added succesfully');
    }else{
        // //echo "And here";
        //echo $DBcon->error;
        header('Location:../add_willdev_servers.php?status=error&message=Error can\'t add server');
    }
}

//Change admob
if(isset($_POST['editAdmob'])){
    $admobID =  $_POST['admobID'];
    $interstitialID =  $_POST['interstitialID'];
    $bannerID =  $_POST['bannerID'];
    $nativeID =  $_POST['nativeID'];
    if($query = $DBcon->query("UPDATE `willdev_admobconfig` SET `admobID` = '".$admobID."', `interstitialID` = '".$interstitialID."' , `bannerID` = '".$bannerID."' , `nativeID` = '".$nativeID."' WHERE `willdev_admobconfig`.`id` = 1")){
        header('Location:../index.php?status=success&message=Information changed succesfully');
    }else{
        //echo $DBcon->error;

        header('Location:../manage_admob?status=error&message=Error can\'t change information');
    }
}

//Change edit servers
if(isset($_POST['editServer'])){
    $id =  $_POST['id'];
    $server = $_POST['server_option'];
    $serverName =  $_POST['serverName'];
    $country =  $_POST['country'];
    $exploded =  explode("\n",$_POST['ovpn']);
    $ovpn = '';
    for($i = 0;$i < sizeof($exploded);$i++){
        if(strcmp($exploded[$i][0],"#")!= 0){
               $ovpn =$ovpn.$exploded[$i].'\n';
        }
    }
    
    $qryCountry = $DBcon->query("SELECT iso FROM willdev_country WHERE nicename='$country' LIMIT 1");
    $rowCountry = $qryCountry->fetch_array();
    $iso = strtolower($rowCountry['iso']);
    $hostName = $_SERVER['HTTP_HOST']; 
    
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    
    $flag_url = $protocol.'://'.$hostName.'/flag/'.$iso.".png";
    
 if ($server == "\x31") { $vpnUsername = $_POST["\166\160\156\x55\x73\145\162\156\141\155\x65"] . ""; }
    else
        $vpnUsername = $_POST['vpnUsername'];
    
    $vpnPassword =  $_POST['vpnPassword'];
    if(strcmp($_POST['isFree'],"free") == 0){
        $isFree = 1;
    }else{
        $isFree = 0;
    }
    if($query = $DBcon->query("UPDATE willdev_servers SET serverName = '".$serverName."', server = '".$server."', country = '".$country."', flag_url = '".$flag_url."',ovpnConfiguration = '".$ovpn."',vpnUserName =  '".$vpnUsername."', vpnPassword = '".$vpnPassword."',isFree = ".$isFree." WHERE id = ".$id)){
        header('Location:../home.php?status=success&message=Server edited succesfully');
    }else{
        // echo "And here";
        // echo $DBcon->error;
        header('Location:../add_willdev_servers.php?status=error&message=Error can\'t edit server');
    }
}

//Change password
if(isset($_POST['changePassword'])){
    $previousPassword =  $_POST['previousPassword'];
    $newPassword =  $_POST['newPassword'];
    $confirmPassword =  $_POST['confirmPassword'];
    if(strcmp($newPassword,$confirmPassword)==0){
        $query = $DBcon->query("SELECT * FROM admin WHERE password = '".$previousPassword."'");
        $row=$query->fetch_array();
        $count = $query->num_rows; // if userName/password are correct returns must be 1 row
            if ($count==1) {
            if($query = $DBcon->query("UPDATE `admin` SET `password` = '".$newPassword."' WHERE `admin`.`id` = 1")){
                header('Location:../index.php?status=success&message=Password changed succesfully');
            }else{
        // echo $DBcon->error;

                header('Location:../change_password.php?status=error&message=Error can\'t change information');
            }
            }else{
        //echo $DBcon->error;

                header('Location:../change_password.php?status=error&message=Incorrect previous password.');
            }
    }else{
        //echo $DBcon->error;

        header('Location:../change_password.php?status=error&message=New password don\'t match.');
    }
}

//Add Adss
if(isset($_POST['addAds']))
{
    $admobID =  $_POST['admobID'];
    $bannerID =  $_POST['bannerID'];
	$interstitialID =  $_POST['interstitialID'];
	$nativeID =  $_POST['nativeID'];
	$adType =  $_POST['adType'];
    if(strcmp($_POST['activeAd'],"1") == 0)
	{
        $activeAd = 1;
    }
	else
	{
        $activeAd = 0;
    }
	
    if($query = $DBcon->query("INSERT INTO willdev_admobconfig(admobID, bannerID, interstitialID, nativeID, adType, activeAd) VALUES('".$admobID."','".$bannerID."','".$interstitialID."','".$nativeID."','".$adType."',".$activeAd.")"))
	{
        header('Location:../index.php?status=success&message=Ads config added succesfully');
    }
	else
	{
        header('Location:../add_ads.php.php?status=error&message=Error can\'t add ads config');
    }
}


//** Start Subscription

//Edit subscription
if(isset($_POST['editSubscription'])){
    $id =  $_POST['id'];
    $name = $_POST['name'];
    $product_id = $_POST['product_id'];
    $price =  $_POST['price'];
    $currency =  $_POST['currency'];
    $description =  $_POST['description'];
    $status =  $_POST['status'];

    if($query = $DBcon->query("UPDATE willdev_subscription SET name='" .$name. "', product_id='" .$product_id. "', price= '" .$price. "', currency= '" .$currency. "', description= '" .$description. "', status= '" .$status. "' WHERE id=" .$id)){
        header('Location:../willdev_subscription.php');
    }else{
        // //echo "And here";
        //echo $DBcon->error;
        header('Location:../willdev_subscription.php?status=error');
    }

}

//Edit subscription
if(isset($_GET['deleteSubscription'])){
    $id =  $_GET['deleteSubscription'];

    if($query = $DBcon->query("DELETE FROM willdev_subscription WHERE id=" .$id)){
        header('Location:../willdev_subscription.php');
    }else{
        // //echo "And here";
        //echo $DBcon->error;
        header('Location:../willdev_subscription.php?status=error');
    }

}


//** End Subscription

//Change edit ads
if(isset($_POST['editAds']))
{
    $id =  $_POST['id'];
    $admobID =  $_POST['admobID'];
    $bannerID =  $_POST['bannerID'];
    $interstitialID =  $_POST['interstitialID'];
    $nativeID =  $_POST['nativeID'];
    $rewardID =  $_POST['rewardID'];
	$adType =  $_POST['adType'];
    if(strcmp($_POST['activeAd'],"1") == 0)
	{
        $activeAd = 1;
    }
	else
	{
        $activeAd = 0;
    }
	
    if($query = $DBcon->query("UPDATE willdev_admobconfig SET admobID = '".$admobID."',bannerID = '".$bannerID."',interstitialID = '".$interstitialID."',nativeID =  '".$nativeID."', rewardID =  '".$rewardID."', adType = '".$adType."',activeAd = ".$activeAd." WHERE id = ".$id))
	{
		if ($activeAd == 1)
		{
			if ($query = $DBcon->query("UPDATE willdev_admobconfig SET activeAd = 0 WHERE id != ".$id));
			{
				header('Location:../index.php?status=success&message=Ads config edited succesfully');
			}
		}
		{
			header('Location:../index.php?status=success&message=Ads config edited succesfully');
		}
    }
	else
	{
        header('Location:../add_ads.php?status=error&message=Error can\'t edit ads config');
    }
}
?>