<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class PdfShareSub

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
	var $template_name 	=	'';

	var $update = 0;
	

	

	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

	function generatePDf()

	{
		//echo $sqlQry="SELECT consumeruser_id  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$sqlQry="SELECT consumeruser_id  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 and consumeruser_id not in (select to_userid from tbl_sharetransfer_data where to_userid = tbl_consumeruser.consumeruser_id and tbl_consumeruser.consumersharecertno = tbl_sharetransfer_data.cert_no_issued_to) UNION SELECT consumeruser_id  fieldValue FROM tbl_consumeruser WHERE consumer_id = '".$this->consumer_id."' and consumerisshareholder=1 and consumeruser_id in (select to_userid from tbl_sharetransfer_data where to_userid = tbl_consumeruser.consumeruser_id and tbl_consumeruser.consumersharecertno = tbl_sharetransfer_data.cert_no_issued_to and tbl_sharetransfer_data.from_userid = 0)";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholder="";

		$countFiles=1;


		while($row=mysqli_fetch_object($res))

		{

			$objTemplate = new Template();

			$objTemplate->update = $this->update;

			$objTemplate->consumer_id=$this->consumer_id;

			$objTemplate->consumeruser_id=$row->fieldValue;

			$objTemplate->pdfType='multiple';

			$objTemplate->pdfCount=$countFiles;

			$objTemplate->parent_id=$this->folder_id;

			$objTemplate->user_id=$this->user_id; 

			$objTemplate->permission=$this->permission; 

			$objTemplate->template_name= $this->template_name;

			if ($this->template_name == 'SHARESUB')
			{
				// $further_count = 0;
				// $sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE from_userid = '".$row->fieldValue."' or to_userid = '".$row->fieldValue."'";

				// $res_cert=mysqli_query($this->dbconnection,$sqlQry);

				// while($row_cert=mysqli_fetch_object($res_cert))
				// {

				// 	$objTemplate->transfer_id = $row_cert->cert_transfer_id;
				// 	$objTemplate->consumeruser_id=$row->fieldValue;
				// 	$further_count++;
				// 	$objTemplate->pdfCount = $countFiles.$further_count;
				// }
				// $objTemplate->generatePDF($this->filePath);
				
			}
			else
			{
				//$objTemplate->generatePDF($this->filePath);

			}

			$objTemplate->generatePDF($this->filePath);

			$countFiles++;

		}

	}

	

}

?>