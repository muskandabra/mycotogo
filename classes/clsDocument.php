<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class Document

{

	var $state_id='';

	var $consumer_id=0;

	var $user_id=0;

	var $sys_folder_id='';

	var $document_id=0;

	
	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
	}

	

	function getTemplate()

	{

		$sqlQry="select * from tbl_template";

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	function getTemplateConsumerByUser()

	{

		if($this->user_id!=0)

		{

			$slQry="select consumer_id from tbl_consumermaster where tbl_consumermaster.is_deleted = 0 and active_status =1 and user_id='".$this->user_id."'";

			if($this->consumer_id!='' || $this->consumer_id!=0)

					$slQry=$slQry . "and tbl_consumermaster.consumer_id = '".$this->consumer_id."' ";

			return mysqli_query($this->dbconnection,$slQry);

		}

	}

	function getTemplateFoldersByUser()

	{

		if($this->consumer_id!=0)

		{

			$slQry="select * from tbl_document where consumer_id='".$this->consumer_id."' and parent_id=0";

			return mysqli_query($this->dbconnection,$slQry);

		}

	}

	function getTemplateFoldersCompanyByUser()

	{

		if($this->consumer_id!=0)

		{

			$slQry="select *,tbl_document.name as bookname from tbl_document,tbl_consumermaster where tbl_consumermaster.consumer_id=tbl_document.consumer_id and tbl_consumermaster.is_deleted = 0 and tbl_document.consumer_id='".$this->consumer_id."' and parent_id=0";

			return mysqli_query($this->dbconnection,$slQry);

		}

	}

	function getTemplateFilesByUser()

	{

		if($this->document_id!=0)

		{

			$slQry="select * from tbl_document where parent_id='".$this->document_id."'";

			return mysqli_query($this->dbconnection,$slQry);

		}

	}

	function getStateName()

	{

		$slQry="select * from tbl_state ORDER BY `tbl_state`.`country` ASC";

		return mysqli_query($this->dbconnection,$slQry);

	}

	

	function getFolderName()

	{

		$sqlQry='select * from tbl_sys_folder where parent_id!=0';

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	

	function updateSysFolderInfo()

	{

	

		$sqlQry='update  tbl_sys_folder set 

			name="'.$this->sys_folder_name.'", 

			consumer_id ="'.$this->consumer_id.'", 

			permission="'.$this->permission.'",

			Description="'.$this->sys_folder_description.'", 

			documenttype="'.$this->sys_folder_type.'",

			parent_id="'.$this->parent_id.'",

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

}

?>