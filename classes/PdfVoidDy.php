<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class PdfVoidDy

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

		$sqlQry="SELECT consumeruser_id  fieldValue, consumerfname FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

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

			//echo "222";

			if ($this->template_name == 'Sharecert')
			{
				$further_count = 0;

				$sqlQry="select  * from tbl_consumeruser where tbl_consumeruser.consumeruser_id='".$row->fieldValue."' and consumersharecertno not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$row->fieldValue."') and consumersharecertno not in (select cert_no_issued_to from tbl_sharetransfer_data where tbl_sharetransfer_data.to_userid='".$row->fieldValue."')";

				$res_cert=mysqli_query($this->dbconnection,$sqlQry);

				while($row_cert=mysqli_fetch_object($res_cert))
				{

					$objTemplate->transfer_id = '0';
					$objTemplate->cancelled = '';
					$objTemplate->consumeruser_id=$row->fieldValue;
					$further_count++;
					$objTemplate->pdfCount = $countFiles.$further_count.'_'.$row->consumerfname.'_'.$row_cert->consumersharecertno;
					$objTemplate->generatePDF($this->filePath);
				}



				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE from_userid = '".$row->fieldValue."' and no_of_shares_from > 0 and cert_no_issued_from not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$row->fieldValue."') order by date";


				$res_cert=mysqli_query($this->dbconnection,$sqlQry);

				while($row_cert=mysqli_fetch_object($res_cert))
				{

					$objTemplate->transfer_id = $row_cert->transfer_id;
						$objTemplate->cancelled = '';
					$objTemplate->consumeruser_id=$row->fieldValue;
					$further_count++;
					$objTemplate->pdfCount = $countFiles.$further_count.'_'.$row->consumerfname.'_'.$row_cert->cert_no_issued_from;
					$objTemplate->generatePDF($this->filePath);
				}

				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE to_userid = '".$row->fieldValue."' and no_of_shares > 0 and cert_no_issued_to not in (select cert_no_cancelled from tbl_sharetransfer_data where tbl_sharetransfer_data.from_userid='".$row->fieldValue."') order by date";


				$res_cert=mysqli_query($this->dbconnection,$sqlQry);

				while($row_cert=mysqli_fetch_object($res_cert))
				{

					$objTemplate->transfer_id = $row_cert->transfer_id;
					$objTemplate->consumeruser_id=$row->fieldValue;
					$objTemplate->cancelled = '';
					$further_count++;
					$objTemplate->pdfCount = $countFiles.$further_count.'_'.$row->consumerfname.'_'.$row_cert->cert_no_issued_to;
					$objTemplate->generatePDF($this->filePath);
				}

				//print cancel certificate

				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE from_userid = '".$row->fieldValue."' and cert_no_cancelled !='' order by date";


				$res_cert=mysqli_query($this->dbconnection,$sqlQry);

				while($row_cert=mysqli_fetch_object($res_cert))
				{
					$objTemplate->cancelled = '1';
					$objTemplate->transfer_id = $row_cert->transfer_id;
					$objTemplate->consumeruser_id=$row->fieldValue;
					$further_count++;
					$objTemplate->pdfCount = '_cancel_'.$countFiles.$further_count.'_'.$row->consumerfname.'_'.$row_cert->cert_no_cancelled;
					$objTemplate->generatePDF($this->filePath);
				}



				
			}
			else
			{
				$objTemplate->generatePDF($this->filePath);

			}

			//$objTemplate->generatePDF($this->filePath);

			$countFiles++;

		}

	}


}

?>

