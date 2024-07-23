<?php 	

$page_title="App Settings";
$active_page="settings";

include("includes/connection.php");
include("includes/header.php");
require("includes/function.php");
require("language/language.php");

$qry="SELECT * FROM willdev_settings where id='1'";
$result=mysqli_query($mysqli,$qry);
$settings_row=mysqli_fetch_assoc($result);

if(isset($_POST['watermark_submit']))
{
  if($_FILES['watermark_image']['name']!="")
  {         

    $watermark_image=$_FILES['watermark_image']['name'];
    $pic1=$_FILES['watermark_image']['tmp_name'];

    $tpath1='images/'.$watermark_image;      
    copy($pic1,$tpath1);

    $data = array
    (                 
      'watermark_on_off' => $_POST['watermark_on_off'],
      'watermark_image' => $watermark_image
    );
  }
  else
  {
    $data = array('watermark_on_off' => $_POST['watermark_on_off']);
  }

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: app_settings.php");
  exit;

}
else if(isset($_POST['admob_submit']))
{

  if($_POST['banner_ad'])
  {
    $banner_ad="true";
  }
  else
  {
    $banner_ad="false";
  }

  if($_POST['interstitial_ad'])
  {
    $interstitial_ad="true";
  }
  else
  {
    $interstitial_ad="false";
  }

  if($_POST['rewarded_video_ads'])
  {
    $rewarded_video_ads="true";
  }
  else
  {
    $rewarded_video_ads="false";
  }

  $data = array(
    'publisher_id'  =>  $_POST['publisher_id'],
    'interstitial_ad'  =>  $interstitial_ad,
    'interstitial_ad_id'  =>  $_POST['interstitial_ad_id'],
    'interstitial_ad_click'  =>  $_POST['interstitial_ad_click'],
    'banner_ad_type'  =>  $_POST['banner_ad_type'],
    'interstitial_ad_type'  =>  $_POST['interstitial_ad_type'],
    'banner_ad'  =>  $banner_ad,
    'banner_ad_id'  =>  $_POST['banner_ad_id'],
    'rewarded_video_ads'  => $rewarded_video_ads,
    'rewarded_video_ads_id'  =>  $_POST['rewarded_video_ads_id'],
    'rewarded_video_click'  =>  $_POST['rewarded_video_click'],
    'facebook_interstitial_ad_id'  =>  $_POST['facebook_interstitial_ad_id'],
    'facebook_banner_ad_id'  =>  $_POST['facebook_banner_ad_id']
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: app_settings.php");
  exit;

}
else if(isset($_POST['api_submit']))
{

  $data = array
  (
    'api_page_limit'  =>  trim($_POST['api_page_limit']),
    'api_latest_limit'  => '0',
    'api_cat_order_by'  =>  trim($_POST['api_cat_order_by']),
    'api_cat_post_order_by'  =>  trim($_POST['api_cat_post_order_by']),
    'api_all_order_by'  =>  trim($_POST['api_all_order_by']),
    'cat_show_home_limit'  =>  trim($_POST['cat_show_home_limit'])
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: app_settings.php");
  exit;   

}
else if(isset($_POST['app_submit']))
{

  $data = array(
    'package_name'  =>  trim($_POST['package_name']),
    'otp_status' => $_POST['otp_status'] ? 'true' : 'false',
    'default_youtube_url'  =>  trim($_POST['default_youtube_url']),
    'default_instagram_url'  =>  trim($_POST['default_instagram_url']),
    'auto_approve' => $_POST['auto_approve'] ? 'on' : 'off',
    'auto_approve_img' => $_POST['auto_approve_img'] ? 'on' : 'off',
    'auto_approve_gif' => $_POST['auto_approve_gif'] ? 'on' : 'off',
    'auto_approve_quote' => $_POST['auto_approve_quote'] ? 'on' : 'off',

    'user_video_upload_limit'  =>  trim($_POST['user_video_upload_limit']),
    'user_image_upload_limit'  =>  trim($_POST['user_image_upload_limit']),
    'user_gif_upload_limit'  =>  trim($_POST['user_gif_upload_limit']),
    'user_quotes_upload_limit'  =>  trim($_POST['user_quotes_upload_limit']),

    'video_upload_opt' => $_POST['video_upload_opt'] ? 'true' : 'false',
    'image_upload_opt' => $_POST['image_upload_opt'] ? 'true' : 'false',
    'gif_upload_opt' => $_POST['gif_upload_opt'] ? 'true' : 'false',
    'quotes_upload_opt' => $_POST['quotes_upload_opt'] ? 'true' : 'false',

    'video_file_size'  =>  trim($_POST['video_file_size']),
    'video_file_duration'  =>  trim($_POST['video_file_duration']),
    'image_file_size'  =>  trim($_POST['image_file_size']),
    'gif_file_size'  =>  trim($_POST['gif_file_size']),
    'delete_note'  =>  trim($_POST['delete_note']),
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location: app_settings.php");
  exit;
}
else if(isset($_POST['app_update_popup']))
{

  $data = array(
    'app_update_status'  =>  ($_POST['app_update_status']) ? 'true' : 'false',
    'app_new_version'  =>  trim($_POST['app_new_version']),
    'app_update_desc'  =>  addslashes(trim($_POST['app_update_desc'])),
    'app_redirect_url'  =>  trim($_POST['app_redirect_url']),
    'cancel_update_status'  =>  ($_POST['cancel_update_status']) ? 'true' : 'false',
  );

  $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

  $_SESSION['msg']="11";
  header("Location:app_settings.php");
  exit;
}

?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title">Alert Popup Setting</div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px">
        <!-- Nav tabs -->
        <ul hidden class="nav nav-tabs" role="tablist">
          <li role="presentation"><a href="#app_update_popup" aria-controls="app_update_popup" role="tab" data-toggle="tab">App Update Popup</a></li>
        </ul>

        <div class="tab-content">
       
          

          <div role="tabpanel" hidden class="tab-pane" id="admob_settings">
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="admob_settings" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                        <label class="col-md-3 control-label field_lable" style="margin-top:0;padding-top:0">Publisher ID <span><a id="button" href="#target-content5" class="lable_tooltip"></a></span>:-
                        </label>
                        <div class="col-md-8" style="margin-left:10px;">
                          <input type="text" name="publisher_id" id="publisher_id" value="<?php echo $settings_row['publisher_id'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-8 col-md-offset-3">                
                        
                        <div class="banner_ads_block">
                          <div class="banner_ad_item">
                            <label class="control-label">Banner Ads :-</label>
                            <div class="row toggle_btn">
                              <input type="checkbox" id="checked1" name="banner_ad" value="true" class="cbx hidden" <?php if($settings_row['banner_ad']=='true'){ echo 'checked';}?> />
                              <label for="checked1" class="lbl"></label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="col-md-4 control-label">Choose Banner Ad:-</label>
                              <div class="col-md-8">
                                <select name="banner_ad_type" id="banner_ad_type" class="select2">
                                  <option value="facebook" <?php if($settings_row['banner_ad_type']=='facebook'){?>selected<?php }?>>Facebook</option>
                                  <option value="admob" <?php if($settings_row['banner_ad_type']=='admob'){?>selected<?php }?>>Admob</option>               
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-md-4 control-label">Admob Banner ID:-</label>
                              <div class="col-md-8">
                                <input type="text" name="banner_ad_id" id="banner_ad_id" value="<?php echo $settings_row['banner_ad_id'];?>" class="form-control">
                              </div>
                            </div>   
                            <div class="form-group">
                              <label class="col-md-4 control-label mr_bottom20">Facebook Banner ID :-</label>
                              <div class="col-md-8">
                                <input type="text" name="facebook_banner_ad_id" id="facebook_banner_ad_id" value="<?php echo $settings_row['facebook_banner_ad_id'];?>" class="form-control">
                              </div>
                            </div>  
                          </div>
                        </div>        
                        <div class="banner_ads_block">
                          <div class="banner_ad_item">
                            <label class="control-label">Interstitial Ads :-</label>
                            <div class="row toggle_btn">
                              <input type="checkbox" id="checked" name="interstitial_ad" value="true" class="cbx hidden" <?php if($settings_row['interstitial_ad']=='true'){ echo 'checked';}?> />
                              <label for="checked" class="lbl"></label>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="col-md-4 control-label">Choose Interstitial Ad:-</label>
                              <div class="col-md-8">
                                <select name="interstitial_ad_type" id="interstitial_ad_type" class="select2">
                                  <option value="facebook" <?php if($settings_row['interstitial_ad_type']=='facebook'){?>selected<?php }?>>Facebook</option>
                                  <option value="admob" <?php if($settings_row['interstitial_ad_type']=='admob'){?>selected<?php }?>>Admob</option>                
                                </select>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-md-4 control-label">Admob Interstitial ID:-</label>
                              <div class="col-md-8">
                                <input type="text" name="interstitial_ad_id" id="interstitial_ad_id" value="<?php echo $settings_row['interstitial_ad_id'];?>" class="form-control">
                              </div>
                            </div>   
                            <div class="form-group">
                              <label class="col-md-4 control-label">Facebook Interstitial ID :-</label>
                              <div class="col-md-8">
                                <input type="text" name="facebook_interstitial_ad_id" id="facebook_interstitial_ad_id" value="<?php echo $settings_row['facebook_interstitial_ad_id'];?>" class="form-control">
                              </div>
                            </div>  
                            <div class="form-group">
                              <label class="col-md-4 control-label">Interstitial Clicks :-</label>
                              <div class="col-md-8">
                                <input type="text" name="interstitial_ad_click" id="interstitial_ad_click" value="<?php echo $settings_row['interstitial_ad_click'];?>" class="form-control">
                              </div>
                            </div>  
                          </div> 
                        </div>
                      </div>
                      <div class="col-md-8 col-md-offset-3">
                        <div class="banner_ads_block">
                          <div class="banner_ad_item">
                            <label class="control-label">Rewarded Video Ads:-</label>
                            <div class="row toggle_btn">
                              <input type="checkbox" id="checked3" name="rewarded_video_ads" value="true" class="cbx hidden" <?php if($settings_row['rewarded_video_ads']=='true'){ echo 'checked';}?>/>
                              <label for="checked3" class="lbl"></label>
                            </div>
                          </div>
                          <div class="col-md-12">             
                            <div class="form-group" id="rewarded_video_ad_id">                              
                              <p class="field_lable">Rewarded Video Ad ID :-
                                
                              </p>
                              <div class="col-md-12">
                                <input type="text" name="rewarded_video_ads_id" id="rewarded_video_ads_id" value="<?php echo $settings_row['rewarded_video_ads_id'];?>" class="form-control">
                              </div>
                          
                            </div>                            
                          </div>
                        </div>
                      </div>
                      <div class="col-md-8 col-md-offset-3">
                        <button type="submit" name="admob_submit" class="btn btn-primary">Save</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="watermark_settings">
            <div class="rows">
              <div class="col-md-12">
                <form action="" name="settings_api" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                   
                        <div class="col-md-6">
                          <div class="fileupload_block">
                            <input type="file" name="watermark_image" id="fileupload">
                            <?php if($settings_row['watermark_image']!="") {?>
                              <div class="fileupload_img"><img type="image" src="images/<?php echo $settings_row['watermark_image'];?>" alt="image" style="width: 100px;height: 100px;" /></div>
                            <?php } else {?>
                              <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="image" /></div>
                            <?php }?>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="watermark_submit" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="api_settings">
            <div class="rows">
              <div class="col-md-12">   
                <form action="" name="settings_api" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                        <label class="col-md-3 control-label">Pagination Limit:-</label>
                        <div class="col-md-6">
                          <input type="number" name="api_page_limit" id="api_page_limit" value="<?php echo $settings_row['api_page_limit'];?>" class="form-control"> 
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-3 control-label">Category Show in Home Limit:-</label>
                        <div class="col-md-6">
                          <input type="number" onkeypress="isNumberKey(this)" min="0" name="cat_show_home_limit" id="cat_show_home_limit" value="<?php echo $settings_row['cat_show_home_limit'];?>" class="form-control"> 
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-3 control-label">Category List Order By:-</label>
                        <div class="col-md-6">
                          <select name="api_cat_order_by" id="api_cat_order_by" class="select2">
                            <option value="cid" <?php if($settings_row['api_cat_order_by']=='cid'){?>selected<?php }?>>ID</option>
                            <option value="category_name" <?php if($settings_row['api_cat_order_by']=='category_name'){?>selected<?php }?>>Name</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Category Status Order:-</label>
                        <div class="col-md-6">
                          <select name="api_cat_post_order_by" id="api_cat_post_order_by" class="select2">
                            <option value="ASC" <?php if($settings_row['api_cat_post_order_by']=='ASC'){?>selected<?php }?>>ASC</option>
                            <option value="DESC" <?php if($settings_row['api_cat_post_order_by']=='DESC'){?>selected<?php }?>>DESC</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">All Video Order:-</label>
                        <div class="col-md-6">
                          <select name="api_all_order_by" id="api_all_order_by" class="select2">
                            <option value="ASC" <?php if($settings_row['api_all_order_by']=='ASC'){?>selected<?php }?>>ASC</option>
                            <option value="DESC" <?php if($settings_row['api_all_order_by']=='DESC'){?>selected<?php }?>>DESC</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="api_submit" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- app update popup -->
          <div role="tabpanel" active class="tab-pane" id="app_update_popup">
            <div class="rows">
              <div class="col-md-12">   
                <form action="" name="app_update_popup" method="post" class="form form-horizontal" enctype="multipart/form-data">
                  <div class="section">
                    <div class="section-body">
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Update Popup Show/Hide:-
                        </label>
                        <div class="col-md-6">
                          <div class="row" style="margin-top: 15px">
                            <input type="checkbox" id="chk_update" name="app_update_status" value="true" class="cbx hidden" <?php if($settings_row['app_update_status']=='true'){ echo 'checked'; }?>/>
                            <label for="chk_update" class="lbl" style="left:13px;"></label>
                          </div>
                        </div>                   
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label field_lable">New App Version Code :-
                         
                        </label>
                        <div class="col-md-6">
                          <input type="number" min="1" name="app_new_version" id="app_new_version" required="" value="<?php echo $settings_row['app_new_version'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Description :-</label>
                        <div class="col-md-6">
                          <textarea name="app_update_desc" class="form-control"><?php echo $settings_row['app_update_desc'];?></textarea>
                        </div>
                      </div> 
                      <div class="form-group">
                        <label class="col-md-3 control-label">App Link :-
                        </label>
                        <div class="col-md-6">
                          <input type="text" name="app_redirect_url" id="app_redirect_url" required="" value="<?php echo $settings_row['app_redirect_url'];?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Cancel Option :-
                        </label>
                        <div class="col-md-6">
                          <div class="row" style="margin-top: 15px;margin-bottom:20px;">
                            <input type="checkbox" id="chk_cancel_update" name="cancel_update_status" value="true" class="cbx hidden" <?php if($settings_row['cancel_update_status']=='true'){ echo 'checked'; }?>/>
                            <label for="chk_cancel_update" class="lbl" style="left:13px;"></label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                          <button type="submit" name="app_update_popup" class="btn btn-primary">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- end app update popup -->          
    
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php");?> 
<script type="text/javascript">
  function removeHash () { 
	history.pushState("", document.title, window.location.pathname
	 + window.location.search);
  }

  $(".close").on("click",function(e){
	removeHash();
	location.reload();
  });  

  $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
	localStorage.setItem('activeTab', $(e.target).attr('href'));
	document.title = $(this).text()+" | <?=APP_NAME?>";
  });

  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
	$('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  $("#interstitial_ad_click").blur(function(e){
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