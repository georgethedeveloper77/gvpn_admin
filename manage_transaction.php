<?php 
$page_title="Manage Transactions";
include('includes/header.php'); 
include('includes/function.php');
include('language/language.php'); 

function send_notification($fields){

  $fields = json_encode($fields);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.ONESIGNAL_REST_KEY));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $notify_res = curl_exec($ch);  

  curl_close($ch);

  return $notify_res;
}

$qry="SELECT * FROM willdev_settings where id='1'";
$result=mysqli_query($mysqli,$qry);
$settings_row=mysqli_fetch_assoc($result);

$qry_users_paid="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
WHERE willdev_users_redeem.`status` = '1'";

$total_paid = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_paid));
$total_paid = $total_paid['num'];

$qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
WHERE willdev_users_redeem.`status` = '0'";

$total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
$total_pending = $total_pending['num'];

$external_link=false;

if(isset($_GET['payment_status']))
{

  if($_GET['payment_status']==2)
  {
    $sql="SELECT willdev_users_redeem.*,willdev_users.`name`,willdev_users.`email` FROM willdev_users_redeem
    LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
    ORDER BY willdev_users_redeem.`redeem_price` DESC";  
  }
  else
  {
    $sql="SELECT willdev_users_redeem.*,willdev_users.`name`,willdev_users.`email` FROM willdev_users_redeem
    LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
    WHERE willdev_users_redeem.`status` = '".$_GET['payment_status']."' ORDER BY willdev_users.id DESC";
  }    
  
  $users_result=mysqli_query($mysqli,$sql);

}
else
{

  $sql="SELECT willdev_users_redeem.*,willdev_users.`name`,willdev_users.`email` FROM willdev_users_redeem
  LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id` 
  ORDER BY willdev_users_redeem.`id` DESC";

  $users_result=mysqli_query($mysqli,$sql);
}

    //Change Transaction Statuses
if(isset($_POST['pending_submit']))
{

  $user_id=$_POST['user_id'];
  $transaction_id=$_POST['transaction_id'];

  $payment_msg=addslashes(trim($_POST['payment_msg']));

  if($user_id!="")
  { 

    if($payment_msg!='')
    {
      $content = array("en" => $payment_msg);
    }  
    else
    {
      $content = array("en" => $client_lang['payment_sent_msg']);
    }

    $fields = array(
      'app_id' => ONESIGNAL_APP_ID,
      'included_segments' => array('Subscribed Users'), 
      'data' => array("foo" => "bar","type" => "payment_withdraw","external_link"=>$external_link),
      'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id)),
      'headings'=> array("en" => APP_NAME),
      'contents' => $content
    );

    send_notification($fields);
  }


  $data = array('status'  =>  '0');    
  $edit_status=Update('willdev_users_redeem', $data, "WHERE id = ".$transaction_id);

  $_SESSION['msg']="18";
  header("Location: manage_transaction.php");
  exit;
}

if(isset($_POST['paid_submit']))
{

  $user_id=$_POST['user_id'];
  $transaction_id=$_POST['transaction_id'];

  $payment_msg=addslashes(trim($_POST['payment_msg']));

  if($user_id!="")
  { 

    if($payment_msg!='')
    {
      $content = array("en" => $payment_msg);
    }  
    else
    {
      $content = array("en" => $client_lang['payment_sent_msg']);
    }

    if($_FILES['payment_receipt']['name']!="")
    {   

      $ext = pathinfo($_FILES['payment_receipt']['name'], PATHINFO_EXTENSION);

      $path = "images/payment_receipt/";
      $payment_receipt=date('dmYhis').'_'.rand(0,99999).".".$ext;

      $tpath1='images/payment_receipt/'.$payment_receipt;    

      if($ext!='png'){
        $pic1=compress_image($_FILES["payment_receipt"]["tmp_name"], $tpath1, 80);
      }else{
        move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $tpath1);
      }

      $file_path = getBaseUrl().'images/payment_receipt/'.$payment_receipt;

      $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'included_segments' => array('Subscribed Users'),                                            
        'data' => array("foo" => "bar","type" =>"payment_withdraw","external_link"=>$external_link),
        'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id)),
        'headings'=> array("en" => APP_NAME),
        'contents' => $content,
        'big_picture' =>$file_path                    
      );

    }
    else{
      $fields = array(
        'app_id' => ONESIGNAL_APP_ID,
        'included_segments' => array('Subscribed Users'),                                            
        'data' => array("foo" => "bar","type" =>"payment_withdraw","external_link"=>$external_link),
        'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id)),
        'headings'=> array("en" => APP_NAME),
        'contents' => $content 
      );
    }

    send_notification($fields);
  }


  $data = array(
    'cust_message'  =>  addslashes(trim($_POST['payment_msg'])),
    'receipt_img'  =>  $payment_receipt,
    'responce_date'  =>  date('Y-m-d h:i:s'),
    'status'  =>  '1'
  );

  $edit_status=Update('willdev_users_redeem', $data, "WHERE id = ".$transaction_id);

  $_SESSION['msg']="17";
  header("Location: manage_transaction.php");
  exit;
}

if(isset($_POST['reject_submit']))
{

  $user_id=$_POST['user_id'];
  $transaction_id=$_POST['transaction_id'];

  $payment_msg=addslashes(trim($_POST['payment_msg']));

  if($user_id!="")
  { 
    if($payment_msg!='')
    {
      $content = array("en" => $payment_msg);
    }  
    else
    {
      $content = array("en" => $client_lang['payment_sent_msg']);
    }


    $fields = array(
      'app_id' => ONESIGNAL_APP_ID,
      'included_segments' => array('Subscribed Users'),                                            
      'data' => array("foo" => "bar","type" =>"payment_withdraw","external_link"=>$external_link),
      'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $user_id)),
      'headings'=> array("en" => APP_NAME),
      'contents' => $content 
    );

    send_notification($fields);
  }

  $data = array(
    'cust_message'  =>  trim($_POST['payment_msg']),
    'responce_date'  =>  date('Y-m-d h:i:s'),
    'status'  =>  '2'
  );

  $edit_status=Update('willdev_users_redeem', $data, "WHERE id = ".$transaction_id);

  $_SESSION['msg']="19";
  header("Location: manage_transaction.php");
  exit;
}

$countStr='';

$no_data_status=false;
$count=$monthCount=0;

for ($mon=1; $mon<=12; $mon++) {

  if(date('n') < $mon){
    break;
  }

  if(isset($_GET['filterByYear'])){

    $year=$_GET['filterByYear'];
    $month = date('F', mktime(0,0,0,$mon, 1, $year));

    $sql="SELECT SUM(`redeem_price`) AS total_amount FROM willdev_users_redeem WHERE DATE_FORMAT(`request_date`, '%c') = '$mon' AND DATE_FORMAT(`request_date`, '%Y') = '$year'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $total_amount=$row['total_amount'] ? $row['total_amount'] : 0;

    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS pending_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '0' AND DATE_FORMAT(willdev_users_redeem.`request_date`, '%c') = '$mon' AND DATE_FORMAT(willdev_users_redeem.`request_date`, '%Y') = '$year'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $pending_amount=$row['pending_amount'] ? $row['pending_amount'] : 0;
    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS paid_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '1' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%c') = '$mon' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%Y') = '$year'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $paid_amount=$row['paid_amount'] ? $row['paid_amount'] : 0;
    
    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS reject_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '2' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%c') = '$mon' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%Y') = '$year'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $reject_amount=$row['reject_amount'] ? $row['reject_amount'] : 0;
    
    mysqli_free_result($res);
  }
  else{

    $month = date('F', mktime(0,0,0,$mon, 1, date('Y')));

    $sql="SELECT SUM(`redeem_price`) AS total_amount FROM willdev_users_redeem WHERE DATE_FORMAT(`request_date`, '%c') = '$mon'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $total_amount=$row['total_amount'] ? $row['total_amount'] : 0;

    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS pending_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '0' AND DATE_FORMAT(willdev_users_redeem.`request_date`, '%c') = '$mon'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $pending_amount=$row['pending_amount'] ? $row['pending_amount'] : 0;
    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS paid_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '1' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%c') = '$mon'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $paid_amount=$row['paid_amount'] ? $row['paid_amount'] : 0;
    
    mysqli_free_result($res);

    $sql="SELECT SUM(`redeem_price`) AS reject_amount FROM willdev_users_redeem WHERE willdev_users_redeem.`status` = '2' AND DATE_FORMAT(willdev_users_redeem.`responce_date`, '%c') = '$mon'";

    $res=mysqli_query($mysqli, $sql);

    $row=mysqli_fetch_assoc($res);

    $reject_amount=$row['reject_amount'] ? $row['reject_amount'] : 0;
    
    mysqli_free_result($res);

  }

  $countStr.="[{v : '".$month."', f: '".$month."'}, ".$total_amount.", ".$paid_amount.", ".$pending_amount.", ".$reject_amount."], ";


  if($total_amount!=0 || $paid_amount!=0 || $pending_amount!=0 || $reject_amount!=0){
    $monthCount++;
  }

}

if($monthCount!=0){
  $no_data_status=false;
}
else{
  $no_data_status=true;
}

$countStr=rtrim($countStr, ", ");

?>
<style type="text/css">
  .top{
    position: relative !important;
    padding: 0px 0px 20px 0px !important;
  }
  .dataTables_wrapper .top .dataTables_filter .form-control{
    border-radius: 2px !important;
    border-color: #ccc !important;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075) !important;
  }
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 2px;padding-bottom: 1%">
      <div class="col-lg-10">
        <h3>Transactions Statistics</h3>
      </div>
      <div class="col-lg-2" style="padding-top: 20px">
        <form method="get" id="graphFilter">
          <select class="select2" name="filterByYear">
            <?php 
            $currentYear=date('Y');
            $minYear=2018;

            for ($i=$currentYear; $i >= $minYear ; $i--) { 
              ?>
              <option value="<?=$i?>" <?=(isset($_GET['filterByYear']) && $_GET['filterByYear']==$i) ? 'selected' : ''?>><?=$i?></option>
              <?php
            }
            ?>
          </select>
        </form>
      </div>
      <div class="col-lg-12">
        <?php 
        if($no_data_status){
          ?>
          <h3 class="text-muted text-center" style="padding-bottom: 2em">No data found !</h3>
          <?php
        }
        else{
          ?>
          <div id="transactionChart">
            <p style="text-align: center;"><i class="fa fa-spinner fa-spin" style="font-size:3em;color:#aaa;margin-bottom:50px" aria-hidden="true"></i></p>
          </div>
          <?php    
        }
        ?>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="page_title_block">
        <div class="col-md-3 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-9">
          <div class="search_list">
            <span class="badge badge-success badge-icon"><i class="fa fa-check fa-2x" aria-hidden="true"></i><span style="font-size: 16px;font-weight: 500"><?php echo $total_paid ? thousandsNumberFormat($total_paid) : '0';?> <?php echo $settings_row['redeem_currency'];?> Paid</span></span>

            <span class="badge badge-danger badge-icon"><i class="fa fa-clock-o fa-2x" aria-hidden="true"></i><span style="font-size: 16px;font-weight: 500"> <?php echo $total_pending ? thousandsNumberFormat($total_pending) : '0';?> <?php echo $settings_row['redeem_currency'];?> Pending</span></span>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4">
          <form method="GET" action="" id="filterForm" enctype="multipart/form-data">
            <div class="form-group">
              <div class="col-md-8" style="padding-left: 0px">
                <select name="payment_status" class="select2 filter" required>
                  <option value="">--Filter--</option>
                  <option value="0" <?php if(isset($_GET['payment_status']) AND $_GET['payment_status']==0){?>selected<?php }?>>Pending</option>
                  <option value="1" <?php if(isset($_GET['payment_status']) AND $_GET['payment_status']==1){?>selected<?php }?>>Paid</option>

                  <option value="2" <?php if(isset($_GET['payment_status']) AND $_GET['payment_status']==2){?>selected<?php }?>>Most Earned</option>
                </select>
              </div>
            </div>
          </form>      
        </div>
        <div class="col-md-8 text-right">
          <button class="btn btn-danger btn_cust btn_delete_all"><i class="fa fa-trash"></i> Delete All</button>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top manage_transaction_btn">
        <table class="datatable table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:40px">
                <div class="checkbox" style="margin: 0px">
                  <input type="checkbox" name="checkall" id="checkall" value="">
                  <label for="checkall"></label>
                </div>
              </th> 
              <th>Name</th>            
              <th>Email</th>                  
              <th>Details</th>
              <th>Points</th>
              <th>Amount Pay</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>  
            </tr>
          </thead>
          <tbody>
            <?php  
            $i=0;
            while($users_row=mysqli_fetch_assoc($users_result))
            {   
              ?>
              <tr>
                <td> 
                  <div class="checkbox">
                    <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $users_row['id']; ?>" class="post_ids">
                    <label for="checkbox<?php echo $i;?>"></label>
                  </div>
                </td>

                <td><?php echo $users_row['name'];?></td>
                <td><?php echo $users_row['email'];?></td>
                <td align="center">
                  <a href="" class="btn btn-success btn_details" data-tooltip="Payment Details"><i class="fa fa-eye"></i></a>
                  <div class="content" style="display: none;">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">Payment details of <?php echo ucwords($users_row['name']);?></h4>
                    </div>     
                    <div class="modal-body">
                      Payment Mode : <?php echo $users_row['payment_mode'];?> <br/>
                      Payment Details : <?php echo $users_row['bank_details'];?> 
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </td>
                <td><?php echo $users_row['user_points'];?></td>
                <td align="center" nowrap="">
                  <?php echo $users_row['redeem_price'];?> <?php echo $settings_row['redeem_currency'];?> 
                </td>
                <td nowrap=""><?php echo date('d-m-Y',strtotime($users_row['request_date']));?></td>
                <td>

                  <div class="btn-group">

                    <button type="button" class="btn <?php if($users_row['status']=="1"){?>btn-success<?php }else if($users_row['status']=="0"){?> btn-warning <?php }else{?>btn-danger<?php }?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php if($users_row['status']=="1"){?>Paid<?php }else if($users_row['status']=="0"){?> Pending <?php }else{?>Reject<?php }?> <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                      <li>
                        <a href="" class="status_modal">Paid</a>
                        <!-- Paid Modal Content -->
                        <div class="status_content" style="display: none;">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Send payment reply to <?php echo ucwords($users_row['name']);?></h4>
                          </div>                        
                          <form action="" method="post" class="" enctype="multipart/form-data">
                            <input type="hidden" name="transaction_id" value="<?php echo $users_row['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $users_row['user_id']; ?>">                        
                            <div class="modal-body">
                              <div class="form-group">
                                <label>Custom Message(Optional)</label>
                                <textarea name="payment_msg" class="form-control" placeholder="Set msg"></textarea>
                              </div>
                              <div class="form-group">
                                <label>Upload Payment Receipt<span style="color: red"> *</span></label>
                                <input type="file" name="payment_receipt" required="" accept=".png, .jpg, .jpeg" class="form-control">
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" name="paid_submit" class="btn btn-sm btn-success">Send</button>
                            </div>
                          </form>
                        </div>
                        <!-- End -->

                      </li>
                      <li>
                        <a href="" class="status_modal">Pending</a>

                        <div class="status_content" style="display: none;">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Payment reply to <?php echo ucwords($users_row['name']);?></h4>
                          </div>                        
                          <form action="" method="post" class="" enctype="multipart/form-data">
                            <input type="hidden" name="transaction_id" value="<?php echo $users_row['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $users_row['user_id']; ?>">                        
                            <div class="modal-body">
                              <label>Custom Message(Optional)</label>
                              <textarea name="payment_msg" class="form-control" placeholder="Set msg"></textarea> 
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" name="pending_submit" class="btn btn-sm btn-success">Send Reply</button>
                            </div>
                          </form>
                        </div>

                      </li>                     
                      <li>
                        <a href="" class="btn_reject">Reject</a>
                        <div class="status_content" style="display: none;">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Reject payment reply to <?php echo ucwords($users_row['name']);?></h4>
                          </div>                        
                          <form action="" method="post" class="" enctype="multipart/form-data">
                            <input type="hidden" name="transaction_id" value="<?php echo $users_row['id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $users_row['user_id']; ?>">                        
                            <div class="modal-body">
                              <label>Custom Message(Optional)</label>
                              <textarea name="payment_msg" class="form-control" placeholder="Set msg"></textarea> 
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                              <button type="submit" name="reject_submit" class="btn btn-sm btn-success">Reject</button>
                            </div>
                          </form>
                        </div>
                      </li>                          
                    </ul>
                  </div>

                  <div class="modal fade" id="rejectModal<?php echo $users_row['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">

                    </div>
                  </div>
                </td>
                <td nowrap=""> 
                  <a href="manage_user_history_total_points.php?user_id=<?php echo $users_row['user_id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-success btn_cust" data-toggle="tooltip" data-tooltip="User History"><i class="fa fa-history"></i></a>
                  <a href="javascript:void(0)" data-id="<?php echo $users_row['id'];?>" class="btn btn-danger btn_cust btn_delete_a" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a>
                </td>  
              </tr>
              <?php
              $i++;
            }
            ?>
          </tbody>
        </table>
      </form> 
    </div>
    <div class="clearfix"></div>
  </div>
</div>
</div>    

<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div> 

<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>



<?php include('includes/footer.php');?>                  

<script type="text/javascript">

  /*$(function() {
    $('#payment_status').on("change",function() {
      this.form.submit();
    });
  });*/

  $(".filter").on("change",function(e){
    $("#filterForm *").filter(":input").each(function(){
      if ($(this).val() == '')
        $(this).prop("disabled", true);
    });
    $("#filterForm").submit();
  });

  $(".btn_details").on("click",function(e){
    e.preventDefault();

    var html=$(this).next("div.content").html();

    $("#paymentDetailsModal .modal-content").html(html);
    $("#paymentDetailsModal").modal("show");

  });

  
  $(".status_modal").on("click",function(e){
    e.preventDefault();

    var html=$(this).next("div.status_content").html();

    $("#statusModal .modal-content").html(html);
    $("#statusModal").modal("show");

  });

  $(".btn_reject").on("click",function(e){
    e.preventDefault();

    swal({
      title: "<?=$client_lang['are_you_sure_msg']?>",
      text: "",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      cancelButtonClass: "btn-warning",
      confirmButtonText: "Yes",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: false,
      showLoaderOnConfirm: true
    },
    function(isConfirm) {
      if (isConfirm) {
        var html=$(this).next("div.status_content").html();
        $("#statusModal .modal-content").html(html);
        $("#statusModal").modal("show");
      }
      else{
        swal.close();
      }

    });
  });


  $(".btn_delete_a").on("click", function(e) {

    e.preventDefault();

    var _id = $(this).data("id");
    var _table = 'willdev_users_redeem';

    swal({
      title: "<?=$client_lang['are_you_sure_msg']?>",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      cancelButtonClass: "btn-warning",
      confirmButtonText: "Yes!",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: false,
      showLoaderOnConfirm: true
    },
    function(isConfirm) {
      if (isConfirm) {

        $.ajax({
          type: 'post',
          url: 'processData.php',
          dataType: 'json',
          data: {id: _id, for_action: 'delete', table: _table, 'action': 'multi_action'},
          success: function(res)
          {
            $('.notifyjs-corner').empty();

            if(res.status==1){
              location.reload();
            }
            else{
              swal({
                title: 'Error!', 
                text: "<?=$client_lang['something_went_worng_err']?>", 
                type: 'error'
              },function() {
                location.reload();
              });
            }
          }
        });
      } else {
        swal.close();
      }

    });
  });

  // for multiple deletes
  $(".btn_delete_all").on("click",function(e){
    var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });

    if(_ids!='')
    {
      swal({
        title: "<?=$client_lang['are_you_sure_msg']?>",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger btn_edit",
        cancelButtonClass: "btn-warning btn_edit",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: true
      },
      function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            type:'post',
            url:'processData.php',
            dataType:'json',
            data:{ids:_ids,'action':'removeAllTransaction'},
            success:function(res){
              console.log(res);
              if(res.status=='1'){
                swal({
                  title: "Successfully", 
                  text: "Transactions are deleted...", 
                  type: "success"
                },function() {
                  location.reload();
                });
              }
              else{
                swal("Something went to wrong !");
              }
            }
          });
        }
        else{
          swal.close();
        }

      });
    }
    else{
      swal({title: 'Sorry no records selected!', type: 'info'});
    }
  });


  var totalItems=0;

  $("#checkall").on("click",function () {

    totalItems=0;

    $('input:checkbox').not(this).prop('checked', this.checked);
    $.each($("input[name='post_ids[]']:checked"), function(){
      totalItems=totalItems+1;
    });

    if($('input:checkbox').prop("checked") == true){
      $('.notifyjs-corner').empty();
      $.notify(
        'Total '+totalItems+' item checked',
        { position:"top center",className: 'success'}
        );
    }
    else if($('input:checkbox'). prop("checked") == false){
      totalItems=0;
      $('.notifyjs-corner').empty();
    }
  });

  var noteOption = {
    clickToHide : false,
    autoHide : false,
  }

  $.notify.defaults(noteOption);

  $(".post_ids").on("click",function(e){

    if($(this).prop("checked") == true){
      totalItems=totalItems+1;
    }
    else if($(this). prop("checked") == false){
      totalItems = totalItems-1;
    }

    if(totalItems==0){
      $('.notifyjs-corner').empty();
      exit();
    }

    $('.notifyjs-corner').empty();

    $.notify(
      'Total '+totalItems+' item checked',
      { position:"top center",className: 'success'}
      );
  });

</script>

<?php
if(!$no_data_status){
  ?>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawAxisTickColors);

    function drawAxisTickColors() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Months');
      data.addColumn('number', 'Total Amount');
      data.addColumn('number', 'Paid Amount');
      data.addColumn('number', 'Pending Amount');
      data.addColumn('number', 'Reject Amount');

      data.addRows([
        <?=$countStr?>]);

      var options = {
        height:400,
        hAxis: {
          title: "Months of <?=(isset($_GET['filterByYear'])) ? $_GET['filterByYear'] : date('Y')?>",
          textStyle: {
            fontSize: 14,
            color: '#053061',
            bold: true,
            italic: false
          },
          titleTextStyle: {
            bold: true,
            italic: false
          }
        },
        vAxis: {
          title: "Total Amount in <?=$settings_row['redeem_currency']?>", 
          textStyle: {
            bold: false,
            italic: false
          },
          titleTextStyle: {
            bold: true,
            italic: false
          },
          viewWindowMode: "explicit", viewWindow:{ min: 0 },
        },
        legend: {
          position: 'bottom'
        },
        chartArea:{
          left:50,top:50,width:'100%',height:'auto'
        },
        colors: ['#3366CC', 'green','orange','red'],
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('transactionChart'));
      chart.draw(data, options);
    }
  </script>
  <?php
}
?>
<script type="text/javascript">
  // filter of graph
  $("select[name='filterByYear']").on("change",function(e){
    $("#graphFilter").submit();
  });
</script>