<?php 
include('includes/header.php');
if(isset($_GET['delete'])){
    if($query = $DBcon->query("DELETE FROM `servers` WHERE `servers`.`id` =".$_GET['delete'])){
      header('Location:index.php?status=success&message=Server deleted succesful');
    }else{
        echo $DBcon->error;

        header('Location:index.php?status=error&message=Error can\'t delete server');
    }
}

if(isset($_GET['delete_ads'])){
    if($query = $DBcon->query("DELETE FROM `willdev_admobconfig` WHERE `willdev_admobconfig`.`id` =".$_GET['delete_ads'])){
      header('Location:index.php?status=success&message=Ads config deleted succesful');
    }else{
        echo $DBcon->error;

        header('Location:index.php?status=error&message=Error can\'t delete ads config');
    }
}
?>
<body>
  <div class="wrapper ">
    
       
          
          

            <!-- Servers Table -->
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">List of Servers</h4>
                  <p class="card-category"> Server configuration information</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>
                          #
                        </th>
                        <th>
                          Server Name
                        </th>
                        <th>
                          Flag URL
                        </th>
                        <th>
                          OVPN Config.
                        </th>
                        <th>
                          VPN Username
                        </th>
                        <th>
                          VPN Password
                        </th>
                      
                        <th></th>
                        <th></th>
                      </thead>
                      <tbody>
                      <?php 
                      $query = $DBcon->query("SELECT * FROM willdev_servers");
                      $count = mysqli_num_rows($query);
                      $i = 1;
                      if($count > 0){
                      while ($row = $query->fetch_assoc()) {
                    ?>
                        <tr  class="configuration">
                          <td>
                            <?php echo $i;?>
                          </td>
                          <td>
                            <?php echo $row['serverName'];?>
                          </td>
                          <td>
                          <?php echo $row['flagURL'];?>
                          </td>
                          <td>
                          <?php echo substr($row['ovpnConfiguration'],0,45);?>
                          </td>
                         <td>
                          <?php 
                          
                              $username = $row['vpnUserName'];
 
                              if (strpos($username, '@') > -1) {
                                  
                                $pos = strpos($username, '@');
                                $username = substr($username,0,$pos);
                                echo $username;
                              } else {
                                echo $row['vpnUserName'];
                              }
                          
                          ?>
                          </td>
                          <td>
                          <?php echo $row['vpnPassword'];?>
                          </td>
                         <td class="td-actions text-right">
                              <a type="button" rel="tooltip" title="Edit Server" href="add_willdev_servers.php?edit=<?php echo $row['id']?>" class="btn btn-primary btn-link btn-sm">
                                <i class="material-icons">edit</i>
                              </a>
                            
                            </td>
                        </tr>
                        <?php 
                        $i++;
                      }
                    }else{
                      echo "<tr><td>No server saved!</td></tr>";
                    }
                      ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    <?php include('./includes/footer.php')?>
</body>



<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.alert {
  padding: 20px;
  background-color: #ffd117;
  color: #000000;
}

.closebtn {
  margin-left: 15px;
  color: #000000;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>


</html>