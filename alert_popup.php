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

if(isset($_POST['app_update_popup']))
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