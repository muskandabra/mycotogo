<?php include_once(PATH."classes/Utility.php");
include_once(PATH."classes/User.php");?>
<?php 
Class OtherMember
{
	var $consumer_id='';
	var $SignerEmail = '';
	var $SignerfName = '';
	var $SignerlName = '';
	var $document_id = '';
	var $status = '';
	
	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
		$this->updatedDate = date('Y-m-d H:i:s');
	}

	function addOtherMember()
	{
		$insert="insert into tbl_manualsign_email set
		consumer_id='".$this->consumer_id."',
		email='".$this->SignerEmail."',
		fname='".$this->SignerfName."',
		lname='".$this->SignerlName."',
		document_id='".$this->document_id."',
		status='".$this->status."',
		createdDate=now()";

		
		mysqli_query($this->dbconnection,$insert);
		//echo $insert;

		// $this->lastInsertedId=mysqli_insert_id($this->dbconnection);
		// $objUtility = new Utility();
		// $objUtility->dataTable = 'tbl_consumermaster';
		// $objUtility->datatableidField ='consumer_id';
		// $objUtility->usertype=$this->usertype;
		// $objUtility->action='Added Consumer';
		// $objUtility->user_id=$this->created_user_id;
		// $objUtility->dataId=$this->lastInsertedId;
		// $objUtility->description='Added Consumer with File No: ['.$this->consumer_fileno.']';
		// $objUtility->logTrack();
	}

	function getMemberUniqueEmail()
	{
		if($this->consumer_id!='')
		{
			$sqlQry = "SELECT DISTINCT CONCAT(fname, lname, email), fname, lname, email FROM `tbl_manualsign_email` where consumer_id='".$this->consumer_id."'";
			return mysqli_query($this->dbconnection,$sqlQry);

		}
	}

}

