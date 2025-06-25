<?php 

  $page_title=(isset($_GET['user_id'])) ? 'Edit Language' : 'Add Language';
  $active_page="user";

  include("includes/header.php");
  include("includes/connection.php");

  include("includes/function.php");
  include("language/language.php");
  include("language/app_language.php"); 
  
  $qryCountry = $DBcon->query("SELECT iso,nicename FROM willdev_country");
  
  if(isset($_POST['submit']) and isset($_GET['add']))
  {

    if (!isset($_POST['words_json']) && !isset($_POST['title']))
    {
        $_SESSION['class']="warn";
        $_SESSION['msg']="All fields are required";

        if(isset($_GET['redirect']))
        {
          header("Location:add_language.php?add=yes&redirect".$_GET['redirect']);
        }
        else{
          header("Location:add_language.php?add=yes");
        }
        exit;
    }
    else
    {
    $flag = addslashes(trim($_POST['flag']));
    $country = explode (",", $flag); 

    $data = array(
      'name' => addslashes(trim($_POST['title'])),
      'direction' =>  addslashes(trim($_POST['direction'])),
      'json' => $_POST['words_json'],
      'flag' => "flag/" .strtolower($country[0]). ".png",
      'country' => $country[1],
      'status' =>  addslashes(trim($_POST['status'])),
    );  

    $qry = Insert('willdev_app_language',$data);

    $_SESSION['class']="success";
    $_SESSION['msg']="10";

    header("location:language.php");   
    exit;
  }
}

  if(isset($_GET['id']))
  {
    $redeem_qry="SELECT * FROM willdev_app_language WHERE id='".$_GET['id']."'";
    $lang_result=mysqli_query($mysqli,$redeem_qry);
    $lang_row=mysqli_fetch_assoc($lang_result);	
  }

  if(isset($_POST['submit']) and isset($_POST['id']))
  {
      
        $id = $_POST['id'];
          
        if (!isset($_POST['words_json']) && !isset($_POST['title']))
        {
            $_SESSION['class']="warn";
            $_SESSION['msg']="All fields are required";
    
            if(isset($_GET['redirect']))
            {
              header("Location:add_language.php?add=yes&redirect".$_GET['redirect']);
            }
            else{
              header("Location:add_language.php?add=yes");
            }
            exit;
        }
        else
        {

        $flag = addslashes(trim($_POST['flag']));
        $country = explode (",", $flag); 
    
        $data = array(
          'name' => addslashes(trim($_POST['title'])),
          'direction' =>  addslashes(trim($_POST['direction'])),
          'json' => $_POST['words_json'],
          'flag' => "flag/" .strtolower($country[0]). ".png",
          'country' => $country[1],
          'status' =>  addslashes(trim($_POST['status'])),
        );
        
        
        $qry = Update('willdev_app_language', $data, "WHERE id = $id");
    
        $_SESSION['class']="success";
        $_SESSION['msg']="10";
    
        header("location:language.php");   
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
        echo '<a href="redemption_code.php" class="btn_back"><h4 class="pull-left" style="font-size: 20px;color: #1ee92b"><i class="fa fa-arrow-left"></i> Back</h4></a>';
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
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="id" value="<?php echo $_GET['id'];?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Name: </label>
                <div class="col-md-6">
                  <input type="text" name="title" id="title" value="<?php echo ($lang_row['name'] != null) ? $lang_row['name'] : ''; ?>" class="form-control" required>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 control-label">Direction: </label>
                <div class="col-md-6">
                    <select name="direction" class="form-control select2 filter">
					    <option value="LTR" <?php if($lang_row['direction']=='LTR'){ echo 'selected';} ?>>LTR (Left-to-Right)</option>
			            <option value="RTL" <?php if($lang_row['direction']=='RTL'){ echo 'selected';} ?>>RTL (Right-to-Left)</option>
				    </select>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 control-label">Words in JSON: </label>
                <div class="col-md-6">
                  <textarea rows="20" type="number" name="words_json" id="words_json" class="form-control" required><?php echo ($lang_row['json'] != null) ? stripslashes($lang_row['json']) : '{&#013;&nbsp;&nbsp;&nbsp;&quot;home&quot;:&quot;Home&quot;,&#013;&nbsp;&nbsp;&nbsp;&quot;back&quot;:&quot;Back&quot;&#013;}'; ?></textarea>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-md-3 control-label">Flag Image :-
                </label>
                <div class="col-md-6">
                  <select name="flag" class="form-control select2 filter">
            	  	    <?php
            	  	        while($rowCountry = $qryCountry->fetch_array()) {
            	  	            $selected = $rowCountry['nicename'] == $lang_row['country'] ? "selected" : "";
            	  	            echo "<option value='" .$rowCountry['iso'].",".$rowCountry['nicename']. "' $selected>" .$rowCountry['nicename']. "</option>";
            	  	        }
            	  	    ?>
            	  	</select>
                </div>
              </div>
           

              <div class="form-group">
                <label class="col-md-3 control-label">Status: </label>
                <div class="col-md-6">
                    <select name="status" class="form-control select2 filter">
						<option value="0" <?php if($redeem_row['status']==0){ echo 'selected';} ?> >Inactive</option>
				        <option value="1" <?php if($redeem_row['status']==1){ echo 'selected';} ?> >Active</option>
					</select>
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

<script>


$("#fileupload").change(function(e) {
    var file, img;
    var thisFile=$(this);

    var countCheck=0;

    if ((file = this.files[0])) {
      img = new Image();

      img.onload = function() {
        if(this.width < this.height || this.width > this.height)
        {
          swal({title: 'Warning!',text: 'Error', type: 'warning'});
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


<?php include('includes/footer.php');?>

