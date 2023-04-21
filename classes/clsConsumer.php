<?php include_once(PATH."classes/Utility.php");
include_once(PATH."classes/User.php");?>
<?php 
Class Consumer
{
	var $consumer_fileno;
	var $jarea_id;
	var $companytype_id;
	var $companyname;
	var $companycontact;
	var $companyworkphone;
	var $companycellphone;
	var $companyfax;
	var $companyemail;
	var $companyresaddress;
	var $companymailingaddress;
	var $companyrecordaddress;
	var $consumeruser_id;
	var $consumerfname;
	var $consumermname;
	var $consumerlname;
	var $consumerisofficer;
	var $consumerofficertitle;
	var $consumerisshareholder;
	var $consumernoofshares;
	var $consumershareclass;
	var $consumersharecolor;
	var $consumersharetype;
	var $consumershareright;
	var $consumersharecertno;
	var $consumeraddress1;
	var $consumercity;
	var $consumerstate_id;
	var $consumerzipcode;
	var $consumerusertype;
	var $consumerotherofficertitle;
	var $created_user_id='';
	var $consumer_id='';
	var $registeration_type='';
	var $name='';
	var $business_type='';
	var $state_province='';
	var $consumerisdirector='';
	var $consumerpricepershare='0';
	var $consumertotalshare='';
	var $consumerfilestatus_id='1';
	var $StatusUpDate='';
	var $rendomnumber='';
	var $usertype='';
	var $user_id='';
	var $lastUserInserted_id='';
	var $updatedDate;
	var $statusfor='';
	var $consumer_ids='';
	var $incorp_date = '';
	var $consumerrec_id = '';
	var $active_status=1;
	var $balance_shares = 0;
	var $member_status = 1;
	var $newconsumer_fileno= '';
	var $consumer_old_fileno = '';
	var $companyreg_address = '';
	var $created_user_id_old='';
	var $filter = 0;
	var $filter_status = 0;
	var $monthOfIncorp = array();
	var $yearOfIncorp = '';
	var $member_name = '';
	var $criteria = '';
	var $member_email = '';
	var $workspace = '';
	var $signatureMode = '';
	var $digitalSignStatus = '';
	var $is_deleted = 0;


	
	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
		$this->updatedDate = date('Y-m-d H:i:s');
	}



	function checkMemberDupliEmail()
	{
		if($this->consumer_id!='')
		{
			$sqlQry = "select * from tbl_consumeruser where consumeremail='".$this->consumeremail."' and consumer_id='".$this->consumer_id."' and consumeruser_id != '".$this->consumeruser_id."'";
			$query=mysqli_query($this->dbconnection,$sqlQry);
			if (mysqli_num_rows($query) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}

		}
	}

	function update_balance_shares()
	{
		if($this->consumeruser_id!='')
		{
		 $sqlQry = "update tbl_consumeruser set balance_shares = ".$this->balance_shares." where consumeruser_id='".$this->consumeruser_id."'";
		 mysqli_query($this->dbconnection,$sqlQry);
		}
	}

	function getNoofSignees()
	{
		if($this->consumer_id!='')
		{
			$sqlQry	= "select consumeruser_id from tbl_consumeruser where consumer_id='".$this->consumer_id."' and share_signee =1";
			$query=mysqli_query($this->dbconnection,$sqlQry);		
			return mysqli_num_rows($query);			
		}
	}

	function updateRecordbookStatus()
	{
		if($this->consumer_id!='')
		{
			$sqlQry	=	"update tbl_consumermaster set RecordbookStatus = '1' where consumer_id='".$this->consumer_id."'";
			mysqli_query($this->dbconnection,$sqlQry);
		}
	}

	function deleteConsumer_rec()
	{
		$user_idl = '';	
		$user_cnt = '';
		$curdate = date('Y-m-d H:i');


		$sqlQry = "select user_id,consumerfolder_id from tbl_consumermaster where is_deleted = 0 and consumer_id='".$this->consumerrec_id."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_idl =  $rows['user_id'];
		$folder_id = $rows['consumerfolder_id'];

		//$sqlQry	=	"Delete from tbl_consumermaster where consumer_id='".$this->consumerrec_id."'";
		$sqlQry	=	"update tbl_consumermaster set is_deleted = 1, deleted_on = '".$curdate."' where consumer_id='".$this->consumerrec_id."'";
		
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='consumerrec_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Delete Consumer master ';
		$objUtility->dataId=$this->consumerrec_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Delete Consumer master with id:['.$this->consumerrec_id.']';
		$objUtility->logTrack();

		$sqlQry = "select count(*) as cnt_user from tbl_consumermaster where is_deleted = 0 and  user_id='".$user_idl."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_cnt =  $rows['cnt_user'];

		$sqlQry = "select count(*) as cnt_user from tbl_consumermaster where is_deleted = 0 and active_status = 1 and  user_id='".$user_idl."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_cnt_status =  $rows['cnt_user'];

		if ($user_cnt == 0)
		{
			$sqlQry	=	"update tbl_user set isDeleted  = 1 where user_id='".$user_idl."'";
			mysqli_query($this->dbconnection,$sqlQry);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->datatableidField ='user_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Delete user ';
			$objUtility->dataId=$user_idl;
			$objUtility->user_id=$this->user_id;
			$objUtility->description='Delete User with id:['.$user_idl.']';
			$objUtility->logTrack();
		}

		if ($user_cnt_status== 0)
		{
			$sqlQry	=	"update tbl_user set userstatus_id = 2  where user_id='".$user_idl."'";
			mysqli_query($this->dbconnection,$sqlQry);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->datatableidField ='user_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Archive user ';
			$objUtility->dataId=$user_idl;
			$objUtility->user_id=$this->user_id;
			$objUtility->description='Archive User with id:['.$user_idl.']';
			$objUtility->logTrack();
		}

		// $folder=PATH."report/template_pdf/";
		// $folderpdf=$folder.$folder_id ."/";
		//  $files1 = scandir($folderpdf,1);

		// $checkValue= count($files1)-2;

		// $totalValue= count($files1);

		// $iLoop=1;

		// foreach($files1 as $files)

		// {

		// 	if($iLoop<=$checkValue)

		// 	{

		// 		//echo $file_name= $folderpdf.$files;
		// 		//echo "<br>";

		// 		//unlink($file_name);

		// 	}

		// 	$iLoop++;

		// }

	}

	function workspaceConsumer_rec()
	{		
		$sqlQry="update tbl_consumermaster set workspace = '".$this->status."' where consumer_id='".$this->consumerrec_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='consumerrec_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Workspace Consumer master ';
		$objUtility->dataId=$this->consumerrec_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Set  Workspace '.$this->status.' Consumer master with id:['.$this->consumerrec_id.']';
		$objUtility->logTrack();			
	}

	function dearchiveConsumer()
	{		
		$sqlQry="update tbl_consumermaster set active_status  = 1 where consumer_id='".$this->consumerrec_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='consumerrec_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='DeArchive Consumer master ';
		$objUtility->dataId=$this->consumerrec_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='DeArchive Consumer master with id:['.$this->consumerrec_id.']';
		$objUtility->logTrack();


		$sqlQry = "select user_id from tbl_consumermaster where is_deleted = 0 and consumer_id='".$this->consumerrec_id."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_idl =  $rows['user_id'];


		$sqlQry	=	"update tbl_user set userstatus_id  = 1 where user_id='".$user_idl."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_user';
		$objUtility->datatableidField ='user_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Activate user ';
		$objUtility->dataId=$user_idl;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Activate User with id:['.$user_idl.']';
		$objUtility->logTrack();
		
	}

	function updatecreated_id()
	{		
		$sqlQry="update tbl_consumermaster set created_user_id  = '".$this->user_id."' where user_id='".$this->user_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='created_user_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Changed creted user id';
		$objUtility->dataId=$this->user_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Changed creted user id with id:['.$this->user_id.']';
		$objUtility->logTrack();
	}

	function move_files()
	{	
		$curdate = date('Y-m-d');
		$sqlQry="update tbl_consumermaster set created_user_id_old = created_user_id  where consumer_id='".$this->user_id."'";
		mysqli_query($this->dbconnection,$sqlQry);	
		$sqlQry="update tbl_consumermaster set created_user_id  = '".$this->created_user_id."' , last_transfered_on = '".$curdate."' where consumer_id='".$this->user_id."'";
		mysqli_query($this->dbconnection,$sqlQry);

		$sqlQry="update tbl_document set created_user_id  = '".$this->created_user_id."'  where consumer_id='".$this->user_id."'";
		mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='created_user_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Changed creted user id';
		$objUtility->dataId=$this->created_user_id;
		$objUtility->user_id=$this->created_user_id;
		$objUtility->description='Changed creted user id with id:['.$this->created_user_id.']';
		$objUtility->logTrack();
	}
	
	function deleteConsumer()
	{
		$return = '';
		$cert_no = '';
		$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."'  and member_dol != ''  ";
		$res = mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)
		{
			$return = 'nodelmember Member Resignation Record is already there';
		}


		$sqlQry="select  consumersharecertno  from tbl_consumeruser where tbl_consumeruser.consumeruser_id='".$this->consumeruser_id."'";
		$res = mysqli_query($this->dbconnection,$sqlQry);
		if(mysqli_num_rows($res)>0)
		{
			$row = mysqli_fetch_object($res);
			$cert_no = $row->consumersharecertno;
		}
		

		$sqlQry="select * from tbl_sharetransfer_data where (from_userid ='".$this->consumeruser_id."' or to_userid ='".$this->consumeruser_id."')";
		$res = mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)
		{
			if(mysqli_num_rows($res)>1)
			{
				$return = 'nodelmember Member share Transfer Record is already there';
			}
			else
			{
				while($details=mysqli_fetch_object($res))
				{
					if ($details->from_userid ==0 && $details->cert_no_issued_to==$cert_no )
					{
						$return = '';
					}
					else
					{
						$return = 'nodelmember Member share Transfer Record is already there';
					}
				}

			}										
		}
		//echo $return;
		//die;
		if (empty($return))
		{
			$sqlQry="Delete from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";
			mysqli_query($this->dbconnection,$sqlQry);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_consumeruser';
			$objUtility->datatableidField ='consumeruser_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Delete Consumer user/member ';
			$objUtility->dataId=$this->consumeruser_id;
			$objUtility->user_id=$this->user_id;
			$objUtility->description='Delete Consumer member with id:['.$this->consumeruser_id.']';
			$objUtility->logTrack();
			$return = 'delmember';
		}
		//echo $return;
		return $return;
	}
	
	function archiveConsumer_rec()
	{
		$user_idl = '';	
		$user_cnt = '';
		$sqlQry	=	"update tbl_consumermaster set active_status  = 0 where consumer_id='".$this->consumerrec_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='consumerrec_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Archive Consumer master ';
		$objUtility->dataId=$this->consumerrec_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Archive Consumer master with id:['.$this->consumerrec_id.']';
		$objUtility->logTrack();

		$sqlQry = "select user_id from tbl_consumermaster where is_deleted = 0 and  consumer_id='".$this->consumerrec_id."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_idl =  $rows['user_id'];

		$sqlQry = "select count(*) as cnt_user from tbl_consumermaster where is_deleted = 0 and active_status = 1 and user_id='".$user_idl."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$rows=mysqli_fetch_array($query);
		$user_cnt =  $rows['cnt_user'];

		if ($user_cnt == 0)
		{
			$sqlQry	=	"update tbl_user set userstatus_id  = 2 where user_id='".$user_idl."'";
			mysqli_query($this->dbconnection,$sqlQry);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->datatableidField ='user_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Archive user ';
			$objUtility->dataId=$user_idl;
			$objUtility->user_id=$this->user_id;
			$objUtility->description='Archive User with id:['.$user_idl.']';
			$objUtility->logTrack();
		}

	}
	function selectShiftedFiles()
	{
		if($this->created_user_id_old !='' && $_SESSION['usertype']!='Consumer')
		{
			$sqlQry="select a.*,b.*,c.useremail as cur_paralegal , a.companyname usercname from tbl_consumermaster a,tbl_user b, tbl_user c where a.user_id=b.user_id and a.created_user_id=c.user_id and (a.created_user_id_old='".$this->created_user_id_old."') ORDER BY a.last_transfered_on  DESC";
			//echo $sqlQry;
			return mysqli_query($this->dbconnection,$sqlQry);
		}

	}



	function selectConsumer()
	{
		if($this->consumer_id!='')
		{
			$sqlQry="select * , tbl_consumermaster.companyname usercname from tbl_consumermaster,tbl_user where tbl_consumermaster.user_id=tbl_user.user_id and  tbl_consumermaster.is_deleted = 0 and consumer_id='".$this->consumer_id."'";
		}
		else if($this->created_user_id!='' && $_SESSION['usertype']!='Consumer')
		{
			$sqlQry="select * , tbl_consumermaster.companyname usercname from tbl_consumermaster,tbl_user where tbl_consumermaster.user_id=tbl_user.user_id and tbl_consumermaster.is_deleted = 0 and  (tbl_consumermaster.user_id='".$this->created_user_id."' or created_user_id='".$this->created_user_id."')";
			if($this->consumer_fileno!='')
			{
				$sqlQry = $sqlQry. " and consumer_fileno='".$this->consumer_fileno."'";
			}
			if($this->companyname!='')
			{
				$sqlQry = $sqlQry. " and tbl_consumermaster.companyname like '%".$this->companyname."%'";
			}
			if($this->consumer_ids!='')
			{
				$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (".$this->consumer_ids.")";
			}
			
			if($this->incorp_date!='')
			{
				$sqlQry = $sqlQry. " and date(tbl_consumermaster.updatedDate) =date('".date('Y-m-d',strtotime($this->incorp_date))."')";
			}
			if ($this->filter)
			{	
				if (count($this->monthOfIncorp) > 0)
				{ 										
					$monthOfIncorp = implode(",", $this->monthOfIncorp); 
					$sqlQry = $sqlQry. " and month(tbl_consumermaster.updatedDate) in  (".$monthOfIncorp.")";
				}
				if ($this->yearOfIncorp !=  '')
				{ 															 
					$sqlQry = $sqlQry. " and year(tbl_consumermaster.updatedDate) =   ".$this->yearOfIncorp;
				}
				
				if ($this->filter_status > 0)
				{
					$sqlQry = $sqlQry. " and consumerfilestatus_id = ".$this->filter_status;
				}
				if ($this->member_name !=  '')
				{		
					$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (SELECT consumer_id from  tbl_consumeruser WHERE concat(consumerfname,' ', consumermname, ' ', consumerlname)  like '%".$this->member_name."%' and tbl_consumeruser.consumer_id = tbl_consumermaster.consumer_id and active_status = 1) ";
				}
				if ($this->member_email !=  '')
				{		
					$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (SELECT consumer_id from  tbl_consumeruser WHERE consumeremail  like '%".$this->member_email."%' and tbl_consumeruser.consumer_id = tbl_consumermaster.consumer_id and active_status = 1) ";
				}				
				if ($this->digitalSignStatus == 'completed')
				{
					$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (SELECT consumer_id FROM `tbl_document` where oneSpanSignId != ''  and oneSpanSign_Status = 'COMPLETED' and uploadtype != 'manual') and consumer_id not in (select consumer_id from tbl_document where oneSpanSignId != ''  and oneSpanSign_Status != 'COMPLETED' and uploadtype != 'manual') ";
				}
				if ($this->digitalSignStatus == 'processing')
				{
					$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (SELECT consumer_id FROM `tbl_document` where oneSpanSignId != '' and oneSpanSign_Status = 'COMPLETED' and uploadtype != 'manual') and consumer_id in (select consumer_id from tbl_document where oneSpanSignId != '' and oneSpanSign_Status != 'COMPLETED' and uploadtype != 'manual')  ";
				}
				if ($this->digitalSignStatus == 'pending')
				{
					$sqlQry = $sqlQry. " and tbl_consumermaster.consumer_id in (SELECT consumer_id FROM `tbl_document` where oneSpanSignId != '' and oneSpanSign_Status != 'COMPLETED' and uploadtype != 'manual') and consumer_id not in (select consumer_id from tbl_document where oneSpanSignId != '' and oneSpanSign_Status = 'COMPLETED' and uploadtype != 'manual')  ";
				}				
									
			}			
			
		}

		else if($_SESSION['usertype']=='Consumer')
		{
			//tbl_consumermaster.companyname usercname added by bimal earlier it gives error
			$sqlQry="select * from tbl_consumermaster where (user_id='".$this->created_user_id."' or created_user_id='".$this->created_user_id."') and tbl_consumermaster.is_deleted = 0";
		}

		else
		{
			$sqlQry="select * from tbl_consumermaster,tbl_user where tbl_consumermaster.user_id=tbl_user.user_id and tbl_consumermaster.is_deleted = 0";
		}
		
		if (!empty($this->workspace))
		{			
			if ($this->workspace == 1)
			{
				$sqlQry = $sqlQry. " and workspace = 1 ";
			}
			else
			{
				$sqlQry = $sqlQry. " and workspace != 1 ";
			}
		}

		    $sqlQry = $sqlQry. " and active_status = ".$this->active_status." ORDER BY `tbl_consumermaster`.`consumer_id` DESC";

		    //echo $sqlQry;
		    //die;

			return mysqli_query($this->dbconnection,$sqlQry);
		    // return $sqlQry;
	}
	
	function selectConsumerFileNo()
	{
		$sqlQry="select consumer_fileno,consumer_id from tbl_consumermaster where tbl_consumermaster.is_deleted = 0";
		if($this->created_user_id!='' && $_SESSION['usertype']!='Consumer')
		{
			$sqlQry= $sqlQry. " and created_user_id='".$this->created_user_id."'";
		}
		return mysqli_query($this->dbconnection,$sqlQry);
	}
	
	function showDirector()
	{
		if($this->consumeruser_id!='')
		{
			$sqlQry="select * from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";
		}
		else
		{
			$sqlQry="select * from tbl_consumeruser where consumer_id='".$this->consumer_id."' and active_status = ".$this->member_status;
		}
		//echo $sqlQry;
		//die;
		return mysqli_query($this->dbconnection,$sqlQry);
	}

	function showMembers()
	{
		$sqlQry="select * from tbl_consumeruser where consumer_id='".$this->consumer_id."' and consumeruser_id not in ('".$this->consumeruser_id."')";
		return mysqli_query($this->dbconnection,$sqlQry);
	}

	function addConsumer()
	{
		$insert="insert into tbl_consumermaster set
		consumer_fileno='".$this->consumer_fileno."',
		state_id='".$this->jarea_id."',
		created_user_id='".$this->created_user_id."',
		companytype_id='".$this->companytype_id."',
		companyname='".addslashes($this->companyname)."',
		companycontact='".addslashes($this->companycontact)."',
		companyworkphone='".$this->companyworkphone."',
		companycellphone='".$this->companycellphone."',
		companyfax='".$this->companyfax."',
		user_id='".$this->user_id."',
		companymailingaddress='".addslashes($this->companymailingaddress)."',
		registeration_type='".$this->registeration_type."',
		name='".$this->name."',
		consumerfilestatus_id='".$this->consumerfilestatus_id."',
		createdDate=now(),
		companyrecordaddress='".addslashes($this->companyrecordaddress)."',
		business_type='".$this->business_type."',
		state_province='".$this->state_province."',
		consumerfolder_id='".@$this->consumerfolder_id."',
		updatedDate=now(),
        is_deleted='".@$this->is_deleted."',
		active_status='".$this->active_status."',
		oneSpanSignId='".@$this->oneSpanSignId."',
		workspace='".$this->workspace."',
		created_user_id_old='".$this->created_user_id_old."',
		companyreg_address='".$this->companyreg_address."',
		deleted_on='".@$this->deleted_on."',
		last_transfered_on='".@$this->last_transfered_on."' ";
		
        
		
		mysqli_query($this->dbconnection,$insert);
		echo $insert;

		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumermaster';
		$objUtility->datatableidField ='consumer_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Added Consumer';
		$objUtility->user_id=$this->created_user_id;
		$objUtility->dataId=$this->lastInsertedId;
		$objUtility->description='Added Consumer with File No: ['.$this->consumer_fileno.']';
		$objUtility->logTrack();
	}


	function updateConsumer()
	{
		
		if($this->updatedDate=='')
		{
			$this->updatedDate=date('Y-m-d H:i:s');
		}
		
		if($this->lastUserInserted_id!='')
		{
			// After Incorporated goes here
			$update	=	"update tbl_consumermaster set consumerfilestatus_id='".$this->consumerfilestatus_id."'";
			if($this->lastUserInserted_id!='')
			$update	.=	", user_id='".$this->lastUserInserted_id."'";
			
			$update	.=	" where consumer_id ='".$this->consumer_id."'";
			
			//$this->sendMailAsPerStatus($this->consumerfilestatus_id);
			
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_consumermaster';
			$objUtility->datatableidField ='consumer_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Updated Consumer';
			$objUtility->dataId=$this->consumer_id;
			$objUtility->user_id=$this->user_id;
			$objUtility->description='Updated Consumer File No:['.$this->consumer_fileno.'] with Consumer File Status ['.$this->getConsumerStatus($this->consumerfilestatus_id).'] and User Id:['.$this->lastUserInserted_id.']';
			$objUtility->logTrack();
			//echo $update;
			return mysqli_query($this->dbconnection,$update);
		}

		else
		{
			if($this->consumerfilestatus_id!='1' && $this->StatusUpDate=='')
			{
				// After adding Reminder Paralegal/Admin goes here
				$update="update tbl_consumermaster set
				consumerfilestatus_id='".$this->consumerfilestatus_id."',
				updatedDate='".$this->updatedDate."',".($this->signatureMode != ''? "signatureMode ='".$this->signatureMode."'":'')."			
				where consumer_id ='".$this->consumer_id."'";
				
				//$this->sendMailAsPerStatus($this->consumerfilestatus_id);
				$objUtility = new Utility();
				$objUtility->dataTable = 'tbl_consumermaster';
				$objUtility->datatableidField ='consumer_id';
				$objUtility->usertype=$this->usertype;
				$objUtility->action='Updated Consumer';
				$objUtility->dataId=$this->consumer_id;
				$objUtility->user_id=$this->user_id;
				$objUtility->description='Updated Consumer File No:['.$this->consumer_fileno.'] with Consumer File Status ['.$this->getConsumerStatus($this->consumerfilestatus_id).']';
				$objUtility->logTrack();
				//echo $update;
				//echo "1";
				return mysqli_query($this->dbconnection,$update);
			}
			else
			{
				
				if($this->StatusUpDate=='true')
				{
					// After Work in process Paralagal goes here.
					$update="update tbl_consumermaster set
					consumerfilestatus_id='".$this->consumerfilestatus_id."',
					updatedDate='".$this->updatedDate."',
					consumerfolder_id='".$this->rendomnumber."'
					where consumer_id ='".$this->consumer_id."'";
					//$this->sendMailAsPerStatus($this->consumerfilestatus_id);
					mysqli_query($this->dbconnection,$update);
					//echo $update;
				    //echo "2";
					$objUtility = new Utility();
					$objUtility->dataTable = 'tbl_consumermaster';
					$objUtility->datatableidField ='consumer_id';
					$objUtility->usertype=$this->usertype;
					$objUtility->action='Updated Consumer';
					$objUtility->dataId=$this->consumer_id;
					$objUtility->user_id=$this->user_id;
					$objUtility->description='Updated Consumer File No:['.$this->consumer_fileno.'] with Consumer File Status ['.$this->getConsumerStatus($this->consumerfilestatus_id).']';
					$objUtility->logTrack();
					
				}
				else
				{
					$update="update tbl_consumermaster set
					state_id='".$this->jarea_id."',
					companytype_id='".$this->companytype_id."',
					companyname='".addslashes($this->companyname)."',
					companycontact='".addslashes($this->companycontact)."',
					companyworkphone='".$this->companyworkphone."',
					companycellphone='".$this->companycellphone."',
					companyfax='".$this->companyfax."',
					companymailingaddress='".addslashes($this->companymailingaddress)."',
					companyrecordaddress='".addslashes($this->companyrecordaddress)."',
					companyreg_address='".addslashes($this->companyreg_address)."',
					user_id='".$this->user_id."'
					where consumer_id ='".$this->consumer_id."'";
					//die;
					//echo $update;
					//echo "3";
					
					mysqli_query($this->dbconnection,$update);
					$objUtility = new Utility();
					$objUtility->dataTable = 'tbl_consumermaster';
					$objUtility->datatableidField ='consumer_id';
					$objUtility->usertype=$this->usertype;
					$objUtility->action='Updated Consumer';
					$objUtility->dataId=$this->consumer_id;
					$objUtility->user_id=$this->user_id;
					$objUtility->description='Updated consumer File No:['.$this->consumer_fileno.']';
					$objUtility->logTrack();
				}
				
			}
		}
	}

	function addConsumerUser()
	{
		$insert="insert into tbl_consumeruser set
		consumerfname='".$this->consumerfname."',
		consumermname='".$this->consumermname."',
		consumerlname='".$this->consumerlname."',
		consumeremail='".$this->consumeremail."',
		consumerisofficer='".$this->consumerisofficer."',
		consumerisdirector='".$this->consumerisdirector."',
		consumerofficertitle='".$this->consumerofficertitle."',
		consumerotherofficertitle='".$this->consumerotherofficertitle."',
		consumerisshareholder='".$this->consumerisshareholder."',
		consumernoofshares='".$this->consumernoofshares."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumershareright='".$this->consumershareright."',
		consumersharecertno='".$this->consumersharecertno."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumertotalshare='".$this->consumertotalshare."',
		consumeraddress1='".addslashes($this->consumeraddress1)."',
		consumercity='".$this->consumercity."',
		consumerstate_id='".$this->consumerstate_id."',
		consumerzipcode='".$this->consumerzipcode."',
		balance_shares='".$this->balance_shares."',
		share_signee='".$this->consumerisShareSignee."',
		consumer_id='".$this->consumer_id."'";
		mysqli_query($this->dbconnection,$insert);
		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
		//echo $insert;
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumeruser';
		$objUtility->datatableidField ='consumeruser_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Added User Under Consumer';
		$objUtility->dataId=$this->lastInsertedId;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Added Consumer User with Name:['.$this->consumerfname.' '.$this->consumermname.' '.$this->consumerlname.'] Under Consumer File No: ['.$this->consumer_fileno.']';
		$objUtility->logTrack();
		return $this->lastInsertedId;

	}

	function updateConsumerUser()
	{
		$update="update tbl_consumeruser set
		consumerfname='".$this->consumerfname."',
		consumermname='".$this->consumermname."',
		consumerlname='".$this->consumerlname."',
		consumeremail='".$this->consumeremail."',
		consumerisofficer='".$this->consumerisofficer."',
		consumerisdirector='".$this->consumerisdirector."',
		consumerofficertitle='".$this->consumerofficertitle."',
		consumerotherofficertitle='".$this->consumerotherofficertitle."',
		consumerisshareholder='".$this->consumerisshareholder."',
		consumernoofshares='".$this->consumernoofshares."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumershareright='".$this->consumershareright."',
		consumersharecertno='".$this->consumersharecertno."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumertotalshare='".$this->consumertotalshare."',
		consumeraddress1='".addslashes($this->consumeraddress1)."',
		consumercity='".$this->consumercity."',
		consumerstate_id='".$this->consumerstate_id."',
		consumerzipcode='".$this->consumerzipcode."',
		share_signee='".$this->consumerisShareSignee."'
		where consumeruser_id='".$this->consumeruser_id."'";
		mysqli_query($this->dbconnection,$update);

		$select = "SELECT * FROM tbl_sharetransfer_data where from_userid = '".$this->consumeruser_id."' or to_userid = '".$this->consumeruser_id."'";
		$query = mysqli_query($this->dbconnection,$select);

		if ( mysqli_num_rows($query) <= 0)
		{
			$update="update tbl_consumeruser set
			balance_shares='".$this->consumernoofshares."'
			where consumeruser_id='".$this->consumeruser_id."'";
			mysqli_query($this->dbconnection,$update);
		}
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumeruser';
		$objUtility->datatableidField ='consumeruser_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Update Consumer User ';
		$objUtility->dataId=$this->consumeruser_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Update Consumer User with Name:['.$this->consumerfname.' '.$this->consumermname.' '.$this->consumerlname.'] and Consumer User Id: ['.$this->consumeruser_id.']';
		$objUtility->logTrack();
		
	}
	
	function getconsumer_count($pconsumerid,$pUserTyle)
	{
		$sqlQry="select * from tbl_consumeruser where consumer_id='".$pconsumerid."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		return mysqli_num_rows($query);
	}

		

	function checkdupli_fileno()
	{
		$sqlQry="select * from tbl_consumermaster where consumer_fileno='".$this->newconsumer_fileno."' and consumer_fileno !='".$this->consumer_old_fileno."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		return mysqli_num_rows($query);
	}

	

	function generateRandomString($length = 8)
	{
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		$startPos=10;
		$endPos=1;
		for ($i = 0; $i < $length; $i++) 
		{
			
			$randomString .= $characters[rand($startPos, strlen($characters)-$endPos)];
			$lenth= strlen($randomString);
			if($lenth >= '6')
			{
				$startPos=10;
				$endPos=1;
			}
			else
			{
				$startPos=0;
				$endPos=27;
			}
		}
		return $randomString;
	}



	function isFound()
	{
		$select="select consumer_fileno from tbl_consumermaster where consumer_fileno='".$this->consumer_fileno."'";
		$query=mysqli_query($this->dbconnection,$select);
		return mysqli_num_rows($query);
	}
	function checkUserEmail()
	{
		if($this->consumer_id=='')
			$select="select useremail from tbl_user where useremail='".$this->companyemail."'";
		else
			if($this->user_id!='')
				$select="select consumer_id from   tbl_consumermaster where consumer_id!='".$this->consumer_id."' and user_id='".$this->user_id."' and tbl_consumermaster.is_deleted = 0 ";
			else
				$select="select useremail from tbl_user where useremail='".$this->companyemail."'";
		$query=mysqli_query($this->dbconnection,$select);
		return mysqli_num_rows($query);
	}
	function checkUserEmailUpdate()
	{
		$select ="select useremail from tbl_consumermaster where tbl_consumermaster.useremail ='".$this->companyemail."' and  consumer_id!='".$this->consumer_id."' and tbl_consumermaster.is_deleted = 0";
		$query=mysqli_query($this->dbconnection,$select);
		return mysqli_num_rows($query);
	}
	function getconsumer_id($pcode)
	{
		$sel="select consumer_id from tbl_consumermaster where consumer_fileno='".$pcode."'";
		$query=mysqli_query($this->dbconnection,$sel);
		$rows=mysqli_fetch_array($query);
		//print_r($rows);
		return @$rows['consumer_id'];
	}

	function get_incorp_date($pcode)
	{
		$sel="select updatedDate from tbl_consumermaster where consumer_fileno='".$pcode."'";
		$query=mysqli_query($this->dbconnection,$sel);
		$rows=mysqli_fetch_array($query);
		return $rows['updatedDate'];
	}

	
	
	function getuser_id($useEmail)
	{
		$sel	=	"Select user_id from tbl_user where useremail = '".$useEmail."'";
		$query	=	mysqli_query($this->dbconnection,$sel);
		if(mysqli_num_rows($query)>0)
		{
			$rows	=	mysqli_fetch_array($query);
			return $rows['user_id'];
		}
	}
	
	function additional_registeration()
	{
		$sqlQry="UPDATE tbl_consumermaster
				SET registeration_type='".$this->registeration_type."', name='".$this->name."',business_type='".$this->business_type."',state_province='".$this->state_province."' WHERE consumer_fileno='".$this->consumer_fileno."'";
				//echo $sqlQry;
		return mysqli_query($this->dbconnection,$sqlQry);

	}
	
	function editadditional_registeration()
	{
		$sqlQry="UPDATE tbl_consumermaster
				SET consumer_fileno ='".$this->newconsumer_fileno."',  registeration_type='".$this->registeration_type."', name='".$this->name."',business_type='".$this->business_type."',state_province='".$this->state_province."',updatedDate=date('".date('Y-m-d',strtotime($this->updatedDate))."') WHERE consumer_fileno='".$this->consumer_fileno."'";
		//new changed on aug 2022
		$sqlQry="UPDATE tbl_consumermaster
				SET consumer_fileno ='".$this->newconsumer_fileno."', updatedDate=date('".date('Y-m-d',strtotime($this->updatedDate))."'), signatureMode  ='".$this->signatureMode ."' WHERE consumer_fileno='".$this->consumer_fileno."'";
				//echo $sqlQry;
		return mysqli_query($this->dbconnection,$sqlQry);

	}
	//Thanx mail
	function sendConsumerEmail($pCompanyemail)
	{
		$name1="MYCOTOGO - Administrator";
		//$row=$this->getCompanyDetails();
		$to1 = $pCompanyemail;
		$email1=SYSTEM_GENRATED_MAIL;
		$e_subject1 = 'MYCOTOGO - Payment Pending';
		$e_content1 ='
		<table>
		<tr>
		<td colspan="2"><b>Payment Pending</b></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2">Your payment is pending.  Confirmation of your payment will be sent upon completion of the transaction.</td>
		</tr>
		<tr>
		<td><br/><b>MYCOTOGO</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
		</table>';

		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	}
	
	//consumer payment pending email 
	function sendConsumerEmailPending($pCompanyemail)
	{
		$name1	=	$_SESSION['sessfirstname'].' '.$_SESSION['sesslastname'];
		$to1 = $pCompanyemail;
		$email1=$_SESSION['sessuseremail'];
		$e_subject1 = 'Payment Pending ';
		$e_content1 ='
		<table>
		<tr>
		<td colspan="2"><b></b></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2">Your payment is pending.  Confirmation of your payment will be sent <br /><br> 

		upon completion of the transaction.
		</td>
		</tr>
		<tr>
		<tr>
		<td><br/><b>MYCOTOGO</b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
		</table>';
		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	}
	//consumer payment success email 
	function sendConsumerEmailSuccess($pCompanyemail)
	{
		$name1=$_SESSION['sessfirstname'].' '.$_SESSION['sesslastname'];
		$to1 = $pCompanyemail;
		$email1=$_SESSION['sessuseremail'];
		$e_subject1 = 'Payment complete';
		$e_content1 ='
		<table>
			<tr>
			</tr>
			<tr>
				<td colspan="2"><b>Payment complete</b></td>
			</tr>
			<tr>
				<td colspan="2">Thank you for your payment.  Your new company is being created and we</br>
				will keep you updated on your file status during the next 24 - 48 hours!   Feel free to direct any </br>questions to us at:  myco@mycotogo.com</td>
			</tr>
			<tr>
				<td><br/><b>MYCOTOGO</b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>';
		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	}
	
	//consumer incorporate email
	function sendConsumerIncorporatedEmail($pCompanyemail)
	{
		$name1=$_SESSION['sessfirstname'].' '.$_SESSION['sesslastname'];
		$to1 = $pCompanyemail;
		$email1=$_SESSION['sessuseremail'];
		$e_subject1 = 'Almost ready..';
		$e_content1 ='
		<table>
			<tr>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">Your company is now registered and your new digital record book is being created.  You will be receiving an email within the next hour containing your username and <br />
				password to access your account!  </td>
			</tr>
			<tr>
				<td><br/><b>MYCOTOGO</b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>';
		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	}
	//consumer complete email
	function sendConsumerCompletedEmail($pCompanyemail)
	{
		$objUser=new User();
		$row=$objUser->getUserDetails($this->lastUserInserted_id);
		$useremail=$row->useremail;
		$password=$row->password;
		$name1=$_SESSION['sessfirstname'].' '.$_SESSION['sesslastname'];
		$to1 = $useremail;
		$email1=$_SESSION['sessuseremail'];
		$e_subject1 = 'Congratulations! Your new company is ready!';
		$e_content1 ='
		<table>
			<tr>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">Your new company is registered and all of your corporate registry documents are filed in your digital record book.  </br> Within the next few minutes you will receive your account activation email.
				</td>
			</tr>
			<tr>
				<td>Next Steps:</td>
			</tr>
			<tr>
				<td>1. We recommend taking the short video tour: <a href="'.URL.'index.php?task=video"> http://mycotogo.com/login/videotour</a>  which introduces you to the features included with your digital record book.</td>
			</tr>
			<tr>
				<td>2.  Review your "next steps" checklist here: <a href="'.URL.'index.php?task=nextstep"> http://mycotogo.com/login/nextsteps.<a>  We have taken care of most of your year one paperwork but the checklist will detail some important next steps including:</td>
			</tr>
			<tr>
				<td>Registering your business with CRA (Canada)  or IRS (USA)<br/><br>
					Municipal Licensing<br/><br>
					WCB<br/><br>
					And so much more...
				</td>
			</tr>
			<tr>
				<td>3. Manage your Profile by clicking open the "profile" link.  This area contains your billing account information, username and password, contact email and allows you to grant access to additional account users.  </td>
			</tr>
			<tr>
				<td>4. Stay connected!  We welcome your feedback and are always happy to answer your questions.  Periodically you will receive emails from us asking you to login and update important information regarding your company.  We will also be notifying you when we conduct system upgrades and add more resources to the "help" section of your account.  </td>
			</tr>
			<tr>
				<td><br/>You can contact us anytime as follows:</td>
			</tr>
			<tr>
				<td>Billing inquiries:  billing@mycotogo.com</br><br>
					General inquiries: myco@mycotogo.com</br><br>
					Toll Free:  1-888-362-5025  ext 1 (Canada)  ext 2 (USA)
				</td>
			</tr>
			<tr>
				<td><br/><b>Thank you and best wishes as you pursue your new venture!</b></td>
			</tr>
			<tr>
				<td><br/><b>Team Mycotogo</b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>';
		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	}
	
	function sendConsumerDeclinedEmail($pCompanyemail)
	
	{
		$name1=$_SESSION['sessfirstname'].' '.$_SESSION['sesslastname'];
		//$row=$this->getCompanyDetails();
		$to1 = $pCompanyemail;
		$email1=$_SESSION['sessuseremail'];
		$e_subject1 = 'MYCOTOGO - Payment Declined';
		$e_content1 ='
		<table>
		<tr>
		<td colspan="2"><b>Payment Declined</b></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td colspan="2">Your payment has been declined.  This can occur for several reasons.  Our office will be contacting you within the next 24 hours to review your payment details.  Should you have any questions, please direct your inquiry to:</td>
		</tr>
		<tr>
		<td>billing@mycotogo.com</td>
		</tr>
		<tr>
		<td>or via telephone 1-888-362-5025</td>
		</tr>
		</table>';

		SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
	
	}
	function getCompanyDetails()
	{
		if($this->consumer_fileno!='')
		{
			//$sqlQry="select * ,tbl_user.user_id as uid from tbl_consumermaster, tbl_user WHERE tbl_user.user_id = tbl_consumermaster.user_id AND consumer_fileno='".$this->consumer_fileno."'";
			$sqlQry="select *  from tbl_consumermaster WHERE  consumer_fileno='".$this->consumer_fileno."' and tbl_consumermaster.is_deleted = 0";
		}
		elseif($this->companyemail!='')
		{
			$sqlQry	=	"Select * ,tbl_user.user_id as uid ,tbl_consumermaster.companyname companydetail from tbl_consumermaster,tbl_user where tbl_user.user_id = tbl_consumermaster.user_id and tbl_user.useremail='".$this->companyemail."' and tbl_consumermaster.is_deleted = 0 order by updatedDate desc";
		}
		else
		{
			$sqlQry="select *, tbl_consumermaster.companyname as compname ,tbl_user.user_id as uid from tbl_consumermaster, tbl_user WHERE tbl_user.user_id = tbl_consumermaster.user_id AND consumer_id='".$this->consumer_id."' and tbl_consumermaster.is_deleted = 0";
		}
		//echo $sqlQry;
		$res=mysqli_query($this->dbconnection,$sqlQry);
		return $res;
		
	}
	
	function getConsumerStatus($consumerfilestatus_id)
	{
	  $sqlQry="select consumerstatus from enum_consumerfilestatus where consumerfilestatus_id='".$consumerfilestatus_id."'";
	  $res=mysqli_query($this->dbconnection,$sqlQry);
	  $row=mysqli_fetch_object($res);
	  return $row->consumerstatus;
	  
	}
	function getUserDetails($user_id)
	{
		if($user_id!='')
		{
			$select = "Select * from tbl_user where user_id= '".$user_id."'";
		}
		else
		{
			$select = "Select * from tbl_user where user_id= '".$this->user_id."'";
		}
		$query = mysqli_query($this->dbconnection,$select);
		$row = mysqli_fetch_object($query);
		return $row->useremail;
	}

	function sendMailAsPerStatus($status)
	{
		$row=$this->getCompanyDetails();
		$row	= mysqli_fetch_object($row);
		$consumerfilestatus_id=$row->consumerfilestatus_id;
		$companyemail=$row->user_id;
		$companyemail=$this->getUserDetails($row->user_id);
		if($consumerfilestatus_id!=$status)
		{
			if($status==2)
			{
				$this->sendConsumerEmail($companyemail);
			}
			if($status==3)
			{
				
				if($this->statusfor=='Declined')
				{
					
					$this->sendConsumerDeclinedEmail($companyemail);
				}
				else
				{
					
					$this->sendConsumerEmailPending($companyemail);
				}
				
			}
			if($status==4)
			{
				$this->sendConsumerEmailSuccess($companyemail);
			}
			if($status==5)
			{
				$this->sendConsumerIncorporatedEmail($companyemail);
			}
			if($status==6)
			{
				$this->sendConsumerCompletedEmail($companyemail);
			}
			
		}
	}
	function activateConsumerMember()
	{
		$sqlQry	=	"update tbl_consumeruser set active_status = 1 where consumeruser_id='".$this->consumeruser_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumeruser';
		$objUtility->datatableidField ='consumeruser_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Activate Consumer member ';
		$objUtility->dataId=$this->consumeruser_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Activate consumer member with id:['.$this->consumeruser_id.']';
		$objUtility->logTrack();

	}
	function archiveConsumerMember()
	{
		$sqlQry	=	"update tbl_consumeruser set active_status = 0 where consumeruser_id='".$this->consumeruser_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumeruser';
		$objUtility->datatableidField ='consumeruser_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Archive Consumer member ';
		$objUtility->dataId=$this->consumeruser_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='Archive consumer member with id:['.$this->consumeruser_id.']';
		$objUtility->logTrack();
		//die;
	}
	function updateConsumerFolder()
	{
		$sqlQry="Update tbl_consumermaster set consumerfolder_id='".$this->rendomnumber."' where consumer_id='".$this->consumer_id."'";
		mysqli_query($this->dbconnection,$sqlQry);
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_consumeruser';
		$objUtility->datatableidField ='consumer_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Update Consumer Master';
		$objUtility->dataId=$this->consumer_id;
		$objUtility->user_id=$this->user_id;
		$objUtility->description='update Consumer User with id:['.$this->consumer_id.']';
	}
}
?>