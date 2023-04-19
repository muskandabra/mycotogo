<?php
include_once("private/settings.php");
include_once("classes/User.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/Module.php");
include_once(PATH."classes/clsProvince.php");
include_once(PATH."includes/accessRights/manageConsumers.php");
include_once("classes/clsNotification.php");
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]--><head>
	<meta charset="utf-8" />
	<title><?php echo SITE_NAME;?> | Additional Registration</title>
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
	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />


	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
</head>
<style>
.checkbox-line {
    display: inline-block;
    width: 23%!important;
}
.control-group.date-section {
  margin-bottom: 10%;
}
.help-inline {
    display: inline-flex;
    float: right;
    width: 138px;
}
</style>
<!-- BEGIN BODY -->
<?php
$name='';
$business_type='';
$state_province='';
$signatureMode = '';
$incorp_date = date('Y-m-d');
$consumerObj= new Consumer();
$provinceObj= new Province();
$userObj= new User();
$adminDel = $userObj->selectAdmin();
$notificationObj= new Notification();
$getValue	=	$_GET['code'];
$code=base64_decode($_GET['code']);
$consumerObj->consumer_fileno=$code;
$row=$consumerObj->getCompanyDetails();
$row	= mysqli_fetch_object($row);
$error = '';
$file_no = '';

$workspace=0;
$parameter = '';
if(isset($_GET['workspace']) && $_GET['workspace']=='1')
{
	$workspace = 1;
	$parameter = "&workspace=1";
	$consumerObj->workspace = '1';
}

if(isset($_SESSION['workspace']) && !empty($_SESSION['workspace']))
{
	$workspace = 1;
	$parameter = "&workspace=1";
	$consumerObj->workspace = '1';
}




if($row->consumerfilestatus_id=='1')
{
	$consumerfilestatus_id='4';
}
else
{
	$consumerfilestatus_id=$row->consumerfilestatus_id;
}

if(isset($_POST['finish']) && isset($_POST['AddRecords']) && $_POST['AddRecords']!='')
{			
	$consumer_id=$consumerObj->getconsumer_id($code);
	$user='user';
	$notificationstatus='pending';
	$consumerObj->consumer_fileno = $code;
	$reminderCount=$_POST['boxCount'];
	
	//$consumerObj->name = $_POST['trade-name'];
	//$consumerObj->business_type = $_POST['type-of-business'];
	//$consumerObj->state_province = $_POST['province-state'];
	//$consumerObj->additional_registeration();
	$consumerObj->consumerfilestatus_id=$_POST['consumerfilestatus_id'];
	$consumerObj->consumer_id=$consumer_id;
	$consumerObj->usertype=$_SESSION['usertype'];
	$consumerObj->user_id=$_SESSION['sessuserid'];
	$consumerObj->signatureMode = $_POST['signatureMode'];	
	$consumerObj->updateConsumer();
	//die;
	if ($_POST['addReminders'] == 1)
	{
		$message_format	=	$_POST['reminderas'];
		for($x=1;$x<=$reminderCount;$x++)
		{
			if($_POST['description_'.$x.'']!='')
			{
				$notificationObj->notificationdate=$_POST['Trading_date_'.$x.''];
				$notificationObj->message_format= $message_format;
				$notificationObj->notificationdescription= $_POST['description_'.$x.''];
				$notificationObj->duedate= $_POST['duedate_'.$x.''];
				if (isset($_POST['nowdate_'.$x.'']))
					$notificationObj->sentnow= $_POST['nowdate_'.$x.''];
				$notificationObj->notificationcreatedby=$user;
				$notificationObj->created_id=$_SESSION['sessuserid'];
				$notificationObj->consumer_id=$consumer_id;
				$notificationObj->notification_category_id=0;
				$notificationObj->parent_id=0;
				$notificationObj->notificationstatus=$notificationstatus;
				$notificationObj->usertype=$_SESSION['usertype'];
				$notificationObj->user_id=$_SESSION['sessuserid'];
				$notificationObj->consumer_fileno=$code;
				$notificationObj->cc_paralegal  = $_POST['is_cc_paralegal'];
				$notificationObj->add_notification();
				// if($message_format=='mail/text' || $message_format=='text')
				// {
					// if($_POST['nowdate_'.$x.'']=='1')
					// {
						// $fetchadmin	=	mysqli_fetch_object($adminDel);
						// $userPhone[]	=	$fetchadmin->ContactNo;
						// $userPhone[]	=	$row->companycellphone;
						// foreach($userPhone as $phone)
						// {
							// if($phone!='')
							// {
								// $notificationObj->companyworkphone=$phone;
								// $notificationObj->sendSmsNotification();
							// }
							
							
						// }
					// }
				// }
				// if($message_format=='mail/text' || $message_format=='mail')
				// {
					// if($_POST['nowdate_'.$x.'']=='1')
					// {
						// $test	=	$notificationObj->MailNotificationDetails();
						// $email_data	=	mysql_fetch_array($test);
						// $notificationObj->useremail=$email_data['useremail'];
						// $notificationObj->sendMailNotification();
					// }
						
				// }
					
			}
		}
	}
	print"<script language=javascript>window.location='consumer.php?no=".$getValue.$parameter."'</script>";
	die;
}
if(isset($_POST['finish']) && isset($_POST['EditRecords']) && $_POST['EditRecords']!='' && $_POST['file_no'] != '' && $_POST['incorp_date'] != '') 
{
	$consumerObj->newconsumer_fileno  = $_POST['file_no'];
	$consumerObj->consumer_old_fileno  = $_POST['old_fileno'];	
	if ($consumerObj->checkdupli_fileno() <= 0)
	{
		$consumer_id=$consumerObj->getconsumer_id($code);
		$consumerObj->consumer_fileno = $code;
		//$consumerObj->name = addslashes($_POST['trade-name']);
		//$consumerObj->business_type = addslashes($_POST['type-of-business']);
		//$consumerObj->state_province = $_POST['province-state'];
		$consumerObj->updatedDate = $_POST['incorp_date'];
		$consumerObj->newconsumer_fileno  = $_POST['file_no'];	
		$consumerObj->signatureMode = $_POST['signatureMode'];
		
		$consumerObj->editadditional_registeration();
		print"<script language=javascript>window.location='consumer.php?no=".base64_encode($_POST['file_no']).$parameter."'</script>";

	}
	else
	{
		echo $error = $_POST['file_no'].' File no. already Exist ! ';
		//die;
	}
}
if(isset($_GET['action']) && $_GET['action']!='')
{
	$code=base64_decode($_GET['code']);

	$consumer_id=$consumerObj->getconsumer_id($code);
	if($consumer_id!='')
	{
		$consumerObj->consumer_id=$consumer_id;
		$query=$consumerObj->selectConsumer();
		// $res=$consumerObj->selectConsumer();
		// $query=mysqli_query($dbconnection,$res);
		if(mysqli_num_rows($query)>0)
		{
			$row=mysqli_fetch_object($query);	
			$name=$row->name;
			$business_type=$row->business_type;
			$state_province=$row->state_province;
			$consumerfilestatus_id =$row->consumerfilestatus_id ;
			$incorp_date = $row->updatedDate; 
			$file_no =  	$row->consumer_fileno; 
			$signatureMode = $row->signatureMode; 
		}
	}
}
$selectoptionTemplate='<option value="">Select Template</option>';
$notificationObj->user_id = $_SESSION['sessuserid'];
$selectNotificationTemplate = $notificationObj->selectNotificationTemplate();
if(mysqli_num_rows($selectNotificationTemplate)>0)
{
	while($fetchNotification =mysqli_fetch_object($selectNotificationTemplate))
	{
		$selectoptionTemplate	=	$selectoptionTemplate.'<option  value="'.$fetchNotification->notification_template_id.'">'.ucfirst($fetchNotification->template_title).'</option>';
	}
}

//die;
?>
<body class="page-header-fixed additional-reg">
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
				<div class="row-fluid">
					<div class="span12">
					</div>
				</div>
			<div class="row-fluid">
					<div class="span12">
							<!-- BEGIN VALIDATION STATES-->
						<div class="portlet box green">
								<div class="portlet-title">
									<div class="caption"><i class="icon-reorder"></i>Paralegal Page 3.3</div>
								</div>
							<div class="portlet-body form reg-additional">
								<div class="inner-wrapper">
									<div class="form">
										<h3 class="required">Fields marked with a* are required</h3>
										<form id="form_sample_1" class="form-horizontal form1" method="POST"  name="adminForm">
										<input type="hidden" name="shareholders" value="shareholders"/>
										<input type="hidden" name="old_fileno" value="<?php echo $file_no;?>"/>
										<?php 
										if(isset($_GET['action']) && $_GET['action']!='')
										{
											?><input type="hidden" name="EditRecords" value="EditRecords"/><?php 
										}
										else
										{
											?><input type="hidden" name="AddRecords" value="AddRecords"/><?php
										}
										?>
											<div class="alert alert-error hide">
												<button class="close" data-dismiss="alert"></button>
												You have errors. Please check below.
											</div>
											<div class="alert alert-success hide">
												<button class="close" data-dismiss="alert"></button>
												Your form validation is successful!
											</div>
											
											<!-- 
											<div class="forms control-group">
												<label>Any additional registrations ?<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														// <input type="radio" value="1" name="OfficerChecks" id="OfficerChecks" onclick="return additionalValue(this.value);" <?php //if($name!='' || $state_province!=''){ echo "checked=checked"; } ?>/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="0" name="OfficerChecks" id="OfficerChecks" onclick="return additionalValue(this.value);" />No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											<div class="forms control-group" id="tradeValues" style="display:none;">
												<label>Trade<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="1" name="TradeChecks" id="TradeChecks" onclick="return TradeValue(this.value);"<?php //if($name!=''){ echo "checked=checked"; } ?>> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="0" name="TradeChecks" <?php //if($name==''){ echo "checked=checked"; } ?>id="TradeChecksfalse" onclick="return TradeValue(this.value);"/>No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											<div id="trade-info" style="display:none;">
												<div class="forms control-group">
													<label>Name<span class="required">*</span></label>
													<input type="text" value="<?php //echo $name;?>" name="trade-name" class="input-m" id="trade-name" />
												</div>
												<div class="forms control-group">
													<label>Type of Business<span class="required">*</span></label>
													<input type="text" name="type-of-business" value="<?php //echo $business_type;?>" class="input-m" id="type-of-business" />
												</div>
											</div>
											<div class="forms control-group" id="provincialValues" style="display:none;">
												<label>Extra Provincial Registration<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="1" name="ProvincialChecks" id="ProvincialChecks" onclick="return ProvincialValue(this.value);"<?php //if($state_province!=''){ echo "checked=checked"; } ?> />  Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="0" <?php //if($state_province==''){ echo "checked=checked"; } ?> name="ProvincialChecks" id="ProvincialChecksfalse" onclick="return ProvincialValue(this.value);"/>No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											
											
											<div id="additional-reg" style="display:none;">
												<div class="forms control-group">
													<label>State or Province <span class="required">*</span></label>
													<select class="input-m" name="province-state" id="province-state">
														<option value="">--State/Province--</option>
														<?php $province = $provinceObj->selectProvince();
														//while($state=mysqli_fetch_object($province))
														{ ?>
															<option <?php if($state_province!='' && $state_province==$state->state_id){ echo "selected=selected"; } ?> value="<?php echo $state->state_id; ?>"><?php echo $state->name; ?></option> <?php
														} 
														$provinceObj->country='other';
														$province = $provinceObj->selectProvince();
														if(mysqli_num_rows($province)>0)
														{
															while($state=mysqli_fetch_object($province))
															{ ?>
																<option <?php if($state_province!='' && $state_province==$state->state_id){ echo "selected=selected"; } ?> value="<?php echo $state->state_id; ?>"><?php echo $state->name; ?></option> <?php
															} 
														} ?>
													</select>
												</div>
											</div> -->
											<?php
											if(!isset($_GET['action'])) {?>
											<div class="forms control-group" >
												<label>Digital signature(s) required ?<span class="required">*</span>
													<span class="showTip L2">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>Choose "Yes" to create documents that are ready to be digitally signed and printed.<br>
															Choose "No" to create printable, signature ready documents</p>
														</span>
													</span>
												</label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="D" name="signatureMode" id="signatureModeDig"  <?php if($signatureMode =='D'){ echo "checked=checked"; } ?>/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="M" name="signatureMode" id="signatureModeMan" <?php if($signatureMode !='D'){ echo "checked=checked"; } ?> />No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											<div class="forms control-group" >
												<label>Add Reminder ?<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="1" name="addReminders" id="addReminders" onclick="return addReminderValue(this.value);" <?php //if($name!='' || $state_province!=''){ echo "checked=checked"; } ?>/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="0" name="addReminders" id="OfficerChecks" onclick="return addReminderValue(this.value);" />No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>
											<div class="forms control-group" style="display:none;" id="add_reminders">
												<label>Send Reminder as :<span class="required">*</span></label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="text" name="reminderas" id="reminderas" /> text
													</div>
													<div class="checkbox-line">
														<input type="radio" value="mail" name="reminderas" id="reminderas"  />email
													</div>
													<div class="checkbox-line">
														<input type="radio" value="mail/text" name="reminderas" id="reminderas" />both
													</div>
													<div class="checkbox-line">
														<input type="radio" value="system" name="reminderas" id="reminderas" />System only
													</div>
												
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>												
													
												<div class="forms control-group  cc_paralegal" style="display:none" >
													<label class="">CC Paralegal<span class="required">*</span></label>
													<div class="control-area">										
															<div class="checkbox-line">
																<input type="radio" id="Checksothers" name="is_cc_paralegal" value="1" > Yes
															</div>
															<div class="checkbox-line">
																<input type="radio" id="Checksothers" name="is_cc_paralegal" value="0" checked="checked">No
															</div>
													</div>
												</div>
											</div>
											<?php
											}
											if(isset($_GET['action'])) {?>

											<div class="forms control-group" >
												<label>Digital signature(s) required ?<span class="required">*</span>
													<span class="showTip L2">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>Choose "yes" to create documents ready for digital signing. Choose "No" to create signature ready documents for offline print signatures.</p>
														</span>
													</span>
												</label>
												<div class="control-area">
													<div class="checkbox-line">
														<input type="radio" value="D" name="signatureMode" id="signatureModeDig"  <?php if($signatureMode =='D'){ echo "checked=checked"; } ?>/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="M" name="signatureMode" id="signatureModeMan" <?php if($signatureMode !='D'){ echo "checked=checked"; } ?> />No
													</div>
													<div class="checkbox-line">
														<span class="help-block"></span>
														<div id="form_2_service_error"></div>
													</div>
												</div>
											</div>

											<div class="forms control-group">
												<label>Date of Incorporation<span class="required">*</span></label>
													<input class="inputbox datepicker" type="text" value="<?php echo $incorp_date;?>" name="incorp_date" id="incorp_date" required />
											</div>
											<div class="forms control-group">
												<label>File Number<span class="required">*</span></label>
													<input class="inputbox" type="text" value="<?php echo $file_no;?>" name="file_no" id="file_no" required />
											</div>
											<?php
												if (!empty($error))
												{
													echo '<div style="color:red;">'.$error.'</div>';							
												}
											?>

											

											<?php }
											if(!isset($_GET['action'])) {?>
											<div id="test" style="display:none">
											<div class="portlet box yellow reminder yellow_reminder " >
											<input type="hidden" id="reminder-title" >
											
												<div class="portlet-title">
													<div class="caption" id="reminder1">
														Add Reminder 
													</div>
													<div class="tools">
														<a class="collapse" href="javascript:;"></a>	
													</div>
												</div>
												<?php 												
												if(mysqli_num_rows($selectNotificationTemplate)>0)
												{
													while($fetchNotification =mysqli_fetch_object($selectNotificationTemplate))
													{
														$selectoptionTemplate	=	$selectoptionTemplate.'<option  value="'.$fetchNotification->notification_template_id.'">'.ucfirst($fetchNotification->template_title).'</option>';
													}
												}
												?>
												<div class="portlet-body" style="display: block;">
													<div class="remain-one-half">
														<div class="control-group date-section">
															<label class="control-label">Due Date</label>
															<div class="controls" >
																<input class="inputbox datepicker" type="text" name="Trading_date_1" value="<?php echo date("Y-m-d");?>" id="Trading_date" />


																<!-- <a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.adminForm.Trading_date);return false;" hidefocus>
																	<img class="PopcalTrigger" align="absmiddle" src="img/calander.png" border="0" alt="">
																</a>
																<input class="input_account" type="text" id="Trading_date" name="Trading_date_1" required value="<?php //echo date("m/d/Y");?>"/> -->
															 
															</div>
														</div>
														<div class="controls">
														<?php 
															echo '<select id="test_1" name="test_1" onchange= "getTemplate(this.value,this.id)"; class="templateinfo clonedSection">'.$selectoptionTemplate.'</select>'; ?>
														</div>
														<div class="forms control-group" style="display:none;">
															<label>Send reminder now ?<span class="required">*</span></label>
															<div class="control-area">
																<div class="checkbox-line">
																	<input type="radio" value="1" name="nowdate_1" id="nowdate_1" required/> Yes
																</div>
																<div class="checkbox-line">
																	<input type="radio" checked="checked" value="0" name="nowdate_1" id="nonowdate_1" required />No
																</div>
																<div class="checkbox-line">
																	<span class="help-block"></span>
																	<div id="form_2_service_error"></div>
																</div>
															</div>
														</div>
													</div>	
													<div class="remain-one-half right">
														<div class="forms control-group">
															<label>Description</label>
															<div class="controls">
																<textarea name="description_1" id="description_1" class="large m-wrap" rows="3" ></textarea>
															</div>
														</div>
														<div class="forms control-group" style="display:none;">
															<label>Send reminder on due date?<span class="required">*</span></label>
															<div class="control-area">
																<div class="checkbox-line">
																	<input type="radio" checked="checked" value="1" name="duedate_1" id="duedate_1" required/> Yes
																</div>
																<div class="checkbox-line">
																	<input type="radio" value="0" name="duedate_1" id="nodate_1" required/>No
																</div>
																<div class="checkbox-line">
																	<span class="help-block"></span>
																	<div id="form_2_service_error"></div>
																</div>
															</div>
														</div>
													</div>
													
												</div>
												
											</div>
											
											</div>
											
											<div class="forms control-group addAnotherDiv" style="display:none">
												<input type="button" name="addmore" class="button1" id="addmore" value="Add Another Reminder" onClick="return multiple_val2();"/>
												<input type="button" style="display:none;" name="removeBox" class="button1" id="removeBtn" value="Remove Reminder" onClick="return remove_box();"/>
												</div>
												<?php } ?>
												<input type="hidden" name="boxCount" value="1" id="boxLength"/>
												<input type="hidden" name="consumerfilestatus_id" value="<?php echo $consumerfilestatus_id;?>"/>
												
												<input type="submit" name="finish" class="button1" id="shareholder" value="Finish" onclick="return checkValidation();" />

											
											
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
				<!-- BEGIN PAGE CONTENT-->	
			<!-- END PAGE CONTAINER-->
		</div>
		<!-- END PAGE -->  
	</div>
	<!-- END PAGE -->  
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
	<script src="<?php echo URL;?>assets/plugins/jquery-1.10.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="<?php echo URL;?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="<?php echo URL;?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/form-validation.js"></script> 
	<script src="assets/scripts/bootstrap-session-timeout.min.js"></script> 
	<script src="assets/scripts/session-timeout.js"></script>
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
		   $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd' });
		  $("#Trading_date").datepicker("setStartDate", new Date());

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
          			//sound.play();
					$('.timeout_reminder').show();
					//alert("Time out Left "+(24-lapse_min));
				} 
				else
				{
					check_timeout();
				}				
			}, 120000);
		
	   } 

	    $('body').on('click','#addmore',function(){
	    	var dtpcikerid = $('#test div.reminder:last').find('.datepicker').prop('id');
	    	 //$("#testing2").datepicker("setStartDate", new Date());
	    	  $("#"+dtpcikerid).datepicker("setStartDate", new Date());
	    });

		$('body').on('change',"input[name='signatureMode']",function(){
			if ($("input[name='EditRecords']").val() == "EditRecords")
			{
				alert("Warning- changes do not come into effect until you recreate the company record book.");
			}	    	
	    });

		$('#incorp_date').on("changeDate", function() {
			if ($("input[name='EditRecords']").val() == "EditRecords")
			{
				alert("Warning- changes do not come into effect until you recreate the company record book.");
			}
		});


		      
		});
			if($('#OfficerChecks:checked').val()=='1')
			{
			
				$('#tradeValues').show();
				if($('#TradeChecks:checked').val()=='1')
				{
					$('#trade-info').show();
				}
				$('#provincialValues').show();
				var check = $('#ProvincialChecks:checked').val();
				if(check==1)
				{
					$('#additional-reg').show();
				}
			}

			

			function addReminderValue(val)
			{
				if(val==1)
				{
					$('#add_reminders').show();	
					$('#test').show();	
					$(".addAnotherDiv").show();	
						$("input[name='reminderas']").rules("add", {
							required:true,							
						 });	
						 $("#description_1").rules("add", {
							required:true,							
						 });					
				}
				else if(val==0)
				{
					$('#add_reminders').hide();	
					$('#test').hide();	
					$(".addAnotherDiv").hide();	
					$("input[name='reminderas']").rules("remove");
					$("#description_1").rules("remove");

					// document.getElementById("ProvincialChecksfalse").checked = true;
					// document.getElementById("trade-name").value = "";
					// document.getElementById("type-of-business").value = "";
					// document.getElementById("province-state").value = "";
					
				}
			}


			function additionalValue(val)
			{
				if(val==1)
				{
					$('#tradeValues').show();
					$('#provincialValues').show();
				}
				else if(val==0)
				{
					$('#tradeValues').hide();
					$('#provincialValues').hide();
					$('#trade-info').hide();
					$('#additional-reg').hide();
					//document.getElementById("TradeChecksfalse").checked = true;
					document.getElementById("ProvincialChecksfalse").checked = true;
					document.getElementById("trade-name").value = "";
					document.getElementById("type-of-business").value = "";
					document.getElementById("province-state").value = "";
					
				}
			}
		function TradeValue(val)
		{
			if(val=='1')
			{
				$('#trade-info').show();
			}
			else if(val=='0')
			{
				$('#trade-info').hide();
				document.getElementById("trade-name").value = "";
				document.getElementById("type-of-business").value = "";
			}
			
		}
		function getTemplate(templateId,divId)
		{
			var divdata	=	divId.split('_');
			var task	=	'showtemplate';
			var query	=	'task='+task+'& notification_template_id='+templateId;
			$.ajax({
				type:"POST",
				url:'includes/bookInfo.php',
				data	:	query,
				success: function(response)
				{
					$('#description_'+divdata[1]).val(response);
				}
			});
		}
		function ProvincialValue(val)
		{
			if(val=='1')
			{
				$('#additional-reg').show();
				
			}
			else if(val=='0')
			{
				$('#additional-reg').hide();
				document.getElementById("province-state").value = "";
			}
		}
	function multiple_val2()
	{
		
		
		document.getElementById('removeBtn').style.display='block';
		var countInput1=($(":input[id^=reminder-title]").length);
		var countInput=countInput1+1;
		var limit = 10;
		document.getElementById('boxLength').value= countInput;
		var candidateForm = document.getElementById('test');
		if (countInput == limit) {
			alert("You have reached the limit of adding " + countInput + " inputs");
		}
		else {
		var maindiv = document.createElement('div');
			maindiv.id="main"+countInput;
			maindiv.className ="portlet box yellow reminder";
			
		var portaldiv=document.createElement('div');
			portaldiv.className="portlet-title";
			
		var captiondiv=document.createElement('div');
			captiondiv.className="caption reminder"+countInput;	
			captiondiv.id="reminder"+countInput;
			
		var toolsdiv=document.createElement('div');
			toolsdiv.className="tools toolcount"+countInput;
			
		var newlink = document.createElement('a');
				newlink.setAttribute('class', 'collapse');
				newlink.setAttribute('href', 'javascript:;');
		
		var portalbodydiv=document.createElement('div');
			portalbodydiv.className="portlet-body";
			
		var remainonehalfdiv=document.createElement('div');
			remainonehalfdiv.className="remain-one-half";
			
		var remainrighthalfdiv=document.createElement('div');
			remainrighthalfdiv.className="remain-one-half right";
			
		var controlformdiv=document.createElement('div');
			controlformdiv.className="forms control-group";
			
		/*
		*
		*	For due date div
		*/
		var controlformdivdate=document.createElement('div');
			controlformdivdate.className="forms control-group";
			controlformdivdate.style.display = 'none';
		
		var duedatelabel =document.createElement('label');
				duedatelabel.setAttribute('class', 'control-label');
				duedatelabel.setAttribute('id', 'duedate'+countInput);
				
		var controlformdivcontrol=document.createElement('div');
			controlformdivcontrol.className="control-area";
		
		var controlformdivcontroldiv1=document.createElement('div');
			controlformdivcontroldiv1.className="checkbox-line first"+countInput;
		
		var controlformdivcontroldiv2=document.createElement('div');
			controlformdivcontroldiv2.className="checkbox-line second"+countInput;
		
		/*
		*
		*	For due date div end
		*/
		
		/*
		*
		*	For reminder now date div 
		*/
		
				var controlformdivreminder =document.createElement('div');
			 controlformdivreminder.className="forms control-group";
			 controlformdivreminder.style.display = 'none';
		
			var nowdatelabel =document.createElement('label');
				nowdatelabel.setAttribute('class', 'control-label');
				nowdatelabel.setAttribute('id', 'nowdate'+countInput);
				
			var controlformdivnow=document.createElement('div');
			controlformdivnow.className="control-area";
		
			var controlformdivcontrolnow1=document.createElement('div');
			controlformdivcontrolnow1.className="checkbox-line firstnow"+countInput;
		
			var controlformdivcontrolnow2=document.createElement('div');
			controlformdivcontrolnow2.className="checkbox-line secondnow"+countInput;
		
		/*
		*
		*	For reminder now date div end
		*/
		var datecontrolgroup=document.createElement('div');
			datecontrolgroup.className="control-group date-section";
		
		var datecontrolgroupselect=document.createElement('div');
			datecontrolgroupselect.className="controls control-group"+countInput;
			
		var datelabel =document.createElement('label');
				datelabel.setAttribute('class', 'control-label');
				datelabel.setAttribute('id', 'date'+countInput);
				
		var descriptionlabel =document.createElement('label');
				descriptionlabel.setAttribute('id', 'description'+countInput);
				
		var controlsclassdate=document.createElement('div');
			controlsclassdate.className="controls";
			
			var controlsclass=document.createElement('div');
			controlsclass.className="controls descriptionfor"+countInput;
			
		var timecontrolgroup=document.createElement('div');
			timecontrolgroup.className="control-group";
			
		var timelabel =document.createElement('label');
				timelabel.setAttribute('class', 'control-label');
				timelabel.setAttribute('id', 'time'+countInput);
		
		var controlsclasstime=document.createElement('div');
			controlsclasstime.className="controls";
			
		// var datepickerdiv=document.createElement('div');
		// 		datepickerdiv.setAttribute('class', 'input-append date date-picker');
		// 		datepickerdiv.setAttribute('data-date-viewmode', 'years');
		// 		datepickerdiv.setAttribute('data-date-format', 'dd-mm-yyyy');
		// 		datepickerdiv.setAttribute('data-date', '12-02-2012');

		var datepickerdiv=document.createElement('div');
				
				
		// var newTextbox = document.createElement('input');
		// 		newTextbox.type = 'text';
		// 		newTextbox.name = 'Trading_date_'+ countInput;
		// 		newTextbox.id = 'testing'+ countInput;
		// 		newTextbox.className = "input_account_date";
		// 		newTextbox.value = '<?php //echo date("m/d/Y");?>';
		// 		newTextbox.required = 'required';

		var newTextbox = document.createElement('input');
				newTextbox.type = 'text';
				newTextbox.name = 'Trading_date_'+ countInput;
				newTextbox.id = 'testing'+ countInput;
				newTextbox.className = "inputbox datepicker";
				newTextbox.value = '<?php echo date("Y-m-d");?>';
				newTextbox.required = 'required';
				
			var textareainput = document.createElement("TEXTAREA"); 
					textareainput.className = 'large m-wrap';
					textareainput.name='description'+ countInput;
					textareainput.setAttribute('rows', '3');
			
			// var texttemplateInfo = document.createElement('select');
			// var optionValue	=	'<?php //echo $selectoptionTemplate; ?>';
				//alert(optionValue);
				
				
			var hiddenTextbox= document.createElement('input');
				hiddenTextbox.type = 'hidden';
				hiddenTextbox.id = 'reminder-title';
				
			// var newspan = document.createElement('a');
			// 	newspan.setAttribute('hidefocus', '');
			// 	newspan.setAttribute('href', 'javascript:void(0)');
			// 	newspan.setAttribute('onclick', 'if(self.gfPop)gfPop.fPopCalendar(document.adminForm.testing'+countInput+');return false;');
				
			// var newi=document.createElement('img');
			// 		newi.setAttribute('class', 'PopcalTrigger_date');
			// 		newi.setAttribute('align', 'absmiddle');
			// 		newi.setAttribute('src', 'img/calander.png');
			// 		newi.setAttribute('alt', '');
			
		var newdiv =document.createElement('div');
			newdiv.id="close"+countInput;
			
			var divmain=candidateForm.appendChild(maindiv);
				var divportal=divmain.appendChild(portaldiv);
					var	divcaption=divportal.appendChild(captiondiv);
						$(".reminder"+countInput).append('Add Reminder');
					var	divtools=divportal.appendChild(toolsdiv);
						var	divlink=divtools.appendChild(newlink);
						var closediv = divtools.appendChild(newdiv);
				var	divportalbody=divmain.appendChild(portalbodydiv);
				
					var	divhalfleft=divportalbody.appendChild(remainonehalfdiv);
					var	labeldate=divhalfleft.appendChild(datecontrolgroup);
							
							var	textdate=labeldate.appendChild(datelabel);
							$("#date"+countInput).append('Due Date');
						
						var	controldate=labeldate.appendChild(controlsclassdate);
						var	test=controldate.appendChild(datepickerdiv);
							test.appendChild(newTextbox);
						// var spancal=test.appendChild(newspan);
						// 	spancal.appendChild(newi);
							
							var	labeltestsel =divhalfleft.appendChild(datecontrolgroupselect);
							
							var num = $('.clonedSection').length;
								
							var newNum  = new Number(num + 1);
							var newSection1 = $('#test_' + num).clone().attr('id', 'test_' + newNum).attr('name','test_' + newNum); 
							$('.control-group'+countInput).html(newSection1);	
						
							
							/*
							*
							*	For right div
							*/
						
							var	divhalfright=divportalbody.appendChild(remainrighthalfdiv);
							
							/*
							*
							*	For description
							*/
							var	divcontrolform=divhalfright.appendChild(controlformdiv);
								var	textdescription=divcontrolform.appendChild(descriptionlabel);
									$("#description"+countInput).append('Description');
							var	divcontrol=divcontrolform.appendChild(controlsclass);
								//divcontrol.appendChild(newSection1);
								
							var newSection2 = $('#description_' + num).clone().attr('id', 'description_' + newNum).attr('name','description_' + newNum); 
							$(".descriptionfor"+countInput).html(newSection2);

							
							
							
							/*
							*
							*	For due date div
							*/duedate_1
							var	divduedate =divhalfright.appendChild(controlformdivdate);
								var	textduelable=divduedate.appendChild(duedatelabel);
									$("#duedate"+countInput).append('Send reminder on due date?*');
										var controldiv =	divduedate.appendChild(controlformdivcontrol);
											controldiv.appendChild(controlformdivcontroldiv1);
											var newSection3 = $('#duedate_' + num).clone().attr('id', 'duedate_' + newNum).attr('name','duedate_' + newNum);
											$('.first'+countInput).html(newSection3);
											$(".first"+countInput).append('Yes');
											controldiv.appendChild(controlformdivcontroldiv2);
											var newSection4 = $('#nodate_' + num).clone().attr('id', 'nodate_' + newNum).attr('name','duedate_' + newNum);
											$('.second'+countInput).html(newSection4);
											$(".second"+countInput).append('No');
								
							/*
							*
							*	End due date div
							*/
							
							/*
							*
							*	For reminder Now div
							*/
								var	divnowdate =divhalfleft.appendChild(controlformdivreminder);
								var	nowdatelabel=divnowdate.appendChild(nowdatelabel);
									$("#nowdate"+countInput).append('Send reminder now ?*');
										var controldiv =	divnowdate.appendChild(controlformdivnow);
											controldiv.appendChild(controlformdivcontrolnow1);
											var newSection5 = $('#nowdate_' + num).clone().attr('id', 'nowdate_' + newNum).attr('name','nonowdate_' + newNum);
											$('.firstnow'+countInput).html(newSection5);
											$(".firstnow"+countInput).append('Yes');
											controldiv.appendChild(controlformdivcontrolnow2);
											var newSection6 = $('#nonowdate_' + num).clone().attr('id', 'nonowdate_' + newNum).attr('name','nonowdate_' + newNum);
											$('.secondnow'+countInput).html(newSection6);
											$(".secondnow"+countInput).append('No');
							/*
							*
							*	For reminder Now div
							*/
			divmain.appendChild(hiddenTextbox);
			closediv.onclick = function(){ 
			   $('#'+maindiv.id).remove();
			   };
		   $('#description_' + newNum).rules("add", {
							required:true,							
					 	});
	   }
	}
	function remove_box()
	{
		var boxCount=document.getElementById('boxLength').value;
		if(boxCount <=2)
		{
			document.getElementById('removeBtn').style.display='none';
		}
		$('#main'+boxCount).remove();
		document.getElementById('boxLength').value=(document.getElementById('boxLength').value-1);
		
	}
	function checkValidation()
	{
		//alert(document.getElementById('TradeChecks').checked);
		if(document.getElementById('OfficerChecks').checked==true) 
		{
			$("#TradeChecks").rules("add", {
			required:true,
			
			});
			$("#ProvincialChecks").rules("add", {
			required:true,
			
			});
			
			if(document.getElementById('TradeChecks').checked==true) 
			{
				$("#trade-name").rules("add", {
				required:true,
				messages: {
				required: "Please Enter Name"
				}
				});
				$("#type-of-business").rules("add", {
				required:true,
				messages: {
				required: "Please Enter Business Type"
				}
				});
		 
			}
			else
			{
				$('#trade-name').rules("remove");
				$('#type-of-business').rules("remove");
			}
			if(document.getElementById('ProvincialChecks').checked==true) 
			{
				$("#province-state").rules("add", {
				required:true,
				messages: {
				required: "Please Select State or Province"
				}
				});
			}
			else
			{
				$('#province-state').rules("remove");
			}
		}
		
		
		

	}

	$('body').on('change',"input[name='reminderas']",function(){
		if ($(this).val() == 'mail' || $(this).val() == 'mail/text' || $(this).val() == 'text')
		{
			$(".cc_paralegal").show();
		}
		else
		{
			$(".cc_paralegal").hide();
		}
	});
	
	</script>
	<!-- END JAVASCRIPTS -->   
	<iframe width=172px height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"> </iframe>
</body>
<!-- END BODY -->
</html>