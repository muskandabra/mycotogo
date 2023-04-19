<?php
include_once("private/settings.php");

include_once(PATH."classes/clsPost.php");

if(isset($_GET['id'])){
$id=$_GET['id'];
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]--><head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?>| Post</title>

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

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

</head>

<!-- BEGIN BODY -->

<?php

$objPost= new Post();

$res=$objPost->getPost($id);

$rows=mysqli_fetch_object($res);

$title=$rows->title;

$description=$rows->description;


$updated_at=$rows->updated_at;

$created_at=$rows->created_at;

$id=$_GET['id'];

if(isset($_POST['actionprocess']) && $_POST['actionprocess']!='')
{

	$objPost->title=$_POST['title'];

	$objPost->description= $_POST['description'];

	$objPost->updated=$_POST['updated_at'];

	$objPost->created_at=$_POST['created_at'];

	$objPost->id=$_POST['id'];

	$objPost->editPost();

	echo "<script>alert('Data Updated Successfully');
    window.location.href='manage_post.php';</script>";

	die;

}

?>


<body class="page-header-fixed additional-reg">

	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<?php include_once("elements/header.php");?>

	</div>

	<!-- END HEADER -->

	<!-- BEGIN CONTAINER -->

	<div class="page-container row-fluid form-section">

		<?php include("elements/left.php");?>

		<!-- END TOP NAVIGATION BAR -->

		<!-- END SIDEBAR MENU -->

	</div>

		<!-- BEGIN PAGE -->  

	<div class="page-content">

		<!-- BEGIN PAGE CONTAINER-->

		<div class="container-fluid">

			<div class="row-fluid">

				<div class="span12">

				</div>

			</div>

			<div class="row-fluid">

				<div class="span12">

					<!-- BEGIN VALIDATION STATES-->

					<div class="portlet box blue">

						<div class="portlet-title">

							<div class="caption"><i class="icon-reorder"></i>Edit Post</div>

						</div>

						<div class="portlet-body form reg-additional">

							<div class="inner-wrapper">

								<div class="form">

									<!--form  class="form-horizontal form1" method="POST" action="#"  name="adminForm" onsubmit="return reminderFormValidation();"-->

									<form  class="form-horizontal form1" method="POST" action="#"  name="adminForm" >
                                    
                                    <input type="hidden" name="shareholders" value="shareholders"/>

										<div class="alert alert-error" id="reminder_error" style="display:none;">

											<button class="close" data-dismiss="alert"></button>

												You have some form errors. Please select status.

										</div>

										<div class="alert alert-success hide">

											<button class="close" data-dismiss="alert"></button>

											Your form validation is successful!

										</div>											

										<div id="test">

										 <div class="portlet box  reminder" >

										 <div class="portlet-body" >

                                         <div class="control-group">

										 <label class="control-label">Title</label>

										 <div class="controls">

											<input type="text" class="large m-wrap" name="title" id="title" data-required="1" value="<?php echo $title; ?>" class="span6 m-wrap"/>
										 
                                         </div>

									     </div>


                                         <div class="control-group">

                                         <label class="control-label">Updated At</label>

                                         <div class="controls">

                                            <input type="date" class="large m-wrap" name="updated_at" id="updated_at" data-required="1" value="<?php echo $updated_at; ?>" class="span6 m-wrap"/>

                                          </div>

                                          </div>

                                         <div class="control-group">

                                         <label class="control-label">Created At</label>

                                         <div class="controls">

                                           <input type="date" class="large m-wrap" name="created_at" id="created_at" data-required="1" value="<?php echo $created_at; ?>" class="span6 m-wrap"/>

                                         </div>

                                         </div>

												<div class="remain-one-half right">

														<div class="forms control-group">

															<label>Description</label>

															<div class="controls">

																<textarea class="large m-wrap" name="description" rows="3"><?php echo $description; ?></textarea>

															</div>

														</div>

												</div>

												

												</div>

											</div>

										</div>

                                        
										<input type="hidden" value ="<?php echo $id;?>" name="id"/>

                                        <input type="hidden" name="actionprocess" value="Update Post"/>	

										<div class="forms control-group reminder-btn">

											<input type="submit" name="edit" class=" button1 btn blue" value="Save" />

											<a href="manage_post.php" class="button1 grey">Cancel</a>

										</div>

									</form>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div><!-- END PAGE CONTENT-->         

		</div>	<!-- END PAGE CONTAINER-->

	</div>

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

	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>

	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-datepicker.js"></script>                
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap-select.js"></script>

	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>

	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL STYLES -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-validation.js"></script> 

<script>




curdate = "<?php echo date('Y,m,d',strtotime($notificationdate)); ?>";
$(".datepicker").datepicker({dateFormat: 'yyyy-mm-dd' });

//alert(curdate);
curdate1 = new Date(curdate);
//alert(curdate1);
//$('.dateselector').datepicker("setDate", new Date(2008,9,03) );
$(".datepicker").datepicker();
$(".datepicker").datepicker("setDate", curdate1);
$(".datepicker").datepicker({dateFormat:'yyyy-mm-dd'});

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

		});

	</script>

	<!-- END JAVASCRIPTS -->   

	<iframe width=172px height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="calendar/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"> </iframe>

</body>

<!-- END BODY -->

</html>