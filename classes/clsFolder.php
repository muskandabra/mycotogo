<?php include_once(PATH."classes/Utility.php");



Class Folder

{


	var $sys_folder_type;

	var $sys_folder_description;

	var $parent_id='';

	var $sequence_id='';

	var $folder_id='';

	//var $name='';

	var $createddate='';

	var $isdeleted=0; 

	var $permission='';

	var $consumer_id='';

	var $user_id='';

	var $isAutomatic='';

	var $consumerfilestatus_id='';

	var $sys_folder_name='';

	var $sys_folder_id='';

	var $foldername='';

	var $uploadtype='';
	var $dbconnection	=	'';
	

	

	function __construct()

	{

		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

	}

	function selectFolder()

	{

		if($this->consumer_id!='' && $this->folder_id=='')

		{

			$sel="select document_id,name,uploadtype from tbl_document where consumer_id='".$this->consumer_id."' and parent_id='0'";

		}

		else

		{

			if($this->folder_id=='')

				$sel="select * from tbl_document where isdeleted='".$this->isdeleted."'";

			else

				$sel="select * from tbl_document where document_id='".$this->folder_id."' and isdeleted='".$this->isdeleted."'";

		}

		return mysqli_query($this->dbconnection,$sel);

	}

	function selectAttachmentFiles()

	{

		$sel="select name, document_id,uploadtype,oneSpanSignId from tbl_document where parent_id='".$this->document_id."' and isdeleted='".$this->isdeleted."'";

		return mysqli_query($this->dbconnection,$sel);

	}

	function addFolder()

	{

			$sqlQry='insert into tbl_document set 

			name="'.$this->sys_folder_name.'", 

			consumer_id ="'.$this->consumer_id.'", 

			created_user_id ="'.$this->user_id.'", 

			permission="'.$this->permission.'",

			Description="'.$this->sys_folder_description.'", 

			documenttype="'.$this->sys_folder_type.'",

			parent_id="'.$this->parent_id.'",

			uploadtype="'.$this->uploadtype.'",

			sequence_id="'.$this->sequence_id.'",

			createddate=NOW(),

			isdeleted=0';

			$query=mysqli_query($this->dbconnection,$sqlQry);

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

			$objUtility = new Utility();

			$objUtility->document_id = $this->lastInsertedId;

			$objUtility->documenttype='Folder';

			$objUtility->consumer_id=$this->consumer_id;

			$objUtility->description='Created Folder With Folder Name:['.$this->sys_folder_name.']';

			$objUtility->user_id=$this->user_id;

			$objUtility->isAutomatic=$this->isAutomatic;

			$objUtility->action='Created';

			$objUtility->documentLogTrack();

			return $this->lastInsertedId;

	}

	

	function getSysFolder()

	{

		if($this->consumerfilestatus_id!='')

		{

			$slQry="select * from tbl_sys_folder where consumerfilestatus_id='".$this->consumerfilestatus_id."'";

		}

		else

		{

			$slQry="select * from tbl_sys_folder where sys_folder_id='".$this->sys_folder_id."'";

		}
		//echo $slQry;

		return mysqli_query($this->dbconnection,$slQry);

	}

	

	

	function isFileExist()

	{

		if($this->consumer_id!='' && $this->sys_folder_name!='')

		{

			$sqlQry="select * from tbl_document where consumer_id='".$this->consumer_id."' and name='".$this->sys_folder_name."'";

		}

		else

		{

			if($this->sys_folder_name!='' && $this->sys_folder_id!='')

			{

				$sqlQry="select * from tbl_sys_folder where  sys_folder_name='".$this->sys_folder_name."' and sys_folder_id!='".$this->sys_folder_id."'";

			}

			else

			{

				$sqlQry="select * from tbl_sys_folder where  sys_folder_name='".$this->sys_folder_name."'";

			}

		}
		//echo $sqlQry;

		$res =mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res)==0)

		{

			return mysqli_num_rows($res);

		}

		else

		{

			$record=mysqli_fetch_object($res);

			return $record->document_id;

		}

	}

	function getParentId($pConsumer_id)

	{

		$sqlQry="select document_id from tbl_document where consumer_id='".$pConsumer_id."' and parent_id=0";

		if($this->foldername!='')

		{

			$sqlQry = $sqlQry." and name='".$this->foldername."'";

		}

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>'0')

		{

			$row=mysqli_fetch_object($res);

			return $row->document_id;

		}

	}

	function CheckOneSpanId()
	{
		$oneSpanSign_Status = '';
		$oneSpanSignId = '';
		$oneSpanSignIdSec = '';
		$sqlQry="select * from tbl_document where consumer_id='".$this->consumer_id."'  and oneSpanSignId != '' and uploadtype  != 'manual'";
		$res=mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res) > 0)
		{
			while ($row=mysqli_fetch_object($res))
			{
				$oneSpanSignIdSec = $row->oneSpanSignId;
								
				$oneSpanSign_Status = $row->oneSpanSign_Status;
				$oneSpanSignId = $row->oneSpanSignId;
				if ($oneSpanSign_Status == 'SIGNING_PENDING' || empty($oneSpanSign_Status))
				{
					return array($oneSpanSignId,$oneSpanSign_Status);
				}				
			}
			// if (empty($oneSpanSignId))
			// {
			// 	$oneSpanSignId = $oneSpanSignIdSec;
			// }
			return array($oneSpanSignId,$oneSpanSign_Status);
		}
		return '';

	}

	function CheckOneSpanExtract()
	{
		$oneSpanSign_Status = '';
		$oneSpanSignId = '';
		$oneSpanSignIdSec = '';
		$sqlQry= "select * from tbl_document where consumer_id='".$this->consumer_id."' and documenttype = 'Attachment' and (oneSpanSignId != '' and oneSpanSignId is not NULL) and oneSpanSign_Status = 'COMPLETED' and ( signed_docname = '' or signed_docname is NULL) ";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		return mysqli_num_rows($res) ;		
	}



	function CheckPendingSign()
	{
		$oneSpanSign_Status = '';
		$oneSpanSignId = '';
		$oneSpanSignIdSec = '';
		
		$sqlQry= "select * from tbl_document where consumer_id = '".$this->consumer_id."'  and (oneSpanSignId = '' or oneSpanSignId is NULL) and isdeleted = 0 and (substr(name,1,6) ='DIRMIN' or substr(name,1,6)='DIRCON' or substr(name,1,8 )='SHAREMIN' or substr(name,1,8)='SHARESUB' or (substr(name,1,9)='SHARECERT' and name not like  '%cancel%') )";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res) > 0)
		{
			return 1;

		}
		else
		{
			return 0;
		}
	}
	

	function updateFolder()

	{

		$updateQry='Update tbl_document set parent_id="'.$this->parent_id.'" where document_id="'.$this->document_id.'"';

		mysqli_query($this->dbconnection,$updateQry);

	}

	function addFileSystem()

	{

		$sqlQry='insert into tbl_sys_folder set 

		sys_folder_name="'.$this->sys_folder_name.'", 

		sys_folder_description="'.$this->sys_folder_description.'", 

		sys_folder_type="'.$this->sys_folder_type.'",

		consumerfilestatus_id="'.$this->consumerfilestatus_id.'",

		parent_id="'.$this->parent_id.'"';

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		$objUtility = new Utility();

		$objUtility->datatableidField ='sys_folder_id';

		$objUtility->dataTable = 'tbl_sys_folder';

		$objUtility->action='Added File';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->lastInsertedId;

		$objUtility->description='Added File with File Name:['.$this->sys_folder_name.']';

		$objUtility->logTrack();
		 //echo $sqlQry;
	}

	

	function editFileSystem()

	{

		$sqlQry='update  tbl_sys_folder set 

		sys_folder_name="'.$this->sys_folder_name.'", 

		sys_folder_description="'.$this->sys_folder_description.'", 

		consumerfilestatus_id="'.$this->consumerfilestatus_id.'"

		where sys_folder_id='.$this->sys_folder_id.'';

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->datatableidField ='sys_folder_id';

		$objUtility->dataTable = 'tbl_sys_folder';

		$objUtility->action='Updated File';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->sys_folder_id;

		$objUtility->description='Updated File with File Name:['.$this->sys_folder_name.']';

		$objUtility->logTrack();

	}

	function DeleteFile()

	{

		$sqlQry='Delete from tbl_document where document_id ='.$this->document_id.'';

		$query=mysqli_query($this->dbconnection,$sqlQry);

		

		$sqlQry1='Delete from tbl_document where parent_id ='.$this->document_id.'';

		$query=mysqli_query($this->dbconnection,$sqlQry1);

		

		$objUtility = new Utility();

		$objUtility->datatableidField ='document_id';

		$objUtility->dataTable = 'tbl_document';

		$objUtility->action='Deleted File';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->document_id;

		$objUtility->description='Delete File with document id:['.$this->document_id.']';

		$objUtility->logTrack();

	}

	function DeleteFileRecreate()

	{

		$sqlQry='Delete from tbl_document where document_id ='.$this->document_id.'';

		$query=mysqli_query($this->dbconnection,$sqlQry);

		

		$sqlQry1='Delete from tbl_document where parent_id ='.$this->document_id.' and  uploadtype!="manual"';


		$query=mysqli_query($this->dbconnection,$sqlQry1);

		

		$objUtility = new Utility();

		$objUtility->datatableidField ='document_id';

		$objUtility->dataTable = 'tbl_document';

		$objUtility->action='Deleted File';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->document_id;

		$objUtility->description='Delete File with document id:['.$this->document_id.']';

		$objUtility->logTrack();

	}

	function DeleteAttachmentFileEach()
	{

		$sqlQueryDelParent='Delete from tbl_document where document_id ='.$this->document_id.' and uploadtype!="manual"';

		mysqli_query($this->dbconnection,$sqlQueryDelParent);

		$objUtility = new Utility();

		$objUtility->datatableidField ='document_id';

		$objUtility->dataTable = 'tbl_document';

		$objUtility->action='Deleted Attachment';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->document_id;

		$objUtility->description='Delete File with document id:['.$this->document_id.']';

		$objUtility->logTrack();
	}


	function DeleteAttachmentFile()

	{

		$sqlQueryDelParent='Delete from tbl_document where parent_id ='.$this->document_id.' and uploadtype!="manual"';

		mysqli_query($this->dbconnection,$sqlQueryDelParent);

		

		$objUtility = new Utility();

		$objUtility->datatableidField ='document_id';

		$objUtility->dataTable = 'tbl_document';

		$objUtility->action='Deleted Attachment';

		$objUtility->user_id=$_SESSION['sessuserid'];

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->dataId=$this->document_id;

		$objUtility->description='Delete File with parent id:['.$this->document_id.']';

		$objUtility->logTrack();

	}

	function selectRandomFolderName()

	{

		$select="select consumerfolder_id from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		return $query=mysqli_query($this->dbconnection,$select);

	}

	function selectDelFiles()

	{

		$selectDelFiles="select name, oneSpanSignId  from tbl_document where consumer_id='".$this->consumer_id."' and uploadtype!='manual' and documenttype='Attachment'";

		return mysqli_query($this->dbconnection,$selectDelFiles);

	}

}

?>