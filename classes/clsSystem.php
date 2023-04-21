<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class SystemSetting

{

	var $sys_id='';

	var $sys_name='';

	var $sys_value='';

	var $sys_filecontent='';

	var $type='';

	var $user_id='';

	var $usertype='';

	var $dbconnection	=	'';
	

	

	function __construct()

	{

		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

	

	function showSysrecordes()

	{

		if($this->type!='')

		{

			$sqlQry="select * from tbl_systemsetting where sys_value='".$this->type."'";

		}

		else

		{

			if($this->sys_id!='')

			{

				$sqlQry="select * from tbl_systemsetting where sys_id='".$this->sys_id."'";

				

			}

			else

			{

				$sqlQry="select * from tbl_systemsetting";

			}

		}

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	function isFound()

	{

		if ($this->sys_id== 0)

			$sqlPK="Select * from tbl_systemsetting where sys_name='".$this->sys_name."'";

		/*else

			$sqlPK="Select * from tbl_systemsetting where sys_name='".$this->sys_name."' and sys_id='".$this->sys_id."' ";*/

		$resFound=mysqli_query($this->dbconnection,$sqlPK);

		return mysqli_num_rows($resFound);

	}

	function editSystemDetails()

	{

		$sqlselect="update  tbl_systemsetting set sys_name='".$this->sys_name."',sys_filecontent='".$this->sys_filecontent."'

		where sys_id='".$this->sys_id."'";

		$resselect=mysqli_query($this->dbconnection,$sqlselect);		

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_systemsetting';

		$objUtility->datatableidField ='sys_id';

		$objUtility->action='Updated';

		$objUtility->dataId=$this->sys_id;

		$objUtility->user_id=$this->user_id;

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->description='Updated System details ';

		$objUtility->logTrack();

	}

}

?>