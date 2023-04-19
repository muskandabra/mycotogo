<?php
include_once("private/settings.php");
include_once("classes/User.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/clsSharetransfer.php");
?>

<!DOCTYPE html>
<?php 
$consumerObj= new Consumer();
$objTransfer = new ShareTransfer();

$consumer_fileno='';
$state_id='';
$companytype_id='';
$companyname='';
$companycontact='';
$companyworkphone='';
$companycellphone='';
$companyfax='';
$companyemail='';
$companyresaddress='';
$companymailingaddress='';
$companyrecordaddress='';
$code='';
$errormsg='';

$code = '';
if(isset($_GET['code']) && $_GET['code']!='')
{
	$code=$_GET['code'];
}

//$res=$consumerObj->generateRandomString();
if(isset($_POST['form1']) && $_POST['form1']!=''|| (isset($_POST['updatebutton']) && $_POST['updatebutton']=='Update'))
{
	$choice='';
	$objTransfer->transfer_id=$_POST['transfer_id'];
	$objTransfer->cert_no_issued_from = $_POST['fromnewCertificate'];
	$objTransfer->cert_no_issued_to = $_POST['tonewCertificate']; 
	$objTransfer->transfer_no = 	$_POST['transfer_no'];  
	$objTransfer->date = $_POST['transfer_date'];
	$objTransfer->from_userid = $_POST['from_userid'];
	$objTransfer->to_userid = $_POST['to_userid'];
	$objTransfer->consumershareclass=$_POST['ParalegalDirector1shareclass'];
	$objTransfer->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
	$objTransfer->consumersharetype=$_POST['ParalegalDirector1sharetype'];
	$objTransfer->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
	$objTransfer->consumershareright =$_POST['ParalegalDirector1sharerights'];
	$objTransfer->oldcertificate_no = $_POST['oldcertificate_no'];
	$objTransfer->no_of_shares = $_POST['tonoofshares'];
	$objTransfer->oldno_of_shares = $_POST['oldno_of_shares'];
	
	$objTransfer->updateTransrec();

	//die;
	//$id = '';
	if(isset($_GET['id']) && $_GET['id']!='')
	{
		$id=$_GET['id'];
	}


			
	
	print"<script language=javascript>window.location='".URL."sharetransferrec.php?id=".$id."&actionprocess=updated&code=".$code."'</script>";
		
			die;
		


	
}
if( isset($_POST['cancelbutton']) && $_POST['cancelbutton']=='Cancel')
{
		if(isset($_GET['id']) && $_GET['id']!='')
	{
		$id=$_GET['id'];
	}
	print"<script language=javascript>window.location='".URL."sharetransferrec.php?id=".$id."&code=".$code."'</script>";

}




if(isset($_GET['number']) && $_GET['number']!='')
{
	$transfer_id=base64_decode($_GET['number']);
}

if(isset($_GET['id']) && $_GET['id']!='')
{
	$id=$_GET['id'];
}



if($transfer_id !='')
{
	$objTransfer->transfer_id=$transfer_id;
	$query= $objTransfer->ShareTrnaferRecord();
	//$query=$consumerObj->selectConsumer();
	// $query=mysqli_query($res);
	if(mysqli_num_rows($query)>0)
	{
		$row=mysqli_fetch_object($query);	
		$transfer_id=$row->transfer_id;
		$fromname=$row->fromname;
		$toname=$row->toname;
		$fromcancelledCert = $row->cert_no_cancelled;
		$fromnewCertificate = $row->cert_no_issued_from;
		$fromnoofShares = $row->no_of_shares_from; 
		$tonewCertificate = $row->cert_no_issued_to; 
		$tonoofshares = $row->no_of_shares;
		$transfer_no = 	$row->transfer_no;  
		$transfer_date = $row->date;
		$from_userid = $row->from_userid; 
		$to_userid 	= $row->to_userid;

		$fromname 	= ($row->from_userid==0)?'Treasury':$row->fromname;
		$toname		=($row->to_userid==0)?'Treasury':$row->toname;

		$consumershareclass=$row->consumershareclass;
		$consumersharecolor=$row->consumersharecolor;
		$consumersharetype=$row->consumersharetype;
		$consumershareright=$row->consumershareright;
		$ParalegalDirector1cetificateprice=$row->consumerpricepershare;
	}
}
if($companytype_id!='')
{
	$value=$companytype_id;
}
else
{
	$value='test';
}

//if(isset($_POST['']))
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD --><head>
	<meta charset="utf-8" />
	<title> <?php echo SITE_NAME;?> | Sharetransfer Form</title>
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
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<style type="text/css">
div#tipDiv {
    font-size:11px; line-height:1.2;
    color:#000; background-color:#E1E5F1; 
    border:1px solid #667295; padding:4px;
    width:270px; 
}
.alert {
    background-color: #fcf8e3;
    border: 1px solid #fbeed5;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 8px 35px 8px 14px;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
}
</style>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed" >
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
				<div class="row-fluid peralegal">
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
						<div class="portlet box green">
								<div class="portlet-title">
									<div class="caption"><i class="icon-reorder"></i>Edit Share Transfer</div>
								</div>
							<div class="portlet-body form">
								<div class="inner-wrapper">
									<div class="form">
										<h3 class="required">Fields marked with * are required</h3>
										<?php  if ($errormsg!=''){ ?><div style="color:red; margin-left: 10px;"> <?php echo $errormsg;?> </div><?php } ?>
										<form action="#" id="form_sample_2" class="form-horizontal form1" method="POST">
										<input type="hidden" name="oldno_of_shares" value = "<?php echo $tonoofshares; ?>" />
										<input type="hidden" name="oldcertificate_no" value="<?php echo $tonewCertificate; ?>" />

						
										<?php if(isset($_GET['number']) && isset($_GET['number'])!='') {
											?> <input type="hidden"  name="Update" value="Update" />
											<?php }   ?>
											<div class="alert alert-error hide">
												<button class="close" data-dismiss="alert"></button>
												You have some form errors. Please check below.
											</div>
											<div class="alert alert-success hide">
												<button class="close" data-dismiss="alert"></button>
												Your form validation is successful!
											</div>
											

											<input type="hidden" name="from_userid" id="from_userid" value="<?php 
												echo $from_userid; ?>" class="input-m"/>
											<input type="hidden" name="to_userid" id="to_userid" value="<?php 
												echo $to_userid; ?>" class="input-m"/>
											<input type="hidden" name="transfer_id" id="transfer_id" value="<?php 
												echo $transfer_id; ?>" class="input-m"/>
												
											<div class="control-group forms">
												<label>Date<span class="required">*</span></label>
												<input type="date" name="transfer_date" id="transfer_date" value="<?php 
												echo $transfer_date; ?>" class="input-m"/>
											</div>
																								
							
										   
											<div class="forms control-group">
												<label>Transfer No.<span class="required"> *</span></label>
												<input type="text" value="<?php echo $transfer_no;?>" name="transfer_no" id="transfer_no" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Transferor</label>
												<input type="text" name="fromname" value="<?php if($fromname!=''){echo $fromname;}?>" id="fromname" class="input-m" readonly/>
											</div>
											
											<div class="forms control-group">
												<label>Cancelled Cerificate</label>
												<input type="text" name="fromcancelledCert" id="fromcancelledCert" value="<?php if($fromcancelledCert!=''){ echo $fromcancelledCert;} ?>" class="input-m" readonly/>
											</div>
											
											<div class="forms control-group">
												<label>New Cerificate No. (Transferor) <span class="required"> *</span> </label>
												<input type="text" name="fromnewCertificate" value="<?php if($fromnewCertificate!=''){ echo $fromnewCertificate;} ?>" id="fromnewCertificate" class="input-m"/>
											</div>

											<div class="forms control-group">
												<label>No. of Shares (Transferor) </label>
												<input type="text" name="fromnoofShares" value="<?php if($fromnoofShares!=''){ echo $fromnoofShares;} ?>" id="fromnoofShares" class="input-m" readonly/>
											</div>

											<div class="forms control-group">
												<label>Transferee</label>
												<input type="text" name="toname" value="<?php if($toname!=''){echo $toname;}?>" id="toname" class="input-m" readonly/>
											</div>
																			
											
											<div class="forms control-group">
												<label>New Cerificate No. (Transferee) <span class="required"> *</span> </label>
												<input type="text" name="tonewCertificate" value="<?php if($tonewCertificate!=''){ echo $tonewCertificate;} ?>" id="tonewCertificate" class="input-m"/>
											</div>

											<div class="forms control-group">
												<label>No. of Shares (Transferee) </label>
												<?php
												$mode = 'readonly';
												if ($from_userid == 0)
												{
													$mode = '';
												}
												?>
												<input type="text" name="tonoofshares" value="<?php if($tonoofshares!=''){ echo $tonoofshares;} ?>" id="tonoofshares" class="input-m" <?php echo $mode; ?>/>
											</div>

												<div class="control-group forms">
												<label>Enter Share Class<span class="required">*</span> </label>
												<select class="input-m selection" name="ParalegalDirector1shareclass" id="ParalegalDirector1shareclass">
												<option value="">--Share Class--</option>
													<?php 	$ShareClass=Paralegalinfo::getShareClass();
														foreach ($ShareClass as $classes){?>
													<option value="<?php echo $classes;  ?>"<?php if($consumershareclass!='' && $consumershareclass==$classes){echo "selected=selected";}?>><?php echo $classes;  ?></option>
													<?php } ?>
												</select>
											</div>
											
											<div class="control-group forms">
												<label>Enter Share Certificate Color<span class="required">*</span> </label>
												<select class="input-m selection" name="Paralegalsharecertificatecolor" id="Paralegalsharecertificatecolor">
												<option value="">--Share Certificate Color--</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Red'){ echo "Selected=Selected"; } ?> value="Red">Red</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Green'){ echo "Selected=Selected"; } ?> value="Green">Green</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Blue'){ echo "Selected=Selected"; } ?> value="Blue">Blue</option>
												</select>
											</div>
											
											<div class="forms control-group">
												<label>Share Type<span class="required">*</span> </label>
												<select class="input-m selection" name="ParalegalDirector1sharetype" id="ParalegalDirector1sharetype">
													<option value="">--Share Type--</option>
													<?php $ShareTypes=Paralegalinfo::getShareType();
															foreach($ShareTypes as $ShareType){?>
													<option value="<?php echo $ShareType;?>"<?php if($consumersharetype!='' && $consumersharetype==$ShareType){echo "selected=selected";}?>><?php echo $ShareType;?></option>
													<?php }?>
													
												</select>
											</div>
											
											<div class="forms control-group">
												<label>Share Rights<span class="required">*</span></label>
												<select class="input-m selection" name="ParalegalDirector1sharerights" id="ParalegalDirector1sharerights">
													<option value=""></option>
													<?php $ShareRights=Paralegalinfo::getShareRights();
												foreach($ShareRights as $ShareRight){?>
													<option value="<?php echo $ShareRight; ?>"<?php if($consumershareright!='' && $consumershareright==$ShareRight){echo "selected=selected";}?>><?php echo $ShareRight; ?></option>
												<?php } ?>
												</select>
											</div>

											<div class="forms control-group">
												<label>Price per Share<span class="required">*</span></label>
												<input type="text" name=""  class="dollarInput" id="" value="$"/>  
												<input type="text" name="ParalegalDirector1cetificateprice" id="ParalegalDirector1cetificateprice" class="input_share" value="<?php if($ParalegalDirector1cetificateprice!=''){ echo $ParalegalDirector1cetificateprice;} ?>"/>  
											</div>

											<input class="response" value="" type="hidden">
									
											<input type="submit" class="button1" name="updatebutton"  id="button1" value="Update"/>
											<input type="submit" class="button1" name="cancelbutton"  id="button2" value="Cancel"/>
										</form>
									</div>
								</div>
						<!-- END VALIDATION STATES-->
							</div>
							
						</div>
								<!-- END PAGE CONTENT-->   
					</div>
							<!-- END PAGE CONTAINER-->
				</div>
			</div>
						<!-- END PAGE -->  
		</div>
		
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
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/form-validation.js"></script> 
	<script src="assets/scripts/dw_tooltip_c.js"></script> 
	<script>
		jQuery(document).ready(function() {   
		   // initiate layout and plugins
		   App.init();
		   FormValidation.init();
		   valuesShareholder();
		});
		
	</script>
<script>
	$(document).ready(function(){
		$(".btn-navbar").click(function(){
			$(".page-container .page-sidebar.nav-collapse").removeAttr("style");
			$(".page-sidebar .page-sidebar-menu").slideToggle(500);
		});
	});
</script> 

	
 <script type="text/javascript">

 	function valuesShareholder()
	{
		from_userid = "<?php echo $from_userid; ?>";


			$("#ParalegalDirector1shareclass").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share holder class."
				}
			 });
			 $("#Paralegalsharecertificatecolor").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share certificate color."
				}
			 });
			 $("#ParalegalDirector1sharetype").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share type"
				}
			 });


			 $("#transfer_date").rules("add", {
				required:true,
				messages: {
				required: "Enter Date of Transfer."
				}
			 });

			 $("#ParalegalDirector1sharerights").rules("add", {
				required:true,
				messages: {
				required: "You did not enter sharerights."
				}
			 });
			 if (from_userid > 0)
			 {
			 // 	$("#fromnewCertificate").rules("add", {
				// required:true,
				// messages: {
				// required: "You did not enter cerificate no ."
				// }
			 // 	});
			 }			 
			 //  $("#tonewCertificate").rules("add", {
				// required:true,
				// messages: {
				// required: "You did not enter cerificate no ."
				// }
			 // });
			 $("#ParalegalDirector1cetificateprice").rules("add", {
				required:true,
				number:true,
				messages: {
				required: "You did not enter price of share",
				number:"only numeric value"
				}
			 });			
	}


</script>

	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>