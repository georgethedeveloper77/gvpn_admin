<?php 
$action=$_GET['action'];
$page_title=ucfirst($action).' Image Status';
$active_page="status";

include("includes/header.php");

require("includes/function.php");
require("language/language.php");
include("language/app_language.php");

$file_path_img = getBaseUrl();

if(isset($_GET['edit_id']))
{
  $qry="SELECT * FROM willdev_img_status where id='".$_GET['edit_id']."'";
  $result=mysqli_query($mysqli,$qry);
  $row=mysqli_fetch_assoc($result);

  $lang_ids=explode(',', $row['lang_ids']);
}

if(isset($_POST['btn_submit']) AND $action=='add')
{
  $lang_ids=implode(',', $_POST['lang_id']);

  $image_tags=(isset($_POST['image_tags'])) ? addslashes(implode(',', $_POST['image_tags'])) : '';

  $file_size=round($_FILES['image_file']['size'] / 1024 / 1024, 2);

  if($file_size > $settings_details['image_file_size']) {

    $img_size_msg=str_replace('###', $settings_details['image_file_size'], $client_lang['img_size_msg']);

    $_SESSION['class']='error';
    $_SESSION['msg']=$img_size_msg;
    if(isset($_GET['redirect']))
    {
      header("Location:image_status.php?action=add&redirect=".$_GET['redirect']);
    }
    else{
      header( "Location:image_status.php?action=add");
    }
    exit;
  }

  $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);

  $image_file=rand(0,99999)."_image_status.".$ext;

        //Main Image
  $tpath1='images/'.$image_file;   

  if($ext!='png')  {
    $pic1=compress_image($_FILES["image_file"]["tmp_name"], $tpath1, 80);
  }
  else{
    $tmp = $_FILES['image_file']['tmp_name'];
    move_uploaded_file($tmp, $tpath1);
  }

  $data = array( 
    'cat_id'  =>  $_POST['cat_id'],
    'lang_ids'  =>  $lang_ids,
    'image_title'  =>  addslashes($_POST['image_title']),
    'image_tags'  =>  $image_tags,
    'image_layout'  =>  $_POST['image_layout'],
    'image_file'  =>  $image_file,
    'status_type'  =>  'image'
  ); 

  $insert = Insert('willdev_img_status',$data); 

  $last_id = mysqli_insert_id($mysqli);
  
  if(isset($_POST['notify_user'])){

    $img_path=$file_path_img.'images/'.$image_file;

    $content = array("en" => str_replace('###', $_SESSION['admin_name'], $client_lang['add_img_notify_msg']));
    
    $fields = array(
      'app_id' => ONESIGNAL_APP_ID,
      'included_segments' => array('All'),
      'data' => array("foo" => "bar","type" => "single_status","status_type" => "image","id" => $last_id,"external_link"=>false),
      'headings'=> array("en" => APP_NAME),
      'contents' => $content,
      'big_picture' =>$img_path
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
     'Authorization: Basic '.ONESIGNAL_REST_KEY));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $notify_res = curl_exec($ch);
    curl_close($ch);
  }

  if($settings_details['image_add_status']=='true')
  {
    $user_id=0;

    $sql_activity = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$last_id' AND `user_id` = '$user_id' AND `activity_type`='".$app_lang['add_image']."'";
    $res_activity = mysqli_query($mysqli,$sql_activity);

    $add_point=$settings_details['image_add']; 

    if($res_activity->num_rows == 0)
    {
      $qry2 = "SELECT * FROM willdev_users WHERE id = '$user_id'";
      $result2 = mysqli_query($mysqli,$qry2);
      $row2=mysqli_fetch_assoc($result2); 

      $user_total_point=$row2['total_point']+$add_point;

      $user_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='$user_total_point'  WHERE id = '$user_id'");

      user_reward_activity($last_id,$user_id,$app_lang['add_image'],$add_point);

    }
  }

  $_SESSION['class']="success";
  $_SESSION['msg']="10";

  if(isset($_GET['redirect']))
  {
    header("Location:".$_GET['redirect']);
  }
  else{
    header( "Location:manage_image_status.php");
  }
  exit; 
}
else if(isset($_POST['btn_submit']) AND ($action=='edit' OR isset($_GET['edit_id'])))
{
  $lang_ids=implode(',', $_POST['lang_id']);

  $image_tags=implode(',', $_POST['image_tags']);

  if (!empty($_FILES['image_file']['name'])) {

    $file_size=round($_FILES['image_file']['size'] / 1024 / 1024, 2);

    if($file_size > $settings_details['image_file_size']) { 

      $img_size_msg=str_replace('###', $settings_details['image_file_size'], $client_lang['img_size_msg']);

      $_SESSION['class']='error';
      $_SESSION['msg']=$img_size_msg;
      if(isset($_GET['redirect']))
      {
        header( "Location:image_status.php?edit_id=".$_POST['edit_id']."&action=edit&redirect=".$_GET['redirect']);
      }
      else{
        header( "Location:image_status.php?edit_id=".$_POST['edit_id']."&action=edit");
      }
      exit;
    }

    unlink('images/'.$row['image_file']);

    $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);

    $image_file=rand(0,99999)."_image_status.".$ext;

            //Main Image
    $tpath1='images/'.$image_file;   

    if($ext!='png')  {
      $pic1=compress_image($_FILES["image_file"]["tmp_name"], $tpath1, 80);
    }
    else{
      $tmp = $_FILES['image_file']['tmp_name'];
      move_uploaded_file($tmp, $tpath1);
    }
  }
  else{
    $image_file=$row['image_file'];
  }
  

  $data = array( 
    'cat_id'  =>  $_POST['cat_id'],
    'lang_ids'  =>  $lang_ids,
    'image_title'  =>  addslashes($_POST['image_title']),
    'image_tags'  =>  $image_tags,
    'image_layout'  =>  $_POST['image_layout'],
    'image_file'  =>  $image_file
  );  

  $update=Update('willdev_img_status', $data, "WHERE id = '".$_POST['edit_id']."'");

  $_SESSION['class']="success";
  $_SESSION['msg']="11";

  if(isset($_GET['redirect']))
  {
    header("Location:".$_GET['redirect']);
  }
  else{
    header( "Location:image_status.php?edit_id=".$_POST['edit_id']."&action=edit");
  }
  exit;

}

?>

<!-- For Bootstrap Tags -->
<link rel="stylesheet" type="text/css" href="assets/bootstrap-tag/bootstrap-tagsinput.css">
<!-- End -->

<div class="row">
  <div class="col-md-12">
    <?php
    if(isset($_GET['redirect'])){
      echo '<a href="'.$_GET['redirect'].'" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
    }
    else{
      echo '<a href="manage_image_status.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
    }
    ?>
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data">
          <input type="hidden" name="edit_id" value="<?php echo $_GET['edit_id'];?>" />

          <div class="section">
            <div class="section-body">
             <div class="form-group">
              <label class="col-md-3 control-label">Title :-</label>
              <div class="col-md-6">
                <input type="text" name="image_title" placeholder="Enter image title" id="image_title" value="<?php echo $row['image_title'];?>" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Category :-</label>
              <div class="col-md-6">
                <select name="cat_id" id="cat_id" class="select2" required>
                  <option value="">--Select Category--</option>
                  <?php
                  $cat_qry="SELECT * FROM willdev_category WHERE `status`='1' ORDER BY `category_name`";
                  $cat_result=mysqli_query($mysqli,$cat_qry);
                  while($cat_row=mysqli_fetch_array($cat_result))
                  {
                    ?>                       
                    <option value="<?php echo $cat_row['cid'];?>" <?=($row['cat_id']==$cat_row['cid']) ? 'selected' : '';?>><?php echo $cat_row['category_name'];?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="lang_id">Languages:-</label>
              <div class="col-md-6">

                <select name="lang_id[]" id="lang_id" class="select2" multiple="" required>
                  <?php
                  $sql="SELECT * FROM willdev_language WHERE `status`='1' ORDER BY `language_name`";
                  $res=mysqli_query($mysqli,$sql);
                  while($row_data=mysqli_fetch_assoc($res))
                  {
                    ?>                       
                    <option value="<?php echo $row_data['id'];?>" <?=(isset($_GET['edit_id']) && in_array($row_data['id'], $lang_ids)) ? 'selected' : ''; ?>><?php echo $row_data['language_name'];?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Tags(Optional):-</label>
              <div class="col-md-6">
                <input type="text" name="image_tags[]" id="image_tags" value="<?php  echo $row['image_tags'];?>" data-role="tagsinput" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Image Layout </label>
              <div class="col-md-6">
                <select name="image_layout" id="image_layout" style="width:280px; height:25px;" class="select2" required="">
                  <option value="Landscape" <?=(isset($_GET['edit_id']) && $row['image_layout']=='Landscape') ? 'selected' : ''; ?>>Landscape</option>
                  <option value="Portrait" <?=(isset($_GET['edit_id']) && $row['image_layout']=='Portrait') ? 'selected' : ''; ?>>Portrait</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label">Select Image :-

                <p class="control-label-help">(Recommended resolution: <strong>Landscape:</strong> 800x500,650x450<br/><strong>Portrait:</strong> 720X1280, 640X1136, 350x800)</p>
                <p class="control-label-help">(<strong>Note:</strong> Maximum <strong><?=$settings_details['image_file_size']?>MB</strong> file size)</p>
              </label>
              <div class="col-md-6">
                <div class="fileupload_block">
                  <input type="file" name="image_file" value="fileupload" id="fileupload" accept=".png, .jpg, .jpeg" onchange="fileValidation()" <?=(!isset($_GET['edit_id'])) ? 'required' : ''?>>
                  
                  <?php if($row['image_file']!="" AND isset($_GET['edit_id'])){?>
                    <div id="uploadPreviewImg">
                      <div class="fileupload_img">
                        <img type="image" src="images/<?php echo $row['image_file'];?>" <?=(strcmp($row['image_layout'], 'Landscape')==0) ? 'style="width: 150px;height: 100px;"' : 'style="width: 120px;height: 200px;"' ?>  alt="image alt" />
                      </div>
                    </div>
                  <?php }else if($row['image_file']=="" AND isset($_GET['edit_id'])){ ?>
                    <div id="uploadPreviewImg">
                      <div class="fileupload_img">
                        <img type="image" alt="image" <?=(strcmp($row['image_layout'], 'Landscape')==0) ? 'src="assets/images/landscape.jpg" style="width: 150px;height: 100px;"' : 'style="src="assets/images/portrait.jpg" width: 120px;height: 200px;"' ?>/>
                      </div>
                    </div>
                  <?php }else{ ?>
                    <div id="uploadPreviewImg">
                      <div class="fileupload_img"><img type="image" src="assets/images/landscape.jpg" style="width: 150px;height: 100px;" alt="image alt" /></div>
                    </div>
                  <?php } ?>

                </div>
                
              </div>
            </div>
            <?php if(!isset($_GET['edit_id'])){ ?>
              <div class="form-group">
                <label class="col-md-3 control-label">Send notification:-</label>
                <div class="col-md-6" style="padding-top: 10px">
                  <input type="checkbox" id="ckbox_notify" class="cbx hidden" name="notify_user" value="true"/>
                  <label for="ckbox_notify" class="lbl"></label>
                </div>
              </div>
            <?php } ?>
            <br/>
            <div class="form-group">
              <div class="col-md-9 col-md-offset-3">
                <button type="submit" name="btn_submit" class="btn btn-primary">Save</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

<?php include("includes/footer.php");?>

<script type="text/javascript" src="assets/bootstrap-tag/bootstrap-tagsinput.js"></script>

<script type="text/javascript">

  $('#image_tags').tagsinput();

  var image_layout=$("#image_layout").val();

  $("#image_layout").on("change",function(event){

    image_layout=$(this).val();

    $("#fileupload").val('');

    if($(this).val()=='Landscape')
    {
      $("#uploadPreviewImg").find("img").css({width:"150px", height: "100px"})
      $("#uploadPreviewImg").find("img").attr("src",'assets/images/landscape.jpg');
    }
    else
    {
      $("#uploadPreviewImg").find("img").css({width:"120px", height: "200px"})
      $("#uploadPreviewImg").find("img").attr("src",'assets/images/portrait.jpg');  
    }

  });
  
  function fileValidation(){
    var fileInput = document.getElementById('fileupload');
    var filePath = fileInput.value;
    var allowedExtensions = /(\.png|.jpg|.jpeg|.PNG|.JPG|.JPEG)$/i;
    if(!allowedExtensions.exec(filePath)){
      
      if(filePath!=''){
        swal({title: 'Invalid!',text: 'Please upload file having extension .png, .jpg, .jpeg .PNG, .JPG, .JPEG only.', type: 'warning'});
        fileInput.value = '';
        return false;  
      }
      else{

        if(image_layout=='Landscape')
        {
          $("#uploadPreviewImg").find("img").css({width:"150px", height: "100px"})
        }
        else
        {
          $("#uploadPreviewImg").find("img").css({width:"120px", height: "200px"})
        }
        fileInput.setAttribute("required", "required");
      }
    }else{
        //image preview
        if (fileInput.files && fileInput.files[0]) {

          var file_size=parseFloat(((fileInput.files[0].size) / (1024 * 1024)).toFixed(2));
          var required_file_size=parseFloat('<?=$settings_details['image_file_size']?>');

          if(file_size <= required_file_size)
          {
            var reader = new FileReader();
            reader.onload = function(e)
            {

              if(image_layout=='Landscape')
              {
                $("#uploadPreviewImg").find("img").css({width:"150px", height: "100px"});
              }
              else
              {
                $("#uploadPreviewImg").find("img").css({width:"120px", height: "200px"});

              }
              $("#uploadPreviewImg").find("img").attr("src",e.target.result);
            };
            reader.readAsDataURL(fileInput.files[0]);
          }
          else{
            fileInput.value = '';
            var msg="<?=str_replace('###', $settings_details['image_file_size'], $client_lang['img_size_msg']);?>";
            swal({title: 'Warning!',text: msg, type: 'warning'});

            if(image_layout=='Landscape')
            {
              $("#uploadPreviewImg").find("img").css({width:"150px", height: "100px"})
            }
            else
            {
              $("#uploadPreviewImg").find("img").css({width:"120px", height: "200px"})
            }

            return false;
          }
        }
      }
    }


  </script>