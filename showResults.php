<?php include_once("private/settings.php");
include_once("classes/clsNotification.php");
include_once(PATH."classes/clsConsumer.php");
include_once(PATH."classes/Utility.php");
include_once(PATH."classes/User.php");
$notificationObj= new Notification();
$consumerObj= new Consumer();
$objUser = new User();




if (($_POST['task']) == 'CheckMaxSignee')
{
	$consumer_id = $_POST['consumer_id'];
	$retu ='0';

	if (!empty($consumer_id))
	{	
		$consumerObj->consumer_id=$_POST['consumer_id'];
		if ($consumerObj->getNoofSignees() >= 2)
		{
			$retu  = '1';
		}

	}
	echo json_encode($retu);
	exit;
}



if (($_POST['task']) == 'check_paralegal')
{
	//print_r($_POST);
	$data = array();
	$data = ['data' => 0];
	$para_legal = $_POST['para_legal'];
	
	if($para_legal != '')
	{
		$objUser->para_legal = $para_legal;
		$res = $objUser->selectUserBytype();
		$data = ['data' => $res];
 
	}
	
	echo json_encode($data);
	exit;	
}


if (($_POST['task']) == 'move_files')
{
	//print_r($_POST);
	$data = array();
	$consumer_files = $_POST['files'];
	$consumer_ids = 	explode(',',$_POST['files']);
	$para_legal = $_POST['para_legal'];
	
	if($consumer_ids !='' && $para_legal != '')
	{
		$consumerObj->created_user_id = $para_legal;
		foreach($consumer_ids as $consumer_id)
		{
			$consumerObj->user_id=base64_decode($consumer_id);
			$response = $consumerObj->move_files();

		}
		$data = ['data' => 'true'];
	}
	
	echo json_encode($data);
	exit;	
}




if (($_POST['task']) == 'CheckdupliEmail')
{
	$consumer_id = $_POST['consumer_id'];
	$email= $_POST['email'];
	$retu ='0';
	//print_r($_POST);

	if (!empty($email))
	{
		$consumerObj->consumeremail=$_POST['email'];
		$consumerObj->consumer_id=$_POST['consumer_id'];
		$consumerObj->consumeruser_id=$_POST['member_id'];
		if ($consumerObj->checkMemberDupliEmail())
		{
			$retu  = '1';
		}

	}
	echo json_encode($retu);
		exit;
}

if (($_POST['task']) == 'CheckdupliConsumer')
{
	$consumer_file= $_POST['consumer_file'];
	$retu ='0';
	if (!empty($consumer_file))
	{
	$sqlQry="select consumer_fileno from tbl_consumermaster where consumer_fileno='".$consumer_file."'";
		
		$mysqli_obj = new DataBase();
		$dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

		$res = mysqli_query($dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)
		{
			$retu = '1';

		}
	}
	echo json_encode($retu);
		exit;
}



if (($_POST['task']) == 'CheckdupliCert')
{
	$consumer_id = $_POST['consumer_id'];
	$cert_no= $_POST['cert_no'];
	$retu ='0';

	if (!empty($cert_no))
	{
	$sqlQry="select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$consumer_id."' and (consumersharecertno = '".$cert_no."' )";
		
		$mysqli_obj = new DataBase();
		$dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

		$res = mysqli_query($dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)
		{
			$retu = '1';

		}
	}
		

	if (!empty($cert_no))
	{
		$sqlQry="select * from tbl_sharetransfer_data where (tbl_sharetransfer_data.cert_no_cancelled='".$cert_no."' or tbl_sharetransfer_data.cert_no_issued_from='".$cert_no."' or tbl_sharetransfer_data.cert_no_issued_to='".$cert_no."')  and (from_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$consumer_id."') or  to_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$consumer_id."')) ";
		
		$res = mysqli_query($dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)
		{
			$retu = '1';

		}
	}
		echo json_encode($retu);
		exit;
}



if (($_POST['task']) == 'searchCertificateInfo')
{
	$data = array();
	$member = '';
	
	if(isset($_POST['member']) && !empty($_POST['member']))
	{
		$member = $_POST['member'];
	}
	if(isset($_POST['certificate']) && !empty($_POST['certificate']) && !empty($member))
	{
		$certno = $_POST['certificate'];
		$sqlQry="SELECT consumershareclass, consumersharetype, consumershareright, consumersharecolor FROM tbl_consumeruser WHERE consumeruser_id = ".$member." and consumersharecertno ='".$certno."'";

		$mysqli_obj = new DataBase();
		$dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

		$res=mysqli_query($dbconnection,$sqlQry);
		if (mysqli_num_rows($res) > 0)
		{
			while($row=mysqli_fetch_object($res))
			{
				$data['class'] = $row->consumershareclass;
				$data['type'] = $row->consumersharetype;
				$data['right'] = $row->consumershareright;
				$data['color'] = $row->consumersharecolor;
			}

		}
		else
		{
			$sqlQry="SELECT consumershareclass, consumersharetype, consumershareright, consumersharecolor FROM tbl_sharetransfer_data WHERE  (( cert_no_issued_from ='".$certno."' and from_userid = '".$member."') or (cert_no_issued_to ='".$certno."'  and to_userid = '".$member."' ))";
			$res=mysqli_query($dbconnection,$sqlQry);
			if (mysqli_num_rows($res) > 0)
			{
				while($row=mysqli_fetch_object($res))
				{
					$data['class'] = $row->consumershareclass;
					$data['type'] = $row->consumersharetype;
					$data['right'] = $row->consumershareright;
					$data['color'] = $row->consumersharecolor;
				}

			}

		}
	}
	echo json_encode($data);		
	exit;
}

//str = "task=reacrhiveMem&fileno="+fileno+"&memberid="+valid;

if (($_POST['task']) == 'reacrhiveMem')
{
	//include_once(PATH."classes/clsConsumer.php");
	$member_id = base64_decode($_POST['memberid']);
	$response  = '';
	$objConsumer = new Consumer();
	$fileno = '';
	$status = 0;
	if (isset($_POST['fileno']))
		$fileno = $_POST['fileno'];
	//if (isset($_POST['status']))
		//$status = $_POST['status'];
	if (isset($_POST['consumer_id']))
		$consumer_id = base64_decode($_POST['consumer_id']);
		

	$objConsumer->consumeruser_id=$member_id;
		//$objConsumer->deleteConsumer();
	$objConsumer->activateConsumerMember();

	$objConsumer->consumeruser_id='';
	$objConsumer->consumer_id=$consumer_id;
	$sqlQry=mysqli_query($dbconnection, "select consumer_fileno,consumer_id from tbl_consumermaster where consumer_id='".$consumer_id."'");
	$consumerInfo	=	mysqli_fetch_object($sqlQry);
	$objConsumer->member_status = $status;
	$resDirector = $objConsumer->showDirector(); 

	$response = '<table class="table table-striped table-hover table-bordered" id="sample_editable_3">
										<thead>
											<tr>
												<th class="hidden">consumer id</th>
												<th>Name</th>
												<th class="hidden-480">Address</th>
												<th>No. of Shares</th>
												<th>Share Type</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>';
											if(mysqli_num_rows($resDirector)>0)
											{ 
												$directorcount=1;
												while($rowDirector=mysqli_fetch_object($resDirector))
												{ 
													$response .= '<tr>
														<td class="hidden"><input type="text" name="consumer_id '.$directorcount.'" value="'.$rowDirector->consumer_id.'"/></td>
														<td>'.$rowDirector->consumerfname; 

														if($rowDirector->consumermname!=''){
															$response .=  ' '.$rowDirector->consumermname;} 

														if($rowDirector->consumerlname!=''){
															$response .= ' '.$rowDirector->consumerlname;}

														$response .='</td><td class="hidden-480">'.$rowDirector->consumeraddress1.'</td>
														<td>'.$rowDirector->balance_shares.'</td>
														<td>'.$rowDirector->consumersharetype.'</td>
														<td>
															<div class="btn-group">
																<a class="btn green" href="#" data-toggle="dropdown">
																<i class="icon-user"></i>
																<i class="icon-angle-down"></i>
																</a>
																<ul class="dropdown-menu pull-right">	<li>
																		<a href="#" onClick="javascript:reactivate(\''.base64_encode($rowDirector->consumeruser_id).'\',\''.base64_encode($consumerInfo->consumer_fileno).'\',\''.base64_encode($consumer_id).'\')"><i class="fa fa-undo"></i> ReActivate</a>
																	</li>

				
																</ul>
															</div>
														</td>
													</tr>';

													$directorcount++;
												}
											} 	
										$response .= '</tbody>
									</table>';

	echo json_encode($response);
			exit;

}

if (($_POST['task']) == 'searchmember')
{
	if(isset($_POST['member_id']) && !empty($_POST['member_id']))
	{
		$member_id = json_decode($_POST['member_id']);

		$consumerObj->consumeruser_id=$member_id;
		$SharesMember= $consumerObj->showDirector();
		//print_r($SharesMember);

		while($rowMember=mysqli_fetch_object($SharesMember))
		{
			//print_r($rowMember);
			echo json_encode($rowMember);
			exit;
		}
		//$rowMember=mysqli_fetch_array($SharesMember);
	
														
	}
	exit;
}

if (($_POST['task']) == 'consumer_list')
{
	if(isset($_POST['consumer_status']) && !empty($_POST['consumer_status']))
	{
		$consumer_status = $_POST['consumer_status']; ?>
		
		$response = '<table class="table table-striped table-bordered table-hover" id="sample_2">
		<thead>
			<tr>
				<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
				<th>File Number</th>
				<th>Company Name</th>
				<th class="hidden-480">Description</th>
				<th class="hidden-480">Reminder</th>
				<th class="hidden-480">Status</th>
			</tr>
		</thead>
		<tbody>';
		<?php 
				
			$notificationObj->consumer_fileno = $cust_file_no;
			$res=$notificationObj->shownotification();			
			$srno=1;
			if(mysqli_num_rows($res)>0)
			{
				while($fetch=mysqli_fetch_object($res))
				{	?>		
					$response .= '<tr class="odd gradeX">		
						<td><input type="checkbox" class="checkboxes" value="1" /></td>
						<td><?php echo $fetch->consumer_fileno;?></td>
						<?php $company_name=rtrim($fetch->companyname,",");  ?>
						<td ><?php echo $company_name?></td>
						<td class="hidden-480"><?php   echo $fetch->notificationdescription;  ?></td>
						<td class="hidden-480"><?php echo $fetch->notificationdate; ?></td>
						<td style="text-transform: capitalize;">
						<span class="btn <?php if($fetch->notificationstatus=="completed") {echo "green";} else{echo "yellow";}?> mini"><?php echo $fetch->notificationstatus;?></span></br>
						
						</td>
					</tr>';
					<?php 
				} 
			}	?>
			</tbody>
	</table>
<?php

	}
}

if (($_POST['task']) == 'screen2')
{
	$response = '';
	$errMessage='';
	//$adminDel = $userObj->selectAdmin();
	$adminDel = $objUser->selectAdmin();
	//print_r($_POST);
	$user_id=$_SESSION['sessuserid'];
	$consumer_ids = $_SESSION['consumer_ids'];
// echo $consumer_ids;

	$response = '<table class="table table-striped table-bordered table-hover selected-checkbox" id="sample_2">
	<thead>
		<tr>
			<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
			<th>File Number</th>
			<th class="hidden-480">Company Contact</th>
			<th class="hidden-480">Company Name</th>
			<th class="hidden-480">InCorporation Date</th>
			<th class="hidden-480"></th>	
		</tr>
	</thead>
	<tbody>';
		$objConsumer = new Consumer();
		$objConsumer->created_user_id=$user_id;
		$objConsumer->consumer_ids = $consumer_ids;
		$res = $objConsumer->selectConsumer();

		// $res =  mysql_query($res);
		$srno=1;
		if(mysqli_num_rows($res)>0)
		{
			while($fetch=mysqli_fetch_object($res))
			{	
				// print_r($fetch);
				
					$response .=  '<tr class="odd gradeX">
					<td><input type="checkbox" class="checkboxes" value="1" /></td>
					<td class="show-reminder"><a href="#">'.$fetch->consumer_fileno.'</a></td>
					<td class="hidden-480">'.$fetch->companycontact.'</td>';

					$company_name=rtrim($fetch->usercname,',');

					$response .= '<td class="hidden-480">'.$company_name.'</td>
					<td class="hidden-480">';
					 if (isset($fetch->updatedDate)) 
					 	$response .= $fetch->updatedDate; 
					 $response .= '</td>
					  <td style="text-transform: capitalize;"><!-- <?php echo $fetch->companyaddress;?> --></td> 
				</tr>';
			} 
		}	
		$response .= '</tbody></table>';
		echo json_encode($response);
}


if (($_POST['task']) == 'all_reminder')
{
		$response = '';

	if (isset($_POST['searchtext']))
	{
		$notificationObj->companyname=$_POST['searchtext'];
	}
	if (isset($_POST['status']))
	{
		if($_POST['status']!='all')
		{
			$notificationObj->notificationstatus=$_POST['status'];
		}
	}
	$notificationObj->user_id = $_SESSION['sessuserid'];

	$response = '<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" /></th>
				<th style="width:10%;">File Number</th>
				<th style="width:20%;">Company Name</th>
				<th style="width:40%;" class="hidden-480">Description</th>
				<th style="width:10%;" class="hidden-480">Reminder</th>
				<th style="width:10%;" class="hidden-480">Status</th></tr>
		</thead>
		<tbody>';
		$user_id=$_SESSION['sessuserid'];
		$notificationObj->user_id = $user_id;
			
		if($user_id!='')
			{
				if($_SESSION['usertype']=='Consumer')
				{
					$consumer_id = '';
					if(isset($_SESSION['bookdetail']) && $_SESSION['bookdetail']!='')
						$consumer_id = base64_decode($_SESSION['bookdetail']);
					$notificationObj->consumer_id= $consumer_id;								
					$res=$notificationObj->showUserNotification();
				}
				else
				{
					$res=$notificationObj->shownotification();
				}
			}
			$srno=1;
			if(mysqli_num_rows($res)>0)
			{
				while($fetch=mysqli_fetch_object($res))
				{	
					
	$response.= '<tr class="odd gradeX">
						<td><input type="checkbox" class="checkboxes" value="1" /></td>
						<td>'.$fetch->consumer_fileno.'</td>';
						 $company_name=rtrim($fetch->companyname,','); 
						$response.='<td >'. $company_name.'</td>
						<td class="hidden-480">'. $fetch->notificationdescription.'</td>
						<td class="hidden-480">'.$fetch->notificationdate.'</td>
						<td style="text-transform: capitalize;">
						<span class="btn ';

						  if($fetch->notificationstatus=='completed') 
						  	{ $response.= 'green';} 
						  else
						  	{ $response.= 'yellow';} 

						  $response.= ' mini">'.$fetch->notificationstatus.'</span></br>
						<a class="btn blue mini" href="editnotification.php?code='.base64_encode($fetch->notification_id).'&action=edit">Edit</a>
						</td>
					</tr>';
				} 
			}	
			$response.='</tbody></table>';
	echo json_encode($response);

}	

if (($_POST['task']) == 'today_reminder')
{
	$response = '';

	$response = '<div class="portlet-body">
	<table class="table table-striped table-bordered table-hover" id="sample_3">
		<thead>
			<tr>
				<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_3 .checkboxes" /></th>
				<th>Company Number</th>
				<th class="hidden-480">Reminder</th>
				<th class="hidden-480">Description</th>
			</tr>
		</thead>
		<tbody>';

		 $notificationObj->today = "today";
			$notificationObj->notificationstatus='pending';
			$user_id=$_SESSION['sessuserid'];
			$notificationObj->user_id = $user_id;
			if($user_id!='')
			{
				if($_SESSION['usertype']=='Consumer')
				{
					$consumer_id = '';
					if(isset($_SESSION['bookdetail']) && $_SESSION['bookdetail']!='')
						$consumer_id = base64_decode($_SESSION['bookdetail']);
					$notificationObj->consumer_id= $consumer_id;						
					$res=$notificationObj->showUserNotification();
				}
				else
				{
					$res=$notificationObj->shownotification();
				}
			}
			$srno=1;
			if(mysqli_num_rows($res)>0)
			{
				while($fetch=mysqli_fetch_object($res))
				{ 	
					$response.= '<tr class="odd gradeX">
						<td><input type="checkbox" class="checkboxes" value="1" /></td>
						<td>'.$fetch->consumer_fileno.'</td>
						<td class="hidden-480"><a href="editnotification.php?code='. base64_encode($fetch->notification_id).'&action=edit">'. $fetch->notificationdate.'</a></td>
						<td>'.$fetch->notificationdescription.'</td>						
					</tr>';
			 } 
			}
			$response.= '</tbody>
	</table>
</div>';

echo json_encode($response);
}

if (($_POST['task']) == 'show_reminder')
{
	if(isset($_POST['cust_id']) && !empty($_POST['cust_id']))
	{
		$cust_file_no = $_POST['cust_id']; ?>
		
		<table class="table table-striped table-bordered table-hover" id="sample_2">
		<thead>
			<tr>
				<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
				<th>File Number</th>
				<th>Company Name</th>
				<th class="hidden-480">Description</th>
				<th class="hidden-480">Reminder</th>
				<th class="hidden-480">Status</th>
			</tr>
		</thead>
		<tbody>
		<?php 
				
			$notificationObj->consumer_fileno = $cust_file_no;
			$res=$notificationObj->shownotification();			
			$srno=1;
			if(mysqli_num_rows($res)>0)
			{
				while($fetch=mysqli_fetch_object($res))
				{	?>
					<tr class="odd gradeX">
						<td><input type="checkbox" class="checkboxes" value="1" /></td>
						<td><?php echo $fetch->consumer_fileno;?></td>
						<?php $company_name=rtrim($fetch->companyname,',');  ?>
						<td ><?php echo $company_name?></td>
						<td class="hidden-480"><?php   echo $fetch->notificationdescription;  ?></td>
						<td class="hidden-480"><?php echo $fetch->notificationdate; ?></td>
						<td style="text-transform: capitalize;">
						<span class="btn <?php if($fetch->notificationstatus=='completed') {echo 'green';} else{echo 'yellow';}?> mini"><?php echo $fetch->notificationstatus;?></span></br>
						
						</td>
					</tr>
					<?php 
				} 
			}	?>
			</tbody>
	</table>
<?php

	}
}



if (($_POST['task']) == 'workspace_consumers')
{
	$data = array();
	$consumer_files = $_POST['files'];
	$consumer_ids = 	explode(',',$_POST['files']);
	$status = $_POST['status'];
	

	if($consumer_ids !='')
	{
		foreach($consumer_ids as $consumer_id)
		{
			$consumerObj->consumerrec_id=base64_decode($consumer_id);
			$consumerObj->status = $status;
			$response = $consumerObj->workspaceConsumer_rec();

		}
		$data = ['data' => 'true'];
	}	
	echo json_encode($data);
}

if (($_POST['task']) == 'archive_consumers')
{
	$data = array();
	$consumer_files = $_POST['files'];
	$consumer_ids = 	explode(',',$_POST['files']);

	$consumerObj->usertype=$_SESSION['usertype'];
	$consumerObj->user_id=$_SESSION['sessuserid'];

	if($consumer_ids !='')
	{
		foreach($consumer_ids as $consumer_id)
		{
			$consumerObj->consumerrec_id=base64_decode($consumer_id);
			$response = $consumerObj->archiveConsumer_rec();

		}
		$data = ['data' => 'true'];
	}
	
	echo json_encode($data);
}

if (($_POST['task']) == 'delete_consumers')
{
	$data = array();
	$consumer_files = $_POST['files'];
	$consumer_ids = 	explode(',',$_POST['files']);
	$consumerObj->usertype=$_SESSION['usertype'];
	$consumerObj->user_id=$_SESSION['sessuserid'];
	
	if($consumer_ids !='')
	{
		foreach($consumer_ids as $consumer_id)
		{
			$consumerObj->consumerrec_id=base64_decode($consumer_id);
			$response = $consumerObj->deleteConsumer_rec();

		}
		$data = ['data' => 'true'];
	}
	
	echo json_encode($data);
}

if (($_POST['task']) == 'add_reminder')
{
	$errMessage='';
	//$adminDel = $userObj->selectAdmin();
	$adminDel = $objUser->selectAdmin(); 
	//print_r($_POST);
	
	if(isset($_POST['Reminder_date']) && $_POST['Reminder_date']!='')
	{
		
		if(isset($_POST['reminderinfo']) && $_POST['reminderinfo']!='')
		{
			$remiderinfo	=	explode(',',$_POST['reminderinfo']);
			$consumer_id=$remiderinfo[0];	
			$code=$remiderinfo[1];
		}
		else if(!isset($_POST['reminderinfo']))
		{
			$res=$notificationObj->showUserNotification();	
			if (mysqli_num_rows($res) > 0)
			{
				$consumerid=mysqli_fetch_object($res);
				$consumer_id=$consumerid->consumer_id;	
				$code=$consumerid->consumer_fileno;
			}
			else
			{
				$code = '';
			}
		}
		$consumer_id = $_POST['checkedValues'];
		if($consumer_id!='')
		{
			$reminder_date	=	date('Y-m-d',strtotime($_POST['Reminder_date']));
			$notificationstatus='pending';
			$notificationObj->notificationdate=$reminder_date;
			$notificationObj->notificationdescription= $_POST['description1'];
			$notificationObj->notificationcreatedby=$_SESSION['usertype'];
			$notificationObj->created_id=$_SESSION['sessuserid'];
			$notificationObj->notification_category_id=0;
			$notificationObj->parent_id=0;
			$notificationObj->notificationstatus=$notificationstatus;
			$notificationObj->usertype=$_SESSION['usertype'];
			$notificationObj->user_id=$_SESSION['sessuserid'];
			// $notificationObj->consumer_fileno=$code;
			$consumer_id = explode(',',$_POST['checkedValues']);
			$userPhone	=array();
			$fetchadmin	=	mysqli_fetch_object($adminDel);
			//print_r($fetchadmin);
			//echo $fetchadmin->contactno;
			if($fetchadmin->contactno!='')
				$userPhone[]	=	$fetchadmin->contactno;
			foreach($consumer_id as $Ids)
			{
				$notificationObj->consumer_id=$Ids;
				$consumerObj->consumer_id=$Ids;
				$qry	=	$consumerObj->selectConsumer();
				// $qry	=	mysql_query($consumertest);
				$data	=	mysqli_fetch_object($qry);
				$userEmail	=	$data->useremail;
				$companyworkphone	=	$data->companycellphone;
					
				$notificationObj->useremail=$userEmail;
				$notificationObj->message_format = '';
				$notificationObj->cc_paralegal  = $_POST['is_cc_paralegal'];
				if(isset($_POST['isemail']) && $_POST['isemail']!='0')
					$notificationObj->message_format= 'mail';
				if(isset($_POST['issms']) && $_POST['issms']!='0')
					$notificationObj->message_format= 'text';

				if(isset($_POST['issms']) && $_POST['issms']!='0' && isset($_POST['isemail']) && $_POST['isemail']!='0' )
					$notificationObj->message_format= 'mail/text';

				if (empty($notificationObj->message_format))
					$notificationObj->message_format= 'system';

				$notificationObj->add_notification();

				if(isset($_POST['isemail']) && $_POST['isemail']!='0')
				{
					if ($reminder_date == date('Y-m-d'))
					{
						//$notificationObj->sendMailNotification();
					}
				}
				if(isset($_POST['issms']) && $_POST['issms']!='0')
				{
					if($companyworkphone!='')
					{
						$con	=	explode('-',$companyworkphone);
						if(count($con)>1)
						{
							$cellcontact	=	'+'.$con['0'].$con['1'].$con['2'].$con['3'];
						}
						else
						{
							$cellcontact	=	$companyworkphone;
						}
						$userPhone[]	=	$cellcontact;
					}
					
				}
			}
			if(isset($_POST['issms']) && $_POST['issms']!='0')
			{
				//print_r($userPhone);
				//die;
				$notificationObj->companyworkphone=$userPhone;
				if ($reminder_date == date('Y-m-d'))
				{
					//$notificationObj->sendSmsNotification();
				} 
				
			}
			//print "<script>window.location='notifications.php?msg=Added#tab_1_3'</script>";
			$$errMessage = 'ok';
		}
		else
		{
			$errMessage='error';
		}
	}
	else
	{
		$errMessage='error';
	}
	//echo $errMessage;


	$user_id=$_SESSION['sessuserid'];
	$consumer_ids = $_POST['checkedValues'];
	$_SESSION['consumer_ids'] = $consumer_ids;

?>
	<table class="table table-striped table-bordered table-hover selected-checkbox" id="sample_2">
	<thead>
		<tr>
			<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
			<th>File Number</th>
			<th class="hidden-480">Company Contact</th>
			<th class="hidden-480">Company Name</th>
			<th class="hidden-480">InCorporation Date</th>
			<th class="hidden-480"></th>


			
		</tr>
	</thead>
	<tbody>
	<?php 
		$objConsumer = new Consumer();
		$objConsumer->created_user_id=$user_id;
		$objConsumer->consumer_ids = $consumer_ids;
		$res = $objConsumer->selectConsumer();

		// $res =  mysql_query($res);
		$srno=1;
		if(mysqli_num_rows($res)>0)
		{
			while($fetch=mysqli_fetch_object($res))
			{	
				// print_r($fetch);
				?>
				<tr class="odd gradeX">
					<td><input type="checkbox" class="checkboxes" value="1" /></td>
					<td class="show-reminder"><a href="#"><?php echo $fetch->consumer_fileno;?></a></td>
					<td class="hidden-480"><?php   echo $fetch->companycontact;  ?></td>
					<?php $company_name=rtrim($fetch->usercname,',');  ?>
					<td class="hidden-480"><?php   echo $company_name;  ?></td>
					<td class="hidden-480"><?php if (isset($fetch->updatedDate)) echo $fetch->updatedDate; ?></td>
					  <td style="text-transform: capitalize;"><!-- <?php echo $fetch->companyaddress;?> --></td> 
				</tr>
				<?php 
			} 
		}	?>
		</tbody>
	</table>
	<?php

}



if (($_POST['task']) == 'searchcomp')
{
	$response = '';

//echo $_POST['searchtext'];
	// var str = "task=searchcomp&fileno="+fileno+"&compnm="+CompNm+"&idate="+idate;

$i_date = '';
$file_id = '';
$CompName = '';

$file_id = $_POST['fileno'];
$CompName = $_POST['compnm'];

if (!empty($_POST['idate']))
	$i_date = $_POST['idate'];

// echo $file_id;
// echo $CompName;
// echo $i_date;

$user_id=$_SESSION['sessuserid'];

	$response = '<table class="table table-striped table-bordered table-hover selected-checkbox" id="sample_2">
	<thead>
		<tr>
			<th style="width:8px;"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes" /></th>
			<th style="width:8px;"><input type="checkbox" class="allcheckboxes" /></th>
			<th>File Number</th>
			<th class="hidden-480">Company Contact</th>
			<th class="hidden-480">Company Name</th>
			<th class="hidden-480">InCorporation Date</th>
			<th class="hidden-480"></th>			
		</tr>
	</thead>
	<tbody>';

		$objConsumer = new Consumer();
		$objConsumer->created_user_id=$user_id;
		$objConsumer->companyname = $CompName;
		$objConsumer->consumer_fileno = $file_id;
		$objConsumer->incorp_date = $i_date;

		$res = $objConsumer->selectConsumer();

		// $res =  mysql_query($res);
		$srno=1;
		if(mysqli_num_rows($res)>0)
		{
			while($fetch=mysqli_fetch_object($res))
			{	
				// print_r($fetch);

				$response.= '<tr class="odd gradeX">
					<td><input type="checkbox" class="checkboxes" value="1" /></td>
					<td><input type="checkbox" class="checkboxes select-item" id="'.$fetch->consumer_id.'" onclick="updateCheckedStatus(this);"/></td>
					<td>'. $fetch->consumer_fileno.'</td>
					<td class="hidden-480">' .$fetch->companycontact.'</td>';
					$company_name=rtrim($fetch->usercname,',');

					$response.= '<td class="hidden-480">'.$company_name.'</td>
					<td class="hidden-480">'; 
					if (isset($fetch->updatedDate)) 
						$response.= $fetch->updatedDate;
						
						$response.='</td>
					  <td style="text-transform: capitalize;"><!-- <?php echo $fetch->companyaddress;?> --></td> 
				</tr>';
				
			} 
		}
		$response.='</tbody></table>';
		echo json_encode($response);
}
