<?php include_once(PATH."classes/Utility.php");

include_once(PATH."classes/User.php");

?>

<?php

	Class Module

	{
		var $usertype_id=0;

		var $module_id=0;

		var $view=0;

		var $add=0;

		var $edit=0;

		var $delete=0;

		var $modulename='';

		var $isEnable='';

		var $createdDate='';

		var $isDeleted=0;

		var $usertype='';

		var $user_id='';
		var $dbconnection	=	'';
		


		function __construct()
		{
			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		}

		

		function addModule()

		{

			$sqlQry="Insert into tbl_module set

			modulename='".$this->modulename."',

			isEnable=1,

			createdDate=curdate()";

			$resselect=mysqli_query($this->dbconnection,$sqlQry);

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_module';

			$objUtility->datatableidField ='module_id';

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Added Module';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->dataId=$this->lastInsertedId;

			$objUtility->description='Added Module with Modulename:['.$this->modulename.']';

			$objUtility->logTrack();

			

		}

		function getModule()

		{

			$sqlQry="select * from tbl_module";

			return mysqli_query($this->dbconnection,$sqlQry);

		}

		function selectModule()

		{

			$sqlQry="select * from tbl_module where module_id='".$this->module_id."'";

			return mysqli_query($this->dbconnection,$sqlQry);

		}

		function editModule()

		{

			$sqlselect="update  tbl_module set modulename='".$this->modulename."'

			where module_id='".$this->module_id."'";

			$resselect=mysqli_query($this->dbconnection,$sqlselect);	

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_module';

			$objUtility->datatableidField ='module_id';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Updated Module';

			$objUtility->dataId=$this->module_id;

			$objUtility->description='Updated Module Info ';

			$objUtility->logTrack();					

		}

		function isFound()

		{

			if ($this->module_id== 0)

			$sqlPK="Select * from tbl_module where modulename='".$this->modulename."'";

			else

			$sqlPK="Select * from tbl_module where modulename='".$this->modulename."' and module_id='".$this->module_id."' ";

			$resFound=mysqli_query($this->dbconnection,$sqlPK);

			return mysqli_num_rows($resFound);

		}

		function addModuleRights()

		{

			$objUser= new User();

			$sqlQry="Insert into `tbl_modulerights` set

			`module_id`='".$this->module_id."',

			`usertype_id`='".$this->usertype_id."',

			`view`='".$this->view."',

			`add`='".$this->add."',

			`edit`='".$this->edit."',

			`delete`='".$this->delete."',

			`isDeleted`='".$this->isDeleted."',

			`isEnable`=1,

			`createdDate`=curdate()";

			$res= mysqli_query($this->dbconnection,$sqlQry);

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_modulerights';

			$objUtility->datatableidField ='modulerights_id';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Provide Module Rights ';

			$objUtility->dataId=$this->lastInsertedId;

			$objUtility->description='Give access to User Type: ['.$objUser->getUserType($this->usertype_id).'] on Module:['.$this->getModuleName($this->module_id).'] View['.$this->view.'],Add['.$this->add.'],Edit['.$this->edit.'],Delete['.$this->delete.']'; 

			$objUtility->logTrack();

		}

		function getModuleName($module_id)

		{

			$sqlQuery="select modulename from  tbl_module where module_id='".$module_id."'";

			$res=mysqli_query($this->dbconnection,$sqlQuery);

			$row= mysqli_fetch_object($res);

			$usertype= $row->modulename;

			return $usertype;

		}

		function deleteModuleRights()

		{

			$objUser= new User();

			$sqlQry="delete from  tbl_modulerights where usertype_id='".$this->usertype_id."'";

			mysqli_query($this->dbconnection,$sqlQry);	

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_modulerights';

			$objUtility->datatableidField ='modulerights_id';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Delete Module Rights ';

			$objUtility->dataId=$this->usertype_id;

			$objUtility->description='Delete Access of User Type: ['.$objUser->getUserType($this->usertype_id).']'; 

			$objUtility->logTrack();

		}

		

		function getModuleRights($p_module_id)

		{

			$sqlQry="select * from tbl_modulerights where module_id='".$p_module_id."' and usertype_id='".$this->usertype_id."'";

			return mysqli_query($this->dbconnection,$sqlQry);

		}

		

		function getModuleId()

		{

			$sqlQry="select module_id from tbl_module where modulename='".$this->modulename."' ";

			return mysqli_query($this->dbconnection,$sqlQry);

		}
		

	}