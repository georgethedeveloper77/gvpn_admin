<?php
include_once("../database/config_DB.php");

//Handle Routes
$URL_LOCATION = "e2link.in";

//Login Route
if(isset($_POST['login'])){
    if(isset($_POST['userName'])&&isset($_POST['password'])){
        $userName = $_POST['userName'];
        $password = $_POST['password'];
        $query = $DBcon->query("SELECT * FROM admin WHERE userName='".$userName."' AND password = '".$password."'");
        $row=$query->fetch_array();
        $count = $query->num_rows; // if userName/password are correct returns must be 1 row
            if ($count==1) {
                echo "Here";
                setcookie("_e_ll", $row['userName'], time() + 7200, '/');
                header("Location:../index.php");
            } else{
                header("Location:../login.php?status=error&message=The provided information is wrong!");
            }
        
    }else{
        header("Location:../login.php?status=error&message=Check your database!");
    }
}

//Logout route
if(isset($_GET['logout'])){
setcookie("_e_ll", "test", time() - 7200, '/');
header("Location:../login.php");
}

//Check if one connect is enabled
if(isset($_GET['oneConnect'])){
    $jsonObj= array();	
    $query="SELECT one_connect, one_connect_key FROM willdev_settings WHERE id='1'";
	$sql = mysqli_query($DBcon,$query);
	$data = mysqli_fetch_assoc($sql);
	$jsonObj["one_connect"] = $data['one_connect'];
	$jsonObj["one_connect_key"] = $data['one_connect_key'];

	echo json_encode($jsonObj);
}

//Get free servers api
if(isset($_GET['frWillServer'])){
    //Check if one connect is enabled
    
    $query = $DBcon->query("SELECT * FROM willdev_servers WHERE isFree='1'");
    $servers_list = array();
    while($row=$query->fetch_array()){
        array_push($servers_list,$row); 
    }
    
    $json = json_encode($servers_list);
    $data = json_decode($json, true);
                            
    foreach ($data as $key => $entry) {
        $data[$key]['vpnUserName'] = simpleEncryption($data[$key]['vpnUserName']);
        $data[$key]['vpnPassword'] = simpleEncryption($data[$key]['vpnPassword']);
    }

    echo json_encode($data);
}

//Get pro servers api
if(isset($_GET['prWillServer'])){
    $query = $DBcon->query("SELECT * FROM willdev_servers WHERE isFree='0'");
    $servers_list = array();
    while($row=$query->fetch_array()){
        array_push($servers_list,$row); 
    }

    $json = json_encode($servers_list);
    $data = json_decode($json, true);
                            
    foreach ($data as $key => $entry) {
        $data[$key]['vpnUserName'] = simpleEncryption($data[$key]['vpnUserName']);
        $data[$key]['vpnPassword'] = simpleEncryption($data[$key]['vpnPassword']);
    }

    echo json_encode($data);
}



//Get pro servers api
if(isset($_GET['allWillAds'])){
    $query = $DBcon->query("SELECT * FROM willdev_admobconfig WHERE activeAd = 1");
    $servers_list = array();
    while($row=$query->fetch_array()){
        array_push($servers_list,$row); 
    }
    echo json_encode($servers_list);
}

//Get subscription
if(isset($_GET['get_subscription'])){
    
    $result = $DBcon->query("SELECT * FROM willdev_subscription");
    $arr = array();
    while($row=$result->fetch_array()){
        array_push($arr,$row); 
    }
    echo json_encode($arr);
}


function simpleEncryption($str)
{
$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&';
$result = array_reduce(str_split($str),
fn($carry, $item)=>$carry.=$item.$chars[rand(0,strlen($chars)-1)], '');
return strrev($result);
}

?>