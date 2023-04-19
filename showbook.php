<?php
include_once("private/settings.php");
include_once(PATH."classes/clsConsumer.php");
include_once(PATH."classes/clsFolder.php");
include_once(PATH."addfolderorignal.php");
?>
<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD --><head>

	<meta charset="utf-8" />

	<title><?php echo SITE_NAME;?>| Document Details</title>

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

	<link href="style.css" rel="stylesheet" type="text/css"/>

	<!-- END GLOBAL MANDATORY STYLES -->

	<link rel="shortcut icon" href="favicon.ico" />

	

<?php
	$workspace = 0;
	$parameter = "";
	if(isset($_SESSION['workspace']) && !empty($_SESSION['workspace']))
	{
		$workspace = 1;
		$parameter = "&workspace=1";
	}

	$objConsumer=new Consumer();

	$objConsumer->consumer_id=base64_decode($_GET['n']);

	$row=$objConsumer->getCompanyDetails();

	$consumerDetail	= mysqli_fetch_object($row);

	$consumerfilestatus_id=$consumerDetail->consumerfilestatus_id;

	//echo $consumerfilestatus_id;

?>

</head>

<body class="page-header-fixed show-details">

	<!-- BEGIN HEADER -->

	<div class="header navbar navbar-inverse navbar-fixed-top">

		<?php include_once("elements/header.php");?>

	</div>

	<div class="page-container row-fluid shownotes-section add_book_main">

		<!-- BEGIN SIDEBAR -->

		<?php include("elements/left.php");?>

		<!-- END SIDEBAR -->

		<!-- BEGIN PAGE -->

		<div class="page-content add_book_sub">

			<!-- BEGIN PAGE CONTAINER-->

			<div class="container-fluid">

				<!-- BEGIN PAGE HEADER-->

					<!-- BEGIN PAGE CONTENT-->

					<div class="row-fluid profile">

						<!-- BEGIN PAGE CONTENT-->

						<div class="row-fluid profile">

							<!-- BEGIN ROW-FLUID CONTENT-->

							<div class="span12">

								<!--BEGIN TABS-->

								<div class="tabbable tabbable-custom tabbable-full-width">

									<!--BEGIN tabbable-->

									<div class="tab-content" >

										<!--BEGIN tab-content-->

										<div class="all-notes-area">

											<!--BEGIN all-notes-area-->

											<h4 class="all-notes-area-header <?php if($consumerfilestatus_id=='6') { echo "complete"; } else {echo "pending";}?>"><?php echo "Company Briefcase"; ?>

												<a href="#" onClick="openbox('Create a New Folder',1)" >

													<img title="Create a Folder" src="img/add-new-folder.png" />

													

												</a>

											</h4>

											<input type="hidden" value="<?php echo URL;?>" id="viewUrl1">

											<div class="note-single"></div>

										</div>

										<div class="contentnotes contentnotes_class "></div>

										<!--END all-notes-area-->

									</div>

									<!--END tab-content-->

								</div>

								<!--EDIT tabbable-->

							</div>

							<!--CLOSE TABS-->

						</div>

						<!-- END ROW-FLUID CONTENT-->

					</div>
					<div class="show_loader" style="display:none"></div>
					<?php
					if (isset($_GET['code']))
					{?> 
						<form>
						<input class= "button1" type="button" value="Back" onclick="window.location.href='consumer.php?no=<?php echo $_GET['code'].$parameter; ?>'" />
						</form> 
					<?php }

					$consumer_no = '';

					if (isset($_GET['n']) && !empty($_GET['n']))
					{
						$consumer_id = base64_decode($_GET['n']);	
						include_once(PATH."classes/clsOtherMember.php");
						$objOthermember = new OtherMember();
						$objOthermember->consumer_id = $consumer_id;
						//echo "123";

						$memberDetails = $objOthermember->getMemberUniqueEmail();
						$memberArray = array();
						$i=0;

				

						if(mysqli_num_rows($memberDetails)>0)
						{
							
								while($details=mysqli_fetch_object($memberDetails))
								{
									//print_r($details);
									$memberArray['fname'][$i] = $details->fname; 
									$memberArray['lname'][$i] = $details->lname;
									$memberArray['email'][$i] = $details->email;
									$i++;
								}			
						}
						//print_r($memberArray);
					}
					?>

					<div class="get_manual_entry add-new-form-popup " style="display:none">
						<div class="portlet box green">
							<span class="fas fa-times" id="popup-close-btn"></span>
							<div class="portlet-title">
								<div class="caption"><i class="icon-reorder"></i>Enter names and email addresses for each signing member</div>
							</div>
							<div class="portlet-body form">
								<div class="inner-wrapper">
									<form action="#" id="sendforSignaureForm" class="form- manual_entry form-horizontal" method="POST" enctype="multipart/form-data">
										<input type="hidden" name="parent_id" id="parent_id" value="">
										<input type="hidden" name="file_name" id="file_name" value="">
										<input type="hidden" name="document_id" id="document_id" value="">
										<input type="hidden" name="consumer_id" id="consumer_id" value="">

										<div class="forms control-group page_no_member_esign">
											<label>Page No. on which signature is Required</label>
											<input type="text"  required name="sign_page_no" id="page_no" class="input-m" value = ""/>					
										</div>

										<div class="forms control-group">
											<label>First Name
												<span class="showTip L2">
                                                    <img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
                                                    <span class="tooltip-text">
                                                        <img src="img/arrow-img.png">
                                                        <p>To have signature box properly placed, the document must have text like  SIGN HERE (FIRST SIGNEE NAME ), example SIGN HERE (Bob Smith). The signature box will be placed over this text. You can add multiple text like this to have signatures from different persons.</p>
                                                    </span>
                                               	</span>
                                               </label>
											<label>Last Name</label>
											<label>Email</label>
										</div>

										
										<?php

										for ($j=1;$j<=5;$j++)
										{
											$fname = isset($memberArray['fname'][$j-1])?$memberArray['fname'][$j-1]:'';
											$lname = isset($memberArray['lname'][$j-1])?$memberArray['lname'][$j-1]:'';
											$email = isset($memberArray['email'][$j-1])?$memberArray['email'][$j-1]:'';

										?>

											<div class="forms control-group">
												<input type="text"  name="person_fname[]" id="person_name<?php echo $j; ?>" class="input-m" value = "<?php echo $fname; ?>"/>
												<input type="text" name="person_lname[]" id="person_lname<?php echo $j; ?>" class="input-m" value = "<?php echo $lname; ?>"/>
												<input type="email" name="person_email[]" id="person_email<?php echo $j; ?>" class="input-m" value = "<?php echo $email; ?>"/>
											</div>
											
										<?php
										}
										?>

										<input type="submit" class="button1" name="sendforSignaure" id="sendforSignaure" value="Send" />	
									</form>							
								</div>
							</div>
						</div>
					</div>

					<!-- END PAGE CONTENT-->

			</div>

		
			<!-- END PAGE HEADER-->

		</div>

		<!-- END PAGE CONTAINER-->

	</div>

	<!-- END HEADER -->

	

	<!-- BEGIN FOOTER-->

	<div class="footer">

		<?php include(PATH."elements/footer.php");

			include_once(PATH."js/scripts.php");

		?>

	</div>

	<!-- END FOOTER -->

	<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

	<!-- BEGIN CORE PLUGINS -->

	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->

	<script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      

	<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	<!--[if lt IE 9]>

	<script src="assets/plugins/excanvas.min.js"></script>

	<script src="assets/plugins/respond.min.js"></script>  

	<![endif]-->   
	<script src="assets/scripts/popup.js" type="text/javascript"></script>

	<script src="assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript" ></script>

	<!-- END CORE PLUGINS -->

	<script type="text/javascript" src="<?php echo URL; ?>admin/js/showrecordbook.js"></script>

	<script type="text/javascript" src="<?php echo URL; ?>js/popup-section.js?ver=1"> </script>

		<script src="<?php echo URL; ?>assets/scripts/app.js"></script>


	<!-- END PAGE LEVEL SCRIPT -->

	<script>

	jQuery(document).ready(function() {       

		   App.init();

		});

	function deleteFiles (id)	{

		if(confirm("Are you sure you want to delete"))

		{

			var task='deleteFiles';

			var name = $('#delefileName').attr('name');

			var query = "task="+task+"&id="+id;

			$.ajax

			({

				type: "POST",

				url: 'ajaxfile.php',

				data: query,

				success: function(response)

				{

					if (response)

					{

							$('#'+name).remove();

					}

					else 

					{

						//notifications.showAlert('Errors', 'Could not sync sucessfully', notifications.CRITICAL);

						alert('no');

					}

				}

			});

		}

		else

		{

			return false;

		}

	}

	</script>

</body>



