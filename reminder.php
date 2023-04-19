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

	<script>

		function showNeedToPostpondDiv($pStatus)

		{

			if($pStatus=='pending')

			{

				document.getElementById('needToPospond').style.display='block';

			}

			else

			{

				document.getElementById('needToPospond').style.display='none';

			}

			

		}

		function reminderFormValidation()

		{

			if(document.getElementById('notification_status').value=='')

			{

				document.getElementById('reminder_error').style.display='block';

				return false;

			}

		}

	</script>

</head>

<!-- BEGIN BODY -->

<?php

$objNotification= new Notification();

$notification_id=base64_decode($_GET['code']);

$objNotification->notification_id=$notification_id;

$res=$objNotification->getNotificationDetails();

$rows=mysqli_fetch_object($res);

$notificationdate=$rows->notificationdate;

$notificationdescription=$rows->notificationdescription;

$notificationcreatedby=$rows->notificationcreatedby;

$created_id=$rows->created_id;

$consumer_id=$rows->consumer_id;





if(isset($_POST['actionprocess']) && $_POST['actionprocess']!='')

{

	$objConsumer= new Consumer();

	$objConsumer->consumer_id=$consumer_id;

	$res=$objConsumer->getCompanyDetails();

	$consumer_fileno=$res->consumer_fileno;

	$objNotification->consumer_fileno=$consumer_fileno;

	$objNotification->user_id=$_SESSION['sessuserid'];

	$objNotification->usertype=$_SESSION['usertype'];

	if($_POST['notificationstatus']=='pending')

	{

		

		$objNotification->notificationstatus='completed';

		$objNotification->update_notification();

		$objNotification->notificationcreatedby=$_POST['notificationcreatedby'];

		$objNotification->created_id=$_POST['created_id'];

		$objNotification->consumer_id=$_POST['consumer_id'];

		$objNotification->notificationdate=$_POST['notificationdate'];

		$objNotification->notificationdescription=$_POST['notificationdescription'];

		$objNotification->parent_id=$_POST['notification_id'];

		$objNotification->notificationstatus=$_POST['notificationstatus'];

		$objNotification->add_notification();

		

	}

	else

	{

	

		$objNotification->notification_id=$_POST['notification_id'];

		$objNotification->notificationstatus=$_POST['notificationstatus'];

		$objNotification->update_notification();

	}



}

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

							<div class="caption"><i class="icon-reorder"></i>Reminder Details</div>

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

												<?php 

												$res1=$objNotification->getNotificationDetails();

												while($row=mysqli_fetch_object($res1))

												{?>

													<div class="portlet-body">

														<div class="remainder-width">

															<div class="reminder-group">

																<label class="control-label">Date:</label>

																<label  name="reminder_date" class="control"><?php echo $row->notificationdate?></label>

															</div>

															<div class="reminder-description">

																<p><?php echo $row->notificationdescription;?></p>

															</div>	

														</div>

													</div>

												<?php } ?>

												<div class="portlet-body" >

													<div class="date_Status">

														<div class="control-group date-section">

															<label class="control-label">Date</label>

															<div class="controls" >

																<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.adminForm.Trading_date);return false;" hidefocus>

																	<img class="PopcalTrigger" align="absmiddle" src="img/calander.png" border="0" alt="">

																</a>

																<input class="input_account" type="text" id="Trading_date" name="notificationdate" required value="<?php echo date("m/d/Y");?>"/>

																

															</div>

														</div>

													</div>

													<div class="notification_status">

														<div class="forms control-group">

															<label>Status</label>

															<div class="controls"class="input m">

																<select name="notificationstatus" onchange="return showNeedToPostpondDiv(this.value);" id="notification_status">

																	<option value="">--Select Status--</option>

																	<option value="pending">Pending</option>

																	<option value="completed">Completed</option>

																</select>

															</div>

														</div>

													</div>

													<div class="forms control-group" id="needToPospond" style="display:none;">

														<label>Need to Postpond</label>

														<div class="controls_textarea">

															<textarea name="notificationdescription"class="textarea_m-wrap" rows="5"></textarea>

														</div>

													</div>

												</div>

											</div>

										</div>

										<input type="hidden" value ="<?php echo $notification_id;?>" name="notification_id"/>

										<input type="hidden" name="actionprocess" value="addNotification_do"/>	

										<input type="hidden" value ="<?php echo $notificationcreatedby;?>" name="notificationcreatedby"/>

										<input type="hidden" value ="<?php echo $created_id;?>" name="created_id"/>

										<input type="hidden" value ="<?php echo $consumer_id;?>" name="consumer_id"/>

										<div class="forms control-group reminder-btn">

											<input type="submit" name="Save" class="button1"  value="Save"  />

											<a href="consumer.php" class="button1 grey">Cancel</a>

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