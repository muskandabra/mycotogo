<?php include_once("private/settings.php");

include_once("classes/User.php");

?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?> | Profile</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<link rel="stylesheet" href="assets/plugins/ajaxImageUpload/style.css" />

	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>

	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>

	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<link href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.css" rel="stylesheet" type="text/css" />

	<link href="assets/plugins/chosen-bootstrap/chosen/chosen.css" rel="stylesheet" type="text/css" />

	<link href="assets/css/pages/profile.css" rel="stylesheet" type="text/css" />

	<!-- END PAGE LEVEL STYLES -->

	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />

	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />

	<link href="assets/css/pages/login-soft.css" rel="stylesheet" type="text/css"/>

	<link rel="shortcut icon" href="favicon.ico" />

</head>

<script>

function changeStatus()

	{

		document.getElementById("form_status_blank").submit();

	}

</script>

<!-- END HEAD -->

<?php

$user_id = 0;

$firstname = '';

$lastname = '';

$contactno = '';

$registrationDate = '';

$zipcode='';

$errMessage = '';

$companyname='';

$useremail='';

$imagePath='';

$fax = '';

$companyaddress='';
$edit 			= 	0;
	$username 		= 	'';
	$firstname 		= 	'';
	$tblName		=	'tbl_user';
	$usernameExist 	= 	0;
	$user_id		=	'';
	$user_code		=	'';
	$lastname		=	'';
	$address		=	'';
	$state			=	'';
	$zipcode		=	'';
	$contactno		=	'';
	$usertype_id	=	'';
	$password		=	'';



if(isset($_POST['actionprocess']) && $_POST['actionprocess']=='changepassword_do')

{

	$objUser = new User();

	$objUser->user_id=$_SESSION['sessuserid'];

	$objUser->password=$_POST['password'];

	$objUser->confirmPassword=$_POST['confirmpassword'];

	$returnVal = $objUser->changePassword();

	if($returnVal == 0)

	{

		$errMessage='Password Mismatch';

		print "<script>window.location='profile.php?err=PasswordError#tab_1_3'</script>";

	}

}



if(isset($_POST['actionprocess']) && $_POST['actionprocess']=='editusers_do')

{



	

	if(trim($_POST['firstname'])!='' && $_POST['lastname']!='' && $_POST['useremail']!='' && $_POST['contactno']!='' )

	{

		$objUser = new User();

		$objUser->user_id = $_SESSION['sessuserid'];

		$objUser->firstname = $_POST['firstname'];

		$objUser->lastname = $_POST['lastname'];

		$objUser->companyaddress=$_POST['companyaddress'];

		$objUser->contactno=$_POST['contactno'];

		$objUser->fax = $_POST['fax'];

		$objUser->useremail = $_POST['useremail'];

		$objUser->companyname = $_POST['companyname'];

		$objUser->editUserProfile();

		$_SESSION['sessfirstname']=$_POST['firstname'];

		$_SESSION['sesslastname']=$_POST['lastname'];

		print "<script>window.location='profile.php#tab_1_3'</script>";

	}

	else

	{

		$errMessage="[Invalid Data]";

		print "<script>window.location='profile.php#tab_1_3'</script>";

		

	}

}



if(isset($_SESSION['sessuserid']) && $_SESSION['sessrights']!="")

{

	$obj = new User();

	$obj->user_id = $_SESSION['sessuserid'];

	$resselect= $obj->selectUser(); 

	(mysqli_num_rows($resselect));

	if(mysqli_num_rows($resselect)>0)

	{

		$row=mysqli_fetch_object($resselect);

		$user_id = $row->user_id;

		$firstname = $row->firstname;

		$lastname = $row->lastname;

		$contactno = $row->contactno;

		$registrationDate = $row->registrationDate;

		$zipcode=$row->zipcode;

		$companyname=$row->companyname ;

		$useremail=$row->useremail;

		$companyaddress=$row->companyaddress;

		$fax=$row->fax;

	}

	else

	{

		print "<script>window.location='index.php?msg=err'</script>";

	}

}

else

{

	print "<script>window.location='index.php?msg=err'</script>";

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

		<!-- BEGIN TOP NAVIGATION BAR -->

		<?php include(PATH."elements/left.php");?>

		<!-- END TOP NAVIGATION BAR -->

		<!-- END SIDEBAR MENU -->

		</div>

		<!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content">

			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<div id="portlet-config" class="modal hide">

				<div class="modal-header">

					<button data-dismiss="modal" class="close" type="button"></button>

					<h3>portlet Settings</h3>

				</div>

				<div class="modal-body">

					<p>Here will be a configuration form</p>

				</div>

			</div>

			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<!-- BEGIN PAGE CONTAINER-->

			<div class="container-fluid">

				<!-- BEGIN PAGE HEADER-->

				<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN PAGE TITLE & BREADCRUMB-->

						<h3 class="page-title">

						 Profile <small></small>

						</h3>

						<!--

						<ul class="breadcrumb">

							<li>

								<i class="icon-home"></i>

								<a href="index.php">Home</a> 

								<i class="icon-angle-right"></i>

							</li>

							<li>

								<a href="#">Extra</a>

								<i class="icon-angle-right"></i>

							</li>

							<li><a href="#">User Profile</a></li>

						</ul>

						-->

						<!-- END PAGE TITLE & BREADCRUMB-->

					</div>

				</div>

				<!-- END PAGE HEADER-->

				<!-- BEGIN PAGE CONTENT-->

				<div class="row-fluid profile">

					<div class="span12">

						<!--BEGIN TABS-->

						<div class="tabbable tabbable-custom tabbable-full-width">

							<ul class="nav nav-tabs">

								

								<li class="active"><a href="#tab_1_2" data-toggle="tab">Profile Info</a></li>

								<li><a href="#tab_1_3" data-toggle="tab">Account</a></li>

							</ul>

							

							<div class="tab-content">

							

								<!--end tab-pane-->

								<div class="tab-pane profile-classic row-fluid active" id="tab_1_2">

									<div class="span2" style="display:none;"><img src="<?php echo (URL.'admin/assets/img/userImages/'.$imagePath);?>" alt="" /></div>

									<ul class="unstyled span10">

										<li><span>User Name:</span> <?php echo $useremail; ?></li>

										<li><span>First Name:</span> <?php echo $firstname; ?></li>

										<li><span>Last Name:</span> <?php echo $lastname; ?></li>

										<li><span>Company Name:</span> <?php echo $companyname; ?></li>

										<li><span>Email:</span> <a href="#"><?php echo $useremail; ?></a></li>

										<li><span>Company Address:</span> <?php echo $companyaddress; ?></li>

										<li><span>Mobile Number:</span> <?php echo $contactno; ?></li>

										<li><span>Fax:</span> <?php echo $fax; ?></li>

									</ul>

								</div>

								<!--tab_1_2-->

								<div class="tab-pane row-fluid profile-account" id="tab_1_3">

									<div class="row-fluid">

										<div class="span12">

											<div class="span3">

												<ul class="ver-inline-menu tabbable margin-bottom-10">

													<li class="<?php echo ($errMessage=='Password Mismatch')?'':'active';?>">

														<a data-toggle="tab" href="#tab_1-1">

														<i class="icon-cog"></i> 

														Personal info

														</a> 

														<span class="after"></span>                                    

													</li>

													<li class="<?php echo($errMessage=='Password Mismatch')?'active':'';?>"><a data-toggle="tab" href="#tab_3-3"><i class="icon-lock"></i> Change Password</a></li>

													

												</ul>

											</div>

											<div class="span9">

											

												<div class="tab-content">

													<div id="tab_1-1" class="tab-pane <?php echo ($errMessage=='Password Mismatch')?'':'active';?>">

														<div style="height: auto;" id="accordion1-1" class="accordion collapse">

															<form action="profile.php" id="form_profile" name="form_profile" method="post">

															<div class="alert alert-error hide" 

																<?php

																	if ($errMessage=='[Invalid Data]')

																		echo('style="display: block;"');

																?>>

																	<button style="float: right;" class="close" data-dismiss="alert"></button>

																	You have some form errors. Please check below.

																	<?php echo $errMessage;?>

																</div>

																<input type="hidden" name="actionprocess" id="actionprocess" value="editusers_do">

																<label class="control-label">First Name<span class="required">*</span></label>

																<input type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>" class="m-wrap span8" />

																<label class="control-label">Last Name<span class="required">*</span></label>

																<input type="text" name="lastname" value="<?php echo $lastname;?>"  class="m-wrap span8" />

																<label class="control-label">User Email<span class="required">*</span></label>

																<input type="text" name="useremail" value="<?php echo $useremail;?>"  class="m-wrap span8" />

																<label class="control-label">Mobile Number<span class="required">*</span></label>

																<input type="text" name="contactno" value="<?php echo $contactno;?>"  class="m-wrap span8" />

																 

																<label class="control-label">Company Name</label>

																<input type="text" name="companyname" value="<?php echo $companyname;?>"  class="m-wrap span8" />

																<label class="control-label">Company Address</label>

																<input type="text" name="companyaddress" value="<?php echo $companyaddress;?>"  class="m-wrap span8" />

																<label class="control-label">Fax</label>

																<input type="text" name="fax" value="<?php echo $fax;?>"  class="m-wrap span8" />

																<div class="submit-btn">

																	<button type="submit" class="btn blue">Save</button>

																	<button type="button" class="btn" onClick="window.location='profile.php'">Cancel</button>

																</div>

																

															</form>

														</div>

													</div>

													

													<div id="tab_3-3" class="tab-pane <?php echo ($errMessage=='Password Mismatch')?'active':'';?>">

														<div style="height: auto;" id="accordion3-3" class="accordion collapse">

															<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN VALIDATION STATES-->

						<div class="portlet box blue">

							<div class="portlet-title">

								<div class="caption"><i class="icon-reorder"></i>Change Password</div>

							</div>

							<div class="portlet-body form">

								<!-- BEGIN FORM-->

								<form action="" id="frmChangePassword" name="frmChangePassword" method="post" class="form-horizontal">

									<input type="hidden" name="user_id" id="user_id" value="<?php echo ($user_id!=0?$user_id:'0');?>">

									<input type="hidden" name="actionprocess" id="actionprocess" value="changepassword_do">

									<div class="alert alert-error hide" 

									<?php

										if ($errMessage=='Password Mismatch')

											echo('style="display: block;">');

									?>>

										<button style="float:right;" class="close" data-dismiss="alert"></button>

										You have some form errors. Please check below.

										<?php echo $errMessage;?>

									</div>

									<div class="control-group">

										<label class="control-label">Old Password<span class="required">*</span></label>

										<div class="controls">

											<input type="text" name="password" data-required="1" value="" class="span6 m-wrap"/>
											<a class = "showHidePassword" href=""><i class="fa fa-eye" aria-hidden="true"></i> </a>

										</div>

									</div>  

  									

									

										<div class="control-group">

													<label class="control-label">New Password<span class="required">*</span></label>

													<div class="controls">

														<input type="password" class="span6 m-wrap" name="newpassword" id="newpassword"/>
														<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

														<span class="help-inline">Enter your password</span>

													</div>

												</div>

												<div class="control-group">

													<label class="control-label">Confirm Password<span class="required">*</span></label>

													<div class="controls">

														<input type="password" class="span6 m-wrap" name="confirmpassword"/>
														<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

														<span class="help-inline">Confirm your password</span>

													</div>

												</div>

                                    

									<div class="form-actions">

										<button type="submit" class="btn blue">Save</button>

										<button type="button" class="btn" onClick="window.location='profile.php'">Cancel</button>

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

									</div>

								</div>

								<!--end tab-pane-->

								

								

								<!--end tab-pane-->

							</div>

						</div>

						<!--END TABS-->

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

	<!-- BEGIN FOOTER -->

		<?php include(PATH."elements/footer.php");?>

	<!-- END FOOTER -->

	<!-- END FOOTER -->

	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

	<!-- BEGIN CORE PLUGINS -->

	<script src="assets/plugins/jquery-1.10.1.min.js" type="text/javascript"></script>

	<script src="assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>

	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

	<script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      

	<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	<!--[if lt IE 9]>

	<script src="assets/plugins/excanvas.min.js"></script>

	<script src="assets/plugins/respond.min.js"></script>  

	<![endif]-->   

	<script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>

	<script src="assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>  

	<script src="assets/plugins/jquery.cookie.min.js" type="text/javascript"></script>

	<script src="assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript" ></script>

	<!-- END CORE PLUGINS -->

	<!-- BEGIN PAGE LEVEL PLUGINS -->

	<script src="assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>

	<script type="text/javascript" src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script> 

	<script src="assets/scripts/form-validation.js"></script> 

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		   FormValidation.init();

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

		

		function imageUpdate() {

			var request = $.ajax({

			  url: "script.php",

			  type: "POST",

			  data: { id : menuId },

			  dataType: "html"

			});

			 

			request.done(function( msg ) {

			  $( "#log" ).html( msg );

			});

			 

			request.fail(function( jqXHR, textStatus ) {

			  alert( "Request failed: " + textStatus );

			});

		}

	</script>

	<!-- END JAVASCRIPTS -->

</body>

<!-- END BODY -->

</html>