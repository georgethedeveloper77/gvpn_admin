<?php 

$page_title="Manage Users";
$active_page="user";

include('includes/header.php'); 
include("includes/connection.php");

include("includes/function.php");
include("language/language.php"); 

$tableName="willdev_users";		
$targetpage = $redirectUrl; 
$limit = 20; 

$keyword='';

if(isset($_GET['status']))
{

	if($_GET['status']=='verified'){
		$status="willdev_users.`is_verified`='1'";
	}
	else if($_GET['status']=='not_verified'){
		$status="willdev_users.`is_verified`='0'";
	}
	else if($_GET['status']=='active'){
		$status="willdev_users.`status`='1'";
	}
	else if($_GET['status']=='suspend'){
		$status="willdev_users.`status`='0'";
	}

	$query = "SELECT * FROM $tableName WHERE `id` <> '0' AND $status";

	if(isset($_GET['keyword']))
	{

		$keyword=addslashes(trim($_GET['keyword']));

		$query = "SELECT * FROM $tableName WHERE willdev_users.`id` <> '0' AND $status AND (willdev_users.`name` LIKE '%$keyword%' OR willdev_users.`email` LIKE '%$keyword%' OR willdev_users.`device_id` LIKE '%$keyword%')";
	}


}
else if(isset($_GET['keyword']))
{

	$keyword=addslashes(trim($_GET['keyword']));

	$query = "SELECT * FROM $tableName WHERE `id` <> '0' AND (willdev_users.`name` LIKE '%$keyword%' OR willdev_users.`email` LIKE '%$keyword%' OR willdev_users.`device_id` LIKE '%$keyword%')";
}
else
{
	$query = "SELECT * FROM $tableName WHERE willdev_users.`id` <> '0'";
}


$total_pages = mysqli_num_rows(mysqli_query($mysqli,$query));

$stages = 3;
$page=0;
if(isset($_GET['page'])){
	$page = mysqli_real_escape_string($mysqli,$_GET['page']);
}
if($page){
	$start = ($page - 1) * $limit; 
}else{
	$start = 0; 
}

if(isset($_GET['status']))
{
	if($_GET['status']=='verified'){
		$status="willdev_users.`is_verified`='1'";
	}
	else if($_GET['status']=='not_verified'){
		$status="willdev_users.`is_verified`='0'";
	}
	else if($_GET['status']=='active'){
		$status="willdev_users.`status`='1'";
	}
	else if($_GET['status']=='suspend'){
		$status="willdev_users.`status`='0'";
	}

	$query="SELECT * FROM willdev_users WHERE willdev_users.`id` <> '0' AND $status ORDER BY willdev_users.`id` DESC LIMIT $start, $limit";

	if(isset($_GET['keyword']))
	{

		$keyword=addslashes(trim($_GET['keyword']));

		$query = "SELECT * FROM willdev_users WHERE willdev_users.`id` <> '0' AND $status AND (willdev_users.`name` LIKE '%$keyword%' OR willdev_users.`email` LIKE '%$keyword%' OR willdev_users.`device_id` LIKE '%$keyword%' OR willdev_users.`auth_id` LIKE '%$keyword%') ORDER BY willdev_users.`id` DESC LIMIT $start, $limit";
	}


}
else if(isset($_GET['keyword']))
{
	$keyword=addslashes(trim($_GET['keyword']));

	$query="SELECT * FROM willdev_users WHERE willdev_users.`id` <> '0' AND (willdev_users.`name` LIKE '%$keyword%' OR willdev_users.`email` LIKE '%$keyword%' OR willdev_users.`device_id` LIKE '%$keyword%' OR willdev_users.`auth_id` LIKE '%$keyword%') ORDER BY willdev_users.`id` DESC LIMIT $start, $limit";

}
else
{

	$query="SELECT * FROM willdev_users WHERE willdev_users.`id` <> '0' ORDER BY willdev_users.`id` DESC LIMIT $start, $limit";

}

$users_result=mysqli_query($mysqli,$query);


$sql_verify="SELECT varify_u.*, user.`name`, user.`email` FROM willdev_verify_user varify_u, willdev_users user WHERE varify_u.`user_id`=user.`id` AND varify_u.`status`='0' ORDER BY varify_u.`id` DESC";

$res_verify=mysqli_query($mysqli, $sql_verify) or die(mysqli_error($mysqli));

function highlightWords($text, $word){
	$text = preg_replace('#'. preg_quote($word) .'#i', '<span style="background-color: #F9F902;">\\0</span>', $text);
	return $text;
}
?>


<div class="row">
	<div class="col-xs-12">
		<div class="card mrg_bottom">
			<div class="page_title_block">
				<div class="col-md-5 col-xs-12">
					<div class="page_title"><?=$page_title?></div>
				</div>
				<div class="col-md-7 col-xs-12">              
					<div class="search_list">
						<div class="search_block">
							<form method="get" id="searchForm" action="">
								<input class="form-control input-sm" placeholder="Search here..." aria-controls="DataTables_Table_0" type="search" name="keyword" value="<?php if(isset($_GET['keyword'])){ echo $_GET['keyword'];} ?>" required="required">
								<button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
							</form>  
						</div>
						<div class="add_btn_primary"> <a href="add_user.php?add=yes&redirect=<?=$redirectUrl?>">Add User</a> </div>
					</div> 
				</div>
				<form id="filterForm" accept="" method="GET">
					<?php 
					if(isset($_GET['keyword'])){
						echo '<input type="hidden" name="keyword" value="'.$_GET['keyword'].'">';
					}
					?>
					<div class="col-md-3">
						<select name="status" class="form-control select2 filter">
							<option value="">All</option>
							<option value="verified" <?php if(isset($_GET['status']) && $_GET['status']=='verified'){ echo 'selected';} ?>>Verified</option>
							<option value="not_verified" <?php if(isset($_GET['status']) && $_GET['status']=='not_verified'){ echo 'selected';} ?>>Not verified</option>
							<option value="active" <?php if(isset($_GET['status']) && $_GET['status']=='active'){ echo 'selected';} ?>>Active</option>
							<option value="suspend" <?php if(isset($_GET['status']) && $_GET['status']=='suspend'){ echo 'selected';} ?>>Suspended</option>
						</select>
					</div>
				</form>
				<div class="col-md-4 col-xs-12 text-right" style="float: right;">
					<div class="checkbox" style="width: 95px;margin-top: 5px;float: left;right: 90px;position: absolute;">
						<input type="checkbox" id="checkall">
						<label for="checkall">
							Select All
						</label>
					</div>
					<div class="dropdown" style="float:right">
						<button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown">Action
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" style="right:0;left:auto;">
							<li><a href="javascript:void(0)" class="actions" data-action="delete">Delete !</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12 mrg-top manage_user_btn">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th nowrap=""></th>
								<th>Device ID</th>
								<th>Image</th>
								<th>Name</th>
								<th>Email/Google/Facebook ID</th>
								<th>Points</th>				  
								<th hidden >Verify Status</th>
								<th hidden >Status</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							if(mysqli_num_rows($users_result) > 0)
							{
								while($users_row=mysqli_fetch_array($users_result))
								{

									$device_id = !empty($keyword)?highlightWords($users_row['device_id'], $keyword):$users_row['device_id'];
									$name = !empty($keyword)?highlightWords($users_row['name'], $keyword):$users_row['name'];

									if($users_row['email']!='' AND $users_row['user_type']=='Normal')
									{
										$email = !empty($keyword)?highlightWords($users_row['email'], $keyword):$users_row['email'];	
									}
									else if($users_row['user_type']=='Google'){
										if($users_row['user_type']=='Google' AND $users_row['email']=='' AND $users_row['auth_id']!=''){
											$email = !empty($keyword)?highlightWords($users_row['auth_id'], $keyword):$users_row['auth_id'];
										}
										else{
											$email = !empty($keyword) ? highlightWords($users_row['email'], $keyword):$users_row['email'];
										}

									}
									else if($users_row['user_type']=='Facebook'){
										if($users_row['user_type']=='Facebook' AND $users_row['email']=='' AND $users_row['auth_id']!=''){
											$email = !empty($keyword)?highlightWords($users_row['auth_id'], $keyword):$users_row['auth_id'];
										}
										else{
											$email = !empty($keyword)?highlightWords($users_row['email'], $keyword):$users_row['email'];
										}

									} 
									?>
									<tr <?php if($users_row['is_duplicate']==1){ echo 'style="background-color: rgba(255,0,0,0.1);"'; } ?> >
										<td> 
											<div>
												<div class="checkbox">
													<input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $users_row['id']; ?>" class="post_ids">
													<label for="checkbox<?php echo $i;?>"></label>
												</div>
											</div>
										</td>
										<td><?=($device_id!='') ? $device_id : '-';?></td>
										<td>
											<div class="row">
												<div class="col-md-12">
													<?php 
													if($users_row['user_type']=='Google'){
														echo '<img src="assets/images/google-logo.png" class="social_img">';
													}
													else if($users_row['user_type']=='Facebook'){
														echo '<img src="assets/images/facebook-icon.png" class="social_img">';
													}
													?>
													<?php if($users_row['user_image']!="" and file_exists('images/'.$users_row['user_image'])) {?>
														<img type="image" src="images/<?php echo $users_row['user_image'];?>" alt="image" style="width: 40px;height: 40px"/>
													<?php }else{?>  
														<img type="image" src="assets/images/user_photo.png" alt="image" style="width: 40px;height: 40px"/>
													<?php } ?>
												</div>
											</div>
										</td>
										<td style="word-wrap: break-word;">
											<?php echo $name;?>
										</td>

										<td><?php echo $email;?></td>   
										<td><?php echo thousandsNumberFormat(get_total_points($users_row['id']));?></td>		
	

										<td hidden >
											<?php if($users_row['status']!="2"){?>
												<a href="" class="btn_status" data-id="<?=$users_row['id']?>" data-action="suspend" title="Change Status"><span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Active</span></span></a>

											<?php }else{?>
												<a href="" class="btn_status" data-id="<?=$users_row['id']?>" data-action="active" title="Change Status"><span class="badge badge-danger badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Suspended </span></span></a>
											<?php }?>
										</td>
										<td nowrap="">

											<a href="add_user.php?user_id=<?php echo $users_row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-primary" data-toggle="tooltip" data-tooltip="Edit"><i class="fa fa-edit"></i></a>

											<a href="javascript:void(0)" class="btn btn-danger btn_delete_a" data-id="<?php echo $users_row['id'];?>" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
									<?php	
									$i++;
								}
							}
							else{
								?>
								<tr>
									<td colspan="9" align="center">
										<h5 class="no_data">Sorry! no data</h5>
									</td>
								</tr>
								<?php
							}
						?>
						</tbody>
					</table>
				</div>
				<!-- Pagination -->

				<div class="col-md-12 col-xs-12">
					<div class="pagination_item_block">
						<nav>
							<?php include("pagination.php");?>
						</nav>
					</div>
				</div>

			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div> 

<div id="suspendModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content" style="border-radius: 5px;overflow: hidden;">
			<div class="modal-header" style="padding-top: 15px;padding-bottom: 15px;">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Suspend Account</h4>
			</div>
			<div class="modal-body">
				<form id="suspendForm">
					<div class="form-group">
						<label style="font-weight: 500">Reason for Suspension:</label>
						<textarea placeholder="E.g. Upload same video multiple times, Having multiple times accounts and so on.." class="form-control" name="suspend_reason" required=""></textarea>
					</div>
					<div class="form-group">
						<button type="submit" name="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div> 

<?php include('includes/footer.php');?> 


<script type="text/javascript">

	var totalItems=0;

	$("#checkall").click(function () {

		totalItems=0;

		$('input:checkbox').not(this).prop('checked', this.checked);
		$.each($("input[name='post_ids[]']:checked"), function(){
			totalItems=totalItems+1;
		});

		if($('input:checkbox').prop("checked") == true){
			$('.notifyjs-corner').empty();
			$.notify(
				'Total '+totalItems+' item checked',
				{ position:"top center",className: 'success'}
				);
		}
		else if($('input:checkbox'). prop("checked") == false){
			totalItems=0;
			$('.notifyjs-corner').empty();
		}
	});

	var noteOption = {
		clickToHide : false,
		autoHide : false,
	}

	$.notify.defaults(noteOption);

	$(".post_ids").click(function(e){

		if($(this).prop("checked") == true){
			totalItems=totalItems+1;
		}
		else if($(this). prop("checked") == false){
			totalItems = totalItems-1;
		}

		if(totalItems==0){
			$('.notifyjs-corner').empty();
			exit();
		}

		$('.notifyjs-corner').empty();

		$.notify(
			'Total '+totalItems+' item checked',
			{ position:"top center",className: 'success'}
			);


	});


	$(".actions").click(function(e){
		e.preventDefault();

		var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });
		var _action=$(this).data("action");

		if(_ids!='')
		{
			swal({
				title: "Action: "+$(this).text(),
				text: "<?=$client_lang['multi_action_txt']?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger btn_edit",
				cancelButtonClass: "btn-warning btn_edit",
				confirmButtonText: "Yes",
				cancelButtonText: "No",
				closeOnConfirm: false,
				closeOnCancel: false,
				showLoaderOnConfirm: true
			},
			function(isConfirm) {
				if (isConfirm) {

					var _table='willdev_users';

					$.ajax({
						type:'post',
						url:'processData.php',
						dataType:'json',
						data:{id:_ids,for_action:_action,table:_table,'action':'multi_action'},
						success:function(res){
							console.log(res);
							if(res.status=='1'){
								swal({
									title: "<?=$client_lang['multi_action_success_lbl']?>", 
									text: "<?=$client_lang['multi_action_msg']?>", 
									type: "success"
								},function() {
									location.reload();
								});
							}
						}
					});
				}
				else{
					swal.close();
				}

			});
		}
		else{
			swal({title: 'Sorry no selected!', type: 'info'});
		}
	});

	$(".btn_delete_a").click(function(e){

		e.preventDefault();

		var _id=$(this).data("id");

		swal({
			title: "<?=$client_lang['are_you_sure_msg']?>",
			text: "<?=$client_lang['are_you_sure_belong_msg']?>",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			cancelButtonClass: "btn-warning",
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			closeOnConfirm: false,
			closeOnCancel: false,
			showLoaderOnConfirm: true
		},
		function(isConfirm) {
			if (isConfirm) {

				$.ajax({
					type:'post',
					url:'processData.php',
					dataType:'json',
					data:{id:_id,for_action:'delete',table:'willdev_users','action':'multi_action'},
					success:function(res){
						console.log(res);
						$('.notifyjs-corner').empty();

						if(res.status==1){
							location.reload();
						}
						else{
							swal({
								title: 'Error!', 
								text: "<?=$client_lang['something_went_worng_err']?>", 
								type: 'error'
							},function() {
								location.reload();
							});
						}
					}
				});
			}
			else{
				swal.close();
			}
		});
	});

	$(".btn_status").on("click",function(e){
		e.preventDefault();
		var _id=$(this).data("id");
		var _action=$(this).data("action");

		swal({
			title: "<?=$client_lang['are_you_sure_msg']?>",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger btn_edit",
			cancelButtonClass: "btn-warning btn_edit",
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			closeOnConfirm: false,
			closeOnCancel: false,
			showLoaderOnConfirm: true
		},
		function(isConfirm) {
			if (isConfirm) {

				if(_action=='suspend'){

					$("#suspendModal").modal("show");
					swal.close();

					$("#suspendForm").submit(function(e){
						e.preventDefault();

						$.ajax({
							type:'post',
							url:'processData.php',
							dataType:'json',
							data : $("#suspendForm").serialize()+"&for_action="+_action+"&id="+_id+"&action=account_status",
							success:function(res){
								console.log(res);
								if(res.status=='1'){
									$("#suspendModal").modal("hide");
									swal({
										title: "Suspended", 
										text: "<?=$client_lang['user_account_suspend_msg']?>", 
										type: "success"
									},function() {
										location.reload();
									});
								}
								else if(res.status=='0'){
									alert(res.message);
									swal.close();
								}
							}
						});
					});

				}else{
					$.ajax({
						type:'post',
						url:'processData.php',
						dataType:'json',
						data:{for_action:_action,id:_id,'action':'account_status'},
						success:function(res){
							console.log(res);
							if(res.status=='1'){
								$("#suspendModal").modal("hide");
								swal({
									title: "Activated", 
									text: "<?=$client_lang['user_account_activated_msg']?>", 
									type: "success"
								},function() {
									location.reload();
								});
							}
							else if(res.status=='0'){
								alert(res.message);
								swal.close();
							}
						}
					});
				}

			}
			else{
				swal.close();
			}

		});
	});

	$(".filter").on("change",function(e){
		$("#filterForm *").filter(":input").each(function(){
			if ($(this).val() == '')
				$(this).prop("disabled", true);
		});

		$("#filterForm").submit();
	});

</script>