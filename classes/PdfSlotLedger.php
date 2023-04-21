<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class PdfSlotLedger

{

	var $state_id='';

	var $consumer_id='';

	var $user_id='';

	var $folder_id='';

	var $sys_folder_id='';

	var $permission='';

	var $isSpecific='';

	var $template_id='';

	var $sequence='';

	var $filePath;
	var $dbconnection	=	'';
	

	

	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

	function generatePDf()

	{

		

		$sqlQry="SELECT consumeruser_id  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholder="";

		$countFiles=1;

		while($row=mysqli_fetch_object($res))

		{

			$objTemplate = new Template();

			$objTemplate->consumer_id=$this->consumer_id;

			$objTemplate->consumeruser_id=$row->fieldValue;

			$objTemplate->pdfType='multiple';

			$objTemplate->pdfCount=$countFiles;

			$objTemplate->parent_id=$this->folder_id;

			$objTemplate->user_id=$this->user_id; 

			$objTemplate->permission=$this->permission; 

			$objTemplate->generatePDF($this->filePath);

			$countFiles++;

		}

	}

	

}

?>