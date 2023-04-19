<?php
include_once("private/settings.php");

include_once("classes/clsNotification.php");

include_once(PATH."classes/clsConsumer.php");

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]--><head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?>| Reminder</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	

	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>

	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>

	<link href="style.css" rel="stylesheet" type="text/css"/>

	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />

	<link rel="stylesheet" type="text/css" href="assets/plugins/chosen-bootstrap/chosen/chosen.css" />

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

</head>

<!-- BEGIN BODY -->

<?php

$objNotification= new Notification();

$notification_template_id=base64_decode($_GET['id']);







if(isset($_POST['actionprocess']) && $_POST['actionprocess']!='')

{

	$objNotification->template_title=$_POST['notificationName'];

	$objNotification->template_description= $_POST['notificationDes'];

	$objNotification->user_id=$_SESSION['sessuserid'];

	$objNotification->usertype=$_SESSION['usertype'];

	$objNotification->notification_template_id=$_POST['notification_template_id'];

	$objNotification->update_notificationTemplate();

	print "<script>window.location='templates.php'</script>";

	die;

}



$objNotification->notification_template_id=$notification_template_id;

$res=$objNotification->selectNotificationTemplate();

$rows=mysqli_fetch_object($res);

$template_title=$rows->template_title;

$template_description=$rows->template_description;



?>

<body class="page-header-fixed additional-reg">

	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<?php include_once("elements/header.php");?>

	</div>

	<!-- END HEADER -->

	<!-- BEGIN CONTAINER -->

	<div class="page-container row-fluid form-section">

		<?php include("elements/left.php");?>

		<!-- END TOP NAVIGATION BAR -->

		<!-- END SIDEBAR MENU -->

	</div>

		<!-- BEGIN PAGE -->  

	<div class="page-content">

		<!-- BEGIN PAGE CONTAINER-->

		<div class="container-fluid">

			<div class="row-fluid">

				<div class="span12">

				</div>

			</div>

			<div class="row-fluid">

				<div class="span12">

					<!-- BEGIN VALIDATION STATES-->

					<div class="portlet box green">

						<div class="portlet-title">

							<div class="caption"><i class="icon-reorder"></i>Edit Template</div>

							<div class="tools">

								<a href="javascript:;" class="collapse"></a>

								<a href="#portlet-config" data-toggle="modal" class="config"></a>

								<a href="javascript:;" class="reload"></a>

								<a href="javascript:;" class="remove"></a>

							</div>

						</div>

						<div class="portlet-body form reg-additional">

							<div class="inner-wrapper">

								<div class="form">

									<form  class="form-horizontal form1" method="POST" action=""  name="adminForm" onsubmit="return reminderFormValidation();">

										<input type="hidden" name="shareholders" value="shareholders"/>

										<div class="alert alert-error" id="reminder_error" style="display:none;">

											<button class="close" data-dismiss="alert"></button>

												You have some form errors. Please select status.

										</div>

										<div class="alert alert-success hide">

											<button class="close" data-dismiss="alert"></button>

											Your form validation is successful!

										</div>											

										<div id="test">

											<div class="portlet box  reminder" >

												<div class="portlet-body" >

													<div class="date_Status">

														<div class="control-group date-section">

															<label class="control-label">Title</label>

															<div class="controls" >

																

																<input class="input_account" type="text" id="notificationName" name="notificationName" required value="<?php echo $template_title;?>"/>

																

															</div>

														</div>

													</div>

													<div class="remain-one-half right">

														<div class="forms control-group">

															<label>Description</label>

															<div class="controls">

																<textarea class="large m-wrap" name="notificationDes" rows="3"><?php echo $template_description; ?></textarea>

															</div>

														</div>

													</div>

												</div>

											</div>

										</div>

										<input type="hidden" value ="<?php echo $notification_template_id;?>" name="notification_template_id"/>

										<input type="hidden" name="actionprocess" value="UpdateTemplate_do"/>	

										<div class="forms control-group reminder-btn">

											<input type="submit" name="Save" class="button1"  value="Save"  />

											<a href="templates.php" class="button1 grey">Cancel</a>

										</div>

									</form>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div><!-- END PAGE CONTENT-->         

		</div>	<!-- END PAGE CONTAINER-->

	</div>

	<!-- BEGIN FOOTER -->

	<div class="footer">

		<div class="footer-inner">

			<?php echo FOOTER_NAME;?>

		</div>

		<div class="footer-tools">

			<span class="go-top">

			<i class="icon-angle-up"></i>

			</span>

		</div>

	</div>

	<!-- END FOOTER -->

	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

	<!-- BEGIN CORE PLUGINS -->

	<script src="<?php echo URL;?>assets/plugins/jquery-1.10.1.min.js" type="text/javascript"></script>

	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

	<script src="<?php echo URL;?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      

	<script src="<?php echo URL;?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-validation.js"></script> 

<script>

	$(document).ready(function(){

		$(".btn-navbar").click(function(){

			$(".page-container .page-sidebar.nav-collapse").removeAttr("style");

			$(".page-sidebar .page-sidebar-menu").slideToggle(500);

		});

	});

</script>  	

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		  FormValidation.init();

		});

	</script>

	<!-- END JAVASCRIPTS -->   

	<iframe width=172px height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"> </iframe>

</body>

<!-- END BODY -->

</html>