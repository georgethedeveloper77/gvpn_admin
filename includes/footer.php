<footer class="app-footer">
      <div class="row">
        <div class="col-xs-12">
          <div class="footer-copyright">Copyright © <?php echo date('Y');?> <a href="https://willdev.in/" target="_blank">Will_Dev</a>. All Rights Reserved.</div>
        </div>
      </div>
    </footer>
  </div>
</div>

<div class="modal fade" id="verifyUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">User Verification</h4>
      </div>
      <form method="post" id="verifyUserForm">
	      <div class="modal-body">
	      </div>
	      <div class="modal-footer">
	        <button type="submit" name="btn_reject" value="reject" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i> Reject</button>
	        <button type="submit" name="btn_approve" value="approve" class="btn btn-sm btn-success"><i class="fa fa-check-square-o"></i> Approve</button>
	      </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/vendor.js"></script> 
<script type="text/javascript" src="assets/js/app.js"></script>

<script type="text/javascript" src="assets/js/notify.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript" src="assets/sweetalert/sweetalert.min.js"></script>    

<script type="text/javascript">
	$(document).ready(function(e){
		
		var old_count = 0;
		var i = 0;

		$.ajax({
	      type:'post',
	      url:'processData.php',
	      dataType:'json',
	      data:{'action':'notify'},
	      success:function(data){
		      $(".notify_count").html(data.count);
		      $.each(data.content, function(index, item) {
		      	$(".dropdown-header").after(item);
		      });

		       $(".btn_verify").on("click",function(event){
					event.preventDefault();
					var _id=$(this).data("id");
					$("#verifyUser .modal-body").load("verification_page.php?id="+_id);
					$("#verifyUser").modal("show");
					$("li.dropdown-header").nextAll("li").remove();
					$.ajax({
				      type:'post',
				      url:'processData.php',
				      dataType:'json',
				      data:{'action':'openNotify',id:_id},
				      success:function(data){
					      $(".notify_count").html(data.count);
					      $.each(data.content, function(index, item) {
					      	$(".dropdown-header").after(item);
					      });
					    }
					});

				});

		    }
		});

		
		setInterval(function(){    
		$.ajax({
	      type:'post',
	      url:'processData.php',
	      dataType:'json',
	      data:{'action':'notify'},
	      success:function(data){
		        if (data.count > old_count)
		        { 
		        	$("li.dropdown-header").nextAll("li").remove();
		        	if (i == 0)
		        	{
		        		old_count = data.count;
		        		$(".notify_count").html(old_count);
						$.each(data.content, function(index, item) {
							$("li.dropdown-header").after(item);
						});
		        	} 
		            else
		            {
		            	old_count = data.count;
		            	$(".notify_count").html(data.count);
						$.each(data.content, function(index, item) {
							$("li.dropdown-header").after(item);
						});
		            }
		        }
		        else{
		        	$("li.dropdown-header").nextAll("li").remove();
		        	old_count = data.count;
	            	$(".notify_count").html(data.count);
					$.each(data.content, function(index, item) {
						$("li.dropdown-header").after(item);
					});
		        } 

		        i=1;
		    }
		});
		},20000);

		$("#verifyUserForm button").click(function(e){
			e.preventDefault();
			var perform = $(this).val();

			if(perform=='approve')
			{
				swal({
					title: "<?=$client_lang['are_you_sure_msg']?>",
					text: "Action: "+perform,
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
					if (isConfirm)
					{
						$("#verifyUserForm button[name='btn_approve']").attr("disabled", true);
						$("#verifyUserForm button[name='btn_reject']").attr("disabled", true);

						$.ajax({
							type:'post',
							url:'processData.php',
							data : $("#verifyUserForm").serialize()+"&perform="+perform,
							dataType:'json',
							success:function(res){
								if(res.status=='1'){
									location.reload();
								}
							}
						});
					}
					else {
						swal.close();
					}
				});
			}
			else if(perform=='reject'){
				$(".rejectReason").slideDown();
				$("#verifyUserForm button[name='btn_approve']").attr("disabled", true);
				$("#verifyUserForm button[name='btn_reject']").attr("disabled", true);

			}
		});
	
		

		$(".btn_verify").on("click",function(event){
			event.preventDefault();
			$("#verifyUserForm button[name='btn_approve']").attr("disabled", false);
			$("#verifyUserForm button[name='btn_reject']").attr("disabled", false);
			var _id=$(this).data("id");
			$("#verifyUser .modal-body").load("verification_page.php?id="+_id);
			$("#verifyUser").modal("show");
			$("li.dropdown-header").nextAll("li").remove();
			$.ajax({
		      type:'post',
		      url:'processData.php',
		      dataType:'json',
		      data:{'action':'openNotify',id:_id},
		      success:function(data){
			      $(".notify_count").html(data.count);
			      $.each(data.content, function(index, item) {
			      	$(".dropdown-header").after(item);
			      });
			    }
			});

		});

	});
</script>

<script>
	$("#checkall").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
</script> 
	
<script>
	$(function() {
		$( ".datepicker" ).datepicker({
			dateFormat:'dd-mm-yy',
			showAnim:'clip',
			setDate: new Date(),
			minDate: 0
		});
	});

	$(".loader").show();
	$(document).ready(function(){
		$(".loader").fadeOut("slow");

	    var ownVideos = $("iframe");
	    $.each(ownVideos, function (i, video) {                
	        var frameContent = $(video).contents().find('body').html();
	        if (frameContent) {
	            $(video).contents().find('body').html(frameContent.replace("autoplay", "0"));
	        }
	    });
	});


	function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    if($(".dropdown-li").hasClass("active")){
	    var _test='<?=(isset($active_page)) ? $active_page : ''; ?>';
	    $("."+_test).next(".cust-dropdown-container").show();
	    $("."+_test).find(".title").next("i").removeClass("fa-angle-right");
	    $("."+_test).find(".title").next("i").addClass("fa-angle-down");
	  }

	  $(document).ready(function(e){
	    var _flag=false;

	    
	    $(".dropdown-a").click(function(e){
	        
	      $(this).parents("ul").find(".cust-dropdown-container").slideUp();

	      $(this).parents("ul").find(".title").next("i").addClass("fa-angle-right");
	      $(this).parents("ul").find(".title").next("i").removeClass("fa-angle-down");

	      if($(this).parent("li").next(".cust-dropdown-container").css('display') !='none'){
	          $(this).parent("li").next(".cust-dropdown-container").slideUp();
	          $(this).find(".title").next("i").addClass("fa-angle-right");
	          $(this).find(".title").next("i").removeClass("fa-angle-down");
	      }else{
	        $(this).parent("li").next(".cust-dropdown-container").slideDown();
	        $(this).find(".title").next("i").removeClass("fa-angle-right");
	        $(this).find(".title").next("i").addClass("fa-angle-down");
	      }

	    });
	  });

</script>

<?php if(isset($_SESSION['msg'])){?>
  <script type="text/javascript">
    $('.notifyjs-corner').empty();
    $.notify(
      '<?php if(!empty($client_lang[$_SESSION['msg']])){ echo $client_lang[$_SESSION['msg']]; }else{ echo $_SESSION['msg']; } ?>',
      { position:"top center",className: '<?php if(isset($_SESSION['class'])){ echo $_SESSION['class']; }else{ echo 'success'; } ?>'}
    );
  </script>
<?php if(isset($_SESSION['class'])){ unset($_SESSION['class']); }?> 
<?php unset($_SESSION['msg']);}?> 

</body>
</html>

