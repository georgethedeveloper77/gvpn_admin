<?php 
    $page_title="Dashboard";
    include("includes/header.php");
    include("includes/function.php");

    $query = $DBcon->query("SELECT * FROM willdev_servers");
    $total = mysqli_num_rows($query);
    $query = $DBcon->query("SELECT * FROM willdev_servers WHERE isFree = 1");
    $free = mysqli_num_rows($query);
    $query = $DBcon->query("SELECT * FROM willdev_servers WHERE isFree = 0");
    $pro = mysqli_num_rows($query);

    $sql="SELECT COUNT(*) as num FROM willdev_language";
    $total_languages= mysqli_fetch_array(mysqli_query($mysqli,$sql));
    $total_languages = $total_languages['num'];

    $qry_cat="SELECT COUNT(*) as num FROM willdev_category";
    $total_category= mysqli_fetch_array(mysqli_query($mysqli,$qry_cat));
    $total_category = $total_category['num'];

    $qry_video="SELECT COUNT(*) as num FROM willdev_video";
    $total_video = mysqli_fetch_array(mysqli_query($mysqli,$qry_video));
    $total_video = $total_video['num'];


    $qry_users="SELECT COUNT(*) as num FROM willdev_users WHERE id <> 0";
    $total_users = mysqli_fetch_array(mysqli_query($mysqli,$qry_users));
    $total_users = $total_users['num'];

    $sql_img="SELECT COUNT(*) as num FROM willdev_img_status WHERE `status_type`='image'";
    $total_img = mysqli_fetch_array(mysqli_query($mysqli,$sql_img));
    $total_img = $total_img['num'];

    $sql_gif="SELECT COUNT(*) as num FROM willdev_img_status WHERE `status_type`='gif'";
    $total_gif = mysqli_fetch_array(mysqli_query($mysqli,$sql_gif));
    $total_gif = $total_gif['num'];

    $sql_quotes="SELECT COUNT(*) as num FROM willdev_quotes";
    $total_quotes = mysqli_fetch_array(mysqli_query($mysqli,$sql_quotes));
    $total_quotes = $total_quotes['num'];

    $sql_verify="SELECT COUNT(*) as num FROM willdev_verify_user varify_u, willdev_users user WHERE varify_u.`user_id`=user.`id` AND varify_u.`status`='0' ORDER BY varify_u.`id` DESC";

    $total_verify = mysqli_fetch_array(mysqli_query($mysqli,$sql_verify));
    $total_verify = $total_verify['num'];


    $qry="SELECT * FROM willdev_settings where id='1'";
    $result=mysqli_query($mysqli,$qry);
    $settings_row=mysqli_fetch_assoc($result);


    $qry_users_paid="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
                    LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
                    WHERE willdev_users_redeem.`status` = 1";
                    $total_paid = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_paid));
                    $total_paid = $total_paid['num'];

    $qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
                  LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`=willdev_users.`id`
                  WHERE willdev_users_redeem.`status`= 0";
                  $total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
                  $total_pending = $total_pending['num'];


    $countStr='';

    $no_data_status=false;
    $count=$monthCount=0;

    for ($mon=1; $mon<=12; $mon++) {

        if(date('n') < $mon){
          break;
        }

        if(isset($_GET['filterByYear'])){

          $year=$_GET['filterByYear'];
          $month = date('M', mktime(0,0,0,$mon, 1, $year));

          $sql_user="SELECT `id` FROM willdev_users WHERE `registration_on` <> 0 AND DATE_FORMAT(FROM_UNIXTIME(`registration_on`), '%c') = '$mon' AND DATE_FORMAT(FROM_UNIXTIME(`registration_on`), '%Y') = '$year'";

          $totalcount=mysqli_num_rows(mysqli_query($mysqli, $sql_user));

          $verified_user="SELECT `id` FROM willdev_verify_user WHERE `verify_at` <> 0 AND `status`='1' AND DATE_FORMAT(FROM_UNIXTIME(`verify_at`), '%c') = '$mon' AND DATE_FORMAT(FROM_UNIXTIME(`verify_at`), '%Y') = '$year'";

          $verifycount=mysqli_num_rows(mysqli_query($mysqli, $verified_user));

          $suspend_user="SELECT `id` FROM willdev_suspend_account WHERE `status`='1' AND DATE_FORMAT(FROM_UNIXTIME(`suspended_on`), '%c') = '$mon' AND DATE_FORMAT(FROM_UNIXTIME(`suspended_on`), '%Y') = '$year'";

          $suspendcount=mysqli_num_rows(mysqli_query($mysqli, $suspend_user));

        }
        else{
          $month = date('M', mktime(0,0,0,$mon, 1, date('Y')));

          $sql_user="SELECT `id` FROM willdev_users WHERE `registration_on` <> 0 AND DATE_FORMAT(FROM_UNIXTIME(`registration_on`), '%c') = '$mon'";

          $totalcount=mysqli_num_rows(mysqli_query($mysqli, $sql_user));

          $verified_user="SELECT `id` FROM willdev_verify_user WHERE `verify_at` <> 0 AND `status`='1' AND DATE_FORMAT(FROM_UNIXTIME(`verify_at`), '%c') = '$mon'";

          $verifycount=mysqli_num_rows(mysqli_query($mysqli, $verified_user));

          $suspend_user="SELECT `id` FROM willdev_suspend_account WHERE `status`='1' AND DATE_FORMAT(FROM_UNIXTIME(`suspended_on`), '%c') = '$mon'";

          $suspendcount=mysqli_num_rows(mysqli_query($mysqli, $suspend_user));
        }


        $countStr.="['".$month."', ".$totalcount.", ".$verifycount.", ".$suspendcount."], ";

        if($totalcount!=0 || $verifycount!=0 || $suspendcount!=0){
          $monthCount++;
        }
    }

    if($monthCount!=0){
      $no_data_status=false;
    }
    else{
      $no_data_status=true;
    }

    $countStr=rtrim($countStr, ", ");

    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }
?>

<style type="text/css">
  .table > tbody, .table > tbody > tr, .table > tbody > tr > td{
    display: block !important;
  }
</style>       

<div class="row">
  <div class="clearfix"></div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12"> 
    <a href="total_willdev_servers.php" class="card card-banner card-green-light">
      <div class="card-body"> <i class="icon fa fa-list-alt"></i>
        <div class="content">
          <div class="title">Total Servers List</div>
          <div class="value"><span class="sign"></span><?php echo $total;?></div>
        </div>
        
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12"> 
    <a href="free_willdev_servers.php" class="card card-banner card-skyeblue-light">
      <div class="card-body"> <i class="icon fa fa-list-alt"></i>
        <div class="content">
          <div class="title">Free Servers List</div>
          <div class="value"><span class="sign"></span><?php echo $free;?></div>
        </div>
      </div>
    </a> 
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12"> <a href="pros_willdev_servers.php" class="card card-banner card-alicerose-light">
    <div class="card-body"> <i class="icon fa fa-list-alt"></i>
    <div class="content">
      <div class="title">Premium Servers List</div>
      <div class="value"><span class="sign"></span><?php echo $pro;?></div>
    </div>
    </div>
    </a> 
  </div> 
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 2px;">
      <div class="col-lg-10">
      <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 mr_bot60"> 
    <a href="manage_users.php" class="card card-banner card-yellow-light">
      <div class="card-body"> <i class="icon fa fa-list-alt"></i>
        <div class="content">
          <div class="title">Total Users</div>
          <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_users);?></span></div>
        </div>
      </div>
    </a> 
  </div>
      </div>
      <div class="col-lg-2" style="padding-top: 20px">
        <form method="get" id="graphFilter">
          <select class="form-control" name="filterByYear" style="box-shadow: none;height: auto;border-radius: 0px;font-size: 16px;">
            <?php 
              $currentYear=date('Y');
              $minYear=2018;

              for ($i=$currentYear; $i >= $minYear ; $i--) { 
                ?>
                <option value="<?=$i?>" <?=(isset($_GET['filterByYear']) && $_GET['filterByYear']==$i) ? 'selected' : ''?>><?=$i?></option>
                <?php
              }
            ?>
          </select>
        </form>
      </div>
      <div class="col-lg-12">
        <?php 
          if($no_data_status){
            ?>
            <h3 class="text-muted text-center" style="padding-bottom: 2em">No data found !</h3>
            <?php
          }
          else{
            ?>
            <div id="registerChart">
              <p style="text-align: center;"><i class="fa fa-spinner fa-spin" style="font-size:3em;color:#aaa;margin-bottom:50px" aria-hidden="true"></i></p>
            </div>
            <?php    
          }
        ?>
      </div>
    </div>
  </div>
</div>




        
<?php include("includes/footer.php");?>

<?php 
  if(!$no_data_status){
    ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {packages: ['corechart', 'line']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = new google.visualization.DataTable();
        data.addColumn('string', '');
        data.addColumn('number', '');
        data.addColumn('number', '');
        data.addColumn('number', '');

        data.addRows([<?=$countStr?>]);

        var options = {
          curveType: 'function',
          fontSize: 15,
          hAxis: {
            title: "Months of <?=(isset($_GET['filterByYear'])) ? $_GET['filterByYear'] : date('Y')?>",
            titleTextStyle: {
              color: '#000',
              bold:'true',
              italic: false
            },
          },
          vAxis: {
            title: "Nos. of Users",
            titleTextStyle: {
              color: '#000',
              bold:'true',
              italic: false,
            },
            gridlines: { count: -1},
            format: '#',
            viewWindowMode: "explicit", viewWindow: {min: 0, max: 'auto'},
          },
          height: 400,
          chartArea:{
            left:50,top:20,width:'100%',height:'auto'
          },
          legend: {
              position: 'bottom'
          },
          colors: ['#3366CC', 'green','red'],
          lineWidth:4,
          animation: {
            startup: true,
            duration: 1200,
            easing: 'out',
          },
          pointSize: 5,
          pointShape: "circle",

        };
        var chart = new google.visualization.LineChart(document.getElementById('registerChart'));

        chart.draw(data, options);
      }


      $(document).ready(function () {
          $(window).resize(function(){
              drawChart();
          });
      });
    </script>
    <?php
  }
?>
<script type="text/javascript">
    
    // filter of graph
    $("select[name='filterByYear']").on("change",function(e){
      $("#graphFilter").submit();
    });

</script>       
