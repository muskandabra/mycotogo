<?php include_once("private/settings.php");

include_once("classes/clsConsumer.php");

include_once("classes/pagination.php");

include_once(PATH."classes/Module.php");

include_once(PATH."includes/accessRights/manageConsumers.php");

include_once(PATH."classes/clsTemplate.php");

include_once(PATH."classes/clsSystem.php");

include_once(PATH."classes/clsSystem.php");

include_once(PATH."classes/clsFolder.php");

include_once(PATH."classes/clsFile.php");



$page='';

	if(isset($_GET['pageNo']) && ($_GET['pageNo']!='' || $_GET['pageNo']!='1'))    

	{

		$k=$_GET['pageNo'];

		$i=($k * $_GET['pageNo'])+1 ;

	}

	if (!isset($_GET['pageNo']))    

	{

		$iCount = 1;

	}

	else

	{

		$page=$page+1;

		$iCount = (5 * $_GET['pageNo']) - 5;

	}

?>

<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>

	<meta charset="utf-8" />

	<title>MYCOTOGO | Data Tables - Advanced Tables</title>

	<meta content="width=device-width, initial-scale=1.0" name="viewport" />

	<meta content="" name="description" />

	<meta content="" name="author" />

	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>

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

	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />

	<link type="text/css" rel="stylesheet" href="assets/css/lightbox-form.css">

	<script src="admin/assets/scripts/lightbox-form.js" type="text/javascript"></script>

	 

	<!-- END PAGE LEVEL STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>

	<script type="text/javascript">

	function showoTr(selectedTr) {

     $('.resultTr').each(function(index) {

          if ($(this).attr('id') == selectedTr)

		  {

			var $id=$(this).attr('id');

			$(this).hide(100);

			if (document.getElementById($id).style.display == 'none')

			{

				$(this).show(100);

			}

          }

          else

		  {

			$(this).hide(100);

          }

     });

}

</script>



</head>

<!-- END HEAD -->

<!-- BEGIN BODY -->

<?php

	$objConsumer = new Consumer();

	$objTemplate = new Template();

	$objSystemSetting= new SystemSetting();

	$objFolder= new Folder();

	$objFile= new File();

	$user_id=$_SESSION['sessuserid'];

											

	if(isset($_POST['activeBtn']))

	{

		$objConsumer->consumer_id=$_POST['consumer_id'];

		$objConsumer->consumerstatus_id=$_POST['status_id'];

		$objConsumer->StatusUpDate='true';

		$rendomnumber=$objConsumer->generateRandomString();

		$objConsumer->rendomnumber=$rendomnumber;

		$objConsumer->updateConsumer();

		

		$res=$objSystemSetting->showSysrecordes();

		$folderinfo=mysqli_fetch_object($res);

		$objFolder->name=$folderinfo->sys_name;

		$objFolder->Description=$folderinfo->sys_value;

		$objFolder->consumer_id=$_POST['consumer_id'];

		$folder_id=$objFolder->addFolder();

		

		$objFile->name='User File';

		$objFile->folder_id=$folder_id;

		$objFile->consumer_id=$_POST['consumer_id'];

		$file_id=$objFile->addFile();

		

		$objTemplate->consumer_id=$_POST['consumer_id'];

		$objTemplate->file_id=$file_id;

		$objTemplate->user_id=$_SESSION['sessuserid'];

		$objTemplate->state_id=$_POST['state_id'];

		$objTemplate->generateTemplate();

		

	}

?>

<body class="page-header-fixed">

	<div class="backdrop"></div>

	<div class="light_box" >

		<div class="close" style="float:right">X</div>

		<div class="renameform" style="padding:20px;"></div>

	</div>

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

						<!-- BEGIN PAGE TITLE & BREADCRUMB-->

						

						<!-- END PAGE TITLE & BREADCRUMB-->

					</div>

				</div>

				<!-- END PAGE HEADER-->

				<!-- BEGIN PAGE CONTENT-->

				<div class="row-fluid">

					<div class="span12">

						<!-- BEGIN EXAMPLE TABLE PORTLET-->

						<div class="portlet box green">

							<div class="portlet-title manage-consumer">

								<div class="caption"><i class="icon-user"></i>Manage Consumer</div>

								<?php 

								if($consumerAdd==1)

								{?>

								<div class="actions">

									<a href="peralegalform.php" class="btn blue"><i class="icon-pencil"></i> Add New</a>

								</div>

								<?php } ?>

							</div>

							<div class="portlet-body manage-consumer">

								

									<table class="table table-striped table-bordered table-hover table-full-width" id="sample_1">

										<thead>

											<tr>

												<th>File Number</th>

												<th>Company Name</th>

												<th class="hidden-480">Contact</th>

												<th class="hidden-480">Email</th>

												<th class="hidden-480">Address</th>

												<th class="hidden-480">Status</th>

												<th>Action</th>

											</tr>

										</thead>

										<tbody>

											<?php 

											$objConsumer->user_id=$user_id;

											$res = $objConsumer->selectConsumer();

											$pagingObject = new pagingRecords();

											$pagingObject->setMaxRecords(5);

											$selectleads = $pagingObject->setQuery($res);

											$queryleads=mysqli_query($dbconnection, $selectleads);

											if(mysqli_num_rows($queryleads)>0)

											{

												$srno=1;

												while($row=mysqli_fetch_object($queryleads))

												{ ?>

													<tr>

													 <input type="hidden" name="companyname" value="<?php echo $row->companyname; ?>"/>

														<td class="hidden"><input type="text" name="consumer_id <?php echo $srno;?> " value="<?php echo $row->consumer_id;?>"/></td>

														<td><?php echo $iCount;?></td>

														<td><?php echo $row->companyname?></td>

														<td class="hidden-480"><?php echo $row->companycontact;?></td>

														<td class="hidden-480 mailing"><?php echo $row->companyemail;?></td>

														<td class="hidden-480"><?php echo $row->companyresaddress;?></td>

														<td class="hidden-480"> 

														<?php if($row->consumerstatus_id!=4)

														{	?>

															<select name="statusdropdwn" id="statusdropdwn <?php echo $srno;?>" onchange="return openPopUp(<?php echo $row->consumer_id;?>,<?php echo $row->state_id;?>,this.value);">

																<option value="1"<?php if(isset($row->consumerstatus_id) && $row->consumerstatus_id=='1'){echo "selected=selected" ;} ?>>Pending</option>

																<option value="4"<?php if(isset($row->consumerstatus_id) && $row->consumerstatus_id=='4'){echo "selected=selected" ;} ?>>Active</option>

															</select>

														<?php }

														else

														{?>

															Active

														<?php }?>

														

														</td>

														<td>

															<div class="btn-group">

																<a class="btn green" href="#" data-toggle="dropdown">

																<i class="icon-user"></i>

																<i class="icon-angle-down"></i>

																</a>

																<ul class="dropdown-menu pull-right">

																	<li>

																	<a href="javascript:showoTr('director<?php echo $srno;?>');"><i class="icon-pencil"></i>Show</a></li>

																	<li>

																	<a href="peralegalform.php?code=<?php echo base64_encode($row->consumer_fileno);?>"><i class="icon-pencil"></i> Edit</a></li>

																	<li><a href="#"><i class="icon-trash"></i> Delete</a></li>

																	<?php if(isset($row->consumerstatus_id) && $row->consumerstatus_id=='4'){?><li><a href="showdetails.php?n=<?php echo $row->consumer_id;?>"><i class="icon-trash"></i> Show Details</a></li><?php } ?>

																</ul>

															</div>

														</td>

														<td class="hidden"><?php $objConsumer->id=$row->consumer_id;?></td>



													</tr>

													<tr class="resultTr" id="director<?php echo $srno;?>" style="display:none;">

														<td colspan=9>

															<table>

																<?php

																$resDirector = $objConsumer->showDirector();

																if(mysqli_num_rows($resDirector)>0)

																{?>

																	<tr>

																		<th>Name</th>

																		<th>Director Tittle</th>

																		<th class="hidden-480">Address</th>

																		<th class="hidden-480">No. of Shares</th>

																		<th class="hidden-480">Share Type</th>

																		<th>Action</th>

																	</tr>

																	<?php

																	$directorcount=1;

																	while($rowDirector=mysqli_fetch_object($resDirector))

																	{?>

																		<tr>

																			<td><?php echo $rowDirector->consumerfname;?></td>

																			<td><?php echo $rowDirector->consumerofficertitle;?></td>

																			<td class="hidden-480"><?php echo $rowDirector->consumeraddress1;?></td>

																			<td class="hidden-480"><?php echo $rowDirector->consumernoofshares;?></td>

																			<td class="hidden-480"><?php echo $rowDirector->consumersharetype;?></td>

																			<td>

																				<div class="btn-group">

																					<a class="btn green" href="#" data-toggle="dropdown">

																					<i class="icon-user"></i>

																					<i class="icon-angle-down"></i>

																					</a>

																					<ul class="dropdown-menu pull-right">

																						<li><a href="peralegalform2.php?id=<?php echo $rowDirector->consumeruser_id;?>&action=edit_<?php echo $directorcount; ?>"><i class="icon-pencil"></i> Edit</a></li>
																						
																						<li><a href="peralegalform2.php?id=<?php echo $rowDirector->consumeruser_id;?>&action=edit_<?php echo $directorcount; ?>"><i class="icon-pencil"></i> Edit</a></li>

																						<li><a href="#"><i class="icon-trash"></i> Delete</a></li>

																					</ul>

																				</div>

																			</td>

																		</tr>

																	<?php 

																	$directorcount++;

																	}

																}

																else

																{?>

																	<td colspan=8><?php echo"no record found";?></td>

																<?php }?>

																

															</table>

														</td>

													</tr>

													<?php

													$srno++; 

													$iCount++;

												}

											}

											

											?>

										</tbody>

									</table>

									<form name="myform" method="POST" action="" style="display:none;">

										<input type="submit" name="generate_template" value="Generate Template" class="btn blue"/>

									</form>

									<?php

										$pagingObject->displayLinks_Front();

									?>

								</div>

							</div>

									

						<!-- END EXAMPLE TABLE PORTLET-->

						<!-- BEGIN EXAMPLE TABLE PORTLET-->

						

						<!-- END EXAMPLE TABLE PORTLET-->

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

	<div class="footer">

		<div class="footer-inner">

			2013 &copy; Metronic by keenthemes.

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

	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.min.js"></script>

	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/table-advanced.js"></script>     

	<script>

		jQuery(document).ready(function() {       

		   App.init();

		   TableAdvanced.init();

		});

		

	</script>

	<script>

	$(document).ready(function(){

		$(".btn-navbar").click(function(){

			$(".page-container .page-sidebar.nav-collapse").removeAttr("style");

			$(".page-sidebar .page-sidebar-menu").slideToggle(500);

		});

	});

	

	function openPopUp(consumer_id,state_id,status_id)

	{

		//alert(status_id);

		var task='statusActive';

		var query = "task="+task+"&id="+consumer_id+"&state_id="+state_id+"&status_id="+status_id;

		var url= "ajaxfile.php";

		

		$.ajax

		({

			type: "POST",

			url: url,

			data: query,

			success: function(response)

			{

			

				if (response)

				{

					jQuery('.backdrop').animate({'opacity':'.50'}, 300, 'linear');

					jQuery('.light_box').animate({'opacity':'1.00'}, 300, 'linear');

					jQuery('.backdrop').css('display', 'block');

					jQuery('.light_box').css('display', 'block');

					jQuery('.renameform').html("<div style='text-align:center'><h1>Loading...</h1></div>");

					// notifications.showAlert('Notice', 'Force sync sucessfully complete', notifications.ALERT);

					jQuery('.renameform').html(response);

				}

				else 

				{

					//notifications.showAlert('Errors', 'Could not sync sucessfully', notifications.CRITICAL);

					alert('no');

				}

			}

		});

	return false;

}

	</script> 

	

</body>

<!-- END BODY -->

</html>