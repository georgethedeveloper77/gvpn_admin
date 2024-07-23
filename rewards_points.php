<?php 

$page_title="Status Rewards Points";
$active_page="settings";

include("includes/header.php");

require("includes/function.php");
require("language/language.php");


$qry="SELECT * FROM willdev_settings WHERE id='1'";
$result=mysqli_query($mysqli,$qry);
$settings_row=mysqli_fetch_assoc($result);


if(isset($_POST['rewards_points_submit']))
{

  $data = array(
    'redeem_points'  =>  addslashes(trim($_POST['redeem_points'])),
    'redeem_money'  =>  addslashes(trim($_POST['redeem_money'])),
    'redeem_currency'  =>  addslashes(trim($_POST['redeem_currency'])),
    'minimum_redeem_points'  =>  addslashes(trim($_POST['minimum_redeem_points'])),
    'registration_reward'  =>  addslashes(trim($_POST['registration_reward'])),
    'app_refer_reward'  =>  addslashes(trim($_POST['app_refer_reward'])),

    'video_views'  =>  addslashes(trim($_POST['video_views'])),
    'video_add'  =>  addslashes(trim($_POST['video_add'])),
    'like_video_points'  =>  addslashes(trim($_POST['like_video_points'])),
    'download_video_points'  =>  addslashes(trim($_POST['download_video_points'])),
    'video_views_status'  =>  $_POST['video_views_status'] ? 'true' : 'false',
    'video_add_status'  =>  $_POST['video_add_status'] ? 'true' : 'false',
    'like_video_points_status'  =>  $_POST['like_video_points_status'] ? 'true' : 'false',
    'download_video_points_status'  =>  $_POST['download_video_points_status'] ? 'true' : 'false',
    'other_user_video_status'  =>  $_POST['other_user_video_status'] ? 'true' : 'false',
    'other_user_video_point'  =>  addslashes(trim($_POST['other_user_video_point'])),

    'image_add'  =>  addslashes(trim($_POST['image_add'])),
    'image_add_status'  =>  $_POST['image_add_status'] ? $_POST['image_add_status'] : 'false',

    'image_views'  =>  addslashes(trim($_POST['image_views'])),

    'other_user_image_point'  =>  addslashes(trim($_POST['other_user_image_point'])),
    'other_user_image_status'  =>  $_POST['other_user_image_status'] ? $_POST['other_user_image_status'] : 'false',

    'like_image_points'  =>  addslashes(trim($_POST['like_image_points'])),
    'like_image_points_status'  =>  $_POST['like_image_points_status'] ? $_POST['like_image_points_status'] : 'false',

    'download_image_points'  =>  addslashes(trim($_POST['download_image_points'])),
    'download_image_points_status'  =>  $_POST['download_image_points_status'] ? $_POST['download_image_points_status'] : 'false',

    'gif_add'  =>  addslashes(trim($_POST['gif_add'])),
    'gif_add_status'  =>  $_POST['gif_add_status'] ? $_POST['gif_add_status'] : 'false',

    'gif_views'  =>  addslashes(trim($_POST['gif_views'])),

    'other_user_gif_point'  =>  addslashes(trim($_POST['other_user_gif_point'])),
    'other_user_gif_status'  =>  $_POST['other_user_gif_status'] ? $_POST['other_user_gif_status'] : 'false',

    'like_gif_points'  =>  addslashes(trim($_POST['like_gif_points'])),
    'like_gif_points_status'  =>  $_POST['like_gif_points_status'] ? $_POST['like_gif_points_status'] : 'false',

    'download_gif_points'  =>  addslashes(trim($_POST['download_gif_points'])),
    'download_gif_points_status'  =>  $_POST['download_gif_points_status'] ? $_POST['download_gif_points_status'] : 'false',

    'quotes_add'  =>  addslashes(trim($_POST['quotes_add'])),
    'quotes_add_status'  =>  $_POST['quotes_add_status'] ? $_POST['quotes_add_status'] : 'false',

    'quotes_views'  =>  addslashes(trim($_POST['quotes_views'])),

    'other_user_quotes_point'  =>  addslashes(trim($_POST['other_user_quotes_point'])),
    'other_user_quotes_status'  =>  $_POST['other_user_quotes_status'] ? $_POST['other_user_quotes_status'] : 'false',

    'like_quotes_points'  =>  addslashes(trim($_POST['like_quotes_points'])),
    'like_quotes_points_status'  =>  $_POST['like_quotes_points_status'] ? $_POST['like_quotes_points_status'] : 'false',
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");



  $_SESSION['msg']="11";
  header( "Location:rewards_points.php");
  exit;

}

?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">

      </div>
      <div class="clearfix"></div>
        <div class="card-body pt_top">

          <div class="rewards_point_page_title">
            <div class="col-md-12 col-xs-12">
              <div class="page_title" style="font-size: 20px;color: #424242;">
                <h3><?=$page_title?></h3>
              </div>
            </div>              
          </div>      
          <form action="" name="admob_settings" method="post" class="form form-horizontal" enctype="multipart/form-data">
            <div class="col-md-12">
              <div class="form-group reward_point_block">
                <div class="col-md-12">
                  <div class="col-md-6 col-sm-8">
                    <div class="form-group">
                      <div class="col-md-7 col-sm-5 points_block mrg_right">
                        <div class="col-md-5">
                          <label class="control-label">Points</label>
                          <input type="text" name="redeem_points" id="redeem_points" value="<?php echo $settings_row['redeem_points'];?>" class="form-control">
                        </div>
                        <div class="col-md-2">
                          <label class="col-md-2 control-label point_count">=</label>
                        </div>
                        <div class="col-md-5">
                          <label class="control-label">Amount</label>
                          <input type="text" name="redeem_money" id="redeem_money" value="<?php echo $settings_row['redeem_money'];?>" class="form-control">
                        </div>  
                      </div>                      
                      <div class="col-md-4 col-sm-6 points_block points_amount">
                        <label class="control-label">Currency Code</label>
                        <input type="text" name="redeem_currency" id="redeem_currency" value="<?php echo $settings_row['redeem_currency'];?>" class="form-control">
                      </div>                    
                    </div>
                  </div>
                  <div class="col-md-4 col-sm-4 redeem_point_section">                    
                    <div class="col-md-12 points_block minimum_redeem_point">
                      <label class="control-label">Minimum Redeem Points</label>  
                      <input type="number" min="1" name="minimum_redeem_points" id="minimum_redeem_points" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?php echo $settings_row['minimum_redeem_points'];?>" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mrg-top manage_user_btn manage_rewards_point_block">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th style="width:300px">Activity Name</th>
                        <th style="width:50px">Points</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>            
                        <td>App Registration Points:-</td>
                        <td><input type="text" name="registration_reward" onkeypress="return event.charCode >= 48 && event.charCode <= 57" id="registration_reward" value="<?php echo $settings_row['registration_reward'];?>" class="form-control limit_0"></td>
                      </tr>
                      <tr>            
                        <td>App Refer Points:-</td>
                        <td><input type="text" name="app_refer_reward" onkeypress="return event.charCode >= 48 && event.charCode <= 57" id="app_refer_reward" value="<?php echo $settings_row['app_refer_reward'];?>" class="form-control limit_0"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          
          
          
       
         
          
          <div align="center" class="form-group">
            <div class="col-md-12">
              <button type="submit" name="rewards_points_submit" class="btn btn-primary ">Save</button>
            </div>
          </div>
          <!-- End -->

        </form>

     

      </div>
      <div class="clearfix"></div>
    </div>
  </div>

</div>


<?php include("includes/footer.php");?>    

<script type="text/javascript">
  $("#other_user_video_point").keyup(function(e){
    if($(this).val()==''){
      $(this).val('0');
    }
  });

  $(".limit_0").blur(function(e){
    if($(this).val() < 0 || $(this).val() == '')
    {
      $(this).val("0");
    }
  });
</script>   
