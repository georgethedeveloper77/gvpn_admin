<?php 
include('includes/header.php');
$is_edit = false;
$name = "";
$description = "";
$product_id = "";
$price = "";
$currency =  "";
$description =  "";
$status =  "";


if(isset($_GET['edit'])){
  $is_edit = true;
  $id = $_GET['edit'];
  $query = $DBcon->query("SELECT * FROM willdev_subscription WHERE id='".$_GET['edit']."'");
  $row=$query->fetch_array();
  $count = $query->num_rows; 

    $name = $row['name'];
    $description = $row['description'];
    $product_id = $row['product_id'];
    $price = $row['price'];
    $currency =  $row['currency'];
    $description =  $row['description'];
    $status =  $row['status'];
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
<h4 class="card-title"><?php if($is_edit){ echo "Edit Subscription"; }else{ echo "Add Subscription"; }?></h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="includes/routes.php">
                    <div class="row">
                    

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Name</label>
                          <input type="text" value="<?php echo $name; ?>" name="name" class="form-control">
                        </div>
                      </div>
                      
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Product ID</label>
                          <input type="text" value="<?php echo $product_id; ?>" name="product_id" class="form-control">
                        </div>
                      </div>
                      
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Price</label>
                          <input type="text" value="<?php echo $price; ?>" name="price" class="form-control">
                        </div>
                      </div>
                      
                      
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Currency</label>
                          <input type="text" value="<?php echo $currency; ?>" name="currency" class="form-control">
                        </div>
                      </div>
                      
                      

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Status</label>
                          <select name="status" class="form-control">
                            <option value="1" <?php echo ($status == 1) ? "selected" : ""; ?>>Active</option>
                            <option value="0" <?php echo ($status == 0) ? "selected" : ""; ?>>Inactive</option>
                          </select>
                        </div>
                      </div>
        
                      <input type="hidden" value="<?php echo $id; ?>" name="id" class="form-control">

                    </div>

                    <br>
                    <br>
                    <br>
                    <button type="submit" name="<?php if($is_edit){echo "editSubscription";}else{echo "addSubscription";}?>" class="btn btn-primary pull-right"><?php if($is_edit){echo "Submit";}else{echo "Submit";}?></button>
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