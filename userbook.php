<?php include_once("private/settings.php");

include_once("classes/clsNotification.php");

include_once("classes/User.php");

include_once("classes/clsConsumer.php");

include_once("classes/clsDocument.php");

$consumerView = 0;
$noticationView = 0;


?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?> | Admin Dashboard</title>

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

	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />

	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />

		<link href="assets/plugins/chosen-bootstrap/chosen/chosen.css" rel="stylesheet" type="text/css" />

	<link href="assets/css/pages/profile.css" rel="stylesheet" type="text/css" />

	<!-- END PAGE LEVEL STYLES -->

	<link href="assets/css/pages/login-soft.css" rel="stylesheet" type="text/css"/>

	<link rel="shortcut icon" href="favicon.ico" />

	

</head>

<style>

.slimScrollDiv {

    height: 300px !important;

}

.tab-content > .active, .pill-content > .active {

    display: block;

    height: 300px !important;

}

.scroller {

    height: 300px !important;

}

.view_more {

  

	display: block;

    margin: 0 auto;

    text-align: center;

    text-transform: capitalize;

    width: 28%;

}

</style>

<?php  $user_id='';

$notificationObj= new Notification();

$documentObj= new Document();

$user_id=$_SESSION['sessuserid'];

$notificationObj->user_id = $user_id;

$documentObj->user_id = $user_id;





$objConsumer = new Consumer();

$objConsumer->created_user_id=$user_id;

$queryleads = $objConsumer->selectConsumer();

// $queryleads=mysql_query($res);

$consumerCount=mysqli_num_rows($queryleads);



if($_SESSION['usertype']=='Consumer')

{

	$res=$notificationObj->showUserNotification();

}

else

{

	$res=$notificationObj->showNotification();

}

$reminderCount= mysqli_num_rows($res);

$obj = new User();

$obj->user_id = $_SESSION['sessuserid'];

$resselect= $obj->selectUser(); 



if(mysqli_num_rows($resselect)>0)

{

	$row=mysqli_fetch_object($resselect);

	$welcomeStatus=$row->isWelcome;

}

//echo $welcomeStatus;

	if(isset($_POST['proceed']) && $_POST['proceed']!='')

	{

		$update=mysql_query("UPDATE tbl_user SET isWelcome =1 WHERE user_id='".$user_id."'");

		print "<script language=javascript>window.location='dashboard.php'</script>";

	}

	

?>

<!-- END HEAD -->

<!-- BEGIN BODY -->

<body class="page-header-fixed">

	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<!-- BEGIN TOP NAVIGATION BAR -->

	<?php include_once("elements/header.php");?>	

	<!-- END TOP NAVIGATION BAR -->

	</div>

	<!-- END HEADER -->

	<!-- BEGIN CONTAINER -->

	<?php if($welcomeStatus==0){ 

	

?> 

	

	<div class="page-container row-fluid">

		<!-- BEGIN SIDEBAR -->

		<?php //include("elements/left.php");?><!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content">

			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			

			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<!-- BEGIN PAGE CONTAINER-->        

			<div class="container-fluid">

				<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN STYLE CUSTOMIZER -->

						<!-- END BEGIN STYLE CUSTOMIZER --> 

						<!-- BEGIN PAGE TITLE & BREADCRUMB-->

						

					</div>

				</div>

				<div class="row-fluid">

					<div class="portlet box green">

								<div class="portlet-title">

									<div class="caption"><i class="icon-reorder"></i>Welcome To Mycotogo </div>

									<div class="tools">

										<a href="javascript:;" class="collapse"></a>

										<a href="#portlet-config" data-toggle="modal" class="config"></a>

										<a href="javascript:;" class="reload"></a>

										<a href="javascript:;" class="remove"></a>

									</div>

								</div>

						<div class="portlet-body form">

							<div class="inner-wrapper">

							<form method="POST">

								<div class="form">

									<span>

										Your new company is registered and all of your corporate registry documents are filed in your digital record book. 

										Next Steps:	</br>

										1. We recommend taking the short video tour: <a href="videotour.php" target="_blank">http://mycotogo.com/login/videotour</a> which introduces you to the features included with your digital record book.	</br>

										2. Review your "next steps" checklist here: <a href="nextstep.php" target="_blank">http://mycotogo.com/login/nextsteps</a>. We have taken care of most of your year one paperwork but the checklist will detail some important next steps including:	

										Registering your business with CRA (Canada) or IRS (USA)</br>



										Municipal Licensing</br>



										WCB</br>



										And so much more... 	</br>

										3. Manage your Profile by clicking open the "profile" link. This area contains your billing account information, username and password, contact email and allows you to grant user access for additional account users. </br>	

										4. Stay connected! We welcome your feedback and are always happy to answer your questions. Periodically you will receive emails from us asking you to login and update important information regarding your company. We will also be notifying you when we conduct system upgrades and add more resources to the "help" section of your account. </br>	



										You can contact us anytime as follows:	Billing inquiries: billing@mycotogo.com</br>

										General inquiries: myco@mycotogo.com</br>

										Toll Free: 1-888-362-5025 ext 1 (Canada) ext 2 (USA) </br>



										Thank you and best wishes as you pursue your new venture!	</br>

									</span>

								</div>

								<div class="forms control-group">

									<input type="submit" name="proceed" class="btn green" value="Next"  />

								</div>

							</form>

								</div>

						<!-- END VALIDATION STATES-->

							</div>

						</div>

					

				</div>

			</div>		<!-- END PAGE CONTAINER-->

		</div>

		<!-- END PAGE -->

	</div>

	<?php  }else{?>

	<div class="page-container row-fluid">

		<!-- BEGIN SIDEBAR -->

		<?php // include("elements/left.php");?><!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content my-briefcase-header">

			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			

			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<!-- BEGIN PAGE CONTAINER-->        

			<div class="container-fluid">

				

				<div class="row-fluid">


				<?php if($consumerView==1)

				{ 
					

					if($_SESSION['usertype']!='Consumer')

					{ ?>

						<div class="span3 responsive" data-tablet="span6" data-desktop="span3">

							<div class="dashboard-stat blue">

								<div class="visual">

									<i class="icon-group"></i>

								</div>

								<div class="details">

									<div class="number">

										<?php echo $consumerCount; ?>

									</div>

									<div class="desc">                           

										Total Consumers

									</div>

								</div>

								<a class="more" href="consumer.php">

								View more <i class="m-icon-swapright m-icon-white"></i>

								</a>                 

							</div>

						</div>

						<?php

					} 

				}?>

					<?php if($noticationView==1)

					{?>

						<div class="span3 responsive" data-tablet="span6" data-desktop="span3">

							<div class="dashboard-stat green">

								<div class="visual">

									<i class="icon-bell"></i>

								</div>

								<div class="details">

									<div class="number"><?php echo $reminderCount; ?></div>

									<div class="desc">Total Reminders</div>

								</div>

								<a class="more" href="notifications.php">

								View more <i class="m-icon-swapright m-icon-white"></i>

								</a>                 

							</div>

						</div>

					<?php }?>

				</div>

			<?php if($_SESSION['usertype']=='Consumer')

					{

						?>

				<div class="row-fluid">

					<div class="span12">

						<ul class="breadcrumb allbriefcase-text">

							<li>

								<i class="icon-briefcase"></i>

								<a href="userbook.php">My Briefcase</a> 

								<i class="icon-angle-right"></i>

							</li>

							

						</ul>

						<!-- END PAGE TITLE & BREADCRUMB-->

					</div>

				</div>

				<!-- END PAGE HEADER-->

				<!-- BEGIN PAGE CONTENT-->

				<div class="tiles">

				<?php 	$res=$documentObj->getTemplateConsumerByUser();

						if(mysqli_num_rows($res)>0)

						{

							while($row=mysqli_fetch_object($res))

							{

								$documentObj->consumer_id=$row->consumer_id;

								$result=$documentObj->getTemplateFoldersCompanyByUser();

								if(mysqli_num_rows($result)>0)

								{

									while($fetchdata=mysqli_fetch_object($result))

									{

										?>

										<div class="row-fluid book-online">

											<div class="tile double selected bg-yellow my-briefcase">

												<div class="corner"></div>

												<div class="check"></div>

												<div class="tile-body">

												<h4><strong><?php echo $fetchdata->bookname; ?></strong></h4>

													<h4>

													<?php $company_name=rtrim($fetchdata->companyname,',');  ?>

													<?php echo $company_name; ?></h4>

													

													<p><?php echo $fetchdata->Description;?></p>

													<p><?php echo $fetchdata->createddate ?></p>

												</div>

												<div class="tile-object">

													<div class="name">

														<i class="icon-folder-open"></i>

													</div>

													<a class="view_more" href="showbook.php?n=<?php echo base64_encode($row->consumer_id); ?> & u=<?php echo base64_encode($fetchdata->document_id);?>">VIEW MORE</a>

													<div class="number">

														<?php

															$documentObj->document_id=$fetchdata->document_id;

															$rows=$documentObj->getTemplateFilesByUser();

															echo mysqli_num_rows($rows);

														?>

													</div>

												</div>

												<a class="more" href="notifications.php">

													View more <i class="m-icon-swapright m-icon-white"></i>

												</a> 

											</div>

										</div>

										<?php

									}

								}

							}

						}

						?>

				</div>

				<br>

				<!-- END PAGE CONTENT-->

				<?php } ?>

			</div>

			<!-- END PAGE CONTAINER--> 

		</div>

		

		<!-- END PAGE -->

	</div>

	<?php } ?>

	<!-- END CONTAINER -->

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

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/table-managed.js"></script>     

	<script>

		jQuery(document).ready(function() {       

		   App.init();

		   TableManaged.init();

		});

	</script>

</body>

<!-- END BODY -->

</html>