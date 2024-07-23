<?php 
  $page_title="Withdrawal";
  $active_page="user";

  $history_page='user';
  
  include('includes/header.php'); 
	include("includes/connection.php");
	
  include("includes/function.php");
	include("language/language.php"); 
 
	$user_id=$_GET['user_id'];


  $qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
                    LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
                    WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = 0";
  $total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
  $total_pending = $total_pending['num'];				

  //Withdrawal List
  $query_withdrawal="SELECT * FROM willdev_users_redeem    
                    where willdev_users_redeem.`user_id`='$user_id' ORDER BY willdev_users_redeem.`id` DESC";
  $sql_withdrawal = mysqli_query($mysqli,$query_withdrawal)or die(mysqli_error());

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

    $sql = mysqli_query($mysqli,$query)or die(mysqli_error());
    $row=mysqli_fetch_assoc($sql);

    return stripslashes($row[$param]);
  }
	 
?>

<style type="text/css">
  .morecontent span {
      display: none;
  }
  .morelink {
      display: block;
  }
  #historyModal .top{
    position: relative;
    padding: 0px;
    padding-bottom: 20px;
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
          <table id="user_history_withdrawal" class="datatable table table-striped primary" cellspacing="0" width="100%">
          <thead>
              <tr>
                  <tr>
                    <th>Account</th>
                    <th>Amount Pay</th>
                    <th>Points</th>
                    <th>Date</th>
                    <th>Current Status</th> 
                    <th>History</th> 
                 </tr>
              </tr>
          </thead>
          <tbody>
              <?php
              $i=0;
              while($users_withdrawal=mysqli_fetch_array($sql_withdrawal))
              {
              ?>
              <tr>
                 <td><?php echo $users_withdrawal['payment_mode'];?></td>
                 <td><?php echo $users_withdrawal['redeem_price'];?> <?php echo $settings_row['redeem_currency'];?></td>   
                 <td><?php echo $users_withdrawal['user_points'];?></td>                
                 <td>
                     <span class="badge badge-success badge-icon"><i class="fa fa-clock-o" aria-hidden="true"></i><span><?php echo date('d-m-Y', strtotime($users_withdrawal['request_date'])).' - '.date('h:i A', strtotime($users_withdrawal['request_date']));?> </span></span>
                </td>
                <td>
                    <?php if($users_withdrawal['status']=="1"){?>
                    <span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Paid</span></span>

                    <?php }else if($users_withdrawal['status']=="2"){?>
                    <span class="badge badge-danger badge-icon"><i class="fa fa-ban" aria-hidden="true"></i><span>Reject </span></span>

                    <?php }else{?>
                    <span class="badge badge-danger badge-icon"><i class="fa fa-clock-o" aria-hidden="true"></i><span>Pending </span></span>
                    <?php }?>
                </td>
                <td>
                  <a href="" class="btn btn-success btn_cust btn_history" data-toggle="tooltip" data-tooltip="User History"><i class="fa fa-history"></i></a>
                  <div class="modal_content" style="display: none;">
                    <table class="datatable2 table table-striped">
                      <thead>
                          <tr>
                              <tr>            
                                <th>Activity Type</th>
                                <th>Points</th>
                                <th>Date</th>  
                             </tr>
                          </tr>
                      </thead>
                      <tbody>
                        <?php
                          $sql_withdrawal_history="SELECT willdev_users_rewards_activity.* FROM willdev_users_rewards_activity
                                          LEFT JOIN willdev_users ON willdev_users_rewards_activity.`user_id`=willdev_users.`id`
                                          WHERE willdev_users_rewards_activity.`user_id`='$user_id' AND willdev_users_rewards_activity.`redeem_id`='".$users_withdrawal['id']."'
                                          ORDER BY willdev_users_rewards_activity.`id` DESC";  

                          $res_withdrawal_history=mysqli_query($mysqli,$sql_withdrawal_history);

                          while($row=mysqli_fetch_array($res_withdrawal_history))
                          {
                          ?>
                          <tr>
                            <td><?php echo $row['activity_type'];?></td>   
                            <td><?php echo $row['points'];?></td>                
                            <td>
                                <span class="badge badge-danger badge-icon"><i class="fa fa-clock-o" aria-hidden="true"></i><span><?php echo date('d-m-Y', strtotime($row['date'])).' - '.date('h:i A', strtotime($row['date']));?> </span></span>
                            </td>
                          </tr>
                          <?php
                          }

                          mysqli_free_result($res_withdrawal_history);
                        ?>
                      </tbody>
                    </table>
                  </div>
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


<!-- History Modal -->
<div id="historyModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Withdrawal History</h4>
        </div> 
        <div class="modal-body">
          
        </div>
      </div>
    </div>
</div>



<?php include('includes/footer.php');?> 

<script type="text/javascript">

  $('#historyModal').on('hidden.bs.modal', function () {
    $('.datatable2').DataTable().destroy();
  })

  //once the modal has been shown
  $('#historyModal').on('shown.bs.modal', function() {


    $('.datatable2').DataTable({
      "dom": '<"top"fl<"clear">>rt<"bottom"ip<"clear">>',
      "oLanguage": {
        "sSearch": "",
        "sLengthMenu": "_MENU_"
      },
      "ordering": false,
      "initComplete": function initComplete(settings, json) {
        $('div.dataTables_filter input').attr('placeholder', 'Search...');
      }
    });
  });

  $(".btn_history").on("click",function(e){
      e.preventDefault();

      var html=$(this).next("div.modal_content").html();

      $("#historyModal .modal-body").html(html);
      $("#historyModal").modal("show");

  });
</script>                 

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
   
      $(".morelink").click(function(e){

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