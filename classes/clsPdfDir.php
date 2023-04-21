<?php include_once(PATH."classes/Utility.php");?>

<?php 

Class PdfDir

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
			

		// $sqlQry="SELECT *, concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;

		// echo $sqlQry="SELECT consumeremail,consumerfname ,consumerlname,  concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

		$sqlQry="SELECT consumeremail,consumerfname ,consumerlname, concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname)) fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and (consumeruser_id not in (select consumeruser_id from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' ))";


		

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholder="";

		$countFiles=1;


		while($row=mysqli_fetch_object($res))

		{

			$objTemplate = new Template();

			$objTemplate->update = $this->update;

			$objTemplate->consumer_id=$this->consumer_id;

			$objTemplate->dir_name=$row->fieldValue;

			$objTemplate->pdfType='multiple';

			$objTemplate->pdfCount=$row->consumerfname.'_'.$countFiles;

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