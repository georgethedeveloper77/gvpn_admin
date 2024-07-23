<?php 

$page_title="Send Notification";
include("includes/header.php");
include("includes/connection.php");
require("includes/function.php");
require("language/language.php");

$file_path = getBaseUrl();

// paramater wise info
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
    $query="SELECT * FROM willdev_category WHERE `cid`='$post_id'";
    break;
  }

  $sql = mysqli_query($mysqli,$query)or die(mysqli_error());
  $row=mysqli_fetch_assoc($sql);

  return stripslashes($row[$param]);
}


if(isset($_POST['submit'])){

  $external_link=($_POST['external_link']!='') ? addslashes(trim($_POST['external_link'])) : false;

  $message=addslashes(trim($_POST['notification_msg']));

  $content = array("en" => $message);

  $id=$type=$status_type=$title=$image_file='';

  if($_POST['cat_id']!=0){
    $id=$_POST['cat_id'];
    $type='category';
    $title=get_single_info($id, 'category_name','category');
    $image_file=$file_path.'images/'.get_single_info($id, 'category_image','category');
  }
  else if($_POST['video_id']!=0){
    $id=$_POST['video_id'];
    $type='single_status';
    $status_type='video';
    $title=get_single_info($id, 'video_title','video');
    $image_file=$file_path.'images/'.get_single_info($id, 'video_thumbnail','video');
  }
  else if($_POST['image_id']!=0){
    $id=$_POST['image_id'];
    $type='single_status';
    $status_type='image';
    $title=get_single_info($id, 'image_title','image');
    $image_file=$file_path.'images/'.get_single_info($id, 'image_file','image');
  }
  else if($_POST['gif_id']!=0){
    $id=$_POST['gif_id'];
    $type='single_status';
    $status_type='gif';
    $title=get_single_info($id, 'image_title','gif');
    $image_file=$file_path.'images/'.get_single_info($id, 'image_file','gif');
  }
  else if($_POST['quote_id']!=0){
    $id=$_POST['quote_id'];
    $type='single_status';
    $title=get_single_info($id, 'quote','quote');
    $status_type='quote';
  }

  if($_FILES['big_picture']['name']!="")
  {
    $big_picture=rand(0,99999)."_".$_FILES['big_picture']['name'];
    $tpath2='images/'.$big_picture;
    move_uploaded_file($_FILES["big_picture"]["tmp_name"], $tpath2);

    $image_file=$file_path.'images/'.$big_picture;

    $fields = array(
      'app_id' => ONESIGNAL_APP_ID,
      'included_segments' => array('All'),                                            
      'data' => array("foo" => "bar","id"=>$id,"type"=>$type,"title"=>$title,"status_type"=>$status_type,"external_link"=>$external_link),
      'headings'=> array("en" => addslashes(trim($_POST['notification_title']))),
      'contents' => $content,
      'big_picture' =>$image_file
    );

  }
  else{
    $fields = array(
      'app_id' => ONESIGNAL_APP_ID,
      'included_segments' => array('All'),                                            
      'data' => array("foo" => "bar","id"=>$id,"type"=>$type,"title"=>$title,"status_type"=>$status_type,"external_link"=>$external_link),
      'headings'=> array("en" => addslashes(trim($_POST['notification_title']))),
      'contents' => $content,
      'big_picture' =>$image_file
    );
  }

  $fields = json_encode($fields);
  print("\nJSON sent:\n");
  print($fields);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
    'Authorization: Basic '.ONESIGNAL_REST_KEY));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $response = curl_exec($ch);
  curl_close($ch);

  $_SESSION['msg']="16";
  header( "Location:send_notification.php");
  exit; 


}

if(isset($_POST['notification_submit']))
{

  $data = array(
    'onesignal_app_id' => $_POST['onesignal_app_id'],
    'onesignal_rest_key' => $_POST['onesignal_rest_key'],
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header( "Location:send_notification.php");
  exit;
}

?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px"> 

        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#notification_settings" name="Notification Settings" aria-controls="notification_settings" role="tab" data-toggle="tab"><i class="fa fa-wrench"></i> Notification Settings</a></li>
          <li role="presentation"><a href="#send_notification" aria-controls="send_notification" name="Send notification" role="tab" data-toggle="tab"><i class="fa fa-send"></i> Send Notification</a></li>

        </ul>

        <div class="tab-content">
          <div role="tabpanel" class="tab-pane" id="send_notification">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                  <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
                    <div class="section">
                      <div class="section-body">

                        <div class="form-group">
                          <label class="col-md-3 control-label">Title :-</label>
                          <div class="col-md-6">
                            <input type="text" name="notification_title" id="notification_title" class="form-control" value="" placeholder="" required>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">Message :-</label>
                          <div class="col-md-6">
                            <textarea name="notification_msg" id="notification_msg" class="form-control" required></textarea>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">Image :-<br/>(Optional)<p class="control-label-help">(Recommended resolution: 1:2)</p></label>
                          <div class="col-md-6">
                            <div class="fileupload_block">
                              <input type="file" name="big_picture" value="" id="fileupload">
                              <div id="uploadPreview">
                                <div class="fileupload_img">
                                  <img type="image" src="assets/images/landscape.jpg" style="width: 150px;height: 90px;" alt="image alt" />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-9 mrg_bottom link_block">
                        

                           
                             
                         
                                
                                
                                   
                                    <div class="form-group">
                                      <label class="col-md-4 control-label">External Link :-<br/>(Optional)</label>
                                      <div class="col-md-8">
                                        <input type="text" name="external_link" id="external_link" class="form-control" value="" placeholder="http://www.yourdomain.com">
                                      </div>
                                    </div>   
                                  </div>   
                                  <div class="form-group">
                                    <div class="col-md-9 col-md-offset-3">
                                      <button type="submit" name="submit" class="btn btn-primary">Send</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>


                        </div>
                      </div>
                    </div>

                    <!-- for notification settings tab -->
                    <div role="tabpanel" class="tab-pane active" id="notification_settings">

                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-md-12">
                            <form action="" name="settings_api" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">
                              <div class="section">
                                <div class="section-body">
                                  <div class="form-group">
                                    <label class="col-md-3 control-label">OneSignal App ID :-</label>
                                    <div class="col-md-6">
                                      <input type="text" name="onesignal_app_id" id="onesignal_app_id" value="<?php echo $settings_details['onesignal_app_id'];?>" class="form-control">
                                    </div>
                                  </div>
                                  <div class="form-group">
                                    <label class="col-md-3 control-label">OneSignal Rest Key :-</label>
                                    <div class="col-md-6">
                                      <input type="text" name="onesignal_rest_key" id="onesignal_rest_key" value="<?php echo $settings_details['onesignal_rest_key'];?>" class="form-control">
                                    </div>
                                  </div>              
                                  <div class="form-group">
                                    <div class="col-md-9 col-md-offset-3">
                                      <button type="submit" name="notification_submit" class="btn btn-primary">Save</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <?php include("includes/footer.php");?>       

          <script type="text/javascript">

            $(".select2").change(function(e){

              var _val=$(this).val();

              if(_val!=0){
                $(this).parents('.link_block').find("input").attr("disabled","disabled");
                $(this).parents('.link_block').find(".select2").attr("disabled","disabled");
                $(this).removeAttr("disabled");
              }
              else{
                $(this).parents('.link_block').find(".select2").removeAttr("disabled");
                $(this).parents('.link_block').find("input").removeAttr("disabled");
              }

            });

            $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
              localStorage.setItem('activeTab', $(e.target).attr('href'));
              document.title = $(this).attr("name")+" | <?=APP_NAME?>";
            });

            var activeTab = localStorage.getItem('activeTab');
            if(activeTab){
              $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }

            var _URL = window.URL || window.webkitURL;

            $("#fileupload").change(function(e) {
              var file, img;
              var thisFile=$(this);

              var countCheck=0;

              if ((file = this.files[0])) {
                img = new Image();
                img.onload = function() {
                  if(this.width < this.height)
                  {
                    swal({title: 'Warning!',text: '<?=$client_lang["slider_img_greater_err"]?>', type: 'warning'});
                    thisFile.val('');
                    $('#uploadPreview').find("img").attr('src', 'assets/images/landscape.jpg');
                    return false;
                  }
                  else if(this.width == this.height){
                    swal({title: 'Warning!',text: '<?=$client_lang["slider_img_square_err"]?>', type: 'warning'});
                    thisFile.val('');
                    $('#uploadPreview').find("img").attr('src', 'assets/images/landscape.jpg');
                    return false;
                  }

                };
                img.onerror = function() {
                  swal({title: 'Error!',text: 'Not a valid file: '+ file.type, type: 'error'});
                  thisFile.val('');
                  $('#uploadPreview').find("img").attr('src', 'assets/images/landscape.jpg');
                  return false;
                };

                img.src = _URL.createObjectURL(file);

                $('#uploadPreview').find("img").attr('src', img.src);

              }

            });


            $(function(){
              $('.select2').select2({
                ajax: {
                  url: 'getData.php',
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                    var query = {
                      type: $(this).data("type"),
                      search: params.term,
                      page: params.page || 1
                    }
                    return query;
                  },
                  processResults: function (data, params) {
                   params.page = params.page || 1;
                   return {
                    results: data.items,
                    pagination: {
                      more: (params.page * 5) < data.total_count
                    }
                  };
                },
                cache: true
              }
            });
            });
          </script>