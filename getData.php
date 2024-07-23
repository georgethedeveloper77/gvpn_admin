<?php 
    require("includes/connection.php");
    require("includes/function.php");
    require("language/language.php");

    $pageEnd = 10;

    $pageStart = ($_GET['page']  - 1) * $pageEnd;
    
    $type=$_GET['type'];

    $items=array();

    if($_GET['page']==1)
        $items[] = array("id"=>0, "text"=>'---Select---');

    if($type=='category'){
        if(isset($_GET['search'])){
        
            $search=trim($_GET['search']);
            
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_category WHERE `status`='1' AND `category_name` LIKE '%$search%'");
            
            $query = "SELECT * FROM willdev_category WHERE `status`='1' AND `category_name` ORDER BY category_name DESC LIKE '%$search%' LIMIT $pageStart, $pageEnd";
        }
        else{
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_category WHERE `status`='1'");
            $query = "SELECT * FROM willdev_category WHERE `status`='1' ORDER BY cid DESC LIMIT $pageStart, $pageEnd";
        }
        
        $total_items=mysqli_num_rows($sql_total);
        
        $res=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        
        $numRows = mysqli_num_rows($res);
        
        if($numRows > 0) {
            while($row = mysqli_fetch_array($res)) {
                $items[] = array("id"=>$row['cid'], "text"=>$row['category_name']);
            }
        }else {
            if(count($items) > 0)
                $items[] = array("id"=>"0", "text"=>"No Results Found...");
        }
    }
    else if($type=='video'){
        if(isset($_GET['search'])){
        
            $search=trim($_GET['search']);
            
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_video WHERE `video_title` LIKE '%$search%'");
            
            $query = "SELECT * FROM willdev_video WHERE `video_title` LIKE '%$search%' ORDER BY video_title DESC LIMIT $pageStart, $pageEnd";
        }
        else{
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_video");
            $query = "SELECT * FROM willdev_video ORDER BY id DESC LIMIT $pageStart, $pageEnd";
        }
        
        $total_items=mysqli_num_rows($sql_total);
        
        $res=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        
        $numRows = mysqli_num_rows($res);
        
        if($numRows > 0) {
            while($row = mysqli_fetch_array($res)) {
                $items[] = array("id"=>$row['id'], "text"=>stripslashes($row['video_title']));
            }
        }else {
            if(count($items) > 0)
                $items[] = array("id"=>"0", "text"=>"No Results Found...");
        }
    }
    else if($type=='image'){
        if(isset($_GET['search'])){
        
            $search=trim($_GET['search']);
            
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='image' AND `image_title` LIKE '%$search%'");
            
            $query = "SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='image' AND `image_title` LIKE '%$search%' ORDER BY image_title DESC LIMIT $pageStart, $pageEnd";
        }
        else{
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='image'");
            $query = "SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='image' ORDER BY id DESC LIMIT $pageStart, $pageEnd";
        }
        
        $total_items=mysqli_num_rows($sql_total);
        
        $res=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        
        $numRows = mysqli_num_rows($res);
        
        if($numRows > 0) {
            while($row = mysqli_fetch_array($res)) {
                $items[] = array("id"=>$row['id'], "text"=>$row['image_title']);
            }
        }else {
            if(count($items) > 0)
                $items[] = array("id"=>"0", "text"=>"No Results Found...");
        }
    }
    else if($type=='gif'){
        if(isset($_GET['search'])){
        
            $search=trim($_GET['search']);
            
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='gif' AND `image_title` LIKE '%$search%'");
            
            $query = "SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='gif' AND `image_title` LIKE '%$search%' ORDER BY image_title DESC LIMIT $pageStart, $pageEnd";
        }
        else{
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='gif'");
            $query = "SELECT * FROM willdev_img_status WHERE `status`='1' AND status_type='gif' ORDER BY id DESC LIMIT $pageStart, $pageEnd";
        }
        
        $total_items=mysqli_num_rows($sql_total);
        
        $res=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        
        $numRows = mysqli_num_rows($res);
        
        if($numRows > 0) {
            while($row = mysqli_fetch_array($res)) {
                $items[] = array("id"=>$row['id'], "text"=>$row['image_title']);
            }
        }else {
            if(count($items) > 0)
                $items[] = array("id"=>"0", "text"=>"No Results Found...");
        }
    }
    else if($type=='quote'){
        if(isset($_GET['search'])){
        
            $search=trim($_GET['search']);
            
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_quotes WHERE `status`='1' AND `quote` LIKE '%$search%'");
            
            $query = "SELECT * FROM willdev_quotes WHERE `status`='1' AND `quote` LIKE '%$search%' ORDER BY quote DESC LIMIT $pageStart, $pageEnd";
        }
        else{
            $sql_total=mysqli_query($mysqli,"SELECT * FROM willdev_quotes WHERE `status`='1'");
            $query = "SELECT * FROM willdev_quotes WHERE `status`='1' ORDER BY id DESC LIMIT $pageStart, $pageEnd";
        }
        
        $total_items=mysqli_num_rows($sql_total);
        
        $res=mysqli_query($mysqli, $query) or die(mysqli_error($mysqli));
        
        $numRows = mysqli_num_rows($res);
        
        if($numRows > 0) {
            while($row = mysqli_fetch_array($res)) {
                $items[] = array("id"=>$row['id'], "text"=>stripslashes($row['quote']));
            }
        }else {
            if(count($items) > 0)
                $items[] = array("id"=>"0", "text"=>"No Results Found...");
        }
    }
    
    $response=array('items' =>$items, 'total_count' => $total_items);
    
    echo json_encode($response);

?>