<?php include_once(PATH."classes/Utility.php");

Class Permissions

{

	var $notificationcreatedby='';

	var $document_id='';

	var $consumer_id='';

	var $notification_category_id='';

	var $notificationdate='';

	var $notificationdescription='';

	var $parent_id='';

	var $notificationstatus='';

	var $created_user_id='';

	var $today='';

	var $notification_id='';

	var $consumer_fileno='';

	var $user_id='0';

	var $usertype='';

	var $Edit;



	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
	}

	function showPermissions()

	{

		//echo "select * from tbl_document where document_id='".$this->document_id."' and created_user_id='".$this->user_id."'";

		if($this->user_id!=0)

			return $sqlQry=mysqli_query($this->dbconnection, "select * from tbl_document where document_id='".$this->document_id."' and created_user_id='".$this->user_id."'");

		else

			return $sqlQry=mysqli_query($this->dbconnection,"select * from tbl_document where document_id='".$this->document_id."'");

	}

	

}

