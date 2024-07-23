<?php 
  $page_title="Lucky Wheel";

  include("includes/header.php");
  require("includes/function.php");
  require("language/language.php");

  //Get all spinner blocks 
  $qry="SELECT * FROM willdev_spinner";
  $result=mysqli_query($mysqli,$qry);
  
  if(isset($_POST['btn_spinner_otn'])){

      $spinner_opt=$ad_on_spin='';
      if(isset($_POST['spinner_opt'])){
        $spinner_opt='true';
      }else{
        $spinner_opt='false';
      }

      if(isset($_POST['ad_on_spin'])){
        $ad_on_spin='true';
      }else{
        $ad_on_spin='false';
      }

      $data = array
      (
        'spinner_opt' => $spinner_opt,
        'ad_on_spin' => $ad_on_spin,
        'spinner_limit' => addslashes(trim($_POST['spinner_limit'])),
      );

      $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");
      

      $_SESSION['msg']="11";
      header( "Location:spinner.php");
      exit;

  }

  $qry="SELECT * FROM willdev_settings where id='1'";
  $result1=mysqli_query($mysqli,$qry);
  $settings_row=mysqli_fetch_assoc($result1);

   
?>

<style type="text/css">
  span.select2{
    margin-bottom: 0px;
  }
</style>
                
    <div class="row">
      <div class="col-xs-12">
        <div class="card mrg_bottom">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title">Lucky Wheel</div>
            </div>
            <div class="col-md-7 col-xs-12">
              <div class="search_list">
                <div class="add_btn_primary"> <a href="add_block.php?redirect=<?=$redirectUrl?>">Add Lucky Wheel Block</a> </div>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="col-md-12 mrg-top">
            <form class="form-inline" action="" method="post">
             
              <div class="form-group col-md-3">
                <div class="row toggle_btn" style="margin-top: 0px;margin-left:0;top: 9px">
                  <p style="float:left;font-weight: 600">Ads. on every spin or not: &nbsp;&nbsp;</p>
                  <input type="checkbox" id="chk_ad_on_spin" class="cbx hidden" name="ad_on_spin" value="true" <?php if($settings_row['ad_on_spin']=='true'){?>checked <?php }?>/>
                  <label for="chk_ad_on_spin" class="lbl" style="top:2px;float:left"></label>
                </div>
              </div>
              <div class="form-group col-md-3">
                <label for="spinner_limit">Users Per day Limit:&nbsp;&nbsp;</label>
                <input type="number" min="1" class="form-control" id="spinner_limit" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="spinner_limit" style="padding-top: 8px;padding-bottom: 8px;padding-left: 15px;padding-right: 15px;width: 80px;margin-bottom: 0px" value="<?=$settings_row['spinner_limit']?>">
              </div>
              <button type="submit" name="btn_spinner_otn" class="btn btn-primary" style="margin-bottom: 0px !important;padding-top: 8px;padding-bottom: 8px;padding-left: 15px;padding-right: 15px;">Save</button>
              
            </form>
            <div class="col-md-12">
              <br/>
              
            </div>
            
            <div class="clearfix"></div>
            <hr/>
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>                  
                  <th>Block Point</th>
                  <th>Background Color</th>
                  <th class="cat_action_list">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $i=0;
                  while($row=mysqli_fetch_array($result))
                  {
                ?>
                <tr>                 
                  <td><?php echo $row['block_points'];?></td>
                  <td>
                    <div style="width: 100px;height: 35px;border-radius: 5px;background: #<?php echo $row['block_bg'];?>"></div>
                  </td>
                  <td>
                      <a href="edit_block.php?block_id=<?php echo $row['block_id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-primary btn_edit"  data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a>
                      <a href="javascript:void(0)" data-id="<?php echo $row['block_id'];?>" data-toggle="tooltip" data-tooltip="Delete" class="btn btn-danger btn_cust btn_delete_a"><i class="fa fa-trash"></i></a>
                  </td>
                </tr>
                <?php
            
                $i++;
                }
              ?> 
              </tbody>
            </table>
          </div>
           
          <div class="clearfix"></div>
        </div>
      </div>
    </div>

<?php include("includes/footer.php");?>  

<script type="text/javascript">
  $("#spinner_limit").keyup(function(e){
    if($(this).val()<=0){
      $(this).val('1');
    }
  });

  $(".btn_delete_a").on("click", function(e) {

    e.preventDefault();

    var _id = $(this).data("id");
    var _table = 'willdev_spinner';

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
