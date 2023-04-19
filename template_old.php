<?php
include_once("private/settings.php");

include_once(PATH."classes/clsNotification.php");
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

	function del(valid)

	{

		if(confirm("Are you sure you want to delete"))

		{

			window.location='templates.php?delid='+valid+'&actionprocess=template';

		}

	}

	</script>

</head>

<!-- END HEAD -->

<!-- BEGIN BODY -->

<?php

	$notificationObj= new Notification();

	if(isset($_GET['delid']) && $_GET['delid']!='')

	{

		

		$notification_template_id	=	base64_decode($_GET['delid']);

		$notificationObj->notification_template_id	=	$notification_template_id;

		$notificationObj->deleteNotificationTemplate();

		print "<script>window.location='templates.php?msg=del'</script>";

	}

	$notificationObj->user_id = $_SESSION['sessuserid'];

	$selectNotificationTemplate = $notificationObj->selectNotificationTemplate();

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

		<div id="flash" style="align:left;"  ></div>

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

				<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN EXAMPLE TABLE PORTLET-->

						<div class="portlet box green">

							<div class="portlet-title manage-consumer">

								<div class="caption"><i class="icon-group"></i>Manage Templates</div>

								<div class="actions">

									<a href="notifications.php?temp=manage#tab_1_4" class="btn blue"><i class="icon-pencil"></i> Add New</a>

								</div>

							</div>

							<div class="portlet-body manage-consumer">

								<?php 

								if(isset($_GET['msg']) && $_GET['msg']=='del')

								{

									?>

									<div class="alert alert-error hide" 

										<?php

											if ($_GET['msg']!='')

												echo('style="display: block;">');

										?>>

											

											Record Deleted Sucessfully.<button style ="float:right;" class="close" data-dismiss="alert"></button>

											

										</div>

									<?php

								}

								?>

								<table class="table table-striped table-hover table-bordered" id="sample_editable_1">

									<thead>

										<tr>

											<th class="hidden">consumer id</th>

											<th>Template Name</th>

											<th>Template Description</th>

											<th class="hidden-480">Created Date</th>

											<th>Edit</th>

											<th>Delete</th>

										</tr>

									</thead>

									<tbody>

										<?php 

										if(mysqli_num_rows($selectNotificationTemplate)>0)

										{

											$srno=1;

											while($row=mysqli_fetch_object($selectNotificationTemplate))

											{ ?>

												<tr>

													<td class="hidden"></td>

													<td><?php echo  $row->template_title;?></td>

													<td class="hidden-480"><?php echo $row->template_description;?></td>

													<td class="hidden-480 mailing"><?php echo $row->date_created;?></td>

													<td class="hidden-480"><a href="edittemplate.php?id=<?php echo base64_encode($row->notification_template_id);?>">Edit</a></td>

													<td class="hidden-480"><a href="#" onClick="javascript:del('<?php echo base64_encode($row->notification_template_id);?>')">Delete</a></td>

												</tr>

												<?php

												$srno++;

											}

										}

										?>

									</tbody>

								</table>

							</div>

						</div>

						<!-- END EXAMPLE TABLE PORTLET-->

					</div>

				</div>

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