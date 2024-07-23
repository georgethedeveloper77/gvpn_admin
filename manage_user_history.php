<?php 

$page_title="Edit User's Info";
$active_page="user";

$history_page='user';

include('includes/header.php'); 
include("includes/connection.php");

include("includes/function.php");
include("language/language.php");

$user_id=$_GET['user_id'];

if(isset($_POST['submit']) and isset($_POST['user_id']))
{ 

  $qry = "SELECT * FROM willdev_users WHERE id = '".$_POST['user_id']."'"; 
  $result = mysqli_query($mysqli,$qry);    
  $row = mysqli_fetch_assoc($result);

  if($_FILES['user_image']['name']!="")
  { 
    $file_name= str_replace(" ","-",$_FILES['user_image']['name']);
    $user_image=rand(0,99999)."_".$file_name;

      //Main Image
    $tpath1='images/'.$user_image;       
    $pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 100);
  }   
  else
  {
    $user_image=$row['user_image'];
  }

  if($_POST['password']!="")
  {
    $data = array(
      'name'  =>  $_POST['name'],
      'email'  =>  $_POST['email'],
      'password'  =>  md5(trim($_POST['password'])),
      'phone'  =>  $_POST['phone'],
      'user_youtube'  =>  $_POST['user_youtube'],
      'user_instagram'  =>  $_POST['user_instagram'],
      'user_image'  =>  $user_image
    );
  }
  else
  {
    $data = array(
      'name'  =>  $_POST['name'],
      'email'  =>  $_POST['email'],      
      'phone'  =>  $_POST['phone'],
      'user_youtube'  =>  $_POST['user_youtube'],
      'user_instagram'  =>  $_POST['user_instagram'],
      'user_image'  =>  $user_image
    );
  }

  $user_edit=Update('willdev_users', $data, "WHERE id = '".$_POST['user_id']."'");

  $_SESSION['msg']="11";
  if(isset($_GET['redirect'])){
    header("Location:manage_user_history.php?user_id=".$_POST['user_id'].'&redirect='.$_GET['redirect']);
  }
  else{
    header("Location:manage_user_history.php?user_id=".$_POST['user_id']);
  }
  exit;

}

?>


<div class="row">
  <?php 
      // for common history header
  require_once 'includes/header_history.php';
  ?>
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="row mrg-top">
        <div class="col-md-12">
          <div class="col-md-12 col-sm-12"> </div>
        </div>
      </div>
      <div class="card-body mrg_bottom">
        <form action="" name="addedituser" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="user_id" value="<?php echo $_GET['user_id'];?>" />

          <div class="section">
            <div class="section-body">


              <div class="form-group">
                <label class="col-md-3 control-label">Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="name" id="name" value="<?php if(isset($_GET['user_id'])){echo $users_res_row['name'];}?>" class="form-control" <?=(isset($_GET['user_id']) AND $users_res_row['user_type']!='Normal') ? 'readonly' : ''?> class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Email :-</label>
                <div class="col-md-6">
                  <input type="email" name="email" id="email" value="<?php if(isset($_GET['user_id'])){echo $users_res_row['email'];}?>" <?=(isset($_GET['user_id']) AND $users_res_row['user_type']!='Normal') ? 'readonly' : ''?> class="form-control" required>
                </div>
              </div>
              <?php 
                if(!isset($_GET['user_id']) OR $users_res_row['user_type']=='Normal')
                {
              ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Password :-</label>
                <div class="col-md-6">
                  <input type="password" name="password" id="password" value="" class="form-control" <?php if(!isset($_GET['user_id'])){?>required<?php }?>>
                </div>
              </div>
              <?php } ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Phone :-</label>
                <div class="col-md-6">
                  <input type="text" name="phone" id="phone" value="<?php if(isset($_GET['user_id'])){echo $users_res_row['phone'];}?>" class="form-control">
                </div>
              </div>
            
              <div class="form-group">
                <label class="col-md-3 control-label">User Image :-
                  <p class="control-label-help">(Use Square Image)</p>
                </label>
                <div class="col-md-6">
                  <div class="fileupload_block">
                    <input type="file" name="user_image" value="fileupload" id="fileupload" <?=(!isset($_GET['user_id'])) ? 'required="required"' : ''?>>

                    <div id="uploadPreviewImg">
                      <?php if(isset($_GET['user_id']) and $users_res_row['user_image']!="") {?>
                        <div class="fileupload_img">
                          <img type="image" src="images/<?php echo $users_res_row['user_image'];?>" alt="image" style="width: 100px;height: 100px;"/>
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

              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary">Save</button>
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