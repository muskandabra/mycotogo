<?php
include_once("private/settings.php");
include_once("classes/User.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/Module.php");
include_once(PATH."classes/clsProvince.php");
include_once(PATH."includes/accessRights/manageConsumers.php");

if($consumerAdd!=1 || $consumerEdit!=1)
{
	print "<script language=javascript>window.location='index.php'</script>";
}?>

<!DOCTYPE html>
<?php 

$consumerObj= new Consumer();
$provinceObj= new Province();
$userObj = new User();
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
$Existerror = 0;
$workspace=0;

if(isset($_GET['workspace']) && $_GET['workspace']=='1')
{
	$workspace = 1;
}
$parameter = '';


//$res=$consumerObj->generateRandomString();
if(isset($_POST['form1']) && $_POST['form1']!=''|| (isset($_POST['updatebutton']) && ($_POST['updatebutton']=='Update' || $_POST['updatebutton']=='Next' )))
{
	$choice='';
	foreach($_POST['Paralegal1Namechoices'] as $choices)
	{
		//$choice=$choice.'{'.$choices.'};';
		$choice=$choice.$choices.',';
	}
	$con =	explode('-',$_POST['Paralegal1CellPhone']);
	if(count($con)>1)
	{
		$cellcontact	=	'+'.$con['0'].$con['1'].$con['2'].$con['3'];
	}
	else
	{
		$cellcontact	=	$_POST['Paralegal1CellPhone'];
	}

	$consumerObj->created_user_id=$_SESSION['sessuserid'];
	$consumerObj->consumer_fileno=$_POST['Paralegal1FileNumber'];
	$consumerObj->jarea_id=$_POST['Paralegal1Jurisdiction'];
	$consumerObj->companytype_id=$_POST['service'];
	$consumerObj->companyname=$choice;
	$consumerObj->companycontact=$_POST['Paralegal1CompanyContact'];
	$consumerObj->companyworkphone=$_POST['Paralegal1WorkPhone'];
	$consumerObj->companycellphone=$cellcontact;
	$consumerObj->companyfax=$_POST['Paralegal1fax'];
	$userObj->useremail=$_POST['Paralegal1email'];
	$consumerObj->companyemail=$_POST['Paralegal1email'];
	//$userObj->companyaddress=$_POST['Paralegal1RegisteredAddress'];
	$consumerObj->companyreg_address=$_POST['Paralegal1RegisteredAddress'];	
	$consumerObj->companymailingaddress=$_POST['Paralegal1RecordsAddress'];
	$consumerObj->companyrecordaddress=$_POST['Paralegal1MailingAddress'];
	$consumerObj->usertype=$_SESSION['usertype'];
	if(isset($_POST['Update']) && $_POST['Update']!='')
	{
		$consumer_id=$consumerObj->getconsumer_id($_POST['Paralegal1FileNumber']);
		$user_id=$consumerObj->getuser_id($_POST['Paralegal1email']);
		if($user_id!='')
			$consumerObj->user_id=$user_id;
			
		$consumerObj->consumer_id=$consumer_id;
		
		
		if($consumerObj->checkUserEmail()==0)
		{
			if($user_id!='')
			{
				$userObj->user_id=$user_id;
				$userObj->editUser();
			}
			else
			{
				$userObj->mailSent='no';				$userObj->usertype_id=7;
				$user_id = $userObj->addUser();
				$consumerObj->user_id=$user_id;
			}
			$consumerObj->user_id=$user_id;
			$consumerObj->updateConsumer();
			$abc=base64_encode($_POST['Paralegal1FileNumber']);
			
			if(isset($_POST['updatebutton']) &&$_POST['updatebutton']=='Update')
			{
				if ($workspace == 1)
				{
					$parameter = "&workspace=1";
				}
				print"<script language=javascript>window.location='".URL."consumer.php?success=success&no=".$abc.$parameter."'</script>";
			}
			else
			{
				print"<script language=javascript>window.location='".URL."addconsumerform2.php?code=".$abc."'</script>";
			}
			die;
		}
		else
		{
			
			if($_POST['continueCreate']!='')
			{
					$consumerObj->consumer_fileno='';
					$consumerObj->companyemail=$_POST['Paralegal1email'];
					$getCompanyDetail = $consumerObj->getCompanyDetails();
					$getCompanyDetail = mysqli_fetch_object($getCompanyDetail);
					
					$consumerObj->user_id = $getCompanyDetail->uid;
					$consumerObj->consumer_fileno=$_POST['Paralegal1FileNumber'];
					$consumerObj->updateConsumer();
					$userObj->user_id=$user_id;
					$userObj->editUser();
					$abc=base64_encode($_POST['Paralegal1FileNumber']);
					if(isset($_POST['updatebutton']) && ($_POST['updatebutton']=='Update' || $_POST['updatebutton']=='Next'))
					{
						if (isset($_GET['task']) && $_GET['task'] == 'finish')
						{
							$consumerObj->consumer_id=$consumer_id;						
							$resDirector = $consumerObj->showDirector();
							//print_r($resDirector);
							// die;
							if(mysqli_num_rows($resDirector)>0)
							{ 
								$directorcount=1;
								while($rowDirector=mysqli_fetch_object($resDirector))
								{ 																								
									echo  "<script language=javascript>window.location='".URL."addconsumerform2.php?task=finish&id=".$rowDirector->consumeruser_id."&action=edit_".$directorcount."&code=".base64_encode( $_POST['Paralegal1FileNumber'])."'</script>";									
								}
								exit;
							}
							else
							{
								print"<script language=javascript>window.location='".URL."addconsumerform2.php?task=finish&code=".$abc."'</script>";
							}

						}
						else
						{
							if ($workspace == 1)
							{
								$parameter = "&workspace=1";
							}
							print"<script language=javascript>window.location='".URL."consumer.php?success=success&no=".$abc.$parameter."'</script>";
						}
					}
					else
					{
						print"<script language=javascript>window.location='".URL."addconsumerform2.php?code=".$abc."'</script>";
					}
					die;
			}
			else
					if (isset($_GET['task']) && $_GET['task'] == 'finish')
						{
							$consumerObj->consumer_id=$consumer_id;						
							$resDirector = $consumerObj->showDirector();
							//print_r($resDirector);
							if(mysqli_num_rows($resDirector)>0)
							{ 
								$directorcount=1;								
								while($rowDirector=mysqli_fetch_object($resDirector))
								{ 																		
									print "<script language=javascript>window.location='".URL."addconsumerform2.php?task=finish&id=".$rowDirector->consumeruser_id."&action=edit_".$directorcount."&code=".base64_encode($_POST['Paralegal1FileNumber'])."'</script>";
									break;
								}
							}
							else
							{
								print"<script language=javascript>window.location='".URL."addconsumerform2.php?task=finish&code=".$abc."'</script>";
							}
							die;

						}

				$errormsg='User Already Exist with this Email Address.';
		}
	}
	else
	{
		if($consumerObj->isFound()==0)
		{
			if($consumerObj->checkUserEmail()==0)
			{
				$userObj->mailSent='no';				$userObj->usertype_id=7;
				$user_id = $userObj->addUser();
				$consumerObj->user_id=$user_id;
				$consumerObj->addConsumer();
				//echo $user_id;
				$abc=base64_encode($_POST['Paralegal1FileNumber']);
				print"<script language=javascript>window.location='".URL."addconsumerform2.php?code=".$abc."'</script>";
				die;
			}
			else
			{				
				if($_POST['continueCreate']!='')
				{					
					$consumerObj->consumer_fileno='';
					$consumerObj->companyemail=$_POST['Paralegal1email'];
					echo $user_id =	$consumerObj->getuser_id($_POST['Paralegal1email']);
					$consumerObj->user_id = $user_id;
					$consumerObj->consumer_fileno=$_POST['Paralegal1FileNumber'];
					$consumerObj->addConsumer();
					$abc=base64_encode($_POST['Paralegal1FileNumber']);
					print"<script language=javascript>window.location='".URL."addconsumerform2.php?code=".$abc."'</script>";
					die;
				}
				else
				$errormsg='User Already Exist with this Email Address.';
			}
		}
		else
		{
			$Existerror = 1;
			//$errormsg = '';
		}

	}
	
}
if(isset($_GET['code']) && $_GET['code']!='')
{
	$code=base64_decode($_GET['code']);
}
$consumer_id=$consumerObj->getconsumer_id($code);
if($consumer_id!='')
{
	$consumerObj->consumer_id=$consumer_id;
	$query=$consumerObj->selectConsumer();
	// $query=mysqli_query($res);
	if(mysqli_num_rows($query)>0)
	{
		$row=mysqli_fetch_object($query);	
		$consumer_fileno=$row->consumer_fileno;
		$state_id=$row->state_id;
		$companytype_id=$row->companytype_id;
		$companyname=$row->usercname;
		$companycontact=$row->companycontact;
		$companyworkphone=$row->companyworkphone;
		$companycellphone=$row->companycellphone;
		$companyfax=$row->companyfax;
		$companyemail=$row->useremail;
		$companyresaddress=is_null($row->companyreg_address)?'':$row->companyreg_address;
		$companymailingaddress=$row->companymailingaddress;
		$companyrecordaddress=$row->companyrecordaddress;
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
//print_R($_POST['Paralegal1Namechoices']);
if(isset($_POST['Paralegal1FileNumber']) && $_POST['Paralegal1FileNumber']!='')
{
		//print_r($_POST['Paralegal1Namechoices']);
		foreach($_POST['Paralegal1Namechoices'] as $choices)
		{
			$choices;
		}
		$consumer_fileno=$_POST['Paralegal1FileNumber'];
		$state_id=$_POST['Paralegal1Jurisdiction'];
		$companytype_id=$_POST['service'];
		$companyname=$choice;
		$companycontact=$_POST['Paralegal1CompanyContact'];
		$companyworkphone=$_POST['Paralegal1WorkPhone'];
		$companycellphone=$_POST['Paralegal1CellPhone'];
		$companyfax=$_POST['Paralegal1fax'];
		$companyemail=$_POST['Paralegal1email'];
		// $companyresaddress=$_POST['Paralegal1RegisteredAddress'];
		// $companymailingaddress=$_POST['Paralegal1RegisteredAddress'];
		// $companyrecordaddress=$_POST['Paralegal1MailingAddress'];

		$companyresaddress=$_POST['Paralegal1RegisteredAddress'];
		$companymailingaddress=$_POST['Paralegal1MailingAddress'];
		$companyrecordaddress=$_POST['Paralegal1RecordsAddress'];
}
//if(isset($_POST['']))
?>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD --><head>
	<meta charset="utf-8" />
	<title> <?php echo SITE_NAME;?> | Paralegal Form</title>
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
	<div class="page-container row-fluid form-section add_consumer_main">
		<?php include("elements/left.php");?>
		<!-- END TOP NAVIGATION BAR -->
		<!-- END SIDEBAR MENU -->
		</div>
		<!-- BEGIN PAGE -->  
		<div class="page-content add_consumer_sub">
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
									<div class="caption"><i class="icon-reorder"></i>Paralegal Form Submission Page 1</div>
								</div>
							<div class="portlet-body form">
								<div class="inner-wrapper">
									<div class="form">
										<h3 class="required">Fields marked with * are required</h3>
										<?php  if ($errormsg!=''){ ?> <div style="color:red; margin-left: 10px;"> 
										<?php echo $errormsg;?> </div> <?php } ?>
										<form action="#" id="form_sample_2" class="form-horizontal form1" method="POST">
										<input type="hidden" name="continueCreate" id="continueCreate" value="">
										<?php if(isset($_GET['code']) && isset($_GET['code'])!='') { ?> 
											<input type="hidden"  name="Update" value="Update" />
											<?php } else { ?>
											 <input type="hidden" name="add"  value="Add" />
											<?php } ?>
											<div class="alert alert-error hide">
												<button class="close" data-dismiss="alert"></button>
												You have some form errors. Please check below.
											</div>
											<div class="alert alert-success hide">
												<button class="close" data-dismiss="alert"></button>
												Your form validation is successful!
											</div>
											<?php
											if ($Existerror)
											{
												$class = 'error';
												$message = 'The file '.$_POST['Paralegal1FileNumber'].' is already in use';
											}
											else
											{
												$class = '';
												$message = '';
											}
											?>																								
											<div class="control-group forms <?php echo $class; ?>">
												<label>File Number<span class="required">*</span><span class="required showTip L1">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>Assign a client file # to your file.  Enter up to 10 digits and/or letters to identify your file.  All files require a unique file#.  
															Example: BCD-5112  File numbers are unique to every firm.</p>
														</span>
													</span>
												</label>

												<input type="text" name="Paralegal1FileNumber" id="Paralegal1FileNumber" value="<?php 
												if($consumer_fileno!=''){echo $consumer_fileno;}else {//echo fILENUMBER; 
												} ?>" <?php if($consumer_fileno!='' && !$Existerror){echo "readonly=readonly";}?> class="input-m"/>
												<span for="Paralegal1FileNumber" class="help-inline ok"><?php echo $message; ?></span>
											</div>												
											<div class="control-group forms">
												<label>Jurisdiction<span class="required">*</span></label>
												<select class="input-m" name="Paralegal1Jurisdiction" id="Paralegal1Jurisdiction">
												  <option value="">--Jurisdiction--</option>
													<?php $province = $provinceObj->selectProvince();
													print_r($province);
													while($state=mysqli_fetch_object($province))
													{ ?>
														<option value="<?php echo $state->state_id; ?>"<?php if($state_id!='' && $state_id==$state->state_id){echo "selected=selected";}?>>
														<?php echo $state->name; ?></option>
														<?php
													} 
													$provinceObj->country='other';
													$province = $provinceObj->selectProvince();
													print_r($province);

													if(mysqli_num_rows($province)>0)
													{
														while($state=mysqli_fetch_object($province))
														{ ?>
															<option value="<?php echo $state->state_id; ?>" <?php if($state_id!='' && $state_id==$state->state_id){echo "selected=selected";}?>><?php echo $state->name; ?></option>
															<?php 
														} 
													} ?>
												</select>
											</div>
												
											<div class="control-group forms">
												<label>Company Type<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="NumberedCorporation" name="service" <?php if($companytype_id!='' && $companytype_id=='NumberedCorporation'){ echo "checked=checked";}?> id="service" onClick="return values('NumberedCorporation');"/> 
														Numbered Corporation
													</div>
													<div class="checkbox-line">
														<input type="radio" value="Named Corporation" <?php if($companytype_id!='' && $companytype_id=='Named Corporation'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														Named Corporation
													</div>
													<div class="checkbox-line">
														<input type="radio" value="LLC" <?php if($companytype_id!='' && $companytype_id=='LLC'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														LLC
													</div>
													<div class="checkbox-line">
														<input type="radio" value="ULC" <?php if($companytype_id!='' && $companytype_id=='ULC'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														ULC
													</div>
													<div class="checkbox-line">
														<input type="radio" value="ProfessionalCorporation" <?php if($companytype_id!='' && $companytype_id=='ProfessionalCorporation'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														ProfessionalCorporation
													</div>
													<div class="checkbox-line">
														<input type="radio" value="C Corporation" <?php if($companytype_id!='' && $companytype_id=='C Corporation'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														C Corporation
													</div>
													<div class="checkbox-line">
														<input type="radio" value="S Corporation" <?php if($companytype_id!='' && $companytype_id=='S Corporation'){ echo "checked=checked";}?> name="service" id="service" onClick="return values('Numbered Corporation');"/>
														S Corporation
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											<div class="control-group" id="otherCompanyText">
												<p>
												</p>
											</div>
											<div class="control-group" id="numberedCompanyText" style="display:none;">
												<p>
													<strong>Numbered companies </strong>- leave the name choice field blank until the numbered company is formed.
												</p>
											</div>
											<div class="forms control-group">
												<label>Company Name
													<!-- <span class="required showTip L1">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>Corporate names must end with a legal element such as Inc., Ltd., Corp.,  Incorporated, Limited or Corporation.LLC companies will end with LLC and ULC companies will end with ULC.Co., Co-Op, LLP are NOT accepted legal elements.When submitting 3 name choices each choice must be different.XYZ Alberta Inc. and XYZ Alberta Ltd. are NOT considered to be different since the only difference is the legal element.  Be sure to enter up to 3 distinctly different names here.</p>
														</span>
													</span> -->
												</label>
												<?php 
												$var=explode(',',$companyname);
												$count=count($var);
												 ?>
												<span id="divName">
													<input type="text" name="Paralegal1Namechoices[]" value="<?php if($var[0]!=''){echo $var[0];}?>" id="Paralegal1Namechoices"class="input-m"/>
													<?php if($count>1){
													for($i=2;$i<$count;$i++)
													{
														$value=$i-1;
														?>
														<div id="main<?php echo $value?>">
															<input id="Paralegal1Namechoices<?php echo $value ?>" type="text" name="Paralegal1Namechoices[]" value="<?php echo $var[$value]; ?>">
															<div id="close1" onclick="return closeButton('main<?php echo $value?>');"></div>
															</div>
														<?php
													}
													} ?>
												 </span>
											</div>
										   <input class="btn-blue" type="button" name="addmore[]" id="addmore" value="Add" onClick="return multiple_val2();" style="display:none;">
										   
											<div class="forms control-group">
												<label>Company Contact<span class="required"> *</span></label>
												<input type="text" value="<?php if($companycontact!=''){echo $companycontact;}?>" name="Paralegal1CompanyContact" id="Paralegal1CompanyContact" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Work Phone</label>
												<input type="text" name="Paralegal1WorkPhone" value="<?php if($companyworkphone!=''){echo $companyworkphone;}?>" id="Paralegal1WorkPhone" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Cell Phone<span class="required"> *</span></label>
												<input type="text" name="Paralegal1CellPhone" id="Paralegal1CellPhone" value="<?php if($companycellphone!=''){ echo $companycellphone;} ?>" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Fax</label>
												<input type="text" name="Paralegal1fax" value="<?php if($companyfax!=''){ echo $companyfax;} ?>" id="Paralegal1fax" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Email<span class="required">*</span></label>
												<input type="hidden" name="Paralegal1emailold" id="Paralegal1emailold" value="<?php if($companyemail!=''){echo $companyemail;} ?>" class="input-m"/>

												<input type="text" name="Paralegal1email"  value="<?php if($companyemail!=''){echo $companyemail;} ?>" id="Paralegal1email" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Registered Address<span class="required">*</span></label>
												<input type="text" value="<?php if($companyresaddress!=''){echo $companyresaddress;} ?>"name="Paralegal1RegisteredAddress" id="Paralegal1RegisteredAddress" class="input-m" />
												<!-- onblur="return recordaddress(this.value,'<?php echo $consumer_fileno; ?>');" -->
											</div>
											
											<div class="forms control-group">
												<label>Records Address<span class="required">*</span> Same as Above<input type="checkbox" name="sameadd" id ="sameadd"></label>
													<input type="text" name="Paralegal1RecordsAddress" value="<?php if($companymailingaddress!=''){echo $companymailingaddress;} ?>" id="Paralegal1RecordsAddress" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>Mailing  Address
													<span class="showTip L2">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>The mailing address is different than the registered and records office addresses.  Corporations cannot use this address as the only company address.
															This field completed only if the Corporation wishes to have a P.O. Box address in addition to its required registered office address.</p>
														</span>
													</span>
												</label>
												<input type="text" value="<?php if($companyrecordaddress!=''){echo $companyrecordaddress;} ?>" name="Paralegal1MailingAddress" id="Paralegal1MailingAddress" class="input-m"/>
											</div>
											<input class="response" value="" type="hidden">
											<?php if(isset($_GET['code']) && $_GET['code']!='') {
												if (isset($_GET['task']) && $_GET['task'] == 'finish')
												{
													?>
													<input type="submit" class="button1" name="updatebutton" id="button1" value="Next" />
												<?php												
												}
												else
												{
													?>
													<input type="submit" class="button1" name="updatebutton" id="button1" value="Update" />																
											<?php
												} 
											}
											else
											{ ?>
												<input type="submit" class="button1" name="form1" id="button1" value="Next"/>
											<?php 
											} ?>
											
										</form>
									</div>
								</div>
						<!-- END VALIDATION STATES-->
							</div>
							
						</div>
								<!-- END PAGE CONTENT-->   
							<!-- Popup Code Start -->
						<div class="timeout_reminder add-new-form-popup" style="display:none">
							<div class="portlet box green">
								<span class="fas fa-times" id="popup-close-btn"></span>
								<div class="portlet-title">
									<div class="caption"><i class="icon-reorder"></i>Reminder - Session Time Out </div>
								</div>
								<div class="portlet-body form">
									<div class="inner-wrapper">
									You Session is going to expire with in 5 minutes.Please Save your record.
									</div>
								</div>
							</div>
						</div>
						<!-- Popup Code End -->

						 <audio id="audio" src="https://secure.mycotogo.com/beep-07.wav" autoplay="false" ></audio>
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
	<script src="assets/scripts/bootstrap-session-timeout.min.js"></script> 
	<script src="assets/scripts/session-timeout.js"></script>
	
	<script>
		jQuery(document).ready(function() {   
		   // initiate layout and plugins
		   App.init();
		   FormValidation.init();
		});
		
	</script>
<script>

		$(".btn-navbar").click(function(){
		$(".page-container .page-sidebar.nav-collapse").removeAttr("style");
		$(".page-sidebar .page-sidebar-menu").slideToggle(500);
		});
		var dt = new Date();
		var time_old = dt.getMinutes()
	   	
		/*** Popup Close Js Strat ***/
		jQuery('#popup-close-btn').click(function(){
		jQuery('.add-new-form-popup').fadeOut();
		});
		/*** Popup Close Js End ***/



	   //check_timeout();
	   function check_timeout()
	   {
	   		$('.timeout_reminder').hide();
		   	setTimeout(function(){ 
			   	//alert(time_old); 
			   	var dt = new Date();
				var time_now= dt.getMinutes(); 
				//alert(time_now);
				if (time_now >= time_old)
				{
					lapse_min = time_now-time_old;
				}
				else
				{
					lapse_min = time_now+60-time_old;
				}
				console.log(lapse_min);
				if (lapse_min >= 18)
				{
					var sound = document.getElementById("audio");
          			sound.play();
					$('.timeout_reminder').show();
					//alert("Time out Left "+(24-lapse_min));
				} 
				else
				{
					check_timeout();
				}				
			}, 120000);
		
	   }

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

    $('body').on('change','#sameadd',function(){
    	var consumer_file = "<?php echo $consumer_fileno; ?>";
    	var recordAddressval = $("#Paralegal1RegisteredAddress").val();
    	var companymailingaddress = "<?php echo $companymailingaddress; ?>";
    	if ($(this).is(':checked'))
    	{
    		$("#Paralegal1RecordsAddress").val(recordAddressval);
    	}
    	else
    	{
    		if (consumer_file.length == 0)
    		$("#Paralegal1RecordsAddress").val('');
    		else
    		$("#Paralegal1RecordsAddress").val(companymailingaddress);
    	}
	});

	$('body').on('change','#Paralegal1FileNumber',function(){
		var file_no = $(this).val();
    	var str = "task=CheckdupliConsumer&consumer_file="+file_no;
    	var cdiv = $(this).parent();
		//alert(str);
		$.ajax({
		type:"POST",
		url:"showResults.php",
		dataType: 'json',
		data:str,
		success:function(response)
		{							
			//alert(response['consumerfname']);
			if (response == '1')
			{
				//alert('Warning! Consumer file already in use '+ file_no);
				cdiv.addClass('error');
				cdiv.find('.help-inline').removeClass('ok');
				cdiv.find('.help-inline').html('Warning! This file is already in use');
			}

		}
	});

	});

	

    });
</script>
	<!-- END PAGE LEVEL STYLES -->    
	
 <script type="text/javascript">
function recordaddress(recordAddressval, file_id)
{
	//alert(file_id);
	//alert(recordAddressval);
	if (file_id == '')
	$("#Paralegal1RecordsAddress").val (recordAddressval);
}
 function closeButton(val)
 {
	$('#'+val).remove();
 }
function multiple_val2()
{
	var countInput=($(":input[id^=Paralegal1Namechoices]").length);
	var limit = 3;
	var candidateForm = document.getElementById('divName');
	
	if (countInput == limit) {
		alert("You have reached the limit of adding " + countInput + " inputs");
	}
	else {
	
		var maindiv = document.createElement('div');
			maindiv.id="main"+countInput;
   
		var newTextbox = document.createElement('input');//creating the textboxes according to the users input
			newTextbox.type = "text";
			newTextbox.name = "Paralegal1Namechoices[]";
			newTextbox.id = "Paralegal1Namechoices"+countInput;
			newTextbox.class = "Paralegal1Namechoices";
			newTextbox.value = '';
			
		var newdiv =document.createElement('div');
			newdiv.id="close"+countInput;
			
			var divmain=candidateForm.appendChild(maindiv);
				divmain.appendChild(newTextbox);
			var closediv = divmain.appendChild(newdiv);
			   //$("#close"+countInput).append('123');
			   closediv.onclick = function(){ 
			   $('#'+maindiv.id).remove();
			   };
			  
	}
}
function values(val)
{
	if(val=="test")
	{
		$('#addmore').hide();
	}
	else if(val!="NumberedCorporation")
	{
		$('#addmore').show();
		
		$("#Paralegal1Namechoices").rules("add", {
			required:true,
			messages: {
				required: "You did not enter Name choices"
			}
		});
	}
	else
	{
		$("#Paralegal1Namechoices").rules("remove");
		$('#addmore').hide();
		$('#main1').remove();
		$('#main2').remove();
		$('#Paralegal1Namechoices1').remove();
		$('#Paralegal1Namechoices2').remove();
	}
}

dw_Tooltip.content_vars = {
    L1: '',
    L2: '',
	 L3: 'Hello'
}
// function formSubmit()
// {
	// console.log('checking mail');
	// var userEmail = $('#Paralegal1email').val();
	
		// if(($('#Paralegal1email').val())!='')
		// {
			// $.ajax({
				// type : 'POST',
				// url : 'includes/bookInfo.php',
				// data : 'task=checkEmail&useremail='+userEmail,
				// success:function(response)
				// {
					// console.log(response);
					// return false;
					// $('.backdrop').show();
					// $('.light_box').show();
					// jQuery('.backdrop').css({'opacity':0.6}) ;
					// jQuery('.light_box').css({'opacity':1}) ;
					
					//$('.corporate-info').html(response);
					// jQuery('.renameform').html("<div style='text-align:center'><h1>Loading...</h1></div>");
							//notifications.showAlert('Notice', 'Force sync sucessfully complete', notifications.ALERT);
					// jQuery('.renameform').html(response);
				// }
			// })
			// return false;
		// }
// }
function continueMail()
{
	$('#continueCreate').val('yes');
	$('.response').val('true');
	//$( "#Paralegal1email" ).rules( "remove" );
	$("[for	=Paralegal1email]").addClass('valid ok').html('user added successfully');
	$('.forms').addClass('success').removeClass('error');
	$('.alert-error').hide();
	$('.backdrop, .light_box'  ).css('display', 'none');	
}

function confirmEmail()
{
	$('#continueCreate').val('');
	$('.response').val('false');
	$('.backdrop, .light_box'  ).css('display', 'none');
	
}

function continueMailSubmit()
{
	$('#continueCreate').val('yes');
	$('.backdrop, .light_box'  ).css('display', 'none');
	//$('#form_sample_2').submit();
	return true;
}



function confirmEmailSubmit()
{
	$('#continueCreate').val('');
	$('.backdrop, .light_box'  ).css('display', 'none');
	//$('#form_sample_2').submit();
	return true;	
}
$('body').on('keyup','#Paralegal1email',function(){
	($('.response').val('false'));
})
$.validator.addMethod('emailValidation', function (value, element, param) {
    //Your Validation Here
	console.log('checking mail');
	console.log($('.response').val());
	if($('.response').val()=='true')
		return true;	
	if ($('#Paralegal1email').val()!='' && $('#Paralegal1email').val()==$('#Paralegal1emailold').val())
	{
		$('#continueCreate').val('yes');
		$('.response').val('true');
		console.log('mail equal');

	}
	else
	{
		$('#continueCreate').val('');
		$('.response').val('false');
		console.log('mail false');
	}
	if($('.response').val()=='true')
		return true;	
	var userEmail = $('#Paralegal1email').val();	
		if(($('#Paralegal1email').val())!='')
		{
			$.ajax({
				type : 'POST',
				url : 'includes/bookInfo.php',
				data : 'task=checkEmail&useremail='+userEmail,
				success:function(response)
				{					
					var result	=	response.trim();
					if(result!='')
					{
						$('.backdrop').show();
						$('.light_box').show();
						jQuery('.backdrop').css({'opacity':0.6}) ;
						jQuery('.light_box').css({'opacity':1}) ;
						$('.corporate-info').html(response);
						//jQuery('.renameform').html("<div style='text-align:center'><h1>Loading...</h1></div>");
						//notifications.showAlert('Notice', 'Force sync sucessfully complete', notifications.ALERT);
						jQuery('.renameform').html(response);
						//$('.response').val('true');
						//return false;
					}
					else
					{
						$('.response').val('true');
						$("[for	=Paralegal1email]").addClass('valid ok').html('user added successfully');
						$('.forms').addClass('success').removeClass('error');
						$('.alert-error').hide();
						return true;
					}
					
				}
			});
			return false;
		}
},'Review email address');


$.validator.addMethod('contactvalidation', function (value, element, param) {
    //Your Validation Here
	var contact = $('#Paralegal1CellPhone').val();
	if(contact.charAt(0)=='+')
	{
		if(contact.length>'11' && contact.length<='13')
		{
			return true;
		}
		else
			return false;
	}
	else
	{
		var firstletter	=	contact.charAt(0);
		var isvalid	=	$.isNumeric(firstletter);
	
		if(isvalid==true)
		{
			var secondletter	=	contact.charAt(1);
			if(secondletter=='-')
			{
				var secondThird	=	contact.substring(2,5);
				if($.isNumeric(secondThird)==true)
				{
					if(contact.substring(5,6)=='-')
					{
						if($.isNumeric(contact.substring(6,9))==true)
						{
							if(contact.substring(9,10)=='-')
							{
								var contactInfo =	contact.substring(10,14);
								if(contact.length=='14')
								{
									return true;
								}
								else
									return false;
							}
							else
								return false;
						}
						else
							return false;
					}
					else
						return false;
				}
				else
				return false;
			}
			else
				return false;
		}
		else
			return false;
	}
}, jQuery.validator.format('Enter correct format (1-###-###-####)'));


// $('#form_sample_2').validate({
    // rules: {
        
    // }
// });
// function checkValidation()
// {
	
	// return false;
	// var contact = $('#Paralegal1WorkPhone').val();
	//alert(contact.charAt(0));
	// if(contact.charAt(0)=='+')
	// {
		// if(($('#Paralegal1email').val())!='')
		// {
			// var userEmail = $('#Paralegal1email').val();
				// $.ajax({
				// type : 'POST',
				// url : 'includes/bookInfo.php',
				// data : 'task=checkEmail&useremail='+userEmail+'&action=submit',
				// success:function(response)
				// {
					// $('.backdrop').show();
					// $('.light_box').show();
					// jQuery('.backdrop').css({'opacity':0.6}) ;
					// jQuery('.light_box').css({'opacity':1}) ;
					
					//$('.corporate-info').html(response);
					// jQuery('.renameform').html("<div style='text-align:center'><h1>Loading...</h1></div>");
							//notifications.showAlert('Notice', 'Force sync sucessfully complete', notifications.ALERT);
					// jQuery('.renameform').html(response);
				// }
			// })
			// return false;
		// }
	// }
	// else
	// {
		// alert('Please Enter Country Code in Work Phone');
		// return false;
	// }
	
// }
</script>

	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>