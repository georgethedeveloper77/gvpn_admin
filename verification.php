<?php   
  
    $page_title="Verify Purchase";

    include("includes/connection.php");
    include("includes/header.php");
    require("includes/function.php");
    require("language/language.php");
   
    $qry="SELECT * FROM willdev_settings where id='1'";
    $result=mysqli_query($mysqli,$qry);
    $settings_row=mysqli_fetch_assoc($result);

    if(isset($_POST['verify_purchase_submit']))
    {
        $data = array
                (
                  'envato_buyer_name' => $_POST['envato_buyer_name'],
                  'envato_purchase_code' => $_POST['envato_purchase_code'],
                  'envato_buyer_email' => '-',
                  'package_name' => trim($_POST['package_name'])
                );
  
        $settings_edit=Update('willdev_settings', $data, "WHERE id = '1'");

        $_SESSION['class']="success";
        $_SESSION['msg']="11";
        header( "Location:verification.php");
        exit;
    }

?>


<head>
  <meta name="author" content="">
  <meta name="description" content="">
  <meta http-equiv="Content-Type"content="text/html;charset=UTF-8"/>
  <meta name="viewport"content="width=device-width, initial-scale=1.0">
  <title> <?php if(isset($page_title)){ echo $page_title.' | '.APP_NAME; }else{ echo APP_NAME; } ?></title>
  <link rel="icon" href="images/<?php echo APP_LOGO;?>" sizes="16x16">
  <link rel="stylesheet" type="text/css" href="assets/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="assets/css/flat-admin.css">

  <!-- Theme -->
  <link rel="stylesheet" type="text/css" href="assets/css/theme/blue-sky.css">
  <link rel="stylesheet" type="text/css" href="assets/css/theme/blue.css">
  <link rel="stylesheet" type="text/css" href="assets/css/theme/red.css">
  <link rel="stylesheet" type="text/css" href="assets/css/theme/yellow.css">

  <link rel="stylesheet" type="text/css" href="assets/sweetalert/sweetalert.css">

  <script src="assets/ckeditor/ckeditor.js"></script>

  <?php 
  if(!empty($css_files)){
    foreach ($css_files as $key => $value) {
      echo '<link rel="stylesheet" type="text/css" href="'.$value.'">';
    }
  }

  if(!empty($js_files)){
    foreach ($js_files as $key => $value) {
      echo '<script type="text/javascript" src="'.$value.'"></script>';
    }

  }
  ?>

  <style type="text/css">
    .btn_edit, .btn_delete, .btn_cust{
      padding: 5px 10px !important;
      font-size: 12px !important; 
    }

    /*--------- for sweet alerts --------*/
    .sweet-alert h2 {
      font-size: 24px;
      line-height: 28px;
      font-weight: 500
    }
    .sweet-alert .lead{
      font-size: 18px; 
      font-weight: 400
    }
    .sweet-alert .btn{
      min-width: 70px !important;
      padding: 8px 12px !important;
      border: 0 !important;
      height: auto !important;
      margin: 0px 3px !important;
      box-shadow: none !important;
      font-size: 15px;
    }
    .sweet-alert .sa-icon {
      margin: 0 auto 15px auto !important;
    }

    .social_img{
      width: 20px !important;
      height: 20px !important;
      position: absolute;
      top: -11px;
      z-index: 1;
      left: 40px;
      margin:5px;
    }

    .control-label-help{
      color: red !important;
    }

    .dropdown-li{
      margin-bottom: 0px !important;
    }
    .cust-dropdown-container{
      background: #E7EDEE;
      display: none;
    }
    .cust-dropdown{
      list-style: none;
      background: #eee;
    }
    .cust-dropdown li a{
      padding: 8px 0px;
      width: 100%;
      display: block;
      color: #444;
      float: left;
      text-decoration: none;
      transition: all linear 0.2s;
      font-weight: 500;
    }
    .cust-dropdown li a:hover{
      color: #1ee92b;
    }

    .cust-dropdown li a.active{
      color: #1ee92b;
    }

  </style>


</head>

   <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title"><?=$page_title?></div>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="card-body mrg_bottom">
          
              <form action="" name="verify_purchase" method="post" class="form form-horizontal" enctype="multipart/form-data">
                <input type="hidden" class="current_tab" name="current_tab">
                <div class="section">
                <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-4 control-label">Username :-
                      
                    </label>
                    <div class="col-md-6">
                      <input type="text" name="envato_buyer_name"  id="envato_buyer_name" value="<?php echo $settings_row['envato_buyer_name'];?>" class="form-control" placeholder="demo">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Purchase Code

                    </label>
                    <div class="col-md-6">
                      <input type="text" name="envato_purchase_code"  id="envato_purchase_code" value="<?php echo $settings_row['envato_purchase_code'];?>" class="form-control" placeholder="xxxx-xxxx-xxxx-xxxx-xxxx">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-4 control-label">Package Name :-
                    </label>
                    <div class="col-md-6">
                      <input type="text" name="package_name" id="package_name" value="<?php echo $settings_row['package_name'];?>" class="form-control" placeholder="com.example.myapp">
                    </div>
                  </div>
                   
                  <div class="form-group">
                  <div class="col-md-9 col-md-offset-4">
                    <button type="submit" name="verify_purchase_submit" class="btn btn-primary">Save</button>
                  </div>
                  </div>
                </div>
                </div>

              </form>
              <br/>
              
          </div>
        </div>
      </div>
    </div>

        
<?php include("includes/footer.php");?> 
