<?php

$users_res=mysqli_query($mysqli,'SELECT * FROM willdev_users WHERE id='.$user_id.'');
$users_res_row=mysqli_fetch_assoc($users_res);

$users_rewards_qry="SELECT * FROM willdev_users_rewards_activity
LEFT JOIN willdev_users ON willdev_users_rewards_activity.`user_id`=willdev_users.`id`
WHERE willdev_users_rewards_activity.`status`='1' AND willdev_users_rewards_activity.`user_id`='".$user_id."'
ORDER BY willdev_users_rewards_activity.`id` DESC";  

$users_rewards_result=mysqli_query($mysqli,$users_rewards_qry);


function get_video_info($video_id,$field_name) 
{
  global $mysqli;

  $qry_video="SELECT * FROM willdev_video WHERE id='".$video_id."' AND status='1'";
  $query1=mysqli_query($mysqli,$qry_video);
  $row_video = mysqli_fetch_array($query1);

  $num_rows1 = mysqli_num_rows($query1);

  if ($num_rows1 > 0)
  {     
    return $row_video[$field_name];
  }
  else
  {
    return "";
  }
} 

function get_user_info($user_id,$field_name) 
{
  global $mysqli;

  $qry_user="SELECT * FROM willdev_users WHERE id='".$user_id."' AND status='1'";
  $query1=mysqli_query($mysqli,$qry_user);
  $row_user = mysqli_fetch_array($query1);

  $num_rows1 = mysqli_num_rows($query1);

  if ($num_rows1 > 0)
  {     
    return $row_user[$field_name];
  }
  else
  {
    return "";
  }
}

function getLastActiveLog($user_id){
  global $mysqli;

  $sql="SELECT * FROM willdev_active_log WHERE `user_id`='$user_id'";
  $res=mysqli_query($mysqli, $sql);

  if(mysqli_num_rows($res) == 0){
    echo 'no available';
  }
  else{

    $row=mysqli_fetch_assoc($res);
    return calculate_time_span($row['date_time'],true); 
  }
} 


$settings_qry="SELECT * FROM willdev_settings where id='1'";
$settings_result=mysqli_query($mysqli,$settings_qry);
$settings_row=mysqli_fetch_assoc($settings_result);

$qry_video="SELECT COUNT(*) as num FROM willdev_video WHERE user_id='".$user_id."'";
$total_video = mysqli_fetch_array(mysqli_query($mysqli,$qry_video));
$total_video = $total_video['num'];

$sql_img="SELECT COUNT(*) as num FROM willdev_img_status WHERE user_id='".$user_id."' AND `status_type`='image'";
$total_img = mysqli_fetch_array(mysqli_query($mysqli,$sql_img));
$total_img = $total_img['num'];

$sql_gif="SELECT COUNT(*) as num FROM willdev_img_status WHERE user_id='".$user_id."' AND `status_type`='gif'";
$total_gif = mysqli_fetch_array(mysqli_query($mysqli,$sql_gif));
$total_gif = $total_gif['num'];

$sql_quote="SELECT COUNT(*) as num FROM willdev_quotes WHERE user_id='$user_id'";
$total_quote = mysqli_fetch_array(mysqli_query($mysqli,$sql_quote));
$total_quote = $total_quote['num'];


$qry_users_paid="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
WHERE willdev_users_redeem.`user_id`='".$user_id."' AND willdev_users_redeem.status = 1";
$total_paid = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_paid));
$total_paid = $total_paid['num'];

$qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
WHERE willdev_users_redeem.`user_id`='".$user_id."' AND willdev_users_redeem.status = 0";
$total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
$total_pending = $total_pending['num']; 
?>

<style type="text/css">

  /* Extra small devices (phones, 600px and down) */
  @media only screen and (max-width: 600px) {
    .top, #historyModal .top{
      position: relative !important;
      padding: 10px !important;
    }
    #historyModal .modal-body{
      padding: 0px;
    }
  }

  /* Small devices (portrait tablets and large phones, 600px and up) */
  @media only screen and (min-width: 600px) {
    .top{
      position: relative;
      padding: 10px;
    }
  } 
  
</style>


<div class="col-md-12 mr_bottom20">
  <?php
  if(isset($_GET['redirect'])){
    echo '<a href="'.$_GET['redirect'].'"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
  }
  else{
    echo '<a href="manage_users.php"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
  }
  ?>
  <div class="card mr_bottom20 mr_top10">
    <div class="page_title_block user_dashboard_item" style="background-color: #333;">
      <div class="user_dashboard_mr_bottom">
        <div class="col-md-12"> <br>
          <span class="badge badge-success badge-icon">
            <div class="user_profile_img">

              <?php 
              if($users_res_row['user_type']=='Google'){
                echo '<img src="assets/images/google-logo.png" style="top: 30px;left: 60px;" class="social_img">';
              }
              else if($users_res_row['user_type']=='Facebook'){
                echo '<img src="assets/images/facebook-icon.png" style="top: 30px;left: 60px;" class="social_img">';
              }
              ?>
              <?php if(isset($_GET['user_id']) and $users_res_row['user_image']!="" and file_exists('images/'.$users_res_row['user_image'])) {?>
                <img type="image" src="images/<?php echo $users_res_row['user_image'];?>" alt="image" style=""/>
              <?php }else{?>  
                <img type="image" src="assets/images/user_photo.png" alt="image"/>
              <?php } ?>           

            </div>
            <span style="font-size: 14px;"><?php echo $users_res_row['name'];?>
              <?php 
              if($users_res_row['is_verified']==1){
                echo '<img src="assets/images/verification_150.png" style="border: none;width: 15px;height: 15px;vertical-align: sub">';
              }
              ?>
            </span>
          </span> 
          <span class="badge badge-success badge-icon">
            <i class="fa fa-envelope fa-2x" aria-hidden="true"></i>
            <span style="font-size: 14px;"><?php echo $users_res_row['email'];?></span>
          </span> 

          <span class="badge badge-success badge-icon">
            <strong style="font-size: 14px;">Registered On</strong>
            <span style="font-size: 14px;"><?php echo ($users_res_row['registration_on']!='0') ? date('d-m-Y h:i A',$users_res_row['registration_on']) : 'not available';?></span>
          </span>
          <span class="badge badge-success badge-icon">
            <strong style="font-size: 14px;">Last Activity On:</strong>
            <span style="font-size: 14px;text-transform: lowercase;"><?php echo getLastActiveLog($users_res_row['id'])?></span>
          </span>
          <hr/>
      </div>
    </div>
    

<div class="col-lg-3 col-md-6 col-sm-6"> <a href="manage_user_history_pending_points.php?user_id=<?php echo $users_res_row['id'];?>&redirect=<?=$_GET['redirect']?>" class="card card-banner card-orange-light">
  <div class="card-body"> <i class="icon fa fa-clock-o fa-4x"></i>
    <div class="content">
      <div class="title">Pending Points</div>
      <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat(get_total_points($users_res_row['id']));?></div>
    </div>
  </div>
</a> 
</div>
<div class="col-lg-3 col-md-6 col-sm-6 mr_bot60"> <a href="javascript::void();" class="card card-banner card-yellow-light">
  <div class="card-body"> <i class="icon fa fa-money fa-4x"></i>
    <div class="content">
      <div class="title">Pending</div>
      <div class="value"><span class="sign"></span><?php echo $total_pending ? thousandsNumberFormat($total_pending) : '0';?><span class="sign"><?php echo $settings_row['redeem_currency'];?></span></div>
    </div>
  </div>
</a> 
</div>
<div class="col-lg-3 col-md-6 col-sm-6 mr_bot60"> <a href="javascript::void();" class="card card-banner card-blue-light">
  <div class="card-body"> <i class="icon fa fa-money fa-4x"></i>
    <div class="content">
      <div class="title">Total Paid</div>
      <div class="value"><span class="sign"></span><?php echo $total_paid ? thousandsNumberFormat($total_paid) : '0';?><span class="sign"><?php echo $settings_row['redeem_currency'];?></span></div>
    </div>
  </div>
</a> 
</div>  
</div>
<div class="user_dashboard_info">
  <ul>
    <li><a href="manage_user_history.php?user_id=<?php echo $users_res_row['id'];?>&redirect=<?=$_GET['redirect']?>" <?php if($currentFile=="manage_user_history.php"){ echo 'style="color: #1ee92b;"'; }?>>Edit Info</a></li>      
    <li><a href="manage_user_history_withdrawal.php?user_id=<?php echo $users_res_row['id'];?>&redirect=<?=$_GET['redirect']?>" <?php if($currentFile=="manage_user_history_withdrawal.php"){ echo 'style="color: #1ee92b;"'; }?>>Withdrawal</a></li>
    <li><a href="manage_user_history_total_points.php?user_id=<?php echo $users_res_row['id'];?>&redirect=<?=$_GET['redirect']?>" <?php if($currentFile=="manage_user_history_total_points.php"){ echo 'style="color: #1ee92b;"'; }?>>All Points History</a></li>
  </ul>
</div>
</div>
</div>