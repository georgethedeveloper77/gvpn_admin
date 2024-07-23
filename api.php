<?php 
	include("includes/connection.php");
	include("language/app_language.php");
 	include("includes/function.php");
 	include("smtp_email.php"); 

 	$mysqli->set_charset('utf8mb4');
	 
    date_default_timezone_set("Asia/Kolkata");
	
	$file_path = getBaseUrl();

	// get spinner limit
	define("SPINNER_LIMIT",$settings_details['spinner_limit']);

	define("PACKAGE_NAME",$settings_details['package_name']);

    define("DOWNLOAD_VIDEO_POINTS",$settings_details['download_video_points']);

    define("REGISTRATION_REWARD_POINTS_STATUS",$settings_details['registration_reward_status']);
    define("APP_REFER_REWARD_POINTS_STATUS",$settings_details['app_refer_reward_status']);
    define("VIDEO_VIEW_POINTS_STATUS",$settings_details['video_views_status']);
    define("VIDEO_ADD_POINTS_STATUS",$settings_details['video_add_status']);
    define("DOWNLOAD_POINTS_STATUS",$settings_details['download_video_points_status']);
    define("OTHER_USER_VIDEO_STATUS",$settings_details['other_user_video_status']);
    define("OTHER_USER_VIDEO_POINT",$settings_details['other_user_video_point']);

    function correctImageOrientation($filename) {
	  if (function_exists('exif_read_data')) {
	    $exif = exif_read_data($filename);
	    if($exif && isset($exif['Orientation'])) {
	      $orientation = $exif['Orientation'];
	      if($orientation != 1){
	        $img = imagecreatefromjpeg($filename);
	        $deg = 0;
	        switch ($orientation) {
	          case 3:
	            $deg = 180;
	            break;
	          case 6:
	            $deg = 270;
	            break;
	          case 8:
	            $deg = 90;
	            break;
	        }
	        if ($deg) {
	          $img = imagerotate($img, $deg, 0);        
	        }
	        imagejpeg($img, $filename, 95);
	      }
	    }
	  }
	}

    function get_thumb($filename,$thumb_size)
	{
		$file_path = getBaseUrl();

		$size_arr=explode('x', $thumb_size);
		
		return $thumb_path=$file_path.'phpThumb.php?src='.$filename.'&w='.$size_arr[0].'&h='.$size_arr[1];
	}

	function createRandomCode() 
	{     
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";     
		srand((double)microtime()*1000000);     
		$i = 0;     
		$pass = '' ;     
		while ($i <= 7) 
		{         
			$num = rand() % 33;         
			$tmp = substr($chars, $num, 1);         
			$pass = $pass . $tmp;         
			$i++;     
		}    
		return $pass; 
	}

	function generateRandomPassword($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	function user_total_status($user_id) 
	{
		global $mysqli;

		$total_count=0;

		$sql="SELECT COUNT(*) as num FROM willdev_video WHERE `user_id`='$user_id'";
		$res=mysqli_query($mysqli,$sql);
		$row=mysqli_fetch_array($res);
		$total_count+=$row['num'];
		mysqli_free_result($res);

		$sql="SELECT COUNT(*) as num FROM willdev_img_status WHERE `user_id`='$user_id'";
		$res=mysqli_query($mysqli,$sql);
		$row=mysqli_fetch_array($res);
		$total_count+=$row['num'];
		mysqli_free_result($res);

		$sql="SELECT COUNT(*) as num FROM willdev_quotes WHERE `user_id`='$user_id'";
		$res=mysqli_query($mysqli,$sql);
		$row=mysqli_fetch_array($res);
		$total_count+=$row['num'];
		mysqli_free_result($res);

		return $total_count;
	}

	function get_subject_info($id,$field_name) 
	{
		global $mysqli;

		$qry_sub="SELECT * FROM willdev_contact_sub WHERE id='$id'";
		$query1=mysqli_query($mysqli,$qry_sub);
		$row_sub = mysqli_fetch_array($query1);

		$num_rows1 = mysqli_num_rows($query1);
		
	    if ($num_rows1 > 0)
	    {		 	
			return $row_sub[$field_name];
		}
		else
		{
			return "";
		}
	}

	// paramater wise info
	function get_single_info($post_id,$param,$type='video')
    {
      global $mysqli;

      switch ($type) {
        case 'video':
          $query="SELECT * FROM willdev_video WHERE `id`='$post_id'";
          break;

        case 'image':
          $query="SELECT * FROM willdev_img_status WHERE `id`='$post_id'";
          break;

        case 'gif':
          $query="SELECT * FROM willdev_img_status WHERE `id`='$post_id'";
          break;

        case 'quote':
          $query="SELECT * FROM willdev_quotes WHERE `id`='$post_id'";
          break;
        
        default:
          $query="SELECT * FROM willdev_video WHERE `id`='$post_id'";
          break;
      }

      $sql = mysqli_query($mysqli,$query)or die(mysqli_error());
      $row=mysqli_fetch_assoc($sql);

      return stripslashes($row[$param]);
    }

	function get_user_info($user_id,$field_name) 
	{
		global $mysqli;

		$qry_user="SELECT * FROM willdev_users WHERE id='".$user_id."'";
		$query1=mysqli_query($mysqli,$qry_user);
		$row_user = mysqli_fetch_array($query1);

		$num_rows1 = mysqli_num_rows($query1);
		
		if ($num_rows1 > 0)
		{		 	
			return $row_user[$field_name];
		}
		else
		{
			return "";
		}
	}
	
	
	if($settings_details['envato_buyer_name']=='' OR $settings_details['envato_purchase_code']=='' OR $settings_details['envato_purchased_status']==0) {  
	
		$set['status']=-1;
		$set['message']="Purchase code verification failed!";
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	

	$get_method = checkSignSalt($_POST['data']); 
	
    if($get_method['method_name']=="home")	
	{

		$jsonObj_2= array();

		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
		}
		else{
			$lang_ids = array();
		}
 		

 		$query_all="SELECT * FROM willdev_slider WHERE `status`='1' ORDER BY `id` DESC";

		$sql_all = mysqli_query($mysqli,$query_all) or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql_all))
		{
			$layout='Landscape';
			$total_views=0;

			$post_id=$data['post_id'];

			switch ($data['slider_type']) {
				case 'video':
				  
				  	if(!empty($lang_ids)){

			 			$column='';
						foreach ($lang_ids as $key => $value) {
							$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
						}

						$column=rtrim($column,'OR ');

						$query="SELECT willdev_video.`video_title`, willdev_video.`video_thumbnail`, willdev_video.`video_layout`, willdev_video.`totel_viewer` FROM willdev_video
								LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
								WHERE willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`id`='$post_id' AND ($column) ORDER BY willdev_video.`id` DESC";	
			 		}
			 		else{
			 			$query="SELECT willdev_video.`video_title`, willdev_video.`video_thumbnail`, willdev_video.`video_layout`, willdev_video.`totel_viewer` FROM willdev_video
								LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
								WHERE willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`id`='$post_id' ORDER BY willdev_video.`id` DESC";
			 		}

			 		$sql_res=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			 		$row_data=mysqli_fetch_assoc($sql_res);

			 		$slider_title=$row_data['video_title'];
					$image=$row_data['video_thumbnail'];
					$layout=$row_data['video_layout'];
					$total_views=$row_data['totel_viewer'];

				  break;

				case 'image':

					if(!empty($lang_ids)){

			 			$column='';
						foreach ($lang_ids as $key => $value) {
							$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
						}

						$column=rtrim($column,'OR ');

						$query="SELECT willdev_img_status.`image_title`, willdev_img_status.`image_file`, willdev_img_status.`image_layout`, willdev_img_status.`total_views` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
								WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`id`='$post_id' AND willdev_img_status.`status_type`='image' AND ($column) ORDER BY willdev_img_status.`id` DESC";	
			 		}
			 		else{
			 			$query="SELECT willdev_img_status.`image_title`, willdev_img_status.`image_file`, willdev_img_status.`image_layout`, willdev_img_status.`total_views` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
								WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`id`='$post_id' AND willdev_img_status.`status_type`='image' ORDER BY willdev_img_status.`id` DESC";
			 		}

			 		$sql_res=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			 		$row_data=mysqli_fetch_assoc($sql_res);

				  	$slider_title=$row_data['image_title'];
				  	$image=$row_data['image_file'];
				  	$layout=$row_data['image_layout'];
				  	$total_views=$row_data['total_views'];

				  break;

				case 'gif':

				  	if(!empty($lang_ids)){

			 			$column='';
						foreach ($lang_ids as $key => $value) {
							$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
						}

						$column=rtrim($column,'OR ');

						$query="SELECT willdev_img_status.`image_title`, willdev_img_status.`image_file`, willdev_img_status.`image_layout`, willdev_img_status.`total_views` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
								WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`id`='$post_id' AND willdev_img_status.`status_type`='gif' AND ($column) ORDER BY willdev_img_status.`id` DESC";	
			 		}
			 		else{
			 			$query="SELECT willdev_img_status.`image_title`, willdev_img_status.`image_file`, willdev_img_status.`image_layout`, willdev_img_status.`total_views` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
								WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`id`='$post_id' AND willdev_img_status.`status_type`='gif' ORDER BY willdev_img_status.`id` DESC";
			 		}

			 		$sql_res=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			 		$row_data=mysqli_fetch_assoc($sql_res);

				  	$slider_title=$row_data['image_title'];
				  	$image=$row_data['image_file'];
				  	$layout=$row_data['image_layout'];
				  	$total_views=$row_data['total_views'];
				  	
				  break;

				case 'quote':
				  	

				  	if(!empty($lang_ids)){

			 			$column='';
						foreach ($lang_ids as $key => $value) {
							$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
						}

						$column=rtrim($column,'OR ');

						$query="SELECT willdev_quotes.`quote`, willdev_quotes.`quote_bg`, willdev_quotes.`quote_font`, willdev_quotes.`total_views` FROM willdev_quotes
								LEFT JOIN willdev_category ON willdev_quotes.`cat_id`= willdev_category.`cid` 
								WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`id`='$post_id' AND ($column) ORDER BY willdev_quotes.`id` DESC";	
			 		}
			 		else{
			 			$query="SELECT willdev_quotes.`quote`, willdev_quotes.`quote_bg`, willdev_quotes.`quote_font`, willdev_quotes.`total_views` FROM willdev_quotes
								LEFT JOIN willdev_category ON willdev_quotes.`cat_id`= willdev_category.`cid` 
								WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`id`='$post_id' ORDER BY willdev_quotes.`id` DESC";
			 		}

			 		$sql_res=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			 		$row_data=mysqli_fetch_assoc($sql_res);

			 		$slider_title=$row_data['quote'];
				  	$total_views=$row_data['total_views'];
				  	
				  break;

				default:
				  $slider_title=$data['slider_title'];
				  $image=$data['external_image'];
				  break;
			}

			if($sql_res->num_rows == 0 AND $data['slider_type']!='external'){
				continue;
	 		}

			$row2['id'] = $data['post_id'];
			$row2['status_type'] = $data['slider_type'];
			$row2['status_title'] = $slider_title;
			$row2['status_layout'] = $layout;

			if($data['slider_type']!='quote')
			{
				$row2['status_thumbnail_b'] = $file_path.'images/'.$image;

				if($layout=='Portrait'){
					$row2['status_thumbnail_s'] = get_thumb('images/'.$image,'280x500');
				}
				else{
					$row2['status_thumbnail_s'] = get_thumb('images/'.$image,'500x280');
				}

				$row2['quote_bg'] = '';
    			$row2['quote_font'] = '';
				
			}
			else
			{
				$row2['status_thumbnail_b'] = '';
				$row2['status_thumbnail_s'] = '';
				$row2['quote_bg'] = '#'.get_single_info($data['post_id'],'quote_bg','quote');
    			$row2['quote_font'] = get_single_info($data['post_id'],'quote_font','quote');
			}

			$row2['external_link'] = ($data['external_url']!='') ? $data['external_url'] : '';
			$row2['total_viewer'] = $total_views;
			
			array_push($jsonObj_2,$row2);
		}

		// slider status
		$row['slider_status']=$jsonObj_2;
		
		$jsonObj= array();	

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='Portrait' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_video.`id` DESC LIMIT 5";	
 		}
 		else{
 			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='Portrait' AND willdev_category.`status`='1' ORDER BY willdev_video.`id` DESC LIMIT 5";
 		}

 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			$row1['id'] = $data['id'];
			$row1['status_type'] = 'video';
			$row1['status_title'] = $data['video_title'];
			$row1['status_layout'] = $data['video_layout'];
			
			$row1['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];
			$row1['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');

			$row1['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

			array_push($jsonObj,$row1);    
		}

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='Portrait' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_img_status.`id` DESC LIMIT 5";	
 		}
 		else{
 			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='Portrait' AND willdev_category.`status`='1' ORDER BY willdev_img_status.`id` DESC LIMIT 5";
 		}

 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			$row1['id'] = $data['id'];
			$row1['status_type'] = $data['status_type'];
			$row1['status_title'] = $data['image_title'];
			$row1['status_layout'] = $data['image_layout'];

			$row1['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];
			$row1['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');

			$row1['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

			array_push($jsonObj,$row1);    
		}	

		
		$row['portrait_status']=$jsonObj;
 
		$set['ANDROID_REWARDS_APP'] = $row;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if($get_method['method_name']=="landscape_status")
 	{
 		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
 		}
		else{
			$lang_ids = array();
		}
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

 		$video_order_by=API_ALL_VIDEO_ORDER_BY;

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1'AND willdev_video.`video_layout`='Landscape' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_video.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1'AND willdev_video.`video_layout`='Landscape' AND willdev_category.`status`='1' ORDER BY willdev_video.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			if($data['video_type']=='server_url' or $data['video_type']=='local')
			{
				$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
			}
			else
			{
				$row['status_thumbnail_b'] = $data['video_thumbnail'];
				$row['status_thumbnail_s'] = $data['video_thumbnail'];
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"select * from willdev_like WHERE post_id='".$data['id']."' && device_id='".$get_method['user_id']."' && like_type='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';

			array_push($jsonObj,$row);
		
		}

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1'AND willdev_img_status.`image_layout`='Landscape' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_img_status.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1'AND willdev_img_status.`image_layout`='Landscape' AND willdev_category.`status`='1' ORDER BY willdev_img_status.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"select * from willdev_like WHERE post_id='".$data['id']."' && device_id='".$get_method['user_id']."' && like_type='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		} 

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';

			array_push($jsonObj,$row);
		
		}

		// for quotes statues 

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`= willdev_category.`cid` 
					WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_quotes.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`= willdev_category.`cid` 
					WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' ORDER BY willdev_quotes.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] = $data['id'];
			$row['status_type'] = 'quote';
			$row['status_title'] = stripslashes($data['quote']);
			$row['status_layout'] = 'Landscape';
			
			$row['status_thumbnail_b'] = '';

			$row['status_thumbnail_s'] = '';

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"select * from willdev_like WHERE post_id='".$data['id']."' && device_id='".$get_method['user_id']."' && like_type='quote'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		} 

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

    		$row['quote_bg'] = '#'.$data['quote_bg'];
    		$row['quote_font'] = $data['quote_font'];
			array_push($jsonObj,$row);
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
 	else if($get_method['method_name']=="portrait_status")
 	{

 		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
 		}
		else{
			$lang_ids = array();
		}
		
		$page_limit=API_PAGE_LIMIT;
			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

 		$video_order_by=API_ALL_VIDEO_ORDER_BY;

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1'AND willdev_video.`video_layout`='Portrait' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_video.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`= willdev_category.`cid` 
					WHERE willdev_video.`status`='1'AND willdev_video.`video_layout`='Portrait' AND willdev_category.`status`='1' ORDER BY willdev_video.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			if($data['video_type']=='server_url' or $data['video_type']=='local')
			{
				$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
			}
			else
			{
				$row['status_thumbnail_b'] = $data['video_thumbnail'];
				$row['status_thumbnail_s'] = $data['video_thumbnail'];
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"select * from willdev_like WHERE post_id='".$data['id']."' && device_id='".$get_method['user_id']."' && like_type='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

			array_push($jsonObj,$row);
		
		}

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1'AND willdev_img_status.`image_layout`='Portrait' AND willdev_category.`status`='1' AND ($column) ORDER BY willdev_img_status.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_img_status
					LEFT JOIN willdev_category ON willdev_img_status.`cat_id`= willdev_category.`cid` 
					WHERE willdev_img_status.`status`='1'AND willdev_img_status.`image_layout`='Portrait' AND willdev_category.`status`='1' ORDER BY willdev_img_status.`id` $video_order_by LIMIT $limit, $page_limit";
 		}
 
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"select * from willdev_like WHERE post_id='".$data['id']."' && device_id='".$get_method['user_id']."' && like_type='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

			array_push($jsonObj,$row);
		
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
    else if($get_method['method_name']=="related_status")
 	{

 		$user_id=$get_method['user_id'];
 		$post_id=$get_method['post_id'];
 		$type=$get_method['type'];

 		$layout=$get_method['filter_value'];
		$cat_id=$get_method['cat_id'];

 		$page=(isset($get_method['page'])) ? $get_method['page'] : 1;

 		$page_limit=API_PAGE_LIMIT;			
		$limit=($page-1) * $page_limit;

		$post_order_by=API_CAT_POST_ORDER_BY; 

		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
 		}
		else{
			$lang_ids = array();
		}

		$jsonObj= array();

		$layout=($layout!='') ? $layout : 'Landscape';

		// for video status

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`id` <> '$post_id' AND willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`video_layout`='$layout' AND willdev_video.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`id` <> '$post_id' AND willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`video_layout`='$layout' AND willdev_video.`cat_id`='$cat_id' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 		}

		$sql_video=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql_video))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

			if($data['video_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
			}
			else if($data['video_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."'AND `device_id`='".$get_method['user_id']."' AND `like_type`='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}
			
		// for image status
		
		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`id` <> '$post_id' AND willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`image_layout`='$layout' AND willdev_img_status.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 		}
 		else{
			$query="SELECT * FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`id` <> '$post_id' AND willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`image_layout`='$layout' AND willdev_img_status.`cat_id`='$cat_id' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 		}

		$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql_image))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			if($data['image_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
			}
			else if($data['image_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}

		
		if($layout=='Landscape')
		{
			// for quotes status
			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
					WHERE willdev_quotes.`id` <> '$post_id' AND willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{

				$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
					WHERE willdev_quotes.`id` <> '$post_id' AND willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($sql_quote))
			{	
				$row['id'] = $data['id'];
				$row['status_type'] = 'quote';
				$row['status_title'] = stripslashes($data['quote']);
				$row['status_layout'] = 'Landscape';
				
				$row['status_thumbnail_b'] = '';

				$row['status_thumbnail_s'] = '';

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

	    		$row['quote_bg'] = '#'.$data['quote_bg'];
	    		$row['quote_font'] = $data['quote_font'];
	 
				array_push($jsonObj,$row);
			}
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}
	else if($get_method['method_name']=="otp_status")
	{

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
		$data = mysqli_fetch_assoc($sql);

		$row['otp_status']=$data['otp_status'];

		$set['ANDROID_REWARDS_APP']= $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="get_cat_lang_list")
 	{
		$jsonObj= array();
		
		$cat_order=API_CAT_ORDER_BY;

		$query="SELECT * FROM willdev_category WHERE willdev_category.`status`='1' ORDER BY willdev_category.".$cat_order."";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row_data['cid'] = $data['cid'];
			$row_data['category_name'] = $data['category_name'];
			array_push($jsonObj,$row_data);
			
		}

		$row['category_list']=$jsonObj;

		mysqli_free_result($sql);

		$jsonObj= array();
		$row_data= array();

		$query="SELECT * FROM willdev_language WHERE willdev_language.`status`='1' ORDER BY willdev_language.`language_name`";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row_data['language_id'] = $data['id'];
			$row_data['language_name'] = $data['language_name'];
			array_push($jsonObj,$row_data);
			
		}

		$row['language_list']=$jsonObj;
		
		$set['ANDROID_REWARDS_APP'] = $row;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="home_cat_list")
 	{
		$jsonObj= array();
		
		$cat_order=API_CAT_ORDER_BY;

		$limit=$settings_details['cat_show_home_limit'];

		$query="SELECT * FROM willdev_category WHERE willdev_category.`status`='1' AND willdev_category.`show_on_home`='1' ORDER BY willdev_category.$cat_order DESC LIMIT $limit";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		if($sql->num_rows == 0){

			mysqli_free_result($sql);
			$query="SELECT * FROM willdev_category WHERE willdev_category.`status`='1' ORDER BY willdev_category.$cat_order DESC LIMIT $limit";

			$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
		}

		while($data = mysqli_fetch_assoc($sql))
		{	 
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['start_color'] = '#'.$data['start_color'];
			$row['end_color'] = '#'.$data['end_color'];
 
			array_push($jsonObj,$row);
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="cat_list")
 	{
		$jsonObj= array();
		
		$cat_order=API_CAT_ORDER_BY;

		$query="SELECT * FROM willdev_category WHERE willdev_category.`status`='1' ORDER BY willdev_category.".$cat_order."";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			 
			$row['cid'] = $data['cid'];
			$row['category_name'] = $data['category_name'];
			$row['category_image'] = $file_path.'images/'.$data['category_image'];
			$row['category_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];

			$row['start_color'] = '#'.$data['start_color'];
			$row['end_color'] = '#'.$data['end_color'];
 
			array_push($jsonObj,$row);
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	elseif ($get_method['method_name']=="get_language") {

		if(isset($get_method['lang_ids']))
			$lang_ids = explode(',', $get_method['lang_ids']);
		else
			$lang_ids = array();

		$jsonObj= array();	

		$query="SELECT * FROM willdev_language WHERE willdev_language.`status`='1' ORDER BY willdev_language.`id`";

		$sql = mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['language_id'] = $data['id'];
			$row['language_name'] = $data['language_name'];
            $row['language_image'] = $file_path.'images/'.$data['language_image'];
			$row['language_image_thumb'] = $file_path.'images/thumbs/'.$data['language_image'];

			if(in_array($data['id'], $lang_ids)){
				$row['is_selected'] = "true";
			}
			else{
				$row['is_selected'] = "false";
			}

			array_push($jsonObj,$row);
		
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}
	else if($get_method['method_name']=="status_by_cat_id")
	{		
 		$cat_id=$get_method['cat_id'];

 		$user_id=$get_method['user_id'];
		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

 		$post_order_by=API_CAT_POST_ORDER_BY; 

 		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
		}
		else{
			$lang_ids = array();
		}

		$jsonObj= array();

 		if($get_method['filter_value']!="")
 		{
 			$filter_value=trim($get_method['filter_value']);

			// for video status

 			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='$filter_value' AND willdev_category.`status`='1' AND willdev_video.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";	
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='$filter_value' AND willdev_category.`status`='1' AND willdev_video.`cat_id`='$cat_id' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for image status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='$filter_value' AND willdev_category.`status`='1' AND willdev_img_status.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";	
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='$filter_value' AND willdev_category.`status`='1' AND willdev_img_status.`cat_id`='$cat_id' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			

			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for quotes status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";	
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

 		}
 		else
 		{
			// for video status

 			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";	
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`cat_id`='$cat_id' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			

			$sql_video=mysqli_query($mysqli,$query) or die('error here ->'.mysqli_error($mysqli));

			// for image status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`cat_id`='$cat_id' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
			

			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for quotes status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
 		}
		
		while($data = mysqli_fetch_assoc($sql_video))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

			if($data['video_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
			}
			else if($data['video_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$user_id,'video');

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}

		while($data = mysqli_fetch_assoc($sql_image))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			if($data['image_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
			}
			else if($data['image_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$user_id,$data['status_type']);

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}

		if($get_method['filter_value']=="" OR $get_method['filter_value']=='Landscape')
 		{
			while($data = mysqli_fetch_assoc($sql_quote))
			{	
				$row['id'] = $data['id'];
				$row['status_type'] = 'quote';
				$row['status_title'] = stripslashes($data['quote']);
				$row['status_layout'] = 'Landscape';
				
				$row['status_thumbnail_b'] = '';

				$row['status_thumbnail_s'] = '';

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$user_id,'quote');

	    		$row['quote_bg'] = '#'.$data['quote_bg'];
	    		$row['quote_font'] = $data['quote_font'];
	 
				array_push($jsonObj,$row);
			}
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="single_status")
 	{	 

 		$jsonObj= array();	

 		$status_id=$get_method['status_id'];
 		$user_id=$get_method['user_id'];

 		$type=$get_method['type'];

 		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
 		}
		else{
			$lang_ids = array();
		}

		$layout='';
		$cat_id='';

		$success=1;

 		switch ($type) {
 			case 'video':
 					{
	 					$sql="SELECT * FROM willdev_video LEFT JOIN willdev_category
	 							ON willdev_video.`cat_id`=willdev_category.`cid`
	 							WHERE willdev_video.`id`='$status_id' AND willdev_category.`status`='1'";

	 					$res=mysqli_query($mysqli, $sql) or die('Error in fetching =>'.mysqli_error($mysqli));

	 					if($res->num_rows > 0){
	 						$data = mysqli_fetch_assoc($res);

							$video_file=$data['video_url'];

							if($data['video_type']=='local'){
								$video_file=$file_path.'uploads/'.basename($data['video_url']);
							}

							$row['total_comment'] = CountRow('willdev_comments',"post_id='".$status_id."' AND type='video'");

							$row['id'] = $data['id'];
							$row['cat_id'] = $data['cat_id'];
							$row['status_type'] = 'video';
							$row['status_title'] = $data['video_title'];
							$row['status_layout'] = $data['video_layout'];
							$row['video_url'] = $video_file;
							
							$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

							if($data['video_layout']=='Landscape'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
							}
							else if($data['video_layout']=='Portrait'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
							}

							$row['total_likes'] = $data['total_likes'];
				  			$row['total_viewer'] = $data['totel_viewer'];  
				  			$row['total_download'] = $data['total_download'];  

							$row['category_name'] = $data['category_name'];

				    		$row['quote_bg'] = '';
				    		$row['quote_font'] = '';

				    		$res_like = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$status_id."' && `device_id`='".$user_id."' && `like_type`='video'"); 
				    		
				    		$num_like = mysqli_num_rows($res_like);
				 		
				            if ($num_like > 0)
						    {
				    			$row['already_like']=true;
				    		}
				    		else
				    		{
				    			$row['already_like']=false;
				    		}

				    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

				    		$row['watermark_image'] = $settings_details['watermark_image'] ? $file_path.'images/'.$settings_details['watermark_image'] : "";

							if($row['watermark_image']==''){
				    			$row['watermark_on_off'] = 'false';
				    		}
				    		else{
				    			$row['watermark_on_off'] = $settings_details['watermark_on_off'];
				    		}

				    		$row['user_id'] = $data['user_id'];			 
							$row['user_name'] = get_user_info($data['user_id'],'name');			 
							
							if(get_user_info($data['user_id'],'user_image')!='')
							{
								$row['user_image'] = $file_path.'images/'.get_user_info($data['user_id'],'user_image');
							}	
							else
							{
								$row['user_image'] ='';
							}

							if(get_user_info($data['user_id'],'is_verified')==1){
								$row['is_verified']="true";
							}
							else{
								$row['is_verified']="false";
							}

							$sql_follower = "SELECT * FROM willdev_follows WHERE `user_id`='".$data['user_id']."' AND `follower_id`= '$user_id'"; 

							$res_follower = mysqli_query($mysqli,$sql_follower);
							$num_follower = mysqli_num_rows($res_follower);
					 		
					    	if ($num_follower > 0)
							{		 
								$row['already_follow']=true;
							}		 
							else
							{
					 			$row['already_follow']=false;
							}

							mysqli_free_result($res);

							//Related Video Status
							$layout=$data['video_layout'];
							$cat_id=$data['cat_id'];

				   			// get comments related to video
							$sql_comment="SELECT * FROM willdev_comments WHERE `post_id`='".$status_id."' AND `status`='1' AND `type`='video' ORDER BY `id` DESC LIMIT 2";

							$res=mysqli_query($mysqli,$sql_comment); 

							if($res->num_rows > 0)
							{
								while ($row_comments=mysqli_fetch_assoc($res)) 
								{
									$row_comment['comment_id'] = $row_comments['id'];			 
									$row_comment['user_id'] = $row_comments['user_id'];			 
									$row_comment['user_name'] = get_user_info($row_comments['user_id'],'name')?get_user_info($row_comments['user_id'],'name'):$row_comments['user_name'];			 

									if(get_user_info($row_comments['user_id'],'user_image')!='')
									{
										$row_comment['user_image'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_image');
									}	
									else
									{
										$row_comment['user_image'] ='';
									}

									$row_comment['post_id'] = $row_comments['post_id'];
									$row_comment['status_type'] = $row_comments['type'];
									$row_comment['comment_text'] = $row_comments['comment_text'];
									$row_comment['comment_date'] = calculate_time_span($row_comments['comment_on']);

									$row['user_comments'][]= $row_comment;
								}
							}
							else
							{	
								$row['user_comments']= array();
							}

							
	 					}
	 					else{
	 						$success=0;
	 					}
	 				}
 				break;

 			case 'image':
 					{
	 					$sql="SELECT * FROM willdev_img_status LEFT JOIN willdev_category
	 							ON willdev_img_status.`cat_id`=willdev_category.`cid`
	 							WHERE willdev_img_status.`id`='$status_id' AND willdev_img_status.`status_type`='$type'  AND willdev_category.`status`='1'";

	 					$res=mysqli_query($mysqli, $sql) or die('Error in fetching =>'.mysqli_error($mysqli));

	 					if($res->num_rows > 0){
	 						$data = mysqli_fetch_assoc($res);

							$row['total_comment'] = CountRow('willdev_comments',"post_id='".$status_id."' AND type='".$type."'");

							$row['id'] = $data['id'];
							$row['cat_id'] = $data['cat_id'];
							$row['status_type'] = $data['status_type'];
							$row['status_title'] = $data['image_title'];
							$row['status_layout'] = $data['image_layout'];
							$row['video_url'] = '';
							
							$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

							if($data['image_layout']=='Landscape'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
							}
							else if($data['image_layout']=='Portrait'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
							}

							$row['total_likes'] = $data['total_likes'];
				  			$row['total_viewer'] = $data['total_views'];
				  			$row['total_download'] = $data['total_download'];  

							$row['category_name'] = $data['category_name'];

				    		$row['quote_bg'] = '';
				    		$row['quote_font'] = '';

				    		$res_like = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$status_id."' && `device_id`='".$user_id."' && `like_type`='".$type."'"); 
				    		
				    		$num_like = mysqli_num_rows($res_like);
				 		
				            if ($num_like > 0)
						    {
				    			$row['already_like']=true;
				    		}
				    		else
				    		{
				    			$row['already_like']=false;
				    		}

				    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$type);

				    		$row['watermark_image'] = '';

							$row['watermark_on_off'] = '';

				    		$row['user_id'] = $data['user_id'];			 
							$row['user_name'] = get_user_info($data['user_id'],'name');			 
							
							if(get_user_info($data['user_id'],'user_image')!='')
							{
								$row['user_image'] = $file_path.'images/'.get_user_info($data['user_id'],'user_image');
							}	
							else
							{
								$row['user_image'] ='';
							}

							if(get_user_info($data['user_id'],'is_verified')==1){
								$row['is_verified']="true";
							}
							else{
								$row['is_verified']="false";
							}

							$sql_follower = "SELECT * FROM willdev_follows WHERE `user_id`='".$data['user_id']."' AND `follower_id`= '$user_id'"; 

							$res_follower = mysqli_query($mysqli,$sql_follower);
							$num_follower = mysqli_num_rows($res_follower);
					 		
					    	if ($num_follower > 0)
							{		 
								$row['already_follow']=true;
							}		 
							else
							{
					 			$row['already_follow']=false;
							}

							mysqli_free_result($res);

							//Related Image Status
							$layout=$data['image_layout'];
							$cat_id=$data['cat_id'];
		 					
				   			// get comments related to image status
							$sql_comment="SELECT * FROM willdev_comments WHERE `post_id`='".$status_id."' AND `status`='1' AND `type`='".$type."' ORDER BY id DESC LIMIT 2";

							$res=mysqli_query($mysqli,$sql_comment); 

							if($res->num_rows > 0)
							{
								while ($row_comments=mysqli_fetch_assoc($res)) 
								{
									$row_comment['comment_id'] = $row_comments['id'];			 
									$row_comment['user_id'] = $row_comments['user_id'];			 
									$row_comment['user_name'] = get_user_info($row_comments['user_id'],'name')?get_user_info($row_comments['user_id'],'name'):$row_comments['user_name'];			 

									if(get_user_info($row_comments['user_id'],'user_image')!='')
									{
										$row_comment['user_image'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_image');
									}	
									else
									{
										$row_comment['user_image'] ='';
									}

									$row_comment['post_id'] = $row_comments['post_id'];
									$row_comment['status_type'] = $row_comments['type'];
									$row_comment['comment_text'] = $row_comments['comment_text'];
									$row_comment['comment_date'] = calculate_time_span($row_comments['comment_on']);

									$row['user_comments'][]= $row_comment;
								}
							}
							else
							{	
								$row['user_comments']= array();
							}

							
	 					}
	 					else{
	 						$success=0;
	 					}
	 				}
 				break;

 			case 'gif':
 					{
	 					$sql="SELECT * FROM willdev_img_status LEFT JOIN willdev_category
	 							ON willdev_img_status.`cat_id`=willdev_category.`cid`
	 							WHERE willdev_img_status.`id`='$status_id' AND willdev_img_status.`status_type`='$type'  AND willdev_category.`status`='1'";

	 					$res=mysqli_query($mysqli, $sql) or die('Error in fetching =>'.mysqli_error($mysqli));

	 					if($res->num_rows > 0){
	 						$data = mysqli_fetch_assoc($res);

							$row['total_comment'] = CountRow('willdev_comments',"post_id='".$status_id."' AND type='".$type."'");

							$row['id'] = $data['id'];
							$row['cat_id'] = $data['cat_id'];
							$row['status_type'] = $data['status_type'];
							$row['status_title'] = $data['image_title'];
							$row['status_layout'] = $data['image_layout'];
							$row['video_url'] = '';
							
							$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

							if($data['image_layout']=='Landscape'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
							}
							else if($data['image_layout']=='Portrait'){
								$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
							}

							$row['total_likes'] = $data['total_likes'];
				  			$row['total_viewer'] = $data['total_views']; 
				  			$row['total_download'] = $data['total_download']; 

							$row['category_name'] = $data['category_name'];

				    		$row['quote_bg'] = '';
				    		$row['quote_font'] = '';

				    		$res_like = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$status_id."' && `device_id`='".$user_id."' && `like_type`='".$type."'"); 
				    		
				    		$num_like = mysqli_num_rows($res_like);
				 		
				            if ($num_like > 0)
						    {
				    			$row['already_like']=true;
				    		}
				    		else
				    		{
				    			$row['already_like']=false;
				    		}

				    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$type);

				    		$row['watermark_image'] = '';

							$row['watermark_on_off'] = '';

				    		$row['user_id'] = $data['user_id'];			 
							$row['user_name'] = get_user_info($data['user_id'],'name');			 
							
							if(get_user_info($data['user_id'],'user_image')!='')
							{
								$row['user_image'] = $file_path.'images/'.get_user_info($data['user_id'],'user_image');
							}	
							else
							{
								$row['user_image'] ='';
							}

							if(get_user_info($data['user_id'],'is_verified')==1){
								$row['is_verified']="true";
							}
							else{
								$row['is_verified']="false";
							}

							//Related gif Status
							$layout=$data['image_layout'];
							$cat_id=$data['cat_id'];

							$sql_follower = "SELECT * FROM willdev_follows WHERE `user_id`='".$data['user_id']."' AND `follower_id`= '$user_id'"; 

							$res_follower = mysqli_query($mysqli,$sql_follower);
							$num_follower = mysqli_num_rows($res_follower);
					 		
					    	if ($num_follower > 0)
							{		 
								$row['already_follow']=true;
							}		 
							else
							{
					 			$row['already_follow']=false;
							}

							mysqli_free_result($res);

							
				   			
				   			// get comments related to image status
							$sql_comment="SELECT * FROM willdev_comments WHERE `post_id`='".$status_id."' AND `status`='1' AND `type`='".$type."' ORDER BY id DESC LIMIT 2";

							$res=mysqli_query($mysqli,$sql_comment); 

							if($res->num_rows > 0)
							{
								while ($row_comments=mysqli_fetch_assoc($res)) 
								{
									$row_comment['comment_id'] = $row_comments['id'];			 
									$row_comment['user_id'] = $row_comments['user_id'];			 
									$row_comment['user_name'] = get_user_info($row_comments['user_id'],'name')?get_user_info($row_comments['user_id'],'name'):$row_comments['user_name'];			 

									if(get_user_info($row_comments['user_id'],'user_image')!='')
									{
										$row_comment['user_image'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_image');
									}	
									else
									{
										$row_comment['user_image'] ='';
									}

									$row_comment['post_id'] = $row_comments['post_id'];
									$row_comment['status_type'] = $row_comments['type'];
									$row_comment['comment_text'] = $row_comments['comment_text'];
									$row_comment['comment_date'] = calculate_time_span($row_comments['comment_on']);

									$row['user_comments'][]= $row_comment;
								}
							}
							else
							{	
								$row['user_comments']= array();
							}

							
	 					}
	 					else{
	 						$success=0;
	 					}
	 				}
 				break;

 			case 'quote':
 					{
	 					$sql="SELECT * FROM willdev_quotes LEFT JOIN willdev_category
	 							ON willdev_quotes.`cat_id`=willdev_category.`cid`
	 							WHERE willdev_quotes.`id`='$status_id' AND willdev_category.`status`='1'";

	 					$res=mysqli_query($mysqli, $sql) or die('Error in fetching =>'.mysqli_error($mysqli));

	 					if($res->num_rows > 0){
	 						$data = mysqli_fetch_assoc($res);

							$row['total_comment'] = CountRow('willdev_comments',"post_id='".$status_id."' AND type='quote'");

							$row['id'] = $data['id'];
							$row['cat_id'] = $data['cat_id'];
							$row['status_type'] = 'quote';
							$row['status_title'] = stripslashes($data['quote']);
							$row['status_layout'] = 'Landscape';
							$row['video_url'] = '';
							
							$row['status_thumbnail_b'] = '';
							$row['status_thumbnail_s'] = '';

							$row['total_likes'] = $data['total_likes'];
				  			$row['total_viewer'] = $data['total_views']; 
				  			$row['total_download'] = '';

							$row['category_name'] = $data['category_name'];

				    		$row['quote_bg'] = '#'.$data['quote_bg'];
				    		$row['quote_font'] = $data['quote_font'];

				    		$res_like = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$status_id."' && `device_id`='".$user_id."' && `like_type`='quote'"); 
				    		
				    		$num_like = mysqli_num_rows($res_like);
				 		
				            if ($num_like > 0)
						    {
				    			$row['already_like']=true;
				    		}
				    		else
				    		{
				    			$row['already_like']=false;
				    		}

				    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

				    		$row['watermark_image'] = '';

							$row['watermark_on_off'] = '';

				    		$row['user_id'] = $data['user_id'];			 
							$row['user_name'] = get_user_info($data['user_id'],'name');			 
							
							if(get_user_info($data['user_id'],'user_image')!='')
							{
								$row['user_image'] = $file_path.'images/'.get_user_info($data['user_id'],'user_image');
							}	
							else
							{
								$row['user_image'] ='';
							}

							if(get_user_info($data['user_id'],'is_verified')==1){
								$row['is_verified']="true";
							}
							else{
								$row['is_verified']="false";
							}

							$sql_follower = "SELECT * FROM willdev_follows WHERE `user_id`='".$data['user_id']."' AND `follower_id`= '$user_id'"; 

							$res_follower = mysqli_query($mysqli,$sql_follower);
							$num_follower = mysqli_num_rows($res_follower);
					 		
					    	if ($num_follower > 0)
							{		 
								$row['already_follow']=true;
							}		 
							else
							{
					 			$row['already_follow']=false;
							}

							mysqli_free_result($res);

							// for related status

							$cat_id=$data['cat_id'];
							
				   			// get comments related to image status
							$sql_comment="SELECT * FROM willdev_comments WHERE `post_id`='".$status_id."' AND `status`='1' AND `type`='quote' ORDER BY id DESC LIMIT 2";

							$res=mysqli_query($mysqli,$sql_comment); 

							if($res->num_rows > 0)
							{
								while ($row_comments=mysqli_fetch_assoc($res)) 
								{
									$row_comment['comment_id'] = $row_comments['id'];			 
									$row_comment['user_id'] = $row_comments['user_id'];			 
									$row_comment['user_name'] = get_user_info($row_comments['user_id'],'name')?get_user_info($row_comments['user_id'],'name'):$row_comments['user_name'];			 

									if(get_user_info($row_comments['user_id'],'user_image')!='')
									{
										$row_comment['user_image'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_image');
									}	
									else
									{
										$row_comment['user_image'] ='';
									}

									$row_comment['post_id'] = $row_comments['post_id'];
									$row_comment['status_type'] = $row_comments['type'];
									$row_comment['comment_text'] = $row_comments['comment_text'];
									$row_comment['comment_date'] = calculate_time_span($row_comments['comment_on']);

									$row['user_comments'][]= $row_comment;
								}
							}
							else
							{	
								$row['user_comments']= array();
							}

							
	 					}
	 					else{
	 						$status=0;
	 					}
	 				}
 				break;
 			
 			default:
 				break;
 		}

 		$layout=($layout!='') ? $layout : 'Landscape';

		// for video status related
		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`id` <> '$status_id' AND willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`video_layout`='$layout' AND willdev_video.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_video.`id` DESC LIMIT 5";
 		}
 		else{
			$query="SELECT * FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`id` <> '$status_id' AND willdev_video.`status`='1' AND willdev_category.`status`='1' AND willdev_video.`video_layout`='$layout' AND willdev_video.`cat_id`='$cat_id' ORDER BY willdev_video.`id` DESC LIMIT 5";
 		}

		$sql_video_related=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));

		if($sql_video_related->num_rows > 0){

			while($data_related = mysqli_fetch_assoc($sql_video_related))
			{	
				$row_related['id'] = $data_related['id'];
				$row_related['status_type'] = 'video';
				$row_related['status_title'] = $data_related['video_title'];
				$row_related['status_layout'] = $data_related['video_layout'];
				
				$row_related['status_thumbnail_b'] = $file_path.'images/'.$data_related['video_thumbnail'];

				if($data_related['video_layout']=='Landscape'){
					$row_related['status_thumbnail_s'] = get_thumb('images/'.$data_related['video_thumbnail'],'500x280');
				}
				else if($data_related['video_layout']=='Portrait'){
					$row_related['status_thumbnail_s'] = get_thumb('images/'.$data_related['video_thumbnail'],'280x500');
				}

				$row_related['total_likes'] = $data_related['total_likes'];
	  			$row_related['total_viewer'] = $data_related['totel_viewer'];  

				$row_related['category_name'] = $data_related['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data_related['id']."'AND `device_id`='".$get_method['user_id']."' AND `like_type`='video'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row_related['already_like']=true;
	    		}
	    		else
	    		{
	    			$row_related['already_like']=false;
	    		}

	    		$row_related['is_favourite']=get_favourite_info($data_related['id'],$get_method['user_id'],'video');

	    		$row_related['quote_bg'] = '';
	    		$row_related['quote_font'] = '';

				$row['related'][]= $row_related;
			}
		}
		else{
			$row['related']=array();
		}

		// End

		mysqli_free_result($sql_video_related);

		// for image status related

		if(!empty($lang_ids)){

 			$column='';
			foreach ($lang_ids as $key => $value) {
				$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
			}

			$column=rtrim($column,'OR ');

			$query="SELECT * FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`id` <> '$status_id' AND willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`image_layout`='$layout' AND willdev_img_status.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_img_status.`id` DESC LIMIT 5";
 		}
 		else{
			$query="SELECT * FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`id` <> '$status_id' AND willdev_img_status.`status`='1' AND willdev_category.`status`='1' AND willdev_img_status.`image_layout`='$layout' AND willdev_img_status.`cat_id`='$cat_id' ORDER BY willdev_img_status.`id` DESC LIMIT 5";
 		}

		$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

		if($sql_image->num_rows > 0){
			while($data_related = mysqli_fetch_assoc($sql_image))
			{	
				$row_related['id'] = $data_related['id'];
				$row_related['status_type'] = $data_related['status_type'];
				$row_related['status_title'] = $data_related['image_title'];
				$row_related['status_layout'] = $data_related['image_layout'];
				
				$row_related['status_thumbnail_b'] = $file_path.'images/'.$data_related['image_file'];

				if($data_related['image_layout']=='Landscape'){
					$row_related['status_thumbnail_s'] = get_thumb('images/'.$data_related['image_file'],'500x280');
				}
				else if($data_related['image_layout']=='Portrait'){
					$row_related['status_thumbnail_s'] = get_thumb('images/'.$data_related['image_file'],'280x500');
				}

				$row_related['total_likes'] = $data_related['total_likes'];
	  			$row_related['total_viewer'] = $data_related['total_views'];  

				$row_related['category_name'] = $data_related['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data_related['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data_related['status_type']."'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row_related['already_like']=true;
	    		}
	    		else
	    		{
	    			$row_related['already_like']=false;
	    		}

	    		$row_related['is_favourite']=get_favourite_info($data_related['id'],$get_method['user_id'],$data_related['status_type']);

	    		$row_related['quote_bg'] = '';
	    		$row_related['quote_font'] = '';
	 
				$row['related'][]= $row_related;
			}
		}
		// End
		mysqli_free_result($sql_image);
 		
		// for quotes status related

		if($layout=='Landscape')
		{
			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
					WHERE willdev_quotes.`id` <> '$status_id' AND willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' AND ($column) ORDER BY willdev_quotes.`id` DESC LIMIT 5";
	 		}
	 		else{

				$query="SELECT * FROM willdev_quotes
					LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
					WHERE willdev_quotes.`id` <> '$status_id' AND willdev_quotes.`status`='1' AND willdev_category.`status`='1' AND willdev_quotes.`cat_id`='$cat_id' ORDER BY willdev_quotes.`id` DESC LIMIT 5";
	 		}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			if($sql_quote->num_rows > 0){
				while($data_related = mysqli_fetch_assoc($sql_quote))
				{	
					$row_related['id'] = $data_related['id'];
					$row_related['status_type'] = 'quote';
					$row_related['status_title'] = stripslashes($data_related['quote']);
					$row_related['status_layout'] = '';
					
					$row_related['status_thumbnail_b'] = '';

					$row_related['status_thumbnail_s'] = '';

					$row_related['total_likes'] = $data_related['total_likes'];
		  			$row_related['total_viewer'] = $data_related['total_views'];  

					$row_related['category_name'] = $data_related['category_name'];

					$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data_related['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
		    		
		    		$num_rows1 = mysqli_num_rows($query1);
		 		
		            if ($num_rows1 > 0)
				    {
		    			$row_related['already_like']=true;
		    		}
		    		else
		    		{
		    			$row_related['already_like']=false;
		    		}

		    		$row_related['is_favourite']=get_favourite_info($data_related['id'],$get_method['user_id'],'quote');

		    		$row_related['quote_bg'] = '#'.$data_related['quote_bg'];
		    		$row_related['quote_font'] = $data_related['quote_font'];
		 
					$row['related'][]= $row_related;
				}
			}	

			// End
			mysqli_free_result($sql_quote);

		}

		$row['video_views_status_ad'] = $settings_details['video_views_status'];
		
		$row['like_video_status_ad'] = $settings_details['like_video_points_status'];
		$row['download_video_status_ad'] = $settings_details['download_video_points_status'];
		
		$row['like_image_status_ad'] = $settings_details['like_image_points_status'];
		$row['download_image_status_ad'] = $settings_details['download_image_points_status'];
		
		$row['like_gif_points_status_ad'] = $settings_details['like_gif_points_status'];
		$row['download_gif_status_ad'] = $settings_details['download_gif_points_status'];

		$row['like_quotes_status_ad'] = $settings_details['like_quotes_points_status'];

		$row['success']=$success;
		
		// array_push($jsonObj,$row);

		$set['ANDROID_REWARDS_APP'] = $row;	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="single_status_view_count")
 	{
 		$type=$get_method['type'];
 		$post_id=$get_method['post_id'];
 		$user_id=$get_method['user_id'];
 		$owner_id=$get_method['owner_id'];

 		if($owner_id!=$user_id){
 			switch ($type) {
	 			case 'video':
	 				{
	 					$sql_view="UPDATE willdev_video SET totel_viewer= totel_viewer + 1  WHERE id = '$post_id'";
	 					mysqli_query($mysqli, $sql_view);

	 					if($settings_details['video_views_status']=='true')
	 					{
	 						$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['video_view_activity']."'";

	 						$res=mysqli_query($mysqli, $sql);

	 						if($res->num_rows == 0){
	 							// insert in user activity

	 							$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['video_views']."' WHERE `id` = '$user_id'";

	 							mysqli_query($mysqli, $user_update);

	 							if(user_reward_activity($post_id,$user_id,$app_lang['video_view_activity'],$settings_details['video_views']))
	 							{
	 								if($settings_details['other_user_video_status']=='true')
					 				{
					 					$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$settings_details['video_views']."'  WHERE id = '".$owner_id."'");

					            		user_reward_activity($post_id, $owner_id, $app_lang['other_video_view'],$settings_details['other_user_video_point'],true);
					 				}
	 							}
	 						}
	 					}

	 				}
	 				break;

	 			case 'image':
	 				{
		 				$sql_view="UPDATE willdev_img_status SET total_views= total_views + 1  WHERE id = '$post_id'";
		 				mysqli_query($mysqli, $sql_view);

		 				$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['image_view_activity']."'";

		 				$res=mysqli_query($mysqli, $sql);

		 				if($res->num_rows == 0){
	 							// insert in user activity

		 					$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['image_views']."' WHERE `id` = '$user_id'";

		 					mysqli_query($mysqli, $user_update);

		 					if(user_reward_activity($post_id,$user_id,$app_lang['image_view_activity'],$settings_details['image_views'])){

		 						if($settings_details['other_user_image_status']=='true')
		 						{
		 							$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$settings_details['image_views']."'  WHERE id = '".$owner_id."'");

		 							user_reward_activity($post_id, $owner_id, $app_lang['other_image_view'],$settings_details['other_user_image_point'],true);
		 						}
		 					}
		 				}
		 			}
	 				break;

	 			case 'gif':
	 				{
		 				$sql_view="UPDATE willdev_img_status SET total_views= total_views + 1  WHERE id = '$post_id'";
		 				mysqli_query($mysqli, $sql_view);

		 				
		 				$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['gif_view_activity']."'";

		 				$res=mysqli_query($mysqli, $sql);

		 				if($res->num_rows == 0){
	 							// insert in user activity

		 					$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['gif_views']."' WHERE `id` = '$user_id'";

		 					mysqli_query($mysqli, $user_update);

		 					if(user_reward_activity($post_id,$user_id,$app_lang['gif_view_activity'],$settings_details['gif_views']))
		 					{
		 						if($settings_details['other_user_gif_status']=='true')
		 						{
		 							$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$settings_details['gif_views']."'  WHERE id = '".$owner_id."'");

		 							user_reward_activity($post_id, $owner_id, $app_lang['other_gif_view'],$settings_details['other_user_gif_point'],true);
		 						}
		 					}
		 				}
		 			}
	 				break;

	 			case 'quote':
	 				{
		 				$sql_view="UPDATE willdev_quotes SET total_views= total_views + 1  WHERE id = '$post_id'";
		 				mysqli_query($mysqli, $sql_view);

		 				$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['quotes_view_activity']."'";

		 				$res=mysqli_query($mysqli, $sql);

		 				if($res->num_rows == 0){
	 							// insert in user activity

		 					$user_update="UPDATE willdev_users SET `total_point`= total_point + '".$settings_details['quotes_views']."' WHERE `id` = '$user_id'";

		 					mysqli_query($mysqli, $user_update);

		 					if(user_reward_activity($post_id,$user_id,$app_lang['quotes_view_activity'],$settings_details['quotes_views']))
		 					{
		 						if($settings_details['other_user_quotes_status']=='true')
		 						{
		 							$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$settings_details['quotes_views']."'  WHERE id = '".$owner_id."'");

		 							user_reward_activity($post_id, $owner_id, $app_lang['other_quotes_view'],$settings_details['other_user_quotes_point']);
		 						}
		 					}
		 				}
		 			}
	 				break;
	 			
	 			default:
	 				# code...
	 				break;
	 		}

	 		$set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['view_count'],'success'=>1);
 		}
 		else{
 			$set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['view_err_count'],'success'=>0);
 		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}

 	else if($get_method['method_name']=="user_status_like")
 	{
 		$type=$get_method['type'];
 		$post_id=$get_method['post_id'];
 		$user_id=$get_method['user_id'];

 		$sql_like="SELECT * FROM willdev_like WHERE `post_id`='$post_id' AND `device_id`='$user_id' AND `like_type`='$type'";

    	$res_like=mysqli_query($mysqli, $sql_like);

    	$total_likes='0';

    	if(mysqli_num_rows($res_like) == 0)
		{
			//	insert in like table
			$insertSql="INSERT INTO `willdev_like`(`post_id`, `device_id`, `likes`, `like_type`) VALUES ('$post_id','$user_id','1','$type')";

			$insertRes=mysqli_query($mysqli, $insertSql) or die(mysqli_error($mysqli));	

			$query = mysqli_query($mysqli,"SELECT SUM(`likes`) AS `total_likes` FROM willdev_like WHERE post_id='$post_id' AND `like_type`='$type'") or die(mysqli_error($mysqli));
				 
			$row = mysqli_fetch_assoc($query); 
			$total_likes = $row['total_likes'];
		}

		switch ($type) {
			case 'video':
				{
					if(mysqli_num_rows($res_like) == 0)
					{
						$data = array(	
							'total_likes'  =>  $total_likes
						);

						$edit=Update('willdev_video', $data, "WHERE id = '$post_id'");

						//Points Count
						if($settings_details['like_video_points_status']=='true')
						{
							$like_point=$settings_details['like_video_points']; 

							$qry = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$post_id' AND `user_id`='$user_id' AND `activity_type`='".$app_lang['video_like_activity']."'";

							$result = mysqli_query($mysqli,$qry);

							if ($result->num_rows == 0)
							{
								$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$like_point."'  WHERE `id` = '".$user_id."'");
							}            

							user_reward_activity($post_id, $user_id, $app_lang['video_like_activity'], $like_point);
						}
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['like'],'success'=>'1','activity_status'=>'1','total_likes'=>$total_likes);
					}
					else{

						$query = mysqli_query($mysqli,"SELECT `total_likes` FROM willdev_video WHERE `id`='$post_id'"); 
						$row = mysqli_fetch_assoc($query);         
						$total_likes = $row['total_likes']-1;

						$data = array(  
							'total_likes'  =>  $total_likes
						);

						$quates_edit=Update('willdev_video', $data, "WHERE `id` = '$post_id'");   

						$res_delete=mysqli_query($mysqli,"DELETE FROM `willdev_like` WHERE `device_id`='$user_id' AND `post_id`='$post_id' AND `like_type`='$type'");

						if($res_delete){
							//Points Count
							if($settings_details['like_video_points_status']=='true')
							{
								$like_point=$settings_details['like_video_points'];

								$update_point=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= (total_point - '".$like_point."')  WHERE `id` = '$user_id'");

								$res_delete_reward=mysqli_query($mysqli,"DELETE FROM `willdev_users_rewards_activity` WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `activity_type`='".$app_lang['video_like_activity']."'");
							}

							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike'],'success'=>'1','activity_status'=>'0','total_likes'=>$total_likes);
						}else{
							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike_err'],'success'=>'0');
						}
					}
				}
				break;

			case 'image':
				{
					if(mysqli_num_rows($res_like) == 0)
					{
						$data = array(	
							'total_likes'  =>  $total_likes
						);

						$edit=Update('willdev_img_status', $data, "WHERE `id` = '$post_id'");

						//Points Count
						if($settings_details['like_image_points_status']=='true')
						{
							$like_point=$settings_details['like_image_points']; 

							$qry = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$post_id' AND `user_id`='$user_id' AND `activity_type`='".$app_lang['image_like_activity']."'";

							$result = mysqli_query($mysqli,$qry);

							if ($result->num_rows == 0)
							{
								$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$like_point."'  WHERE `id` = '".$user_id."'");
							}            

							user_reward_activity($post_id, $user_id, $app_lang['image_like_activity'], $like_point);
						}
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['like'],'success'=>'1','activity_status'=>'1','total_likes'=>$total_likes);
					}
					else{

						$query = mysqli_query($mysqli,"SELECT `total_likes` FROM willdev_img_status WHERE `id`='$post_id'"); 
						$row = mysqli_fetch_assoc($query);         
						$total_likes = $row['total_likes']-1;

						$data = array(  
							'total_likes'  =>  $total_likes
						);

						$quates_edit=Update('willdev_img_status', $data, "WHERE `id` = '$post_id'");   

						$res_delete=mysqli_query($mysqli,"DELETE FROM `willdev_like` WHERE `device_id`='$user_id' AND `post_id`='$post_id' AND `like_type`='$type'");

						if($res_delete){
							//Points Count
							if($settings_details['like_image_points_status']=='true')
							{
								$like_point=$settings_details['like_image_points'];

								$update_point=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= (total_point - '".$like_point."')  WHERE `id` = '$user_id'");

								$res_delete_reward=mysqli_query($mysqli,"DELETE FROM `willdev_users_rewards_activity` WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `activity_type`='".$app_lang['image_like_activity']."'");
							}

							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike'],'success'=>'1','activity_status'=>'0','total_likes'=>$total_likes);
						}else{
							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike_err'],'success'=>'0');
						}
					}
				}
				break;

			case 'gif':
				{
					if(mysqli_num_rows($res_like) == 0)
					{
						$data = array(	
							'total_likes'  =>  $total_likes
						);

						$edit=Update('willdev_img_status', $data, "WHERE `id` = '$post_id'");

						//Points Count
						if($settings_details['like_gif_points_status']=='true')
						{
							$like_point=$settings_details['like_gif_points']; 

							$qry = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$post_id' AND `user_id`='$user_id' AND `activity_type`='".$app_lang['gif_like_activity']."'";

							$result = mysqli_query($mysqli,$qry);

							if ($result->num_rows == 0)
							{
								$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$like_point."'  WHERE `id` = '".$user_id."'");
							}            

							user_reward_activity($post_id, $user_id, $app_lang['gif_like_activity'], $like_point);
						}
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['like'],'success'=>'1','activity_status'=>'1','total_likes'=>$total_likes);
					}
					else{

						$query = mysqli_query($mysqli,"SELECT `total_likes` FROM willdev_img_status WHERE `id`='$post_id'"); 
						$row = mysqli_fetch_assoc($query);         
						$total_likes = $row['total_likes']-1;

						$data = array(  
							'total_likes'  =>  $total_likes
						);

						$quates_edit=Update('willdev_img_status', $data, "WHERE `id` = '$post_id'");   

						$res_delete=mysqli_query($mysqli,"DELETE FROM `willdev_like` WHERE `device_id`='$user_id' AND `post_id`='$post_id' AND `like_type`='$type'");

						if($res_delete){
							//Points Count
							if($settings_details['like_gif_points_status']=='true')
							{
								$like_point=$settings_details['like_gif_points'];

								$update_point=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= (total_point - '".$like_point."')  WHERE `id` = '$user_id'");

								$res_delete_reward=mysqli_query($mysqli,"DELETE FROM `willdev_users_rewards_activity` WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `activity_type`='".$app_lang['gif_like_activity']."'");
							}

							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike'],'success'=>'1','activity_status'=>'0','total_likes'=>$total_likes);
						}else{
							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike_err'],'success'=>'0');
						}
					}
				}
				break;

			case 'quote':
				{
					if(mysqli_num_rows($res_like) == 0)
					{
						$data = array(	
							'total_likes'  =>  $total_likes
						);

						$edit=Update('willdev_quotes', $data, "WHERE `id` = '$post_id'");

						//Points Count
						if($settings_details['like_quotes_points_status']=='true')
						{
							$like_point=$settings_details['like_quotes_points']; 

							$qry = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$post_id' AND `user_id`='$user_id' AND `activity_type`='".$app_lang['quotes_like_activity']."'";

							$result = mysqli_query($mysqli,$qry);

							if ($result->num_rows == 0)
							{
								$user_view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$like_point."'  WHERE `id` = '".$user_id."'");
							}            

							user_reward_activity($post_id, $user_id, $app_lang['quotes_like_activity'], $like_point);
						}
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['like'],'success'=>'1','activity_status'=>'1','total_likes'=>$total_likes);
					}
					else{

						$query = mysqli_query($mysqli,"SELECT `total_likes` FROM willdev_quotes WHERE `id`='$post_id'"); 
						$row = mysqli_fetch_assoc($query);         
						$total_likes = $row['total_likes']-1;

						$data = array(  
							'total_likes'  =>  $total_likes
						);

						$quates_edit=Update('willdev_quotes', $data, "WHERE `id` = '$post_id'");   

						$res_delete=mysqli_query($mysqli,"DELETE FROM `willdev_like` WHERE `device_id`='$user_id' AND `post_id`='$post_id' AND `like_type`='$type'");

						if($res_delete){
							//Points Count
							if($settings_details['like_quotes_points_status']=='true')
							{
								$like_point=$settings_details['like_quotes_points'];

								$update_point=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= (total_point - '".$like_point."')  WHERE `id` = '$user_id'");

								$res_delete_reward=mysqli_query($mysqli,"DELETE FROM `willdev_users_rewards_activity` WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `activity_type`='".$app_lang['quotes_like_activity']."'");
							}

							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike'],'success'=>'1','activity_status'=>'0','total_likes'=>$total_likes);
						}else{
							$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['unlike_err'],'success'=>'0');
						}
					}
				}
				break;
			
			default:
				break;
		}
 		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}

 	else if($get_method['method_name']=="single_status_download")
 	{
 		$type=$get_method['type'];
 		$post_id=$get_method['post_id'];
 		$user_id=$get_method['user_id'];

 		$total_download=0;

		switch ($type) {
			case 'video':
				{
					

					if($settings_details['download_video_points_status']=='true')
					{
						$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['video_download_activity']."'";

						$res=mysqli_query($mysqli, $sql);

						if($res->num_rows == 0){
							// insert in user activity

							$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['download_video_points']."' WHERE `id` = '$user_id'";

							mysqli_query($mysqli, $user_update);

							user_reward_activity($post_id,$user_id,$app_lang['video_download_activity'],$settings_details['download_video_points']);

							$sql_view="UPDATE willdev_video SET total_download= total_download + 1  WHERE id = '$post_id'";
							mysqli_query($mysqli, $sql_view);

						}
					}

					$total_download=get_single_info($post_id, 'total_download', 'video');

				}
				break;

			case 'image':
				{
					
					if($settings_details['download_image_points_status']=='true')
					{
						$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['image_download_activity']."'";

						$res=mysqli_query($mysqli, $sql);

						if($res->num_rows == 0){
							// insert in user activity

							$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['download_image_points']."' WHERE `id` = '$user_id'";

							mysqli_query($mysqli, $user_update);

							user_reward_activity($post_id,$user_id,$app_lang['image_download_activity'],$settings_details['download_image_points']);

							$sql_view="UPDATE willdev_img_status SET total_download= total_download + 1  WHERE id = '$post_id'";

							mysqli_query($mysqli, $sql_view);

						}
					}

					$total_download=get_single_info($post_id, 'total_download', 'image');

				}
				break;

			case 'gif':
				{
					
					if($settings_details['download_gif_points_status']=='true')
					{
						$sql="SELECT * FROM willdev_users_rewards_activity WHERE `post_id`='$post_id' AND `user_id`='$user_id' AND `activity_type` = '".$app_lang['gif_download_activity']."'";

						$res=mysqli_query($mysqli, $sql);

						if($res->num_rows == 0){
							// insert in user activity

							$user_update="UPDATE willdev_users SET `total_point`= `total_point` + '".$settings_details['download_gif_points']."' WHERE `id` = '$user_id'";

							mysqli_query($mysqli, $user_update);

							user_reward_activity($post_id,$user_id,$app_lang['gif_download_activity'],$settings_details['download_gif_points']);

							$sql_view="UPDATE willdev_img_status SET total_download= total_download + 1  WHERE id = '$post_id'";
							mysqli_query($mysqli, $sql_view);
						}
					}

					$total_download=get_single_info($post_id, 'total_download', 'gif');

				}
				break;
			
			default:
				# code...
				break;
		}

		$set['ANDROID_REWARDS_APP'] = array('total_download'=>$total_download,'msg'=>$app_lang['video_download'],'success'=>1);
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
 	}


	else if($get_method['method_name']=="search_status")		
 	{	
 		$post_order_by=API_CAT_POST_ORDER_BY;
 		$search_text=strip_tags(trim($get_method['search_text']));

 		$user_id=$get_method['user_id'];
		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

 		$jsonObj= array();

 		if(isset($get_method['lang_ids']) && $get_method['lang_ids']!=''){
			$lang_ids = explode(',', $get_method['lang_ids']);
		}
		else{
			$lang_ids = array();
		}

 		if($get_method['filter_value']!="")
 		{
 			$filter_value=trim($get_method['filter_value']);

			// for video status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='$filter_value' AND ($column) AND (willdev_video.`video_title` LIKE '%$search_text%' OR willdev_video.`video_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND willdev_video.`video_layout`='$filter_value' AND (willdev_video.`video_title` LIKE '%$search_text%' OR willdev_video.`video_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
			

			$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for image status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_img_status.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='$filter_value' AND ($column) AND (willdev_img_status.`image_title` LIKE '%$search_text%' OR willdev_img_status.`image_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_img_status
						LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
						WHERE willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='$filter_value' AND (willdev_img_status.`image_title` LIKE '%$search_text%' OR willdev_img_status.`image_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}

			
			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));


			// for quotes status

			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_quotes.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE ($column) AND willdev_quotes.`status`='1' AND (willdev_quotes.`quote` LIKE '%$search_text%' OR willdev_quotes.`quote_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_quotes
						LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
						WHERE willdev_quotes.`status`='1' AND (willdev_quotes.`quote` LIKE '%$search_text%' OR willdev_quotes.`quote_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
			
			

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

 		}
 		else
 		{
			// for video status
			if(!empty($lang_ids)){

	 			$column='';
				foreach ($lang_ids as $key => $value) {
					$column.='FIND_IN_SET('.$value.', willdev_video.`lang_ids`) OR ';
				}

				$column=rtrim($column,'OR ');

				$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE ($column) AND willdev_video.`status`='1' AND (willdev_video.`video_title` LIKE '%$search_text%' OR willdev_video.`video_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
	 		else{
	 			$query="SELECT * FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`status`='1' AND (willdev_video.`video_title` LIKE '%$search_text%' OR willdev_video.`video_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
	 		}
			

			$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for image status
			
			$query="SELECT * FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`status`='1' AND (willdev_img_status.`image_title` LIKE '%$search_text%' OR willdev_img_status.`image_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";

			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for quotes status
			
			$query="SELECT * FROM willdev_quotes
				LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
				WHERE willdev_quotes.`status`='1' AND (willdev_quotes.`quote` LIKE '%$search_text%' OR willdev_quotes.`quote_tags` LIKE '%$search_text%' OR willdev_category.`category_name` LIKE '%$search_text%') ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
 		}
		
		while($data = mysqli_fetch_assoc($sql_video))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

			if($data['video_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
			}
			else if($data['video_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}

		while($data = mysqli_fetch_assoc($sql_image))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			if($data['image_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
			}
			else if($data['image_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';
 
			array_push($jsonObj,$row);
		}

		if($get_method['filter_value']=="" OR $get_method['filter_value']=='Landscape')
 		{
			while($data = mysqli_fetch_assoc($sql_quote))
			{	
				$row['id'] = $data['id'];
				$row['status_type'] = 'quote';
				$row['status_title'] = stripslashes($data['quote']);
				$row['status_layout'] = 'Landscape';
				
				$row['status_thumbnail_b'] = '';

				$row['status_thumbnail_s'] = '';

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

	    		$row['quote_bg'] = '#'.$data['quote_bg'];
	    		$row['quote_font'] = $data['quote_font'];
	 
				array_push($jsonObj,$row);
			}
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

 	}
 	else if($get_method['method_name']=="upload_img_gif_status")	
	{

		$uploadStatus=true;

		$lang_ids=$get_method['lang_ids'];
        $image_tags=htmlentities(trim($get_method['image_tags']));
        $user_id=$get_method['user_id'];

        $status_type=$get_method['status_type'];

        $sql_user="SELECT * FROM willdev_users WHERE id = '$user_id'";
		$res_user=mysqli_query($mysqli, $sql_user);

		$row_user=mysqli_fetch_assoc($res_user);

        if($status_type=='image'){
        	define("AUTO_APPROVE",$settings_details['auto_approve_img']);
        }
        else{
        	define("AUTO_APPROVE",$settings_details['auto_approve_gif']);
        }

        if(AUTO_APPROVE=='on'){
			if($row_user['is_verified']==1){
				$status=1;
			}else{
				$status=0;
			}
		}else{
			$status=0;
		}

        $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);

        $image_file=rand(0,99999)."_".$status_type.".".$ext;

        //Main Image
        $tpath1='images/'.$image_file;   

        $tmp = $_FILES['image_file']['tmp_name'];
    	if(!move_uploaded_file($tmp, $tpath1)){
    		$uploadStatus=false;
    	}

        if($uploadStatus){
        	$data = array( 
	          'cat_id'  =>  $get_method['cat_id'],
	          'user_id'  =>  $user_id,
	          'lang_ids'  =>  $lang_ids,
	          'image_title'  =>  htmlentities(trim($get_method['image_title'])),
	          'image_tags'  =>  $image_tags,
	          'image_layout'  =>  $get_method['image_layout'],
	          'image_file'  =>  $image_file,
	          'status_type'  =>  $status_type,
	          'status'  => $status
	        ); 

	        $insert = Insert('willdev_img_status',$data);

	        $last_id=mysqli_insert_id($mysqli);

	        if($status==1){

	        	if($status_type=='image'){
		    		if($settings_details['image_add_status']=='true')
					{
						$qry="SELECT * FROM willdev_img_status WHERE `id`='$last_id'";
						$result=mysqli_query($mysqli,$qry);
						$row=mysqli_fetch_assoc($result); 

						$user_id =$row['user_id'];

						$sql_activity = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$last_id' AND `user_id` = '$user_id' AND `activity_type`='".$app_lang['add_image']."'";
						$res_activity = mysqli_query($mysqli,$sql_activity);

						$add_point=$settings_details['image_add']; 

						if ($res_activity->num_rows == 0)
						{

							$qry2 = "SELECT * FROM willdev_users WHERE id = '".$user_id."'";
							$result2 = mysqli_query($mysqli,$qry2);
							$row2=mysqli_fetch_assoc($result2); 

							$user_total_point=$row2['total_point']+$add_point;

							$user_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='".$user_total_point."'  WHERE id = '".$user_id."'");

							user_reward_activity($last_id,$user_id,$app_lang['add_image'],$add_point);
						}

					}
		    	}
		    	else{
		    		if($settings_details['gif_add_status']=='true')
					{
						$qry="SELECT * FROM willdev_img_status where `id`='$last_id'";
						$result=mysqli_query($mysqli,$qry);
						$row=mysqli_fetch_assoc($result); 

						$user_id =$row['user_id'];

						$sql_activity = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$last_id' AND `user_id` = '$user_id' AND `activity_type`='".$app_lang['add_gif']."'";
						$res_activity = mysqli_query($mysqli,$sql_activity);

						$add_point=$settings_details['gif_add']; 

						if ($res_activity->num_rows == 0)
						{

							$qry2 = "SELECT * FROM willdev_users WHERE id = '".$user_id."'";
							$result2 = mysqli_query($mysqli,$qry2);
							$row2=mysqli_fetch_assoc($result2); 

							$user_total_point=$row2['total_point']+$add_point;

							$user_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='".$user_total_point."'  WHERE id = '".$user_id."'");

							user_reward_activity($last_id,$user_id,$app_lang['add_gif'],$add_point);
						}
					}
		    	}
	            
	            $img_path=$file_path.'images/'.$image_file;

	            // send notification to user's followers

	            $user_name=ucwords($row_user['name']);

	            $send_msg=str_replace('###', $status_type, $app_lang['add_img_gif_notify_msg']);

	            $send_msg=str_replace('$$$', $user_name, $send_msg);

	            $content = array("en" => $send_msg);

	            $sql_follower="SELECT * FROM willdev_follows, willdev_users WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id'";

	            $res_follower=mysqli_query($mysqli, $sql_follower);

	            $followers=array();

	            while ($row_follower=mysqli_fetch_assoc($res_follower)) {
	              $followers[]=$row_follower['player_id'];
	            }

	            $fields = array(
					'app_id' => ONESIGNAL_APP_ID,
					'include_player_ids' => $followers, 
					'data' => array("foo" => "bar","type" => "single_status","status_type" => $status_type,"id" => $last_id,"external_link"=>false),
					'headings'=> array("en" => APP_NAME),
					'contents' => $content,
					'big_picture' =>$img_path
				);

	            $fields = json_encode($fields);

	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.ONESIGNAL_REST_KEY));
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	            curl_setopt($ch, CURLOPT_HEADER, FALSE);
	            curl_setopt($ch, CURLOPT_POST, TRUE);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	            $notify_res = curl_exec($ch);       
	            
	            curl_close($ch);

	        }

	        $set['ANDROID_REWARDS_APP'][]=array('msg'=>ucfirst($status_type).' '.$app_lang['upload_success'],'success'=>'1');
        }
        else{
        	$set['ANDROID_REWARDS_APP'][]=array('msg'=>'Error in uploading status','success'=>'1');
        }

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="upload_quote_status")	
	{
		define("AUTO_APPROVE",$settings_details['auto_approve_quote']);

		$lang_ids=$get_method['lang_ids'];
        $quote_tags=htmlentities(trim($get_method['quote_tags']));
        $user_id=$get_method['user_id'];

		$quote=htmlentities(trim($get_method['quote']));

        $bg_color=trim($get_method['bg_color']);

        $quote_bg=dechex((float) $bg_color);
        $bg_color = substr($quote_bg, -6);

        $sql_user="SELECT * FROM willdev_users WHERE id = '$user_id'";
		$res_user=mysqli_query($mysqli, $sql_user);

		$row_user=mysqli_fetch_assoc($res_user);

        if(AUTO_APPROVE=='on'){
			if($row_user['is_verified']==1){
				$status=1;
			}else{
				$status=0;
			}
		}else{
			$status=0;
		}

        $data = array( 
          'cat_id'  =>  $get_method['cat_id'],
          'user_id'  =>  $user_id,
          'lang_ids'  =>  $lang_ids,
          'quote'  =>  $quote,
          'quote_font'  =>  $get_method['quote_font'],
          'quote_tags'  =>  $quote_tags,
          'quote_bg'  =>  $bg_color,
          'status'  => $status
        ); 

        $insert = Insert('willdev_quotes',$data);

        $last_id=mysqli_insert_id($mysqli);

        if($status==1){

        	if($settings_details['quotes_add_status']=='true')
			{
				$qry="SELECT * FROM willdev_quotes WHERE `id`='$last_id'";
				$result=mysqli_query($mysqli,$qry);
				$row=mysqli_fetch_assoc($result); 

				$user_id =$row['user_id'];

				$sql_activity = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$last_id' AND `user_id` = '$user_id' AND `activity_type`='".$app_lang['add_quote']."'";
				$res_activity = mysqli_query($mysqli,$sql_activity);

				$add_point=$settings_details['quotes_add']; 

				if($res_activity->num_rows == 0)
				{
					$qry2 = "SELECT * FROM willdev_users WHERE id = '".$user_id."'";
					$result2 = mysqli_query($mysqli,$qry2);
					$row2=mysqli_fetch_assoc($result2); 

					$user_total_point=$row2['total_point']+$add_point;

					$user_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='".$user_total_point."'  WHERE id = '".$user_id."'");

					user_reward_activity($last_id,$user_id,$app_lang['add_quote'],$add_point);

				}

			}

	    	// send notification to user's followers

	        $user_name=ucwords($row_user['name']);

            $content = array("en" => str_replace('###', $user_name, $app_lang['add_quote_notify_msg'])); 

	        $sql_follower="SELECT * FROM willdev_follows, willdev_users WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id'";

	        $res_follower=mysqli_query($mysqli, $sql_follower);

	        $followers=array();

	        while ($row_follower=mysqli_fetch_assoc($res_follower)) {
	          $followers[]=$row_follower['player_id'];
	        }

	        $fields = array(
				'app_id' => ONESIGNAL_APP_ID,
				'include_player_ids' => $followers, 
				'data' => array("foo" => "bar","type" => "single_status","status_type" => 'quote',"id" => $last_id,"external_link"=>false),
				'headings'=> array("en" => APP_NAME),
				'contents' => $content
			);

	        $fields = json_encode($fields);

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.ONESIGNAL_REST_KEY));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_HEADER, FALSE);
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	        $notify_res = curl_exec($ch);       
	        
	        curl_close($ch);
	    }
        $set['ANDROID_REWARDS_APP']=array('msg'=>'Quotes '.$app_lang['upload_success'],'success'=>'1');
		

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	} 
	else if($get_method['method_name']=="daily_upload_limit")
	{
		$type=trim($get_method['type']);

		$user_id=trim($get_method['user_id']);

		$date=date('Y-m-d');

		switch ($type) {
			case 'video':
				{
					$upload_limit=$settings_details['user_video_upload_limit'];

					$sql="SELECT * FROM willdev_users_rewards_activity WHERE `user_id`='$user_id' AND date(`date`) = '$date' AND `activity_type` LIKE '%".$app_lang['add_video']."%'";
				}
				break;

			case 'image':
				{
					$upload_limit=$settings_details['user_image_upload_limit'];

					$sql="SELECT * FROM willdev_users_rewards_activity WHERE `user_id`='$user_id' AND date(`date`) = '$date' AND `activity_type` LIKE '%".$app_lang['add_image']."%'";
				}
				break;

			case 'gif':
				{
					$upload_limit=$settings_details['user_gif_upload_limit'];

					$sql="SELECT * FROM willdev_users_rewards_activity WHERE `user_id`='$user_id' AND date(`date`) = '$date' AND `activity_type` LIKE '%".$app_lang['add_gif']."%'";
				}
				break;

			case 'quote':
				{
					$upload_limit=$settings_details['user_quotes_upload_limit'];

					$sql="SELECT * FROM willdev_users_rewards_activity WHERE `user_id`='$user_id' AND date(`date`) = '$date' AND `activity_type` LIKE '%".$app_lang['add_quote']."%'";
				}
				break;
			
			default:
				
				break;
		}

		$res=mysqli_query($mysqli, $sql);

		if($res->num_rows < $upload_limit){
			$set['ANDROID_REWARDS_APP']=array('success'=>'1','msg' => '');
		}
		else{
			$set['ANDROID_REWARDS_APP']=array('success'=>'0','msg' => $app_lang['exceed_upload_limit']);
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}	
 	else if($get_method['method_name']=="user_register_verify_email")
	{	
		$email=htmlentities(trim($get_method['email']));
		$qry = "SELECT * FROM willdev_users WHERE `email` = '$email' AND `user_type`='Normal'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);
		
		if($row['email']!="")
		{
			$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['email_exist'],'success'=>'0');
		}
		else
		{	
			$to = $email;
			$recipient_name='';
			// subject
			$subject = '[IMPORTANT] '.APP_NAME.' Email Verification Code';
				
			$message='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'/images/'.APP_LOGO.'" alt="header" width="120"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Thank you for using '.APP_NAME.',<br>
					                            Your OTP is: '.$get_method['otp_code'].'</p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
					                            '.APP_NAME.'.</p></td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';
 			
			send_email($to,$recipient_name,$subject,$message);
 
			  
			$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['otp_sent'],'success'=>'1');
		}
		 
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}		
	else if($get_method['method_name']=="user_register")	
	{
		
		$user_type=trim($get_method['type']); //Google, Normal, Facebook
		$device_id=trim($get_method['device_id']);

		$email=addslashes(trim($get_method['email']));
		$auth_id=addslashes(trim($get_method['auth_id']));

		$registration_reward=API_REGISTRATION_REWARD;

		$registration_on=strtotime(date('d-m-Y h:i A'));

		$user_code=createRandomCode();

		if($user_type=='Google' || $user_type=='google')
		{

			$sql="SELECT * FROM willdev_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Google'";
			$res=mysqli_query($mysqli,$sql);
			$num_rows = mysqli_num_rows($res);
 			$row = mysqli_fetch_assoc($res);
		
    		if($num_rows == 0)
    		{
				

				$is_duplicate='';

				$sql_device="SELECT * FROM willdev_users WHERE `device_id` = '".$device_id."'";
				$res_device=mysqli_query($mysqli,$sql_device);
				if(mysqli_num_rows($res_device) > 0){
					$is_duplicate='1';
				}else{
					$is_duplicate='0';
				}

				$dataUser = array(
					'user_type' => 'Google',  
					'device_id' => $device_id,
					'user_code'  =>$user_code,
					'name'  =>  addslashes(trim($get_method['name'])),
					'email'  =>  trim($get_method['email']),
					'phone'  =>  addslashes(trim($get_method['phone'])),
					'player_id'  =>  trim($get_method['player_id']),
					'is_duplicate'  =>  $is_duplicate,
					'registration_on' => strtotime(date('d-m-Y h:i A')),
					'auth_id'  =>  $auth_id,
					'status'  =>  '1',
				);  

				$register_user = Insert('willdev_users',$dataUser);
				  	
				$user_id=mysqli_insert_id($mysqli);

				if(REGISTRATION_REWARD_POINTS_STATUS=='true')
				{
					$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".API_REGISTRATION_REWARD."' WHERE id = '".$user_id."'");

					user_reward_activity('',$user_id,$app_lang['register_reward'],API_REGISTRATION_REWARD);
				}

				//Default Admin Follow
				$data_follow = array(
					'user_id' =>0,
					'follower_id'  => $user_id,
					'created_at'  => date('d-m-Y h:i:s A')
				);   

				$qry_follow = Insert('willdev_follows',$data_follow);             

				$user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_followers=total_followers+1 WHERE id = '0'");
				$user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_following=total_following+1 WHERE id = '".$user_id."'");

				$set['ANDROID_REWARDS_APP']=array('user_id' => strval($user_id),'name'=>$get_method['name'],'email'=>$get_method['email'],'success'=>'1','msg' =>'','auth_id' => $auth_id, 'referral_code' => true);


			}
			else{

				$is_duplicate='';
				$sql_device="SELECT * FROM willdev_users WHERE `device_id` = '".$device_id."' AND `id` <> '".$row['id']."'";
				$res_device=mysqli_query($mysqli,$sql_device);
				if(mysqli_num_rows($res_device) > 0){
					$is_duplicate='1';
				}else{
					$is_duplicate='0';
				}

				$data = array(
		            'is_duplicate'  =>  $is_duplicate,
		            'player_id'  =>  $get_method['player_id'],
		            'auth_id'  =>  $auth_id,
		        ); 
   
		        $update=Update('willdev_users', $data, "WHERE id = '".$row['id']."'");

				if($row['status']==2)
				{
					$set['ANDROID_REWARDS_APP']=array('msg'  => $app_lang['account_deactive'],'success'=>'0');
				}
				else if($row['status']==0)
				{
					$set['ANDROID_REWARDS_APP']=array('msg'  => $app_lang['account_blocked'],'success'=>'0');
				}	
				else
				{
					$set['ANDROID_REWARDS_APP']=array('user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'success'=>'1','msg' =>'','auth_id' => $auth_id, 'referral_code' => false);
				}
			}
		}
		else if($user_type=='Facebook' || $user_type=='facebook'){

			$sql="SELECT * FROM willdev_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Facebook'";
			$res=mysqli_query($mysqli,$sql);
			$num_rows = mysqli_num_rows($res);
 			$row = mysqli_fetch_assoc($res);
		
    		if($num_rows == 0)
    		{
				$user_code=createRandomCode();

				$is_duplicate='';

				$sql_device="SELECT * FROM willdev_users WHERE `device_id` = '".$device_id."'";
				$res_device=mysqli_query($mysqli,$sql_device);
				if(mysqli_num_rows($res_device) > 0){
					$is_duplicate='1';
				}else{
					$is_duplicate='0';
				}

				$dataUser = array(
					'user_type' => 'Facebook',  
					'device_id' => $device_id,
					'user_code'  =>$user_code,
					'name'  =>  addslashes(trim($get_method['name'])),
					'email'  =>  trim($get_method['email']),
					'phone'  =>  addslashes(trim($get_method['phone'])),
					'player_id'  =>  trim($get_method['player_id']),
					'is_duplicate'  =>  $is_duplicate,
					'registration_on' => strtotime(date('d-m-Y h:i A')),
					'auth_id'  =>  $auth_id,
					'status'  =>  '1',
				);  

				$register_user = Insert('willdev_users',$dataUser);
				  	
				$user_id=mysqli_insert_id($mysqli);

				if(REGISTRATION_REWARD_POINTS_STATUS=='true')
				{
					$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".API_REGISTRATION_REWARD."' WHERE id = '".$user_id."'");

					user_reward_activity('',$user_id,$app_lang['register_reward'],API_REGISTRATION_REWARD);
				}

				//Default Admin Follow
				$data_follow = array(
					'user_id' =>0,
					'follower_id'  => $user_id,
					'created_at'  => date('d-m-Y h:i:s A')               
				);   

				$qry_follow = Insert('willdev_follows',$data_follow);             

				$user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_followers=total_followers+1 WHERE id = '0'");
				$user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_following=total_following+1 WHERE id = '".$user_id."'");

				$set['ANDROID_REWARDS_APP']=array('user_id' => strval($user_id),'name'=>$get_method['name'],'email'=>$get_method['email'],'success'=>'1','msg' =>'','auth_id' => $auth_id, 'referral_code' => true);

			}else{

				$is_duplicate='';

				$sql_device="SELECT * FROM willdev_users WHERE `device_id` = '".$device_id."' AND `id` <> '".$row['id']."'";
				$res_device=mysqli_query($mysqli,$sql_device);
				
				if(mysqli_num_rows($res_device) > 0){
					$is_duplicate='1';
				}else{
					$is_duplicate='0';
				}

				$data = array(
		            'is_duplicate'  =>  $is_duplicate,
		            'player_id'  =>  $get_method['player_id'],
		            'auth_id'  =>  $auth_id,
		        );  
   
		        $update=Update('willdev_users', $data, "WHERE id = '".$row['id']."'");

				if($row['status']==2)
				{
					$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['account_deactive'],'success'=>'0');
				}
				else if($row['status']==0)
				{
					$set['ANDROID_REWARDS_APP']=array('msg'  => $app_lang['account_blocked'],'success'=>'0');
				}	
				else
				{
					$set['ANDROID_REWARDS_APP']=array('user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'success'=>'1','msg' =>'','auth_id' => $auth_id, 'referral_code' => false);
				}
			}

		}
	  	else if($user_type=='Normal' || $user_type=='normal')
		{
			$qry="SELECT * FROM willdev_users WHERE `email` = '$email' AND `user_type`='Normal'";
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);

			if($row['email']!="")
			{
				$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['email_exist'],'success'=>'0');
			}
			else
			{ 
				$user_code=createRandomCode();

				$is_duplicate='';

				$sql_device="SELECT * FROM willdev_users WHERE `device_id` = '".$device_id."'";
				$res_device=mysqli_query($mysqli,$sql_device);
				if(mysqli_num_rows($res_device) > 0){
					$is_duplicate='1';
				}else{
					$is_duplicate='0';
				}

				$dataUser = array(
					'user_type' => 'Normal',  
					'device_id' => $device_id,
					'user_code'  =>$user_code,
					'name'  =>  addslashes(trim($get_method['name'])),
					'email'  =>  trim($get_method['email']),
					'password'  =>  md5(trim($get_method['password'])),
					'phone'  =>  addslashes(trim($get_method['phone'])),
					'player_id'  =>  trim($get_method['player_id']),
					'is_duplicate'  =>  $is_duplicate,
					'registration_on' => strtotime(date('d-m-Y h:i A')),
					'status'  =>  '1',
				);  

				$register_user = Insert('willdev_users',$dataUser);
				  	
				$user_id=mysqli_insert_id($mysqli);

				if(REGISTRATION_REWARD_POINTS_STATUS=='true')
				{
					$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".API_REGISTRATION_REWARD."' WHERE id = '".$user_id."'");

					user_reward_activity('',$user_id,$app_lang['register_reward'],API_REGISTRATION_REWARD);
				}

				if(isset($get_method['user_refrence_code']))
				{
				 
					$user_qry="SELECT * FROM willdev_users WHERE user_code='".$get_method['user_refrence_code']."'";
					$user_result=mysqli_query($mysqli,$user_qry);
					$user_row=mysqli_fetch_assoc($user_result);

					if(APP_REFER_REWARD_POINTS_STATUS=='true')
					{ 
						if($user_row['id']!="")
						{		  
							$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".API_REFER_REWARD."' WHERE `id` = '".$user_row['id']."'");

							$refer_msg_text= str_replace('###', $get_method['name'], $app_lang['user_refer_msg']);
						
							user_reward_activity('',$user_row['id'],$refer_msg_text,API_REFER_REWARD);
						 }
					}
				}

				//Default Admin Follow
				$data_follow = array(
					'user_id' =>0,
					'follower_id'  => $user_id,
					'created_at'  => date('d-m-Y h:i:s A')               
				);   

				$qry_follow = Insert('willdev_follows',$data_follow);             

				$user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_followers=total_followers+1 WHERE id = '0'");
				$user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_following=total_following+1 WHERE id = '".$user_id."'");

				$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['register_success'],'success'=>'1');
			}

		}
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="user_login")	
	{

		if(!isset($get_method['user_id']))
		{
			$email = htmlentities(trim($get_method['email']));
			$password = trim($get_method['password']);

			$sql="SELECT * FROM willdev_users WHERE `email` = '$email' AND (`user_type`='Normal' OR `user_type`='normal') AND `id` <> 0";
			$res=mysqli_query($mysqli, $sql);

			if(mysqli_num_rows($res) > 0){
				$row = mysqli_fetch_assoc($res);

				if(strcmp($row['password'], md5($password))==0)
				{
					if($row['status']==0){
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['account_blocked'],'success'=>'0');
					}
					else if($row['status']==2)
					{
						$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['account_deactive'],'success'=>'0');
					}	
					else
					{
						$set['ANDROID_REWARDS_APP']=array('user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'msg' => $app_lang['login_success'],'auth_id'=>'','success'=>'1');

						$data = array(
							'player_id'  =>  $get_method['player_id']
						);  

						$updatePlayerID=Update('willdev_users', $data, "WHERE id = '".$row['id']."'");
					}

				}
				else{
					$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['invalid_password'],'success'=>'0');
				}

			}
			else{
				$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['email_not_found'],'success'=>'0');
			}
		}
		else{
			// check user login

			$user_id = htmlentities(trim($get_method['user_id']));

			$sql="SELECT * FROM willdev_users WHERE `id` = '$user_id'";
			$res=mysqli_query($mysqli, $sql);

			if(mysqli_num_rows($res) > 0)
			{
				$row = mysqli_fetch_assoc($res);

				if($row['status']==0){
					$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['account_blocked'],'success'=>'0');
				}
				else if($row['status']==2)
				{
					$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['account_deactive'],'success'=>'0');
				}	
				else
				{
					$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['login_success'],'success'=>'1');
				}
			}
			else{
				$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['email_not_found'],'success'=>'0');
			}
		}

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="user_search")	
	{
		$jsonObj= array();
		$search_keyword=htmlentities(trim($get_method['search_keyword']));
		$user_id=trim($get_method['user_id']);

		$page=(isset($get_method['page'])) ? $get_method['page'] : 1;
 		$page_limit=20;			
		$limit=($page-1) * $page_limit;

		$sql="SELECT * FROM willdev_users 
		      WHERE willdev_users.`status`='1' AND willdev_users.`id` <> $user_id AND (willdev_users.`name` LIKE '%$search_keyword%' OR willdev_users.`email` LIKE '%$search_keyword%') ORDER BY willdev_users.`name` DESC LIMIT $limit, $page_limit";
	 	
	 	$res=mysqli_query($mysqli, $sql) or die('Error in fetching followers list =>'.mysqli_error($mysqli));
 		
 		if($res->num_rows > 0){

			while($data = mysqli_fetch_assoc($res))
			{	 
				$row['user_id'] = $data['id'];			 
				$row['user_name'] = $data['name'];

				if($data['user_image']!='')
				{
					$row['user_image']=$file_path.'images/'.$data['user_image'];
				}	
				else
				{
					$row['user_image']='';
				} 

				if(get_user_info($data['id'],'is_verified')==1){
					$row['is_verified']="true";
				}
				else{
					$row['is_verified']="false";
				}
			 	 
				array_push($jsonObj,$row);
			}
 		}
 		$set['ANDROID_REWARDS_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="user_followers")	
	{
		$jsonObj= array();
		$user_id=trim($get_method['user_id']);

		$page=(isset($get_method['page'])) ? $get_method['page'] : 1;
 		$page_limit=20;			
		$limit=($page-1) * $page_limit;

		$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id' ORDER BY willdev_follows.`id` DESC LIMIT $limit, $page_limit";
	 	
	 	$res=mysqli_query($mysqli, $sql) or die('Error in fetching followers list =>'.mysqli_error($mysqli));
 		
 		if($res->num_rows > 0){

			while($data = mysqli_fetch_assoc($res))
			{	 
				$row['user_id'] = $data['follower_id'];			 
				$row['user_name'] = get_user_info($data['follower_id'],'name');
				
				if(get_user_info($data['follower_id'],'user_image')!='')
				{
					$row['user_image'] = $file_path.'images/'.get_user_info($data['follower_id'],'user_image');
				}	
				else
				{
					$row['user_image'] ='';
				} 

				if(get_user_info($data['follower_id'],'is_verified')==1){
					$row['is_verified']="true";
				}
				else{
					$row['is_verified']="false";
				}
			 	 
				array_push($jsonObj,$row);
			}
 		}
 		$set['ANDROID_REWARDS_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="user_following")	
	{
		$jsonObj= array();
		$user_id=trim($get_method['user_id']);

		$page=(isset($get_method['page'])) ? $get_method['page'] : 1;
 		$page_limit=20;			
		$limit=($page-1) * $page_limit;

		$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`user_id`=willdev_users.`id` AND willdev_follows.`follower_id`='$user_id' ORDER BY willdev_follows.`id` DESC LIMIT $limit, $page_limit";
	 	
	 	$res=mysqli_query($mysqli, $sql) or die('Error in fetching following list =>'.mysqli_error($mysqli));
 		
 		if($res->num_rows > 0){

			while($data = mysqli_fetch_assoc($res))
			{	 
				$row['user_id'] = $data['user_id'];			 
				$row['user_name'] = get_user_info($data['user_id'],'name');
				
				if(get_user_info($data['user_id'],'user_image')!='')
				{
					$row['user_image'] = $file_path.'images/'.get_user_info($data['user_id'],'user_image');
				}	
				else
				{
					$row['user_image'] ='';
				} 

				if(get_user_info($data['user_id'],'is_verified')==1){
					$row['is_verified']="true";
				}
				else{
					$row['is_verified']="false";
				}
			 	 
				array_push($jsonObj,$row);
			}
 		}
 		$set['ANDROID_REWARDS_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="user_profile")	
	{

		$user_id=$get_method['user_id'];
		
		$qry = "SELECT * FROM willdev_users WHERE id = '$user_id'"; 
		$result = mysqli_query($mysqli,$qry);		 
		$row = mysqli_fetch_assoc($result);
 		
 		if(mysqli_num_rows($result) > 0){

			if($row['user_image']!='')
			{
				$user_image=$file_path.'images/'.$row['user_image'];
			}	
			else
			{
				$user_image='';
			}
			
			$phone=$row['phone'] ? $row['phone']:'';

			$user_youtube=$row['user_youtube'] ? $row['user_youtube']: $settings_details['default_youtube_url'];
	    	$user_instagram=$row['user_instagram'] ? $row['user_instagram']: $settings_details['default_instagram_url'];


			if($row['is_verified']==1){
				$is_verified="true";
			}
			else{
				$is_verified="false";
			}

			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id' ORDER BY willdev_follows.`id` DESC";
	 	
	 		$res_follower=mysqli_query($mysqli, $sql);

	 		$total_followers=mysqli_num_rows($res_follower);

			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`user_id`=willdev_users.`id` AND willdev_follows.`follower_id`='$user_id' ORDER BY willdev_follows.`id` DESC";
	 	
	 		$res_following=mysqli_query($mysqli, $sql);

			
			$total_following=mysqli_num_rows($res_following);

			$user_total_status=user_total_status($row['id']);
			 
		    $set['ANDROID_REWARDS_APP']=array('user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'phone'=>$phone,'user_image'=>$user_image,'user_youtube'=>$user_youtube,'user_instagram'=>$user_instagram,'user_code'=>$row['user_code'],'total_point'=>thousandsNumberFormat(get_total_points($user_id)),'total_status'=>$user_total_status,'total_followers'=>$total_followers,'total_following'=>$total_following,'is_verified'=>$is_verified, 'success'=>'1');
 		}
 		else{
 			$set['ANDROID_REWARDS_APP']=array('success'=>'0');
 		}

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="get_user_data")	
	{
		$user_id=$get_method['user_id'];
		
		$qry = "SELECT * FROM willdev_users WHERE id = '$user_id'"; 
		$result = mysqli_query($mysqli,$qry);		 
		$row = mysqli_fetch_assoc($result);
 		
 		if(mysqli_num_rows($result) > 0){

			if($row['user_image']!='')
			{
				$user_image=$file_path.'images/'.$row['user_image'];
			}	
			else
			{
				$user_image='';
			}

			if($row['is_verified']==1){
				$is_verified="true";
			}
			else{
				$is_verified="false";
			}
			
			$phone=$row['phone'] ? $row['phone']:'';


			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id' ORDER BY willdev_follows.`id` DESC";
	 	
	 		$res_follower=mysqli_query($mysqli, $sql);

	 		$total_followers=mysqli_num_rows($res_follower);

			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`user_id`=willdev_users.`id` AND willdev_follows.`follower_id`='$user_id' ORDER BY willdev_follows.`id` DESC";
	 	
	 		$res_following=mysqli_query($mysqli, $sql);

			$total_following=mysqli_num_rows($res_following);

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

			$qry_users_pending="SELECT SUM(redeem_price) AS num FROM willdev_users_redeem
			      LEFT JOIN willdev_users ON willdev_users_redeem.`user_id`= willdev_users.`id`
			      WHERE willdev_users_redeem.`user_id`='$user_id' AND willdev_users_redeem.`status` = '0'";
			$total_pending = mysqli_fetch_array(mysqli_query($mysqli,$qry_users_pending));
			$total_pending = $total_pending['num']; 

			$total_pending=($total_pending==null) ? '0' : $total_pending;
			 
		    $set['ANDROID_REWARDS_APP']=array('success'=>'1','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'phone'=>$phone,'user_image'=>$user_image,'user_code'=>$row['user_code'],'total_point'=>get_total_points($user_id),'pending_point'=>$total_pending,'total_video'=>$total_video,'total_image'=>$total_image,'total_gif'=>$total_gif,'total_quote'=>$total_quote,'total_followers'=> strval($total_followers),'total_following'=>strval($total_following),'is_verified'=>$is_verified,'delete_note' => $settings_details['delete_note']);
 		}
 		else{
 			$set['ANDROID_REWARDS_APP']=array('success'=>'0');
 		}

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="delete_user_account")	
	{
		$id=$get_method['user_id'];
		$email=$get_method['email'];
		
		$qry = "SELECT * FROM willdev_users WHERE id = '$id'"; 
		$result = mysqli_query($mysqli,$qry);		 

		if($result->num_rows > 0){

			deleted_user_copy($id,$email);

			Delete('willdev_comments','user_id='.$id); 
			Delete('willdev_reports','user_id='.$id);
			Delete('willdev_users_redeem','user_id='.$id); 
			Delete('willdev_users_rewards_activity','user_id='.$id); 
			Delete('willdev_like','device_id='.$id); 

			Delete('willdev_favourite','user_id='.$id); 

			Delete('willdev_suspend_account','user_id='.$id.''); 
			
			$sql="SELECT user_id FROM willdev_follows WHERE follower_id='$id'";
			$res=mysqli_query($mysqli, $sql);

			while($row=mysqli_fetch_assoc($res)){

				$updateSql="UPDATE willdev_users SET total_followers= total_followers - 1  WHERE id = '".$row['user_id']."'";

				$update=mysqli_query($mysqli,$updateSql) or die(mysqli_error($mysqli));
			}

			mysqli_free_result($res);

			$sql="SELECT follower_id FROM willdev_follows WHERE `user_id`='$id'";
			$res_2=mysqli_query($mysqli, $sql);

			while($row_2=mysqli_fetch_assoc($res_2)){

				$updateSql="UPDATE willdev_users SET total_following= total_following - 1  WHERE id = '".$row_2['follower_id']."'";

				$update=mysqli_query($mysqli,$updateSql) or die(mysqli_error($mysqli));
			}


			Delete('willdev_follows','user_id='.$id);
			Delete('willdev_follows','follower_id='.$id);

			mysqli_free_result($res);

			$sql="SELECT * FROM willdev_video WHERE `user_id`='$id' AND `video_type`='local'";
			$res=mysqli_query($mysqli, $sql);
			while ($row = mysqli_fetch_assoc($res)) {

				if(file_exists('images/'.$row['video_thumbnail'])){
					unlink('images/'.$row['video_thumbnail']);
				}

				if(file_exists('uploads/'.basename($row['video_url']))){
					unlink('uploads/'.basename($row['video_url']));
				}
			}

			Delete('willdev_video','user_id='.$id); 
			mysqli_free_result($res);

			$sql="SELECT * FROM willdev_img_status WHERE `user_id`='$id'";
			$res=mysqli_query($mysqli, $sql);
			while ($row = mysqli_fetch_assoc($res)) {
				if(file_exists('images/'.$row['image_file'])){
					unlink('images/'.$row['image_file']);
				}
			}

			Delete('willdev_img_status','user_id='.$id); 

			Delete('willdev_quotes','user_id='.$id);


			$sql="SELECT * FROM willdev_verify_user WHERE `user_id`='$id'";
			$res=mysqli_query($mysqli, $sql);
			while ($row = mysqli_fetch_assoc($res)) {

				if(file_exists('images/documents/'.$row['document'])){
					unlink('images/documents/'.$row['document']);
				}
			}

			Delete('willdev_verify_user','user_id='.$id); 

			$row_user=mysqli_fetch_assoc($result);

			if(file_exists('images/'.$row_user['user_image'])){
				unlink('images/'.$row_user['user_image']);
			}

			Delete('willdev_users','id='.$id);

			$set['ANDROID_REWARDS_APP']=array('success'=>'1','msg' => $app_lang['account_deleted']);

		}
		else{
			$set['ANDROID_REWARDS_APP']=array('success'=>'0' ,'msg' => $app_lang['error_msg']);
		}

 		

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="user_profile_update")	
	{
		$qry = "SELECT * FROM willdev_users WHERE id = '".$get_method['user_id']."'"; 
		$result = mysqli_query($mysqli,$qry);		 
		$row = mysqli_fetch_assoc($result);

		$old_image='';

		if($_FILES['user_image']['name']!="")
        {	
	        $old_image="images/".$row['user_image'];

	        $ext = pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

			$user_image=date('dmYhis').'_'.rand(0,99999).".".$ext;

			$tpath1='images/'.$user_image;        
			if($ext!='png'){
				$pic1=compress_image($_FILES["user_image"]["tmp_name"], $tpath1, 80);
			}else{
				move_uploaded_file($_FILES['user_image']['tmp_name'], $tpath1);
			}

        }		
        else
        {
        	$user_image=$row['user_image'];
        }

		if($get_method['password']!="")
		{
			$user_edit= "UPDATE willdev_users SET name='".$get_method['name']."',email='".$get_method['email']."',password='".md5(trim($get_method['password']))."',phone='".$get_method['phone']."',user_image='".$user_image."',user_youtube='".$get_method['user_youtube']."',user_instagram='".$get_method['user_instagram']."' WHERE id = '".$get_method['user_id']."'";	 
		}
		else
		{
			$user_edit= "UPDATE willdev_users SET name='".$get_method['name']."',email='".$get_method['email']."',phone='".$get_method['phone']."',user_image='".$user_image."',user_youtube='".$get_method['user_youtube']."',user_instagram='".$get_method['user_instagram']."' WHERE id = '".$get_method['user_id']."'";	 
		}
   		
   		$user_res = mysqli_query($mysqli,$user_edit) or die(mysqli_error($mysqli));	

   		if($user_res){
   			if($old_image!=''){
   				unlink($old_image);
   			}
   		}

	  				 
		$set['ANDROID_REWARDS_APP']=array('msg'=>$app_lang['update_success'],'success'=>'1');

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="other_user_profile")		
	{

		$other_user_id=$get_method['other_user_id'];

		$user_id=$get_method['user_id'];
		
		$qry = "SELECT * FROM willdev_users WHERE `id` = '$other_user_id' AND `status`='1'"; 
		$result = mysqli_query($mysqli,$qry);		

		if($result->num_rows > 0)
		{
			$row = mysqli_fetch_assoc($result);

			if($row['user_image']!='')
			{
				$user_image=$file_path.'images/'.$row['user_image'];
			}	
			else
			{
				$user_image='';
			}
			
			if($user_id!=''){
				$qry1 = "SELECT willdev_follows.* FROM willdev_follows, willdev_users WHERE willdev_follows.`user_id`=willdev_users.`id` AND willdev_follows.`user_id` = '$other_user_id' AND `follower_id`= '$user_id'"; 

				$result1 = mysqli_query($mysqli,$qry1);
		 		
		    	if (mysqli_num_rows($result1) > 0)
				{ 
					$already_follow=true;
				}		 
				else
				{
					$already_follow=false;
				}
			}
			else{
				$already_follow=false;
			}

			$user_youtube=$row['user_youtube'] ? $row['user_youtube'] : $settings_details['default_youtube_url'];
			$user_instagram=$row['user_instagram'] ? $row['user_instagram'] : $settings_details['default_instagram_url'];

			if($row['is_verified']==1){
				$is_verified="true";
			}
			else{
				$is_verified="false";
			}

			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
			      WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$other_user_id' ORDER BY willdev_follows.`id` DESC";
		 	
	 		$res_follower=mysqli_query($mysqli, $sql);

	 		$total_followers=mysqli_num_rows($res_follower);

			$sql="SELECT willdev_follows.* FROM willdev_follows, willdev_users 
		      WHERE willdev_follows.`user_id`=willdev_users.`id` AND willdev_follows.`follower_id`='$other_user_id' ORDER BY willdev_follows.`id` DESC";
	 	
	 		$res_following=mysqli_query($mysqli, $sql);

			
			$total_following=mysqli_num_rows($res_following);

			$user_total_status=user_total_status($row['id']);
		  				 
		    $set['ANDROID_REWARDS_APP']=array('user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'user_image'=>$user_image,'user_code'=>$row['user_code'],'user_youtube'=>$user_youtube,'user_instagram'=>$user_instagram,'already_follow'=>$already_follow,'total_status'=>$user_total_status,'total_followers'=>$total_followers,'total_following'=>$total_following,'is_verified'=>$is_verified,'success'=>'1');
		}
		else{
 			$set['ANDROID_REWARDS_APP']=array('success'=>'0','msg' => $app_lang['error_msg']);
 		}

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}	
	else if($get_method['method_name']=="user_status")	
	{
		$user_id = $get_method['user_id'];
		 
		$qry = "SELECT * FROM willdev_users WHERE status='1' and id = '".$user_id."'"; 
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		
    	if($num_rows > 0)
		{			 
			$set['ANDROID_REWARDS_APP'][]=array('message' => 'Enable','success'=>'1');
			 
		}		 
		else
		{
				 
 			$set['ANDROID_REWARDS_APP'][]=array('message' => 'Disable','success'=>'0');
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}

	else if($get_method['method_name']=="profile_status")	
	{
		$user_id = $get_method['user_id'];

		$name=$email='';

		if($get_method['user_id']!='')
		{
			$user_qry="SELECT * FROM willdev_users WHERE `id` = '$user_id'";
			$user_result=mysqli_query($mysqli,$user_qry);
			$user_row=mysqli_fetch_assoc($user_result);

			$name = $user_row['name'];
			$email = $user_row['email'];
		}
		else
		{
			$name = '';
			$email = '';
		}
		 
		$qry = "SELECT * FROM willdev_verify_user WHERE `user_id` = '".$user_id."'"; 
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);
		
    	if($num_rows > 0)
		{ 
			$row = mysqli_fetch_assoc($result);
			
			if($row['status']==0){
				$set['ANDROID_REWARDS_APP']=array('status'=>'0','message' => 'Request is pending','success'=>'1','name'=>$name,'email'=>$email);
			}	
			else if($row['status']==2){
				$set['ANDROID_REWARDS_APP']=array('status'=>'2','message' => 'Request is rejected','success'=>'1','name'=>$name,'email'=>$email);
			}
			else{
				$set['ANDROID_REWARDS_APP']=array('status'=>'1','message' => 'Verified','success'=>'1','name'=>$name,'email'=>$email);
			}					 
			    
		}		 
		else
		{
			$set['ANDROID_REWARDS_APP']=array('status'=>'3','message' => 'Not verified !','success'=>'1','name'=>$name,'email'=>$email);
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}

	else if($get_method['method_name']=="verfication_details")	
	{
		$user_id = $get_method['user_id'];
		 
		$qry = "SELECT * FROM willdev_verify_user WHERE `user_id` = '".$user_id."'"; 
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);
		
		if($num_rows > 0)
		{ 
			$row = mysqli_fetch_assoc($result);
			
			$data['success']=1;

			$data['id']=$row['id'];
			$data['user_full_name']=$row['full_name'];
			$data['user_message']=$row['message'];
			$data['document_img']=$file_path.'images/documents/'.$row['document'];
			$data['request_date']=date('d-m-Y',$row['created_at']);

			if($row['verify_at']!=0){
				$data['response_date']=date('d-m-Y',$row['verify_at']);
			}else{
				$data['response_date']='';
			}

			if($row['status']==2){
				$data['admin_message']=$row['reject_reason'];
			}else{
				$data['admin_message']='';
			}

			$data['status']=$row['status'];

			$set['ANDROID_REWARDS_APP']=$data;

		}		 
		else
		{
			$set['ANDROID_REWARDS_APP']=array('message' => 'Not varified !','success'=>'0');
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}

	else if($get_method['method_name']=="forgot_pass")	
	{
		
		$email=htmlentities(trim($get_method['email']));
	 	 
		$qry = "SELECT * FROM willdev_users WHERE `email` = '$email' AND `user_type`='Normal'"; 
		$result = mysqli_query($mysqli,$qry);
		$row = mysqli_fetch_assoc($result);

		if($result->num_rows > 0)
		{

			$password=generateRandomPassword(7);

			$new_password=md5($password);
 
			$to = $row['email'];
			$recipient_name=$row['name'];
			// subject
			$subject = '[IMPORTANT] '.APP_NAME.' Forgot Password Information';
 			
			$message='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" style="width:100px;height:auto"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td>
					                        	<p style="color: #262626; font-size: 24px; margin-top:0px;"><strong>'.$app_lang['dear_lbl'].' '.$row['name'].'</strong></p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-top:5px;"><br>'.$app_lang['your_password_lbl'].': <span style="font-weight:400;">'.$password.'</span></p>
					                          <p style="color:#262626; font-size:17px; line-height:32px;font-weight:500;margin-bottom:30px;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>

					                        </td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';
 
			    send_email($to,$recipient_name,$subject,$message);
				$sql="UPDATE willdev_users SET `password`='$new_password' WHERE `id`='".$row['id']."'";
      			mysqli_query($mysqli,$sql);

      			$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['password_sent_mail'],'success'=>'1');

		}
		else
		{  	 	
			$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['email_not_found'],'success'=>'0');		
		}
	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	} 	
	else if($get_method['method_name']=="user_rewads_point")		
	{
		$jsonObj= array();
		$user_id=$get_method['user_id'];

		$query="SELECT * FROM willdev_users WHERE `id`='$user_id'";

		$sql = mysqli_query($mysqli,$query);

		while($data = mysqli_fetch_assoc($sql))
		{

			$row['id'] =$data['id'];
			$row['total_point'] =$data['total_point'];

			$sql="SELECT * FROM willdev_users_rewards_activity WHERE user_id='$user_id' AND `status`='1' ORDER BY `id` DESC";

			$res = mysqli_query($mysqli,$sql);
			$num_rows = mysqli_num_rows($res);

			if($num_rows > 0)
			{
				while($data_arr = mysqli_fetch_assoc($res))
				{

					$type='';
					$title='';
					$status_img='';

					$video = "video";
					$image = "image";
					$gif = "gif";
					$quote = "quote";
					$activity = strtolower($data_arr['activity_type']);

					if(strpos($activity, $video) !== false)
					{
					  $type='video';
					  $title='video_title';
					  $status_img='video_thumbnail';
					} 
					else if(strpos($activity, $image) !== false)
					{
					  $type='image';
					  $title='image_title';
					  $status_img='image_file';
					} 
					else if(strpos($activity, $gif) !== false)
					{
					  $type='gif';
					  $title='image_title';
					  $status_img='image_file';
					} 
					else if(strpos($activity, $quote) !== false)
					{
					  $type='quote';
					  $title='quote';
					}
					else{
					  $type='';
					}

					$row1['id'] =$data_arr['post_id'];


					$row1['title']=get_single_info($data_arr['post_id'],$title,$type);

					if($type!='quote'){
						$row1['status_thumbnail'] =$file_path.'images/'.get_single_info($data_arr['post_id'],$status_img,$type);	
					}
					else{
						$row1['status_thumbnail']='';
					}

					$row1['user_id'] =$data_arr['user_id'];
					$row1['activity_type'] =$data_arr['activity_type'];
					$row1['points'] =$data_arr['points'];
					$row1['date'] =date('d-m-Y', strtotime($data_arr['date']));
					$row1['time'] =date('h:i A', strtotime($data_arr['date']));

					$row['user_rewads_point'][]=$row1;
				}
			}
			else
			{
				$row['user_rewads_point']=array();
			}

			array_push($jsonObj,$row);
		}

		$set['ANDROID_REWARDS_APP']=$jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="user_redeem_points_history")	
	{	
		$jsonObj= array();

		$user_id=$get_method['user_id'];
		$redeem_id=$get_method['redeem_id'];

		$sql="SELECT * FROM willdev_users_rewards_activity WHERE user_id='$user_id' AND redeem_id='".$get_method['redeem_id']."' AND `status` = '0' ORDER BY id DESC";

		$res = mysqli_query($mysqli,$sql);

		while($data_arr = mysqli_fetch_assoc($res))
		{

			$type='';
			$title='';
			$status_img='';

			$video = "video";
			$image = "image";
			$gif = "gif";
			$quote = "quote";
			$activity = strtolower($data_arr['activity_type']);

			if(strpos($activity, $video) !== false)
			{
			  $type='video';
			  $title='video_title';
			  $status_img='video_thumbnail';
			} 
			else if(strpos($activity, $image) !== false)
			{
			  $type='image';
			  $title='image_title';
			  $status_img='image_file';
			} 
			else if(strpos($activity, $gif) !== false)
			{
			  $type='gif';
			  $title='image_title';
			  $status_img='image_file';
			} 
			else if(strpos($activity, $quote) !== false)
			{
			  $type='quote';
			  $title='quote';
			}
			else{
			  $type='';
			}

			$row1['id'] =$data_arr['post_id'];

			$row1['title']=get_single_info($data_arr['post_id'],$title,$type);

			if($type!='quote'){
				$row1['status_thumbnail'] =$file_path.'images/'.get_single_info($data_arr['post_id'],$status_img,$type);	
			}
			else{
				$row1['status_thumbnail']='';
			}

			$row1['user_id'] =$data_arr['user_id'];
			$row1['activity_type'] =$data_arr['activity_type'];
			$row1['points'] =$data_arr['points'];
			$row1['date'] =date('d-m-Y', strtotime($data_arr['date']));
			$row1['time'] =date('h:i A', strtotime($data_arr['date']));

			array_push($jsonObj,$row1);
		}			    

		$set['ANDROID_REWARDS_APP']=$jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

   	}
   	else if($get_method['method_name']=="user_redeem_request")	
	{	
		$user_id = $get_method['user_id'];
		$user_points = $get_method['user_points'];
		$payment_mode = $get_method['payment_mode'];
		$bank_details = $get_method['bank_details'];

		$redeem_price= ($get_method['user_points']*REDEEM_MONEY)/REDEEM_POINTS;

		$data = array(
			'user_id' =>$user_id ,
			'user_points'  => $user_points,
			'redeem_price'  => $redeem_price,
			'payment_mode'  => $payment_mode,
			'bank_details'  =>  $bank_details,
		);  

		$qry = Insert('willdev_users_redeem',$data);
		$redeem_id=mysqli_insert_id($mysqli); 

		$update_points=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='0' WHERE `id` = '$user_id'");

		$updated_activity=mysqli_query($mysqli,"UPDATE willdev_users_rewards_activity SET `redeem_id`='$redeem_id', `status`='0' WHERE `user_id` = '$user_id' AND `status` = '1'");


		$set['ANDROID_REWARDS_APP'][] = array('msg'=>$app_lang['redeem_request_success'],'success'=>1);


		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

   }
   else if($get_method['method_name']=="user_redeem_history")	
	{	
		$jsonObj= array();	
	
	    $query="SELECT * FROM willdev_users_redeem WHERE willdev_users_redeem.`user_id`='".$get_method['user_id']."' ORDER BY willdev_users_redeem.`id` DESC";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{
			$row['redeem_id'] = $data['id'];			 
			$row['user_points'] = $data['user_points'];
			$row['redeem_price'] = $data['redeem_price'].' '.$settings_details['redeem_currency'];
			$row['request_date'] = date('d-m-Y',strtotime($data['request_date']));
			$row['status'] = $data['status'];			 
		 	  
			array_push($jsonObj,$row);
		
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		 
   	else if($get_method['method_name']=="user_status_list")		
   	{
		$post_order_by='DESC';
		$user_id=$get_method['user_id'];

		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

		if($get_method['filter_value']!="")
 		{
 			$filter_value=trim($get_method['filter_value']);

			// for video status
			if($get_method['login_user']=='true')
			{
				$query="SELECT willdev_video.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`user_id`='".$user_id."' AND willdev_video.`video_layout`='$filter_value' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_video.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`user_id`='".$user_id."' AND willdev_video.`status`='1' AND willdev_video.`video_layout`='$filter_value' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for image status
			if($get_method['login_user']=='true')
			{
			    $query="SELECT willdev_img_status.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`user_id`='".$user_id."' AND willdev_img_status.`image_layout`='$filter_value' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_img_status.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`user_id`='".$user_id."' AND willdev_img_status.`status`='1' AND willdev_img_status.`image_layout`='$filter_value' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));


			// for quotes status

			if($get_method['login_user']=='true')
			{
			    $query="SELECT willdev_quotes.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_quotes
				LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
				WHERE willdev_quotes.`user_id`='".$user_id."' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_quotes.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_quotes
				LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
				WHERE willdev_quotes.`user_id`='".$user_id."' AND willdev_quotes.`status`='1' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

 		}
 		else
 		{
			// for video status
			if($get_method['login_user']=='true')
			{
			    $query="SELECT willdev_video.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_video
				LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
				WHERE willdev_video.`user_id`='".$user_id."' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_video.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_video
					LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
					WHERE willdev_video.`user_id`='".$user_id."' AND willdev_video.`status`='1' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for image status
			if($get_method['login_user']=='true')
			{
			    $query="SELECT willdev_img_status.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`user_id`='".$user_id."' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_img_status.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_img_status
				LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
				WHERE willdev_img_status.`user_id`='".$user_id."' AND willdev_img_status.`status`='1' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

			// for quotes status
			if($get_method['login_user']=='true')
			{
			    $query="SELECT willdev_quotes.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_quotes
				LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
				WHERE willdev_quotes.`user_id`='".$user_id."' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}
			else
			{
				$query="SELECT willdev_quotes.*, willdev_category.`cid`, willdev_category.`category_name` FROM willdev_quotes
				LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
				WHERE willdev_quotes.`user_id`='".$user_id."' AND willdev_quotes.`status`='1' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";
			}

			$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
 		}	

		while($data = mysqli_fetch_assoc($sql_video))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = 'video';
			$row['status_title'] = $data['video_title'];
			$row['status_layout'] = $data['video_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

			if($data['video_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
			}
			else if($data['video_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['totel_viewer'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='video'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';

			$row['is_reviewed'] = ($data['status']!=0) ? true : false;    		
 
			array_push($jsonObj,$row);
		}

		while($data = mysqli_fetch_assoc($sql_image))
		{	
			$row['id'] = $data['id'];
			$row['status_type'] = $data['status_type'];
			$row['status_title'] = $data['image_title'];
			$row['status_layout'] = $data['image_layout'];
			
			$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

			if($data['image_layout']=='Landscape'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
			}
			else if($data['image_layout']=='Portrait'){
				$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
			}

			$row['total_likes'] = $data['total_likes'];
  			$row['total_viewer'] = $data['total_views'];  

			$row['category_name'] = $data['category_name'];

			$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
    		
    		$num_rows1 = mysqli_num_rows($query1);
 		
            if ($num_rows1 > 0)
		    {
    			$row['already_like']=true;
    		}
    		else
    		{
    			$row['already_like']=false;
    		}

    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

    		$row['quote_bg'] = '';
    		$row['quote_font'] = '';

    		$row['is_reviewed'] = ($data['status']!=0) ? true : false;
 
			array_push($jsonObj,$row);
		}

		if($get_method['filter_value']=="" OR $get_method['filter_value']=='Landscape')
 		{
			while($data = mysqli_fetch_assoc($sql_quote))
			{	
				$row['id'] = $data['id'];
				$row['status_type'] = 'quote';
				$row['status_title'] = stripslashes($data['quote']);
				$row['status_layout'] = 'Landscape';
				
				$row['status_thumbnail_b'] = '';

				$row['status_thumbnail_s'] = '';

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

	    		$row['quote_bg'] = '#'.$data['quote_bg'];
	    		$row['quote_font'] = $data['quote_font'];

	    		$row['is_reviewed'] = ($data['status']!=0) ? true : false;
	 
				array_push($jsonObj,$row);
			}
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="status_favourite")
	{
		$user_id =$get_method['user_id'];
		$post_id =$get_method['post_id'];
		$type =$get_method['type'];

		$sql="SELECT * FROM willdev_favourite WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `type`='$type'";
		$res=mysqli_query($mysqli, $sql);

		if($res->num_rows == 0){
			// add to favourite list

			$data = array( 
				'post_id'  =>  $post_id,
				'user_id'  =>  $user_id,
				'type'  =>  $type,
				'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
			);      

			$qry = Insert('willdev_favourite',$data);

			$set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['add_favourite'],'success'=>1,'is_favourite'=>true);

		}
		else{
			// remove to favourite list

			$deleteSql="DELETE FROM willdev_favourite WHERE `user_id`='$user_id' AND `post_id`='$post_id' AND `type`='$type'";

			if(mysqli_query($mysqli, $deleteSql)){
				$set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['remove_favourite'],'success'=>1,'is_favourite'=>false);
			}
			else{

				$set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['error_msg'],'success'=>0,'is_favourite'=>false);
			}
		}
    
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if($get_method['method_name']=="get_favourite_status")		
   	{
		$post_order_by=API_CAT_POST_ORDER_BY;
		$user_id=$get_method['user_id'];

		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

		$sql="SELECT * FROM willdev_favourite WHERE `user_id`='$user_id'";
		$res=mysqli_query($mysqli, $sql);

		while($row_favourite=mysqli_fetch_assoc($res)) {
			
			$type=trim($row_favourite['type']);
			$post_id=trim($row_favourite['post_id']);

			switch ($type) {
				case 'video':
					{
						// for video status

						if($get_method['filter_value']!="")
 						{
 							$query="SELECT willdev_video.*, willdev_category.`category_name` FROM willdev_video
								LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
								WHERE willdev_video.`id`='".$post_id."' AND willdev_video.`video_layout`='".$get_method['filter_value']."' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 						}
 						else{
 							$query="SELECT willdev_video.*, willdev_category.`category_name` FROM willdev_video
								LEFT JOIN willdev_category ON willdev_video.`cat_id`=willdev_category.`cid` 
								WHERE willdev_video.`id`='".$post_id."' ORDER BY willdev_video.`id` ".$post_order_by." LIMIT $limit, $page_limit";
						}

						$sql_video=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
					}
					break;

				case 'image':
					{
						// for image status
						
						if($get_method['filter_value']!="")
 						{

							$query="SELECT willdev_img_status.*, willdev_category.`category_name` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
								WHERE willdev_img_status.`id`='".$post_id."' AND  willdev_img_status.`status_type`='".$type."' AND willdev_img_status.`image_layout`='".$get_method['filter_value']."' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 						}
 						else{
 							$query="SELECT willdev_img_status.*, willdev_category.`category_name` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
								WHERE willdev_img_status.`id`='".$post_id."' AND  willdev_img_status.`status_type`='".$type."' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
						}

						$sql_image=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));


					}
					break;

				case 'gif':
					{
						// for gif status
						if($get_method['filter_value']!="")
 						{

							$query="SELECT willdev_img_status.*, willdev_category.`category_name` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
								WHERE willdev_img_status.`id`='".$post_id."' AND  willdev_img_status.`status_type`='".$type."' AND willdev_img_status.`image_layout`='".$get_method['filter_value']."' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
 						}
 						else{
 							$query="SELECT willdev_img_status.*, willdev_category.`category_name` FROM willdev_img_status
								LEFT JOIN willdev_category ON willdev_img_status.`cat_id`=willdev_category.`cid` 
								WHERE willdev_img_status.`id`='".$post_id."' AND  willdev_img_status.`status_type`='".$type."' ORDER BY willdev_img_status.`id` ".$post_order_by." LIMIT $limit, $page_limit";
						}

						$sql_gif=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

					}
					break;

				case 'quote':
					{
						// for quotes status
						$query="SELECT willdev_quotes.*, willdev_category.`category_name` FROM willdev_quotes
							LEFT JOIN willdev_category ON willdev_quotes.`cat_id`=willdev_category.`cid` 
							WHERE willdev_quotes.`id`='".$post_id."' AND willdev_category.`status`='1' ORDER BY willdev_quotes.`id` ".$post_order_by." LIMIT $limit, $page_limit";

						$sql_quote=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

						
					}
					break;
				
				default:
					# code...
					break;
			}

			while($data = mysqli_fetch_assoc($sql_video))
			{	
				if($data['user_id']!=$user_id AND $data['status']==0){
					continue;
				}

				$row['id'] = $data['id'];
				$row['status_type'] = 'video';
				$row['status_title'] = $data['video_title'];
				$row['status_layout'] = $data['video_layout'];
				
				$row['status_thumbnail_b'] = $file_path.'images/'.$data['video_thumbnail'];

				if($data['video_layout']=='Landscape'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'500x280');
				}
				else if($data['video_layout']=='Portrait'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['video_thumbnail'],'280x500');
				}

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['totel_viewer'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='video'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'video');

	    		$row['quote_bg'] = '';
	    		$row['quote_font'] = '';
	 
				array_push($jsonObj,$row);
			}

			while($data = mysqli_fetch_assoc($sql_image))
			{	

				if($data['user_id']!=$user_id AND $data['status']==0){
					continue;
				}

				$row['id'] = $data['id'];
				$row['status_type'] = $data['status_type'];
				$row['status_title'] = $data['image_title'];
				$row['status_layout'] = $data['image_layout'];
				
				$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

				if($data['image_layout']=='Landscape'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
				}
				else if($data['image_layout']=='Portrait'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
				}

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

	    		$row['quote_bg'] = '';
	    		$row['quote_font'] = '';
	 
				array_push($jsonObj,$row);
			}

			while($data = mysqli_fetch_assoc($sql_gif))
			{	
				if($data['user_id']!=$user_id AND $data['status']==0){
					continue;
				}

				$row['id'] = $data['id'];
				$row['status_type'] = $data['status_type'];
				$row['status_title'] = $data['image_title'];
				$row['status_layout'] = $data['image_layout'];
				
				$row['status_thumbnail_b'] = $file_path.'images/'.$data['image_file'];

				if($data['image_layout']=='Landscape'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'500x280');
				}
				else if($data['image_layout']=='Portrait'){
					$row['status_thumbnail_s'] = get_thumb('images/'.$data['image_file'],'280x500');
				}

				$row['total_likes'] = $data['total_likes'];
	  			$row['total_viewer'] = $data['total_views'];  

				$row['category_name'] = $data['category_name'];

				$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='".$data['status_type']."'"); 
	    		
	    		$num_rows1 = mysqli_num_rows($query1);
	 		
	            if ($num_rows1 > 0)
			    {
	    			$row['already_like']=true;
	    		}
	    		else
	    		{
	    			$row['already_like']=false;
	    		}

	    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],$data['status_type']);

	    		$row['quote_bg'] = '';
	    		$row['quote_font'] = '';
	 
				array_push($jsonObj,$row);
			}

			if($get_method['filter_value']=='Landscape')
			{
				while($data = mysqli_fetch_assoc($sql_quote))
				{	
					if($data['user_id']!=$user_id AND $data['status']==0){
						continue;
					}

					$row['id'] = $data['id'];
					$row['status_type'] = 'quote';
					$row['status_title'] = stripslashes($data['quote']);
					$row['status_layout'] = 'Landscape';
					
					$row['status_thumbnail_b'] = '';

					$row['status_thumbnail_s'] = '';

					$row['total_likes'] = $data['total_likes'];
		  			$row['total_viewer'] = $data['total_views'];  

					$row['category_name'] = $data['category_name'];

					$query1 = mysqli_query($mysqli,"SELECT * FROM willdev_like WHERE `post_id`='".$data['id']."' && `device_id`='".$get_method['user_id']."' && `like_type`='quote'"); 
		    		
		    		$num_rows1 = mysqli_num_rows($query1);
		 		
		            if ($num_rows1 > 0)
				    {
		    			$row['already_like']=true;
		    		}
		    		else
		    		{
		    			$row['already_like']=false;
		    		}

		    		$row['is_favourite']=get_favourite_info($data['id'],$get_method['user_id'],'quote');

		    		$row['quote_bg'] = '#'.$data['quote_bg'];
		    		$row['quote_font'] = $data['quote_font'];
		 
					array_push($jsonObj,$row);
				}

			}
		}
		
		$set['ANDROID_REWARDS_APP'] = $jsonObj;	
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}

	else if($get_method['method_name']=="user_video_upload")
	{

		$uploadStatus=true;

		define("AUTO_APPROVE",$settings_details['auto_approve']);

		$path = "uploads/"; //set your folder path
        $video_local=rand(0,99999)."_".str_replace(" ", "-", $_FILES['video_local']['name']);

        $tmp = $_FILES['video_local']['tmp_name'];
        
        if (move_uploaded_file($tmp, $path.$video_local)) 
        {
            $video_url=$video_local;
        }
        else{
        	$uploadStatus=false;
        }

        if($uploadStatus){
        	$ext = pathinfo($_FILES['video_thumbnail']['name'], PATHINFO_EXTENSION);

			$video_thumbnail=rand(0,99999)."_video_thumb.".$ext;

			//Main Image
			$tpath1='images/'.$video_thumbnail;   

			if($ext!='png')  {
				$pic1=compress_image($_FILES["video_thumbnail"]["tmp_name"], $tpath1, 80);
			}
			else{
				$tmp = $_FILES['video_thumbnail']['tmp_name'];
				move_uploaded_file($tmp, $tpath1);
			}
	     
			$video_id='';

			$user_id =$get_method['user_id'];  

			$sql_user="SELECT * FROM willdev_users WHERE id = '$user_id'";
			$res_user=mysqli_query($mysqli, $sql_user);

			$row_user=mysqli_fetch_assoc($res_user);

			if(AUTO_APPROVE=='on'){
				if($row_user['is_verified']==1){
					$status=1;
				}else{
					$status=0;
				}
			}else{
				$status=0;
			}

			$data = array( 
				'user_id'  =>  $user_id,
				'cat_id'  =>  $get_method['cat_id'],
				'lang_ids'  =>  $get_method['lang_ids'],
				'video_type'  =>  'local',
				'video_title'  =>  htmlentities(trim($get_method['video_title'])),
				'video_url'  =>  $video_url,
				'video_id'  =>  $video_id,
				'video_tags'  =>  htmlentities(trim($get_method['video_tags'])),
				'video_layout'  =>  $get_method['video_layout'],
				'video_thumbnail'  =>  $video_thumbnail,
				'status'  => $status
			);      

			$qry = Insert('willdev_video',$data);

			$last_id = mysqli_insert_id($mysqli);

	        if($status==1){

	        	if(VIDEO_ADD_POINTS_STATUS=='true')
				{

					$qry="SELECT * FROM willdev_video WHERE id='".$last_id."'";
					$result=mysqli_query($mysqli,$qry);
					$row=mysqli_fetch_assoc($result); 

					$user_id =$row['user_id'];

					$sql_activity = "SELECT * FROM willdev_users_rewards_activity WHERE `post_id` = '$last_id' AND `user_id` = '$user_id' AND `activity_type`='".$app_lang['add_video']."'";

					$res_activity = mysqli_query($mysqli,$sql_activity);

					$add_point=$settings_details['video_add']; 

					if ($res_activity->num_rows == 0)
					{

						$qry2 = "SELECT * FROM willdev_users WHERE id = '".$user_id."'";
						$result2 = mysqli_query($mysqli,$qry2);
						$row2=mysqli_fetch_assoc($result2); 

						$user_total_point=$row2['total_point']+$add_point;

						$user_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point='".$user_total_point."'  WHERE id = '".$user_id."'");

						user_reward_activity($last_id,$user_id,$app_lang['add_video'],$add_point);

					}

				}

	            
	            $img_path=$file_path.'images/'.$video_thumbnail;

	            // send notification to user's followers

	            $user_name=ucwords($row_user['name']);

	            $content = array("en" => str_replace('###', $user_name, $app_lang['add_video_notify_msg']));

	            $sql_follower="SELECT * FROM willdev_follows, willdev_users WHERE willdev_follows.`follower_id`=willdev_users.`id` AND willdev_follows.`user_id`='$user_id'";

	            $res_follower=mysqli_query($mysqli, $sql_follower);

	            $followers=array();

	            while ($row_follower=mysqli_fetch_assoc($res_follower)) {
	              $followers[]=$row_follower['player_id'];
	            }

	            $fields = array(
					'app_id' => ONESIGNAL_APP_ID,
					'include_player_ids' => $followers, 
					'data' => array("foo" => "bar","type" => "single_status","status_type" => "video","id" => $last_id,"external_link"=>false),
					'headings'=> array("en" => APP_NAME),
					'contents' => $content,
					'big_picture' =>$img_path
				);

	            $fields = json_encode($fields);

	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.ONESIGNAL_REST_KEY));
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	            curl_setopt($ch, CURLOPT_HEADER, FALSE);
	            curl_setopt($ch, CURLOPT_POST, TRUE);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	            $notify_res = curl_exec($ch);       
	            
	            curl_close($ch);

	        }

	        $set['ANDROID_REWARDS_APP'][] = array('msg'=>$app_lang['upload_video_success'],'success'=>1);
        }
        else{
        	$set['ANDROID_REWARDS_APP'][] = array('msg'=>'Video upload issue !!','success'=>1);
        }
        
    
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
   	else if($get_method['method_name']=="user_status_delete")
   	{
		$user_id=$get_method['user_id'];
		$post_id=$get_method['post_id'];
		$type=$get_method['type'];

		switch ($type) {
			case 'video':

				$sql="SELECT * FROM willdev_video WHERE id IN ($post_id)";
				$res=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

				while ($row=mysqli_fetch_assoc($res)) {

					if($row['video_thumbnail']!="")
					{
						unlink('images/thumbs/'.$row['video_thumbnail']);
						unlink('images/'.$row['video_thumbnail']);
						unlink('uploads/'.basename($row['video_url']));
					}
				}

				$deleteSql="DELETE FROM willdev_video WHERE `id` IN ($post_id)";

				mysqli_query($mysqli, $deleteSql);

				$delete_comment="DELETE FROM willdev_comments WHERE `post_id` IN ($post_id) AND `type`='video'";

				mysqli_query($mysqli, $delete_comment);

				$delete_report="DELETE FROM willdev_reports WHERE `post_id` IN ($post_id) AND `report_type`='video'";

				mysqli_query($mysqli, $delete_report);
				
				break;

			case 'image':

				$sql="SELECT * FROM willdev_img_status WHERE `id` IN ($post_id) AND `status_type`='image'";
				$res=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

				while ($row=mysqli_fetch_assoc($res)) {

					if($row['image_file']!="")
					{
						unlink('images/'.$row['image_file']);
					}
				}

				$deleteSql="DELETE FROM willdev_img_status WHERE `id` IN ($post_id) AND `status_type`='image'";

				mysqli_query($mysqli, $deleteSql);

				$delete_comment="DELETE FROM willdev_comments WHERE `post_id` IN ($post_id) AND `type`='image'";

				mysqli_query($mysqli, $delete_comment);

				$delete_report="DELETE FROM willdev_reports WHERE `post_id` IN ($post_id) AND `report_type`='image'";

				mysqli_query($mysqli, $delete_report);
				
				break;

			case 'gif':
				
				$sql="SELECT * FROM willdev_img_status WHERE `id` IN ($post_id) AND `status_type`='gif'";
				$res=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));

				while ($row=mysqli_fetch_assoc($res)) {

					if($row['image_file']!="")
					{
						unlink('images/'.$row['image_file']);
					}
				}

				$deleteSql="DELETE FROM willdev_img_status WHERE `id` IN ($post_id) AND `status_type`='gif'";

				mysqli_query($mysqli, $deleteSql);

				$delete_comment="DELETE FROM willdev_comments WHERE `post_id` IN ($post_id) AND `type`='gif'";

				mysqli_query($mysqli, $delete_comment);

				$delete_report="DELETE FROM willdev_reports WHERE `post_id` IN ($post_id) AND `report_type`='gif'";

				mysqli_query($mysqli, $delete_report);
				
				break;

			case 'quote':

				$deleteSql="DELETE FROM willdev_quotes WHERE `id` IN ($post_id)";

				mysqli_query($mysqli, $deleteSql);

				$delete_comment="DELETE FROM willdev_comments WHERE `post_id` IN ($post_id) AND `type`='quote'";

				mysqli_query($mysqli, $delete_comment);

				$delete_report="DELETE FROM willdev_reports WHERE `post_id` IN ($post_id) AND `report_type`='quote'";

				mysqli_query($mysqli, $delete_report);
				
				break;
			
			default:
				
				break;
		}

	    $set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['status_delete'],'success'=>1);
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}		
	else if($get_method['method_name']=="add_status_comment")	
	{
		$comment_text = addslashes(trim($get_method['comment_text']));

		$data = array(
	 	    'post_id'  => $get_method['post_id'],
	 	    'user_id'  => $get_method['user_id'],
			'comment_text'  =>  $comment_text,
			'type'  => $get_method['type'],
			'comment_on'  =>  strtotime(date('d-m-Y h:i:s A'))
		);	

		$qry = Insert('willdev_comments',$data);
		$last_id = mysqli_insert_id($mysqli);

		if($last_id > 0)
		{
			$set['success']=1;
			$set['msg']=$app_lang['comment_success'];

			$sql="SELECT * FROM willdev_comments where id='".$last_id."'";
			$res=mysqli_query($mysqli, $sql);

			$row_comments=mysqli_fetch_assoc($res);

			$set['total_comment'] = strval(CountRow("willdev_comments","post_id='".$get_method['post_id']."' AND type='".$get_method['type']."'"));			 

			$set['comment_id'] = $row_comments['id'];			 
			$set['user_id'] = $row_comments['user_id'];			 
			$set['user_name'] = get_user_info($row_comments['user_id'],'name') ? get_user_info($row_comments['user_id'],'name'): $row_comments['user_name'];			 

			if(get_user_info($row_comments['user_id'],'user_image')!='')
			{
				$set['user_image'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_image');
			}	
			else
			{
				$set['user_image'] ='';
			}
			$set['post_id'] = $row_comments['post_id'];
			$set['status_type'] = $row_comments['type'];
			$set['comment_text'] = $row_comments['comment_text'];
			$set['comment_date'] = calculate_time_span($row_comments['comment_on']);
		}
		else{
			$set['success']=0;
			$set['msg']=$app_lang['comment_fail'];
		}

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	
	}
	else if($get_method['method_name']=="user_follow")	
	{
		$user_id = $get_method['user_id'];
	    $follower_id = $get_method['follower_id'];
	      
	    $qry1 = "SELECT * FROM willdev_follows WHERE user_id = '".$get_method['user_id']."' AND follower_id= '".$get_method['follower_id']."'"; 
	    $result1 = mysqli_query($mysqli,$qry1);
	    $num_rows1 = mysqli_num_rows($result1);

	    $row1=mysqli_fetch_assoc($result1);
	    
	    if($num_rows1 > 0)
	    {

	        Delete('willdev_follows','id='.$row1['id'].'');

	        $user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_followers=total_followers-1 WHERE id = '".$user_id."'");

	        $user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_following=total_following-1 WHERE id = '".$follower_id."'");

	           
	         $set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['unfollow'],'success'=>1,'activity_status'=>'0');

	    }
	    else
	    {
	          $data = array(
	               'user_id' =>$user_id ,
	               'follower_id'  => $follower_id,
	               'created_at'  => date('d-m-Y h:i:s A')               
	               );  
	 
	          $qry = Insert('willdev_follows',$data);
	             
	          $user_followers_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_followers=total_followers+1 WHERE id = '".$user_id."'");

	          $user_following_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_following=total_following+1 WHERE id = '".$follower_id."'");

	           
	          $set['ANDROID_REWARDS_APP'] = array('msg'=>$app_lang['follow'],'success'=>1,'activity_status'=>'1');
	    }

	    	
	    
	    header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	    die();
	}	
	else if($get_method['method_name']=="user_contact_us")	
	{	

	    $contact_name = htmlentities(trim($get_method['contact_name']));
	    $contact_email = htmlentities(trim($get_method['contact_email']));
	    $contact_subject = trim($get_method['contact_subject']);
	    $contact_msg = htmlentities(trim($get_method['contact_msg']));

	    $data = array(
	 	    'contact_name'  => $contact_name,
	 	    'contact_email'  => $contact_email,
			'contact_subject'  =>  $contact_subject,
			'contact_msg'  =>  $contact_msg,
			'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
		);	

		$qry = Insert('willdev_contact_list',$data);

		$to = $settings_details['app_email'];
		$recipient_name=APP_NAME;
		// subject
		$subject = '[IMPORTANT] '.APP_NAME.' Contact';
			
		$message='<div style="background-color: #f9f9f9;" align="center"><br />
				  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
				    <tbody>
				      <tr>
				        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120" /></td>
				      </tr>
				      <tr>
				        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
				          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
				            <tbody>
				              <tr>
				                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
				                    <tbody>
				                      <tr>
				                        <td>
				                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Hello Admin,<br>
				                            Name: '.$contact_name.'</p>
				                            <p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
				                            Email: '.$contact_email.'</p>
				                            <p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
				                            Subject: '.get_subject_info($contact_subject,'title').'</p>
				                             <p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
				                            Message: '.$contact_msg.'</p>
				                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
				                            '.APP_NAME.'.</p></td>
				                      </tr>
				                    </tbody>
				                  </table></td>
				              </tr>
				               
				            </tbody>
				          </table></td>
				      </tr>
				      <tr>
				        <td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © '.APP_NAME.'.</td>
				      </tr>
				    </tbody>
				  </table>
				</div>';

		send_email($to,$recipient_name,$subject,$message);
			  
		$set['ANDROID_REWARDS_APP']=array('msg' => $app_lang['msg_sent'],'success'=>'1');
		  
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}	
	else if($get_method['method_name']=="status_report")	
  	{		
  		$type=$get_method['report_type'];
  		$report=htmlentities(trim($get_method['report_text']));

  		$post_id=$get_method['post_id'];
  		$user_id=$get_method['user_id'];

  		$report_type=$get_method['type'];

  		if($report!=''){

  			$data = array(
	            'user_id'  => $user_id,
	            'post_id'  => $post_id,
	            'email'  =>  '-',
	            'type'  => $type,
	            'report'  => $report,
	            'report_type'  => $report_type,
	            'report_on'  =>  strtotime(date('d-m-Y h:i:s A'))
	        );		

          	$insert = Insert('willdev_reports',$data);

          	$set['ANDROID_REWARDS_APP'] = array('msg' => $app_lang['report_success'],'success'=>'1');

  		}
  		else{
  			$set['ANDROID_REWARDS_APP'] = array('msg' => 'Please add report text','success'=>'0');
  		}

  		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
    }
	else if($get_method['method_name']=="points_details")		
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);
		
		if(REGISTRATION_REWARD_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['register_point'],'point'=>$data['registration_reward']);
		}
		
		if(APP_REFER_REWARD_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['refer_point'],'point'=>$data['app_refer_reward']);
		}

		if(VIDEO_VIEW_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['video_view_point'],'point'=>$data['video_views']);
		}

		if(VIDEO_ADD_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['video_add_point'],'point'=>$data['video_add']);
		}

		if(LIKE_VIDEO_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['video_like_point'],'point'=>$data['like_video_points']);
		}

		if(DOWNLOAD_POINTS_STATUS=='true'){
			$row[] = array('title' => $app_lang['video_download_point'],'point'=>$data['download_video_points']);
		}

		if(OTHER_USER_VIDEO_STATUS=='true'){
			$row[] = array('title' => $app_lang['video_view_by_other'],'point'=>$data['other_user_video_point']);
		}


		// for image status
		if($data['image_views_status']=='true'){
			$row[] = array('title' => $app_lang['image_view_point'],'point'=>$data['image_views']);
		}

		if($data['image_add_status']=='true'){
			$row[] = array('title' => $app_lang['image_add_point'],'point'=>$data['image_add']);
		}

		if($data['like_image_points_status']=='true'){
			$row[] = array('title' => $app_lang['image_like_point'],'point'=>$data['like_image_points']);
		}

		if($data['download_image_points_status']=='true'){
			$row[] = array('title' => $app_lang['image_download_point'],'point'=>$data['download_image_points']);
		}

		if($data['other_user_image_status']=='true'){
			$row[] = array('title' => $app_lang['image_view_by_other'],'point'=>$data['other_user_image_point']);
		}

		// for gif status
		if($data['gif_views_status']=='true'){
			$row[] = array('title' => $app_lang['gif_view_point'],'point'=>$data['gif_views']);
		}

		if($data['gif_add_status']=='true'){
			$row[] = array('title' => $app_lang['gif_add_point'],'point'=>$data['gif_add']);
		}

		if($data['like_gif_points_status']=='true'){
			$row[] = array('title' => $app_lang['gif_like_point'],'point'=>$data['like_gif_points']);
		}

		if($data['download_gif_points_status']=='true'){
			$row[] = array('title' => $app_lang['gif_download_point'],'point'=>$data['download_gif_points']);
		}

		if($data['other_user_gif_status']=='true'){
			$row[] = array('title' => $app_lang['gif_view_by_other'],'point'=>$data['other_user_gif_point']);
		}

		// for gif status
		if($data['quotes_views_status']=='true'){
			$row[] = array('title' => $app_lang['quotes_view_point'],'point'=>$data['quotes_views']);
		}

		if($data['quotes_add_status']=='true'){
			$row[] = array('title' => $app_lang['quotes_add_point'],'point'=>$data['quotes_add']);
		}

		if($data['like_quotes_points_status']=='true'){
			$row[] = array('title' => $app_lang['quotes_like_point'],'point'=>$data['like_quotes_points']);
		}

		if($data['other_user_quotes_status']=='true'){
			$row[] = array('title' => $app_lang['quotes_view_by_other'],'point'=>$data['other_user_quotes_point']);
		}
 
		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="get_transaction"){
		$jsonObj= array();	
		
		$redeem_id=$get_method['redeem_id'];

	    $query="SELECT * FROM willdev_users_redeem WHERE willdev_users_redeem.`id`='".$get_method['redeem_id']."' ORDER BY willdev_users_redeem.`id` DESC";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = '1';

		$row['redeem_id'] = $data['id'];			 
		$row['user_points'] = $data['user_points'];
		$row['redeem_price'] = $data['redeem_price'];
		$row['payment_mode'] = $data['payment_mode'];
		$row['bank_details'] = $data['bank_details'];
		$row['request_date'] = date('d-m-Y',strtotime($data['request_date']));

		if($data['cust_message']!=null){
			$row['cust_message'] = $data['cust_message'];	
		}else{
			$row['cust_message'] = '';
		}
		if($data['receipt_img']!=null && $data['status']==1){
			$row['receipt_img'] =  $file_path.'images/payment_receipt/'.$data['receipt_img'];	
		}else{
			
			$row['receipt_img'] =  '';
		}
		
		$row['responce_date'] = date('d-m-Y',strtotime($data['responce_date']));
		$row['status'] = $data['status'];
		
		$set['ANDROID_REWARDS_APP'] = $row;	

		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_spinner")
	{
		$jsonObj= array();	

		$user_id=$get_method['user_id'];

		if($user_id!='' || isset($user_id)){
			$query="SELECT count(*) AS total_cnt FROM  willdev_users_rewards_activity activity WHERE activity.`user_id`='$user_id' AND activity.`activity_type`='".$app_lang['lucky_spin_lbl']."' AND DATE_FORMAT(activity.`date`, '%Y-%m-%d') = CURDATE()";

			$res=mysqli_query($mysqli, $query);

			$data=mysqli_fetch_assoc($res);

			$set['daily_spinner_limit'] = SPINNER_LIMIT;

			$set['ad_on_spin'] = $settings_details['ad_on_spin'];	

			$remain_spin=SPINNER_LIMIT-$data['total_cnt'];

			$set['remain_spin'] = "$remain_spin";	

		    $query1="SELECT * FROM willdev_spinner ORDER BY willdev_spinner.`block_id` DESC";

			$sql = mysqli_query($mysqli,$query1)or die(mysqli_error($mysqli));

			while($data = mysqli_fetch_assoc($sql))
			{
				$row['spinner_id'] = $data['block_id'];			 
				$row['points'] = $data['block_points'];
				$row['bg_color'] = '#'.$data['block_bg'];	 
			 	 
				array_push($jsonObj,$row);
			
			}
			
			$set['ANDROID_REWARDS_APP'] = $jsonObj;		
		}
		else{
			$set['ANDROID_REWARDS_APP'][] = array('msg' => 'User ID is required !','success'=>'0');
		}

		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="save_spinner_points"){

		$jsonObj= array();	

		$user_id=$get_method['user_id'];
		$ponints=$get_method['ponints'];

		$query="SELECT count(*) AS total_cnt FROM  willdev_users_rewards_activity activity WHERE activity.`user_id`='$user_id' AND activity.`activity_type`='".$app_lang['lucky_spin_lbl']."' AND DATE_FORMAT(activity.`date`, '%Y-%m-%d') = CURDATE()";

		$sql = mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
		$row = mysqli_fetch_assoc($sql);

		if($row['total_cnt'] < SPINNER_LIMIT){

			$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".$ponints."' WHERE id = '".$user_id."'");

			user_reward_activity('',$user_id, $app_lang['lucky_spin_lbl'], $ponints);
			
			$remain_spin=SPINNER_LIMIT-$row['total_cnt']-1;

			$set['ANDROID_REWARDS_APP'][] = array('daily_spinner_limit' => SPINNER_LIMIT,'remain_spin' => "$remain_spin",'msg' => $app_lang['point_save'].' '.$ponints.' points','success'=>'1');

		}
		else{

			$remain_spin=SPINNER_LIMIT-$row['total_cnt'];

			$set['ANDROID_REWARDS_APP'][] = array('daily_spinner_limit' => SPINNER_LIMIT,'remain_spin' => "$remain_spin",'msg' => $app_lang['reach_limit'],'success'=>'0');


		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}

	else if($get_method['method_name']=="profile_verify"){

		$jsonObj= array();	

		$user_id=$get_method['user_id'];
		$full_name=$get_method['full_name'];
		$message=$get_method['message'];

		$ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);

		$path = "images/documents/"; //set your folder path
		$document=date('dmYhis').'_'.rand(0,99999).".".$ext;
        //Main Image
        $tpath1=$path.$document;        
        if($ext!='png'){
        	$pic1=compress_image($_FILES["document"]["tmp_name"], $tpath1, 80);
        }else{
            move_uploaded_file($_FILES['document']['tmp_name'], $tpath1);
        }

        $sql="SELECT * FROM willdev_verify_user WHERE `user_id`='$user_id'";
        $res=mysqli_query($mysqli, $sql);

        if(mysqli_num_rows($res) > 0){

        	$row=mysqli_fetch_assoc($res);

        	if($row['status']=='1'){
        		$set['ANDROID_REWARDS_APP'] = array('msg' => 'Your profile is verified..','success'=>
        			'1');
        		header( 'Content-Type: application/json; charset=utf-8' );
			    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
				die();
        	}
        	else{

        		$data = array(
		            'user_id'  => $user_id,
		            'full_name'  => $full_name,
		            'message'  =>  $message,
		            'document'  => $document,
		            'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
		            'is_opened'  => '0',
		            'status'  => '0'
		        );		
			    $update=Update("willdev_verify_user", $data, "WHERE user_id = '$user_id'");

			    $data = array('is_verified'  =>  '0');
			    $update=Update("willdev_users", $data, "WHERE id = '$user_id'");

        		$set['ANDROID_REWARDS_APP'] = array('msg' => 'Verification request is sended to admin...','success'=>
        			'1');
        		header( 'Content-Type: application/json; charset=utf-8' );
			    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
				die();
        	}
        }else{


        	$data = array(
	            'user_id'  => $user_id,
	            'full_name'  => $full_name,
	            'message'  =>  $message,
	            'document'  => $document,
	            'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
	            'is_opened'  => '0'
	        );		

          	$insert = Insert('willdev_verify_user',$data);

          	$set['ANDROID_REWARDS_APP'] = array('msg' => 'Verification request is sended to admin...','success'=>'1');

          	 header( 'Content-Type: application/json; charset=utf-8' );
		    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
        }

	}
	else if($get_method['method_name']=="delete_comment")
	{
		$jsonObj= array();	
		$comment_id=$get_method['comment_id'];
		$post_id=$get_method['post_id'];
		$type=$get_method['type'];

		$set['success']="1";	
		$set['msg']=$app_lang['comment_delete'];

		$sql="SELECT * FROM willdev_comments WHERE willdev_comments.`post_id`='$post_id' AND `type`='$type' ORDER BY willdev_comments.`id` DESC LIMIT 1 OFFSET 2";

		$res=mysqli_query($mysqli,$sql);

		Delete('willdev_comments','id='.$get_method['comment_id'].'');

		if(mysqli_num_rows($res) > 0){

			$set['comment_status'] = "1";
			$row=mysqli_fetch_assoc($res);

			$set['total_comment'] = CountRow('willdev_comments',"post_id='$post_id' AND type='$type'");

			$set['comment_id'] = $row['id'];			 
			$set['user_id'] = $row['user_id'];			 
			$set['user_name'] = get_user_info($row['user_id'],'name')?get_user_info($row['user_id'],'name'):$row['user_name'];			 

			if(get_user_info($row['user_id'],'user_image')!='')
			{
				$set['user_image'] = $file_path.'images/'.get_user_info($row['user_id'],'user_image');
			}	
			else
			{
				$set['user_image'] ='';
			}
			$set['post_id'] = $row['post_id'];
			$set['status_type'] = $row['type'];
			$set['comment_text'] = $row['comment_text'];
			$set['comment_date'] = calculate_time_span($row['comment_on']);
		}
		else{
			$set['comment_status'] = "0";
			$set['total_comment'] = CountRow('willdev_comments',"post_id='$post_id' AND type='$type'");;			 
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
		die();

	}
	else if($get_method['method_name']=="get_all_comments")
	{
		$jsonObj= array();	
		$post_id=$get_method['post_id'];
		$user_id=$get_method['user_id'];

		$type=$get_method['type'];

		$page_limit=10;

		$limit=($get_method['page']-1) * $page_limit;

		$sql="SELECT * FROM willdev_comments WHERE willdev_comments.`post_id`='$post_id' AND willdev_comments.`user_id`='$user_id' AND willdev_comments.`type`='$type' AND willdev_comments.`status`='1' ORDER BY willdev_comments.`id` DESC LIMIT $limit, $page_limit";

		$res=mysqli_query($mysqli,$sql);

		if(mysqli_num_rows($res) > 0){
			while ($row=mysqli_fetch_assoc($res)) {

				$info['total_comment'] = CountRow('willdev_comments',"post_id='$post_id' AND type='$type'");;			 
				$info['comment_id'] = $row['id'];			 
				$info['user_id'] = $row['user_id'];			 
				$info['user_name'] = get_user_info($row['user_id'],'name') ? get_user_info($row['user_id'],'name'): $row['user_name'];			 

				if(get_user_info($row['user_id'],'user_image')!='')
				{
					$info['user_image'] = $file_path.'images/'.get_user_info($row['user_id'],'user_image');
				}	
				else
				{
					$info['user_image'] ='';
				}
				$info['post_id'] = $row['post_id'];
				$info['status_type'] = $row['type'];
				$info['comment_text'] = $row['comment_text'];
				$info['comment_date'] = calculate_time_span($row['comment_on']);

				array_push($jsonObj,$info);
			}
		}

		mysqli_free_result($res);

		$sql="SELECT * FROM willdev_comments WHERE willdev_comments.`post_id`='$post_id' AND willdev_comments.`user_id` <> '$user_id' AND willdev_comments.`type`='$type' AND willdev_comments.`status`='1' ORDER BY willdev_comments.`id` DESC LIMIT $limit, $page_limit";

		$res=mysqli_query($mysqli,$sql);

		if(mysqli_num_rows($res) > 0){
			while ($row=mysqli_fetch_assoc($res)) {

				$info['total_comment'] = CountRow('willdev_comments',"post_id='$post_id' AND type='$type'");;			 

				$info['comment_id'] = $row['id'];			 
				$info['user_id'] = $row['user_id'];			 
				$info['user_name'] = get_user_info($row['user_id'],'name') ? get_user_info($row['user_id'],'name'): $row['user_name'];			 

				if(get_user_info($row['user_id'],'user_image')!='')
				{
					$info['user_image'] = $file_path.'images/'.get_user_info($row['user_id'],'user_image');
				}	
				else
				{
					$info['user_image'] ='';
				}
				$info['post_id'] = $row['post_id'];
				$info['status_type'] = $row['type'];
				$info['comment_text'] = $row['comment_text'];
				$info['comment_date'] = calculate_time_span($row['comment_on']);

				array_push($jsonObj,$info);
			}
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;
				 
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
		die();

	}
	else if($get_method['method_name']=="user_suspend")
	{
		$jsonObj= array();	
		$user_id=$get_method['account_id'];

		$sql="SELECT * FROM willdev_suspend_account WHERE user_id='$user_id' ORDER BY `id` DESC LIMIT 1";

		$res=mysqli_query($mysqli,$sql);

		$row=mysqli_fetch_assoc($res);

		if(mysqli_num_rows($res) > 0){

			if($row['status']==1){
				$set['success']='0';	
				$set['date']=date('d-m-Y',$row['suspended_on']);
				$set['msg']=$row['suspension_reason'];
			}else{
				$set['success']='1';
				$set['date']=date('d-m-Y',$row['activated_on']);
				$set['msg']='';
			}
		}

		$set['user_name']=get_user_info($user_id,'name');

		if(get_user_info($user_id,'user_image')!='')
		{
			$set['user_image'] = $file_path.'images/'.get_user_info($user_id,'user_image');
		}	
		else
		{
			$set['user_image'] ='';
		}

		if(get_user_info($user_id,'is_verified')==1){
			$set['is_verified']="true";
		}
		else{
			$set['is_verified']="false";
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
		die();

	}
	else if($get_method['method_name']=="get_contact")		
	{
		$jsonObj= array();	

		$query="SELECT * FROM willdev_contact_sub WHERE status='1' ORDER BY id DESC";
		$sql = mysqli_query($mysqli,$query);

		if($get_method['user_id']!='')
		{
			$user_id=$get_method['user_id'];
			$user_qry="SELECT * FROM willdev_users WHERE `id` = '$user_id'";
			$user_result=mysqli_query($mysqli,$user_qry);
			$user_row=mysqli_fetch_assoc($user_result);

			$set['name'] = $user_row['name'];
			$set['email'] = $user_row['email'];
		}
		else
		{
			$set['name'] = '';
			$set['email'] = '';
		}

		if(mysqli_num_rows($sql) > 0)
		{
			while ($data = mysqli_fetch_assoc($sql)){
				$info['id']=$data['id'];
				$info['subject']=$data['title'];
				array_push($jsonObj, $info);
			}
		}
	
		$set['ANDROID_REWARDS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="get_payment_mode")	
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_payment_mode WHERE status='1' ORDER BY `id` ASC";
		$sql = mysqli_query($mysqli,$query);

		if(mysqli_num_rows($sql) > 0){
			while ($data = mysqli_fetch_assoc($sql)){
				$row['id']=$data['id'];
				$row['mode_title']=$data['mode_title'];
				array_push($jsonObj, $row);
			}
		}

		$set['ANDROID_REWARDS_APP'] = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}

	else if($get_method['method_name']=="app_settings")		
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;

		$row['app_name'] = $data['app_name'];

		if(file_exists('privacy_policy.php'))
        {
			$row['privacy_policy_url'] = $file_path.'privacy_policy.php';
		}
		else{
			$row['privacy_policy_url'] = '';
		}
			
		$row['publisher_id'] = $data['publisher_id'];
		$row['interstitial_ad'] = $data['interstitial_ad'];
		$row['banner_ad'] = $data['banner_ad'];
		$row['interstitial_ad_click'] = $data['interstitial_ad_click'];
		$row['rewarded_video_ads'] = $data['rewarded_video_ads'];
		$row['rewarded_video_ads_id'] = $data['rewarded_video_ads_id'];
		$row['rewarded_video_click'] = $data['rewarded_video_click'];

		$row['watermark_image'] = $data['watermark_image'] ? $file_path.'images/'.$data['watermark_image'] : "";

		if($row['watermark_image']==''){
			$row['watermark_on_off'] = 'false';
		}
		else{
			$row['watermark_on_off'] = $data['watermark_on_off'];
		}
		
		$row['banner_ad_type'] =$data['banner_ad_type'];

		if($data['banner_ad_type']=='facebook'){

			$row['banner_ad_id'] = $data['facebook_banner_ad_id'];
		}
		else{
			$row['banner_ad_id'] = $data['banner_ad_id'];
		}

		$row['interstitial_ad_type'] =$data['interstitial_ad_type'];
		
		if($data['interstitial_ad_type']=='facebook'){

			$row['interstitial_ad_id'] = $data['facebook_interstitial_ad_id'];
		}
		else{
			$row['interstitial_ad_id'] = $data['interstitial_ad_id'];
		}

		$row['spinner_opt'] = $data['spinner_opt'];

		$row['video_views_status_ad'] = $data['video_views_status'];
		
		$row['like_video_status_ad'] = $data['like_video_points_status'];
		$row['download_video_status_ad'] = $data['download_video_points_status'];
		
		$row['like_image_status_ad'] = $data['like_image_points_status'];
		$row['download_image_status_ad'] = $data['download_image_points_status'];
		
		$row['like_gif_points_status_ad'] = $data['like_gif_points_status'];
		$row['download_gif_status_ad'] = $data['download_gif_points_status'];

		$row['like_quotes_status_ad'] = $data['like_quotes_points_status'];

		$row['app_update_status'] = $data['app_update_status'];
		$row['app_new_version'] = $data['app_new_version'];
		$row['app_update_desc'] = stripslashes($data['app_update_desc']);
		$row['app_redirect_url'] = $data['app_redirect_url'];
		$row['cancel_update_status'] = $data['cancel_update_status'];
 
		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="app_about")		
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;
		$row['package_name'] = $data['package_name'];  
		$row['app_name'] = $data['app_name'];
		$row['app_logo'] = $data['app_logo'];
		$row['app_version'] = $data['app_version'];
		$row['app_author'] = $data['app_author'];
		$row['app_contact'] = $data['app_contact'];
		$row['app_email'] = $data['app_email'];
		$row['app_website'] = $data['app_website'];
		$row['app_description'] = $data['app_description'];
		$row['app_developed_by'] = $data['app_developed_by'];

		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="app_faq")		
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;
		$row['app_faq'] = stripslashes($data['app_faq']);
 
		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="app_privacy_policy")		
	{
		  
		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;
		$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);
 
		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="reward_points")		
	{
		  
		$user_id=$get_method['user_id'];

		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;

		$row['total_point'] = get_user_info($user_id,'total_point');
		$row['user_id'] = $user_id;
		$row['redeem_points'] = $data['redeem_points'];
		$row['redeem_money'] = $data['redeem_money'].' '.$data['redeem_currency'];
		$row['minimum_redeem_points'] = $data['minimum_redeem_points'];

		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="upload_status_opt")		
	{

		$jsonObj= array();	

		$query="SELECT * FROM willdev_settings WHERE id='1'";
		$sql = mysqli_query($mysqli,$query);

		$data = mysqli_fetch_assoc($sql);

		$row['success'] = 1;
		$row['video_upload_opt'] = $data['video_upload_opt'];
		$row['image_upload_opt'] = $data['image_upload_opt'];
		$row['gif_upload_opt'] = $data['gif_upload_opt'];
		$row['quotes_upload_opt'] = $data['quotes_upload_opt'];

		$row['video_add'] = $data['video_add_status'];
		$row['image_add'] = $data['image_add_status'];
		$row['gif_add'] = $data['gif_add_status'];
		$row['quotes_add'] = $data['quotes_add_status'];

		$row['video_file_size'] = $data['video_file_size'];
		$row['video_file_duration'] = $data['video_file_duration'];
		$row['image_file_size'] = $data['image_file_size'];
		$row['gif_file_size'] = $data['gif_file_size'];

		$video_msg=str_replace('###', $data['video_file_size'], $app_lang['video_msg']);
		$video_msg=str_replace('$$$', $data['video_file_duration'], $video_msg);

		$row['video_msg'] = $video_msg;

		$row['video_size_msg'] = str_replace('###', $data['video_file_size'], $app_lang['video_size_msg']);

		$row['video_duration_msg'] = str_replace('###', $data['video_file_duration'], $app_lang['video_duration_msg']);

		$row['img_size_msg'] = str_replace('###', $data['image_file_size'], $app_lang['img_size_msg']);
		$row['gif_size_msg'] = str_replace('###', $data['gif_file_size'], $app_lang['gif_size_msg']);

		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="apply_user_refrence_code")		
	{
		  
		$user_id=$get_method['user_id'];
		$user_refrence_code=addslashes(trim($get_method['user_refrence_code']));

		$jsonObj= array();	

		$user_qry="SELECT * FROM willdev_users WHERE `user_code`='$user_refrence_code' AND `id` <> '$user_id'";
		$user_result=mysqli_query($mysqli,$user_qry);
		$user_row=mysqli_fetch_assoc($user_result);

		if(APP_REFER_REWARD_POINTS_STATUS=='true')
		{ 
			if($user_row['id']!="")
			{		  
				$view_qry=mysqli_query($mysqli,"UPDATE willdev_users SET total_point= total_point + '".API_REFER_REWARD."' WHERE id = '".$user_row['id']."'");

				$refer_msg_text= str_replace('###', get_user_info($user_id,'name'), $app_lang['user_refer_msg']);
			
				user_reward_activity('',$user_row['id'], $refer_msg_text, API_REFER_REWARD);

				$row['success'] = 1;
				$row['msg'] = $app_lang['apply_reference_code'];

			}
			else{
				$row['success'] = 0;
				$row['msg'] = $app_lang['invalid_reference_code'];
			}
		}

		$set['ANDROID_REWARDS_APP'] = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="fetch_onesignal_id")		
	{
		$qry="SELECT * FROM willdev_settings where id='1'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result); 

		$onesignal_app_id = $row['onesignal_app_id'];
		
		$set['ANDROID_REWARDS_APP']=array('onesignal_app_id' => $onesignal_app_id, 'success'=>'1');
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else
	{
		$set['status']=0;
        $set['message']="Invalid Method !!";

        header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	 
	 
?>