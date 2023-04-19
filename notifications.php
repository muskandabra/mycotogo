<?php
include_once("private/settings.php");
include_once("classes/clsNotification.php");
include_once(PATH."classes/clsConsumer.php");
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title><?php echo SITE_NAME;?> | Notification</title>
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
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css"/>
</head>
<style>
.slimScrollDiv {
    height: 500px !important;
}
.tab-content > .active, .pill-content > .active {
    display: block;
    height: 500px !important;
}
.scroller {
    height: 500px !important;
}
.control-label
{
	padding-top: 2px !important;
	line-height: 1 !important;
}
#Checksothers{
	margin:0!important;
}
#txtNewsletterDate {
    float: left;
    width: 88%;
}

/*#adcorp_dt {
    float: left;
    width: 88%;
}
*/

</style>
<?php  $user_id='';

$consumer_id = '';

if(isset($_SESSION['bookdetail']) && $_SESSION['bookdetail']!='')
$consumer_id = base64_decode($_SESSION['bookdetail']);
$notificationObj= new Notification();
$consumerObj= new Consumer();
$userObj= new User();
$adminDel = $userObj->selectAdmin();
$user_id=$_SESSION['sessuserid'];
$notificationObj->user_id = $user_id;
$Reminder_date = date('Y-m-d');
$msg='';
$consumer_fileno='';
$notificationstatus='';
$errMessage='';
$consumer_id='';
$checkedValues='';

if(isset($_POST['searchBtn']))
{
	$notificationObj->consumer_fileno=$_POST['file_no'];
	$notificationObj->user_id = $_SESSION['sessuserid'];
	if($_POST['status']!='all')
	{
		$notificationObj->notificationstatus=$_POST['status'];
	}		
	$consumer_fileno=$_POST['file_no'];
	$notificationstatus=$_POST['status'];
}

if(isset($_POST['finish']) && $_POST['finish']!='')
{
	
	if(isset($_POST['Reminder_date']) && $_POST['Reminder_date']!='')
	{
		
		if(isset($_POST['reminderinfo']) && $_POST['reminderinfo']!='')
		{
			$remiderinfo	=	explode(',',$_POST['reminderinfo']);
			$consumer_id=$remiderinfo[0];	
			$code=$remiderinfo[1];
		}
		else if(!isset($_POST['reminderinfo']))
		{
			$res=$notificationObj->showUserNotification();	
			if (mysqli_num_rows($res) > 0)
			{
				$consumerid=mysqli_fetch_object($res);
				$consumer_id=$consumerid->consumer_id;	
				$code=$consumerid->consumer_fileno;
			}
			else
			{
				$code = '';
			}
		}
		$consumer_id = $_POST['checkedValues'];
		if($consumer_id!='')
		{
			$reminder_date	=	date('Y-m-d',strtotime($_POST['Reminder_date']));
			$notificationstatus='pending';
			$notificationObj->notificationdate=$reminder_date;
			$notificationObj->notificationdescription= $_POST['description1'];
			$notificationObj->notificationcreatedby=$_SESSION['usertype'];
			$notificationObj->created_id=$_SESSION['sessuserid'];
			$notificationObj->notification_category_id=0;
			$notificationObj->parent_id=0;
			$notificationObj->notificationstatus=$notificationstatus;
			$notificationObj->usertype=$_SESSION['usertype'];
			$notificationObj->user_id=$_SESSION['sessuserid'];
			// $notificationObj->consumer_fileno=$code;
			$consumer_id = explode(',',$_POST['checkedValues']);
			$userPhone	=array();
			$fetchadmin	=	mysqli_fetch_object($adminDel);
			if($fetchadmin->contactno!='')
				$userPhone[]	=	$fetchadmin->contactno;
			foreach($consumer_id as $Ids)
			{
				$notificationObj->consumer_id=$Ids;
				$consumerObj->consumer_id=$Ids;
				$qry	=	$consumerObj->selectConsumer();
				// $qry	=	mysql_query($consumertest);
				$data	=	mysqli_fetch_object($qry);
				$userEmail	=	$data->useremail;
				$companyworkphone	=	$data->companycellphone;

				$notificationObj->cc_paralegal  = $_POST['is_cc_paralegal'];
					
				$notificationObj->useremail=$userEmail;
				$notificationObj->message_format = '';
				if(isset($_POST['isemail']) && $_POST['isemail']!='0')
					$notificationObj->message_format= 'mail';
				if(isset($_POST['issms']) && $_POST['issms']!='0')
					$notificationObj->message_format= 'text';

				if(isset($_POST['issms']) && $_POST['issms']!='0' && isset($_POST['isemail']) && $_POST['isemail']!='0' )
					$notificationObj->message_format= 'mail/text';

				if (empty($notificationObj->message_format))
					$notificationObj->message_format= 'system';

				$notificationObj->add_notification();
	
				if(isset($_POST['isemail']) && $_POST['isemail']!='0')
					//$notificationObj->sendMailNotification();
				if(isset($_POST['issms']) && $_POST['issms']!='0')
				{
					if($companyworkphone!='')
					{
						$con	=	explode('-',$companyworkphone);
						if(count($con)>1)
						{
							$cellcontact	=	'+'.$con['0'].$con['1'].$con['2'].$con['3'];
						}
						else
						{
							$cellcontact	=	$companyworkphone;
						}
						$userPhone[]	=	$cellcontact;
					}
					
				}
			}
			if(isset($_POST['issms']) && $_POST['issms']!='0')
			{
				$notificationObj->companyworkphone=$userPhone;
				//$notificationObj->sendSmsNotification();
			}
			print "<script>window.location='notifications.php?msg=Added#tab_1_3'</script>";
		}
		else
		{
			$errMessage='Mismatch';
			print "<script>window.location='notifications.php?err=PasswordError#tab_1_3'</script>";
		}
	}
	else
	{
		$errMessage='Mismatch';
		print "<script>window.location='notifications.php?err=PasswordError#tab_1_3'</script>";
	}
}

if(isset($_POST['addNotification']))
{
	$notificationObj->template_title=$_POST['notificationName'];
	$notificationObj->template_description= $_POST['notificationDes'];
	$notificationObj->user_id=$_SESSION['sessuserid'];
	$notificationObj->usertype=$_SESSION['usertype'];
	$notificationObj->add_notificationTemplate();

	if(isset($_GET['temp']))
	{
		print "<script>window.location='templates.php'</script>";	
	}
	else
	{
		print "<script>window.location='notifications.php?msg=Added#tab_1_4'</script>";	
	}
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
	<div class="page-container row-fluid">
		<!-- BEGIN SIDEBAR -->
		<?php include("elements/left.php");?><!-- END SIDEBAR -->
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
						<h3 class="page-title">
							Notifications <small></small>
						</h3>
					</div>
				</div>
				<div class="row-fluid profile">
					<div class="portlet-body">
					<!--BEGIN TABS-->
						<div class="tabbable tabbable-custom">

							<ul class="nav nav-tabs">
								<li class="active" id="tab_1"><a href="#tab_1_1" data-toggle="tab">All Reminders</a></li>
								<li id="tab_2"><a href="#tab_1_2"  data-toggle="tab">Today's Reminders</a></li>
								<?php $hreftab='#tab_1_3'; ?>
								<li id="tab_3"><a href="<?php echo $hreftab;?>" data-toggle="tab">Add Reminder</a></li>
								<li id="tab_4"><a href="#tab_1_4" data-toggle="tab">Add Template</a></li>
																
							</ul>

							<div class="tab-content notificationforcss">
								
								<?php 
								if(isset($_GET['msg']))
								{ ?>
									<div class="alert alert-success">
										<button data-dismiss="alert" class="close" style="float: right;"></button>
										<strong>Success!</strong> 
										<?php if($_GET['msg']=='del'){echo "The record has been deleted";} elseif ($_GET['msg']=='edit') {echo "The record has been updated";}else{echo "The record has been added";}
										?>
									</div><?php 
								} ?>

								<div class="tab-pane active" id="tab_1_1">
										<div class="scroller" data-height="150px" data-always-visible="1" data-rail-visible="0">
											<!-- BEGIN PAGE HEADER-->
											<div class="row-fluid">
												<div class="span12">
													<!-- BEGIN EXAMPLE TABLE PORTLET-->
													<div class="portlet box green">
														<div class="portlet-title">
															<div class="caption"><i class="icon-bell"></i>All Reminders</div>
														</div>
														<div class="portlet-body">
															<form action="" class="horizontal-form" method="POST" id="form_sample_1" >
																<div class="alert alert-error" id="fileNo_error" style="display:none;">
																	<button class="close" data-dismiss="alert"></button>
																		You have some form errors.Please enter File no.
																</div>
																<div class="alert alert-error" id="status_error" style="display:none;">
																	<button class="close" data-dismiss="alert"></button>
																		You have some form errors.Please select status.
																</div>
																<div class="row-fluid">
																	<div class="span6 ">
																		<div class="control-group">
																			<label class="control-label" for="file_no">Company Name.</label>
																			<div class="controls">
																				<input type="text" name="file_no" class="m-wrap medium" placeholder="Company Name" id="fileNo" value="<?php echo $consumer_fileno;?>">
																			</div>
																		</div>
																	</div>
																	<!--/span-->
																	<div class="span5 ">
																		<div class="span3 ">
																			<div class="control-group">
																				<label class="control-label" for="status">Status</label>
																				<div class="controls">
																					<select  class="m-wrap small" name="status" id="notification_status" >
																					<option value="">-Select Status-</option>
																					<option value="all" <?php if($notificationstatus=='all') { echo 'selected';}?>>All</option>
																					<option value="pending" <?php if($notificationstatus=='pending') { echo 'selected';}?>>Pending</option>
																					<option value="completed" <?php if($notificationstatus=='completed') { echo 'selected';}?>>Completed</option>
																				</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="controls">
																		<input id="ARsearch" type="button" name="searchBtn" class="btn green" value="Search"/>
																	</div>
																	<div class="controls">
																		<input id="ClrSearch" type="button" name="clearBtn" class="btn green" value="Clear"/>
																	</div>
																	<!--/span-->
																</div>
															</form>
															<div id="rem_data1">
																<table class="table table-striped table-bordered table-hover" id="sample_1">
																	<thead>
																		<tr>
																			<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
																			<th style="width:10%;">File Number</th>
																			<th style="width:20%;">Company Name</th>
																			<th style="width:40%;" class="hidden-480">Description</th>
																			<th style="width:10%;" class="hidden-480">Reminder</th>
																			<th style="width:10%;" class="hidden-480">Status</th>
																			
																		</tr>
																	</thead>
																	<tbody>
																	<?php if($user_id!='')
																		{
																			if($_SESSION['usertype']=='Consumer')
																			{
																				$notificationObj->consumer_id= $consumer_id;								
																				$res=$notificationObj->showUserNotification();
																			}
																			else
																			{
																				$res=$notificationObj->shownotification();
																			}
																		}
																		$srno=1;
																		if(mysqli_num_rows($res)>0)
																		{
																			while($fetch=mysqli_fetch_object($res))
																			{	?>
																				<tr class="odd gradeX">
																					<td><input type="checkbox" class="checkboxes" value="1" /></td>
																					<td><?php echo $fetch->consumer_fileno;?></td>
																					<?php $company_name=rtrim($fetch->companyname,',');  ?>
																					<td ><?php echo $company_name?></td>
																					<td class="hidden-480"><?php   echo $fetch->notificationdescription;  ?></td>
																					<td class="hidden-480"><?php echo $fetch->notificationdate; ?></td>
																					<td style="text-transform: capitalize;">
																					<span class="btn <?php if($fetch->notificationstatus=='completed') {echo 'green';} else{echo 'yellow';}?> mini"><?php echo $fetch->notificationstatus;?></span>
																					<?php
																					if (strtolower($fetch->notificationcreatedby) == 'consumer' )
																					{
																						?>
																						</br>
																						<a class="btn blue mini" href="editnotification.php?code=<?php echo base64_encode($fetch->notification_id);?>&action=edit">Edit</a>
																					<?php
																					}
																					?>
																					</td>
																				</tr>
																				<?php 
																			} 
																		}	?>
																		</tbody>
																</table>
															</div>
														</div>
													</div>
													<!-- END EXAMPLE TABLE PORTLET-->
												</div>
											</div>
										</div>
								</div>

								<div class="tab-pane" id="tab_1_2">
									<div class="scroller" data-height="150px" data-always-visible="1" data-rail-visible="0">
										<div class="row-fluid">										
											<div class="span12 responsive" data-tablet="span12 fix-offset" data-desktop="span12">
												<!-- BEGIN EXAMPLE TABLE PORTLET-->
												<div class="portlet box purple">
													<div class="portlet-title" >
														<div class="caption"><i class="icon-cogs"></i>Today's Reminders</div>
														<div class="actions" style="display:none;">
															<a href="#" class="btn green"><i class="icon-plus"></i> Add</a>
															<a href="#" class="btn yellow"><i class="icon-print"></i> Print</a>
														</div>
													</div>
													<div id="rem_data3">
													<div class="portlet-body">
														<table class="table table-striped table-bordered table-hover" id="sample_3">
															<thead>
																<tr>
																	<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes" /></th>
																	<th>Company Number</th>
																	<th class="hidden-480">Reminder</th>
																	<th class="hidden-480">Description</th>
																</tr>
															</thead>
															<tbody>
															<?php $notificationObj->today = "today";
																$notificationObj->notificationstatus='pending';
																if($user_id!='')
																{
																	if($_SESSION['usertype']=='Consumer')
																	{
																		$notificationObj->consumer_id= $consumer_id;																		$res=$notificationObj->showUserNotification();
																	}
																	else
																	{
																		$res=$notificationObj->shownotification();
																	}
																}
																$srno=1;
																if(mysqli_num_rows($res)>0)
																{
																	while($fetch=mysqli_fetch_object($res))
																	{ ?>	
																		<tr class="odd gradeX">
																			<td><input type="checkbox" class="checkboxes" value="1" /></td>
																			<td><?php echo $fetch->consumer_fileno;?></td>
																			<td class="hidden-480"><a href="editnotification.php?code=<?php echo base64_encode($fetch->notification_id);?>&action=edit"><?php echo $fetch->notificationdate; ?></a></td>
																			<td><?php echo $fetch->notificationdescription;  ?></td>
																			
																		</tr>
																<?php } }?>
																</tbody>
														</table>
													</div>
												</div>
												</div>
												<!-- END EXAMPLE TABLE PORTLET-->
											</div>
										</div>
										<!-- END PAGE CONTENT-->
									</div>
								</div>

								<?php $hreftab='tab_1_3';?>			 
							
									<div class="tab-pane" id="<?php echo $hreftab; ?>">
									<?php 
									$notificationObj->today = "";
									$notificationObj->notificationstatus='';?>
									<div class="scroller" data-height="150px" data-always-visible="1" data-rail-visible="0">
									<!-- BEGIN PAGE HEADER-->
									<div class="row-fluid">
										<div class="span12">
											<!-- BEGIN EXAMPLE TABLE PORTLET-->
											<div class="portlet box yellow">
												<div class="portlet-title">
													<div class="caption addremind"><i class="icon-bell"></i>All Companies</div>
												</div>
												
												<div class="portlet-body">
													<div class="row-fluid">
													<div class="span6 span6addrem">
														<div class="control-group">
															
															<!-- <label class="control-label" for="file_no">File Number</label> -->
															
															<div class="controls">
																	<input type="text" name="adfile_no" class="m-wrap medium" placeholder="File Number" id="adfileNo" value="">
																</div>
																
														</div>
													</div>
													<div class="span6 span6addrem">
														<div class="control-group">
															
														<!-- 	<label class="control-label" for="file_no">Company Name.</label> -->
															
															<div class="controls">
																<input type="text" name="adname" class="m-wrap medium" placeholder="Company Name" id="adname" value="">
															</div>	
														</div>
													</div>
													<div class="span6 span6addrem">
														<div class="control-group">						<label class="control-label" for="file_no">Incorporation Date</label>

															
															<div class="controls">
																<!-- <input type="date" data-date-format="yyyy/mm/dd" name="adcorpdt" class="m-wrap medium" placeholder="InCorporation Date" id="adcorp_dt" value=""> -->

																<input class="inputbox datepicker" type="text" name="adcorpdt" value="" id="adcorp_dt" />

																<!-- <input class="inputbox" value="<?php //echo date('Y/m/d'); ?>" type="text" name="adcorpdt" id="adcorp_dt" />
																&nbsp;<a href="javascript:void(0)" style="top: 2px; left: 189px;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.adminForm.adcorp_dt);return false;" hidefocus><img class="PopcalTrigger" align="absmiddle" src="<?php //echo URL?>img/calander.png" width="22" height="18" border="0" alt=""></a> -->
															</div>	

														</div>
													</div>
																	<!--/span-->		
													<div class="controls controlsaddrem">
														<input type="button" id="adsearchBtn" name="adsearchBtn" class="btn green" value="Search"/>
													</div>
													<div class="controls controlsaddrem">
														<input type="button" id="adclearBtn" name="adclearBtn" class="btn green" value="Clear"/>
													</div>	
													<!--/span-->
												</div>
												<div id="rem_data">
													<table class="table table-striped table-bordered table-hover selected-checkbox" id="sample_2">
														<thead>
															<tr>
																<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
																<th style="width:8px;"><input type="checkbox" class="allcheckboxes" /></th>
																<th>File Number</th>
																<th class="hidden-480">Company Contact</th>
																<th class="hidden-480">Company Name</th>
																<th class="hidden-480">InCorporation Date</th>
																<th class="hidden-480"></th>
																
															</tr>
														</thead>
														<tbody>
														<?php 
															$objConsumer = new Consumer();
															$objConsumer->created_user_id=$user_id;
															$res = $objConsumer->selectConsumer();
															// $res =  mysql_query($res);
															$srno=1;
															if(mysqli_num_rows($res)>0)
															{
																while($fetch=mysqli_fetch_object($res))
																{	
																	// print_r($fetch);

																	?>
																	<tr class="odd gradeX">
																		<td><input type="checkbox" class="checkboxes" value="1" /></td>
																		<td><input type="checkbox" class="checkboxes select-item" id="<?php echo $fetch->consumer_id;?>" onclick="updateCheckedStatus(this);"/></td>
																		<td><?php echo $fetch->consumer_fileno;?></td>
																		<td class="hidden-480"><?php   echo $fetch->companycontact;  ?></td>
																		<?php $company_name=rtrim($fetch->usercname,',');  ?>
																		<td class="hidden-480"><?php   echo $company_name;  ?></td>
																		<td class="hidden-480"><?php if (isset($fetch->updatedDate)) echo $fetch->updatedDate; ?></td>
																		  <td style="text-transform: capitalize;"><!-- <?php echo $fetch->companyaddress;?> --></td> 
																	</tr>
																	<?php 
																} 
															}	?>
															</tbody>
													</table>
												</div>
												</div>
											</div>
											<!-- END EXAMPLE TABLE PORTLET-->
										</div>
									</div>

									<?php
								$res = $objConsumer->selectConsumerFileNo();
								$selectoption='<option value="">Select File No</option>';
								$selectoptionTemplate='<option value="">Select Template</option>';
								if(mysqli_num_rows($res)>0)
								{
									while($fetchfileNo=mysqli_fetch_object($res))
									{
										$selectoption	=	$selectoption.'<option value="'.$fetchfileNo->consumer_id.','.$fetchfileNo->consumer_fileno.'">'.$fetchfileNo->consumer_fileno.'</option>';
									}
								}
								
								$selectNotificationTemplate = $notificationObj->selectNotificationTemplate();
								if(mysqli_num_rows($selectNotificationTemplate)>0)
								{
									while($fetchNotification =mysqli_fetch_object($selectNotificationTemplate))
									{
										$selectoptionTemplate	=	$selectoptionTemplate.'<option  value="'.$fetchNotification->notification_template_id.'">'.ucfirst($fetchNotification->template_title).'</option>';
									}
								}
								?>

									<form id="form_sample_1R"  class="form-horizontal form1" method="POST"  name="adminForm">
									<input type='hidden' name="checkedValues" id="checkedValues" value="<?php echo $checkedValues;?>">
										<div class="row-fluid">
											<div class="portlet box yellow notification" >
												<div class="portlet-title">
														<div class="caption" id="reminder1">
															Add Reminder 
														</div>
													</div>
													<div class="portlet-body" style="display: block;">
														<div class="remain-one-half">
															
															<div class="forms control-group">
																<label class="control-label"> Use Templates</label>
																<div class="controls" >
																	
																	<?php echo '<select name="templateinfo" onchange= "getTemplate(this.value)"; class="templateinfo">'.$selectoptionTemplate.'</select>'; ?>
																</div>
															</div>
															<div class="control-group date-section">
															<label class="control-label">Due Date</label>
															<div class="controls" >
																<input class="inputbox datepicker" type="text" name="Reminder_date" value="<?php echo $Reminder_date; ?>" id="txtNewsletterDate" />

																<!-- <input class="inputbox" value="<?php //echo date('Y/m/d'); ?>" type="text" name="Reminder_date" id="txtNewsletterDate" />
																&nbsp;<a href="javascript:void(0)" style="top: 2px; left: 189px;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.adminForm.txtNewsletterDate);return false;" hidefocus><img class="PopcalTrigger" align="absmiddle" src="<?php //echo URL?>img/calander.png" width="22" height="18" border="0" alt=""></a> -->
															</div>
														</div>
													</div>	
													<div class="remain-one-half">
														<div class="forms control-group">
															<label class="control-label">Description</label>
															<div class="controls">
																<textarea name="description1" rows="3" id="notification_description" required="required"><?php if(isset($_POST['description1'])){ echo $_POST['description1'];}?></textarea>
															</div>
														</div>
													</div>
													<div class="remain-one-half">
														<div class="forms control-group">
															<label class="control-label">Sms<span class="required">*</span></label>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="issms" value="1"> Yes
																</div>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="issms" value="0" checked="checked">No
																</div>
														</div>
													</div>
													<div class="remain-one-half">
														<div class="forms control-group">
															<label class="control-label">Email<span class="required">*</span></label>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="isemail" value="1" > Yes
																</div>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="isemail" value="0" checked="checked">No
																</div>
														</div>
													</div>
													
													<div class="remain-one-half cc_paralegal" >
														<div class="forms control-group">
															<label class="control-label">CC Paralegal<span class="required">*</span></label>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="is_cc_paralegal" value="1" > Yes
																</div>
																<div class="checkbox-line">
																	<input type="radio" id="Checksothers" name="is_cc_paralegal" value="0" checked="checked">No
																</div>
														</div>
													</div>
													<div class="portlet-body">
														<div class="forms control-group">
															<input type="button" name="finish" class="btn yellow right" id="shareholderA" value="Finish" onclick="return checkValidationFile();" />
														</div>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="tab-pane" id="tab_1_4">
								<form id="form_sample_1" class="form-horizontal form1" method="POST"  name="">
									<div class="row-fluid">
										<div class="portlet box red notificationt" >
											<div class="portlet-title">
												<div class="caption" id="reminder1">
													Add Template
												</div>
												<div class="tools">
													<a class="collapse" href="javascript:;"></a>	
												</div>
											</div>
											<div class="portlet-body" style="display: block;">
												<div class="remain-one-half">
													<div class="control-group">
														<label class="control-label">Title</label>
														<div class="controls" >
															<input type="text" name="notificationName" class="span6 m-wrap" required="required"/>
														</div>
													</div>
												</div>	
												<div class="remain-one-half">
													<div class="forms control-group">
														<label class="control-label">Description</label>
														<div class="controls">
															<textarea name="notificationDes" rows="3" required="required"><?php if(isset($_POST['description1'])){ echo $_POST['description1'];}?></textarea>
														</div>
													</div>
												</div>
												<div class="portlet-body">
													<input type="submit" name="addNotification" class="btn red right" id="shareholder" value="Finish"  />
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
						</div>
					</div>
				</div>
			
			
			</div>		<!-- END PAGE CONTAINER-->
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
	<script src="assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-datepicker.js"></script>                
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-select.js"></script>

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
	<script src="assets/scripts/bootstrap-session-timeout.min.js"></script> 
	<script src="assets/scripts/session-timeout.js"></script>     
	<script>
		jQuery(document).ready(function() {       
		   App.init();
		  TableManaged.init();


		  $( ".datepicker" ).datepicker({dateFormat: 'yy-mm-dd' });
		  $("#txtNewsletterDate").datepicker("setStartDate", new Date());	    
		   $('body').on('click','#adsearchBtn',function(){
		   	//alert("1");
		   	var fileno = $('#adfileNo').val();
		   	var CompNm = $('#adname').val();
		   	var idate = $('#adcorp_dt').val();
		   	var str = "task=searchcomp&fileno="+fileno+"&compnm="+CompNm+"&idate="+idate;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{						
						//alert(response);	
						$('#rem_data').html(response);
						//$('#sample_2').DataTable({"sDom": 'lrtip'}).ajax.reload();
						$('#sample_2').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();				
						//$('#add_elements').append(response);										
					}
				});			
		   });

		    $('body').on('click','#adclearBtn',function(){
		   	//alert("1");
		   	$('#adfileNo').val("");
		   	$('#adname').val("");
		   	$('#adcorp_dt').val("");
		   	var fileno = $('#adfileNo').val();
		   	var CompNm = $('#adname').val();
		   	var idate = $('#adcorp_dt').val();
		   	var str = "task=searchcomp&fileno="+fileno+"&compnm="+CompNm+"&idate="+idate;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{						
						//alert(response);	
						$('#rem_data').html(response);
						//$('#sample_2').DataTable({"sDom": 'lrtip'}).ajax.reload();	
						$('#sample_2').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();			
						//$('#add_elements').append(response);										
					}
				});			
		   });


			jQuery('body').on('click','.allcheckboxes', function(){
            var checked = this.checked;
            $("input.select-item").each(function (index,item) {
            	if (checked == true)
            	{
            		$(this).parent('span').addClass("checked"); 
            	}
            	else
            	{
            		$(this).parent('span').removeClass("checked");
            	}
                item.checked = checked;
            });

            var items=[];
            checkedValues='';
            $("input.select-item:checked:checked").each(function (index,item) {
                items[index] = $(this).attr('id');
               // alert($(this).attr('id'));
            });
            if (items.length < 1) {
            	checkedValues = '';
			
            }else {
                checkedValues  = items.join(',');
            }
            document.getElementById("checkedValues").value = checkedValues;
			});

			$('body').on('change',"input[name='isemail']",function(){
				if ($(this).val() ==1)
				{
					//$(".cc_paralegal").show();
				}
				else
				{
					//$(".cc_paralegal").hide();
				}
			});

		    jQuery('body').on('click','.show-reminder', function(){
		    	var cust_id = $(this).text();
		    	var postData = 'task=show_reminder&cust_id='+cust_id;
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					data:postData,
					success:function(response)
					{
						//alert(response);
						$('.addremind').html('<i class="icon-bell"></i>Reminder\'s for Selected Company');
						$('#rem_data').html(response);
						$('#Back_1').remove();	
						var bvalue = $(".tab-content").find("input[name='back_2']" ).val();
						if (bvalue==undefined)
						{						
							$(".tab-content").prepend('<input id="Back_2" type="button" name="back_2" class="btn green" value="Back"/>');
						}							
						$('#sample_2').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();							
					}
				});

		    });

		    jQuery('body').on('click','#tab_2', function(){
		    	//alert("2");
		    	var postData = 'task=today_reminder';
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					data:postData,
					dataType: 'json',
					success:function(response)
					{
						//alert(response);
						//alert("3");
						$('#rem_data3').html(response);
							$('#sample_3').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]], "iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});
				$(".alert-success").remove();	
		    });

		    jQuery('body').on('click','#tab_1', function(){
		    	var fileno = $('#fileNo').val();
			   	var status = $('#notification_status').val();
			   	//alert(status);
			   	var str = "task=all_reminder&searchtext="+fileno+"&status="+status;
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					dataType: 'json',
					data:str,
					success:function(response)
					{
						//alert(response);
						//alert("3");
						$('#rem_data1').html(response);
						$('#sample_1').DataTable({ "sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});
				$(".alert-success").remove();	
		    });

		    jQuery('body').on('click','#tab_4', function(){
				$(".alert-success").remove();	
		    });

		    jQuery('body').on('click','#ClrSearch', function(){
		    	$('#fileNo').val("");
		    	$("#notification_status option:first").attr('selected','selected');
		    	var fileno = $('#fileNo').val();
			   	var status = $('#notification_status').val();
			   	//alert(status);
			   	var str = "task=all_reminder&searchtext="+fileno+"&status="+status;
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					dataType: 'json',
					data:str,
					success:function(response)
					{
						//alert(response);
						//alert("3");
						$('#rem_data1').html(response);
						$('#sample_1').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});
		    });
		    
		    jQuery('body').on('click','#tab_3', function(){
				$('.span6addrem').show();
				$('.controlsaddrem').show();	
				var fileno = $('#adfileNo').val('');
			   	var CompNm = $('#adname').val('');
			   	var idate = $('#adcorp_dt').val('');
				var fileno = $('#adfileNo').val();
			   	var CompNm = $('#adname').val();
			   	var idate = $('#adcorp_dt').val();

			   	var str = "task=searchcomp&fileno="+fileno+"&compnm="+CompNm+"&idate="+idate;
		    	var postData = 'task=screen2';
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					data:str,
					dataType: 'json',
					success:function(response)
					{
						$('#rem_data').html(response);
						$('.addremind').html('<i class="icon-bell"></i>All Companies');
						$('#Back_1').remove();
						$('#Back_2').remove();
						$('.notification').show();
						$('#checkedValues').val('');
						$('#sample_2').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});
				$(".alert-success").remove();
			});	


		    // "sDom": 'lrtip'

		    //  "sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>",

		    $('body').on('click','#ARsearch',function(){
		   	//alert("1");
		   	var fileno = $('#fileNo').val();
		   	var status = $('#notification_status').val();
		   	//alert(status);
		   	var str = "task=all_reminder&searchtext="+fileno+"&status="+status;
					//alert(str);
					$.ajax({
					type:"POST",
					dataType: 'json',
					url:"showResults.php",
					data:str,
					success:function(response)
					{						
						//alert(response);	
						$('#rem_data1').html(response);
						$('#sample_1').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();				
						//$('#add_elements').append(response);										
					}
				});			
		   });

			  jQuery('body').on('click','#shareholderA', function(){
			  	var val = $('#checkedValues').val();
			  	var notval = $('#notification_description').val();
				if(val	=='')
				{
					alert('Please Select File');
				}
				if(notval	=='')
				{
					alert('Please Fill Notification Detail');
				}
				if(val	!='' && notval	!='')
				{
	            var postData = jQuery('#form_sample_1R').serialize()+'&task=add_reminder';
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					data:postData,
					success:function(response)
					{
						//alert(response);
						$('.span6addrem').hide();
						$('.controlsaddrem').hide();
						$('#rem_data').html(response);
						$(".tab-content").prepend('<div class="alert alert-success">				<button data-dismiss="alert" class="close" style="float: right;"></button><strong>Success!</strong>"The reminder has been added"</div>');
						$('.notification').hide();
						$('.addremind').html('<i class="icon-bell"></i>Selected Company');
						$('#sample_2').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]], "iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
							//$('#sample_3').DataTable().ajax.reload();
					}
				});
				}	
			});

			  jQuery('body').on('click','#Back_2', function(){
		    	var postData = 'task=screen2';
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					dataType: 'json',
					data:postData,
					success:function(response)
					{
						$('#rem_data').html(response);
						$('.addremind').html('<i class="icon-bell"></i>Selected Company');
						$('#Back_2').remove();
						var bvalue = $(".tab-content").find("input[name='back_1']" ).val();
						if (bvalue==undefined)
						{						
							$(".tab-content").prepend('<input id="Back_1" type="button" name="back_1" class="btn green" value="Back"/>');
						}	
						$('#sample_2').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});

				jQuery('body').on('click','#Back_1', function(){
				$('.span6addrem').show();
				$('.controlsaddrem').show();					
				var fileno = $('#adfileNo').val();
			   	var CompNm = $('#adname').val();
			   	var idate = $('#adcorp_dt').val();
			   	var str = "task=searchcomp&fileno="+fileno+"&compnm="+CompNm+"&idate="+idate;
				$.ajax({
					url: 'showResults.php',
					type: 'POST',
					data:str,
					dataType: 'json',
					success:function(response)
					{
						$('#rem_data').html(response);
						$('#Back_1').remove();
						$('.notification').show();
						$('.addremind').html('<i class="icon-bell"></i>All Companies');
						$('#checkedValues').val('');
						$('#sample_2').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
						$('#sample_1').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
					}
				});
			});
		
			});

		});
	</script>
	<script>
	function checkValidationFile()
	{
		// var val = $('#checkedValues').val();
		// if(val	=='')
		// {
		// 	alert('Please Select File');
		// 	return false;
		// }
		// else
		// {
		// 	return true;
		// }
	}
		function searchFormValidation()
		{
			if(document.getElementById('fileNo').value.trim()=='')
			{
				document.getElementById('fileNo_error').style.display='block';
				document.getElementById('status_error').style.display='none';
				return false;
			}
			if(document.getElementById('notification_status').value.trim()=='')
			{
				document.getElementById('status_error').style.display='block';
				document.getElementById('fileNo_error').style.display='none';
				return false;
			}
		}
		function updateCheckedStatus(chkElement)
		{
			var checkedValues  = document.getElementById("checkedValues").value;
			if (chkElement.checked == true)
			{
				if (checkedValues == '')
					checkedValues = chkElement.id;
				else
					checkedValues = checkedValues + ',' + chkElement.id;
			}
			else
			{	
				var uncheckedValues = checkedValues.replace(chkElement.id,"");
				checkedValues = uncheckedValues;
			}
			checkedValues = checkedValues.replace(',,',',');
			document.getElementById("checkedValues").value = checkedValues;
			//alert(document.getElementById("checkedValues").value);
		}
		function getTemplate(templateId)
		{
			var task	=	'showtemplate';
			var query	=	'task='+task+'& notification_template_id='+templateId;
			$.ajax({
				type:"POST",
				url:'includes/bookInfo.php',
				data	:	query,
				success: function(response)
				{
					console.log(response);
					$('#notification_description').val(response.trim());
				}
				
			});
		}
	</script>
	<iframe width=172px height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-400px;"> </iframe>

	<script>
		jQuery(document).ready(function(){
			 jQuery('body').on('click','img.PopcalTrigger', function(){
				jQuery('body iframe').toggleClass('myiframe');
			});
		});
	</script>
</body>
<!-- END BODY -->
</html>