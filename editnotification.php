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

$notification_id=base64_decode($_GET['code']);

$objNotification->notification_id=$notification_id;

$res=$objNotification->getNotificationDetails();

$rows=mysqli_fetch_object($res);

$notificationdate=$rows->notificationdate;

$explodeDate=explode('-',$notificationdate);

//print_r($explodeDate);

$year=$explodeDate[0];

$month=$explodeDate[1];

$day=$explodeDate[2];

//$notificationdate=$month.'/'.$day.'/'.$year;

//m/d/Y

//2014-11-02 

$notificationdescription=$rows->notificationdescription;

$notificationcreatedby=$rows->notificationcreatedby;

$created_id=$rows->created_id;

$consumer_id=$rows->consumer_id;



if(isset($_POST['actionprocess']) && $_POST['actionprocess']!='')

{

	$objConsumer= new Consumer();

	$objConsumer->consumer_id=$consumer_id;

	$res=$objConsumer->getCompanyDetails();

	@$consumer_fileno=$res->consumer_fileno; //gives error but not affecting functionality noted by bimal

	$objNotification->consumer_fileno=@$consumer_fileno;

	$objNotification->user_id=$_SESSION['sessuserid'];

	$objNotification->usertype=$_SESSION['usertype'];

	$objNotification->notification_id=$_POST['notification_id'];

	if($_POST['notificationstatus']=='Deleted')

	{

		$objNotification->deleteNotification();

		print"<script language=javascript>window.location='notifications.php?msg=del'</script>";

	}

	else

	{

		if(isset($_POST['notificationstatus']) && $_POST['notificationstatus']!='')

		{

			$objNotification->notificationstatus=$_POST['notificationstatus'];

		}

		$explodeddatefordatabase=explode('/',$_POST['notificationdate']);

		$dbmonth=$explodeddatefordatabase['0'];

		//$dbdate=$explodeddatefordatabase['1'];

		//$dbyear=$explodeddatefordatabase['2'];

		//$explodeddatefordatabase=$dbyear.'-'.$dbmonth.'-'.$dbdate;

		$objNotification->notificationdescription=$_POST['notificationdescription'];

		//$objNotification->notificationdate=$explodeddatefordatabase;

		$objNotification->notificationdate	=	date('Y-m-d',strtotime($_POST['notificationdate']));

		$objNotification->Edit='table';

		$objNotification->update_notification();
		//die();

		print"<script language=javascript>window.location='notifications.php?msg=edit'</script>";

	}


}

$res=$objNotification->getNotificationDetails();

$rows=mysqli_fetch_object($res);

$notificationdate=$rows->notificationdate;

$explodeDate=explode('-',$notificationdate);

//print_r($explodeDate);

$year=$explodeDate[0];

$month=$explodeDate[1];

$day=$explodeDate[2];

//$notificationdate=$month.'/'.$day.'/'.$year;

//m/d/Y

//2014-11-02 

$notificationdescription=$rows->notificationdescription;

$notificationcreatedby=$rows->notificationcreatedby;

$created_id=$rows->created_id;

$consumer_id=$rows->consumer_id;

$notificationstatus=$rows->notificationstatus;

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

							<div class="caption"><i class="icon-reorder"></i>Edit Reminder</div>

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

															<label class="control-label">Date</label>

															<div class="controls" >

																<!-- <a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.adminForm.Trading_date);return false;" hidefocus>

																	<img class="PopcalTrigger" align="absmiddle" src="img/calander.png" border="0" alt="">

																</a> -->

																<input class="input_account datepicker" type="text" data-date-format='yy-mm-dd' id="Trading_date" name="notificationdate"  required value="<?php echo $notificationdate;?>"/>
															</div>

														</div>

													</div>

													<div class="remain-one-half right">

														<div class="forms control-group">

															<label>Description</label>

															<div class="controls">

																<textarea class="large m-wrap" name="notificationdescription" rows="3"><?php echo $notificationdescription; ?></textarea>

															</div>

														</div>

													</div>

													<div class="remain-one-half right">

														<div class="forms control-group">

															<label>Status</label>

															<div class="controls"class="input m">

																<select name="notificationstatus" id="notification_status">

																	<option <?php if($notificationstatus=='pending'){ echo "selected=selected";} ?> value="pending">Pending</option>

																	<option <?php if($notificationstatus=='completed'){ echo "selected=selected";} ?> value="completed">Completed</option>

																	<option <?php if($notificationstatus=='deleted'){ echo "selected=selected";} ?> value="Deleted">Delete</option>

																</select>

															</div>

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

											<a href="notifications.php" class="button1 grey">Cancel</a>

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

	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-datepicker.js"></script>                
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-select.js"></script>

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-validation.js"></script> 

<script>




curdate = "<?php echo date('Y,m,d',strtotime($notificationdate)); ?>";
$(".datepicker").datepicker({dateFormat: 'yyyy-mm-dd' });

//alert(curdate);
curdate1 = new Date(curdate);
//alert(curdate1);
//$('.dateselector').datepicker("setDate", new Date(2008,9,03) );
$(".datepicker").datepicker();
$(".datepicker").datepicker("setDate", curdate1);
$(".datepicker").datepicker({dateFormat:'yyyy-mm-dd'});

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