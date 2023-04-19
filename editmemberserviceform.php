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
	$checkflag = 1;

	if (isset($_POST['member_doj']) && !empty($_POST['member_doj']))
	{
		if (!empty($_POST['member_dol']))
		{
			if ($_POST['member_doj'] > $_POST['member_dol'])
			{
				$checkflag = 0;
			}
		}
	}
	else
	{
		$checkflag = 0;
	}
	if ($checkflag == 1)
	{
		$choice='';
		$objTransfer->member_dol = '';
		$objTransfer->servicerec_id=$_POST['servicerec_id'];
		$objTransfer->member_doj=$_POST['member_doj'];
		$objTransfer->consumeruser_id=$_POST['consumeruser_id'];		
		if (isset($_POST['member_dol']) && !empty($_POST['member_dol']))
			$objTransfer->member_dol = $_POST['member_dol'];
		if (isset($_POST['ParalegalDirector1title']) && !empty($_POST['ParalegalDirector1title']))
			$objTransfer->consumerofficertitle = $_POST['ParalegalDirector1title'];
		if (isset($_POST['ParalegalOtherTitle']) && !empty($_POST['ParalegalOtherTitle']))
			$objTransfer->consumerotherofficertitle = $_POST['ParalegalOtherTitle']; 

		$objTransfer->updateServiceRec();

		//die;
		//$id = '';
		if(isset($_GET['id']) && $_GET['id']!='')
		{
			$id=$_GET['id'];
		}		
		print"<script language=javascript>window.location='".URL."memberservice_rec.php?id=".$id."&actionprocess=updated&code=".$code."'</script>";
			
				die;
	}
	else
	{
		$errormsg = 'Error in Dates, Appointment date sould be filled and less than Resignation date.';

	}
		


	
}
if( isset($_POST['cancelbutton']) && $_POST['cancelbutton']=='Cancel')
{
		if(isset($_GET['id']) && $_GET['id']!='')
	{
		$id=$_GET['id'];
	}
	print"<script language=javascript>window.location='".URL."memberservice_rec.php?id=".$id."&code=".$code."'</script>";

}




if(isset($_GET['number']) && $_GET['number']!='')
{
	$servicerec_id=base64_decode($_GET['number']);
}

if(isset($_GET['id']) && $_GET['id']!='')
{
	$id=$_GET['id'];
}



if($servicerec_id !='')
{
	$objTransfer->servicerec_id=$servicerec_id;
	$query= $objTransfer->MemberServiceRecord();
	//$query=$consumerObj->selectConsumer();
	// $query=mysqli_query($res);
	if(mysqli_num_rows($query)>0)
	{
		$row=mysqli_fetch_object($query);	

		//print_r($row);

		$servicerec_id=$row->servicerec_id;
		$member_designation=$row->member_designation;
		$member_doj=$row->member_doj;
		$member_dol = $row->member_dol;
		$consumerofficertitle = $row->consumerofficertitle;
		$consumerotherofficertitle = $row->consumerotherofficertitle; 
		$membername = $row->fromname; 
		$consumeruser_id = $row->consumeruser_id; 


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
	<title> <?php echo SITE_NAME;?> | Member Form</title>
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

<body class="page-header-fixed" onload="return values('<?php echo $value;?>');">
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
									<div class="caption"><i class="icon-reorder"></i>Edit Member Record</div>
								</div>
							<div class="portlet-body form">
								<div class="inner-wrapper">
									<div class="form">
										<h3 class="required">Fields marked with * are required</h3>
										<?php  if ($errormsg!=''){ ?><div style="color:red; margin-left: 10px;"> <?php echo $errormsg;?> </div><?php } ?>
										<form action="#" id="form_sample_2" class="form-horizontal form1" method="POST">

						
										<?php if(isset($_GET['number']) && isset($_GET['number'])!='') {
											?> <input type="hidden"  name="Update" value="Update" />
											<?php }   ?>
											<div class="alert alert-error hide">
												<button class="close" data-dismiss="alert"></button>
												You have some form errors. Please check below.
											</div>
											<!-- <div class="alert alert-success hide">
												<button class="close" data-dismiss="alert"></button>
												Your form validation is successful!
											</div> -->
											

								
											<input type="hidden" name="servicerec_id" id="servicerec_id" value="<?php 
												echo $servicerec_id; ?>" class="input-m"/>
											<input type="hidden" name="consumeruser_id" id="consumeruser_id" value="<?php 
												echo $consumeruser_id; ?>" class="input-m"/>

												

											<div class="control-group forms " >
													<label><b><?php echo ucfirst($membername).'  ('.ucfirst($member_designation).')'; ?></b></label>
											
											</div>

											<div class="control-group forms directordoj" >
													<label>Date of Joining<span class="required">*</span></label>
													<input type="date" name="member_doj" value="<?php echo $member_doj; ?>" id="member_doj" class="input-m"/>
											</div>
											<div class="control-group forms  directordol" >
													<label>Date of Resignation</label>
													<input type="date" name="member_dol" value="<?php echo $member_dol; ?>"  id="member_dol" class="input-m"/>
											</div>
											<?php
											if ($member_designation == 'officer')
											{
											?>
												<div class="control-group forms officerTitle">
													<label>Officer title<span class="required">*</span></label>
													<select class="input-m selection" name="ParalegalDirector1title" id="ParalegalDirector1title" onchange="return paralegalDirectorField(this.value);">
													<option value="">--Title--</option>
													<?php $OfficerTitles=Paralegalinfo::getOfficerTitle();
														foreach($OfficerTitles as $OfficerTitle) {?>
														<option value="<?php echo $OfficerTitle; ?>"<?php if($consumerofficertitle!='' && $consumerofficertitle==$OfficerTitle){echo "selected=selected";}?>><?php echo $OfficerTitle; ?></option>
														<?php }?>
													</select>
												</div>
												<?php
												$divstyle="display:none";
												if ($consumerofficertitle == 'Other (enter value below)')
												{
													$divstyle="";
												}
												?>
												<div class="control-group" id="otherTitle"  style="<?php echo $divstyle; ?>">
												<label></label>
												<input type="text" class="input-m " value="<?php if($consumerotherofficertitle!=''){ echo $consumerotherofficertitle;} ?>" name="ParalegalOtherTitle" id="ParalegalOtherTitle" />
												</div>
											<?php
											}
											?>																			
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
    $(document).ready(function(){
        $('input[type="radio"]').click(function(){
            if($(this).attr("value")=="NumberedCorporation"){
               $("#numberedCompanyText").show();
               $("#otherCompanyText").hide();
            }
			else
			{
				$("#numberedCompanyText").hide();
               $("#otherCompanyText").show();
			}
    
        });
    });
</script>
	<!-- END PAGE LEVEL STYLES -->    
	
 <script type="text/javascript">

 	function paralegalDirectorField(val)
	{
		if(val=='Other (enter value below)')
		{
			document.getElementById('otherTitle').style.display = 'block';
			 $("#ParalegalOtherTitle").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Cerificate no"
				}
			 });
		}
		else
		{
			$("#ParalegalOtherTitle").rules("remove");
			document.getElementById("ParalegalOtherTitle").value = "";
			document.getElementById('otherTitle').style.display = 'none';
		}
	}

	function valuesShareholder()
	{
			$("#member_doj").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Date of joining."
				}
			 });
				
	}







</script>

	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>