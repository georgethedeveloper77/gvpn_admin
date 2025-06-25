<?php 
include('includes/header.php');
include_once("../database/config_DB.php");
$is_edit = false;
$admobID =  "";
$bannerID =  "";
$rewardID =  "";
$interstitialID =  "";
$nativeID =  "";
$adType =  "";
$activeAd =  "";
$id = 0;

if(isset($_GET['edit']))
{
  $is_edit = true;
  $id = $_GET['edit'];
  $query = $DBcon->query("SELECT * FROM willdev_admobconfig WHERE id='".$_GET['edit']."'");
  $row=$query->fetch_array();
  $count = $query->num_rows; 
  $admobID =  $row['admobID'];
  $bannerID =  $row['bannerID'];
  $interstitialID =  $row['interstitialID'];
  $rewardID =  $row['rewardID'];
  $nativeID =  $row['nativeID'];
  $adType =  $row['adType'];
  $activeAd =  $row['activeAd'];
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
      
      
      <div class="content ">
        <div class="container-fluid ">

       <div class="col-md-12">

              <div class="card">
                <div class="card-header card-header-primary">
<h4 class="card-title"><?php if($is_edit){ echo "Edit Ads Config"; }else{ echo "Add Ads Config"; }?></h4>
                </div>
                <div class="card-body">
                  <form method="POST" action="includes/routes.php">
                    <div class="row">					  
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">App Id </label>
                          <input type="text" value="<?php echo $admobID; ?>" name="admobID" class="form-control" >
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Banner Ads ID</label>
                          <input type="text" value="<?php echo $bannerID; ?>" name="bannerID" class="form-control">
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Interstitial Ads ID</label>
                          <input type="text" value="<?php echo $interstitialID; ?>" name="interstitialID" class="form-control">
                        </div>
                      </div>


                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Native Ads ID</label>
                          <input type="text"  value="<?php echo $nativeID; ?>"name="nativeID" class="form-control">
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="bmd-label-floating">Reward Ads ID</label>
                          <input type="text" value="<?php echo $rewardID; ?>" name="rewardID" class="form-control">
                        </div>
                      </div>

				<div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Ads Type</label>
                            <select name="adType" class="form-control">
                              <option value="ADMOB" <?php if($adType=="ADMOB"){echo "selected";} ?>>AdMob</option>
                              <option value="FACEBOOK_ADS"  <?php if($adType=="FACEBOOK_ADS"){echo "selected";} ?>>Facebook Ads</option>
                              
                              
                              <option value="STARTAPP" <?php if($adType=="STARTAPP"){echo "selected";} ?>>StartApp</option>
                              <option value="MPV"  <?php if($adType=="MPV"){echo "selected";} ?>>Mopub Ads</option>
                              <option value="UNV" <?php if($adType=="UNV"){echo "selected";} ?>>Unity</option>
                              <option value="APV"  <?php if($adType=="APV"){echo "selected";} ?>>Applovin</option>
                              <option value="APPODEAL" <?php if($adType=="APPODEAL"){echo "selected";} ?>>Appodeal</option>
                             
                              </select>
                        </div>
                      </div>
					  
                      <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">ActiveAd</label>
                            <select name="activeAd" class="form-control">
                              <option value="1" <?php if($activeAd==1){echo "selected";} ?>>Active</option>
                              <option value="0"  <?php if($activeAd==0){echo "selected";} ?>>Not Active</option>
                            </select>
                        </div>
                      </div>
                      <input type="text" value="<?php echo $id; ?>" name="id" style="display:none" class="form-control">

                    </div>

                    <br>
                    <br>
                    <br>
                    <button type="submit" name="<?php if($is_edit){echo "editAds";}else{echo "addAds";}?>" class="btn btn-primary pull-right"><?php if($is_edit){echo "Submit";}else{echo "Submit";}?></button>
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