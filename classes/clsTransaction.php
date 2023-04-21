<?php include_once(PATH."classes/Utility.php");

include_once(PATH."classes/clsConsumer.php");

Class Transaction

{

	var $transaction_id='';

	var $consumer_id="";

	var $payment_method="";

	var $payment_description="";

	var $payment_status="";

	var $company_email="";

	var $consumer_password='';

	var $usertype='';

	var $user_id='';

	var $consumer_fileno='';



	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
	}


	

	function addTransaction()

	{

		$objConsumer= new Consumer();

		$objConsumer->consumer_id=$this->consumer_id;

		$res=$objConsumer->getCompanyDetails();

		$getCompanyDetail	= mysqli_fetch_object($res);

		//print_r($getCompanyDetail);
		//die;

		$consumer_fileno=$getCompanyDetail->consumer_fileno;

		$insert="insert into tbl_transaction  set

		payment_method='".$this->payment_method."',

		payment_description='".$this->payment_description."',

		payment_status='".$this->payment_status."',

		consumer_id='".$this->consumer_id."',

		transactiondate=curdate()";
		//echo $insert;

		// mysql_query($insert);

		mysqli_query($this->dbconnection,$insert);

		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_transaction';

		$objUtility->datatableidField ='transaction_id';

		$objUtility->action='Added Transaction';

		$objUtility->usertype=$this->usertype;

		$objUtility->user_id=$this->user_id;

		$objUtility->dataId=$this->lastInsertedId;

		$objUtility->description='Added Transaction with Transaction Id:['.$this->lastInsertedId.'] Under Consumer File No:['.$consumer_fileno.'] ';

		$objUtility->logTrack();

		return $consumer_fileno;

	}

	

	function selectTransaction()

	{

		$sqlQry="select * from tbl_transaction  where consumer_id='".$this->consumer_id."'";

		// return mysql_query($sqlQry);
		return mysqli_query($this->dbconnection,$sqlQry);


	}

	

	function editTransaction()

	{

		$objConsumer= new Consumer();

		$objConsumer->consumer_id=$this->consumer_id;

		$res=$objConsumer->getCompanyDetails();

		$consumer_fileno=$res->consumer_fileno;

		$sqlQry="update  tbl_transaction  set

		payment_method='".$this->payment_method."',

		payment_description='".$this->payment_description."',

		payment_status='".$this->payment_status."'

		where consumer_id='".$this->consumer_id."'";

		// mysql_query($sqlQry);
		mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_transaction';

		$objUtility->datatableidField ='consumer_id';

		$objUtility->action='Updated Transaction';

		$objUtility->usertype=$this->usertype;

		$objUtility->user_id=$this->user_id;

		$objUtility->dataId=$this->consumer_id;

		$objUtility->description='Updated Transaction With Consumer Id['.$this->consumer_id.'] Under Consumer File No:['.$consumer_fileno.'] ';

		$objUtility->logTrack();

		return $consumer_fileno;

	}

	

	

	

	

}

?>