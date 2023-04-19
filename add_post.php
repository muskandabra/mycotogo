<?php include_once("private/settings.php");
include_once(PATH."classes/clsPost.php");
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title><?php echo SITE_NAME;?> | Add Post</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />
	<link rel="stylesheet" type="text/css" href="assets/plugins/chosen-bootstrap/chosen/chosen.css" />
    <link href="assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" />
	<link href="assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
</head>
<!-- END HEAD -->
<?php

$title = '';
$description = '';
$updated = '';
$created_at = '';

$objPost = new Post();

if(isset($_POST['submit']))
{
	if($_POST['title']!="" && $_POST['description']!=''  && $_POST['updated_at']!='' && $_POST['created_at']!='')
	{
	
			$objPost->title = $_POST['title'];
			$objPost->description = $_POST['description'];
			$objPost->updated=$_POST['updated_at'];
			$objPost->created_at=$_POST['created_at'];
			$objPost->addPost();
			
			echo "<script>alert('Data Added Successfully');
            window.location.href='manage_post.php';</script>";  
			//print"<script>window.location='manage_post.php'</script>";

	}
		
}

?>

<!-- BEGIN BODY -->
<body class="page-header-fixed">
	<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse navbar-fixed-top">
		<!-- BEGIN TOP NAVIGATION BAR -->
		<?php include(PATH."elements/header.php");?>
		<!-- END TOP NAVIGATION BAR -->
	</div>
	<!-- END HEADER -->
	<!-- BEGIN CONTAINER -->
	<div class="page-container row-fluid">
		<!-- BEGIN SIDEBAR -->
		<?php include(PATH."elements/left.php");?>
		<!-- END SIDEBAR -->
		<!-- BEGIN PAGE -->  
		<div class="page-content">
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN PAGE CONTAINER-->
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->   
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN STYLE CUSTOMIZER -->
						
						<!-- END BEGIN STYLE CUSTOMIZER -->     
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN VALIDATION STATES-->
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption"><i class="icon-edit"></i>Add Posts</div>
							</div>
							<div class="portlet-body form">
								<!-- BEGIN FORM-->
								<form action="#" id="form_sample_1" name="form_users" method="post" class="form-horizontal">
									<div class="alert alert-error hide" 
									<?php
										if ($errMessage!='')
											echo('style="display: block;">');
									?>>
										<button class="close" data-dismiss="alert"></button>
										You have some form errors. Please check below.
										<?php echo $errMessage;?>
									</div>
								
									
									<div class="control-group">
										<label class="control-label">Title<span class="required">*</span></label>
										<div class="controls">
											<input type="text" name="title" id="title" data-required="1"  class="span6 m-wrap"/>
										</div>
									</div>
									
									<div class="control-group">
										<label class="control-label">Description<span class="required">*</span></label>
										<div class="controls">
											<input type="text" name="description" id="" data-required="1"  class="span6 m-wrap"/>
										</div>
									</div>
									
                                        <div class="control-group">
										<label class="control-label">Updated At<span class="required">*</span></label>
										<div class="controls">
											<input type="date" name="updated_at" id="useremail" data-required="1"  class="span6 m-wrap"/>
										</div>
									</div>  

                                    <div class="control-group">
										<label class="control-label">Created At<span class="required">*</span></label>
										<div class="controls">
											<input type="date" name="created_at" id="useremail" data-required="1"  class="span6 m-wrap"/>
										</div>
									</div> 
								
									<div class="form-actions">
										<input type="submit" name="submit" class="btn blue" value="Save"/>

										
									</div>
                                    <div class="row-fluid">                                   
                                    </div>
                                </div>
								</form>
								<!-- END FORM-->
							</div>
						</div>
						<!-- END VALIDATION STATES-->
					</div>
				</div>
				
				
				<!-- END PAGE CONTENT-->         
			</div>
			<!-- END PAGE CONTAINER-->
		</div>
		<!-- END PAGE -->  
	</div>
	<!-- END CONTAINER -->
	<!-- BEGIN FOOTER -->
		<?php include(PATH."elements/footer.php");?>
	<!-- END FOOTER -->
	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
	<!-- BEGIN CORE PLUGINS -->
   <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/form-validation.js"></script> 
    <script src="assets/scripts/form-components.js"></script>
    <script src="assets/scripts/form-fileupload.js"></script>
	<!-- END PAGE LEVEL STYLES -->    
	<script>
		jQuery(document).ready(function() {   
			App.init();
			FormValidation.init();
			var test= $('#usertype_id').val();
			if(test!='' && test != undefined)
			{	
				var data=test.split(",");
				var userTypeValue=data[1];
				var splitValue=userTypeValue.split("-");
				var checkValue=splitValue[1];
				if(checkValue!='Internal')
				{
					$('#compnyinfo').show();
					$("#companyname").rules("add", {
						required:true,
					});
				
					$("#companyaddress").rules("add", {
						required:true,
					});
					// $("#fax").rules("add", {
						// required:true,
					// });
					$("#phone").rules("add", {
						required:true,
					});
				}
				
				else
				{
					$("#companyname").rules("remove");
					document.getElementById("companyname").value = "";
					$("#companyaddress").rules("remove");
					document.getElementById("companyaddress").value = "";
					//$("#fax").rules("remove");
					document.getElementById("fax").value = "";
					$("#phone").rules("remove");
					document.getElementById("phone").value = "";
					$('#compnyinfo').hide();
				}
			   // initiate layout and plugins
			}			
			//FormComponents.init();

			$(".showHidePassword").on('click', function(e) {
			    e.preventDefault();

			    // get input group of clicked link
			    var input_group = $(this).parent('.controls');


			    // find the input, within the input group
			    var input = input_group.find('input.span6');


			    // find the icon, within the input group
			    var icon = $(this).find('i');

			    // toggle field type
			    input.prop('type', input.attr("type") === "text" ? 'password' : 'text')
			
			    // toggle icon class
			    icon.toggleClass('fa-eye-slash fa-eye');
			 });

		});
		
		function usertypevalue(val)
		{
			//alert(val);
			if(val=='')
			{
				$('#compnyinfo').hide();
				$("#companyname").rules("remove");
				document.getElementById("companyname").value = "";
				$("#companyaddress").rules("remove");
				document.getElementById("companyaddress").value = "";
				//$("#fax").rules("remove");
				document.getElementById("fax").value = "";
				$("#phone").rules("remove");
				document.getElementById("phone").value = "";
			}
			var data=val.split(",");
			var userTypeValue=data[1];
			var splitValue=userTypeValue.split("-");
			var checkValue=splitValue[1];
			if(checkValue!='Internal')
			{
				$('#compnyinfo').show();
				$("#companyname").rules("add", {
					required:true,
				});
			
				$("#companyaddress").rules("add", {
					required:true,
				});
				// $("#fax").rules("add", {
					// required:true,
				// });
				$("#phone").rules("add", {
					required:true,
				});
			}
			else
			{
				$("#companyname").rules("remove");
				document.getElementById("companyname").value = "";
				$("#companyaddress").rules("remove");
				document.getElementById("companyaddress").value = "";
				$("#fax").rules("remove");
				document.getElementById("fax").value = "";
				$("#phone").rules("remove");
				document.getElementById("phone").value = "";
				$('#compnyinfo').hide();
			}
		}
	</script>
	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>