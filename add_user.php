<?php 

  $page_title=(isset($_GET['user_id'])) ? 'Edit User' : 'Add User';
  $active_page="user";

  include("includes/header.php");
  include("includes/connection.php");

  include("includes/function.php");
  include("language/language.php");
  include("language/app_language.php"); 
  require_once 'vendor/stripe-php/init.php';
  
    $user_id = $_GET['user_id'];
	    
    $sql= "SELECT * FROM willdev_payment_method WHERE name='Stripe'";
    $row_method = mysqli_fetch_assoc(mysqli_query($mysqli, $sql));
    
    $stripe = new \Stripe\StripeClient($row_method['private_key']);
    
    $query = "SELECT stripe_subscription_id FROM willdev_users WHERE id=$user_id";
    $row_user = mysqli_fetch_assoc(mysqli_query($mysqli, $query));
    
    $stripe_json = "";
    
    if ($row_user['stripe_subscription_id'] != "" && $row_user['stripe_subscription_id'] != null)
       $stripe_json = $stripe->subscriptions->retrieve($row_user['stripe_subscription_id'], []);
  
$redeem_code_qry = "SELECT * FROM willdev_redeem WHERE status=1";
$redeem_code_result=mysqli_query($mysqli,$redeem_code_qry);	

  function createRandomCode() 
  {     
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";     
    srand((double)microtime()*1000000);     
    $i = 0;     
    $pass = '' ;     
    while ($i <= 7) 
    {         
      $num = rand() % 33;         
      $tmp = substr($chars, $num, 1);         
      $pass = $pass . $tmp;         
      $i++;     
    }    
    return $pass; 
  }

  if(isset($_POST['submit']) and isset($_GET['add']))
  {

    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
    {
      $_SESSION['class']="warn";
      $_SESSION['msg']="invalid_email_format";
      if(isset($_GET['redirect']))
      {
        header("Location:add_user.php?add=yes&redirect".$_GET['redirect']);
      }
      else{
        header("Location:add_user.php?add=yes");
      }
      exit;
    }
    else
    {
      $email=addslashes(trim($_POST['email']));

      $sql="SELECT * FROM willdev_users WHERE `email` = '$email' AND `user_type`='Normal' AND `user_type` <> 'Admin'";

      $res=mysqli_query($mysqli, $sql);

      if(mysqli_num_rows($res) == 0)
      {
        if($_FILES['user_image']['name']!="")
        {
          $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

          $user_image=rand(0,99999)."_user.".$ext;

          //Main Image
          $tpath1='images/'.$user_image;   

          if($ext!='png')  {
            $pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
          }
          else{
            $tmp = $_FILES['user_image']['tmp_name'];
            move_uploaded_file($tmp, $tpath1);
          }
        }   
        else
        {
          $user_image='';
        }

        $data = array(
          'user_type'=>'Normal',  
          'user_code'  =>createRandomCode(),
          'name'  =>  addslashes(trim($_POST['name'])),
          'email'  =>  trim($_POST['email']),
          'password'  =>  md5(trim($_POST['password'])),
          'phone'  =>  addslashes(trim($_POST['phone'])),
          'user_youtube'  =>  trim($_POST['user_youtube']),
          'user_instagram'  =>  trim($_POST['user_instagram']),
          'user_image'  =>  $user_image,
          'registration_on' => strtotime(date('d-m-Y h:i A'))
        );  

        $qry = Insert('willdev_users',$data);

        $user_id=mysqli_insert_id($mysqli);

        //Default Admin Follow
        $data_follow = array(
          'user_id' => 0,
          'follower_id'  => $user_id                
        );   

        $qry_follow = Insert('willdev_follows',$data_follow);             

        $user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET `total_followers` = `total_followers` + 1 WHERE id = '0'");
        $user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET `total_following` = `total_following` + 1 WHERE id = '$user_id'");

        if($settings_details['registration_reward_status']=='true')
        {
          $points=$settings_details['registration_reward'];
          $view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET `total_point`= `total_point` + '$points' WHERE `id` = '$user_id'");

          user_reward_activity('',$user_id,$app_lang['register_reward'],$settings_details['registration_reward']);
        }
        
        if(isset($_POST['is_premium']))		
    	{
    		  
    		$qry = "SELECT * FROM willdev_redeemed WHERE user_id=$user_id";
		    $result = mysqli_query($mysqli, $qry); 
		    $row_count = mysqli_num_rows($result);
    		 
    		$code = $_POST['redeem_code'];
    
            if ($row_count > 0) {
                
                $code_qry = "SELECT * FROM willdev_redeem WHERE code='$code'";
        		$code_result = mysqli_query($mysqli, $code_qry);
                $code_row = mysqli_fetch_assoc($code_result);
          
                $period = $code_row['period'];  // 'day', 'month', or 'year'
                $length = $code_row['period_length']; // Number of days, months, or years to add
                
                // Get the current date
                $currentDate = new DateTime();
                
                // Calculate the future date based on the specified period and length
                if ($period === 'Day') {
                    $currentDate->add(new DateInterval("P{$length}D"));
                } elseif ($period === 'Month') {
                    $currentDate->add(new DateInterval("P{$length}M"));
                } elseif ($period === 'Year') {
                    $currentDate->add(new DateInterval("P{$length}Y"));
                }
                
                // Format and print the resulting date
                $futureDate = $currentDate->format('Y-m-d');
                
                $view_qry = mysqli_query($mysqli,"UPDATE willdev_redeemed SET redeem_id='$code', expiration='$futureDate' WHERE user_id=$user_id");
        	        
                mysqli_free_result($code_result);
                
            } else {
    
                $code_qry = "SELECT * FROM willdev_redeem WHERE code='$code'";
        		$code_result = mysqli_query($mysqli, $code_qry);
                $code_row = mysqli_fetch_assoc($code_result);
          
                $period = $code_row['period'];  // 'day', 'month', or 'year'
                $length = $code_row['period_length']; // Number of days, months, or years to add
                
                // Get the current date
                $currentDate = new DateTime();
                
                // Calculate the future date based on the specified period and length
                if ($period === 'Day') {
                    $currentDate->add(new DateInterval("P{$length}D"));
                } elseif ($period === 'Month') {
                    $currentDate->add(new DateInterval("P{$length}M"));
                } elseif ($period === 'Year') {
                    $currentDate->add(new DateInterval("P{$length}Y"));
                }
                
                // Format and print the resulting date
                $futureDate = $currentDate->format('Y-m-d');
                
                $view_qry = mysqli_query($mysqli,"INSERT INTO willdev_redeemed (user_id, redeem_id, expiration) VALUES ($user_id, $code, '$futureDate')");
                
                //$new_count = $code_row['redeem_count'] - 1;
                //mysqli_query($mysqli,"UPDATE willdev_redeem SET redeem_count=$new_count WHERE code='$code'");
        	        
                mysqli_free_result($code_result);
            
            }
    
    	} else {
    	    mysqli_query($mysqli,"DELETE FROM willdev_redeemed WHERE user_id=$user_id");
    	}



        $_SESSION['class']="success";
        $_SESSION['msg']="10";

        header("location:manage_users.php");   
        exit;
      }
      else
      {
        $_SESSION['class']="warn";
        $_SESSION['msg']="email_exist";

        if(isset($_GET['redirect']))
        {
          header("Location:add_user.php?add=yes&redirect".$_GET['redirect']);
        }
        else{
          header("Location:add_user.php?add=yes");
        }
        exit;
      }
    }
  }

  if(isset($_GET['user_id']))
  {
    $user_qry="SELECT * FROM willdev_users WHERE id='".$_GET['user_id']."'";
    $user_result=mysqli_query($mysqli,$user_qry);
    $user_row=mysqli_fetch_assoc($user_result);	
    
    $user_redeem_qry = "SELECT * FROM willdev_redeemed WHERE user_id=" .$_GET['user_id'];
    $user_redeem_result = mysqli_query($mysqli, $user_redeem_qry); 
    $user_row_count = mysqli_num_rows($user_redeem_result);
	 
	if ($user_row_count > 0) {
        $is_user_premium = 1;
        $selected_code_row = mysqli_fetch_assoc($user_redeem_result);
        $selected_code = $selected_code_row['redeem_id'];
	}
    else
        $is_user_premium = 0;
  }

  if(isset($_POST['submit']) and isset($_POST['user_id']))
  {

    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
    {
      $_SESSION['class']="warn";
      $_SESSION['msg']="invalid_email_format";
      if(isset($_GET['redirect']))
      {
        header("Location:add_user.php?user_id=".$_POST['user_id'].'&redirect'.$_GET['redirect']);
      }
      else{
        header("Location:add_user.php?user_id=".$_POST['user_id']);
      }
      exit;
    }
    else
    {
      $email=addslashes(trim($_POST['email']));

      $sql="SELECT * FROM willdev_users WHERE `email` = '$email' AND `user_type`='Normal' AND `user_type` <> 'Admin' AND `id` <> '".$_POST['user_id']."'";

      $res=mysqli_query($mysqli, $sql);

      if(mysqli_num_rows($res) == 0)
      {
        $data = array(
          'name'  =>  addslashes(trim($_POST['name'])),
          'email'  =>  trim($_POST['email']),
          'phone'  =>  trim($_POST['phone']),
          'user_youtube'  =>  trim($_POST['user_youtube']),
          'user_instagram'  =>  trim($_POST['user_instagram'])
        );
    

        if($_FILES['user_image']['name']!="")
        { 
          $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

          $user_image=rand(0,99999)."_user.".$ext;

          //Main Image
          $tpath1='images/'.$user_image;   

          if($ext!='png')  {
            $pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
          }
          else{
            $tmp = $_FILES['user_image']['tmp_name'];
            move_uploaded_file($tmp, $tpath1);
          }

          $data = array_merge($data, array("user_image" => $user_image));

        }

        if(isset($_POST['password']) && $_POST['password']!="")
        {
          $password=md5(trim($_POST['password']));

          $data = array_merge($data, array("password"=>$password));
        }

        $user_edit=Update('willdev_users', $data, "WHERE id = '".$_POST['user_id']."'");
        

        $user_id = $_POST['user_id'];
        
        if(isset($_POST['is_premium']))		
    	{
    		  
    		$qry = "SELECT * FROM willdev_redeemed WHERE user_id=$user_id";
		    $result = mysqli_query($mysqli, $qry); 
		    $row_count = mysqli_num_rows($result);
    		 
    		$code = $_POST['redeem_code'];
    
            if ($row_count > 0) {
                
                $code_qry = "SELECT * FROM willdev_redeem WHERE code='$code'";
        		$code_result = mysqli_query($mysqli, $code_qry);
                $code_row = mysqli_fetch_assoc($code_result);
          
                $period = $code_row['period'];  // 'day', 'month', or 'year'
                $length = $code_row['period_length']; // Number of days, months, or years to add
                
                // Get the current date
                $currentDate = new DateTime();
                
                // Calculate the future date based on the specified period and length
                if ($period === 'Day') {
                    $currentDate->add(new DateInterval("P{$length}D"));
                } elseif ($period === 'Month') {
                    $currentDate->add(new DateInterval("P{$length}M"));
                } elseif ($period === 'Year') {
                    $currentDate->add(new DateInterval("P{$length}Y"));
                }
                
                // Format and print the resulting date
                $futureDate = $currentDate->format('Y-m-d');
                
                $view_qry = mysqli_query($mysqli,"UPDATE willdev_redeemed SET redeem_id='$code', expiration='$futureDate' WHERE user_id=$user_id");
        	        
                mysqli_free_result($code_result);
                
            } else {
    
                $code_qry = "SELECT * FROM willdev_redeem WHERE code='$code'";
        		$code_result = mysqli_query($mysqli, $code_qry);
                $code_row = mysqli_fetch_assoc($code_result);
          
                $period = $code_row['period'];  // 'day', 'month', or 'year'
                $length = $code_row['period_length']; // Number of days, months, or years to add
                
                // Get the current date
                $currentDate = new DateTime();
                
                // Calculate the future date based on the specified period and length
                if ($period === 'Day') {
                    $currentDate->add(new DateInterval("P{$length}D"));
                } elseif ($period === 'Month') {
                    $currentDate->add(new DateInterval("P{$length}M"));
                } elseif ($period === 'Year') {
                    $currentDate->add(new DateInterval("P{$length}Y"));
                }
                
                // Format and print the resulting date
                $futureDate = $currentDate->format('Y-m-d');
                
                $view_qry = mysqli_query($mysqli,"INSERT INTO willdev_redeemed (user_id, redeem_id, expiration) VALUES ($user_id, $code, '$futureDate')");
                
                //$new_count = $code_row['redeem_count'] - 1;
                //mysqli_query($mysqli,"UPDATE willdev_redeem SET redeem_count=$new_count WHERE code='$code'");
        	        
                mysqli_free_result($code_result);
            
            }
    
    	} else {
    	    mysqli_query($mysqli,"DELETE FROM willdev_redeemed WHERE user_id=$user_id");
    	}
        
        
        

        $_SESSION['class']="success";
        $_SESSION['msg']="11";

        if(isset($_GET['redirect']))
        {
          header("Location:".$_GET['redirect']);
        }
        else{
          header("Location:add_user.php?user_id=".$_POST['user_id']);
        }

        exit;
      }
      else
      {
        $_SESSION['class']="warn";
        $_SESSION['msg']="email_exist";

        if(isset($_GET['redirect']))
        {
          header("Location:add_user.php?user_id=".$_POST['user_id'].'&redirect'.$_GET['redirect']);
        }
        else{
          header("Location:add_user.php?user_id=".$_POST['user_id']);
        }
        exit;
      }
    }
  }
?>


<div class="row">
  <div class="col-md-12">
    <?php
      if(isset($_GET['redirect'])){
        echo '<a href="'.$_GET['redirect'].'" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
      }
      else{
        echo '<a href="manage_users.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
      }
    ?>
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-6 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
          <?php if ($stripe_json != "" && $stripe_json['status'] == "active") {
              echo '<button type="button" id="btn_cancel_stripe" class="btn btn-primary float-right" style="margin-top: 20px; background-color: #FF5349; border: none">Cancel Stripe Subscription</button>';
          } ?>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="user_id" value="<?php echo $_GET['user_id'];?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="name" id="name" value="<?php if(isset($_GET['user_id'])){echo $user_row['name'];}?>" class="form-control" <?=(isset($_GET['user_id']) AND $user_row['user_type']!='Normal') ? 'readonly' : ''?> required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Email :-</label>
                <div class="col-md-6">
                  <input type="email" name="email" id="email" value="<?php if(isset($_GET['user_id'])){echo $user_row['email'];}?>" class="form-control" <?=(isset($_GET['user_id']) AND $user_row['user_type']!='Normal') ? 'readonly' : ''?> <?php if(!isset($_GET['user_id'])){ echo 'required="required"'; }?>>
                </div>
              </div>
              <?php 
                if(!isset($_GET['user_id']) OR $user_row['user_type']=='Normal')
                {
              ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Password :-</label>
                <div class="col-md-6">
                  <input type="password" name="password" id="password" value="" class="form-control" <?php if(!isset($_GET['user_id'])){ echo 'required="required"'; }?>>
                </div>
              </div>
              <?php } ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Phone :-</label>
                <div class="col-md-6">
                  <input type="text" name="phone" id="phone" value="<?php if(isset($_GET['user_id'])){echo $user_row['phone'];}?>" class="form-control">
                </div>
              </div>
           
              <div class="form-group">
                <label class="col-md-3 control-label">User Image :-
                  <p class="control-label-help">(Use Square Image</p>
                </label>
                <div class="col-md-6">
                  <div class="fileupload_block">
                    <input type="file" name="user_image" value="fileupload" id="fileupload" <?=(!isset($_GET['user_id'])) ? 'required="required"' : ''?>>

                    <div id="uploadPreviewImg">
                      <?php if(isset($_GET['user_id']) and $user_row['user_image']!="") {?>
                        <div class="fileupload_img">
                          <img type="image" src="images/<?php echo $user_row['user_image'];?>" alt="image" style="width: 100px;height: 100px;"/>
                        </div>	
                      <?php }else{?>	
                        <div class="fileupload_img">
                          <img type="image" src="assets/images/square.jpg" alt="image" style="width: 100px;height: 100px;"/>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <div  class="form-group">
                <label class="col-md-3 control-label">Type: </label>
                <div class="col-md-3">
                    <label  class="control-label">
                        <input type="checkbox" name="is_premium" id="is_premium" value="1" <?php if($is_user_premium == 1){ echo 'checked';} ?>> Premium User
                    </label>
                </div>
              </div>
              
              <br>
              
              <div class="form-group" id="div_redeem" style="display:<?php echo ($is_user_premium == 1) ? '' : 'none'; ?>">
              <label class="col-md-3 control-label">Code: </label>
                <div class="col-md-6">
                    <select style="width: 300px" name="redeem_code" id="redeem_code">
                        <?php
                        
                            while ($row = mysqli_fetch_assoc($redeem_code_result)) {
                                $selected = ($selected_code == $row['id']) ? "selected" : '';
                              echo "<option value='" . $row['id']. "' $selected>" . $row['code'] . "</option>";
                            }
                        
                        ?>
                    </select>
                </div>
               </div>
               
              <br>
              <br>

              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary">Save</button>&nbsp;
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<?php include('includes/footer.php');?>

<script type="text/javascript">
  var _URL = window.URL || window.webkitURL;
  
    const checkbox = $('input[name="is_premium"]');
    checkbox.change((event) => {
      if (event.target.checked) {
        $('#div_redeem').show();
      } else {
        $('#div_redeem').hide();
      }
    });
    
  $("#btn_cancel_stripe").on("click",function(e){
        e.preventDefault();
        
        swal({
			title: "<?=$client_lang['are_you_sure_msg']?>",
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
                  data:{user_id:<?php echo $_GET["user_id"]; ?>,'action':'cancel_stripe_subscription'},
                  success:function(res){
                    console.log(res);
                    if(res.status=='1'){
                      location.reload();
                    }
                  }
                });

			}
			else{
				swal.close();
			}

		});

      });

  $("#fileupload").change(function(e) {
    var file, img;
    var thisFile=$(this);

    var countCheck=0;

    if ((file = this.files[0])) {
      img = new Image();

      img.onload = function() {
        if(this.width < this.height || this.width > this.height)
        {
          swal({title: 'Warning!',text: '<?=$client_lang["user_img_err"]?>', type: 'warning'});
          thisFile.val('');
          $('#uploadPreviewImg').find("img").attr('src', 'assets/images/square.jpg');
          return false;
        }

      };
      img.onerror = function() {
        swal({title: 'Error!',text: 'Not a valid file: '+ file.type, type: 'error'});
        thisFile.val('');
        $('#uploadPreview').find("img").attr('src', 'assets/images/square.jpg');
        return false;
      };

      img.src = _URL.createObjectURL(file);

      $("#uploadPreviewImg").find("img");
      $("#uploadPreviewImg").find("img").attr("src",img.src);  

    }

  });
</script>