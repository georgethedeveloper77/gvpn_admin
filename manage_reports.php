<?php  
  
  $page_title="Manage Reports";
  include("includes/header.php");
  include("includes/connection.php");
  require("includes/function.php");
  require("language/language.php");

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

  function total_reports($post_id,$type='video')
  {
    global $mysqli;

    $query="SELECT COUNT(*) AS total_reports FROM willdev_reports WHERE `post_id`='$post_id' AND `report_type`='$type'";
    $sql = mysqli_query($mysqli,$query) or die(mysqli_error());
    $row=mysqli_fetch_assoc($sql);
    
    return stripslashes($row['total_reports']);
  }
  
?>

<style type="text/css">
  .morecontent span {
      display: none;
  }
  .morelink {
      display: block;
  }
</style>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title">Users Interaction</div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px"> 

        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px">
            <li role="presentation"><a href="manage_comments.php" aria-controls="comments"><i class="fa fa-comments"></i> Comments</a></li>
            <li role="presentation" class="active"><a href="manage_reports.php" aria-controls="reports"><i class="fa fa-bug"></i> Reports</a></li> 
        </ul>

        <div class="col-md-12 mrg-top manage_comment_btn">
          <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#video" aria-controls="video" role="tab" data-toggle="tab">Video</a></li>
                <li role="presentation"><a href="#image" aria-controls="image" role="tab" data-toggle="tab">Image</a></li>
                <li role="presentation"><a href="#gif" aria-controls="gif" role="tab" data-toggle="tab">GIF</a></li>
                <li role="presentation"><a href="#quote" aria-controls="quote" role="tab" data-toggle="tab">Quote</a></li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="video">
                <button class="btn btn-danger btn_cust btn_delete_all" data-type="video" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th style="width:40px">
                        <div class="checkbox" style="margin: 0px">
                          <input type="checkbox" name="checkall" id="checkall" value="">
                          <label for="checkall"></label>
                        </div>
                      </th> 
                      <th>Video Title</th>  
                      <th>Total Reports</th>
                      <th>Last Report</th>
                      <th class="cat_action_list" style="width:60px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $tableName="willdev_reports";    
                      $targetpage = "manage_reports.php";
                      $limit = 20; 
                      
                      $query = "SELECT * FROM $tableName WHERE `report_type`='video' GROUP BY `post_id`";
                      $total_pages = mysqli_num_rows(mysqli_query($mysqli,$query));
                      
                      $stages = 3;
                      $page=0;
                      if(isset($_GET['page'])){
                        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
                      }
                      if($page){
                        $start = ($page - 1) * $limit; 
                      }else{
                        $start = 0; 
                      } 
                      
                      $sql="SELECT  report.`id`,  report.`post_id`, max(report.`report_on`) as  report_on FROM willdev_reports report
                        WHERE   report.`report_type`='video'
                        GROUP BY  report.`post_id`
                        ORDER BY  report.`id` DESC LIMIT $start, $limit"; 
                       
                      $result=mysqli_query($mysqli,$sql);

                      if($result->num_rows > 0)
                      {
                      $i=0;
                      while($row=mysqli_fetch_array($result))
                      {  
                  ?>
                    <tr>
                      <td> 
                        <div class="checkbox">
                          <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row['post_id']; ?>" class="post_ids">
                          <label for="checkbox<?php echo $i;?>"></label>
                        </div>
                      </td>
                      <td><?=ucfirst(get_single_info($row['post_id'],'video_title','video'))?></td>
                      <td><a href="view_reports.php?post_id=<?=$row['post_id']?>"><?=total_reports($row['post_id'],'video')?> Reports</a></td>
                      <td><?=calculate_time_span($row['report_on'])?></td>
                      <td> 
                          <a href="" data-id="<?=$row['post_id']?>" data-type="video" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete All
                          </a>
                      </td>
                    </tr>
                   <?php    
                      $i++;
                      }
                    }
                    else{
                      ?>
                      <tr>
                        <td colspan="5">
                          <p class="text-muted text-center" style="font-size: 16px">No records available</p>
                        </td>
                      </tr>
                      <?php
                    }
                   ?>
                  </tbody>
                </table>
              </div>

              <!-- For image status -->
              <div role="tabpanel" class="tab-pane" id="image">
                <button class="btn btn-danger btn_cust btn_delete_all" data-type="image" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th style="width:40px">
                          <div class="checkbox" style="margin: 0px">
                        <input type="checkbox" name="checkall" id="checkall" value="">
                        <label for="checkall"></label>
                      </div>
                      </th> 
                      <th>Image Title</th>  
                      <th>Total Reports</th>
                      <th>Last Report</th>
                      <th class="cat_action_list" style="width:60px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $tableName="willdev_reports";    
                      $targetpage = "manage_reports.php";
                      $limit = 20; 
                      
                      $query = "SELECT * FROM $tableName WHERE `report_type`='image' GROUP BY `post_id`";
                      $total_pages = mysqli_num_rows(mysqli_query($mysqli,$query));
                      
                      $stages = 3;
                      $page=0;
                      if(isset($_GET['page'])){
                        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
                      }
                      if($page){
                        $start = ($page - 1) * $limit; 
                      }else{
                        $start = 0; 
                      } 
                      
                      $sql="SELECT  report.`id`,  report.`post_id`, max(report.`report_on`) as  report_on FROM willdev_reports report
                        WHERE   report.`report_type`='image'
                        GROUP BY  report.`post_id`
                        ORDER BY  report.`id` DESC LIMIT $start, $limit"; 
                     
                      $result=mysqli_query($mysqli,$sql);

                      if($result->num_rows > 0)
                      {
                      $i=0;
                      while($row=mysqli_fetch_array($result))
                      {  
                  ?>
                    <tr>
                      <td> 
                        <div class="checkbox">
                          <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row['post_id']; ?>" class="post_ids">
                          <label for="checkbox<?php echo $i;?>"></label>
                        </div>
                      </td>
                      <td><?=ucfirst(get_single_info($row['post_id'],'image_title','image'))?></td>
                      <td><a href="view_image_reports.php?post_id=<?=$row['post_id']?>&type=image"><?=total_reports($row['post_id'],'image')?> Reports</a></td>
                      <td><?=calculate_time_span($row['report_on'])?></td>
                      <td> 
                          <a href="" data-id="<?=$row['post_id']?>" data-type="image" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete All
                          </a>
                      </td>
                    </tr>
                   <?php    
                        $i++;
                      }
                    }
                    else{
                      ?>
                      <tr>
                        <td colspan="5">
                          <p class="text-muted text-center" style="font-size: 16px">No records available</p>
                        </td>
                      </tr>
                      <?php
                    }
                   ?>
                  </tbody>
                </table>
              </div>

              <!-- For gif status -->
              <div role="tabpanel" class="tab-pane" id="gif">
                <button class="btn btn-danger btn_cust btn_delete_all" data-type="gif" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th style="width:40px">
                          <div class="checkbox" style="margin: 0px">
                        <input type="checkbox" name="checkall" id="checkall" value="">
                        <label for="checkall"></label>
                      </div>
                      </th> 
                      <th>GIF Title</th>  
                      <th>Total Reports</th>
                      <th>Last Report</th>
                      <th class="cat_action_list" style="width:60px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $tableName="willdev_reports";    
                      $targetpage = "manage_reports.php";
                      $limit = 20; 
                      
                      $query = "SELECT * FROM $tableName WHERE `report_type`='gif' GROUP BY `post_id`";
                      $total_pages = mysqli_num_rows(mysqli_query($mysqli,$query));
                      
                      $stages = 3;
                      $page=0;
                      if(isset($_GET['page'])){
                        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
                      }
                      if($page){
                        $start = ($page - 1) * $limit; 
                      }else{
                        $start = 0; 
                      } 
                      
                      $sql="SELECT  report.`id`,  report.`post_id`, max(report.`report_on`) as  report_on FROM willdev_reports report
                        WHERE   report.`report_type`='gif'
                        GROUP BY  report.`post_id`
                        ORDER BY  report.`id` DESC LIMIT $start, $limit"; 
                     
                      $result=mysqli_query($mysqli,$sql);
                      $i=0;

                      if($result->num_rows > 0)
                      {
                      while($row=mysqli_fetch_array($result))
                      {  
                  ?>
                    <tr>
                    <td> 
                      <div class="checkbox">
                        <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row['post_id']; ?>" class="post_ids">
                        <label for="checkbox<?php echo $i;?>"></label>
                      </div>
                    </td>
                    <td><?=ucfirst(get_single_info($row['post_id'],'image_title','gif'))?></td>
                    <td><a href="view_image_reports.php?post_id=<?=$row['post_id']?>&type=gif"><?=total_reports($row['post_id'],'gif')?> Reports</a></td>
                    <td><?=calculate_time_span($row['report_on'])?></td>
                    <td> 
                        <a href="" data-id="<?=$row['post_id']?>" data-type="gif" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete All
                        </a>
                    </td>
                    </tr>
                   <?php    
                        $i++;
                      } // end of while loop
                    }
                    else{
                      ?>
                      <tr>
                        <td colspan="5">
                          <p class="text-muted text-center" style="font-size: 16px">No records available</p>
                        </td>
                      </tr>
                      <?php
                    }
                   ?>
                  </tbody>
                </table>
              </div>

              <!-- For quote status -->
              <div role="tabpanel" class="tab-pane" id="quote">
                <button class="btn btn-danger btn_cust btn_delete_all" data-type="quote" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                        <th style="width:40px">
                            <div class="checkbox" style="margin: 0px">
                          <input type="checkbox" name="checkall" id="checkall" value="">
                          <label for="checkall"></label>
                        </div>
                      </th> 
                      <th>Quote</th>  
                      <th nowrap="">Total Reports</th>
                      <th nowrap="">Last Report</th>
                      <th class="cat_action_list" style="width:60px">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $tableName="willdev_reports";    
                      $targetpage = "manage_reports.php";
                      $limit = 20; 
                      
                      $query = "SELECT * FROM $tableName WHERE `type`='quote' GROUP BY `post_id`";
                      $total_pages = mysqli_num_rows(mysqli_query($mysqli,$query));
                      
                      $stages = 3;
                      $page=0;
                      if(isset($_GET['page'])){
                        $page = mysqli_real_escape_string($mysqli,$_GET['page']);
                      }
                      if($page){
                        $start = ($page - 1) * $limit; 
                      }else{
                        $start = 0; 
                      } 
                      
                      $sql="SELECT  report.`id`,  report.`post_id`, max(report.`report_on`) as  report_on FROM willdev_reports report
                        WHERE report.`report_type`='quote'
                        GROUP BY  report.`post_id`
                        ORDER BY  report.`id` DESC LIMIT $start, $limit";
                      
                      $result=mysqli_query($mysqli,$sql);

                      if($result->num_rows > 0)
                      {
                      $i=0;
                      while($row=mysqli_fetch_array($result))
                      {  
                  ?>
                    <tr>
                    <td> 
                      <div class="checkbox">
                        <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row['post_id']; ?>" class="post_ids">
                        <label for="checkbox<?php echo $i;?>"></label>
                      </div>
                    </td>
                    <td>
                      <span class="more">
                        <?=get_single_info($row['post_id'],'quote','quote')?>
                      </span>
                    </td>
                    <td><a href="view_quotes_reports.php?post_id=<?=$row['post_id']?>"><?=total_reports($row['post_id'],'quote')?> Reports</a></td>
                    <td><?=calculate_time_span($row['report_on'])?></td>
                    <td> 
                        <a href="" data-id="<?=$row['post_id']?>" data-type="quote" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete All
                        </a>
                    </td>
                    </tr>
                   <?php    
                    $i++;
                    }
                  }
                  else{
                    ?>
                    <tr>
                      <td colspan="5">
                        <p class="text-muted text-center" style="font-size: 16px">No records available</p>
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
          <div class="col-md-12 col-xs-12">
            <div class="pagination_item_block">
              <nav>
                <?php if(!isset($_POST["search"])){ include("pagination.php");}?>                 
              </nav>
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

  });
  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  $('a[data-toggle="tab"]').click(function(e){

    var uri = window.location.toString();
    if (uri.indexOf("?") > 0) {
        var clean_uri = uri.substring(0, uri.indexOf("?"));
        window.history.replaceState({}, document.title, clean_uri);
    }
    location.reload();
  });



  // for multiple deletes
  $(".btn_delete_all").click(function(e){
    var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });
    var type=$(this).data('type');
    if(_ids!='')
    {
      swal({
            title: "Are you sure to delete?",
            text: "Reports of that records will be deleted !",
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
                data:{post_id:_ids,'action':'removeAllReports','type':type},
                success:function(res){
                    console.log(res);
                    if(res.status=='1'){
                      swal({
                        title: "Successfully", 
                        text: "Reports are deleted...", 
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
      swal("Sorry no records selected !!")
    }
  });


  // for single comment row delete
  $(".btn_delete").click(function(e){
      e.preventDefault();
      var _id = $(this).data('id');
      var type=$(this).data('type');
      if(_id!='')
      {
        swal({
              title: "Are you sure to delete?",
              text: "Reports of this records will be deleted !",
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
                  data:{post_id:_id,'action':'removeReport','type':type},
                  success:function(res){
                      console.log(res);
                      if(res.status=='1'){
                        swal({
                          title: "Successfully", 
                          text: "Reports are deleted...", 
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
        swal("Sorry no records selected !!")
      }
  });


  // for read more

  $(document).ready(function() {
      // Configure/customize these variables.
      var showChar = 80;  // How many characters are shown by default
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
   
      $(".morelink").click(function(){
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