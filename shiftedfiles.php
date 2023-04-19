<?php
include_once("private/settings.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/Module.php");
include_once(PATH."includes/accessRights/manageConsumers.php");
include_once(PATH."classes/clsTemplate.php");
include_once(PATH."classes/clsSystem.php");
include_once(PATH."classes/clsFolder.php");
include_once(PATH."classes/clsFile.php");
include_once(PATH."classes/clsTransaction.php");
include_once(PATH."classes/User.php");
include_once(PATH."includes/accessRights/manageLeftNav.php");

if($consumerView!='1' )
{ 
	print "<script language=javascript>window.location='index.php'</script>";
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title>MYCOTOGO | Manage Consumer</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

<!-- 	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />
	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<script type="text/javascript">
	</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<?php
	$explodedDate='';
	$objConsumer = new Consumer();
	// $objTransaction= new Transaction();
	// $objTemplate = new Template();
	// $objSystemSetting= new SystemSetting();
	 $objUser=new User();
	// $objFolder= new Folder();
	// $objFile= new File();
	// $active_status = 1;
	$checkedValues ='';
	
	$objConsumer->usertype=$_SESSION['usertype'];
	$objConsumer->user_id=$_SESSION['sessuserid'];
?>
<!-- BEGIN BODY -->
<body class="page-header-fixed">
	<div class="backdrop"></div>
	<div class="light_box" >
		<div class="close" style="float:right">X</div>
		<div class="renameform"></div>
	</div>
	<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse navbar-fixed-top">
		<!-- BEGIN TOP NAVIGATION BAR -->
		<?php include(PATH."elements/header.php");?>
		<!-- END TOP NAVIGATION BAR -->
	</div>
	<!-- END HEADER -->
	<!-- BEGIN CONTAINER -->
		<div id="flash" align="left"  ></div>
	<div class="page-container row-fluid" id="flash_back">
		<!-- BEGIN SIDEBAR -->
		<?php include(PATH."elements/left.php");?>
		<!-- END SIDEBAR -->
		<!-- BEGIN PAGE -->
		<div class="page-content">
			<!-- BEGIN PAGE CONTAINER-->        
			<div class="container-fluid">
				<!-- BEGIN PAGE CONTENT-->
				<div class="row-fluid">
					<div class="span12">  
						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<?php include_once(PATH.'includes/ShiftedConsumerView.php');?>
				<div class="testing"></div>	
				<!-- END PAGE CONTENT -->
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
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/table-editable.js"></script>   
	
	<script>
		jQuery(document).ready(function() {       
		   App.init();
		   TableEditable.init();		    
		});
	</script>
</body>
<!-- END BODY -->
</html>