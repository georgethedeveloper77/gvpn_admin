<?php  
$page_title="Contact List";
include("includes/header.php");
include("includes/connection.php");
require("includes/function.php");
require("language/language.php");
?>

<style type="text/css">
	.dataTables_wrapper .top{
		padding-top: 0px !important;
	}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="page_title_block">
				<div class="col-md-5 col-xs-12">
					<div class="page_title"><?=$page_title?></div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="card-body mrg_bottom" style="padding: 0px"> 
				<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px">
					<li role="presentation" class="active"><a href="#subject_list" aria-controls="comments" role="tab" data-toggle="tab"><i class="fa fa-comments"></i> Subjects List</a></li>
					<li role="presentation"><a href="#contact_list" aria-controls="contact_list" role="tab" data-toggle="tab"><i class="fa fa-envelope"></i> Contact Forms</a></li>
				</ul>
				<div class="col-md-12 mrg-top">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="subject_list">
							<div> <a href="contact_subject.php?add=true&redirect=<?=$redirectUrl?>" class="btn btn-primary btn-sm">Add Subject</a></div>
							<div class="clearfix"></div>
							<br/>
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th width="100">Sr No.</th>
										<th>Subject Title</th>
										<th class="cat_action_list" style="width:60px">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$sql="SELECT * FROM willdev_contact_sub ORDER BY id DESC";
									$res=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
									$no=1;
									while ($row=mysqli_fetch_assoc($res)) {
										?>
										<tr>
											<td><?=$no++?></td>
											<td><?=$row['title']?></td>
											<td nowrap="">
												<a href="contact_subject.php?edit_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-primary btn_edit"><i class="fa fa-edit"></i></a>
												<a href="javascript:void(0)" data-id="<?=$row['id']?>" class="btn btn-danger btn_cust btn_delete_subject"><i class="fa fa-trash"></i></a>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>

						<!-- for contact list tab -->
						<div role="tabpanel" class="tab-pane" id="contact_list">
							<button class="btn btn-danger btn_cust btn_delete_all" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>

							<table class="datatable table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th style="width:40px">
											<div class="checkbox" style="margin: 0px">
												<input type="checkbox" name="checkall" id="checkall" value="">
												<label for="checkall"></label>
											</div>
										</th>	
										<th>Name</th>
										<th>Email</th>		
										<th>Subject</th>		
										<th>Message</th>
										<th>Date</th>
										<th class="cat_action_list" style="width:60px">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php

									$users_qry="SELECT willdev_contact_list.*, sub.`title` FROM willdev_contact_list, willdev_contact_sub sub WHERE willdev_contact_list.`contact_subject`=sub.`id` ORDER BY willdev_contact_list.`id` DESC";  

									$users_result=mysqli_query($mysqli,$users_qry);
									$i=0;
									while($users_row=mysqli_fetch_array($users_result))
									{

										?>
										<tr>
											<td>  
												<div class="checkbox">
													<input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $users_row['id']; ?>" class="post_ids">
													<label for="checkbox<?php echo $i;?>"></label>
												</div>
											</td>	
											<td><?php echo ucwords($users_row['contact_name']);?></td>
											<td><?php echo $users_row['contact_email'];?></td>
											<td><?php echo $users_row['title'];?></td>
											<td><?php echo $users_row['contact_msg'];?></td>
											<td nowrap=""><?php echo date('d-m-Y',$users_row['created_at']);?></td>
											<td> 
												<a href="javascript:void(0)" data-id="<?php echo $users_row['id'];?>" class="btn btn-danger btn_delete" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a></td>
											</tr>
											<?php
											$i++;
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

	<?php include("includes/footer.php");?>       

	<script type="text/javascript">
		$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
			localStorage.setItem('activeTab', $(e.target).attr('href'));

		});

		var activeTab = localStorage.getItem('activeTab');

		if(activeTab){
			$('.nav-tabs a[href="' + activeTab + '"]').tab('show');
		}


  // for multiple deletes
  $(".btn_delete_all").click(function(e){
  	var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });

  	if(_ids!='')
  	{
  		swal({
  			title: "<?=$client_lang['are_you_sure_msg']?>",
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
  					data:{ids:_ids,'action':'removeContact'},
  					success:function(res){
  						console.log(res);
  						if(res.status=='1'){
  							swal({
                  title: "<?=$client_lang['multi_action_success_lbl']?>", 
                  text: "<?=$client_lang['12']?>", 
                  type: "success"
                },function() {
                  location.reload();
                });
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
  	}
  	else{
      swal({title: 'Sorry no records selected!', type: 'info'});
  	}
  });

  $(".btn_delete").click(function(e){
  	e.preventDefault();
  	var _ids=$(this).data("id");
  	swal({
  		title: "<?=$client_lang['are_you_sure_msg']?>",
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
  				data:{ids:_ids,'action':'removeContact'},
  				success:function(res)
          {
  					if(res.status=='1'){
              swal({
                title: "<?=$client_lang['multi_action_success_lbl']?>", 
                text: "<?=$client_lang['12']?>", 
                type: "success"
              },function() {
                location.reload();
              });
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


  $(".btn_delete_subject").on("click", function(e) {

    e.preventDefault();

    var _id = $(this).data("id");
    var _table = 'willdev_contact_sub';

    swal({
      title: "<?=$client_lang['are_you_sure_msg']?>",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      cancelButtonClass: "btn-warning",
      confirmButtonText: "Yes!",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: false,
      showLoaderOnConfirm: true
    },
    function(isConfirm) {
      if (isConfirm) {

        $.ajax({
          type: 'post',
          url: 'processData.php',
          dataType: 'json',
          data: {id: _id, for_action: 'delete', table: _table, 'action': 'multi_action'},
          success: function(res)
          {
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
      } else {
        swal.close();
      }

    });
  });

</script>