<?php 
include('includes/header.php');
$is_edit = false;
$serverName = "";
$server = "";
$country = "United States";
$flagURL = "";
$ovpnConfiguration =  "";
$vpnUserName =  "";
$vpnPassword =  "";
$isFree =  "";
$id = 0;

$qryCountry = $DBcon->query("SELECT nicename FROM willdev_country");

if(isset($_GET['edit'])){
  $is_edit = true;
  $id = $_GET['edit'];
  $query = $DBcon->query("SELECT * FROM willdev_servers WHERE id='".$_GET['edit']."'");
  $row=$query->fetch_array();
  $count = $query->num_rows; 
  $serverName =  $row['serverName'];
  $flagURL =  $row['flagURL'];
  $ovpnConfiguration =  $row['ovpnConfiguration'];
  $vpnPassword =  $row['vpnPassword'];
  $isFree =  $row['isFree'];
  
  $username = $row['vpnUserName'];
 
  if (strpos($username, '@') > -1) {
      
    $pos = strpos($username, '@');
    $username = substr($username,0,$pos);
    $vpnUserName = $username;
  } else {
    $vpnUserName = $row['vpnUserName'];
  }
  
  $server = $row['server'];
  $country = $row['country'];
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.alert {
  padding: 20px;
  background-color: #4571F5D4;
  color: #ffffff;
}

.closebtn {
  margin-left: 15px;
  color: #000000;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.8s;
}

.closebtn:hover {
  color: black;
}

    
      @keyframes blinking {
        0% {
          background-color: #4571F5D4;
        }
        100% {
          background-color: #4571F5D4;
        }
      }
      #blink {
        animation: blinking 1s infinite;
      }
    
</style>
<body>
<div class="wrapper ">
    <?php include('includes/sidenav.php')?>
    <div class="main-panel">
      <!-- Navbar -->
      <?php include("includes/navbar.php")?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Servers Table -->
            <div class="col-md-12">
              <div class="card">
              
                <div class="card-header card-header-primary">
<h4 class="card-title"><?php if($is_edit){ echo "Edit Server"; }else{ echo "Add Server"; }?></h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="includes/routes.php">
                    <div class="row">
                    
                    <div class="col-md-12">
                    	<div class="form-group">
                    	  
                    	  	<select name="server_option" hidden >
                    	  		<option value="1" <?php echo $server == 1 ? "selected" : ""; ?>>Oneconnect.top Server</option>
                    	  		<option value="0" <?php echo $server == 0 ? "selected" : ""; ?>>Another Server File</option>
                    	  	</select>
                    	  	     <div class="alert"  >
  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
<p>If you want to use <a href="https://oneconnect.top/"><span style="color: rgb(255, 255, 255);">Onceconnect.top</span></a> Server, <strong>Y</strong><strong>ou Just need to Put OneConnect API Key inside Setting and Enable OneConnect Server,</strong> Then Inside app Server will Load Via OneConnect SDK <a href="https://developer.oneconnect.top/packages/"><span style="color: rgb(255, 255, 255);">https://developer.oneconnect.top/</span></a> <strong>Then the Server List Will Show Automatically inside App Via OneConnect Key Or </strong>If you want to Use Your Own Country Server file, Just Put your Country Server file details below.<strong><br></strong></p></div>
                    </div>
                    

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Country Name</label>
                          <input type="text" value="<?php echo $serverName; ?>" name="serverName" class="form-control">
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Select Country Flag</label>
                          <br>
                    	  	<select name="country" class="form-control">
                    	  	    <?php
                    	  	        while($rowCountry = $qryCountry->fetch_array()) {
                    	  	            $selected = $rowCountry['nicename'] == $country ? "selected" : "";
                    	  	            echo "<option value='" .$rowCountry['nicename']. "' $selected>" .$rowCountry['nicename']. "</option>";
                    	  	        }
                    	  	    ?>
                    	  	</select>
                    	  	<br>
                        </div>
                      </div>


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">OVPN Configuration File</label>
                          <textarea name="ovpn"  class="form-control"><?php echo $ovpnConfiguration; ?></textarea>
                        </div>
                      </div>


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">VPN Username</label>
                          <input type="text"  value="<?php echo $vpnUserName; ?>"name="vpnUsername" class="form-control">
                        </div>
                      </div>


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">VPN Password</label>
                          <input type="text"  value="<?php echo $vpnPassword; ?>" name="vpnPassword" class="form-control">
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Select Server List Type :-  Free List / Pro List</label>
                            <select name="isFree" class="form-control">
                              <option value="free" <?php if($isFree==1){echo "selected";} ?>>Free User</option>
                              <option value="pro"  <?php if($isFree==0){echo "selected";} ?>>Pro User</option>
                            </select>
                        </div>
                      </div>
                      <input type="text" value="<?php echo $id; ?>" name="id" style="display:none" class="form-control">

                    </div>

                    <br>
                   <br>
                   
                      <button style="text-align: center;" type="submit" name="<?php if($is_edit){echo "editServer";}else{echo "addServer";}?>" class="btn btn-primary pull-right"><?php if($is_edit){echo "Submit";}else{echo "Submit";}?></button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
          </div>
            
        </div>
      </div>
    <?php include('./includes/footer.php')?>
</body>

</html>