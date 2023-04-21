<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class Faq

	{

		var $usertype_id=0;

		var $faq_id=0;

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

		function addFaq()

		{

			$sqlQry="Insert into tbl_faq set

			faqauestion='".addslashes($this->faqauestion)."',

			faqanswer='".addslashes($this->faqanswer)."',

			createdDate=curdate()";

			$resselect=mysqli_query($this->dbconnection,$sqlQry);

			$this->lastInsertedId=mysqli_insert_id($this->dbconnection);

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_faq';

			$objUtility->datatableidField ='faq_id';

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Added Faq';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->dataId=$this->lastInsertedId;

			$objUtility->description='Added Faq with Modulename:['.$this->modulename.']';

			$objUtility->logTrack();

			//echo $sqlQry;
		}

		function SelectFaq()

		{

			if($this->faq_id!=0)

			{

				echo $sqlQry="select * from tbl_faq where faq_id='".$this->faq_id."'";

			}

			else

			{

				$sqlQry="select * from tbl_faq";

			}

			return mysqli_query($this->dbconnection,$sqlQry);

		}

		function editFaq()

		{

			$sqlselect="update  tbl_faq set 

			faqauestion='".addslashes($this->faqauestion)."',

			faqanswer='".addslashes($this->faqanswer)."'

			where faq_id='".$this->faq_id."'";

			

			$resselect=mysqli_query($this->dbconnection,$sqlselect);	

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_faq';

			$objUtility->datatableidField ='faq_id';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Updated Faq';

			$objUtility->dataId=$this->faq_id;

			$objUtility->description='Updated Faq Info ';

			$objUtility->logTrack();				

		}

		function isquestionExist()

		{

			if ($this->faq_id== 0)

				$sqlPK="Select * from tbl_faq where faqauestion='".addslashes($this->faqauestion)."'";

			else

				$sqlPK="Select * from tbl_faq where faqauestion='".addslashes($this->faqauestion)."' and faq_id!='".$this->faq_id."' ";

			$resFound=mysqli_query($this->dbconnection,$sqlPK);

			return mysqli_num_rows($resFound);

		}

		function deleteFaq()

		{

			$objUser= new User();

			$sqlQry="delete from  tbl_faqrights where usertype_id='".$this->usertype_id."'";

			mysqli_query($this->dbconnection,$sqlQry);	

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_faqrights';

			$objUtility->datatableidField ='modulerights_id';

			$objUtility->usertype=$_SESSION['usertype'];

			$objUtility->user_id=$this->user_id;

			$objUtility->action='Delete Faq Rights ';

			$objUtility->dataId=$this->usertype_id;

			$objUtility->description='Delete Access of User Type: ['.$objUser->getUserType($this->usertype_id).']'; 

			$objUtility->logTrack();

		}

		function updateSequence()

		{

			$sqlQry = "update tbl_faq SET sequence='".$this->sequence."' WHERE faq_id = '".$this->faq_id."'";

			mysqli_query($this->dbconnection,$sqlQry);

			$objUtility = new Utility();

			$objUtility->dataTable = 'tbl_faq';

			$objUtility->dataId=$this->faq_id;

			$objUtility->description='Order Changed for faq';

			$objUtility->logTrack();

		}

	

	}