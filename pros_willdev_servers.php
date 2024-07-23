<?php 
include('includes/header.php');
if(isset($_GET['delete'])){
    if($query = $DBcon->query("DELETE FROM `willdev_servers` WHERE `willdev_servers`.`id` =".$_GET['delete'])){
      header('Location:index.php?status=success&message=Server deleted succesful');
    }else{
        echo $DBcon->error;

        header('Location:index.php?status=error&message=Error can\'t delete server');
    }
}
?>
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
                  <h4 class="card-title ">List of Pro Servers</h4>
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
                        <th hidden>
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
                      $query = $DBcon->query("SELECT * FROM willdev_servers WHERE isFree = 0");
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
                          <td hidden>
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
                              <a type="button" rel="tooltip" title="Edit Server" href="add_willdev_servers.php?edit=<?php echo $row['id']?>" class="btn btn-primary">
                                <i class="material-icons">edit</i>
                              </a>
                               <a type="button" rel="tooltip" title="Delete Server" href="pros_willdev_servers.php?delete=<?php echo $row['id']?>" class="btn btn-danger">
                                <i class="material-icons">close</i>
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

</html>