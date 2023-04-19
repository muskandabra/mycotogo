<?php 

include_once("private/settings.php");

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

	<title><?php echo SITE_NAME;?> | Manage Users</title>

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

	<script language="javascript">

	function del(valid)

	{

		if(confirm("Are you sure you want to delete"))

		{

			window.location='action_users_para.php?delid='+valid+'&actionprocess=users';

		}

	}

	</script>

</head>

<!-- END HEAD -->

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

						<div class="portlet box blue">

							<div class="portlet-title">

								<div class="caption"><i class="icon-user"></i>Manage Users</div>

							</div>

							<div class="portlet-body">

								<div class="clearfix">

									<div class="btn-group">

										<!-- <button  class="btn green" onClick="window.location='action_users_para.php'">

										Add New <i class="icon-plus"></i>

										</button> -->

									</div>

								</div>

								<table class="table table-striped table-hover table-bordered" id="sample_editable_1">

									<thead>

										<tr>

											<th>Sr no.</th>

											<th>Name</th>

											<th>Use Type</th>

                                            <th>Email</th>

											<th>Date Joined</th>

											<th>Status</th>

											<th>Edit</th>

											<!-- <th>Delete</th> -->

										</tr>

									</thead>

									<tbody>

										<?php 

										$objUser = new User();
										$user_id = $_SESSION['sessuserid'];
										$objUser->created_by = $user_id;

										$res = $objUser->selectUser();

										if(mysqli_num_rows($res)>0)

										{

											$srno=1;

											while($row=mysqli_fetch_object($res))

											//show data from tbl_user

											{?>

												<tr class="">

													<td><?php echo $srno;?></td>

													<td><?php if($row->firstname=='' && $row->lastname=='') {?><span style="color:red;"> <?php echo '[NOT REGISTERED YET]';?> </span> <?php } else {  echo ucfirst($row->firstname).' '.ucfirst($row->lastname);}?></td>

													<td><?php echo $row->usertype;?></td>

													<td><?php echo $row->useremail;?></td>

													<td><?php echo $row->registrationDate;?></td>

													<td><?php echo $row->userstatus;?></td>

													<td><a href="action_users_para.php?user=<?php echo base64_encode($row->user_id);?>" >Edit</a></td>

													<!-- <td><a href="#" onClick="javascript:del('<?php echo base64_encode($row->user_id);?>')" >Delete</a></td> -->

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