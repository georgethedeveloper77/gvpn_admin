<?php 
include("includes/connection.php");
include_once("database/config_DB.php");

date_default_timezone_set("Asia/Kolkata");

    //Get file name
$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];

$requestUrl = $_SERVER["REQUEST_URI"];
$urlparts = Explode('/', $requestUrl);
$redirectUrl = $urlparts[count($urlparts) - 1];

$mysqli->set_charset("utf8mb4");   

$jsonObj= array();	
$query="SELECT one_connect FROM willdev_settings WHERE id='1'";
$sql = mysqli_query($DBcon,$query);
$data = mysqli_fetch_assoc($sql);
$oneConnect = $data['one_connect'];

?>

<!DOCTYPE html>
<html>
<head>
  <meta name="author" content="">
  <meta name="description" content="">
  <meta http-equiv="Content-Type"content="text/html;charset=UTF-8"/>
  <meta name="viewport"content="width=device-width, initial-scale=1.0">
  <title> <?php if(isset($page_title)){ echo $page_title.' | '.APP_NAME; }else{ echo APP_NAME; } ?></title>
  <link rel="icon" href="images/<?php echo APP_LOGO;?>" sizes="16x16">
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/flat-admin.css">

  <!-- Theme -->
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/theme/blue-sky.css">
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/theme/blue.css">
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/theme/red.css">
  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/css/theme/yellow.css">

  <link rel="stylesheet" type="text/css" href="https://will-vpn.willdev.in/assets/sweetalert/sweetalert.css">

  <script src="https://will-vpn.willdev.in/assets/ckeditor/ckeditor.js"></script>

  <?php 
  if(!empty($css_files)){
    foreach ($css_files as $key => $value) {
      echo '<link rel="stylesheet" type="text/css" href="'.$value.'">';
    }
  }

  if(!empty($js_files)){
    foreach ($js_files as $key => $value) {
      echo '<script type="text/javascript" src="'.$value.'"></script>';
    }

  }
  ?>

  <style type="text/css">
    .btn_edit, .btn_delete, .btn_cust{
      padding: 5px 10px !important;
      font-size: 12px !important; 
    }

    /*--------- for sweet alerts --------*/
    .sweet-alert h2 {
      font-size: 24px;
      line-height: 28px;
      font-weight: 500
    }
    .sweet-alert .lead{
      font-size: 18px; 
      font-weight: 400
    }
    .sweet-alert .btn{
      min-width: 70px !important;
      padding: 8px 12px !important;
      border: 0 !important;
      height: auto !important;
      margin: 0px 3px !important;
      box-shadow: none !important;
      font-size: 15px;
    }
    .sweet-alert .sa-icon {
      margin: 0 auto 15px auto !important;
    }

    .social_img{
      width: 20px !important;
      height: 20px !important;
      position: absolute;
      top: -11px;
      z-index: 1;
      left: 40px;
      margin:5px;
    }

    .control-label-help{
      color: red !important;
    }

    .dropdown-li{
      margin-bottom: 0px !important;
    }
    .cust-dropdown-container{
      background: #E7EDEE;
      display: none;
    }
    .cust-dropdown{
      list-style: none;
      background: #eee;
    }
    .cust-dropdown li a{
      padding: 8px 0px;
      width: 100%;
      display: block;
      color: #444;
      float: left;
      text-decoration: none;
      transition: all linear 0.2s;
      font-weight: 500;
    }
    .cust-dropdown li a:hover{
      color: #1ee92b;
    }

    .cust-dropdown li a.active{
      color: #1ee92b;
    }

  </style>


</head>

<body>

  <div class="loader"></div>

  <div class="app app-default">
     
<div class="app-container" >
