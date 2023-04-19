<?php include_once("private/settings.php");
include_once(PATH."classes/User.php");
include_once(PATH."classes/clsFile.php");
include_once(PATH."classes/clsFolder.php");
include_once(PATH."classes/Module.php");
include_once(PATH."classes/clsConsumer.php");
include_once(PATH."classes/clsPermissions.php");
include_once(PATH."includes/accessRights/manageShowNotes.php");
include_once(PATH."addfolder.php");

$attachmentfiles='';
$styleclass="display:none;";
$objFile=new File();
$objConsumer=new Consumer();
$objPermissions=new Permissions();
$objPermissions->user_id=$_SESSION['sessuserid'];
$folder="report/template_pdf/";
$select=mysql_query("select consumerfolder_id from tbl_consumermaster where consumer_id='".base64_decode($_GET['n'])."'");
$folderName=mysqli_fetch_object($select);

if(isset($_POST['sendShareFiles']))
{
	if(isset($_POST['email1']) && $_POST['email1']!='')
	{
		$email1=$_POST['email1'];
	}
	if(isset($_POST['email2']) && $_POST['email2']!='')
	{
		$email2=$_POST['email2'];
	}
	if(isset($_POST['message']) && $_POST['message']!='')
	{
		$message=$_POST['message'];
	}
	$objFile->parent_id=$_POST['document_id'];
	$select=mysql_query("select * from tbl_document where document_id='".$_POST['document_id']."'");
	$filedescription=mysqli_fetch_object($select);
	$folderTitle=$filedescription->name;
	$folderDescription=$filedescription->Description;
	$objFile->consumer_id=base64_decode($_POST['consumer_id']);
	$selectattachment=$objFile->selectFile();

	if(mysqli_num_rows($selectattachment)>0)
	{
		while($attchmentformail=mysqli_fetch_object($selectattachment))
		{
			$attachmentfiles[]= URL .$folder.$folderName->consumerfolder_id."/".$attchmentformail->name;
		}
	}
		$objFile->attachmentfilesformail=$attachmentfiles;
		$objFile->email1=$email1;
		$objFile->email2=$email2;
		$objFile->attachmentfolderName=$folderTitle;
		$objFile->attachmentfolderDescription=$folderDescription;
		$objFile->message=$message;
		$objFile->sendMailWithAttachment();
		//print_r($attachmentfiles);
		//echo $folder.$folderName->consumerfolder_id."/".$attachments->name; 

		print"<script language=javascript>window.location='".URL."showdetails.php?n=".$_POST['consumer_id']."'</script>";
	//die;
}
if(isset($_POST['submit']) && $_POST['submit']!='')
{
	//print_r($_POST);
	$folder_id=$_POST['folder_id'];
	if($folder_id!='')
	{
		$consumer_id=$_POST['consumer_id'];
		$objFile->name='Untitled';
		$objFile->consumer_id=base64_decode($_POST['consumer_id']);
		$objFile->permission='V,A,E,D';
		$objFile->folder_id=$folder_id;
		$objFile->user_id=$_SESSION['sessuserid'];
		$objFile->isAutomatic='0';
		$objFile->addFile();
		print"<script language=javascript>window.location='".URL."showdetails.php?n=".$consumer_id."'</script>";
	}
}


if(isset($_POST['save']) && $_POST['save']!='')
{
	$objFile->name=$_POST['fileName'];
	$array=$_POST;
	$third = array_splice($array, 2, 2);
	$value=array_values($third);
	$objFile->file_id=$_POST['file_id'];
	$objFile->Description=$value[1];
	$objFile->user_id=$_SESSION['sessuserid'];
	$objFile->consumer_id=base64_decode($_POST['consumer_id']);
	$objFile->editFile();
	if(isset($_FILES['upload_file']) && $_FILES['upload_file']["name"]!='')
	{

		$_POST['filepath'];
		//print_r($_FILES['upload_file']);
		//echo $_POST['filepath']. $_FILES["upload_file"]["name"];
		//die;
		move_uploaded_file($_FILES["upload_file"]["tmp_name"],$_POST['filepath']. $_FILES["upload_file"]["name"]);
		$objFile->consumer_id=base64_decode($_POST['consumer_id']);
		$objFile->name=$_FILES['upload_file']['name'];
		$objFile->documenttype='Attachment';
		$objFile->uploadtype='manual';
		$objFile->Description='';
		$objFile->folder_id=$_POST['file_id'];
		$objFile->isAutomatic='0';
		$objFile->addFile();
	}
	$styleclass="display:block;";
	//print"<script>window.location='".URL."showdetails.php?n=".$_POST['consumer_id']."'</script>";
}

$objFile->consumer_id=base64_decode($_GET['n']);
$selectFile=$objFile->selectFile();
if(mysqli_num_rows($selectFile)>0)
{
	if(isset($_GET['u']) && $_GET['u']!='')
	{
		$objFolder->folder_id=base64_decode($_GET['u']);
	}
	$objFolder->consumer_id=base64_decode($_GET['n']);
	$selectfolder=$objFolder->selectFolder();
	$folderrow=mysqli_num_rows($selectfolder);
	$objConsumer->consumer_id=base64_decode($_GET['n']);
	$row=$objConsumer->getCompanyDetails();
	$consumerfilestatus_id=$row->consumerfilestatus_id;
}



if(isset($_POST['addFileBtn']))
{
	$select=mysql_query("select MAX(sequence_id) as sequence_id from tbl_document where parent_id='".$_POST['document_id']."'");
	$squence=mysqli_fetch_object($select);
	$sequence_id= $squence->sequence_id+1;
	$objFile->name=$_POST['name'];
	$objFile->Description=$_POST['description'];
	$objFile->consumer_id=base64_decode($_POST['consumer_id']);
	$objFile->permission='V,A,E,D';
	$objFile->user_id=$_SESSION['sessuserid'];
	$objFile->folder_id=$_POST['document_id'];
	$objFile->sequence_id =$sequence_id;
	$objFile->isAutomatic='0';
	$objFile->documenttype='File';
	$objFile->addFile();
	print"<script>window.location='".URL."showdetails.php?n=".$_POST['consumer_id']."'</script>";

}

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
	<link type="text/css" rel="stylesheet" href="assets/css/lightbox-form.css">
	<link href="assets/plugins/glyphicons/css/glyphicons.css" rel="stylesheet" />
	<link href="assets/plugins/glyphicons_halflings/css/halflings.css" rel="stylesheet" />
	<!-- END PAGE LEVEL STYLES -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"> </script>
	<script type="text/javascript" src="<?php echo URL; ?>js/popup-section.js"> </script>	
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/mycogo-favicon.png" type="image/x-icon"/>

</head>

<style>

</style>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed show-details">
	<div id="overlay"></div>
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
			<!-- BEGIN PAGE CONTAINER-->
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->
				<div class="row-fluid">
					<div class="span12">
						
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
								<h4 class="all-notes-area-header <?php if($consumerfilestatus_id=='6') { echo "complete"; } else {echo "pending";}?>"><?php echo "All Folders";
								if(!isset($_GET['u'])){?>
									<a href="#" onClick="openbox('Create a New Folder',1)" >
										<img src="img/add-new-folder.png" />
									</a>
									<?php }?>
								</h4>
								<div class="note-single">
								<?php if($folderrow>0) {
								$folderCount=1;
								while($selectFile=mysqli_fetch_object($selectfolder))	{		
								$parent_id=$selectFile->document_id; 
										?>
									<span><div class="folders" id="<?php echo $folderCount;?>" onclick="return showfilesname('filesNotes<?php echo $folderCount ?>')">		<?php echo $folderTitle=$selectFile->name; ?></div>
										<div class="actions user-drop-down">
											<div class="btn-group">
												<a class="btn green" href="#" data-toggle="dropdown">
													<i class="icon-collapse"></i>
												</a>
												<ul class="dropdown-menu pull-right">
													<li><a href="#" onClick="openAddFilePopUp('<?php echo $parent_id;?>')">Add New</a></li>
												</ul>
											</div>
										</div>
									</span>
								<div class="filesNotes<?php echo $folderCount; ?>" <?php echo $styleclass; ?>>
									<?php 
									$objFile->parent_id=$parent_id;
									$selectFile=$objFile->selectFile();
									if(mysqli_num_rows($selectFile)>0)
									{
										$iCount=1;
										while($row=mysqli_fetch_object($selectFile))
										{
											if(($iCount%2)=='0')
											{
												$counted='even';
											}
											else
											{
												$counted='odd';
											}
											?>
											
											<div class="notes-area <?php echo $counted; ?>" id="filesNotes<?php echo $folderCount; ?>_<?php echo $iCount; ?>">
												<ul class="tabs_navigation isotope_filters page_margin_top clearfix">
													<li>
														<a class="notesNab" href="#filter=.<?php echo $row->document_id;?>" title="All Departments">
															<h3><?php echo $row->name; ?></h3>
															<div class="notes-date"><?php echo  $row->createddate;?></div>
														</a>
														<div class="actions" style="display:none;">
															<div class="btn-group">
																<a class="btn green" href="#" data-toggle="dropdown">
																	<i class="icon-collapse"></i>
																</a>
																<ul class="dropdown-menu pull-right">
																<?php if($_SESSION['usertype']=='Consumer')
																{
																	$objPermissions->document_id=$row->document_id;
																	$result=$objPermissions->showPermissions();
																	if($result==true)
																	{	
																		?><li>
																	<a href="#" id="pop-Del" value="<?php echo $row->document_id;?>" class="deleteFiles" name="filesNotes<?php echo $folderCount; ?>_<?php echo $iCount; ?>">
																			<i id="modal_ajax_demo_btn" class="halflings-icon trash"></i> 
																			Delete
																		</a>
																	</li><?php
																	}
																}
																else {?>
																	<li>
																	<a href="#" id="pop-Del" value="<?php echo $row->document_id;?>" class="deleteFiles" name="filesNotes<?php echo $folderCount; ?>_<?php echo $iCount; ?>">
																			<i id="modal_ajax_demo_btn" class="halflings-icon trash"></i> 
																			Delete
																		</a>
																	</li> <?php } ?>
																	<li>
																		<a href="#" name="sharefiles" onClick="openShareFilePopUp('<?php echo $row->document_id;?>')" class="sharefiles" id="pop-Del" value="<?php echo $row->document_id;?>" >
																			<i id="modal_ajax_demo_btn" class="halflings-icon trash"></i> 
																			Share
																		</a>
																	</li>
																</ul>
															</div>
														</div>
														
													</li>
														</li>
													</ul> 
											</div>
											<?php
											$iCount++;
										} 
									}
									?>
									</div>
									
								<?php $folderCount++; } } ?>
							</div></div>
							<div class="contentnotes">
							<?php 
							$objFile->consumer_id=base64_decode($_GET['n']);
							$objFile->parent_id='0';
							$res=$objFile->selectFile();
							if(mysqli_num_rows($res)>0)
							{
								while($result=mysqli_fetch_object($res))
								{
								
									$values=explode(',', $result->permission);
									$key = array_search('E', $values); 
									if($key!='')
									{
										$showing='class="gallery-inside"';
									}
									else
									{
										$showing='class="gallery-insideda"';
									}
									?> 
									<ul class="gallery isotope" style="position: relative; overflow: hidden; height: 423px;"> 
									<li style="position: absolute; left: 0px; top: 0px; transform: translate(0px, 0px);" class="<?php echo $result->document_id;?> gallery_box" id="gallery-item-1">
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" name="consumer_id" value="<?php echo $_GET['n']; ?>">
										<div class="span9">
											
												<input type="hidden" name="file_id" id="fileid<?php echo $result->document_id; ?>" value="<?php echo $result->document_id;?>">
												<div <?php echo $showing; ?>>
													<div class="top-section <?php if($consumerfilestatus_id=='6') { echo "complete"; } else {echo "pending";}?>"></div>
													<div class="editor-area">
														<input type="text" name="fileName" class="fileName" id="fileName<?php echo $result->document_id; ?>" value="<?php echo $result->name;?>">
														<div class="edit editor<?php echo $result->document_id; ?>" style="color:Red" onchange="return testing('this.id');">
															<p class="description" id="description<?php echo $result->document_id; ?>"><?php echo $result->Description;?></p>
														</div>
														<?php  
																if($key!='')
																{
															?>
																<div class="submit-area" style="display:none;">
																	<input type="submit" name="save" value="Save" />
																	<input type="reset" name="reset" value="Reset" />
																</div>
														<?php } ?>
													</div>
												</div>
											
										</div>	
										<div class="attached-files">
										<?php if($key!='')
										{?>
											<input type="hidden" name="filepath" value="<?php echo $folder.$folderName->consumerfolder_id."/"?>" >
												<div class="attach-area">
													<h3>Attach Files</h3>
													<input type="file" value= "Choose File" name="upload_file" id="Upload File"><br>
													<p></p>
													<div class="submit-area">
														<input type="submit" name="save" value="Add/Upload" />
														<input style="display:none;"type="submit" name="preview" value="Preview" />
													</div>
												</div>
											
											<?php } ?>
										</div>
										<span id="documents">
											<div class="attached-doc">
											<?php $objFile->parent_id=$result->document_id;
													$selectattachment=$objFile->selectFile();
													if(mysqli_num_rows($selectattachment)>0)
													{
													?>
														<ul>
													<?php	
													while($attachments=mysqli_fetch_object($selectattachment))
													{
														?> 
														<li>
													<span>
														<?php 
														$fileExt=explode('.',$attachments->name);
														;
														 ?>
														 <img src="icon/<?php echo $fileExt[1];?>.png">
													</span>
													<div class="size-file" id="filesNotes<?php echo $folderCount; ?>_<?php echo 'del'.$folderCount; ?>">
														<a href="<?php echo $folder.$folderName->consumerfolder_id."/".$attachments->name; ?>" target="_blank">
																<?php echo $attachments->name; ?>
															</a>  (1KB)
															<?php if($_SESSION['usertype']=='Consumer')
																{
																	$objPermissions->document_id=$attachments->document_id;
																	$result=$objPermissions->showPermissions();
																	if($result==true)
																	{	
																		?>
																		<a href="#" id="pop-Del" value="<?php echo $attachments->document_id;?>" class="deleteFiles" name="filesNotes<?php echo $folderCount; ?>_<?php echo 'del'.$folderCount; ?>">
																		<i id="modal_ajax_demo_btn" class="halflings-icon trash"></i> 
																			Delete
																		</a>
																		<?php 
																	} 	
																} ?>
													</div>
														
												</li>
											<?php }?>
											</ul>
										<?php }?>
											</div>
										</span>	
									</form>										
									</li>
								</ul>
								<?php  
									} } 
								?>
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
	
	
	<!-- PopUp Section -->
	
	<!-- PopUp Rename Section -->
	<form name="addFileForm" Method="Post" onsubmit="return addFileFormValidation();" action=""  target="_parent">
		<div id="overlay_form" >
		<div id="addFiles">
		<h3>Add File Here</h3>
		<div class="formdata">
			
			<div id="file_error" style="color:#C03; display:none;"> Please Provide File Name. </div>
			<div class="control-group error">
				<label class="control-label">File Name<span class="required">*</span></label>
				<label class="controls">
					<input name="name" class="m-wrap medium" type="text" placeholder="File Name" id="file_name">
				</label>
				<label class="control-label">File Description</label>
				<label class="controls">
					<textarea name="description" class="wrap_text" rows="5" placeholder="Description" id="file_description"></textarea>
				</label>
			</div>
			<div class="form-actions">
				<button class="btn blue" name="addFileBtn" type="submit">
					Save
				</button>
			</div>
			<a class="btn red" onclick='return removeShadow();' id="close" href="#"><i class="icon-remove" ></i></a>
		</div>
		</div>
		<div id="addsharefiles">
			<h3>Share</h3>
			<div class="formdata">
				<h2>Type emails:</h2>
				<div class="input">
				<input type="text" spellcheck="false" data-role="human_input" data-behavior="input_change_emitter" value="" id="email1" name="email1">
				</div>
				<div class="input">
				<input type="text" spellcheck="false" data-role="human_input" data-behavior="input_change_emitter" value="" id="email2" name="email2">
				</div>
				<h2> Add a welcome message for your team: </h2>
				<textarea name="message">Hi there. We will be using Mycotogo to share ideas, gather feedback, and track progress during this project.</textarea>
				<div class="form-actions">
					<button class="btn green" name="sendShareFiles" type="submit">
						Share
					</button>
				</div>
			</div>
			<a class="btn red" onclick='return removeShadowshare();' id="close" href="#"><i class="icon-remove" ></i></a>
		</div>
			<div class="form-actions">
				<input type="hidden"  id="document_id" name="document_id" />
				<input type="hidden" name="consumer_id" value="<?php echo $_GET['n']; ?>"/>
			</div>
			
		</div>
	</form>
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
<script>
function showfilesname(val)
{
	//alert(val);
	$('.'+val).toggle('show');
	return false;
}
	$(".close").click(function() 
	{
		$('.light_box').hide();
		jQuery('.backdrop').hide();
	});
	$('.deleteFiles').click(function() 
	{
		if(confirm("Are you sure you want to delete"))
		{
			var task='deleteFiles';
			var id=$(this).attr('value');
			var name = $(this).attr('name');
			//alert(name);
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
	});
	
</script>
	<script>
		jQuery(document).ready(function() {       
		   // initiate layout and plugins
		  // App.init();
		});
 tinymce.init({
  selector: "div.edit",
  theme: "modern",
  
  plugins: [
   ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker"],
   ["searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking"],
   ["save table contextmenu directionality emoticons template paste importcss colorpicker textpattern"],
   ["textcolor"]
  ],
  //content_css: "css/content.css",
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
<script>
function openAddFilePopUp($folder_id)
{
	document.getElementById('shadowing').style.display='Block';
	document.getElementById('overlay_form').style.display='Block';
	document.getElementById('addFiles').style.display='Block';
	document.getElementById('addsharefiles').style.display='none';
	document.getElementById('document_id').value=$folder_id;
	
}
function openShareFilePopUp(folder_id)
{
	document.getElementById('shadowing').style.display='Block';
	document.getElementById('overlay_form').style.display='Block';
	document.getElementById('addsharefiles').style.display='Block';
	document.getElementById('addFiles').style.display='none';
	document.getElementById('document_id').value=folder_id;
}
function removeShadow()
{
	document.getElementById('shadowing').style.display='none';
	document.getElementById('addFiles').style.display='none';
	
}
function removeShadowshare()
{
	document.getElementById('shadowing').style.display='none';
	document.getElementById('addsharefiles').style.display='none';
}
function addFileFormValidation()
{
	//alert(document.getElementById('file_name').value);
	var el = document.getElementById('addFiles');
    if( el && el.style.display == 'none')    
	{
		var msg='';
		var msg1='';
		var email=document.getElementById('email1');
		var email2=document.getElementById('email2');
		filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (filter.test(email.value)) {
		  // alert('valid');
		}
		else
		{
			var msg='invalid';
		}
		if (filter.test(email2.value)) {
				//alert('valid');
		}
		else
		{
			var msg1='invalid';
		}
		if(msg==''|| msg1=='')
		{
			return true;
		}
		else
		{
			alert('Please Enter Email');
			return false;
		}
		
	}
		
    else 
	{
        if(document.getElementById('file_name').value.trim() =='')
		{
			document.getElementById('file_error').style.display='block';
			return false;
		}
	}
	//return false;
	
}
</script>
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>