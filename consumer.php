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

$parameter = '';
$workspace=0;

if($consumerView!='1' )
{ 
	print "<script language=javascript>window.location='index.php'</script>";
}
$workspace=0;
$parameter = '';
if(isset($_GET['workspace']) && $_GET['workspace']=='1')
{
	$workspace = 1;
	$parameter = "&workspace=1";
	$_SESSION['workspace'] = '1';
}
else
{
	$workspace = 2;
	$_SESSION['workspace'] = '';
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
	<!-- <link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" /> -->
	<link rel="stylesheet" href="assets/plugins/data-tables/DT_bootstrap.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">

	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link rel="shortcut icon" href="img/mycogo-favicon.png" type="image/x-icon"/>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<style>
		/*input::-webkit-calendar-picker-indicator {
              opacity: 100;
           }*/
          input::-webkit-calendar-picker-indicator {
  display: none;/* remove default arrow */
}
.myarrow:after {
    content: url(https://i.stack.imgur.com/i9WFO.png);
    margin-left: -20px; 
    padding: .1em;
    pointer-events:none;
}
	</style>
	<script type="text/javascript">
	function del(valid,fileno)
	{
		if(confirm("Are you sure you want to delete member"))
		{			
			window.location="consumer.php?delid="+valid+"&actionprocess=consumer&no="+fileno+"<?php echo $parameter; ?>";
		}
	}

	function reactivate(valid,fileno,conumerid)
	{
		   	var str = "task=reacrhiveMem&fileno="+fileno+"&memberid="+valid+"&consumer_id="+conumerid;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{						
						//alert(response);	
						$('#manage-consumer').html(response);
						//$('#sample_2').DataTable({"sDom": 'lrtip'}).ajax.reload();
						$('#sample_editable_3').DataTable({"sPaginationType":"bootstrap","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>f>tr<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();		

							//$('#sample_2').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();		
						//$('#add_elements').append(response);										
					}
				});	

	}
	function archive(valid,fileno)
	{
		if(confirm("Are you sure you want to Archive member"))
		{
			window.location="consumer.php?archid="+valid+"&actionprocess=consumer&no="+fileno+"<?php echo $parameter; ?>";
		}
	}
	function delete_rec(consumer_record_id, fileno)
	{
		if(confirm("Are you sure you want to delete consumer of file no "+fileno))
		{
			window.location="consumer.php?delid="+consumer_record_id+"&actionprocess=delete_consumer_rec"+"<?php echo $parameter; ?>";
		}
	}
	function archived_rec(consumer_record_id, fileno)
	{
		if(confirm("Are you sure you want to Archive "+fileno))
		{
			window.location="consumer.php?archid="+consumer_record_id+"&actionprocess=archive_consumer_rec"+"<?php echo $parameter; ?>";
		}
	}
	
	</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<?php
	$explodedDate='';
	$objConsumer = new Consumer();
	$objTransaction= new Transaction();
	$objTemplate = new Template();
	$objSystemSetting= new SystemSetting();
	$objUser=new User();
	$objFolder= new Folder();
	$objFile= new File();
	$active_status = 1;
	$checkedValues ='';
	
	
	
	$objConsumer->usertype=$_SESSION['usertype'];
	$objConsumer->user_id=$_SESSION['sessuserid'];

	//print_r($_GET);	

	if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='dearchive')
	{
		echo $objConsumer->consumerrec_id=base64_decode($_GET['code']);
		$objConsumer->dearchiveConsumer();
		//die;
		if (isset($_GET['no']))
			print "<script>window.location='consumer.php?msg=rearch&no=".$_GET['no'].$parameter."'</script>";
		else
			print "<script>window.location='consumer.php?msg=rearch".$parameter."'</script>";
		die;
	}
	
	if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='consumer')
	{
		if (isset($_GET['archid']) && !empty($_GET['archid']))
		{
			$objConsumer->consumeruser_id=base64_decode($_GET['archid']);
			//$objConsumer->deleteConsumer();
			$objConsumer->archiveConsumerMember();
			print "<script>window.location='consumer.php?msg=archmember&no=".$_GET['no'].$parameter."'</script>";
			die;
		}
		if (isset($_GET['delid']) && !empty($_GET['delid']))
		{
			$objConsumer->consumeruser_id=base64_decode($_GET['delid']);
			$response = $objConsumer->deleteConsumer();
			//die;
			print "<script>window.location='consumer.php?msg=".$response."&no=".$_GET['no'].$parameter."'</script>";
			die;
		}
		
	}
	if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='delete_consumer_rec')
	{
		$objConsumer->consumerrec_id=base64_decode($_GET['delid']);
		$objConsumer->deleteConsumer_rec();
		//die;
		print "<script>window.location='consumer.php?msg=del".$parameter."'</script>";
		die;
	}
	if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='archive_consumer_rec')
	{
		$objConsumer->consumerrec_id=base64_decode($_GET['archid']);
		$objConsumer->archiveConsumer_rec();
		//die;
		print "<script>window.location='consumer.php?msg=arch".$parameter."'</script>";
		die;
	}
	if(isset($_GET['active_status']) && $_GET['active_status']=='0')
	{
		$active_status = 0;
	}
	


	if(isset($_POST['activeBtn']) || isset($_POST['completedBtn']))
	{		
		$consumer_id=$_POST['consumer_id'];
		$objConsumer->consumer_id=$consumer_id;
		$row	=	$objConsumer->getCompanyDetails();
		$row	=	mysqli_fetch_object($row);
		$company_email=$row->useremail;
		$lastUserInsert_id=$row->uid;
		$userkey=$row->userkey;
		$consumer_fileno=$row->consumer_fileno;
		$objUser->consumerCompany_name=rtrim($row->companyname,',');
		$objUser->usertype_id='7';
		$objConsumer->usertype=$_SESSION['usertype'];
		$objConsumer->user_id=$_SESSION['sessuserid'];
		$objFolder->user_id=$_SESSION['sessuserid'];
		
		if(isset($_POST['activeBtn']))
		{			
			$explodedDate=explode('/',$_POST['date']);
			if($explodedDate!='')
			{
				$year=$explodedDate['2'];
				$day=$explodedDate['1'];
				$month=$explodedDate['0'];
				$date=$year.'-'.$month.'-'.$day;
			}
			else
			{
				$date='';
			}
			$objConsumer->updatedDate=$date;
			$objConsumer->consumer_id=$consumer_id;
			$objConsumer->consumerfilestatus_id='5';
			$objFolder->consumerfilestatus_id='5';
			$objConsumer->consumer_fileno=$consumer_fileno;
			$objFolder->isAutomatic='1';
			$objTemplate->state_id=$_POST['state_id'];
			$objFolder->permission='V,A,E';
			$objConsumer->StatusUpDate='true';
			$rendomnumber=$objConsumer->generateRandomString();
			$objConsumer->rendomnumber=$rendomnumber;
			$objConsumer->updateConsumer();
			include_once(PATH."generate_pdf.php");
		}
		else
		{
			
			$objConsumer->consumerfilestatus_id='6';
			$objFolder->consumerfilestatus_id='6';
			$objFolder->isAutomatic='0';
			$objFolder->permission='V,A,E';
			$objConsumer->consumer_fileno=$consumer_fileno;
			include_once(PATH."generate_pdf.php");
			$objUser->consumerUserEmail=$company_email;
			$objUser->useremail=$company_email;
			$objUser->lastUserInserted_id=$lastUserInsert_id;
			$objUser->userkey=$userkey;
			$objUser->isFoundUser();
			if($objUser->isFoundUser()==0)
				$objUser->addUser();
			$objConsumer->lastUserInserted_id=$lastUserInsert_id;
			$objConsumer->updateConsumer();
		}	
		print "<script>window.location='consumer.php?no=".(isset($_GET['no'])?$_GET['no']:"").$parameter."'</script>";
	}


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
				<?php include_once(PATH.'includes/consumerView.php');?>
				<div class="testing showMemberInfo"></div>	
				<!-- END PAGE CONTENT -->
			</div>
			<!-- END PAGE CONTAINER-->
		</div>
		<!-- END PAGE -->
	</div>
	<div class="get-paralegal add-new-form-popup-transfer" style="display:none">
		<div class="portlet box green">
			<span class="fas fa-times" id="popup-close-btn"></span>
			<div class="portlet-title">
				<div class="caption"><i class="icon-reorder"></i>Please Enter Paralegal User Email ID</div>
			</div>
			<div class="portlet-body form">
				<div class="inner-wrapper">
					 <input id="select_paralegal" type="text" name="select_paralegal" >
                	<input id="consumer_shift" type="button" name="consumer_shift" class="btn green" value="OK">
				</div>
			</div>
		</div>
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
<!-- 	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script> -->
	<script type="text/javascript" src="assets/plugins/data-tables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="assets/plugins/data-tables/DT_bootstrap.js"></script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/table-editable.js"></script>   
	
	<script>
		jQuery(document).ready(function() {       
		   App.init();
		   TableEditable.init();	
		    $(".select-status").selectpicker();
            //$(".select-status").selectpicker('selectAll');	    
		});
		function showUserInfoPopup(val,fileno,status)
		{
			var URL = $('#viewUrl1').val();
			var consumer_id	= val;
			var task	=	'showuserinfo';
			var query	=	'task='+task+'&consumer_id='+val+'&fileno='+fileno+'&status='+status;
			$.ajax({
				type:"POST",
				url:URL+'includes/bookInfo.php',
				data	:	query,
				success: function(response)
				{
					$('.backdrop').show();
					jQuery('.backdrop').css({'opacity':0.6}) ;
					jQuery('.testing').html(response);
				}
				
			});
		}
		function closepopup()
		{
			jQuery('.testing').html('');
			$('.backdrop').hide();
			jQuery('.backdrop').css({'opacity':0}) ;
		}
	function openPopUp(consumer_id,task)
	{		
		var query = "task="+task+"&consumer_id="+consumer_id;
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

	function updateCheckedStatus(chkElement)
	{
		//alert($(chkElement).closest('td').next('td').html());
		var checkedValues  = document.getElementById("checkedValues").value;
		var checkedValuesfn  = document.getElementById("checkedValuesfn").value;
		console.log(checkedValuesfn);
		if (chkElement.checked == true)
		{
			if (checkedValues == '')
			{
				checkedValues = chkElement.id;
				checkedValuesfn = $(chkElement).closest('td').next('td').html();
			}
			else
			{
				checkedValues = checkedValues + ',' + chkElement.id;
				checkedValuesfn = checkedValuesfn+ ',' +$(chkElement).closest('td').next('td').html();
			}
		}
		else
		{	
			var uncheckedValues = checkedValues.replace(chkElement.id,"");
			var uncheckedValuesfn = checkedValues.replace($(chkElement).closest('td').next('td').html(),"");
			checkedValues = uncheckedValues;
			checkedValuesfn = uncheckedValuesfn;
		}
		console.log(checkedValuesfn);

		checkedValues = checkedValues.replace(',,',',');
		document.getElementById("checkedValues").value = checkedValues;

		checkedValuesfn = checkedValuesfn.replace(',,',',');
		document.getElementById("checkedValuesfn").value = checkedValuesfn;
		//alert(document.getElementById("checkedValues").value);
	}


function updatepdf(selval)
	{
		var state_id='';
		var pConsumer=''
		$('#flash_back').addClass('flash_back');
		strSplit = selval.split(",");
		pConsumer = strSplit[0];
		state_id= strSplit[1];
		var task='update';
		var dataString = "task=checkSignatureStatus&consumer_id="+pConsumer;		
		$.ajax
		({
			type: "POST",
			url: "includes/bookInfo.php",
			data: dataString,
			cache: false,			
			success: function(html)
			{
				if(html.result !='')
				{
					if (confirm('Existing documents marked signed and complete will remain intact.\nNewly created documents will need to be sent to members for signatures.  Do you wish to proceed?' ))
					{
						$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
						var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
						$.ajax
						({
							type: "POST",
							url: "regenrate_pdf.php",
							data: dataString,
							cache: false,
							dataType:"json",
							success: function(html)
							{								
								if(html[0] !='Done')
								{
									alert(html[0]);									
								}
								$('#flash_back').removeClass('flash_back');
								$("#flash").hide();
							}
						});
					}
				}
				else
				{
					var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
					$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
					$.ajax
					({
						type: "POST",
						url: "regenrate_pdf.php",
						data: dataString,
						dataType:"json",
						cache: false,
						success: function(html)
						{								
							if(html[0] !='Done')
							{
								alert(html[0]);									
							}
							$('#flash_back').removeClass('flash_back');
							$("#flash").hide();
						}
					});
				}
			}
		});		
		return false;
	}
	// function rectreatepdf(selval)
	// {
	// 	var state_id='';
	// 	var pConsumer=''
	// 	$('#flash_back').addClass('flash_back');
	// 	strSplit = selval.split(",");
	// 	pConsumer = strSplit[0];
	// 	state_id= strSplit[1];
	// 	var dataString = "task=checkSignatureStatus&consumer_id="+pConsumer;		
	// 	$.ajax
	// 	({
	// 		type: "POST",
	// 		url: "includes/bookInfo.php",
	// 		data: dataString,
	// 		cache: false,
	// 		dataType:"json",
	// 		success: function(html)
	// 		{
	// 			if(html.result !='')
	// 			{
	// 				if (confirm('It seems some documents in this file has been sent for signing and status is '+html.result[1]+'\nAll records related to signing will be REMOVED \nDo You want to continue ?'))
	// 				{
	// 					var task='recreate';				
	// 					$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
	// 					var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
	// 					$.ajax
	// 					({
	// 						type: "POST",
	// 						url: "regenrate_pdf.php",
	// 						data: dataString,
	// 						cache: false,
	// 						success: function(html)
	// 						{
	// 							if(html!='')
	// 							{
	// 								$('#flash_back').removeClass('flash_back');
	// 								$("#flash").hide();
	// 							}
	// 						}
	// 					});
	// 				}
	// 			}
	// 			else
	// 			{
	// 				var task='recreate';						
	// 					$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
	// 					var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
	// 					$.ajax
	// 					({
	// 						type: "POST",
	// 						url: "regenrate_pdf.php",
	// 						data: dataString,
	// 						cache: false,
	// 						success: function(html)
	// 						{				
	// 							if(html!='')
	// 							{
	// 								$('#flash_back').removeClass('flash_back');
	// 								$("#flash").hide();
	// 							}
	// 						}
	// 					});
	// 			}
	// 		}			
	// 	});
	// 	return false;
	// }


	jQuery('body').on('change','#buttonAction', function(){
		var valAction = $(this).val();
		switch(valAction) {
		  case 'Archive':
		    archiveData();
		    break;
		  case 'Transfer':
		    transferFiles();
		    break;
		 case "Workspace":
		    toWorkspace();
		    break;
		  case "noWorkspace":
		  	toWorkspace();
		    break;
		} 
	});
	

	//jQuery('body').on('click','#consumer_archive', function(){
	function archiveData()
	{
	  	var val = $('#checkedValues').val();
		if(val	=='')
		{
			alert('Please Select File');
			$("#buttonAction").val('').change();
			return false;
		}
		if (!confirm('Selected records will be archived'))
		{
			$("#buttonAction").val('').change();
			return false;
		}

		if(val	!='')
		{
	    var postData = 'files='+val+'&task=archive_consumers';
		$.ajax({
			url: 'showResults.php',
			type: 'POST',
			data:postData,
			dataType: 'json',
			success:function(response)
			{
				if (response.data == 'true')
				{
					window.open("consumer.php?msg=arch","_self"); 
				}
				
				// $('#rem_data').html(response);
				// $(".tab-content").prepend('<div class="alert alert-success">				<button data-dismiss="alert" class="close" style="float: right;"></button><strong>Success!</strong>"The reminder has been added"</div>');
				// $('.notification').hide();
				// $('.addremind').html('<i class="icon-bell"></i>Selected Company');
				// $('#sample_2').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]], "iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
				// 	//$('#sample_3').DataTable().ajax.reload();
			}
		});
		}
	}	
	//});

	function toWorkspace()
	{
	  	var val = $('#checkedValues').val();
	  	var valaction = $('#buttonAction').val();
		if(val	== '')
		{
			alert('Please Select File');
			$("#buttonAction").val('').change();
			return false;
		}
	

		if(valaction == 'Workspace')
		{
			if (!confirm('Selected records will be moved to WorkSpace'))
			{
				$("#buttonAction").val('').change();
				return false;
			}
	    	var postData = 'files='+val+'&task=workspace_consumers&status=1';
	    }
	    else
	    {
	    	if (!confirm('Selected records will be removed from WorkSpace'))
			{
				$("#buttonAction").val('').change();
				return false;
			}
	    	var postData = 'files='+val+'&task=workspace_consumers&status=0';
	    }
		$.ajax({
			url: 'showResults.php',
			type: 'POST',
			data:postData,
			dataType: 'json',
			success:function(response)
			{
				if (response.data == 'true')
				{
					window.open("consumer.php?msg=workspace","_self"); 
				}							
			}
		});		
	}

	jQuery('body').on('click','#consumer_del', function(){
	  	var val = $('#checkedValues').val();
		if(val	=='')
		{
			alert('Please Select File');			
			return false;
		}
		if (!confirm('Selected records will be deleted'))
		{
			return false;
		}

		if(val	!='')
		{
	    var postData = 'files='+val+'&task=delete_consumers';
		$.ajax({
			url: 'showResults.php',
			type: 'POST',
			data:postData,
			dataType: 'json',
			success:function(response)
			{
				if (response.data == 'true')
				{
					window.open("consumer.php?active_status=0&msg=del","_self"); 
				}
				
				// $('#rem_data').html(response);
				// $(".tab-content").prepend('<div class="alert alert-success">				<button data-dismiss="alert" class="close" style="float: right;"></button><strong>Success!</strong>"The reminder has been added"</div>');
				// $('.notification').hide();
				// $('.addremind').html('<i class="icon-bell"></i>Selected Company');
				// $('#sample_2').DataTable({"sPaginationType":"full_numbers", "aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]], "iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();
				// 	//$('#sample_3').DataTable().ajax.reload();
			}
		});
		}	
	});

	
	//jQuery('body').on('click','.showPopupTransfer', function(){
	function transferFiles()
	{
		var val = $('#checkedValues').val();
		if(val	=='')
		{
			alert('Please Select File');
			$("#buttonAction").val('').change();
			return false;
		}
		$('.get-paralegal').show();
	}
	//});

	jQuery('#popup-close-btn').click(function(){
		jQuery('.add-new-form-popup-transfer').fadeOut();
	});

	jQuery('body').on('click','#consumer_shift', function(){
	  	var val = $('#checkedValues').val();
	  	var file_nos = $('#checkedValuesfn').val();
	  	var sel_para = $('#select_paralegal').val();
	  	//var sel_para_text = $('#select_paralegal').find(":selected").text();
	  	file_nos = file_nos.replace(",", "\n");
	  
		if(val	=='')
		{
			alert('Please Select File');
			return false;
		}
		if(sel_para	=='')
		{
			alert('Please Select Paralegal User');
			return false;
		}
		check_para = 0;

		

		var postData = 'para_legal='+sel_para+'&task=check_paralegal';
		$.ajax({
			url: 'showResults.php',
			type: 'POST',
			data:postData,
			dataType: 'json',
			success:function(response)
			{
				if (response.data > 0)
				{
					check_para = 1;
					if (!confirm('Following selected files will move to Paralegal '+sel_para+' ?\n\n'+file_nos))
					{
						return false;
					}
					if(val	!='')
					{
					    var postData = 'files='+val+'&para_legal='+response.data+'&task=move_files';
						$.ajax({
							url: 'showResults.php',
							type: 'POST',
							data:postData,
							dataType: 'json',
							success:function(response1)
							{
								if (response1.data == 'true')
								{
									window.open("consumer.php","_self"); 
								}			
							}
						});
					} 
				}
				else
				{
					alert("Error- this user does not exist as paralegal user- try a different email address");
					return false;
				}			
			}
		});
			
	});

	 $('body').on('click','#ARsearch1',function(){
		   	//alert("1");
		   	var fileno = $('#fileNo').val();
		   	var status = $('#notification_status').val();
		   	var formdata = $("#form_filters").serialize();
		   	//alert(status);
		   	var str = "task=filterdata&"+formdata;
					//alert(str);
					$.ajax({
					type:"POST",
					dataType: 'json',
					url:"showResults.php",
					data:str,
					success:function(response)
					{						
						alert(response);	
						// $('#rem_data1').html(response);
						// $('#sample_1').DataTable({"sPaginationType":"full_numbers","aLengthMenu": [[5, 15, 20, -1],[5, 15, 20, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l>r>t<'row-fluid'<'span6'i><'span6'p>>"}).ajax.reload();				
														
					}
				});			
		   });

	jQuery('body').on('click','.allcheckboxes', function(){
            var checked = this.checked;
            $("input.select-item").each(function (index,item) {
            	if (checked == true)
            	{
            		$(this).parent('span').addClass("checked"); 
            	}
            	else
            	{
            		$(this).parent('span').removeClass("checked");
            	}
                item.checked = checked;
            });

            var items=[];
            checkedValues='';
            $("input.select-item:checked:checked").each(function (index,item) {
                items[index] = $(this).attr('id');
               // alert($(this).attr('id'));
            });
            if (items.length < 1) {
            	checkedValues = '';
			
            }else {
                checkedValues  = items.join(',');
            }
            document.getElementById("checkedValues").value = checkedValues;
			});
	
	function rectreatepdf(selval)
	{
		var state_id='';
		var pConsumer=''
		$('#flash_back').addClass('flash_back');
		strSplit = selval.split(",");
		pConsumer = strSplit[0];
		state_id= strSplit[1];
		
		var dataString = "task=checkSignatureStatus&consumer_id="+pConsumer;		
		$.ajax
		({
			type: "POST",
			url: "includes/bookInfo.php",
			data: dataString,
			cache: false,		
			success: function(html)
			{
				if(html.result !='')
				{
					if (confirm('Notice- All existing signed and unsigned formation documents will be deleted during this process.  A new document signing request will need to be sent to all members after the record book is recreated. Do you wish to proceed? ')) 

					// if (confirm('It seems some documents in this file has been sent for signing and status is '+html.result[1]+'\nAll records related to signing will be REMOVED \nDo You want to continue ?'))
					{
						var task='recreate';
						$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
						var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
						$.ajax
						({
							type: "POST",
							url: "regenrate_pdf.php",
							data: dataString,
							cache: false,
							dataType:"json",
							success: function(html)
							{								
								if(html[0] !='Done')
								{
									alert(html[0]);									
								}
								$('#flash_back').removeClass('flash_back');
								$("#flash").hide();
							}
						});
					}
				}
				else
				{
					var task='recreate';
					$("#flash").fadeIn(400).html('<div class="flash_inner"><img src="loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading</span></div>');
					var dataString = "task="+task+"&consumer_id="+pConsumer+"&state_id="+state_id;
					$.ajax
					({
						type: "POST",
						url: "regenrate_pdf.php",
						data: dataString,
						cache: false,
						dataType:"json",
						success: function(html)
						{							
							if(html[0] !='Done')
							{
								alert(html[0]);									
							}
							$('#flash_back').removeClass('flash_back');
							$("#flash").hide();
						}
					});
				}
			}			
		});
		return false;
	}
	</script>
</body>
<!-- END BODY -->
</html>