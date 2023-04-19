<?php
include_once("private/settings.php");

 include_once(PATH."classes/clsNotification.php");

 include_once(PATH.'classes/clsConsumer.php');

if($_POST['task']=='deleteFiles')

{

	include_once(PATH."classes/clsFolder.php");

	$objFolder= new Folder();

	$objFolder->document_id=base64_decode($_POST['id']);

	$objFolder->DeleteFile();
	echo 'done';

	exit;


}



?>
<!DOCTYPE html>
<head>

	<link type="text/css" rel="stylesheet" href="assets/css/lightbox-form.css">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

	<script src="assets/scripts/lightbox-form.js" type="text/javascript"></script>

</head>

<body>

<?php 

//print_r($_POST);

$objNotification= new Notification();

$objConsumer= new Consumer();

if(isset($_POST['consumer_id']) && $_POST['consumer_id']!='')

{

	$objNotification->consumer_id=$_POST['consumer_id'];

	$objNotification->notificationstatus='pending';

	$res=$objNotification->shownotification();

	$objConsumer->consumer_id=$_POST['consumer_id'];

	$row=$objConsumer->getCompanyDetails();

	$row	=	mysqli_fetch_object($row);

	$consumer_fileno=$row->consumer_fileno;

	$state_id=$row->state_id;

	//$file_no=$row->consumer_fileno;

}

if($_POST['task']=='Incorporated')

{

	?>

	<form id="test" name="123" method="post">

		<div class="controls">

			<div class="popup-submit"> 

				<p>Do you want to incorporate the company ?</p>

				<input type="submit" name="activeBtn" value="Yes" class="btn blue">

				<a href=""  class="btn blue">No </a>

			</div>

		</div>

		<div class="control-group yes-date">

					<label class="control-label"  style="width: 30%;">Yes! Choose a specific date</label>

					<div class="controls">

						<input class="m-wrap m-ctrl-medium date-picker" name="date" size="16" type="text" value="<?php echo date('m/d/Y') ?>" />

						<input type="submit" name="activeBtn" value="Go" class="btn green">

					</div>

				</div>

			<div class="control-group">

			<table class="control-group-table">

				<thead>

					<tr>

						<th style="width:13%;">File No</th>

						<th style="width:19%;">Reminder Date</th>

						<th style="width:68%">Description</th>

					</tr>

				</thead>

				<tbody class="tbody_tr">

					<?php

					if(mysqli_num_rows($res)>0)

					{

						$srNo=1;

						while($row=mysqli_fetch_object($res))

						{?>

							<tr>

								<?php if($row->parent_id=='0')

								{

									$notification_id=base64_encode($row->notification_id);

								}

								else

								{

									$notification_id=base64_encode($row->parent_id);

								}

								?>

								<td><?php echo $consumer_fileno;?></td>

								<td><a href="reminder.php?code=<?php echo $notification_id;?>" ><?php echo $row->notificationdate;?></a></td>

								<td><?php echo $row->notificationdescription;?></td>

							</tr>

							<?php $srNo++;

						}

						

					}?>

				</tbody>

			</table>

			</div>

			<div class="controls">

				<div class="popup-submit">

					<input type="hidden" value ="<?php echo $_POST['consumer_id'];?>" name="consumer_id"/>

					<input type="hidden" value ="<?php echo $state_id;?>" name="state_id"/>

					<input type="hidden" value ="<?php echo $consumer_fileno;?>" name="consumer_fileno"/>

				</div>

			</div>

		</div>

	</form>

	<!-- END PAGE LEVEL PLUGINS -->

	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-components.js"></script>     

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		   FormComponents.init();

		});

	</script>

<?php } 

if($_POST['task']=='Completed')

{ ?>



<form action="#"  method="POST">

									

									<div class="controls">

			<div class="popup-submit"> 

				<p>Have all the documents been uploaded successfully ?</p>

				<input type="submit" name="completedBtn" value="Yes" class="btn blue">

				<a href=""  class="btn blue">No </a>

		</div>

			<div class="control-group">

			<table class="control-group-table">

				<thead>

					<tr>

						<th style="width:13%;">File No</th>

						<th style="width:19%;">Reminder Date</th>

						<th style="width:68%">Description</th>

					</tr>

				</thead>

				<tbody class="tbody_tr">

					<?php

					if(mysqli_num_rows($res)>0)

					{

						$srNo=1;

						while($row=mysqli_fetch_object($res))

						{?>

							<tr>

								<?php if($row->parent_id=='0')

								{

									$notification_id=base64_encode($row->notification_id);

								}

								else

								{

									$notification_id=base64_encode($row->parent_id);

								}

								?>

								<td><?php echo $consumer_fileno;?></td>

								<td><a href="reminder.php?code=<?php echo $notification_id;?>" ><?php echo $row->notificationdate;?></a></td>

								<td><?php echo $row->notificationdescription;?></td>

							</tr>

							<?php $srNo++;

						}

						

					}?>

				</tbody>

			</table>

			</div>

			<div class="controls">

				<div class="popup-submit"> 

					<input type="hidden" value ="<?php echo $_POST['consumer_id'];?>" name="consumer_id"/>

					<input type="hidden" value ="<?php echo $consumer_fileno;?>" name="consumer_fileno"/>

				</div>

			</div>

		</div>

	

								</form>



	<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<script src="assets/scripts/app.js"></script>

	<script src="assets/scripts/form-components.js"></script>     

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		   FormComponents.init();

		});

	</script>

	<!-- END JAVASCRIPTS -->   

<?php } ?>

<!-- BEGIN PAGE LEVEL SCRIPTS -->

	<!-- END PAGE LEVEL SCRIPTS -->

	<script>

		jQuery(document).ready(function() {       

		   // initiate layout and plugins

		   App.init();

		   UIJQueryUI.init();

		});

	</script>

	

</body>

</html>



	<!-- END JAVASCRIPTS -->  

