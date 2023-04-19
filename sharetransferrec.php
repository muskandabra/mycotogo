<?php
include_once("private/settings.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/Module.php");
include_once(PATH."includes/accessRights/manageConsumers.php");
include_once(PATH."classes/clsTemplate.php");
include_once(PATH."classes/clsSharetransfer.php");
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
	function delete_rec(transfer_id, id,code)
	{
		if(confirm("Are you sure you want to delete this transfer record"))
		{
			window.location="sharetransferrec.php?delid="+transfer_id+"&id="+id+"&code="+code;
		}
	}


	</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<?php
	$explodedDate='';
	$consumeruser_id='';
	$objConsumer = new Consumer();
	$objTransfer = new ShareTransfer();
	//$objTransaction= new Transaction();
	//$objTemplate = new Template();
	//$objSystemSetting= new SystemSetting();
	$objUser=new User();
	//$objFolder= new Folder();
	//$objFile= new File();
	$active_status = 1;
	
	$objConsumer->usertype=$_SESSION['usertype'];
	$objConsumer->user_id=$_SESSION['sessuserid'];

	//print_r($_GET);

	$workspace = 0;
	$parameter = "";
	if(isset($_SESSION['workspace']) && !empty($_SESSION['workspace']))
	{
		$workspace = 1;
		$parameter = "&workspace=1";
	}

	$code = '';
	if (isset($_GET['code']))
		echo $code =  $_GET['code'];



	

		if (isset($_GET['delid']) && !empty($_GET['delid']))
		{
			$objTransfer->transfer_id=base64_decode($_GET['delid']);
			$response = $objTransfer->deleteTransferrec();
			if (!empty($response))
			{
				$response = "deleted";
			}
			else
			{
				$response = 'nodeleted';
			}
			print "<script>window.location='sharetransferrec.php?actionprocess=".$response."&id=".$_GET['id']."&code=".$code."'</script>";
			die;
		}
		
	

	if(isset($_GET['active_status']) && $_GET['active_status']=='0')
	{
		$active_status = 0;
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
				<?php
				if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='updated')
				{ ?>
					<div style="color:green;">Share transfer record updated successfully</div>
				<?php
				}

				if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='deleted')
				{ ?>
					<div style="color:green;">Share transfer record deleted successfully</div>
				<?php
				}
				
				if(isset($_GET['actionprocess']) && $_GET['actionprocess']=='nodeleted')
				{
				?>
					<div style="color:red;">Error in  deletion, May be certicate already used in other transaction</div>
				<?php
				}

				?>

				<div class="portlet box green">
			        <div class="portlet-title manage-consumer">
			            <div class="caption"><i class="icon-group"></i>Share Transfer Record</div>
			           
			         </div>
			        
			         <div class="portlet-body manage-consumer">
			           <div class="row-fluid archive">
			              <!-- <div class="span6 archivespan">
			                <div class="control-group">
			                  <label class="control-label" for="file_no">Archive</label>
			                  <div class="controls">
			                    <input type="checkbox" name="archive_check" class="m-wrap medium" id="archive_check" value="0">
			                  </div>
			                </div>
			              </div> -->

			            
			              <!--/span-->
			            </div>
			            <input type="hidden" value="<?php echo URL;?>" id="viewUrl1">
			            <div id = "consumer_data">             
			            <table class="table table-striped table-hover table-bordered" id="sample_editable_1">
			               <thead>
			                  <tr>
			                     <th>Date</th>
			                       <th>Date</th>
			                     <th>Transferor</th>
			                     <th>Cancelled Cert No.</th>
			                     <th >New Certificate No.</th>
			                     <th >No. of Shares</th>
			                     <th >Transferee</th>
			                     <th >Certificate No.</th>
			                     <th >No. of Shares</th>			                    
			                     <th>Action</th>
			                  </tr>
			               </thead>
			               <tbody>
			                <?php
			                $objConsumer = new Consumer();
			                $user_id = $_SESSION['sessuserid'];
			                if (isset($_GET['no']) && $_GET['no'])
			                {
			                    $code = base64_decode($_GET['no']);
			                    $objConsumer->consumer_fileno = $code;
			                }
			                if ($user_id != 1) $objConsumer->created_user_id = $user_id;
			                $objConsumer->active_status = $active_status;
			                if (isset($_GET['id']))
			                {
			                	$objTransfer->consumeruser_id = base64_decode($_GET['id']);
			                	$consumeruser_id = $_GET['id'];
			                }
			                $queryleads = $objTransfer->ShareTrnaferRecord();
			                // $queryleads = mysqli_query($dbconnection,$res);

			                if (mysqli_num_rows($queryleads) > 0)
			                {
			                    $srno = 1;
			                    while ($row = mysqli_fetch_object($queryleads))
			                    { ?>                                
			                  <tr>
			                                         
			                  
			                     <td><?php echo  $row->date;?></td>    
			                     <td><?php echo  $row->date;?></td>                                
			                     <td><?php echo $row->fromname ?></td>
			                     <td ><?php echo $row->cert_no_cancelled;?></td>
			                     <td ><?php echo $row->cert_no_issued_from;?></td>
			                     <td ><?php echo $row->no_of_shares_from;?></td>

			                     <td><?php echo $row->toname ?></td>
			                     <td ><?php echo $row->cert_no_issued_to;?></td>
			                     <td ><?php echo $row->no_of_shares;?></td>
		                     	<td>
									<div class="btn-group">
										<a class="btn green" href="#" data-toggle="dropdown">
										<i class="icon-user"></i>
										<i class="icon-angle-down"></i>
										</a>
										<ul class="dropdown-menu pull-right">
											<li>
					                     	<a href="editsharetransferform.php?number=<?php echo base64_encode($row->transfer_id);?>&id=<?php echo $consumeruser_id;?>&code=<?php echo $code; ?>">
					                            <i class="icon-pencil"></i> Edit
					                                    </a>
				                             </li>
				                        	<li>
			                                    <a href="#" onClick="javascript:delete_rec('<?php echo base64_encode($row->transfer_id); ?>','<?php echo $consumeruser_id;?>','<?php echo $code; ?>')">
			                                        <i class="icon-trash"></i>Delete
			                                    </a>
	                                		</li>
			                     		</ul>
			                     	</div>
			                     </td>
	
			                  </tr>
			                  <?php                             
			                  $srno++;                                
			                  //$iCount++;                         
			                   }                       }                       ?>                  
			               </tbody>
			            </table>
			          </div>

			         </div>

			     </div>
			       <?php
					if (isset($_GET['code']))
					{?> 
						<form>
						<input class= "button1" type="button" value="Back" onclick="window.location.href='consumer.php?no=<?php echo $_GET['code'].$parameter; ?>'" />
						</form> 
					<?php }
					?>
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