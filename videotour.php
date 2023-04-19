<?php
include_once("private/settings.php");

	include_once("classes/User.php");

	?>
	<!DOCTYPE html>

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

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic' rel='stylesheet' type='text/css'>

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

</head>



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

		<?php //include("elements/left.php");?>

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

									<div class="caption"><i class="icon-reorder"></i>videotour</div>

									<div class="tools">

										<a href="javascript:;" class="collapse"></a>

										<a href="#portlet-config" data-toggle="modal" class="config"></a>

										<a href="javascript:;" class="reload"></a>

										<a href="javascript:;" class="remove"></a>

									</div>

								</div></div>

								<form>

									<iframe width="100%" height="315" src="//www.youtube.com/embed/IHeVZvnCjNA" frameborder="0" allowfullscreen></iframe>

									<button type="button" class="btn green" onClick="window.location='dashboard.php'">NEXT</button>

								</form>

						

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

		});

		

	</script>

<!-- END JAVASCRIPTS -->   

</body>

<!-- END BODY -->

</html>