<?php include_once("private/settings.php");
include_once(PATH."classes/User.php");
include_once(PATH."classes/clsFile.php");
include_once(PATH."classes/Module.php");
include_once(PATH."includes/accessRights/manageShowNotes.php");

?>
<?php 
$objFile=new File();
if(isset($_POST['submit']) && $_POST['submit']!='')
{
	$folder_id=$_POST['folder_id'];
	if($folder_id!='')
	{
		$objFile->name='Untitled';
		$objFile->folder_id=$folder_id;
		$objFile->user_id=$_SESSION['sessuserid'];
		$objFile->isAutomatic='0';
		//$objFile->documenttype='File';
		$objFile->addFile();
		print"<script language=javascript>window.location='".URL."shownotes.php?n=".$folder_id."'</script>";
	}
}


if(isset($_POST['save']) && $_POST['save']!='')
{
	$objFile->name=$_POST['fileName'];
	$array=$_POST;
	$third = array_splice($array, 2, 1);
	$value=array_values($third);
	$objFile->file_id=$_POST['file_id'];
	$objFile->Description=$value[0];
	$objFile->user_id=$_SESSION['sessuserid'];
	$objFile->editFile();

}
$selectFile=$objFile->selectFile();
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD --><head>
	<meta charset="utf-8" />
	<title>MYCOTOGO | Show Notes</title>
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
	<link href="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.css" rel="stylesheet" type="text/css" />
	<link href="assets/plugins/chosen-bootstrap/chosen/chosen.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/pages/profile.css" rel="stylesheet" type="text/css" />
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
</head>

<style>

</style>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">
	<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse navbar-fixed-top">
		<?php include_once("elements/header.php");?>
	</div>
	<!-- END HEADER -->
	<!-- BEGIN CONTAINER -->   
	<div class="page-container row-fluid shownotes-section">
		<!-- BEGIN SIDEBAR -->
		<?php include("elements/left.php");?>
		<!-- END SIDEBAR -->
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
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN STYLE CUSTOMIZER -->
						
						<!-- END BEGIN STYLE CUSTOMIZER --> 
						<!-- BEGIN PAGE TITLE & BREADCRUMB-->
						<h3 class="page-title">
							User Notes <small></small>
							<?php if($notesAdd==1)
								{?>
							<form method="post" class="new-note-area">
								<input type="hidden" name="folder_id" value="<?php echo $_GET['n']; ?>">
								<input type="hidden" name="user_id" value="<?php echo $_SESSION['sessuserid'];?>"/>
								<i class="icon-plus"></i> <input type="submit" name="submit" value="New Note">
							</form>
							<?php }?>
						</h3>
						<!-- END PAGE TITLE & BREADCRUMB-->
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
				<div class="row-fluid profile">
					<div class="span12">
						<!--BEGIN TABS-->
						<div class="tabbable tabbable-custom tabbable-full-width">
							<div class="tab-content" >
							<div class="all-notes-area">
								<h4>All Notes</h4>
								<div class="note-single">
									<?php 
									if(mysqli_num_rows($selectFile)>0)
									{
										while($row=mysqli_fetch_object($selectFile))
										{
											?>
											<div class="notes-area">
												<ul class="tabs_navigation isotope_filters page_margin_top clearfix">
													<li>
														<a class="notesNab" href="#filter=.<?php echo $row->document_id;?>" title="All Departments">
															<h3><?php echo $row->name; ?></h3>
															<div class="notes-date"><?php echo  $row->createddate;?></div>
															<p>
																<?php  echo mb_strimwidth($row->Description, 0, 250, "...")?>
															</p>
														</a>
													</li>
												</ul> 
											</div>
											<?php
										} 
									}
									?>
									</div>
							</div>
							<div class="contentnotes">
							<?php $res=$objFile->selectFile();
							if(mysqli_num_rows($res)>0)
							{
								while($result=mysqli_fetch_object($res))
								{
								
							?> 	<ul class="gallery isotope" style="position: relative; overflow: hidden; height: 423px;"> 
									<li style="position: absolute; left: 0px; top: 0px; transform: translate(0px, 0px);" class="<?php echo $result->document_id;?> gallery_box" id="gallery-item-1">
										<div class="span9">
											<form method="post">
												<input type="hidden" name="file_id" id="fileid<?php echo $result->document_id; ?>" value="<?php echo $result->document_id;?>">
												<div class="gallery-inside">
													<div class="top-section"></div>
													<div class="editor-area">
														<input type="text" name="fileName" class="fileName" id="fileName<?php echo $result->document_id; ?>" value="<?php echo $result->name;?>">
														<div class="edit editor<?php echo $result->document_id; ?>" style="color:Red" onchange="return testing('this.id');">
															<p class="description" id="description<?php echo $result->document_id; ?>">
																<?php echo $result->Description;?>
															</p>
														</div>
														<div class="submit-area">
															<input type="submit" name="save" value="Save" />
															<input type="reset" name="reset" value="Reset" />
														</div>
													</div>
												</div>
											</form>
										</div>																
									</li>
								</ul>
								<?php  } }?>
								</div>
							</div>
							<!--end tab-pane-->
							</div>
						</div>
						<!--END TABS-->
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
	<script type="text/javascript" src="assets/plugins/bootstrap-fileupload/bootstrap-fileupload.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<script type="text/javascript" src="admin/tinymce/js/tinymce/tinymce.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="assets/scripts/app.js"></script>    

<script type="text/javascript" src="admin/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="admin/js/jquery.ba-bbq.min.js"></script>
<script type="text/javascript" src="admin/js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="admin/js/jquery.carouFredSel-5.6.4-packed.js"></script>
<script type="text/javascript" src="admin/js/jquery.sliderControl.js"></script>
<script type="text/javascript" src="admin/js/jquery.isotope.min.js"></script>
<script type="text/javascript" src="admin/js/jquery.isotope.masonry.js"></script>
<script type="text/javascript" src="admin/js/main.js"></script>
<script>
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
		});
	</script><script>
 tinymce.init({
  selector: "div.edit",
  theme: "modern",
  
  plugins: [
   ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker"],
   ["searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking"],
   ["save table contextmenu directionality emoticons template paste importcss colorpicker textpattern"],
   ["textcolor"]
  ],
  content_css: "css/content.css",
  add_unload_trigger: false,
  inline: true,
  toolbar: "insertfile undo redo |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage |  backcolor emoticons | fontselect | fontsizeselect ",

  spellchecker_callback: function(method, words, callback) {
   if (method == "spellcheck") {
    var suggestions = {};

    for (var i = 0; i < words.length; i++) {
     suggestions[words[i]] = ["First", "second"];
    }

    callback(suggestions);
   }
  }
 });

 tinymce.init({
  selector: "h1.edit",
  theme: "modern",
  content_css: "css/development.css",
  add_unload_trigger: false,
  inline: true,
  toolbar: "undo redo"
 });
</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>