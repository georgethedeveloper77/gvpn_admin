<?php




ini_set('memory_limit', '256M');


#Admin Login
function adminUser($username, $password){

  global $mysqli;

  $sql = "SELECT id,username FROM willdev_admin where username = '".$username."' and password = '".md5($password)."'";       
  $result = mysqli_query($mysqli,$sql);
  $num_rows = mysqli_num_rows($result);

  if ($num_rows > 0){
    while ($row = mysqli_fetch_array($result)){
      echo $_SESSION['ADMIN_ID'] = $row['id'];
      echo $_SESSION['ADMIN_USERNAME'] = $row['username'];

      return true; 
    }
  }

}


# Insert Data 
function Insert($table, $data){

  global $mysqli;
    //print_r($data);

  $fields = array_keys( $data );  
  $values = array_map( array($mysqli, 'real_escape_string'), array_values( $data ) );

   //echo "INSERT INTO $table(".implode(",",$fields).") VALUES ('".implode("','", $values )."');";
   //exit;  
  mysqli_query($mysqli, "INSERT INTO $table(".implode(",",$fields).") VALUES ('".implode("','", $values )."');") or die( mysqli_error($mysqli) );

}

// Total Count
function CountRow($table_name, $where_clause='')
{   
  global $mysqli;
    // check for optional where clause
  $whereSQL = '';
  if(!empty($where_clause))
  {
        // check to see if the 'where' keyword exists
    if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
    {
            // not found, add keyword
      $whereSQL = " WHERE ".$where_clause;
    } else
    {
      $whereSQL = " ".trim($where_clause);
    }
  }
    // build the query
  $sql = "SELECT * FROM ".$table_name.$whereSQL;

    // run and return the query result resource
  $res=mysqli_query($mysqli,$sql);

  return mysqli_num_rows($res);
}  

// Update Data, Where clause is left optional
function Update($table_name, $form_data, $where_clause='')
{   
  global $mysqli;
    // check for optional where clause
  $whereSQL = '';
  if(!empty($where_clause))
  {
        // check to see if the 'where' keyword exists
    if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
    {
            // not found, add key word
      $whereSQL = " WHERE ".$where_clause;
    } else
    {
      $whereSQL = " ".trim($where_clause);
    }
  }
    // start the actual SQL statement
  $sql = "UPDATE ".$table_name." SET ";

    // loop and build the column /
  $sets = array();
  foreach($form_data as $column => $value)
  {
   $sets[] = "`".$column."` = '".$value."'";
 }
 $sql .= implode(', ', $sets);

    // append the where statement
 $sql .= $whereSQL;

    // run and return the query result
 return mysqli_query($mysqli,$sql);
}


//Delete Data, the where clause is left optional incase the user wants to delete every row!
function Delete($table_name, $where_clause='')
{   
  global $mysqli;
    // check for optional where clause
  $whereSQL = '';
  if(!empty($where_clause))
  {
        // check to see if the 'where' keyword exists
    if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
    {
            // not found, add keyword
      $whereSQL = " WHERE ".$where_clause;
    } else
    {
      $whereSQL = " ".trim($where_clause);
    }
  }
    // build the query
  $sql = "DELETE FROM ".$table_name.$whereSQL;

    // run and return the query result resource
  return mysqli_query($mysqli,$sql);
}

// Get base url
function getBaseUrl($array=false) {
  $protocol = "http";
  $host = "";
  $port = "";
  $dir = "";  

    // Get protocol
  if(array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] != "") {
    if($_SERVER["HTTPS"] == "on") { $protocol = "https"; }
    else { $protocol = "http"; }
  } elseif(array_key_exists("REQUEST_SCHEME", $_SERVER) && $_SERVER["REQUEST_SCHEME"] != "") { $protocol = $_SERVER["REQUEST_SCHEME"]; }

    // Get host
  if(array_key_exists("HTTP_X_FORWARDED_HOST", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_HOST"] != "") { $host = trim(end(explode(',', $_SERVER["HTTP_X_FORWARDED_HOST"]))); }
  elseif(array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] != "") { $host = $_SERVER["SERVER_NAME"]; }
  elseif(array_key_exists("HTTP_HOST", $_SERVER) && $_SERVER["HTTP_HOST"] != "") { $host = $_SERVER["HTTP_HOST"]; }
  elseif(array_key_exists("SERVER_ADDR", $_SERVER) && $_SERVER["SERVER_ADDR"] != "") { $host = $_SERVER["SERVER_ADDR"]; }
    //elseif(array_key_exists("SSL_TLS_SNI", $_SERVER) && $_SERVER["SSL_TLS_SNI"] != "") { $host = $_SERVER["SSL_TLS_SNI"]; }

    // Get port
  if(array_key_exists("SERVER_PORT", $_SERVER) && $_SERVER["SERVER_PORT"] != "") { $port = $_SERVER["SERVER_PORT"]; }
  elseif(stripos($host, ":") !== false) { $port = substr($host, (stripos($host, ":")+1)); }
    // Remove port from host
  $host = preg_replace("/:\d+$/", "", $host);

    // Get dir
  if(array_key_exists("SCRIPT_NAME", $_SERVER) && $_SERVER["SCRIPT_NAME"] != "") { $dir = $_SERVER["SCRIPT_NAME"]; }
  elseif(array_key_exists("PHP_SELF", $_SERVER) && $_SERVER["PHP_SELF"] != "") { $dir = $_SERVER["PHP_SELF"]; }
  elseif(array_key_exists("REQUEST_URI", $_SERVER) && $_SERVER["REQUEST_URI"] != "") { $dir = $_SERVER["REQUEST_URI"]; }
    // Shorten to main dir
  if(stripos($dir, "/") !== false) { $dir = substr($dir, 0, (strripos($dir, "/")+1)); }

    // Create return value
  if(!$array) {
    if($port == "80" || $port == "443" || $port == "") { $port = ""; }
    else { $port = ":".$port; } 
    return htmlspecialchars($protocol."://".$host.$port.$dir, ENT_QUOTES); 
  } else { return ["protocol" => $protocol, "host" => $host, "port" => $port, "dir" => $dir]; }
}


function get_total_points($user_id)
{
  global $mysqli;

  $query="SELECT SUM(`points`) AS `total_points` FROM willdev_users_rewards_activity
  LEFT JOIN willdev_users ON willdev_users_rewards_activity.`user_id`=willdev_users.`id`
  WHERE willdev_users_rewards_activity.`status`='1' AND willdev_users_rewards_activity.`user_id`='".$user_id."'
  ORDER BY willdev_users_rewards_activity.`id` DESC";  

  $sql = mysqli_query($mysqli,$query) or die(mysqli_error());
  $row=mysqli_fetch_assoc($sql);

  $total_points=$row['total_points'] ? $row['total_points'] : 0;

  $data = array(
    'total_point'  => $total_points,
  );    

  $update=Update("willdev_users", $data, "WHERE id = '$user_id'");

  return stripslashes($total_points);
}

function deleted_user_copy($user_id,$email='')
{
  global $mysqli;

  $sql="SELECT * FROM willdev_users WHERE `id`='$user_id'";
  $res=mysqli_query($mysqli, $sql);
  
  $row=mysqli_fetch_assoc($res);

  $qry_users_paid="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
  LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
  WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = '1'";
  $total_paid = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_paid));
  $total_paid = $total_paid['num'];

  $total_paid=($total_paid==null) ? '0' : $total_paid;

  $qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
  LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
  WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = '0'";
  $total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
  $total_pending = $total_pending['num']; 

  $total_pending=($total_pending==null) ? '0' : $total_pending;

  $qry_video="SELECT COUNT(*) as num FROM willdev_video WHERE user_id='".$user_id."'";
  $total_video = mysqli_fetch_array(mysqli_query($mysqli,$qry_video));
  $total_video = $total_video['num'];

  $sql_img="SELECT COUNT(*) as num FROM willdev_img_status WHERE user_id='".$user_id."' AND `status_type`='image'";
  $total_img = mysqli_fetch_array(mysqli_query($mysqli,$sql_img));
  $total_image = $total_img['num'];

  $sql_gif="SELECT COUNT(*) as num FROM willdev_img_status WHERE user_id='".$user_id."' AND `status_type`='gif'";
  $total_gif = mysqli_fetch_array(mysqli_query($mysqli,$sql_gif));
  $total_gif = $total_gif['num'];

  $sql_quote="SELECT COUNT(*) as num FROM willdev_quotes WHERE user_id='$user_id'";
  $total_quote = mysqli_fetch_array(mysqli_query($mysqli,$sql_quote));
  $total_quote = $total_quote['num'];


  $data = array(
    'user_code'  =>  $row['user_code'],
    'user_type'  =>  $row['user_type'],
    'device_id'  =>  $row['device_id'],
    'name'  =>  $row['name'],
    'email'  =>  $email=='' ? $row['email'] : $email,
    'phone'  =>  $row['phone'],
    'total_video'  =>  $total_video,
    'total_image'  =>  $total_image,
    'total_gif'  =>  $total_gif,
    'total_quote'  =>  $total_quote,
    'total_point'  =>  $row['total_point'],
    'pending_points'  =>  $total_pending,
    'paid_points'  =>  $total_paid,
    'total_followers'  =>  $row['total_followers'],
    'total_following'  =>  $row['total_following'],
    'verify_status'  =>  $row['is_verified'],
    'registration_on'  =>  $row['registration_on'],
    'auth_id'  =>  $row['auth_id'],
    'deleted_on'  =>  strtotime(date('d-m-Y h:i:s A')),
    'deleted_by'  =>  '1',
  );

  $qry = Insert('willdev_deleted_users',$data);


}

function thousandsNumberFormat($num) {

  if($num>1000) {

    $x = round($num);
    $x_number_format = number_format($x);
    $x_array = explode(',', $x_number_format);
    $x_parts = array(' K', ' M', ' B', ' T');
    $x_count_parts = count($x_array) - 1;
    $x_display = $x;
    $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
    $x_display .= $x_parts[$x_count_parts - 1];

    return $x_display;

  }

  return $num;
} 


function is_suspend($user_id) 
{
  global $mysqli;

  $sql="SELECT * FROM willdev_suspend_account WHERE user_id='$user_id' AND status='1' ORDER BY `id` DESC LIMIT 1";
  $res=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
  $row = mysqli_fetch_array($res);

  $num_rows1 = mysqli_num_rows($res);

  if($num_rows1 > 0)
  {     
    return true;
  }
  else
  {
    return false;
  }
}

function get_suspend_info($user_id,$field_name) 
{
  global $mysqli;

  $sql="SELECT * FROM willdev_suspend_account WHERE user_id='$user_id' AND status='1' ORDER BY `id` DESC LIMIT 1";
  $res=mysqli_query($mysqli,$sql);
  $row = mysqli_fetch_array($res);

  $num_rows = mysqli_num_rows($res);
  
  if ($num_rows > 0)
  {     
    return $row[$field_name];
  }
  else
  {
    return "";
  }
}

function get_favourite_info($post_id,$user_id,$type)
{
  global $mysqli;

  $sql="SELECT * FROM willdev_favourite WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `type`='$type'";
  $res=mysqli_query($mysqli,$sql);

  return ($res->num_rows == 1) ? true : false;

}


//GCM function
function Send_GCM_msg($registration_id,$data)
{
  $data1['data']=$data;

  $url = 'https://fcm.googleapis.com/fcm/send';
  
  $registatoin_ids = array($registration_id);
     // $message = array($data);

  $fields = array(
   'registration_ids' => $registatoin_ids,
   'data' => $data1,
 );
  
  $headers = array(
   'Authorization: key='.APP_GCM_KEY.'',
   'Content-Type: application/json'
 );
         // Open connection
  $ch = curl_init();
  
         // Set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
         // Disabling SSL Certificate support temporarly
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  
         // Execute post
  $result = curl_exec($ch);
  if ($result === FALSE) {
   die('Curl failed: ' . curl_error($ch));
 }

         // Close connection
 curl_close($ch);
       //echo $result;exit;
}


//Image compress
function compress_image($source_url, $destination_url, $quality) 
{

  $info = getimagesize($source_url);

  if($info['mime'] == 'image/jpeg')
    $image = imagecreatefromjpeg($source_url);

  elseif ($info['mime'] == 'image/gif')
    $image = imagecreatefromgif($source_url);

  elseif ($info['mime'] == 'image/png')
    $image = imagecreatefrompng($source_url);

  imagejpeg($image, $destination_url, $quality);
  return $destination_url;
}

//Create Thumb Image
function create_thumb_image($target_folder ='',$thumb_folder = '', $thumb_width = '',$thumb_height = '')
{  
     //folder path setup
  $target_path = $target_folder;
  $thumb_path = $thumb_folder;  

  $thumbnail = $thumb_path;
  $upload_image = $target_path;

  $file_ext=pathinfo($upload_image, PATHINFO_EXTENSION);

  list($width,$height) = getimagesize($upload_image);
  $thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
  switch($file_ext){
    case 'jpg':
    $source = imagecreatefromjpeg($upload_image);
    break;
    case 'jpeg':
    $source = imagecreatefromjpeg($upload_image);
    break;
    case 'png':
    $source = imagecreatefrompng($upload_image);
    break;
    case 'gif':
    $source = imagecreatefromgif($upload_image);
    break;
    default:
    $source = imagecreatefromjpeg($upload_image);
  }
  imagecopyresized($thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width,$height);
  switch($file_ext){
    case 'jpg' || 'jpeg':
    imagejpeg($thumb_create,$thumbnail,80);
    break;
    case 'png':
    imagepng($thumb_create,$thumbnail,80);
    break;
    case 'gif':
    imagegif($thumb_create,$thumbnail,80);
    break;
    default:
    imagejpeg($thumb_create,$thumbnail,80);
  }
}

// for time format

function calculate_time_span($post_time)
{  
  if($post_time!=''){
    $seconds = time() - $post_time;
    $year = floor($seconds /31556926);
    $months = floor($seconds /2629743);
    $week=floor($seconds /604800);
    $day = floor($seconds /86400); 
    $hours = floor($seconds / 3600);
    $mins = floor(($seconds - ($hours*3600)) / 60); 
    $secs = floor($seconds % 60);
    if($seconds < 60) $time = $secs." seconds ago";
    else if($seconds < 3600 ) $time =($mins==1)? $mins." min ago" : $mins." mins ago";
    else if($seconds < 86400) $time = ($hours==1)?$hours." hour ago":$hours." hours ago";
    else if($seconds < 604800) $time = ($day==1)?$day." day ago":$day." days ago";
    else if($seconds < 2629743) $time = ($week==1)?$week." week ago":$week." weeks ago";
    else if($seconds < 31556926) $time =($months==1)? $months." month ago":$months." months ago";
    else $time = ($year==1)? $year." year ago":$year." years ago";
    return $time;
  }
  else{
    return 'not available';
  }
  
}  

function user_reward_activity($post_id="",$user_id,$activity_type,$rewards_points,$owner=false)
{
  global $mysqli;

  if($post_id!="" AND $user_id!="" AND $activity_type!="")
  {
    if(!$owner)
    {
      $sql = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$post_id' AND `user_id` = '$user_id' AND `activity_type` = '$activity_type'";

      $result = mysqli_query($mysqli,$sql);
      $num_rows = mysqli_num_rows($result);
      $row = mysqli_fetch_assoc($result);

      if($num_rows == 0)
      {                      
       $data = array( 
         'post_id'  =>  $post_id,
         'user_id'  =>  $user_id,
         'activity_type'  =>  $activity_type,
         'points'  =>  $rewards_points
       );

       $qry = Insert('willdev_users_rewards_activity',$data);
       return  $qry;
     }
   }
   else{
    $data = array( 
     'post_id'  =>  $post_id,
     'user_id'  =>  $user_id,
     'activity_type'  =>  $activity_type,
     'points'  =>  $rewards_points
   );

    $qry = Insert('willdev_users_rewards_activity',$data);
    return  $qry;
  }


}      
else
{
  $data = array(                    
   'post_id'  =>  '0',
   'user_id'  =>  $user_id,
   'activity_type'  =>  $activity_type,
   'points'  =>  $rewards_points
 );

  $qry = Insert('willdev_users_rewards_activity',$data);

  return  $qry; 
}

}

function checkSignSalt($data_info){

  $key="dev";

  $data_json = $data_info;

  $data_arr = json_decode(urldecode(base64_decode($data_json)),true);

  if($data_arr['package_name']==PACKAGE_NAME){
    if($data_arr['sign'] == '' && $data_arr['salt'] == '' ){

      $set['status']=-1;
      $set['message']="Invalid sign or salt !!";

      header('Content-Type: application/json; charset=utf-8' );
      echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
      exit();


    }else{

      $data_arr['salt'];    

      $md5_salt=md5($key.$data_arr['salt']);

      if($data_arr['sign']!=$md5_salt){

        $set['status']=-1;
        $set['message']="Invalid sign !!";

        header( 'Content-Type: application/json; charset=utf-8' );
        echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        exit();
      }
      else{

        if(isset($data_arr['user_id']) AND $data_arr['user_id']!=''){
          if(CountRow("willdev_users","id=".$data_arr['user_id'])!=0)
          {

            $user_id=$data_arr['user_id'];

            $sql="SELECT * FROM willdev_active_log WHERE `user_id`='$user_id'";
            $res=mysqli_query($mysqli, $sql);

            if(mysqli_num_rows($res) == 0)
            {
              
              // insert active log

              $data_log = array(
                'user_id'  =>  $user_id,
                'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
              );

              $qry = Insert('willdev_active_log',$data_log);

            }
            else{
              // update active log
              $data_log = array(
                'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
              );

              $update=Update('willdev_active_log', $data_log, "WHERE user_id = '$user_id'");  
            }

            mysqli_free_result($res);


            if(CountRow("willdev_users","id=".$data_arr['user_id']." AND status = 3")!=0){
              $set['status']=-2;
              $set['message']='Your account is deleted.';
              header('Content-Type: application/json; charset=utf-8');
              echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
              exit();
            }

            if($data_arr['user_id']!='' && !isset($data_arr['other_user_id'])){

              if(is_suspend($data_arr['user_id'])){
                $set['status']=-2;
                $set['message']='Your account is suspended.';
                header('Content-Type: application/json; charset=utf-8');
                echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                exit(); 
              }else{

              }
            }
            else if(isset($data_arr['user_id']) && isset($data_arr['other_user_id'])){
              if(is_suspend($data_arr['other_user_id'])){
                $set['status']=-3;
                $set['message']='This account is currently suspended';
                header( 'Content-Type: application/json; charset=utf-8' );
                echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                exit(); 
              }else{

              }
            }
          }
          else{
            $set['status']=-2;
            $set['message']='Your account is deleted or disabled by admin !!';
            header( 'Content-Type: application/json; charset=utf-8' );
            echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            exit(); 
          }
        }
      }
    }
  } 


  return $data_arr;

}

 