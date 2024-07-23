<?php 
include('includes/header.php')
?>
<body style="margin-top: 100px!important; overflow:hidden!important">
  <div class="wrapper">
      <div class="content" >
      <?php
          if(isset($_GET['status'])){
            if(strcmp($_GET['status'],'success') == 0){
        ?>
        <div class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
            <b> Success - </b> <?php echo $_GET['message']; ?></span>
        </div>
        <?php 
            }else{
              ?>
              <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
                </button>
                <span>
                  <b> Error - </b> <?php echo $_GET['message']; ?></span>
              </div>              
              <?php
            }
          }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-4 ">

          <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Login</h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="includes/api.php">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Username</label>
                          <input type="text" name="userName" class="form-control">
                        </div>
                      </div>


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Passowrd</label>
                          <input type="text" name="password" class="form-control">
                        </div>
                      </div>

                    </div>
                    <button type="submit" name="login" class="btn btn-primary pull-right">Login</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
              </div>
            </div>
</body>

</html>