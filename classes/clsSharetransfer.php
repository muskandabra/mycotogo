<?php include_once(PATH."classes/Utility.php");
include_once(PATH."classes/User.php");?>
<?php 
Class ShareTransfer
{
	var $consumer_fileno;
	var $lastUserInserted_id='';
	var $date='';
	var	$cert_no_issued_from='';
	var	$cert_no_cancelled='';
	var	$transfer_no='';
	var	$from_userid='';
	var	$to_userid='';
	var	$cert_no_issued_to='';
	var	$folio='';
	var	$no_of_shares='';
	var $no_of_shares_from = '';
	var	$from_balance ='';
	var	$to_balance='';
	var $usertype='';
	var $user_id='';
	var $consumeruser_id = '';
	var $consumershareclass='';
	var $consumersharecolor='';
	var $consumersharetype= '';
	var $consumerpricepershare = '';
	var $consumershareright=''; 
	var $dir_doj = '';
	var $dir_dol ='';
	var $officer_doj='';
	var $officer_dol='';
	var $member_doj='';
	var $member_dol='';
	var $lastInsertedId = '';
	var $consumerofficertitle='';
	var $consumerotherofficertitle='';
	var $transfer_id='';
	var $servicerec_id='';
	VAR $oldcertificate_no = '';
	
	
	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
		$this->updatedDate = date('Y-m-d H:i:s');
	}

	function MemberServiceRecord()
	{
		$res = '';
		if($this->consumeruser_id!='')
		{
			$sqlQry="select tbl_members_servicerec.*, a.consumerfname   as fromname  from tbl_members_servicerec LEFT JOIN tbl_consumeruser a on a.consumeruser_id = tbl_members_servicerec.consumeruser_id where tbl_members_servicerec.consumeruser_id = '".$this->consumeruser_id."' order by member_designation, member_doj";
			$res = mysqli_query($this->dbconnection,$sqlQry);

		}
		if($this->servicerec_id!='')
		{
			$sqlQry="select tbl_members_servicerec.*, a.consumerfname   as fromname  from tbl_members_servicerec LEFT JOIN tbl_consumeruser a on a.consumeruser_id = tbl_members_servicerec.consumeruser_id where tbl_members_servicerec.servicerec_id = '".$this->servicerec_id."' ";
			$res = mysqli_query($this->dbconnection,$sqlQry);
		}

		return $res;

	}

	function ShareTrnaferRecord()
	{
		$res = '';
		if($this->consumeruser_id!='')
		{
			$sqlQry="select tbl_sharetransfer_data.*, IF(a.consumerfname ='' or a.consumerfname  IS NULL , 'Treasury', a.consumerfname)  as fromname,  IF(b.consumerfname ='' or b.consumerfname  IS NULL , 'Treasury', b.consumerfname) as toname from tbl_sharetransfer_data LEFT JOIN tbl_consumeruser a on a.consumeruser_id = tbl_sharetransfer_data.from_userid LEFT JOIN tbl_consumeruser b on b.consumeruser_id = tbl_sharetransfer_data.to_userid where (tbl_sharetransfer_data.from_userid = '".$this->consumeruser_id."' or  tbl_sharetransfer_data.to_userid = '".$this->consumeruser_id."') order by date DESC";
			$res = mysqli_query($this->dbconnection,$sqlQry);

		}
		if($this->transfer_id!='')
		{
			$sqlQry="select tbl_sharetransfer_data.*, a.consumerfname as fromname, b.consumerfname as toname from tbl_sharetransfer_data LEFT JOIN tbl_consumeruser a on a.consumeruser_id = tbl_sharetransfer_data.from_userid LEFT JOIN tbl_consumeruser b on b.consumeruser_id = tbl_sharetransfer_data.to_userid where (tbl_sharetransfer_data.transfer_id = '".$this->transfer_id."' ) order by date DESC";
			$res = mysqli_query($this->dbconnection,$sqlQry);

		}

		return $res;
	}

	function showlastdetails()
	{
		$retu_array = array();
		if($this->consumeruser_id!='')
		{
			$sqlQry="select member_dol from tbl_members_servicerec where consumeruser_id='".$this->consumeruser_id."' and member_designation = 'director' order by member_dol desc ";

			$res = mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array = array('director_dollast'=>$records['member_dol']);
					break;
				}
			}
			else
			{
				$retu_array = array('director_dollast'=>'');

			}

			$sqlQry="select member_doj from tbl_members_servicerec where consumeruser_id='".$this->consumeruser_id."' and member_designation = 'director' order by member_doj desc ";

			$res = mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array = array_merge($retu_array,array('director_dojlast'=>$records['member_doj']));
					break;
				}
			}
			else
			{
				$retu_array = array_merge($retu_array,array('director_dojlast'=>''));
			}

			$sqlQry="select member_dol from tbl_members_servicerec where consumeruser_id='".$this->consumeruser_id."' and member_designation = 'officer' order by member_dol desc ";

			$res = mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array = array_merge($retu_array,array('officer_dollast'=>$records['member_dol']));
					break;
				}
			}
			else
			{
				$retu_array = array_merge($retu_array,array('officer_dollast'=>''));
			}

			$sqlQry="select member_doj from tbl_members_servicerec where consumeruser_id='".$this->consumeruser_id."' and member_designation = 'officer' order by member_doj desc ";

			$res = mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array = array_merge($retu_array,array('officer_dojlast'=>$records['member_doj']));
					break;
				}
			}
			else
			{
				$retu_array = array_merge($retu_array,array('officer_dojlast'=>''));
			}
		}
		//print_r($retu_array);
			return $retu_array;

	}

	// $officer_dojlast = $info['officer_dojlast'];
	// $officer_dollast = $info['officer_dollast'];
	// $director_dojlast = $info['director_dojlast'];
	// $director_dollast = $info['director_dollast'];

	function certificate_details()
	{
		$retu_array = array();
		if($this->consumeruser_id!='')
		{
			$sqlQry="select  consumersharecertno ,consumernoofshares from tbl_consumeruser where tbl_consumeruser.consumeruser_id='".$this->consumeruser_id."' and consumersharecertno not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$this->consumeruser_id."') and consumersharecertno not in (select cert_no_issued_to from tbl_sharetransfer_data where tbl_sharetransfer_data.to_userid='".$this->consumeruser_id."')";

			$res = mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array[] = array('cert_no_issued_to'=>$records['consumersharecertno'],'no_of_shares'=>$records['consumernoofshares']);

				}
			}
			// echo '<pre>';
			// 	print_r($retu_array);
			// 	echo '</pre>';
			$sqlQry="select cert_no_issued_from,no_of_shares,no_of_shares_from from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$this->consumeruser_id."' and no_of_shares_from > 0 and cert_no_issued_from not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$this->consumeruser_id."') ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array[] = array('cert_no_issued_to'=>$records['cert_no_issued_from'],'no_of_shares'=>$records['no_of_shares_from']);
					//$retu_array[] = $records;
				}	
				// echo '<pre>';
				// print_r($retu_array);
				// echo '</pre>';
				//print_r($records);
				//print_r($records[1]);
				//die;
				
			}
			// echo '<pre>';
			// 	print_r($retu_array);
			// 	echo '</pre>';
			$sqlQry="select cert_no_issued_to,no_of_shares from tbl_sharetransfer_data where tbl_sharetransfer_data.to_userid='".$this->consumeruser_id."'  and no_of_shares > 0 and cert_no_issued_to not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$this->consumeruser_id."') ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$records = array();
				while ($records=mysqli_fetch_array($res))
				{
					//print_r($records);
					$retu_array[] = $records;
				}	
				// echo '<pre>';
				// print_r($retu_array);
				// echo '</pre>';
				//print_r($records);
				//print_r($records[1]);
				//die;
				
			}
			// echo '<pre>';
			// 	print_r($retu_array);
			// 	echo '</pre>';
			return $retu_array;
		}
		else
		{
			return $retu_array;
		}
	}
	function checkdupliCert()
	{
		$retu = '';

		if (!empty($this->cert_no_issued_from))
		{

		$sqlQry="select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."' and (consumersharecertno = '".$this->cert_no_issued_from."' )";
			
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$retu = $this->cert_no_issued_from;

			}
		}

		if (!empty($this->cert_no_issued_to))
		{
		$sqlQry="select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."' and (consumersharecertno = '".$this->cert_no_issued_to."' )";
			
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$retu = $this->cert_no_issued_to;

			}
		}
		

		if (!empty($this->cert_no_issued_from))
		{
			$sqlQry="select * from tbl_sharetransfer_data where (tbl_sharetransfer_data.cert_no_cancelled='".$this->cert_no_issued_from."' or tbl_sharetransfer_data.cert_no_issued_from='".$this->cert_no_issued_from."' or tbl_sharetransfer_data.cert_no_issued_to='".$this->cert_no_issued_from."')  and (from_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."') or  to_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."')) ";
			
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$retu = $this->cert_no_issued_from;

			}
		}
		if (!empty($this->cert_no_issued_to))
		{

			$sqlQry="select * from tbl_sharetransfer_data where (tbl_sharetransfer_data.cert_no_cancelled='".$this->cert_no_issued_to."' or tbl_sharetransfer_data.cert_no_issued_from='".$this->cert_no_issued_to."' or tbl_sharetransfer_data.cert_no_issued_to='".$this->cert_no_issued_to."')  and (from_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."') or  to_userid in (select tbl_consumeruser.consumeruser_id from tbl_consumeruser where consumer_id = '".$this->consumer_id."')) ";
			
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				if (empty($retu))
				{
					$retu = $this->cert_no_issued_to;
				}
				else
				{
					$retu = $this->cert_no_issued_to;
				}				
			}
		}
		//echo $retu;
		//die;
		return $retu;
			//die;

	}


	function updateTransrec()
	{
		$update="update  tbl_sharetransfer_data set
		date='".$this->date."',
		cert_no_issued_from='".$this->cert_no_issued_from."',
		transfer_no='".$this->transfer_no."',
		cert_no_issued_to='".$this->cert_no_issued_to."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		no_of_shares='".$this->no_of_shares."',
		updated_on='".date('Y-m-d')."' where transfer_id = '".$this->transfer_id."'";
		
		mysqli_query($this->dbconnection,$update);
		//echo $insert;


	
		$update="update tbl_sharecertificates set
		date='".$this->date."',
		cert_no='".$this->cert_no_issued_from."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		status='ACTIVE' where consumeruser_id='".$this->from_userid."' and cert_no = '".$this->cert_no_issued_from."'";
		
		mysqli_query($this->dbconnection,$update);


		$update="update tbl_sharecertificates set
		date='".$this->date."',
		cert_no='".$this->cert_no_issued_to."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		no_of_shares='".$this->no_of_shares."',
		status='ACTIVE' where consumeruser_id='".$this->to_userid."' and cert_no = '".$this->cert_no_issued_to."'";
		mysqli_query($this->dbconnection,$update);

		$sqlQry= "SELECT * FROM `tbl_consumeruser` WHERE  tbl_consumeruser.consumeruser_id = '".$this->to_userid."' and tbl_consumeruser.consumersharecertno = '".$this->oldcertificate_no."'";

		$res = mysqli_query($this->dbconnection,$sqlQry);
		if(mysqli_num_rows($res)>0)
		{
			$update="update tbl_consumeruser set
			consumersharecertno='".$this->cert_no_issued_to."',
			consumershareclass='".$this->consumershareclass."',
			consumersharecolor='".$this->consumersharecolor."',
			consumersharetype='".$this->consumersharetype."',
			consumerpricepershare='".$this->consumerpricepershare."',
			consumershareright='".$this->consumershareright."',
			consumernoofshares='".$this->no_of_shares."'
			where consumeruser_id='".$this->to_userid."' and tbl_consumeruser.consumersharecertno = '".$this->oldcertificate_no."'";
			mysqli_query($this->dbconnection,$update);
		}
		if ($this->from_userid == 0 && $this->no_of_shares != $this->oldno_of_shares)
		{
			$number = -$this->oldno_of_shares+$this->no_of_shares;

			$sqlQry	=	"update tbl_consumeruser set balance_shares = balance_shares+".$number." where consumeruser_id='".$this->to_userid."'";
			mysqli_query($this->dbconnection,$sqlQry);

		}

		



		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_sharetransfer_data';
		$objUtility->datatableidField ='transfer_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='updated Share transfer detail';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=$this->transfer_id;
		$objUtility->description='Updated Transfer with  id: ['.$this->transfer_id.']';
		$objUtility->logTrack();
		return $this->transfer_id;

	}

	function deleteMemberrec()
	{
		$delete="delete from tbl_members_servicerec where servicerec_id ='".$this->servicerec_id."'" ;

		mysqli_query($this->dbconnection,$delete);



		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_members_servicerec';
		$objUtility->datatableidField ='servicerec_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='delete Member record';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=$this->servicerec_id;
		$objUtility->description='Delete member record  with servicerec_id : ['.$this->servicerec_id.'] ';
		$objUtility->logTrack();


		return $this->servicerec_id;


	}

	function deleteTransferrec()
	{
		$fromuserid = '';
		$fromcertno = '';
		$touserid = '';
		$tocertno = '';

		$delete="select *  from tbl_sharetransfer_data where transfer_id ='".$this->transfer_id."'";

		$res = mysqli_query($this->dbconnection,$delete);

		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);
			{
				$fromuserid = $records->from_userid;
				$fromcertno = $records->cert_no_issued_from;
				$touserid = $records->to_userid;
				$tocertno = $records->cert_no_issued_to;
				$noofshares = $records->no_of_shares; 
			}
		}

		$Sqlqry="select *  from tbl_sharetransfer_data where ( from_userid  = '".$fromuserid."' or from_userid  = '".$touserid."') and  (cert_no_cancelled ='".$fromcertno."' or cert_no_cancelled ='".$tocertno."') and from_userid > 0 and cert_no_cancelled != ''";

		//die;

		$res = mysqli_query($this->dbconnection,$Sqlqry);

		if(mysqli_num_rows($res)>0)
		{
			return '';
		}




		$delete="delete from tbl_sharecertificates where consumeruser_id ='".$fromuserid."' and cert_no='".$fromcertno."'" ;

		mysqli_query($this->dbconnection,$delete);


		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_sharecertificates';
		$objUtility->datatableidField ='consumeruser_id,cert_no';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='delete Certificate';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=0;
		$objUtility->description='Delete Certificates with consumeruser_id : ['.$fromuserid.'] and cert no = : ['.$fromcertno.'] ';
		$objUtility->logTrack();


		$delete="delete from tbl_sharecertificates where consumeruser_id ='".$touserid."' and cert_no='".$tocertno."'" ;

		mysqli_query($this->dbconnection,$delete);


		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_sharecertificates';
		$objUtility->datatableidField ='consumeruser_id,cert_no';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='delete Certificate';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=0;
		$objUtility->description='Delete Certificates with consumeruser_id : ['.$touserid.'] and cert no = : ['.$tocertno.'] ';
		$objUtility->logTrack();

		$delete="delete from tbl_sharetransfer_data where transfer_id ='".$this->transfer_id."'" ;

		mysqli_query($this->dbconnection,$delete);

			$sqlQry	=	"update tbl_consumeruser set balance_shares = balance_shares+".$noofshares." where consumeruser_id='".$fromuserid."'";
			mysqli_query($this->dbconnection,$sqlQry);

			$sqlQry	=	"update tbl_consumeruser set balance_shares = balance_shares-".$noofshares." where consumeruser_id='".$touserid."'";
			mysqli_query($this->dbconnection,$sqlQry);


		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_sharetransfer_data';
		$objUtility->datatableidField ='transfer_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='delete Transfer record';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=$this->transfer_id;
		$objUtility->description='Delete transfer record  with transfer_id : ['.$this->transfer_id.'] ';
		$objUtility->logTrack();

		return $this->transfer_id;

	}


	function addtransfer()
	{
		$insert="insert into tbl_sharetransfer_data set
		date='".$this->date."',
		cert_no_issued_from='".$this->cert_no_issued_from."',
		cert_no_cancelled='".$this->cert_no_cancelled."',
		transfer_no='".$this->transfer_no."',
		from_userid='".$this->from_userid."',
		to_userid='".$this->to_userid."',
		cert_no_issued_to='".$this->cert_no_issued_to."',
		folio='".$this->folio."',
		no_of_shares='".$this->no_of_shares."',
		no_of_shares_from='".$this->no_of_shares_from."',
		from_balance ='".$this->from_balance ."',
		to_balance='".$this->to_balance."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		balance_shares='".$this->balance_shares."',
		created_on='".date('Y-m-d')."'";
		
		mysqli_query($this->dbconnection,$insert);
		//echo $insert;

		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		$insert="insert into tbl_sharecertificates set
		date='".$this->date."',
		cert_no='".$this->cert_no_issued_from."',
		consumeruser_id='".$this->from_userid."',
		no_of_shares='".$this->no_of_shares_from."',
		transfer_id='".$this->lastInsertedId."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		status='ACTIVE',
		created_on='".date('Y-m-d')."'";
		
		mysqli_query($this->dbconnection,$insert);

		$insert="insert into tbl_sharecertificates set
		date='".$this->date."',
		consumeruser_id='".$this->to_userid."',
		cert_no='".$this->cert_no_issued_to."',
		no_of_shares='".$this->no_of_shares."',
		transfer_id='".$this->lastInsertedId."',
		consumershareclass='".$this->consumershareclass."',
		consumersharecolor='".$this->consumersharecolor."',
		consumersharetype='".$this->consumersharetype."',
		consumerpricepershare='".$this->consumerpricepershare."',
		consumershareright='".$this->consumershareright."',
		status='ACTIVE',
		created_on='".date('Y-m-d')."'";
		mysqli_query($this->dbconnection,$insert);

		$sql="update tbl_sharecertificates set
		status='CANCELLED' where consumeruser_id='".$this->from_userid."' and 
		cert_no='".$this->cert_no_cancelled."'";

		mysqli_query($this->dbconnection,$sql);


		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_sharetransfer_data';
		$objUtility->datatableidField ='transfer_id';
		$objUtility->usertype=$this->usertype;
		$objUtility->action='Added Share transfer detail';
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->dataId=$this->lastInsertedId;
		$objUtility->description='Added Transfer with  id: ['.$this->lastInsertedId.']';
		$objUtility->logTrack();
		return $this->lastInsertedId;
	}	

	function addmember_servicerec()
	{ 
		if ($this->dir_doj != '' && $this->dir_dol == '')
		{
			$insert="insert into tbl_members_servicerec set
			consumeruser_id='".$this->consumeruser_id."',
			member_designation='director',
			member_doj='".$this->dir_doj."',
			created_on='".date('Y-m-d')."'";
			
			mysqli_query($this->dbconnection,$insert);
			//echo $insert;

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			//die;
			
			//return $this->lastInsertedId;

		}
		// if ($this->dir_dol != '')
		// {
		// 	$retu_id = '';
		// 	echo $sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' and member_doj != '' and  isnull(member_dol) ";
		// 	$res = mysqli_query($this->dbconnection,$sqlQry);

		// 	if(mysqli_num_rows($res)>0)
		// 	{
		// 		while ($records=mysqli_fetch_object($res))
		// 		{
		// 			//print_r($records);
		// 			$retu_id = $records->servicerec_id;
		// 		}	
		// 	}
		// 	if ($retu_id == '')
		// 	{
		// 		$insert="insert into tbl_members_servicerec set
		// 		consumeruser_id='".$this->consumeruser_id."',
		// 		member_designation='director',
		// 		member_dol='".$this->dir_dol."',
		// 		created_on='".date('Y-m-d')."'";
		// 	}
		// 	else
		// 	{
		// 		$insert="update  tbl_members_servicerec set
		// 		consumeruser_id='".$this->consumeruser_id."',
		// 		member_designation='director',
		// 		member_dol='".$this->dir_dol."',
		// 		created_on='".date('Y-m-d')."' where servicerec_id = '".$retu_id."'";
		// 	}

		// 	echo $insert;
			
			
		// 	mysqli_query($this->dbconnection,$insert);
		// 	$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
		// 	//return $this->lastInsertedId;

		// }


		if ($this->officer_doj != '' && $this->officer_dol == '')
		{
			$insert="insert into tbl_members_servicerec set
			consumeruser_id='".$this->consumeruser_id."',
			consumerofficertitle='".$this->consumerofficertitle."',
			consumerotherofficertitle='".$this->consumerotherofficertitle."',
			member_designation='officer',
			member_doj='".$this->officer_doj."',
			created_on='".date('Y-m-d')."'";
			
			mysqli_query($this->dbconnection,$insert);
			//echo $insert;

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			//die;
			//return $this->lastInsertedId;

		}
		// if ($this->officer_dol != '')
		// {
		// 	$retu_id = '';
		// 	echo $sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='officer' and member_doj != '' and  isnull(member_dol) ";
		// 	$res = mysqli_query($this->dbconnection,$sqlQry);

		// 	if(mysqli_num_rows($res)>0)
		// 	{
		// 		while ($records=mysqli_fetch_object($res))
		// 		{
		// 			//print_r($records);
		// 			$retu_id = $records->servicerec_id;
		// 		}	
		// 	}
		// 	if ($retu_id == '')
		// 	{
		// 		$insert="insert into tbl_members_servicerec set
		// 		consumeruser_id='".$this->consumeruser_id."',
		// 		member_designation='officer',
		// 		member_dol='".$this->officer_dol."',
		// 		created_on='".date('Y-m-d')."'";
		// 	}
		// 	else
		// 	{
		// 		$insert="update  tbl_members_servicerec set
		// 		consumeruser_id='".$this->consumeruser_id."',
		// 		member_designation='officer',
		// 		member_dol='".$this->officer_dol."',
		// 		created_on='".date('Y-m-d')."' where servicerec_id = '".$retu_id."'";
		// 	}

		// 		echo $insert;
			
			
		// 	mysqli_query($this->dbconnection,$insert);
		// 	//echo $insert;

		// 	$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			

		// }
		if ($this->lastInsertedId != '')
		{
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_members_servicerec';
			$objUtility->datatableidField ='servicerec_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Added Member service detail';
			$objUtility->user_id=$_SESSION['sessuserid'];
			$objUtility->dataId=$this->lastInsertedId;
			$objUtility->description='Added member service with  id: ['.$this->lastInsertedId.']';
			$objUtility->logTrack();
			return $this->lastInsertedId;
		}
				
		
	}

	function updateServiceRec()
	{
		if ($this->member_doj != '' && $this->member_dol != '' && $this->member_doj> $this->member_dol)
		{
			return '';
		}
		if ($this->servicerec_id != '' )
		{
			if ($this->member_doj != '' && $this->member_dol != '' )
			{
				$update="update tbl_members_servicerec set
				consumerofficertitle='".$this->consumerofficertitle."',
				consumerotherofficertitle='".$this->consumerotherofficertitle."',
				member_doj='".$this->member_doj."',
				member_dol='".$this->member_dol."',
				updated_on='".date('Y-m-d')."' where servicerec_id= '".$this->servicerec_id."'";
			}
			if ($this->member_doj != '' && $this->member_dol == '' )
			{
				$update="update tbl_members_servicerec set
				consumerofficertitle='".$this->consumerofficertitle."',
				consumerotherofficertitle='".$this->consumerotherofficertitle."',
				member_doj='".$this->member_doj."',
				member_dol=NULL,
				updated_on='".date('Y-m-d')."' where servicerec_id= '".$this->servicerec_id."'";
			}			
			//die;						
			mysqli_query($this->dbconnection,$update);

			$sqlQry = "select * from tbl_members_servicerec where member_designation = 'officer' and   	consumeruser_id  = '".$this->consumeruser_id."' order by member_doj LIMIT 1";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$records=mysqli_fetch_object($res);

				$retu_id = $records->servicerec_id;


				if ($retu_id == $this->servicerec_id )
				{
					$update="update tbl_consumeruser set
					consumerofficertitle='".$this->consumerofficertitle."',
					consumerotherofficertitle='".$this->consumerotherofficertitle."'
					where consumeruser_id= '".$this->consumeruser_id."'";

					mysqli_query($this->dbconnection,$update);
				}
			}
		}			

		if ($this->servicerec_id != '')
		{
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_members_servicerec';
			$objUtility->datatableidField ='servicerec_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Updated Member service detail';
			$objUtility->user_id=$_SESSION['sessuserid'];
			$objUtility->dataId=$this->servicerec_id;
			$objUtility->description='Updated member service with  id: ['.$this->servicerec_id.']';
			$objUtility->logTrack();
			return $this->servicerec_id;
		}

	}
	function editmember_servicerec()
	{ 
		$error = 0;
		$msg = '';

		if ($this->dir_doj != '' && $this->dir_dol == '')
		{
			$retu_id = '';
			$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' and member_doj != '' and  isnull(member_dol) ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				while ($records=mysqli_fetch_object($res))
				{
					//print_r($records);
					$retu_id = $records->servicerec_id;
				}	
			}
			if ($retu_id == '')
			{
				$insert="insert into tbl_members_servicerec set
				consumeruser_id='".$this->consumeruser_id."',
				member_designation='director',
				member_doj='".$this->dir_doj."',
				created_on='".date('Y-m-d')."'";
				
				mysqli_query($this->dbconnection,$insert);
				//echo $insert;

				$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			}
			else
			{
				$error = 1;
				$msg = 'Date of Joining Already exist for Director';
			}
			//die;
			
			//return $this->lastInsertedId;

		}
		if ($this->dir_dol != '')
		{
			// $retu_id = '';
			// $sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' and member_doj != '' and  isnull(member_dol) ";
			// $res = mysqli_query($this->dbconnection,$sqlQry);

			// if(mysqli_num_rows($res)>0)
			// {
			// 	while ($records=mysqli_fetch_object($res))
			// 	{
			// 		//print_r($records);
			// 		$retu_id = $records->servicerec_id;
			// 	}	
			// }
			// if ($retu_id == '')
			// {
			// 	$insert="insert into tbl_members_servicerec set
			// 	consumeruser_id='".$this->consumeruser_id."',
			// 	member_designation='director',
			// 	member_dol='".$this->dir_dol."',
			// 	created_on='".date('Y-m-d')."'";
			// }
			// else
			// {
			// 	$insert="update  tbl_members_servicerec set
			// 	consumeruser_id='".$this->consumeruser_id."',
			// 	member_designation='director',
			// 	member_dol='".$this->dir_dol."',
			// 	created_on='".date('Y-m-d')."' where servicerec_id = '".$retu_id."'";
			// }

			// //echo $insert;
			
			
			// mysqli_query($this->dbconnection,$insert);
			// $this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			// //return $this->lastInsertedId;

			$retu_id = '';
			$retu_id1 = '';
			$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' and isnull(member_dol)";
				$res = mysqli_query($this->dbconnection,$sqlQry);
				if(mysqli_num_rows($res)>0)
				{
					$retu_id1 = '1';

				}
				else
				{
					$retu_id1 = '0';
				}
			}
			if ($retu_id == '' && $retu_id1 == '1')
			{
				$retu_id = '';
				$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='director' and member_doj != '' and  isnull(member_dol) ";
				$res = mysqli_query($this->dbconnection,$sqlQry);

				if(mysqli_num_rows($res)>0)
				{
					while ($records=mysqli_fetch_object($res))
					{
						//print_r($records);
						$retu_id = $records->servicerec_id;
					}	
				}
				if ($retu_id == '')
				{
					$insert="insert into tbl_members_servicerec set
					consumeruser_id='".$this->consumeruser_id."',
					member_designation='director',
					member_dol='".$this->dir_dol."',
					created_on='".date('Y-m-d')."'";
				}
				else
				{
					$insert="update  tbl_members_servicerec set
					consumeruser_id='".$this->consumeruser_id."',
					member_designation='director',
					member_dol='".$this->dir_dol."',
					created_on='".date('Y-m-d')."' where servicerec_id = '".$retu_id."'";
				}

					//echo $insert;
				
				
				mysqli_query($this->dbconnection,$insert);
				//echo $insert;
			}
			else
			{
				if ($retu_id == '' && $retu_id1 == '0')
				{
					$error = 1;
					$msg .= ' Date of Leaving Already exist for Director';

				}
				else
				{
					$sqlQry="SELECT * FROM `tbl_consumeruser` where consumerisdirector = 0 and consumeruser_id = '".$this->consumeruser_id."' ";
					$res = mysqli_query($this->dbconnection,$sqlQry);

					if(mysqli_num_rows($res) > 0 )
					{
						$error = 1;
						$msg .= ' Member was never a  Director';
					}
					else
					{
						$insert="insert into tbl_members_servicerec set
						consumeruser_id='".$this->consumeruser_id."',
						member_designation='director',
						member_dol='".$this->dir_dol."',
						created_on='".date('Y-m-d')."'";
						//echo $insert;			
						mysqli_query($this->dbconnection,$insert);
					}
				}
				
			}

		}



		if ($this->officer_doj != '' && $this->officer_dol == '')
		{
			$retu_id = '';
			$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='officer' and member_doj != '' and  isnull(member_dol) ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				while ($records=mysqli_fetch_object($res))
				{
					//print_r($records);
					$retu_id = $records->servicerec_id;
				}	
			}
			if ($retu_id == '')
			{
				$insert="insert into tbl_members_servicerec set
				consumeruser_id='".$this->consumeruser_id."',
				member_designation='officer',
				consumerofficertitle='".$this->consumerofficertitle."',
				consumerotherofficertitle='".$this->consumerotherofficertitle."',
				member_doj='".$this->officer_doj."',
				created_on='".date('Y-m-d')."'";
				
				mysqli_query($this->dbconnection,$insert);
				//echo $insert;

				$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
				//die;
				//return $this->lastInsertedId;
			}
			else
			{
				$error = 1;
				$msg .= ' Date of Joining Already exist for officer';
			}
		}
		if ($this->officer_dol != '')
		{
			$retu_id = '';
			$retu_id1 = '';
			$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='officer' ";
			$res = mysqli_query($this->dbconnection,$sqlQry);

			if(mysqli_num_rows($res)>0)
			{
				$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='officer' and isnull(member_dol)";
				$res = mysqli_query($this->dbconnection,$sqlQry);
				if(mysqli_num_rows($res)>0)
				{
					$retu_id1 = '1';

				}
				else
				{
					$retu_id1 = '0';
				}
			}
			if ($retu_id == '' && $retu_id1 == '1')
			{
				$retu_id = '';
				$sqlQry="select * from tbl_members_servicerec where tbl_members_servicerec.consumeruser_id='".$this->consumeruser_id."' and member_designation='officer' and member_doj != '' and  isnull(member_dol) ";
				$res = mysqli_query($this->dbconnection,$sqlQry);

				if(mysqli_num_rows($res)>0)
				{
					while ($records=mysqli_fetch_object($res))
					{
						//print_r($records);
						$retu_id = $records->servicerec_id;
					}	
				}
				if ($retu_id == '')
				{
					$insert="insert into tbl_members_servicerec set
					consumeruser_id='".$this->consumeruser_id."',
					member_designation='officer',
					consumerofficertitle='".$this->consumerofficertitle."',
					consumerotherofficertitle='".$this->consumerotherofficertitle."',
					member_dol='".$this->officer_dol."',
					created_on='".date('Y-m-d')."'";
				}
				else
				{
					$insert="update  tbl_members_servicerec set
					consumeruser_id='".$this->consumeruser_id."',
					member_designation='officer',
					member_dol='".$this->officer_dol."',
					created_on='".date('Y-m-d')."' where servicerec_id = '".$retu_id."'";
				}

					//echo $insert;
				
				
				mysqli_query($this->dbconnection,$insert);
				//echo $insert;
			}
			else
			{
				if ($retu_id == '' && $retu_id1 == '0')
				{
					$error = 1;
					$msg .= ' Date of Leaving Already exist for officer';

				}
				else
				{
					$insert="insert into tbl_members_servicerec set
					consumeruser_id='".$this->consumeruser_id."',
					consumerofficertitle='".$this->consumerofficertitle."',
					consumerotherofficertitle='".$this->consumerotherofficertitle."',
					member_designation='officer',
					member_dol='".$this->officer_dol."',
					created_on='".date('Y-m-d')."'";
					//echo $insert;			
					mysqli_query($this->dbconnection,$insert);
				}
				
			}
		}

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_members_servicerec';
			$objUtility->datatableidField ='servicerec_id';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Added Member service detail';
			$objUtility->user_id=$_SESSION['sessuserid'];
			$objUtility->dataId=$this->lastInsertedId;
			$objUtility->description='Added member service with  id: ['.$this->lastInsertedId.']';
			$objUtility->logTrack();
			//return $this->lastInsertedId;

		//return $this->lastInsertedId;
		return array($error,$msg);
		
	}

}