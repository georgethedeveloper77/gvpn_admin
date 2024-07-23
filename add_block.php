<?php 

$page_title="Add Spinner Block";

include("includes/header.php");

require("includes/function.php");
require("language/language.php");
require_once("thumbnail_images.class.php");

if(isset($_POST['submit']))
{

 extract($_POST);

 $block_points=addslashes(trim($block_points));
 $block_bg=addslashes(trim($block_bg));

 $sql="SELECT * FROM willdev_spinner WHERE (block_bg = '$block_bg' OR `block_points`=$block_points)";
 $res=mysqli_query($mysqli, $sql);

 if(mysqli_num_rows($res) == 0){
  $data = array( 
    'block_points'  =>  $_POST['block_points'],
    'block_bg'  =>  $_POST['block_bg']
  );    

  $qry = Insert('willdev_spinner',$data); 

  $_SESSION['class']='success';            
  $_SESSION['msg']="10";

  if(isset($_GET['redirect'])){
    header("Location:".$_GET['redirect']);
  }
  else{
    header( "Location:add_block.php");
  }
  exit; 
}else
{

  $_SESSION['class']='warn';
  $_SESSION['msg']="Background color OR Block Points is already exist !";

  if(isset($_GET['redirect'])){
    header("Location:".$_GET['redirect']);
  }
  else{
    header( "Location:add_block.php");
  }
  exit;
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
        echo '<a href="spinner.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
      }
    ?>
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title">Add Spinner Block</div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data">

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Block Points :-</label>
                <div class="col-md-6">
                  <input type="number" min="0" name="block_points" id="block_points" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Block Background :-</label>
                <div class="col-md-6">
                  <input value="e91e63" name="block_bg" class="form-control jscolor {width:243, height:150, position:'right',
                  borderColor:'#000', insetColor:'#FFF', backgroundColor:'#ddd'}">
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

<?php include("includes/footer.php");?>   


<script type="text/javascript" src="assets/js/jscolor.js"></script>   
