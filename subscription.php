<?php 
include('includes/header.php');
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
            
            <!--<a type="button" rel="tooltip" title="Add Subscription" href="add_subscription.php" class="btn btn-primary btn-link btn-sm" style="background-color: #0000FF; color: #FFF; font-style: normal; float: right">
                                <i class="material-icons">Add Subscription</i>
                              </a>-->
            
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title ">All Subscription Plans</h4>
                  <p class="card-category"> Subscription information</p>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>
                          #
                        </th>
                        <th>
                          Name
                        </th>
                        <th>
                          Product ID
                        </th>
                        <th>
                          Price
                        </th>
                        <th>
                          Currency
                        </th>
                      
                        <th>
                          Status
                        </th>
                      
                        <th></th>
                        <th></th>
                      </thead>
                      <tbody>
                      <?php 
                      $query = $DBcon->query("SELECT * FROM willdev_subscription");
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
                            <?php echo $row['name'];?>
                          </td>
                          <td>
                          <?php echo $row['product_id'];?>
                          </td>
                          <td>
                          <?php echo $row['price'];?>
                          </td>
                          <td>
                          <?php echo $row['currency'];?>
                          </td>
                         
                          <td>
                          <?php echo ($row['status'] == 1) ? "Active" : "Inactive"; ?>
                          </td>
                          
                          <td class="td-actions text-right">
                              <a type="button" rel="tooltip" title="Edit Server" href="add_subscription.php?edit=<?php echo $row['id']?>" class="btn btn-primary btn-link btn-sm" style="background-color: #0000FF; color: #FFF; font-style: normal;">
                                <i class="material-icons">Edit</i>
                              </a>
                              <!--<a type="button" rel="tooltip" title="Delete Server" href="includes/routes.php?deleteSubscription=<?php echo $row['id']?>" class="btn btn-danger btn-link btn-sm" style="background-color: #0000FF; color: #FFF; font-style: normal;">
                                <i class="material-icons">Delete</i>
                              </a>-->
                            </td>
                        </tr>
                        <?php 
                        $i++;
                      }
                    }else{
                      echo "<tr><td>No subscription saved!</td></tr>";
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