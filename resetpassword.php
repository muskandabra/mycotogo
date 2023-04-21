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

	<title> <?php echo SITE_NAME; ?> | Login Form</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>


    <link href="assets/css/style.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>

	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>

	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<link href="assets/css/pages/login.css" rel="stylesheet" type="text/css"/>
    

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />
    <style>
     .login .content .forget-form {
      display: block; 
     }
      .login .content .m-wrap {
      border-left: 1px solid #e5e5e5 !important;
     }
    </style>

</head>

<!-- END HEAD -->

<?php

$response = '';

if (isset($_GET['token_id'])) {
		
	$token = $_GET['token_id'];
		
	//echo $token;
		
}

$objUser = new User();

    $useremail=$objUser->checktoken($token);

	//$expireddate=$objUser->checkexpiretoken($token);

	//echo $expireddate;

   // echo $useremail;

	if($useremail == ""){

	$response = ' <center> <h1 style="color:white;position:relative;top:300px"> Invalid Token </h1> </center> ';

    echo $response;

	exit;

	}
	else{

	$response = ' <center> <h4 style="position:relative;COLOR:GREEN;top:150px"> Valid Token </h4> </center> ';

	echo $response;

	}

if(isset($_POST['actionprocess']) && $_POST['actionprocess']=='changepassword_do')
{
	
	$objUser->confirmPassword = $_POST['confirmpassword'];

	$returnVal = $objUser->changePasswordToken($token);

	if($returnVal != 0 )
	{
		$response = '<center><h1 style="color:white;position:relative;top:300px">Successfully Reset Password</h1></center>';

        echo $response;

        exit;
	}

}


?>

<!-- BEGIN BODY -->

<body class="login">

	<!-- BEGIN LOGO -->

	<div class="logo" style="color: white;">

		MYCOTOGO 

	</div>

	<!-- END LOGO -->



	<div class="content">  


		<!-- BEGIN FORGOT PASSWORD FORM -->


			<h3 class="">Create New Password</h3>

            <div class="portlet-body form">

								<!-- BEGIN FORM-->

			<form action="" class="form-vertical forget-form" id="formvalidation" name="formvalidation" method="post" class="form-horizontal">
			
			<div class="alert alert-error hide" 

            <?php

	     if ($response=='[Invalid Data]')

	     echo('style="display: block;"');

         ?>>

	     <button style="float: right;" class="close" data-dismiss="alert"></button>

	      Unsuccessful to reset Password

	     <?php echo $response;?>

         </div>

			<input type="hidden" name="actionprocess" id="actionprocess" value="changepassword_do">

										<div class="control-group">

													<label class="control-label"> Password<span class="required">*</span></label>

													<div class="controls">

														<input type="password" class="span6 m-wrap" name="newpassword" id="newpassword"/>
														<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

														<span class="help-inline">Enter your password</span>

													</div>

												</div>

												<div class="control-group">

													<label class="control-label">Confirm Password<span class="required">*</span></label>

													<div class="controls">

														<input type="password" class="span6 m-wrap" name="confirmpassword" id="confirmpassword"/>
														<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

														<span class="help-inline">Confirm your password</span>

													</div>

												</div>

                                    

									<div class="form-actions">

										<button type="submit" class="btn blue">Save</button>

										<button type="button" class="btn" onClick="window.location='index.php'">Cancel</button>

									</div>

                                    <div class="row-fluid">                                           

                                    </div>

                                 </div>

								</form>

								<!-- END FORM-->

							</div>

		<!-- END FORGOT PASSWORD FORM -->

	</div>

	<!-- BEGIN COPYRIGHT -->

	<div class="copyright">

		<?php echo date('Y');?> &copy; <?php echo SITE_NAME?>

	</div>

	<!-- END COPYRIGHT -->

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

	<script src="assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<script type="text/javascript" src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>
	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js" type="text/javascript"></script>

	<script src="assets/scripts/login-soft.js" type="text/javascript"></script>    
    
    <!--script src="assets/scripts/form-validation.js"></script--> 

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



		

var FormValidation = function () {

return {

	//main function to initiate the module

	init: function () {


		var formCP = $('#formvalidation');

		var errorCP = $('.alert-error', formCP);

		var successCP = $('.alert-success', formCP);


		formCP.validate({

			errorElement: 'span', //default input error message container

			errorClass: 'help-inline', // default input error message class

			focusInvalid: false, // do not focus the last invalid input

			ignore: "",

			rules: {

				password: {

					minlength: 5,

					required: true

				},

				newpassword: {

					minlength: 8,

					required: true

				},

				confirmpassword: {

					minlength: 8,

					required: true,

					equalTo: "#newpassword"

				}

			},


			invalidHandler: function (event, validator) { //display error alert on form submit              

				successCP.hide();

				errorCP.show();

				App.scrollTo(errorCP, -200);

			},

			highlight: function (element) { // hightlight error inputs

				$(element)

					.closest('.help-inline').removeClass('ok'); // display OK icon

				$(element)

					.closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group

			},

			unhighlight: function (element) { // revert the change dony by hightlight

				$(element)

					.closest('.control-group').removeClass('error'); // set error class to the control group

			},

			success: function (label) {

				label

					.addClass('valid').addClass('help-inline ok') // mark the current input as valid and display OK icon

				.closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group

			},

			submitHandler: function (form) {

				successCP.show();

				errorCP.hide();

				form.submit();

			}

		});

	}

};


}();


</script>

</body>

<!-- END BODY -->

</html>