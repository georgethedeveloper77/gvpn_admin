<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php echo $product_info['product_name']; ?> - Installer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
    <style type="text/css">
      body, html {
        background: #F7F7F7;
      }
      .control-label-help{
        font-weight: 500;
        font-size: 14px;
      }
    </style>
  </head>
  <body>
    <?php
      $errors = false;
      $step = isset($_GET['step']) ? $_GET['step'] : '';
    ?>
    <div class="container"> 
      <div class="section">
        <div class="column is-6 is-offset-3">
          <center>
            <h1 class="title" style="padding-top: 20px"><?php echo $product_info['product_name']; ?> Installer</h1><br>
          </center>
          <div class="box">
            <?php
            switch ($step) {
              default: ?>
                <div class="tabs is-fullwidth">
                  <ul>
                    <li class="is-active">
                      <a>
                        <span><b>Requirements</b></span>
                      </a>
                    </li>
                   
                  </ul>
                </div>
                <?php  
               
                if(phpversion() < "7.2"){
                  $errors = true;
                  echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Current PHP version is ".phpversion()."! minimum PHP 7.2 or higher required.</div>";
                }else{
                  echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Recomended PHP Version is 7.4, But You are Running on ".phpversion()."</div>";
                }
                if(!extension_loaded('mysqli')){
                  $errors = true; 
                  echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> MySQLi PHP extension missing!</div>";
                }else{
                  echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> MySQLi PHP extension available</div>";
                } 
                if(!extension_loaded('curl')){
                  $errors = true; 
                echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> Curl PHP extension missing!</div>";
                }else{
                  echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> Curl PHP extension available</div>";
                }
                if(!extension_loaded('pdo')){
                  $errors = true; 
                echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> PDO PHP extension missing!</div>";
                }else{
                  echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> PDO PHP extension available</div>";
                }
                if(!extension_loaded('json')){
                  $errors = true; 
                  echo "<div class='notification is-danger' style='padding:12px;'><i class='fa fa-times'></i> JSON PHP extension missing!</div>";
                }else{
                  echo "<div class='notification is-success' style='padding:12px;'><i class='fa fa-check'></i> JSON PHP extension available</div>";
                }
                

                ?>

                <div style='text-align: right;'>
                  <?php if($errors==true){ ?>
                  <a href="#" class="button is-link" disabled>Next</a>
                  <?php }else{ ?>
                  <a href="index.php?step=0" class="button is-link">Next</a>
                  <?php } ?>
                </div><?php
                break;
              case "0": ?>
                <div class="tabs is-fullwidth">
                  <ul>
              
                    <li class="is-active">
                      <a>
                        <span><b>Verify</b></span>
                      </a>
                    </li>
                   
                  </ul>
                </div>
                <?php
                  $license_code = null;
                  $client_name = null;
                  if(!empty($_POST['license'])&&!empty($_POST['client'])){
                    $license_code = strip_tags(trim($_POST["license"]));
                    $client_name = strip_tags(trim($_POST["client"]));
                    /* Once we have the license code and client's name we can use LicenseBoxAPI's activate_license() function for activating/installing the license, if the third parameter is empty a local license file will be created which can be used for background license checks. */
                    $activate_response = $api->activate_license($license_code,$client_name);

                    $_SESSION['envato_buyer_name']=$client_name;
                    $_SESSION['envato_purchase_code']=$license_code;

                    if(empty($activate_response)){
                      $msg='Server is unavailable.';
                    }else{

                      $msg=$activate_response['message'];
                    }
                    if($activate_response['status'] != true){ ?>
                      <form action="index.php?step=0" method="POST">
                        <div class="notification is-danger"><?php echo ucfirst($msg); ?></div>
                        <div class="field">
                          <label class="label">Envato Username
                              <p class="control-label-help">https://codecanyon.net/user/<u style="color: #1ee92b">example</u></p>
                              <p class="control-label-help">(<u style="color: #1ee92b">example</u> is username, Write your envato <u style="color: #1ee92b">username</u>)</p>
                          </label>
                          <div class="control">
                            <input class="input" type="text" placeholder="Your Envato User Name" name="client" required>
                          </div>
                        </div>
                        <div class="field">
                          <label class="label">Envato Purchase Code :-
                            <p class="control-label-help">(<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank">Where Is My Purchase Code?</a>)</p>
                          </label>
                          <div class="control">
                            <input class="input" type="text" placeholder="xxxx-xxxx-xxxx-xxxx-xxxx" name="license" required>
                          </div>
                        </div>
                        
                        <div style='text-align: right;'>
                          <button type="submit" class="button is-link">Verify</button>
                        </div>
                      </form><?php
                    }else{ ?>
                      <form action="index.php?step=1" method="POST">
                        <div class="notification is-success"><?php echo ucfirst($msg); ?></div>
                        <input type="hidden" name="lcscs" id="lcscsindex.php?" value="<?php echo ucfirst($activate_response['status']); ?>">
                        <div style='text-align: right;'>
                          <button type="submit" class="button is-link">Next</button>
                        </div>
                      </form><?php
                    }
                  }else{ ?>
                    <form action="index.php?step=0" method="POST">
                      <div class="field">
                        <label class="label">Envato Username
                            <p class="control-label-help">https://codecanyon.net/user/<u style="color: #1ee92b">example</u></p>
                            <p class="control-label-help">(<u style="color: #1ee92b">example</u> is username, Write your envato <u style="color: #1ee92b">username</u>)</p>
                        </label>
                        <div class="control">
                          <input class="input" type="text" placeholder="Your Envato User Name" name="client" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Envato Purchase Code :-
                          <p class="control-label-help">(<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank">Where Is My Purchase Code?</a>)</p>
                        </label>
                        <div class="control">
                          <input class="input" type="text" placeholder="xxxx-xxxx-xxxx-xxxx-xxxx" name="license" required>
                        </div>
                      </div>
                      
                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Verify</button>
                      </div>
                    </form>
                  <?php } 
                break;
              case "1": ?>
                <div class="tabs is-fullwidth">
                  <ul>
                 
                    <li class="is-active">
                      <a>
                        <span><b>Database</b></span>
                      </a>
                    </li>
                    
                  </ul>
                </div>
                <?php
                  if($_POST && isset($_POST["lcscs"])){
                    $valid = strip_tags(trim($_POST["lcscs"]));
                    $db_host = strip_tags(trim($_POST["host"]));
                    $db_user = strip_tags(trim($_POST["user"]));
                    $db_pass = strip_tags(trim($_POST["pass"]));
                    $db_name = strip_tags(trim($_POST["name"]));
                    $base_urls = strip_tags(trim($_POST["baseurl"]));
                    $base_code = strip_tags(trim($_POST["basecode"]));
                    $map_keys = strip_tags(trim($_POST["mapkeys"]));
                    $firdb_urls = strip_tags(trim($_POST["firedburls"]));
                    $fcm_keys = strip_tags(trim($_POST["fcmkeys"]));
                    if(!empty($db_host)){
                      $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                      if(mysqli_connect_errno()){ ?>
                        <form action="index.php?step=1" method="POST">
                          <div class='notification is-danger'>Failed to connect to MySQL: <?php echo mysqli_connect_error(); ?></div>
                          <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                          <div class="field">
                            <label class="label">Database Host</label>
                            <div class="control">
                              <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Username</label>
                            <div class="control">
                              <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                            </div>
                          </div>
                          
                          
                          <div class="field">
                            <label class="label">Database Password</label>
                            <div class="control">
                              <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                            </div>
                          </div>
                          <div class="field">
                            <label class="label">Database Name</label>
                            <div class="control">
                              <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                            </div>
                          </div>
                           
                           <div class="field">
                            <label class="label">Enter Your Site Url </label>
                            <div class="control">
                              <input class="input" type="text" id="baseurl" placeholder="https://yourdomain.com" name="baseurl" required>
                            </div>
                          </div>




                          <div style='text-align: right;'>
                            <button type="submit" class="button is-link">Import</button>
                          </div>
                        </form><?php
                        exit;
                      }
                      $templine = '';
                      $lines = file($filename);
                      foreach($lines as $line){
                        if(substr($line, 0, 2) == '--' || $line == '')
                          continue;
                        $templine .= $line;
                        $query = false;
                        if(substr(trim($line), -1, 1) == ';'){
                          $query = mysqli_query($con, $templine);
                          $templine = '';
                        }
                      }

                      $dataFile = "../includes/connection.php";
                      $fhandle = fopen($dataFile,"r");
                      $content = fread($fhandle,filesize($dataFile));

                      $content = str_replace('db_name', $db_name, $content);

                      $content = str_replace('db_uname', $db_user, $content);

                      $content = str_replace('db_password', $db_pass, $content);

                      $content = str_replace('db_hname', $db_host, $content);

                      $fhandle = fopen($dataFile,"w");
                      fwrite($fhandle,$content);
                      fclose($fhandle);

   
                      $dataFile = "../database/config_DB.php";
                      $fhandle = fopen($dataFile,"r");
                      $content = fread($fhandle,filesize($dataFile));

                      $content = str_replace('db_name', $db_name, $content);

                      $content = str_replace('db_uname', $db_user, $content);

                      $content = str_replace('db_password', $db_pass, $content);

                      $content = str_replace('db_hname', $db_host, $content);

                      $fhandle = fopen($dataFile,"w");
                      fwrite($fhandle,$content);
                      fclose($fhandle);
                      
                

                       
                      $dataFile = "../includes/header.php";
                      $fhandle = fopen($dataFile,"r");
                      $content = fread($fhandle,filesize($dataFile));

                      $content = str_replace('base_urls', $base_urls, $content);
                    
                      $fhandle = fopen($dataFile,"w");
                      fwrite($fhandle,$content);
                      fclose($fhandle);

            

                
              
                        mysqli_close($con);

                      $config_file_default    = "https://doc.willdev.in/willninelaunch/rotsrv.default";
                      $config_file_name       = "../includes/routes.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }
                      
                      
                      mysqli_close($con);
                    
                      $config_file_default    = "https://doc.willdev.in/willninelaunch/instl.default";
                      $config_file_name       = "../install/index.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }


                      $config_file_default    = "https://doc.willdev.in/willninelaunch/indx.default";
                      $config_file_name       = "../index.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }



                      $config_file_default    = "https://doc.willdev.in/willninelaunch/editsubscrip.default";
                      $config_file_name       = "../edit_willdev_subscription.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }

                      $config_file_default    = "https://doc.willdev.in/willninelaunch/subscrip.default";
                      $config_file_name       = "../willdev_subscription.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }
                      
                      
                      $config_file_default    = "https://doc.willdev.in/willninelaunch/redemptioncode.default";
                      $config_file_name       = "../redemption_code.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }

                      $config_file_default    = "https://doc.willdev.in/willninelaunch/addredemption.default";
                      $config_file_name       = "../add_redemption.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }

                      $config_file_default    = "https://doc.willdev.in/willninelaunch/active.default";
                      $config_file_name       = "../view_quotes_report.php";    

                      $config_file_path       = $config_file_name;

                      $config_file = file_get_contents($config_file_default);

                      $f = @fopen($config_file_path, "w+");

                      if(@fwrite($f, $config_file) > 0){
                      }
                     

                    ?>
                    <form action="index.php?step=2" method="POST">
                      <div class='notification is-success'>Database was successfully imported.</div>
                      <input type="hidden" name="dbscs" id="dbscs" value="true">
                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Next</button>
                      </div>
                    </form><?php
                  }else{ ?>
                    <form action="index.php?step=1" method="POST">
                      <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                      <div class="field">
                        <label class="label">Database Host</label>
                        <div class="control">
                          <input class="input" type="text" id="host" placeholder="enter your database host" name="host" required>
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Username</label>
                        <div class="control">
                          <input class="input" type="text" id="user" placeholder="enter your database username" name="user" required>
                        </div>
                      </div>
                          
                      <div class="field">
                        <label class="label">Database Password</label>
                        <div class="control">
                          <input class="input" type="text" id="pass" placeholder="enter your database password" name="pass">
                        </div>
                      </div>
                      <div class="field">
                        <label class="label">Database Name</label>
                        <div class="control">
                          <input class="input" type="text" id="name" placeholder="enter your database name" name="name" required>
                        </div>
                      </div>

                           <div class="field">
                            <label class="label">Enter Your Site Url </label>
                            <div class="control">
                              <input class="input" type="text" id="baseurl" placeholder="https://yourdomain.com" name="baseurl" required>
                            </div>
                          </div>
                          

                     


                      <div style='text-align: right;'>
                        <button type="submit" class="button is-link">Import</button>
                      </div>
                    </form><?php
                } 
              }else{ ?>
                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
              }
              break;
            case "2": ?>
              <div class="tabs is-fullwidth">
                <ul>
            
                  <li class="is-active">
                    <a>
                      <span><b>Finish</b></span>
                    </a>
                  </li>
                </ul>
              </div>
              <?php
              if($_POST && isset($_POST["dbscs"])){
                $valid = $_POST["dbscs"];
                session_destroy();
                ?>
                <center>
                  <p><strong><?php echo $product_info['product_name']; ?> is successfully installed.</strong></p><br>
                  <br>
                  <p>You can now login using your username: <strong>Will_VPN</strong> and default password: <strong>12345678</strong></p><br><strong>
                  <p><a class='button is-link' href='index.php'>Login</a></p></strong>
                  <br>
                  <p class='help has-text-grey'>The first thing you should do is change your account details.</p>
                </center>
                <?php
              }else{ ?>
                <div class='notification is-danger'>Sorry, something went wrong.</div><?php
              } 
            break;
          } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="content has-text-centered">
    <p>Copyright <?php echo date('Y'); ?> Will_Dev, All rights reserved.</p><br>
  </div>
</body>
</html>
 