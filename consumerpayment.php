<?php
include_once("private/settings.php");

include_once(PATH."classes/clsTransaction.php");

include_once(PATH."classes/clsConsumer.php");

include_once(PATH."classes/User.php");



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

	<link rel="stylesheet" type="text/css" href="assets/plugins/chosen-bootstrap/chosen/chosen.css" />



	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

</head>

<!-- END HEAD -->

<?php

$objTransaction= new Transaction();

$objConsumer= new Consumer();

$objUser=new User();

$payment_method='';

$payment_description='';

$payment_status='';

$errMessage='';

if(isset($_GET['code']) && $_GET['code']!='')

{

	$mode="ADD";

	$code=base64_decode($_GET['code']);

	$consumer_id=$objConsumer->getconsumer_id($code);

	$objConsumer->consumer_id=$consumer_id;

	$select=$objConsumer->selectConsumer();

	//$res=mysqli_query($dbconnection,$select);

	$rows=mysqli_fetch_object($select);

	$state_id=$rows->state_id;

}



if(isset($_GET['edit']) && $_GET['edit']!="")

{

	$mode="EDIT";

	$code=base64_decode($_GET['edit']);

	$consumer_id=$objConsumer->getconsumer_id($code);

	$objConsumer->consumer_id=$consumer_id;

	$select=$objConsumer->selectConsumer();

	$res=mysqli_query($select);

	$rows=mysqli_fetch_object($res);

	$state_id=$rows->state_id;

	$objTransaction->consumer_id=$consumer_id;

	$res = $objTransaction->selectTransaction();

	if(mysqli_num_rows($res)>0)

	{

		

		$row=mysqli_fetch_object($res);

		$payment_method= $row->payment_method;

		$payment_description= $row->payment_description;

		$payment_status= $row->payment_status;

	}

	

}

if(isset($_POST['add_transaction']) && $_POST['add_transaction']!='')

{

	$objTransaction->usertype=$_SESSION['usertype'];

	$objTransaction->user_id=$_SESSION['sessuserid'];

	$objConsumer->usertype=$_SESSION['usertype'];

	$objConsumer->user_id=$_SESSION['sessuserid'];

	$objTransaction->consumer_id=$_POST['consumer_id'];

	$objTransaction->payment_method=$_POST['payment_method'];

	$objTransaction->payment_description=$_POST['payment_description'];

	$objTransaction->payment_status=$_POST['payment_status'];
	$objTransaction->consumer_fileno = '';

	$consumer_fileno=$objTransaction->addTransaction();

	$objConsumer->consumer_id=$_POST['consumer_id'];

	$row=$objConsumer->getCompanyDetails();

	//$company_email=$row->companyemail;

	//$objUser->consumerUserEmail=$company_email;

	if($_POST['payment_status']==0)	

	{

		$objConsumer->consumerfilestatus_id=3;

		$objConsumer->consumer_fileno=$consumer_fileno;

		$objConsumer->updateConsumer();

	}

	else

	{

		if($_POST['payment_status']==2)

		{

			$objConsumer->consumerfilestatus_id=3;

			$objConsumer->statusfor=Declined;

			$objConsumer->consumer_fileno=$consumer_fileno;

			$objConsumer->updateConsumer();

		}

		else

		{

			$objConsumer->consumerfilestatus_id=4;

			$objConsumer->consumer_fileno=$consumer_fileno;

			$objConsumer->updateConsumer();

		}

	}

	//die;

	print"<script language=javascript>window.location='consumer.php?no=".$_GET['code']."'</script>";

}



if(isset($_POST['edit_transaction']) && $_POST['edit_transaction']!='')

{

	$objTransaction->usertype=$_SESSION['usertype'];

	$objTransaction->user_id=$_SESSION['sessuserid'];

	$objConsumer->usertype=$_SESSION['usertype'];

	$objConsumer->user_id=$_SESSION['sessuserid'];

	$objTransaction->consumer_id=$_POST['consumer_id'];

	$objTransaction->payment_method=$_POST['payment_method'];

	$objTransaction->payment_description=$_POST['payment_description'];

	$objTransaction->payment_status=$_POST['payment_status'];

	$consumer_fileno=$objTransaction->editTransaction();

	$objConsumer->consumer_id=$_POST['consumer_id'];

	$row=$objConsumer->getCompanyDetails();

	//$company_email=$row->companyemail;

	//$objUser->consumerUserEmail=$company_email;

	if($_POST['payment_status']==0)	

	{

		$objConsumer->consumerfilestatus_id=3;

		$objConsumer->consumer_fileno=$consumer_fileno;

		$objConsumer->updateConsumer();

	}

	else

	{

		if($_POST['payment_status']==2)

		{

			$objConsumer->consumerfilestatus_id=3;

			$objConsumer->statusfor=Declined;

			$objConsumer->consumer_fileno=$consumer_fileno;

			$objConsumer->updateConsumer();

		}

		else

		{

			$objConsumer->consumerfilestatus_id=4;

			$objConsumer->consumer_fileno=$consumer_fileno;

			$objConsumer->updateConsumer();

		}

	}
	//die;

	

	print"<script language=javascript>window.location='consumer.php?no=".$_GET['code']."'</script>";

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

			<!-- BEGIN PAGE CONTAINER-->

			<div class="container-fluid">

				<div class="row-fluid">

					<div class="span12">

						

					</div>

				</div>

				<!-- BEGIN PAGE CONTENT-->

				<div class="row-fluid">

					<div class="span12">

						<div class="portlet box blue">

							<div class="portlet-title">

								<div class="caption"><i class="icon-reorder"></i>Payment</div>

							</div>

							<div class="portlet-body form">

								<!-- BEGIN FORM-->

								<form action="" id="form_sample_2" name="form_users" method="post" class="form-horizontal">

								<?php if($mode=="ADD")

								{?>

									<input type="hidden" name="add_transaction" value="add_transaction">

								<?php }

								if($mode=="EDIT")

								{?>

									<input type="hidden" name="edit_transaction" value="edit_transaction">

								<?php }?>

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

										<label class="control-label">Payment Type<span class="required">*</span></label>

										<div class="controls">

											<label class="radio line">

												<input type="radio" name="payment_method" value="PayPal" <?php if($payment_method=='PayPal') { echo "checked";} else {echo "checked";}?> />

												PayPal

											</label>

											<label class="radio line">

												<input type="radio" name="payment_method" value="Email" <?php if($payment_method=='Email') { echo "checked";} ?> />

												Email

											</label>  

											<label class="radio line">

												<input type="radio" name="payment_method" value="SMS/Phone" <?php if($payment_method=='SMS/Phone') { echo "checked";} ?> />

												SMS/Phone

											</label>  

										</div>

									</div>

									<div class="control-group">

										<label class="control-label">Payment Description<span class="required">*</span></label>

										<div class="controls">

											<textarea name="payment_description" class="large m-wrap" rows="3"><?php echo $payment_description;?></textarea>

										</div>

									</div>

									<div class="control-group">

										<label class="control-label">Payment Status<span class="required">*</span></label>

										<div class="controls">

											<label class="radio line1">

												<input type="radio" name="payment_status" value="1" <?php if($payment_status==1) { echo "checked";} ?> checked  />

												Success

											</label>

											<label class="radio line1">

												<input type="radio" name="payment_status" value="0" <?php if($payment_status==0) { echo "checked";}  ?>  />

												Pending

											</label>  

											<label class="radio line1">

												<input type="radio" name="payment_status" value="2" <?php if($payment_status==2) { echo "checked";} ?>  />

												Declined

											</label> 

										</div>

									</div>

									<input type="hidden" name="consumer_id" value="<?php echo $consumer_id;?>"/>

									<input type="hidden" name="state_id" value="<?php echo $state_id;?>"/>

									<div class="form-actions">

										<button type="submit" class="btn blue">Next</button>

										<a href="consumer.php"><button type="button" class="btn">Cancel</button></a>

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

    <script type="text/javascript" src="assets/plugins/ckeditor/ckeditor.js"></script>

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

	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>

	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

    <script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script> 

	<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

    

	

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-validation.js"></script> 

    <script src="assets/scripts/form-components.js"></script>

   

	<!-- END PAGE LEVEL STYLES -->    

	<script>

		jQuery(document).ready(function() {   

			App.init();

			FormValidation.init();

		});

	</script>

	<!-- END JAVASCRIPTS -->   

</body>

<!-- END BODY -->

</html>