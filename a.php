<?php include_once("private/settings.php");

include_once("classes/User.php");

?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?> | Profile</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<link rel="stylesheet" href="assets/plugins/ajaxImageUpload/style.css" />

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

	<link href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.css" rel="stylesheet" type="text/css" />

	<link href="assets/plugins/chosen-bootstrap/chosen/chosen.css" rel="stylesheet" type="text/css" />

	<link href="assets/css/pages/profile.css" rel="stylesheet" type="text/css" />

	<!-- END PAGE LEVEL STYLES -->

	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />

	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />

	<link href="assets/css/pages/login-soft.css" rel="stylesheet" type="text/css"/>

	<link rel="shortcut icon" href="favicon.ico" />

</head>


<!-- END HEAD -->

<?php

$user_id = 0;

$firstname = '';

$lastname = '';

$contactno = '';

$registrationDate = '';

$zipcode='';

$errMessage = '';

$companyname='';

$useremail='';

$imagePath='';

$fax = '';

    $companyaddress='';
    $edit 			= 	0;
	$username 		= 	'';
	$firstname 		= 	'';
	$tblName		=	'tbl_user';
	$usernameExist 	= 	0;
	$user_id		=	'';
	$user_code		=	'';
	$lastname		=	'';
	$address		=	'';
	$state			=	'';
	$zipcode		=	'';
	$contactno		=	'';
	$usertype_id	=	'';
	$password		=	'';



if(isset($_POST['actionprocess']) && $_POST['actionprocess']=='changepassword_do')

{

	$objUser = new User();

	$objUser->user_id=$_SESSION['sessuserid'];

	$objUser->password=$_POST['password'];

	$objUser->confirmPassword=$_POST['confirmpassword'];

	$returnVal = $objUser->changePassword();

	if($returnVal == 0)

	{

		$errMessage='Password Mismatch';

		print "<script>window.location='profile.php?err=PasswordError'</script>";

	}

}



?>

<!-- BEGIN BODY -->



<body class="page-header-fixed">



	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<!-- BEGIN TOP NAVIGATION BAR -->

		

		<!-- END TOP NAVIGATION BAR -->

	</div>

	<!-- END HEADER -->

	<!-- BEGIN CONTAINER -->   

	<div class="page-container row-fluid">

		<!-- BEGIN SIDEBAR -->

		<!-- BEGIN TOP NAVIGATION BAR -->

		<?php include(PATH."elements/left.php");?>

		<!-- END TOP NAVIGATION BAR -->

		<!-- END SIDEBAR MENU -->

		</div>

		<!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content">


















			

			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->

			<!-- BEGIN PAGE CONTAINER-->

			<div class="container-fluid">






            <div class="portlet-body form">

<!-- BEGIN FORM-->

<form action="" id="frmChangePassword" name="frmChangePassword" method="post" class="form-horizontal">

    <input type="hidden" name="user_id" id="user_id" value="<?php echo ($user_id!=0?$user_id:'0');?>">

    <input type="hidden" name="actionprocess" id="actionprocess" value="changepassword_do">

    <div class="alert alert-error hide" 

    <?php

        if ($errMessage=='Password Mismatch')

            echo('style="display: block;">');

    ?>>

        <button style="float:right;" class="close" data-dismiss="alert"></button>

        You have some form errors. Please check below.

        <?php echo $errMessage;?>

    </div>

    <div class="control-group">

        <label class="control-label">Old Password<span class="required">*</span></label>

        <div class="controls">

            <input type="text" name="password" data-required="1" value="" class="span6 m-wrap"/>
            <a class = "showHidePassword" href=""><i class="fa fa-eye" aria-hidden="true"></i> </a>

        </div>

    </div>  

      

    

        <div class="control-group">

                    <label class="control-label">New Password<span class="required">*</span></label>

                    <div class="controls">

                        <input type="password" class="span6 m-wrap" name="newpassword" id="newpassword"/>
                        <a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

                        <span class="help-inline">Enter your password</span>

                    </div>

                </div>

                <div class="control-group">

                    <label class="control-label">Confirm Password<span class="required">*</span></label>

                    <div class="controls">

                        <input type="password" class="span6 m-wrap" name="confirmpassword"/>
                        <a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

                        <span class="help-inline">Confirm your password</span>

                    </div>

                </div>

    

    <div class="form-actions">

        <button type="submit" class="btn blue">Save</button>

        <button type="button" class="btn" onClick="window.location='profile.php'">Cancel</button>

    </div>

    <div class="row-fluid">

                                            

    </div>

</div>

</form>

<!-- END FORM-->

</div>






			<!-- END PAGE CONTAINER--> 

		</div>

		<!-- END PAGE -->    

	</div>

	<!-- END CONTAINER -->

	<!-- BEGIN FOOTER -->

	<!-- BEGIN FOOTER -->

		<?php include(PATH."elements/footer.php");?>

	<!-- END FOOTER -->

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

	<script src="assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>

	<script type="text/javascript" src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script> 

	<script src="assets/scripts/form-validation.js"></script> 

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		   FormValidation.init();

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

		


	</script>

	<!-- END JAVASCRIPTS -->

</body>

<!-- END BODY -->

</html>