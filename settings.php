<?php 	

$page_title="General Settings";
$active_page="settings";

include("includes/connection.php");
include("includes/header.php");
require("includes/function.php");
require("language/language.php");

$qry="SELECT * FROM willdev_settings where id='1'";
$result=mysqli_query($mysqli,$qry);
$settings_row=mysqli_fetch_assoc($result);

$qry = "SELECT * FROM willdev_proper_settings WHERE setting='app_terms_of_service'";
$result = mysqli_query($mysqli,$qry);
$row_tos = mysqli_fetch_assoc($result);

if(isset($_POST['submit']))
{

  $img_res=mysqli_query($mysqli,"SELECT * FROM willdev_settings WHERE id='1'");
  $img_row=mysqli_fetch_assoc($img_res);


  if($_FILES['app_logo']['name']!="")
  {        

    unlink('images/'.$img_row['app_logo']);   

    $app_logo=$_FILES['app_logo']['name'];
    $pic1=$_FILES['app_logo']['tmp_name'];

    $tpath1='images/'.$app_logo;      
    copy($pic1,$tpath1);


    $data = array(      
      'one_connect'  =>  $_POST['one_connect'],
      'one_connect_key'  =>  $_POST['one_connect_key'],
      'email_from'  =>  $_POST['email_from'],
      'app_name'  =>  $_POST['app_name'],
      'app_logo'  =>  $app_logo,  
      'app_description'  => addslashes($_POST['app_description']),
      'app_version'  =>  $_POST['app_version'],
      'app_author'  =>  $_POST['app_author'],
      'app_contact'  =>  $_POST['app_contact'],
      'app_email'  =>  $_POST['app_email'],   
      'app_website'  =>  $_POST['app_website'],
      'app_developed_by'  =>  $_POST['app_developed_by']                     

    );

  }
  else
  {
    $data = array(
      'one_connect'  =>  $_POST['one_connect'],
      'one_connect_key'  =>  $_POST['one_connect_key'],
      'email_from'  =>  $_POST['email_from'],
      'app_name'  =>  $_POST['app_name'],
      'app_description'  => addslashes($_POST['app_description']),
      'app_version'  =>  $_POST['app_version'],
      'app_author'  =>  $_POST['app_author'],
      'app_contact'  =>  $_POST['app_contact'],
      'app_email'  =>  $_POST['app_email'],   
      'app_website'  =>  $_POST['app_website'],
      'app_developed_by'  =>  $_POST['app_developed_by']
    );

  } 

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");


  $_SESSION['msg']="11";
  header("Location: settings.php");
  exit;

}
else if(isset($_POST['app_faq_submit']))
{

  $data = array('app_faq'  =>  addslashes($_POST['app_faq']));

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: settings.php");
  exit;

}
else if(isset($_POST['app_pri_poly']))
{

  $data = array('app_privacy_policy'  =>  addslashes($_POST['app_privacy_policy']));

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: settings.php");
  exit;

}

else if(isset($_POST['btn_terms_of_service']))
{

  $data = array('value'  =>  addslashes($_POST['app_terms_of_service']));

  $settings_edit= Update('willdev_proper_settings', $data, "WHERE setting='app_terms_of_service'");

  $_SESSION['msg']="11";
  header("Location: settings.php");
  exit;

}

else if(isset($_POST['account_delete']))
{

  $data = array(
    'account_delete_intruction'  =>  trim($_POST['account_delete_intruction'])
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header( "Location:settings.php");
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
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">

          <li role="presentation" class="active"><a href="#general_settings" aria-controls="general_settings" role="tab" data-toggle="tab">General</a></li>

          <li role="presentation"><a href="#payment_settings" aria-controls="payment_settings" role="tab" data-toggle="tab">Payment Mode</a></li>

          <li role="presentation"><a href="#api_faq" aria-controls="api_faq" role="tab" data-toggle="tab">FAQ Content</a></li>

          <li role="presentation"><a href="#api_privacy_policy" aria-controls="api_privacy_policy" role="tab" data-toggle="tab"> Privacy Policy</a></li>
          <li role="presentation"><a href="#api_terms_of_service" aria-controls="api_terms_of_service" role="tab" data-toggle="tab">Terms of Service</a></li>


        </ul>

        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="general_settings">   
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="settings_from" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                        <label class="col-md-3 control-label">One Connect :-
                        </label>
                        <div class="col-md-6">
                            <select name="one_connect" id="one_connect" class="form-control">
                                <option value="0" <?php echo $settings_row['one_connect']=='0' ? 'selected' : '';?>>Disabled</option>
                                <option value="1" <?php echo $settings_row['one_connect']=='1' ? 'selected' : '';?>>Enabled</option>
                            </select>
                        </div>
                        <br><br>
                      </div>  
                      <div class="form-group" style="">
                        <label class="col-md-3 control-label">One Connect Key :-
                        </label>
                        <div class="col-md-6">
                          <input type="text" name="one_connect_key" id="one_connect_key" value="<?php echo $settings_row['one_connect_key'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group" style="">
                        <label class="col-md-3 control-label">Email <span style="color: red">*</span>:-
                        </label>
                        <div class="col-md-6">
                          <input type="text" name="app_email" id="app_email" value="<?php echo $settings_row['app_email'];?>" class="form-control">
                        </div>
                      </div>                   
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Name :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_name" id="app_name" value="<?php echo $settings_row['app_name'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Logo :-</label>
                        <div class="col-md-6">
                          <div class="fileupload_block">
                            <input type="file" name="app_logo" id="fileupload">

                            <?php if($settings_row['app_logo']!="") {?>
                              <div class="fileupload_img"><img type="image" src="images/<?php echo $settings_row['app_logo'];?>" alt="image" style="width: 100px;height: 100px;" /></div>
                            <?php } else {?>
                              <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="image" /></div>
                            <?php }?>

                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Description :-</label>
                        <div class="col-md-6">
                          <textarea name="app_description" id="app_description" class="form-control"><?php echo $settings_row['app_description'];?></textarea>
                          <script>CKEDITOR.replace( 'app_description' );</script>
                        </div>
                      </div>
                      <div class="form-group">&nbsp;</div>                 
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Version :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_version" id="app_version" value="<?php echo $settings_row['app_version'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Author :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_author" id="app_author" value="<?php echo $settings_row['app_author'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Contact :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_contact" id="app_contact" value="<?php echo $settings_row['app_contact'];?>" class="form-control">
                        </div>
                      </div>     

                      <div class="form-group">
                        <label class="col-md-3 control-label">Website :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_website" id="app_website" value="<?php echo $settings_row['app_website'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Developed By :-</label>
                        <div class="col-md-6">
                          <input type="text" name="app_developed_by" id="app_developed_by" value="<?php echo $settings_row['app_developed_by'];?>" class="form-control">
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

          <div role="tabpanel" class="tab-pane" id="payment_settings">
            <div class="rows">
              <div class="col-md-12">
                <div class="add_btn_primary"> 
                  <a href="payment_mode.php?redirect=<?=$redirectUrl?>">Add New</a>
                </div>
                <div class="clearfix"></div>
                <br/>
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th width="100">Sr No.</th>
                      <th>Payment Mode</th>
                      <th>Status</th>
                      <th class="cat_action_list" style="width:60px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $sql="SELECT * FROM willdev_payment_mode ORDER BY `id` DESC";
                    $res=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
                    $no=1;
                    while ($row=mysqli_fetch_assoc($res)) {
                      ?>
                      <tr>
                        <td><?=$no++?></td>
                        <td><?=$row['mode_title']?></td>
                        <td>
                          <?php if($row['status']!="0"){?>
                            <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$row['id']?>" data-action="deactive" data-column="status"><span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

                          <?php }else{?>
                            <a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$row['id']?>" data-action="active" data-column="status"><span class="badge badge-danger badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
                          <?php }?>
                        </td>
                        <td nowrap="">
                          <a href="payment_mode.php?edit_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="Edit" class="btn btn-primary btn_edit"><i class="fa fa-edit"></i></a>
                          <a href="javascript:void(0)" data-id="<?=$row['id'];?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_cust btn_delete_a"><i class="fa fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="api_faq">   
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="api_faq" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                        <label class="col-md-3 control-label">FAQ Content :-</label>
                        <div class="col-md-9">
                          <textarea name="app_faq" id="app_faq" class="form-control"><?php echo stripslashes($settings_row['app_faq']);?></textarea>
                          <script>CKEDITOR.replace( 'app_faq' );</script>
                        </div>
                      </div>
                      <br>
                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="app_faq_submit" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div> 

          <div role="tabpanel" class="tab-pane" id="api_privacy_policy">   
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="api_privacy_policy" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <?php 
                      if(file_exists('privacy_policy.php'))
                      {
                        ?>
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Privacy Policy URL :-</label>
                          <div class="col-md-9">
                            <input type="text" readonly class="form-control" value="<?=getBaseUrl().'privacy_policy.php'?>">
                          </div>
                        </div>
                      <?php } ?>
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Privacy Policy :-</label>
                        <div class="col-md-9">
                          <textarea name="app_privacy_policy" id="privacy_policy" class="form-control"><?php echo stripslashes($settings_row['app_privacy_policy']);?></textarea>
                          <script>CKEDITOR.replace( 'privacy_policy' );</script>
                        </div>
                      </div>
                      <br>
                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="app_pri_poly" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          
          <div role="tabpanel" class="tab-pane" id="api_terms_of_service">   
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="api_terms_of_service" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <?php 
                      if(file_exists('terms_of_service.php'))
                      {
                        ?>
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Terms of Service URL :-</label>
                          <div class="col-md-9">
                            <input type="text" readonly class="form-control" value="<?=getBaseUrl().'terms_of_service.php'?>">
                          </div>
                        </div>
                      <?php } ?>
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Terms of Service :-</label>
                        <div class="col-md-9">
                          <textarea name="app_terms_of_service" id="terms_of_service" class="form-control"><?php echo stripslashes($row_tos['value']);?></textarea>
                          <script>CKEDITOR.replace( 'terms_of_service' );</script>
                        </div>
                      </div>
                      <br>
                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="btn_terms_of_service" class="btn btn-primary">Save</button>
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

      $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
        document.title = $(this).text()+" | <?=APP_NAME?>";
      });

      var activeTab = localStorage.getItem('activeTab');
      if(activeTab){
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
      }

      $("#interstital_ad_click").blur(function(e){
        if($(this).val() == '')
          $(this).val("0");
      });
      $("#rewarded_video_click").blur(function(e){
        if($(this).val() == '')
          $(this).val("0");
      });

      $(".toggle_btn_a").on("click",function(e){
        e.preventDefault();

        var _for=$(this).data("action");
        var _id=$(this).data("id");
        var _column=$(this).data("column");
        var _table='willdev_payment_mode';

        $.ajax({
          type:'post',
          url:'processData.php',
          dataType:'json',
          data:{id:_id,for_action:_for,column:_column,table:_table,'action':'toggle_status','willdev_id':'id'},
          success:function(res){
            console.log(res);
            if(res.status=='1'){
              location.reload();
            }
          }
        });

      });

      $(".limit_1").blur(function(e){
        if($(this).val() < 1)
        {
          alert("Value must be >= 1");
          $(this).val("1");
        }
      });

      $("input[name='cat_show_home_limit']").blur(function(e){
        if($(this).val() == '')
        {
          $(this).val("0");
        }
      });


      $(".btn_delete_a").on("click", function(e) {

        e.preventDefault();

        var _id = $(this).data("id");
        var _table = 'willdev_payment_mode';

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

    </script>