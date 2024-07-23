<?php 
    $page_title="All Points History";
    $active_page="user";

    $history_page='user';

    include('includes/header.php'); 
	  include("includes/connection.php");
    include("includes/function.php");
    include("language/language.php"); 

    $user_id=trim($_GET['user_id']);
 
    $qry_users_paid="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
                      LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
                      WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = '1'";
                      $total_paid = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_paid));
                      $total_paid = $total_paid['num'];

    $qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
                        LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`=willdev_users.`id`
                        WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = '0'";
                        $total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
                        $total_pending = $total_pending['num'];		



    function get_single_info($post_id,$param,$type='video')
    {
      global $mysqli;

      switch ($type) {
        case 'video':
          $query="SELECT * FROM willdev_video WHERE `id`='$post_id'";
          break;

        case 'image':
          $query="SELECT * FROM willdev_img_status WHERE `id`='$post_id'";
          break;

        case 'gif':
          $query="SELECT * FROM willdev_img_status WHERE `id`='$post_id'";
          break;

        case 'quote':
          $query="SELECT * FROM willdev_quotes WHERE `id`='$post_id'";
          break;
        
        default:
          $query="SELECT * FROM willdev_video WHERE `id`='$post_id'";
          break;
      }

      $sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

      if(mysqli_num_rows($sql) > 0){
        $row=mysqli_fetch_assoc($sql);
        return stripslashes($row[$param]);  
      }
      else{
        return '-';
      }

      
    }

 
	 
?>

<style type="text/css">
  .morecontent span {
      display: none;
  }
  .morelink {
      display: block;
  }
</style>

<div class="row">
    <?php 
      // for common history header
      require_once 'includes/header_history.php';
    ?>
    <div class="col-xs-12">
      <div class="card">
        <div class="card-header">
          <?=$page_title?>
        </div>
        <div class="card-body no-padding">
          <table class="datatable table table-striped primary" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <tr>
                      <th>Title</th>             
                      <th>Activity Type</th>
                      <th>Points</th>
                      <th>Date</th>  
                   </tr>
                </tr>
            </thead>
            <tbody>
                <?php
                $i=0;
                while($users_row=mysqli_fetch_array($users_rewards_result))
                {
                  $type='';
                  $title='';

                  $video = "video";
                  $image = "image";
                  $gif = "gif";
                  $quote = "quote";
                  $activity = strtolower($users_row['activity_type']);

                  if(strpos($activity, $video) !== false)
                  {
                      $type='video';
                      $title='video_title';
                  } 
                  else if(strpos($activity, $image) !== false)
                  {
                      $type='image';
                      $title='image_title';
                  } 
                  else if(strpos($activity, $gif) !== false)
                  {
                      $type='gif';
                      $title='image_title';
                  } 
                  else if(strpos($activity, $quote) !== false)
                  {
                      $type='quote';
                      $title='quote';
                  }
                  else{
                      $type='';
                  }
            ?>
            <tr>
              <td width="500">
                <span class="more">
                  <?php
                    if($type!=''){
                      echo get_single_info($users_row['post_id'],$title,$type);
                    }
                    else{
                      echo '#';
                    }
                  ?>
                </span>
              </td>
              <td><?php echo $users_row['activity_type'];?></td>   
              <td><?php echo $users_row['points'];?></td>                
              <td>
                  <span class="badge badge-danger badge-icon"><i class="fa fa-clock-o" aria-hidden="true"></i>
                    <span><?php echo date('d-m-Y', strtotime($users_row['date'])).' - '.date('h:i A', strtotime($users_row['date']));?> </span>
                  </span>
              </td>
           
            </tr>
           <?php
            
            $i++;
            }
          ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>    
</div> 



<?php include('includes/footer.php');?>        


<script type="text/javascript">
  $(document).ready(function() {
      // Configure/customize these variables.
      var showChar = 50;  // How many characters are shown by default
      var ellipsestext = "...";
      var moretext = "Show more >";
      var lesstext = "Show less";
      

      $('.more').each(function() {
          var content = $.trim($(this).text());
          
          if(content.length > showChar) {
    
              var c = content.substr(0, showChar);
              var h = content.substr(showChar, content.length - showChar);
    
              var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span><a href="" class="morelink">' + moretext + '</a></span>';
   
              $(this).html(html);
          }
   
      });
   
      $(".morelink").click(function(){
          if($(this).hasClass("less")) {
              $(this).removeClass("less");
              $(this).html(moretext);
          } else {
              $(this).addClass("less");
              $(this).html(lesstext);
          }
          $(this).parent().prev().toggle();
          $(this).prev().toggle();
          return false;
      });
  });
</script>          