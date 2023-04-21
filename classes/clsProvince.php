<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class Province

{

	var $name='';

	var $country='';

	var $consumer_id=0;

	var $user_id=0;

	var $state_id='';

	
	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
		$this->updatedDate = date('Y-m-d H:i:s');
	}


	// function Province()

	// {

	// 	$dbconnection= new Database(DBHOST,DBUSER,DBPASS,DBNAME);

	// 	$this->userkey = rand(111111,999999);

	// }

	

	

	function selectProvince()

	{

		$sqlQry="select * from tbl_state where 1 ";

		if($this->country!='')

		{

			$sqlQry=$sqlQry." and  country ='".$this->country."' ORDER BY `tbl_state`.`name` ASC ";

		}

		else

		{

			$sqlQry=$sqlQry." and  country !='other' ORDER BY country ASC";

		}

		//echo $sqlQry;

		//die;
		return mysqli_query($this->dbconnection,$sqlQry);

		// return mysql_query($sqlQry);

	}

	function addProvince()

	{

		$select=mysqli_query($this->dbconnection,"SELECT state_id  FROM  tbl_state ORDER BY state_id  DESC LIMIT 1");

		$fetch=mysqli_fetch_object($select);

		$state_id=$fetch->state_id+1;

		

		$sqlQry='Insert into tbl_state set 

			state_id="'.$state_id.'",

			name="'.$this->name.'", 

			country ="'.$this->country.'"';

			$query=mysqli_query($this->dbconnection,$sqlQry);

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

			$objUtility = new Utility();

			$objUtility->datatableidField ='tbl_state';

			$objUtility->dataTable = 'tbl_state';

			$objUtility->action='Added province';

			$objUtility->user_id=$_SESSION['sessuserid'];

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->dataId=$this->lastInsertedId;

			$objUtility->description='Added Province with Province Name:['.$this->name.']';

			$objUtility->logTrack();

			return $state_id;

	}

	function isfound()

	{

		$sqlQry	=	"select * from tbl_state where name='".$this->name."' and country='".$this->country."'";

		// $query	=	mysql_query($sqlQry);
		$query =  mysqli_query($this->dbconnection,$sqlQry);


		if(mysqli_num_rows($query)=='0')

		{

			return mysqli_num_rows($query);

		}

		else

		{

			$res=mysqli_fetch_object($query);

			return $res->state_id;

		}

	}

}

?>