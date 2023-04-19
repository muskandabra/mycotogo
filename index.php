<?php include_once("private/settings.php");

include_once("classes/User.php");

include_once("classes/clsConsumer.php");

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

	<link href="assets/css/style.css?ver=<?php echo time(); ?>" rel="stylesheet" type="text/css"/>

	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>

	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>

	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>

	<!-- END GLOBAL MANDATORY STYLES -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<link href="assets/css/pages/login.css" rel="stylesheet" type="text/css"/>

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

</head>

<!-- END HEAD -->

<?php

$msg='';

$objUser = new User();

$objConsumer=new Consumer();

if(isset($_GET['activateid']) && $_GET['activateid']!="" && isset($_GET['key']))

{

	

	if($_SERVER['SERVER_NAME']=='localhost')

	{

		$id=135; // New created User_id from tbl_user 

		$key=112982;	// key for tbl_user

		

		//http://localhost/mycotogo/index.php?activateid=54&key=696478&type=7   //URL Should like this for local host..

	}

	else

	{

		$id=base64_decode($_GET['activateid']);

		$key=base64_decode($_GET['key']);

	}

	$objUser = new User();

	$objUser->user_id = $id;

	$objUser->userkey = $key;

	if($objUser->activateAccount()== 'true') 

	{	

		$msg='Congratulations! You have successfully activated your account. ';

	}

	else

	{

		$msg='You already registered';

	}



}

	

if(isset($_POST['actionprocess']) && $_POST['actionprocess']=="consumerLogin_do")

{ 

	if(isset($_POST['email']) && $_POST['email']!='')

	{

		$objUser->useremail = $_POST['email'];

		$objUser->user_id=$_POST['user_id'];

		if($objUser->isFound()>0)

		{

			$msg='Email already exits';

		}

		else

		{

			$objUser->user_id=$_POST['user_id'];

			$objUser->firstname=$_POST['firstname'];

			$objUser->lastname=$_POST['lastname'];

			$objUser->password=$_POST['rpassword'];

			$objUser->usertype_id=7;

			$objUser->userstatus_id=1;

			$objUser->isWelcome=0;

			$objUser->editUser();

			$resselect= $objUser->checkLogin();

			if(mysqli_num_rows($resselect)>0)

			{

		

				$row=mysqli_fetch_object($resselect);

				$_SESSION['sessuserid']=$row->user_id;

				$_SESSION['sessusername']=$row->username;

				$_SESSION['sessfirstname']=$row->firstname;

				$_SESSION['sesslastname']=$row->lastname;

				$_SESSION['sessuseremail']=$row->useremail;

				$_SESSION['sessuserpswd']=$row->password;

				$_SESSION['sessrights']=$row->usertype_id;



				$select=mysqli_query($dbconnection,"select * from  enum_usertype where usertype_id='".$_SESSION['sessrights']."'");

				if(mysqli_num_rows($select)>0)

				{

					$fetch=mysqli_fetch_object($select);

					$_SESSION['usertype']=$fetch->usertype;

				

				}

				$objUtility = new Utility();

				$objUtility->dataTable = 'tbl_user';

				$objUtility->dataId=$_SESSION['sessuserid'];

				$objUtility->user_id=$_SESSION['sessuserid'];

				$objUtility->usertype =$_SESSION['usertype'];

				$objUtility->action='Logged In';

				$objUtility->description= 'Logged In Username:'.$_SESSION['sessuseremail'].' || with Password:['.$_SESSION['sessuserpswd'].']';

				$objUtility->logTrack();

				

				if(isset($_POST['task']) && $_POST['task']=='video')

				{

					print "<script>window.location='videotour.php'</script>";

				}

				else if(isset($_POST['task']) && $_POST['task']=='nextstep')

				{

					print "<script>window.location='nextstep.php'</script>";

				}

				else

				{

					print "<script>window.location='dashboard.php'</script>";

				}



			}

			else

			{

				$objUtility = new Utility();

				$objUtility->dataTable = '';

				$objUtility->usertype ='';

				$objUtility->action='Trying to Log In';

				$objUtility->description= 'WARNING: HACKING for Username:['.$_POST['email'].'] || with Password:['.$_POST['rpassword'].']';

				$objUtility->logTrack();

				$msg="Please enter correct credentials";

			}			

		}

		

	}

	

}



if(isset($_POST['actionprocess']) && $_POST['actionprocess']=="login_do")

{ 

	

	if(isset($_POST['username']) && isset($_POST['username']) && $_POST['password']!="" && $_POST['password']!="")

	{

	

		$objUser->useremail = $_POST['username'];

		$objUser->password = $_POST['password'];

		$objUser->usertype ='paralegal';

		$resselect= $objUser->checkLogin(); 

		if(mysqli_num_rows($resselect)>0)

		{

			

			$row=mysqli_fetch_object($resselect);

			if($row->userstatus_id=='2')

			{

				$msg="Your Account has been deactivated";

				$objUtility = new Utility();

				$objUtility->dataTable = '';

				$objUtility->usertype='';

				$objUtility->action='Inactive';

				$objUtility->description= 'Logged In Denied of Username:['.$_POST['username'].'] || With Password: ['.$_POST['password'].']';

				$objUtility->logTrack();

			}

			else

			{

				$_SESSION['sessuserid']=$row->user_id;

				$_SESSION['sessusername']=$row->username;

				$_SESSION['sessfirstname']=$row->firstname;

				$_SESSION['sesslastname']=$row->lastname;

				$_SESSION['sessuseremail']=$row->useremail;

				$_SESSION['sessuserpswd']=$row->password;

				$_SESSION['sessrights']=$row->usertype_id;

					echo $_SESSION['sessrights'];
				echo $row->usertype_id;
				//die;

				$select=mysqli_query($dbconnection,"select * from  enum_usertype where usertype_id='".$_SESSION['sessrights']."'");

				if(mysqli_num_rows($select)>0)

				{

					$fetch=mysqli_fetch_object($select);

					$_SESSION['usertype']=$fetch->usertype;

					

				

				}

				$objUtility = new Utility();

				$objUtility->dataTable = 'tbl_user';

				$objUtility->datatableidField ='user_id';

				$objUtility->dataId=$_SESSION['sessuserid'];

				$objUtility->user_id=$_SESSION['sessuserid'];

				$objUtility->usertype =$_SESSION['usertype'];

				$objUtility->action='Logged In';

				$objUtility->description= 'Logged In Username:'.$_SESSION['sessuseremail'].' || with Password:['.$_SESSION['sessuserpswd'].']';

				$objUtility->logTrack();

				if(isset($_POST['task']) && $_POST['task']=='video')

				{

					print "<script>window.location='videotour.php'</script>";

				}

				else if(isset($_POST['task']) && $_POST['task']=='nextstep')

				{

					print "<script>window.location='nextstep.php'</script>";

				}

				else

				{

					

					$objUser->user_id = $_SESSION['sessuserid'];

					$numRows	=	$objUser->UserCountBook();

					//if(mysqli_num_rows($numRows) == 1 || $_SESSION['usertype'] == 7)

					if($_SESSION['usertype'] == 7)

					{

						print "<script>window.location='userbook.php'</script>";

					}

					else

						print "<script>window.location='dashboard.php'</script>";

				}

			}



		}

		else

		{

			$objUtility = new Utility();

			$objUtility->dataTable = '';

			$objUtility->usertype ='';

			$objUtility->action='Trying to Log In';

			$objUtility->description= 'WARNING: HACKING for Username: ['.$_POST['username'].'] with Password:['.$_POST['password'].']';

			$objUtility->logTrack();

			$msg="Please enter correct credentials";

		}

	}

}



if(isset($_POST['actionprocess']) && $_POST['actionprocess']=="forgetpassword_do")

{ 

	if(isset($_POST['email']) && isset($_POST['email']) !="")

	{

		$objUser->useremail = $_POST['email'];

		$objUser->group_id = 2; // 2 for Item Writer

		if ($objUser->resetPassword()==1)

		{

			print "<script>window.location='index.php?msg=succ'</script>";

		}

		else

		{

			print "<script>window.location='index.php?msg=EMDNE'</script>";

		}

	}

}

if (isset($_GET['msg']))

{

	$msg=$_GET['msg'];

	if($msg=="EMDNE")

		$msg="Email Does not Exists or not Active";

}



?>

<!-- BEGIN BODY -->

<body class="login">

	<!-- BEGIN LOGO -->

	<div class="logo" style="color: white;">

		MYCOTOGO 

	</div>

	<!-- END LOGO -->

	<!-- BEGIN LOGIN -->

	<div class="content">

		<!-- BEGIN LOGIN FORM -->

		<?php

		if(!isset($_GET['type']))

		{?>

			<form class="form-vertical login-form" action="index.php" method="post" name="frmlogin" id="frmlogin">

			<input type="hidden" name="task" value="<?php if(isset($_GET['task'])){ echo $_GET['task']; }?>">

			<input type="hidden" name="actionprocess" id="actionprocess" value="login_do">

				<h3 class="form-title">Login to your account </h3>

				<div class="alert alert-error hide">

					<button class="close" data-dismiss="alert"></button>

					<span>Enter any username and password.</span>

				</div>

				

				<?php if($msg!="")

						if ($msg=='succ') {?>

				<div class="alert alert-success hide" style="display:block";>

						<button class="close" data-dismiss="alert"></button>

						Your Password Reset email is on the way!						

				</div>

				<?php } else { ?>

				<div class="alert alert-error hide" style="display:block";>

					<button class="close" data-dismiss="alert"></button>

					<span><?php echo $msg; ?></span>

				</div>

				<?php } ?>

				<div class="control-group">

					<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->

					<label class="control-label visible-ie8 visible-ie9">Username</label>

					<div class="controls">

						<div class="input-icon left">

							<i class="icon-user"></i>

							<input class="m-wrap placeholder-no-fix" type="text" placeholder="Username" name="username"/>

						</div>

					</div>

				</div>

				<div class="control-group">

					<label class="control-label visible-ie8 visible-ie9">Password</label>

					<div class="controls">

						<div class="input-icon left">

						<!-- 	<i class="icon-lock"></i> -->
							<a class = "showHidePassword" href=""><i class="fa fa-eye-slash" aria-hidden="true"></i> </a>

							<input class="m-wrap placeholder-no-fix" type="password" placeholder="Password" name="password"/>					

						</div>

					</div>

				</div>

				<div class="form-actions logit">

					<label class="checkbox" style="display:none;">

					<input type="checkbox" name="remember" value="1"/> Remember me

					</label>

					<button type="submit" class="btn green pull-right">

					Login

					</button> 

				</div>

				<div class="forget-password">

					<h4>Forgot your password ?</h4>

					<p>


<!--?php

//$user_id = $_SESSION['user_id']; // Set the user ID

$user_id = 46; // Set the user ID

$user = new User();
$token = $user->generate_token($user_id);

if ($token) {
  // Token generated and inserted successfully
  echo $token;
} else {
  // Error generating or inserting token
  echo "Error generating or inserting token.";
}

?-->
						Click <a href="resetpassword.php" class="" id="forget-password">here</a>

						<!--a href="resetpassword.php?token_id=$token" class="" id="forget-password">here</a-->

						to reset your password.

					</p>

				</div>

				

				<!-- 

				<div class="create-account">

					<p>

						Don't have an account yet ?&nbsp; 

						<a href="javascript:;" id="register-btn" class="">Create an account</a>

					</p>

				</div>

				-->

			</form>

		<?php }?>

		<!-- END LOGIN FORM -->        

		<!-- BEGIN FORGOT PASSWORD FORM -->

		<form class="form-vertical forget-form" action="index.php" name="frmlogin" id="frmlogin" method="post">

			<input type="hidden" name="actionprocess" id="actionprocess" value="forgetpassword_do">

			<h3 class="">Forget Password ?</h3>

			<p>Enter your e-mail address below to reset your password.</p>

			<div class="control-group">

				<div class="controls">

					<div class="input-icon left">

						<i class="icon-envelope"></i>

						<input class="m-wrap placeholder-no-fix" type="text" placeholder="Email" name="email" />

					</div>

				</div>

			</div>

			<div class="form-actions">

				<button type="button" id="back-btn" class="btn">

				<i class="m-icon-swapleft"></i> Back

				</button>

				<button type="submit" class="btn blue pull-right">

				Submit <i class="m-icon-swapright m-icon-white"></i>

				</button>            

			</div>

		</form>

		<!-- END FORGOT PASSWORD FORM -->

		<!-- BEGIN REGISTRATION FORM -->

		<?php

		if(isset($_GET['activateid']) && $_GET['activateid']!="" && isset($_GET['key']) && isset($_GET['type']) && $_GET['type']!="")

		{?>

			<form class="form-vertical register-form" action="" style="display:block;" method="POST">

			<div class="alert alert-error hide" style="display:block";>

				<button class="close" data-dismiss="alert"></button>

				<span><?php echo $msg; ?></span>

			</div>

			<?php if($msg=='You already registered')

			{?>

				<a href="index.php">Click here to login</a>

			<?php }

			else

			{?>

				<input type="hidden" name="actionprocess" id="actionprocess" value="consumerLogin_do">

					<h3 class="">Sign Up</h3>

					<p>Enter your account details below:</p>

					<div class="control-group">

						<label class="control-label visible-ie8 visible-ie9">First Name</label>

						<div class="controls">

							<div class="input-icon left">

								<i class="icon-user"></i>

								<input class="m-wrap placeholder-no-fix" type="text" placeholder="First Name" name="firstname"/>

							</div>

						</div>

					</div>

					<div class="control-group">

						<label class="control-label visible-ie8 visible-ie9">Last Name</label>

						<div class="controls">

							<div class="input-icon left">

								<i class="icon-user"></i>

								<input class="m-wrap placeholder-no-fix" type="text" placeholder="Last Name" name="lastname"/>

							</div>

						</div>

					</div>

					<div class="control-group">

						<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->

						<label class="control-label visible-ie8 visible-ie9">Email</label>

						<div class="controls">

							<div class="input-icon left">

								<i class="icon-envelope"></i>

								<input class="m-wrap placeholder-no-fix" type="text" placeholder="Email" name="email" />

							</div>

						</div>

					</div>

					<div class="control-group">

						<label class="control-label visible-ie8 visible-ie9">Password</label>

						<div class="controls">

							<div class="input-icon left">

								<i class="icon-lock"></i>

								<input class="m-wrap placeholder-no-fix" type="password" id="register_password" placeholder="Password" name="password"/>

							</div>

						</div>

					</div>

					<div class="control-group">

						<label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>

						<div class="controls">

							<div class="input-icon left">

								<i class="icon-ok"></i>

								<input class="m-wrap placeholder-no-fix" type="password" placeholder="Re-type Your Password" name="rpassword"/>

							</div>

						</div>

					</div>

					<div class="control-group">

						<input  type="hidden" name="user_id" value="<?php echo $id;?>"/>

					</div>

					<div class="control-group">

						<div class="controls">

							<label class="checkbox">

							<input type="checkbox" name="tnc"/> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>

							</label>  

							<div id="register_tnc_error"></div>

						</div>

					</div>

					<div class="form-actions">

						<button id="register-back-btn" type="button" class="btn">

						<i class="m-icon-swapleft"></i>  Back

						</button>

						<button type="submit" id="" class="btn blue pull-right" name="consumer_login">

						Sign Up <i class="m-icon-swapright m-icon-white"></i>

						</button>            

					</div>

				<?php }?>

			</form>

		<?php }?>

		<!-- END REGISTRATION FORM -->

	</div>

	<!-- END LOGIN -->

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

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js" type="text/javascript"></script>

	<script src="assets/scripts/login-soft.js" type="text/javascript"></script>      

	<!-- END PAGE LEVEL SCRIPTS --> 

	<script>

		jQuery(document).ready(function() {     

		  App.init();

		  Login.init();

		   	$(".showHidePassword").on('click', function(e) {
			    e.preventDefault();

			    // get input group of clicked link
			    var input_group = $(this).parent('.input-icon');


			    // find the input, within the input group
			    var input = input_group.find('input.m-wrap');


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