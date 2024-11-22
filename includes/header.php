<?php 
include("includes/connection.php");
include("includes/session_check.php");
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
  <link rel="icon" href="images/<?php echo APP_LOGO;?>" sizes="16x16">
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/flat-admin.css">

  <!-- Theme -->
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/theme/blue-sky.css">
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/theme/blue.css">
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/theme/red.css">
  <link rel="stylesheet" type="text/css" href="base_urls/assets/css/theme/yellow.css">

  <link rel="stylesheet" type="text/css" href="base_urls/assets/sweetalert/sweetalert.css">

  <script src="base_urls/assets/ckeditor/ckeditor.js"></script>

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
    <aside class="app-sidebar" style="background-color:#4E3FFF;" id="sidebar">
      <div class="sidebar-header"> <a class="sidebar-brand" href="home.php"><img src="images/<?php echo APP_LOGO;?>" alt="app logo" /></a>
        <button type="button" class="sidebar-toggle"> <i class="fa fa-times"></i> </button>
      </div>
      <div class="sidebar-menu">
            <ul class="sidebar-nav">
          <li <?php if($currentFile=="home.php"){?>class="active"  style="background-color:#1ee92b;"   <?php }?>> <a href="home.php">
            <div class="icon"> <i class="fa fa-home" aria-hidden="true"></i> </div>
            <div class="title">Dashboard</div>
          </a> 
        </li>

      <!-- New Vpn Side Bar-->

        <?php if($oneConnect == "0"){?>
        <li <?php if($currentFile=="add_willdev_servers.php"){?>class="active"<?php }?>> <a href="add_willdev_servers.php">
            <div class="icon"> <i class="fa fa-plus-circle" aria-hidden="true"></i> </div>
            <div class="title">Add Servers</div>
          </a> 
        </li>
        <?php }?>
        
        <?php if($oneConnect == "0"){?>
        <li <?php if($currentFile=="free_willdev_servers.php"){?>class="active"<?php }?>> <a href="free_willdev_servers.php">
            <div class="icon"> <i class="fa fa-tencent-weibo" aria-hidden="true"></i> </div>
            <div class="title">Free Servers</div>
          </a> 
        </li>
        <?php }?>

        <?php if($oneConnect == "0"){?>
        <li <?php if($currentFile=="pros_willdev_servers.php"){?>class="active"<?php }?>> <a href="pros_willdev_servers.php">
            <div class="icon"> <i class="fa fa-bolt" aria-hidden="true"></i> </div>
            <div class="title">Pro Servers</div>
          </a> 
        </li>
        <?php }?>



        <li <?php if($currentFile=="manage_users.php"){?>class="active"<?php }?>> <a href="manage_users.php">
            <div class="icon"> <i class="fa fa-user" aria-hidden="true"></i> </div>
            <div class="title">Users List</div>
          </a> 
        </li>

        <li <?php if($currentFile=="redemption_code.php" or $currentFile=="add_redemption.php" or $currentFile=="edit_redemption.php"){?>class="active"<?php }?>> <a href="redemption_code.php">
  <div class="icon"> <i class="fa fa-trophy" aria-hidden="true"></i> </div>
  <div class="title">Redeem Option</div>
</a> 
</li>
<li <?php if($currentFile=="ads_type.php?edit=2"){?>class="active"<?php }?>> <a href="ads_type.php?edit=2">
            <div class="icon"> <i class="fa fa-plus-square" aria-hidden="true"></i> </div>
            <div class="title">Facebook Ads</div>
          </a> 
        </li>

        <li <?php if($currentFile=="ads_type.php?edit=1"){?>class="active"<?php }?>> <a href="ads_type.php?edit=1">
            <div class="icon"> <i class="fa fa-plus-square" aria-hidden="true"></i> </div>
            <div class="title">Admob Ads</div>
          </a> 
        </li>
        

        <li <?php if($currentFile=="ads_type.php?edit=3"){?>class="active"<?php }?>> <a href="ads_type.php?edit=3">
            <div class="icon"> <i class="fa fa-plus-square" aria-hidden="true"></i> </div>
            <div class="title">StartApp Ads</div>
          </a> 
        </li>
        
                <li <?php if($currentFile=="ads_type.php?edit=4"){?>class="active"<?php }?>> <a href="ads_type.php?edit=4">
            <div class="icon"> <i class="fa fa-plus-square" aria-hidden="true"></i> </div>
            <div class="title">Unity Ads</div>
          </a> 
        </li>

                <li <?php if($currentFile=="ads_type.php?edit=5"){?>class="active"<?php }?>> <a href="ads_type.php?edit=5">
            <div class="icon"> <i class="fa fa-plus-square" aria-hidden="true"></i> </div>
            <div class="title">Appodeal Ads</div>
          </a> 
        </li>

       
        

        <!--new setting -->

        <li <?php if($currentFile=="smtp_settings.php"){?>class="active"<?php }?>> <a href="smtp_settings.php">
            <div class="icon"> <i class="fa fa-sun-o" aria-hidden="true"></i> </div>
            <div class="title">Smtp Settings</div>
          </a> 
        </li>

        <li <?php if($currentFile=="alert_popup.php"){?>class="active"<?php }?>> <a href="alert_popup.php">
            <div class="icon"> <i class="fa fa-wrench" aria-hidden="true"></i> </div>
            <div class="title">Alert Popup</div>
          </a> 
        </li>
        
        <li <?php if($currentFile=="subscription.php"){?>class="active"<?php }?>> <a href="subscription.php">
            <div class="icon"> <i class="fa fa-sliders" aria-hidden="true"></i> </div>
            <div class="title">Subscription</div>
          </a> 
        </li>

        <li <?php if($currentFile=="settings.php"){?>class="active"<?php }?>> <a href="settings.php">
            <div class="icon"> <i class="fa fa-cogs" aria-hidden="true"></i> </div>
            <div class="title">General Settings</div>
          </a> 
        </li>




 <li <?php if($currentFile=="manage_transaction.php"){?>class="active"<?php }?>> <a href="manage_transaction.php">
    <div class="icon"> <i class="fa fa-money" aria-hidden="true"></i> </div>
    <div class="title">Transaction</div>
  </a> 
</li> 
<li <?php if($currentFile=="manage_contact_list.php" or $currentFile=="contact_subject.php"){?>class="active"<?php }?>> <a href="manage_contact_list.php">
  <div class="icon"> <i class="fa fa-bug" aria-hidden="true"></i> </div>
  <div class="title">Contact List</div>
</a> 
</li>

<li <?php if($currentFile=="send_notification.php"){?>class="active"<?php }?>> <a href="send_notification.php">
  <div class="icon"> <i class="fa fa-bell" aria-hidden="true"></i> </div>
  <div class="title">Notification</div>
</a> 
</li>
<li <?php if($currentFile=="spinner.php" or $currentFile=="add_block.php" or $currentFile=="edit_block.php"){?>class="active"<?php }?>> <a href="spinner.php">
  <div class="icon"> <i class="fa fa-asterisk" aria-hidden="true"></i> </div>
  <div class="title">Lucky Wheel</div>
</a> 
</li>

<li <?php if($currentFile=="rewards_points.php" or $currentFile=="add_block.php" or $currentFile=="edit_block.php"){?>class="active"<?php }?>> <a href="rewards_points.php">
  <div class="icon"> <i class="fa fa-cog" aria-hidden="true"></i> </div>
  <div class="title">Reward Setting</div>
</a> 
</li>









</ul>
</div>

</aside>   
<div class="app-container" >
  <nav class="navbar navbar-default" id="navbar">
    <div class="container-fluid" >
      <div class="navbar-collapse collapse in" style="background-color:#4E3FFF;">
        <ul class="nav navbar-nav navbar-mobile">
          <li>
            <button type="button" class="sidebar-toggle"> <i class="fa fa-bars"></i> </button>
          </li>
          <li class="logo"> <a class="navbar-brand" href="#"><?php echo APP_NAME;?></a> </li>
          <li>
            <button type="button" class="navbar-toggle">
              <?php if(PROFILE_IMG){?>               
                <img class="profile-img" src="images/<?php echo PROFILE_IMG;?>">
              <?php }else{?>
                <img class="profile-img" src="assets/images/profile.png">
              <?php }?>

            </button>
          </li>
        </ul>
        <ul class="nav navbar-nav navbar-left" >
          <li class="navbar-title"><?php echo APP_NAME;?></li>

        </ul>
        <ul class="nav navbar-nav navbar-right">
          
          
          <li class="dropdown profile"> <a href="profile.php" class="dropdown-toggle" data-toggle="dropdown"> <?php if(PROFILE_IMG){?>               
            <img class="profile-img" src="images/<?php echo PROFILE_IMG;?>">
          <?php }else{?>
            <img class="profile-img" src="assets/images/profile.png">
          <?php }?>
          <div class="title">Profile</div>
        </a>
        <div class="dropdown-menu">
          <div class="profile-info">
            <h4 class="username">Admin</h4>
          </div>
          <ul class="action">
            <li><a href="profile.php">Profile</a></li>  
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</div>
</nav>