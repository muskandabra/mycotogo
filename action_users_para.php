<?php include_once("private/settings.php");
include_once("private/checkusersession.php");
include_once(PATH."classes/User.php");
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title><?php echo SITE_NAME;?> | Add/Edit User</title>
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
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />
	<link rel="stylesheet" type="text/css" href="assets/plugins/chosen-bootstrap/chosen/chosen.css" />
    <link href="assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" />
	<link href="assets/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet" />
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
</head>
<!-- END HEAD -->
<?php
$user_id = 0;
$firstname = '';
$lastname = '';
$useremail = '';
$userstatus_id = '';
$errMessage = '';
$mode = 'ADD';
$companyname='';
$companyaddress='';
$fax='';
$phone='';
$password='';
$objUser = new User();
if(isset($_POST['actionprocess']) && $_POST['actionprocess']=="addusers_do")
{
	if($_POST['firstname']!="" && $_POST['usertype_id']!='' && $_POST['password']!='' && $_POST['useremail']!='')
	{
		//print_r($_POST);
		//echo $_POST['usertype_id'];
		$usertype=(explode(",",$_POST['usertype_id']));
		$usertype= $usertype[0];
		$objUser = new User();
		$objUser->useremail = $_POST['useremail'];
		if($objUser->isFound() == 0)
		{
			$objUser->firstname = $_POST['firstname'];
			$objUser->lastname = $_POST['lastname'];
			$objUser->password = $_POST['rpassword'];
			$objUser->usertype_id = $usertype[0];
			$objUser->companyname=$_POST['companyname'];
			$objUser->companyaddress=$_POST['companyaddress'];
			$objUser->fax=$_POST['fax'];
			$objUser->phone=$_POST['phone'];
			$objUser->addUser();
			
			print "<script>window.location='para_user.php?msg=done'</script>";
		}
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$useremail = $_POST['useremail'];
		$errMessage="[User Already Exists]";
	}
	else
	{
		$errMessage="[Invalid Data]";
	}
}

if(isset($_POST['actionprocess']) && $_POST['actionprocess']=='editusers_do')
{
	if(trim($_POST['firstname'])!='' && $_POST['user_id'] != '' )
	{
		$objUser = new User();
		$objUser->useremail = $_POST['useremail'];
		$objUser->user_id = $_POST['user_id'];
		//$usertype=(explode(",",$_POST['usertype_id']));
		//$usertype= $usertype[0];
		
		if($objUser->isFound() == 0)
		{
			$objUser->firstname = $_POST['firstname'];
			$objUser->lastname = $_POST['lastname'];
			$objUser->useremail = $_POST['useremail'];
			$objUser->userstatus_id = $_POST['userstatus_id'];
			//$objUser->usertype_id = $usertype[0];
			$objUser->companyname=$_POST['companyname'];
			$objUser->companyaddress=$_POST['companyaddress'];
			$objUser->fax=$_POST['fax'];
			$objUser->phone=$_POST['phone'];
			
			if(isset($_POST['rpassword']) && $_POST['rpassword']!='' && isset($_POST['old_password']) && $_POST['old_password']!='' ) 
			{
				if($_POST['rpassword']!= $_POST['old_password'])
				{
					$objUser->password = $_POST['password'];
				}
			}
			$objUser->editUser();
			//die;
			print "<script>window.location='para_user.php?msg=done'</script>";
			exit();
		}
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$useremail = $_POST['useremail'];
		$errMessage="[User Already Exists]";
	}
	else
	{
		$errMessage="[Invalid Data]";
	}

}

if(isset($_GET['delid']) && $_GET['delid']!='')
{
	$user_id=base64_decode($_GET['delid']);
	if($_GET['actionprocess']=='users')
	{
		$sqldel="Delete from tbl_user where  user_id='".$user_id."'";
		mysqli_query($dbconnection,$sqldel);
		
		$delete="DELETE tbl_consumermaster , tbl_consumeruser  FROM tbl_consumermaster  INNER JOIN tbl_consumeruser  
			WHERE tbl_consumermaster.consumer_id= tbl_consumeruser.consumer_id and tbl_consumermaster.user_id = '".$user_id."'";
		mysqli_query($dbconnection,$delete);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_user';
		$objUtility->datatableidField ='module_id';
		$objUtility->dataId=$user_id;
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->usertype=$_SESSION['usertype'];
		$objUtility->action='Deleted User';
		$objUtility->description='Deleted User Info';
		$objUtility->logTrack();
		print "<script>window.location='para_user.php?msg=done'</script>";
	}
}

if(isset($_GET['user']) && $_GET['user']!="")
{
	$mode = 'EDIT';
	$user_id =  base64_decode($_GET['user']);
	$objUser = new User();
	$objUser->user_id = $user_id;
	$res = $objUser->selectUserPara();
	$row=mysqli_fetch_object($res);
	//print_r($row);
	//die;
	$firstname = $row->firstname;
	$lastname = $row->lastname;
	$password=$row->password;
	$useremail = $row->useremail;
	$userstatus_id = $row->userstatus_id;
	$usertype=$row->usertype_id;
	$companyname=$row->companynamemaster;	
	$companyaddress=$row->companyaddress;
	$fax=$row->fax;
	if (empty($row->phone))
	{
		$phone=$row->companycellphonessmaster;
	}
	else
	{
		$phone=$row->phone;
	}

}

else if (isset($_POST['user_id']) && $_POST['user_id']!=0 )
{
	$mode = 'REEDIT';
	$user_id =  $_POST['user_id'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$useremail = $_POST['useremail'];
	$userstatus_id = $_POST['userstatus_id'];
	//$usertype = $_POST['usertype'];
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
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN PAGE CONTAINER-->
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->   
				<div class="row-fluid">
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
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption"><i class="icon-edit"></i>Add/Edit Users</div>
							</div>
							<div class="portlet-body form">
								<!-- BEGIN FORM-->
								<form action="action_users_para.php" id="form_sample_1" name="form_users" method="post" class="form-horizontal">
									<input type="hidden" name="user_id" id="user_id" value="<?php echo ($user_id!=0?$user_id:'0');?>">
									<?php 
									if($mode=='EDIT' || $mode=='REEDIT')
									{ ?>
										<input type="hidden" name="actionprocess" id="actionprocess" value="editusers_do">
										<?php
									}
									else
									{ ?>
										<input type="hidden" name="actionprocess" id="actionprocess" value="addusers_do">
										<?php
										
									}
									
									?>
									<input type="hidden" name="group_id" data-required="1" value="2" class="span6 m-wrap"/>
									<!-- <input type="hidden" name="usertype" value="<?php if(isset($usertype) && $usertype!='')echo $usertype;?>"/> -->
									<div class="alert alert-error hide" 
									<?php
										if ($errMessage!='')
											echo('style="display: block;">');
									?>>
										<button class="close" data-dismiss="alert"></button>
										You have some form errors. Please check below.
										<?php echo $errMessage;?>
									</div>
									<!-- <div class="alert alert-success hide">
										<button class="close" data-dismiss="alert"></button>
										Your form validation is successful!
									</div>
									-->
									
									<div class="control-group">
										<label class="control-label">First Name<span class="required">*</span></label>
										<div class="controls">
											<input type="text" name="firstname" id="firstname" data-required="1" value="<?php echo $firstname;?>" class="span6 m-wrap"/>
										</div>
									</div>
									
									<div class="control-group">
										<label class="control-label">Last Name<span class="required">*</span></label>
										<div class="controls">
											<input type="text" name="lastname" id="" data-required="1" value="<?php echo $lastname;?>" class="span6 m-wrap"/>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Email<span class="required">*</span></label>
										<div class="controls">
											<input type="text" name="useremail" id="useremail" data-required="1" value="<?php echo $useremail;?>" class="span6 m-wrap"/>
										</div>
									</div>  
  									
									<div class="control-group">
										<label class="control-label">Password<span class="required">*</span></label>
										<div class="controls">
											<input type="password" class="span6 m-wrap" name="password" id="password" value="<?php echo $password;?>"/>
									
											 	<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>
										
											<span class="help-inline">Provide your password</span>
										</div>
									</div>
									<div class="control-group">
										<label class="control-label">Confirm Password<span class="required">*</span></label>
										<div class="controls">
											<input type="password" class="span6 m-wrap" name="rpassword" id="rpassword" value="<?php echo $password;?>"/>
											<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>
											<span class="help-inline">Confirm your password</span>
										</div>
									</div>
									<input type="hidden" class="span6 m-wrap" name="old_password" id="" value="<?php echo $password;?>"/>
                                    <?php 
										if($mode=='EDIT')
										{ ?>										
											<div class="control-group">
												<label class="control-label">Status<span class="required">*</span></label>
												<div class="controls">
													<select class="span6 m-wrap" name="userstatus_id">
														<?php $sql="select * from tbl_userstatus";
														$res=mysqli_query($dbconnection,$sql);
														if(mysqli_num_rows($res)>0)
														{
															while($rowStatus=mysqli_fetch_object($res))
															{?>
																<option value="<?php echo $rowStatus->userstatus_id;?>" <?php if($userstatus_id == $rowStatus->userstatus_id){echo "selected";}?>><?php echo $rowStatus->userstatus;?></option>
															<?php
															}
														}?>
													</select>
												</div>
											</div>
										
										<?php }?>
										<!-- <div class="control-group">
											<label class="control-label">User Type<span class="required">*</span></label>
											<div class="controls">
											
											<select class="span6 m-wrap" name="usertype_id" id="usertype_id" onchange="return usertypevalue(this.value);" >
											<option value="">--SELECT--</option>
											<?php $res=$objUser->showUserType();
											while($row=mysqli_fetch_object($res))
												{
													if($usertype=='7')
													{
														?> <option value="<?php echo $row->usertype_id .','.$row->usertype; ?>" <?php if(isset($usertype) && $usertype==$row->usertype_id){echo "selected=selected";}?>><?php echo $row->usertype; ?></option> <?php
													}
													else
													{
														if($row->usertype_id!='7')
														{
															?> <option value="<?php echo $row->usertype_id .','.$row->usertype; ?>" <?php if(isset($usertype) && $usertype==$row->usertype_id){echo "selected=selected";}?>><?php echo $row->usertype; ?></option> <?php
														}
													}
												} ?>
											</select>
											</div>
										</div> -->
										<div id="compnyinfo" >
											<div class="control-group">
												<label class="control-label">Company Name<span class="required">*</span></label>
												<div class="controls">
													<input type="text" name="companyname" id="companyname" data-required="1" value="<?php echo $companyname;?>" class="span6 m-wrap"/>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label">Company Address<span class="required">*</span></label>
												<div class="controls">
													<input type="text" name="companyaddress" id="companyaddress" data-required="1" value="<?php echo $companyaddress;?>" class="span6 m-wrap"/>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label">Fax</label>
												<div class="controls">
													<input type="text" name="fax" id="fax" data-required="1" value="<?php echo $fax;?>" class="span6 m-wrap"/>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label">Phone<span class="required">*</span></label>
												<div class="controls">
													<input type="text" name="phone" id="phone" data-required="1" value="<?php echo $phone;?>" class="span6 m-wrap"/>
												</div>
											</div>
											
										</div>
									<div class="form-actions">
										<button type="submit" class="btn blue">Save</button>
										<button type="button" class="btn" onClick="window.location='para_user.php'">Cancel</button>
									</div>
                                    <div class="row-fluid">                                   
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
   <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/form-validation.js"></script> 
    <script src="assets/scripts/form-components.js"></script>
    <script src="assets/scripts/form-fileupload.js"></script>
	<!-- END PAGE LEVEL STYLES -->    
	<script>
		jQuery(document).ready(function() {   
			App.init();
			FormValidation.init();
			var test= $('#usertype_id').val();
			if(test!='' && test != undefined)
			{	
				var data=test.split(",");
				var userTypeValue=data[1];
				var splitValue=userTypeValue.split("-");
				var checkValue=splitValue[1];
				if(checkValue!='Internal')
				{
					$('#compnyinfo').show();
					$("#companyname").rules("add", {
						required:true,
					});
				
					$("#companyaddress").rules("add", {
						required:true,
					});
					// $("#fax").rules("add", {
						// required:true,
					// });
					$("#phone").rules("add", {
						required:true,
					});
				}
				
				else
				{
					$("#companyname").rules("remove");
					document.getElementById("companyname").value = "";
					$("#companyaddress").rules("remove");
					document.getElementById("companyaddress").value = "";
					//$("#fax").rules("remove");
					document.getElementById("fax").value = "";
					$("#phone").rules("remove");
					document.getElementById("phone").value = "";
					$('#compnyinfo').hide();
				}
			   // initiate layout and plugins
			}			
			//FormComponents.init();

			$(".showHidePassword").on('click', function(e) {
			    e.preventDefault();

			    // get input group of clicked link
			    var input_group = $(this).parent('.controls');


			    // find the input, within the input group
			    var input = input_group.find('input.span6');


			    // find the icon, within the input group
			    var icon = $(this).find('i');

			    // toggle field type
			    input.prop('type', input.attr("type") === "text" ? 'password' : 'text')
			
			    // toggle icon class
			    icon.toggleClass('fa-eye-slash fa-eye');
			 });

		});
		
		function usertypevalue(val)
		{
			//alert(val);
			if(val=='')
			{
				$('#compnyinfo').hide();
				$("#companyname").rules("remove");
				document.getElementById("companyname").value = "";
				$("#companyaddress").rules("remove");
				document.getElementById("companyaddress").value = "";
				//$("#fax").rules("remove");
				document.getElementById("fax").value = "";
				$("#phone").rules("remove");
				document.getElementById("phone").value = "";
			}
			var data=val.split(",");
			var userTypeValue=data[1];
			var splitValue=userTypeValue.split("-");
			var checkValue=splitValue[1];
			if(checkValue!='Internal')
			{
				$('#compnyinfo').show();
				$("#companyname").rules("add", {
					required:true,
				});
			
				$("#companyaddress").rules("add", {
					required:true,
				});
				// $("#fax").rules("add", {
					// required:true,
				// });
				$("#phone").rules("add", {
					required:true,
				});
			}
			else
			{
				$("#companyname").rules("remove");
				document.getElementById("companyname").value = "";
				$("#companyaddress").rules("remove");
				document.getElementById("companyaddress").value = "";
				$("#fax").rules("remove");
				document.getElementById("fax").value = "";
				$("#phone").rules("remove");
				document.getElementById("phone").value = "";
				$('#compnyinfo').hide();
			}
		}
	</script>
	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>