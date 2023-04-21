<?php 

	include_once(PATH."classes/Utility.php");

	include_once(PATH."classes/clsPdfShareSub.php");

	include_once(PATH."classes/PdfSlotLedger.php");

	include_once(PATH."classes/PdfVoidDy.php");

	include_once(PATH."classes/clsPdfDir.php");

	include_once(PATH."classes/clsConsumer.php");?>

<?php 

Class Template

{

	var $state_id='';

	var $consumer_id='';

	var $user_id='';

	var $folder_id='';

	var $sys_folder_id='';

	var $permission='';

	var $parent_id='';

	var $isSpecific='';

	var $template_id='';

	var $sequence='';

	var $pdfType='';

	var $pdfCount='';

	var $consumeruser_id='';

	var $dbconnection	=	'';

	var $transfer_id = '';

	var $cancelled = '';

	var $dir_name = '';

	var $update = 0;

	var $UsersTobeSign = '';

	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

	

	function generateTemplate()
	{		
		$this->generateTemp(0);
		$this->generateTemp(1);
	}
	
	function generateTemp($is_template_for_specific_state)
	{
		if($is_template_for_specific_state == 0)
		{

			$sqlQry='select * from tbl_template where is_template_for_specific_state=0 and sys_folder_id='.$this->sys_folder_id.'';
		}
		else
		{
			$sqlQry='SELECT * FROM tbl_template_for_specific_state J, tbl_template M WHERE J.state_id = '.$this->state_id.' AND J.template_id = M.template_id and M.sys_folder_id='.$this->sys_folder_id.' ';
		}
		//echo $sqlQry;
		

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if(mysqli_num_rows($res)>0)

		{

			while($rowTemplate=mysqli_fetch_object($res))

			{

				$filePath=$rowTemplate->template_path;

				if($rowTemplate->isCriteria!='')

				{
					
					$varClass = $rowTemplate->isCriteria;

					//echo $varClass;

					$clsPdfShareSub= new $varClass;

					$clsPdfShareSub->consumer_id=$this->consumer_id;

					$clsPdfShareSub->folder_id=$this->parent_id;

					$clsPdfShareSub->user_id=$this->user_id; 

					$clsPdfShareSub->permission=$this->permission; 

					$clsPdfShareSub->filePath=$filePath;

					$clsPdfShareSub->template_name=$rowTemplate->template_name;

					$clsPdfShareSub->update = $this->update;

					$clsPdfShareSub->generatePdf();
					
				}

				else

				{

					//echo basename($filePath);
					if ($this->update)
					{ 						
						if ($rowTemplate->sys_folder_id != 6)
						{
					
							$this->generatePdf($filePath);
						}
					}
					else
					{
						$this->generatePdf($filePath);
					}					

				}

			}

		}
		
	}

	

	function generatePdf($filePath)

	{

		$name= basename($filePath);

		$nameArr=explode(".",$name);

		$name=$nameArr[0];


		if($this->pdfType=='multiple')

		{

			$name=$nameArr[0].'_'.$this->pdfCount;
			//echo $name;

		}

		else

		{

			$name=$nameArr[0];
			//echo $name;

		}

		$objFile= new File();

		$objFile->name=addslashes($name.'.pdf');

		$objFile->consumer_id=$this->consumer_id;

		$objFile->folder_id=$this->parent_id;

		$objFile->user_id=$this->user_id; 

		$objFile->documenttype='Attachment';



		if ($this->update )
		{
			$document_id = $objFile->checkSignedFile();
			if ($document_id > 0)
			{
				if (strtolower(substr($objFile->name, 0,9)) ==  'sharecert')
				{
					$objFile->document_id = $document_id;			
					$objFile->isdeleted = 0;
					$objFile->markDeleted();
				}
				//echo 'file exist';
				return;
			}
		}

		$folder=PATH."report/template_pdf/";

		$select="select consumerfolder_id from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$query=mysqli_query($this->dbconnection,$select);

		$row=mysqli_fetch_object($query);

		if($row->consumerfolder_id=='')

		{

			$objConsumer = new Consumer();

			$rendomnumber=$objConsumer->generateRandomString();

			$objConsumer->rendomnumber=$rendomnumber;

			$objConsumer->consumer_id=$this->consumer_id;

			$objConsumer->updateConsumerFolder();

			$folderpdf=$folder.$rendomnumber."/";

		}

		else

		{

			$folderpdf=$folder.$row->consumerfolder_id."/";

		}

		

		if(!is_dir($folderpdf))

		{

			$pathpdf=mkdir($folderpdf);

		//echo ('NOT EXIST Export');

		}

		chmod($folderpdf,0777); 

		

		// $files1 = scandir($folderpdf,1);

		// $checkValue= count($files1)-2;

		// $totalValue= count($files1);

		// $iLoop=1;

		// foreach($files1 as $files)

		// {

			// if($iLoop<=$checkValue)

			// {

				// $file_name= $folderpdf.$files;

				// unlink($file_name);

			// }

			// $iLoop++;

		// }

		require_once(PATH."classes/pdfcrowd.php");

		

		$Orgfile = PATH.$filePath;

		

		$tempfile = $newfile = (PATH.'report/temporary_file/'.$name.'_temp.html');

		// if (file_exists($Orgfile))
		// {
		// 	echo "esit";
		// }
		// else
		// {
		// 	echo 'no';
		// }

		if (!copy($Orgfile, $tempfile))

		{

			echo "failed to copy $name...\n";

		}

		$tempFileName= basename($tempfile);

		$str = "VAR_";

		$foundStr = array();

		$file = @fopen(PATH."report/temporary_file/".$tempFileName."", "r");

		if ($file)

		{

			while (!feof($file))

			{

				$buffer = fgets($file);

				if(strpos($buffer, $str) !== FALSE)

				{

					$ab=strrchr($buffer,$str);

					$strLength = strlen($ab);

					//echo ":";

					$strTo = strpos($ab,']');

					$ab = substr($ab,0,$strTo);

					//echo "=</br>";

					if($ab!='')

					{

						$ab='['.$ab.']';

						if(!in_array($ab,$foundStr))

						{

							array_push($foundStr,$ab);	

						}

					}

				}

			}

			fclose($file);

		}

		// if($this->pdfType=='multiple')

		// {

		// 	$name=$nameArr[0].'_'.$this->pdfCount;
		// 	//echo $name;

		// }

		// else

		// {

		// 	$name=$nameArr[0];
		// 	//echo $name;

		// }

		//$objFile= new File();

		$objFile->name=$name.'.pdf';

		$objFile->consumer_id=$this->consumer_id;

		$objFile->folder_id=$this->parent_id;

		$objFile->user_id=$this->user_id; 

		$objFile->permission=$this->permission; 

		$objFile->documenttype='Attachment';

		$objFile->isAutomatic='1';

		$fileNewId = $objFile->addFile();

		$sqlQry="";

		$dataBaseName=array();

		foreach ($foundStr as $value)

		{

			//echo $value;

			switch (trim($value)) 

			{

				case "[VAR_COMPANY_NAME]":

				{

					$sqlQry="select companyname fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_JURISDICTION]":

				{

					$sqlQry="SELECT STATE.name  fieldValue FROM tbl_consumermaster CMASTER, tbl_state STATE WHERE CMASTER.state_id = STATE.state_id AND CMASTER.consumer_id ='".$this->consumer_id."'";

				}

				break;

				case "[VAR_DIRECTOR_MINUTES]":

				{

					$sqlQry	="";

					$fieldValue	= $this->getDirectorMinutes();

				}
				break;

				case "[VAR_CONTENT_FOR_NOTBC]":
				{

					$sqlQry	="";

					$fieldValue	= $this->getContentforNotBC();

				}
				break;

				case "[VAR_DIRECTORS_OFFICERS]":

				{

					$sqlQry	="";

					$fieldValue	= $this->getDirectorOfficerDetails();

				}

				break;

				case "[VAR_SHARE_TRANSFER]":

				{

					$sqlQry	="";

					$fieldValue	= $this->getShareTransfer();
					//die;

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorName();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_CORP]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorName_incorp();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_FIRST_CORP]":

				{	

					$sqlQry="";

					$fieldValue = $this->dir_name;

					//$fieldValue = $this->getDirectorNamefirst_incorp();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_FIRST_CORP_SIGN]":

				{	
					$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

					$res=mysqli_query($this->dbconnection,$sqlQry);

					$row=mysqli_fetch_object($res);

					$fieldValue  = '';
				

					if ($row->signatureMode != 'D')
					{						
						$fieldValue = '__________________________'.'<br>'.$this->dir_name;		
					}
					$sqlQry= "";								

				}

				break;
								
				case "[VAR_COMPANY_DIRECTOR_C_CORP]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorNamecomma_incorp();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_C]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorNamecomma();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_Signature]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorSignature();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_Signature_CORP]":

				{	

					$sqlQry="";

					$fieldValue = $this->getDirectorSignature_corp();

				}

				break;

				case "[VAR_COMPANY_DIRECTOR_M_Signature_CORP_SHRCERT]":

				{	

					$sqlQry="";

					//$fieldValue = $this->getDirectorSignature_corp_shrcert();
					$fieldValue = $this->getDirectorSignature_corp_shrcert_new();
					$objFile= new File();
					$objFile->document_id = $fileNewId;
					$objFile->UsersTobeSign = $this->UsersTobeSign;
					$objFile->updateUsersTobeSign();
					$this->UsersTobeSign = '';
								
				}

				break;
				

				case "[VAR_COMPANY_SHAREHOLDER_M_Signature]":

				{	

					$sqlQry="";

					$fieldValue = $this->getShareholderSignature();

				}

				break;

				case "[VAR_COMPANY_OFFICER_M_Signature]":

				{	

					$sqlQry="";

					$fieldValue = $this->getOfficerSignature();

				}

				break;

				case "[VAR_COMPANY_OFFICER_M_Signature_corp]":

				{	

					$sqlQry="";

					$fieldValue = $this->getOfficerSignature_corp();

				}

				break;


				case "[VAR_COMPANY_OFFICER_M]":

				{	

					$sqlQry="";

					$fieldValue = $this->getOfficerName();

				}

				break;

				case "[VAR_OFFICER_NAME_M]":

				{	

					$sqlQry="";

					$fieldValue = $this->getOfficerTitle();

				}

				break;

				case "[VAR_SHAREHOLDER_NAME_M]":

				{	

					$sqlQry="";

					$fieldValue = $this->getShareholderName();

				}

				break;

				case "[VAR_SHAREHOLDER_LEDGER]":

				{	

					$sqlQry="";

					$fieldValue = $this->getShareholderLedger();

				}

				break;

				

				case "[VAR_INCORPORATED_DATE]":

				{

					$sqlQry="select DATE_FORMAT(updatedDate,'%D day of %M, %Y') fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

					//$fieldValue=date("d-m-y");

				}
				break;

				case "[VAR_INCORPORATED_DATE_TR]":

				{

					$fieldValue = $this->getShareholderfirst_date();

					//$fieldValue=date("d-m-y");

				}

				break;

				case "[VAR_NUMBER_OF_SHARE_CLASS_TYPE_M']":

				{

					$sqlQry="";

					$fieldValue = $this->getShareholderDetails();

				}

				break;

				case "[VAR_NUMBER_OF_SHARE_CLASS_TYPE]":

				{

					$sqlQry="";

					$fieldValue = $this->getShareholderDetailsType();

				}

				break;

				case "[VAR_NUMBER_OF_SHARE_CLASS_TYPE_T]":

				{

					$sqlQry="";

					$fieldValue = $this->getShareholderDetailsType_transfer();

				}

				break;

				case "[VAR_NUMBER_OF_SHARE]":

				{

					if($this->consumeruser_id!='')
					{

						$sqlQry="select consumernoofshares fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

					}

					else
					{

						$sqlQry="select consumernoofshares fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'";

					}

				}

				break;

				case "[VAR_SHARE_CLASS]":

				{

					if($this->consumeruser_id!='')

						$sqlQry="select consumershareclass fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

					else

						$sqlQry="select consumershareclass fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_SHARE_TYPE]":

				{

					if($this->consumeruser_id!='')

						$sqlQry="select consumersharetype  fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

					else

						$sqlQry="select consumersharetype  fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_SHARE_RIGHT]":

				{

					if($this->consumeruser_id!='')

						$sqlQry="select consumershareright  fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

					else	

						$sqlQry="select consumershareright  fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_SHARECERTIFICATE_NUMBER_M]":

				{

					$sqlQry="";

					$fieldValue = $this->getShareholdercertificateno();

				}

				break;
				case "[VAR_SHARECERTIFICATE_NUMBER_M_T]":
				{

					$sqlQry="";

					$fieldValue = $this->getShareholdercertificateno_trans();

				}

				break;

				case "[VAR_NUMBER_OF_SHARE_T]":
				{

					$sqlQry="";

					$fieldValue = $this->getShareholdercertificateno_trans('1');

				}

				break;

				case "[VAR_CANCELLED_STATUS]":
				{

					$sqlQry="";

					$fieldValue = $this->getShareholdercertificate_status();

				}

				break;

					
				case "[VAR_INCORPORATED_DATE_T]":
				{

					$sqlQry="";

					$fieldValue = $this->getShareholder_transdate();

				}

				break;

				

				case "[VAR_PRICE_PER_SHARE]":

				{

					$sqlQry="";

					$fieldValue = $this->getPricePerShare();

				}

				break;

				case "[VAR_TOTAL_SHARE_PRICE]":

				{

					$sqlQry="";

					$fieldValue = $this->gettotalPricePerShare();

				}

				break;

				case "[VAR_SHAREHOLDER_NAME]":

				{

					$sqlQry="select consumertotalshare fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_COMPANY_ADDRESS]":

				{

					$sqlQry="select companymailingaddress fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

				}

				break;

				case "[VAR_FIRST_DIRECTOR]":

				{

					$sqlQry="select concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1 LIMIT 1";

				}

				break;

				case "[VAR_COMPANY_OFFICER_M_DETAILS]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyOfficerDetails();

				}

				break;

				case "[VAR_COMPANY_OFFICER_M_DETAILS_CORP]":

				{
					
					$sqlQry="";

					$fieldValue = $this->getCompanyOfficerDetails_corp();

				}

				break;



				case "[VAR_SHAREHOLDER_M_DETAILS]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyShareholderDetails();

				}

				break;

				case "[VAR_SHAREHOLDER_M_DETAILS_CORP]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyShareholderDetails_corp();

				}

				break;

				case "[VAR_COMPANY_SHAREHOLDER_D]":

				{

					$sqlQry="";

					$sqlQry = $this->getCompanyShareholderNameD();

				}

				break;

				case "[VAR_COMPANY_SHAREHOLDER_D_SIGNATURE]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyShareholderSignatureD();

				}

				break;

				case "[VAR_COMPANY_SHAREHOLDER]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyShareholders();

				}

				break;

				case "[VAR_SHAREHOLDER_ADDRESS]":

				{

					$sqlQry="";

					$fieldValue = $this->getCompanyShareholdersAddress();

				}

				break;

				default:

					$fieldValue="test";

					break;

			}

			if ($sqlQry!="")

			{

				$res=mysqli_query($this->dbconnection,$sqlQry);

				while($row=mysqli_fetch_object($res))

				{

					$fieldValue = rtrim($row->fieldValue,',');

				}

			}

			array_push($dataBaseName,$fieldValue);

			

		}

		
		try

		{

			//echo $folderpdf.$name.".pdf";

			// create an API client instance	

			//temprorory

			$client = new Pdfcrowd("lgriffio", "90be8d99ad190a774b100bd73c1b7584");

			$content = file_get_contents($tempfile);

			// echo"<pre>";

			// print_r($foundStr);

			// print_r($dataBaseName);

			$content = str_replace($foundStr, $dataBaseName, $content);

			file_put_contents($tempfile, $content);

			//$out_file = fopen("report/template_pdf/".$name.".pdf", "wb");

			$out_file = fopen($folderpdf.$name.".pdf", "w");

			//temprorory

			$pdf = $client->convertFile($tempfile,$out_file);

			unlink($tempfile);

			//echo $name;

			//if (substr($name, 0,9) == 'Sharecert' || substr($name, 0,6) == 'LEDGER' || $name =='SHAREHOLDERREGISTER' || $name =='DIRREG')
			
			//echo $content;

		}

		catch(PdfcrowdException $why)

		{

			echo "Pdfcrowd Error: " . $why;

		}  

		
        //echo $sqlQry;

		if (!empty($sqlQry))
		$query=mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'All files stored in'.$folderpdf;

		$objUtility->datatableidField ='consumerfolder_id';

		$objUtility->action='Pdf files Added';

		$objUtility->dataId=$folderpdf;

		$objUtility->user_id=$this->consumer_id;

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->description='FILES STORED IN FOLDER:['.$folderpdf.'] ';

		$objUtility->logTrack();

		

}   

function getmemberName($userid)
{

if (!empty($userid))
{
	$sqlQry="select consumerfname,consumermname, consumerlname  from tbl_consumeruser where consumeruser_id='".$userid."'";
	
	$res=mysqli_query($this->dbconnection,$sqlQry);


		//echo $sqlQry;

		if(mysqli_num_rows($res)>0)
		{
			while($row=mysqli_fetch_object($res))

			{
				return  ucfirst($row->consumerfname).' '.ucfirst($row->consumermname).' '.ucfirst($row->consumerlname);
			}

		}
}
else
{
	return 'Treasury';
}

		

}

	function getProvinceName($pStateid)

	{

		$select=mysqli_query($this->dbconnection,"SELECT name FROM tbl_state where state_id='".$pStateid."'");

		$statename=mysqli_fetch_object($select);

		return $statename->name;

	}

	function getDirectorName()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$directors="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($directors=='')

					$directors = $row->fieldValue;

				else

					$directors = $directors .'<br>'.'<br>'. $row->fieldValue;

			}

		}

		return $directors;

	}

	

	function getDirectorName_incorp()

	{

		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$directors="";

		// if ($row->signatureMode != 'D')
		// {
			$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

			$res=mysqli_query($this->dbconnection,$sqlQry);

			$directors="";

			//echo $sqlQry;

			while($row=mysqli_fetch_object($res))

			{

				if(isset($row->fieldValue))

				{

					if($directors=='')

						$directors = $row->fieldValue;

					else

						$directors = $directors .'<br>'.'<br>'. $row->fieldValue;

				}

			}
		//}

		return $directors;

	}


	function getDirectorNamefirst_incorp()

	{

		$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;



		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$directors="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{
			

			if(isset($row->fieldValue))

			{		

				if($directors=='')

					$directors = '__________________________'.'<br>'.$row->fieldValue;

				else

					$directors = $directors .'<br>'.'<br>'. $row->fieldValue;	

				break;

			}

		}		

		return $directors;

	}


	function getShareholderLedger()
	{
		$sqlQry="select DATE_FORMAT(updatedDate,'%D day of %M, %Y') fieldValue, updatedDate  from tbl_consumermaster where consumer_id='".$this->consumer_id."'";
		$res=mysqli_query($this->dbconnection,$sqlQry);

		//echo $sqlQry;
		$incorpdate = '';
		$noofShares = '';
		$incorpdt 	='';
		$cert_no_alloted = '';
		$noofShares = 0;

		 $cert_no = $this->getShareholdercertificateno();

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{
				$incorpdate = $row->fieldValue;
				$incorpdt = $row->updatedDate;				 
			}
		}

		if($this->consumeruser_id!='')
		{

			$sqlQry="select consumernoofshares fieldValue, consumersharecertno from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

			$res=mysqli_query($this->dbconnection,$sqlQry);
			while($row=mysqli_fetch_object($res))

			{

				if(isset($row->fieldValue))

				{
					$noofShares = $row->fieldValue;
					$cert_no_alloted = $row->consumersharecertno;
				}
			}

		}
		$sharesorgbuy = $noofShares;
		$firstdt =    '';
		$from_userid = '';
		$sqlQry="SELECT from_userid,date FROM `tbl_sharetransfer_data` where (to_userid='".$this->consumeruser_id."' and cert_no_issued_to = '".$cert_no_alloted."') order by date";

		//$sqlQry="SELECT date FROM `tbl_sharetransfer_data` where (to_userid='".$this->consumeruser_id."' and  from_userid ='' ) order by date";


			$res=mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				while($row=mysqli_fetch_object($res))
				{
					$firstdt = $row->date;
					$from_userid = $row->from_userid;
					$sharesorgbuy = 0;
					break;
				}
			}
		if (!empty($firstdt))
		{
			if (strtotime($firstdt) != strtotime($incorpdt))
			{
				$incorpdate =  date("jS \of F Y",strtotime($firstdt));
			}
		}


		$detail='';

		$detail='<table class="table1" cellpadding="0" cellspacing="0">

            	<tr class="row1">

                    <td>DATE</td>

                    <td>NO. OF CERTIFIC ATEISSUED</td>

                    <td>NO. OF CERTIFICATE CANCELLED</td>

                    <td>TRANSFER NO.</td>

                    <td>TO OR FROM WHOM</td>

                    <td>FOLIO</td>

                    <td>PAID UP</td>

                    <td>SOLD SHARES</td>

                    <td>BOUGHT SHARES</td>

                    <td>BALANCE</td>

                </tr>

            	<tr class="row1">

                    <td>'.$incorpdate.'</td>

                    <td>'.$cert_no.'</td>

                    <td></td>

                    <td></td>

                    <td>'.$this->getmemberName($from_userid).'</td>

                    <td></td>

                    <td></td>

                    <td></td>

                    <td>'.$noofShares.'</td>

                    <td>'.$noofShares.'</td>

                </tr>';

        if($this->consumeruser_id!='')
		{
			$sqlQry="SELECT * FROM `tbl_sharetransfer_data` where (from_userid='".$this->consumeruser_id."' or to_userid ='".$this->consumeruser_id."' ) and cert_no_issued_to != '".$cert_no_alloted."' order by date";

			$res=mysqli_query($this->dbconnection,$sqlQry);
			if(mysqli_num_rows($res)>0)
			{
				while($row=mysqli_fetch_object($res))
				{
					$detail.= '<tr class="row1">';

	                $detail.= '<td>'.date("jS \of F Y",strtotime($row->date)).'</td>';

	                $detail.= '<td>';

	                if (!empty($row->cert_no_issued_from) and $row->from_userid == $this->consumeruser_id)
	                {
	                	$detail.= $row->cert_no_issued_from;

	                }
	                if (!empty($row->cert_no_issued_to) and $row->to_userid == $this->consumeruser_id)
	                {
	                	$detail.= $row->cert_no_issued_to;

	                }


	                $detail.= '</td>';

	                $detail.= '<td>';

	                 if (!empty($row->cert_no_cancelled) )
	                {
	                	//and $row->from_userid == $this->consumeruser_id
	                	$detail.= $row->cert_no_cancelled;

	                }

	                $detail.= '</td>';

	                 $detail.= '<td>';

	                 if (!empty($row->transfer_no)) 
	                {
	                	$detail.= $row->transfer_no ;

	                }
	                $detail.= '</td>';

	                 $detail.= '<td>';

	                if ($row->from_userid == $this->consumeruser_id) 
	                {
	                	$detail.= $this->getmemberName($row->to_userid) ;

	                }
	                else
	                {
	                	$detail.= $this->getmemberName($row->from_userid) ;

	                }
	                $detail.= '</td>';

	                $detail.= '<td></td>

	                        <td></td>';

	                $detail.='<td>';

	                if ($row->from_userid == $this->consumeruser_id)
	                {
	                	$detail.=$row->no_of_shares;
	                }

	                $detail.='</td>';

	                 $detail.='<td>';

	                if ($row->to_userid == $this->consumeruser_id)
	                {
	                	$detail.=$row->no_of_shares;
	                }

	                $detail.='</td>';

	                $detail.='<td>';

	                $share_balance = $this->getsharebalance($this->consumeruser_id,$row->date,$sharesorgbuy,$cert_no_alloted);

	                //getsharebalance($consumeruser_id,$date,$noofShares,$cert_no_alloted)

	                if ($row->to_userid == $this->consumeruser_id)
	                {
	                	//$detail.=$row->to_balance;
	                }
	                else
	                {
	                	//$detail.=$row->from_balance;
	                }
	                $detail.=$share_balance;

	                $detail.='</td>                

	                    </tr>';	
				}
			}
		}
		$detail.='</table>';           
		//echo $detail;
			return $detail;


		//$select="Select *  , DATE_FORMAT(updatedDate,'%D day of %M, %Y') fieldValue FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and (consumerisdirector>=1 or consumerisofficer>=1)";

		//$query=mysqli_query($this->dbconnection,$select);

		// if(mysqli_num_rows($query)>0)

		// {
		// 	$post='';
		// 	$dir_dol = '';

		// 	$detail	= '

		// 	<style> 

		// 	th 

		// 	{

		// 		border-bottom: 1px solid;

		// 	}

		// 	td 

		// 	{

		// 		border-bottom: 1px solid;

		// 	}

		// 	</style>

		// 	<table style="width: 100%; border: 3px double;">';

		// 	$detail	= $detail.'

		// 			<thead>

		// 			<tr style="border-bottom: 1px solid;text-align: center;">

		// 				<th style="border-right: 1px solid;">FULL NAME</th>

  //                       <th style="border-right: 1px solid;">ADDRESS</th>

  //                       <th style="border-right: 1px solid;">OCCUPATION</th>

  //                       <th style="border-right: 1px solid;">DATE WHEN APPOINTED OR ELECTED</th>

  //                       <th style="border-right: 1px solid;">DATE WHEN CEASED TO HOLD OFFICE</th>

  //                       <th style="border-right: 1px solid;">POSITION HELD</th>

		// 			</tr> </thead>';

		// 	while($details=mysqli_fetch_object($query))

		// 	{

		// 		$position='';

		// 		if($details->consumerisdirector=='1'){$position=$position."Director".'<br>';$post = 'director';}

				

		// 		if($details->consumerofficertitle=='Other (enter value below)')

		// 			$officertitle = $details->consumerotherofficertitle;

		// 		else

		// 			$officertitle = $details->consumerofficertitle;

					

		// 		if($details->consumerofficertitle!='')
		// 		{
		// 			$position= $position.ucfirst($officertitle);
		// 			//$post = 'officer';

		// 		}

		// 		$selectmem =" Select *  FROM  tbl_members_servicerec,tbl_consumeruser WHERE tbl_members_servicerec.consumeruser_id= tbl_consumeruser. consumeruser_id and tbl_members_servicerec.consumeruser_id ='".$details->consumeruser_id."' and member_designation  = '".$post."'";

		// 		$query_s=mysqli_query($this->dbconnection,$selectmem);

		// 		if(mysqli_num_rows($query_s)>0)

		// 		{
		// 			while($details_s=mysqli_fetch_object($query_s))

		// 			{
		// 				if (!empty($details_s->member_dol))
		// 				{
		// 					echo $dir_dol = date("jS \of F Y",strtotime($details_s->member_dol)); 
		// 					$post = $details_s->member_designation;
		// 				}
		// 			}


		// 		}
		// 		else
		// 		{
		// 			$dir_dol = '';
		// 		}

				 

		// 		$detail=$detail.'

		// 		<tbody>

		// 		<tr style="border-bottom: 1px solid;  text-align: center;">

		// 			<td style=" border-right: 1px solid;">'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

		// 			<td style=" border-right: 1px solid;"><p>'.ucfirst($details->consumeraddress1)." "." ".ucfirst($details->consumercity)." ,".$this->getProvinceName($details->consumerstate_id)." ".ucfirst($details->consumerzipcode).'</p></td>

		// 			<td style=" border-right: 1px solid;"></td>

		// 			<td style=" border-right: 1px solid;">'.$details->fieldValue.'</td>

		// 			<td style=" border-right: 1px solid;">'.$dir_dol.'</td>

		// 			<td style=" border-right: 1px solid;">'.$post.'</td>

		// 		</tr> </tbody>';

				

		// 	}			

			 

		//}

	}

	function getsharebalance($consumeruser_id,$date,$sharesorgbuy ,$cert_no_alloted)
	{
		// $sharesorgbuy = $noofShares;
		// 		$Sqlquery = "SELECT * FROM `tbl_sharetransfer_data` where to_userid='".$consumeruser_id."' and cert_no_issued_to = '".$cert_no_alloted."'";

		// 		$res=mysqli_query($this->dbconnection,$Sqlquery);
		// 		if(mysqli_num_rows($res)>0)
		// 		{
		// 			while($row=mysqli_fetch_object($res))
		// 			{
		// 				$sharesorgbuy = 0  ;

		// 				break;
		// 			}
		// 		}
				//echo $sharesorgbuy;
				//echo "<br>";


				$sharessold = 0;
				$Sqlquery = "SELECT sum(no_of_shares) as sharessold FROM `tbl_sharetransfer_data` where from_userid='".$consumeruser_id."' and date <= '".$date."'";

				$res=mysqli_query($this->dbconnection,$Sqlquery);
				if(mysqli_num_rows($res)>0)
				{
					while($row=mysqli_fetch_object($res))
					{
						$sharessold = $row->sharessold;

						break;
					}
				}

				//echo $sharessold;
				//echo "<br>";
				$sharesbuy = 0;
				$Sqlquery = "SELECT sum(no_of_shares) as sharesbuy FROM `tbl_sharetransfer_data` where to_userid='".$consumeruser_id."' and date <= '".$date."'";

				$res=mysqli_query($this->dbconnection,$Sqlquery);
				if(mysqli_num_rows($res)>0)
				{
					while($row=mysqli_fetch_object($res))
					{
						$sharesbuy = $row->sharesbuy;
						break;
					}
				}

				//echo $sharesbuy;
				//echo "<br>";

				$ShresBalance = $sharesbuy+$sharesorgbuy-$sharessold;

				return $ShresBalance;


	}
	

	function getDirectorOfficerDetails()
	{

		$detail='';

		$select="Select *  , DATE_FORMAT(updatedDate,'%D day of %M, %Y') fieldValue FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and (consumerisdirector>=1 or consumerisofficer>=1)";

		$query=mysqli_query($this->dbconnection,$select);

		if(mysqli_num_rows($query)>0)

		{
			$post='';
			$dir_dol = '';

			$detail	= '

			<style> 

			th 

			{

				border-bottom: 1px solid;

			}

			td 

			{

				border-bottom: 1px solid;

			}

			</style>

			<table style="width: 100%; border: 3px double;">';

			$detail	= $detail.'

					<thead>

					<tr style="border-bottom: 1px solid;text-align: center;">

						<th style="border-right: 1px solid;">FULL NAME</th>

                        <th style="border-right: 1px solid;">ADDRESS</th>

                        <th style="border-right: 1px solid;">OCCUPATION</th>

                        <th style="border-right: 1px solid;">DATE WHEN APPOINTED OR ELECTED</th>

                        <th style="border-right: 1px solid;">DATE WHEN CEASED TO HOLD OFFICE</th>

                        <th style="border-right: 1px solid;">POSITION HELD</th>

					</tr> </thead><tbody>';

			while($details=mysqli_fetch_object($query))
			{
				$flagdirector = 0;
				$flagofficer = 0;	
				$repeat = 1;							
				for ($cnt=1;$cnt<=2;$cnt++)
				{
					$position='';
					$post = '';		
								
					if ($cnt == 1)
					{
						if($details->consumerisdirector >=1){$position=$position."Director";$post = 'director';}
						//$post = 'director';
						if ($details->consumerisdirector >= 1)
							$flagdirector = 1;

						$flagofficer = 0;
					}
					else
					{
						if($details->consumerofficertitle=='Other (enter value below)')
							$officertitle = $details->consumerotherofficertitle;
						else
							$officertitle = $details->consumerofficertitle;
						
						if($details->consumerofficertitle!='')
						{
							$position= ucfirst($officertitle);	
							$post = 'officer';						
						}
						if ($details->consumerisofficer  >= 1)
						{
							$flagofficer = 1;							
						}
						$post = 'officer';	
						$flagdirector = 0;						
					}				
				//echo $details->consumeruser_id;

				$dir_doj = $details->fieldValue;
				$dir_dojdt = $details->updatedDate;

				$firstdt =    '';
				$from_userid = '';
				$sqlQry=" Select *  FROM  tbl_members_servicerec WHERE  tbl_members_servicerec.consumeruser_id ='".$details->consumeruser_id."' and member_designation  = '".$post."' order by member_doj";

				//$sqlQry="SELECT date FROM `tbl_sharetransfer_data` where (to_userid='".$this->consumeruser_id."' and  from_userid ='' ) order by date";


					$res=mysqli_query($this->dbconnection,$sqlQry);
					if(mysqli_num_rows($res)>0)
					{
						while($row=mysqli_fetch_object($res))
						{

							if (is_null($row->member_doj) or $row->member_doj == '')
							{
								$firstdt = '';
							}							
							else
							{
								$firstdt  = $row->member_doj;
								$dir_dojdt = $row->member_doj;
							}
								//$position='';
								if ($row->member_designation == 'director')
								{
									$position='Director';
								}
								else
								{
									if($row->consumerofficertitle=='Other (enter value below)')
									$officertitle = $row->consumerotherofficertitle;
									else
										$officertitle = $row->consumerofficertitle;
									
									if($row->consumerofficertitle!='')
									{
										$position= ucfirst($officertitle);						
									}
								}
							break;
						}
					}

				if (!empty($firstdt))
				{
					
						$dir_doj  =  date("jS \of F Y",strtotime($firstdt));

				}

				$selectmem =" Select *  FROM  tbl_members_servicerec WHERE  tbl_members_servicerec.consumeruser_id ='".$details->consumeruser_id."' and member_designation  = '".$post."' and ((member_dol != '' and member_doj IS NULL) or (member_dol != '' and member_doj = '".$dir_dojdt."'))";

				$query_s=mysqli_query($this->dbconnection,$selectmem);

				if(mysqli_num_rows($query_s)>0)

				{
					while($details_s=mysqli_fetch_object($query_s))

					{
						if (!empty($details_s->member_dol))
						{
							$dir_dol = date("jS \of F Y",strtotime($details_s->member_dol));

							if($details_s->consumerofficertitle=='Other (enter value below)')
								$officertitle = $details_s->consumerotherofficertitle;
							else
								$officertitle = $details_s->consumerofficertitle;								
							if($details_s->consumerofficertitle!='')
							{
								$position= ucfirst($officertitle);						
							}

						}
					}
				}
				else
				{
					$dir_dol = '';
				}


				 if ($flagofficer or $flagdirector)
				 {
					$detail=$detail.'				
					<tr style="border-bottom: 1px solid;  text-align: center;">';
					if ($cnt == 1 || $repeat )
					{
						$detail=$detail.'<td style=" border-right: 1px solid;">'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

						<td style=" border-right: 1px solid;"><p>'.ucfirst($details->consumeraddress1)." "." ".ucfirst($details->consumercity)." ,".$this->getProvinceName($details->consumerstate_id)." ".ucfirst($details->consumerzipcode).'</p></td>';
						$repeat = 0;

					}
					else
					{
						$detail=$detail.'<td style=" border-right: 1px solid;"></td>

						<td style=" border-right: 1px solid;"></td>';

					}


					$detail=$detail.'<td style=" border-right: 1px solid;"></td>

						<td style=" border-right: 1px solid;">'.$dir_doj.'</td>

						<td style=" border-right: 1px solid;">'.$dir_dol.'</td>

						<td style=" border-right: 1px solid;">'.$position.'</td>

					</tr> ';
				}

				$sqlQry=" Select *  FROM  tbl_members_servicerec WHERE  tbl_members_servicerec.consumeruser_id ='".$details->consumeruser_id."' and member_designation  = '".$post."' order by member_doj";

				$res=mysqli_query($this->dbconnection,$sqlQry);
				if(mysqli_num_rows($res)>0)
				{
					$count = 1;
					while($row=mysqli_fetch_object($res))
					{
						if ($count >= 2)
						{
							if (empty($row->member_doj) || is_null($row->member_doj) )
							{
								$jointdt = '';

							}
							else
							{
								$jointdt =  date("jS \of F Y",strtotime($row->member_doj));

							}
							if (empty($row->member_dol))
							{
								$leavedt = '';

							}
							else
							{
								$leavedt =  date("jS \of F Y",strtotime($row->member_dol));

							}
							$position='';
							if ($row->member_designation == 'director')
							{
								$position='Director';
							}
							else
							{
								if($row->consumerofficertitle=='Other (enter value below)')
								$officertitle = $row->consumerotherofficertitle;
								else
									$officertitle = $row->consumerofficertitle;
								
								if($row->consumerofficertitle!='')
								{
									$position= ucfirst($officertitle);						
								}
							}
							$detail=$detail.'				
							<tr style="border-bottom: 1px solid;  text-align: center;">

							<td style=" border-right: 1px solid;"></td>

								<td style=" border-right: 1px solid;"></td>

								<td style=" border-right: 1px solid;"></td>

								<td style=" border-right: 1px solid;">'. $jointdt .'</td>

								<td style=" border-right: 1px solid;">'. $leavedt.'</td>

								<td style=" border-right: 1px solid;">'.$position.'</td>
							</tr> ';
						}
						$count++;
					}
				}
				}

	
				

			}			

			$detail	= $detail.'</tbody></table>';

			return $detail; 

		}

	}

	

	function getShareTransfer()
	{
		$member_add = array();
		$intial_cert = array();
		$detail	= '';
		$select="Select * , updatedDate as fieldValue FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 and consumersharecertno not in (select tbl_sharetransfer_data.cert_no_issued_to  from tbl_sharetransfer_data  WHERE  tbl_sharetransfer_data.cert_no_issued_to = tbl_consumeruser.consumersharecertno and tbl_sharetransfer_data.to_userid =tbl_consumeruser.consumeruser_id and tbl_sharetransfer_data.from_userid = 0) order by consumeruser_id";

		$select="Select * , updatedDate as fieldValue FROM tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id  ='".$this->consumer_id."'  and consumerisshareholder=1 and consumersharecertno not in (select tbl_sharetransfer_data.cert_no_issued_to from tbl_sharetransfer_data WHERE tbl_sharetransfer_data.to_userid =tbl_consumeruser.consumeruser_id and tbl_sharetransfer_data.from_userid >= 0) order by consumeruser_id";

		//edited above  on 20/05/2020 as not displaying record with new add memmber from treasury and record in tranferrec table
		//echo $select="Select *  , updatedDate as fieldValue FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 and consumersharecertno not in (select tbl_sharetransfer_data.cert_no_issued_to  from tbl_sharetransfer_data  WHERE  tbl_sharetransfer_data.cert_no_issued_to = tbl_consumeruser.consumersharecertno and tbl_sharetransfer_data.to_userid =tbl_consumeruser.consumeruser_id ) order by consumeruser_id";

		

		$query=mysqli_query($this->dbconnection,$select);

		$detail	= '

			<style> 

			th 

			{

				border-bottom: 1px solid;

			}

		

			</style>

			<table style="width: 100%; border: 3px double;">';

			$detail	= $detail.'

					<thead>

					<tr style="border-bottom: 1px solid;text-align: center;">

						<th style="border-right: 1px solid;">NO. OF TRANSFER</th>

                        <th style="border-right: 1px solid;">DATE</th>

                        <th style="border-right: 1px solid;">COMMON OR PREFERENCE</th>

                        <th style="border-right: 1px solid;" colspan="2">CERTIFICATE SURRENDERED NUMBER SHARE</th>

                        <th style="border-right: 1px solid;">NAME OF TRANSFEROR</th>

                        <th style="border-right: 1px solid;">NAME OF TRANSFEREE</th>

                        <th style="border-right: 1px solid;">ADDRESS</th>

                        <th style="border-right: 1px solid;"colspan="2">CERTIFICATE ISSUED</th>

                        <th style="border-right: 1px solid;">SIGNATURE OF TRANSFEROR OR ATTORNEY</th>

					</tr> </thead><tbody>';


		if(mysqli_num_rows($query)>0)

		{

			
			while($details=mysqli_fetch_object($query))
			{

				$incorp_date = $details->fieldValue;

				$displaydt = $this->checkDateofTransfer($details->consumeruser_id,$details->consumersharecertno);

				if (!empty($displaydt))
				{
					if (strtotime($displaydt) < strtotime($incorp_date))
					{
						$displaydt = $incorp_date; 
					}
				}
				else
				{
					$displaydt = $incorp_date;

				}
				//echo $displaydt;

				$detail=$detail.'<tr style="border-bottom: 1px solid;  text-align: center;">

					<td style=" border-top: 1px solid; border-right: 1px solid;"></td>

					<td style=" border-top: 1px solid; border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($displaydt)).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details->consumersharetype)." ".ucfirst($details->consumershareright).'<p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>Treasury</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</p></td>

					<td style="border-top: 1px solid; border-right: 1px solid;"><p>'.ucfirst($details->consumeraddress1)." "." ".ucfirst($details->consumercity)." ,".$this->getProvinceName($details->consumerstate_id)." ".ucfirst($details->consumerzipcode).'</p></td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details->consumersharecertno).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details->consumernoofshares).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"></td>

				</tr>';
					$member_add[] = $details->consumeruser_id;
					$consumeruser_id = $details->consumeruser_id;

					$certno_cancelled = $details->consumersharecertno;
					$intial_cert[] = $details->consumersharecertno;
					while(true)
					{
						$select="Select *   FROM  tbl_sharetransfer_data WHERE from_userid = '".$consumeruser_id."' and cert_no_cancelled = '".$certno_cancelled."'";

						$trans_query=mysqli_query($this->dbconnection,$select);

						if(mysqli_num_rows($trans_query)>0)
						{
							while($details_trans=mysqli_fetch_object($trans_query))
							{
								$detail.='<tr style="border-bottom: 1px solid;  text-align: center;">

								<td style=" border-right: 1px solid;">'.$details_trans->transfer_no.'</td>

								<td style=" border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($details_trans->date)).'</p></td>

								<td style=" border-right: 1px solid;"><p><p></td>

								<td style=" border-right: 1px solid;">'.$details_trans->cert_no_cancelled. '</td>

								<td style=" border-right: 1px solid;">'.$details_trans->no_of_shares.'</td>
								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->from_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->to_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p></p></td>
								<td style=" border-right: 1px solid;"><p><br>'.$details_trans->cert_no_issued_from.'</p>
								<td style=" border-right: 1px solid;"><p>-'.ucfirst($details_trans->no_of_shares).'<br>'.$details_trans->no_of_shares_from .'</p></td>

								<td style=" border-right: 1px solid;"></td>

							</tr>';		
							$certno_cancelled = $details_trans->cert_no_issued_from;
							$intial_cert[] = $details_trans->cert_no_issued_from;					
							}						
						}
						else
						{
							break;
						}
					
					}				

				}
			}

			//echo $select="Select *   FROM  tbl_sharetransfer_data WHERE from_userid >= 0 and to_userid in (select consumeruser_id  from tbl_consumeruser where consumer_id = '".$this->consumer_id."' ) order by date";

			$select= "Select a.*, b.consumeraddress1,b.consumercity, b.consumerstate_id,b.consumerzipcode  FROM  tbl_sharetransfer_data a,  tbl_consumeruser b WHERE from_userid >= 0 and to_userid = b.consumeruser_id and consumer_id = '".$this->consumer_id."' and consumersharecertno not in (select tbl_sharetransfer_data.cert_no_issued_to from tbl_sharetransfer_data WHERE tbl_sharetransfer_data.cert_no_issued_to = b.consumersharecertno and tbl_sharetransfer_data.to_userid =b.consumeruser_id and tbl_sharetransfer_data.from_userid = 0) order by date";

			//edited on 20/05/2020

			//echo $select= "Select a.*, b.consumeraddress1,b.consumercity, b.consumerstate_id,b.consumerzipcode  FROM  tbl_sharetransfer_data a,  tbl_consumeruser b WHERE from_userid >= 0 and to_userid = b.consumeruser_id and consumer_id = '".$this->consumer_id."' order by date";

			//start now

			$select= "Select a.*, b.consumeraddress1,b.consumercity, b.consumerstate_id,b.consumerzipcode, b.consumersharecertno, b.consumeruser_id  FROM  tbl_sharetransfer_data a,  tbl_consumeruser b WHERE from_userid >= 0 and to_userid = b.consumeruser_id and consumer_id = '".$this->consumer_id."'  order by date";

			$tt_query=mysqli_query($this->dbconnection,$select);

			if(mysqli_num_rows($tt_query)>0)
			{
				while($details_t=mysqli_fetch_object($tt_query))
				{
					$consumersharecertno = $details_t->consumersharecertno;
					//echo "<br>";
					//echo $details_t->cert_no_issued_to;
					//echo "<br>";
					//echo $details_t->to_userid;
					//echo "<br>";
					$consumeruser_id = $details_t->consumeruser_id;
					//echo "<br>";
					//echo $details_t->from_userid;
					//echo "<br>";
					//$details_t->cert_no_issued_to != $consumersharecertno and

					if ($details_t->from_userid >= 0 and !(in_array($details_t->cert_no_issued_to, $intial_cert)))
					{
					$intial_cert[] = $details_t->cert_no_issued_to;
					$detail.= '<tr style="border-bottom: 1px solid;  text-align: center;">

					<td  style=" border-top: 1px solid;border-right: 1px solid;">'.$details_t->transfer_no.'</td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($details_t->date)).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details_t->consumersharetype)." ".ucfirst($details_t->consumershareright).'<p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;">'.$details_t->cert_no_cancelled.'</td>

					<td style=" border-top: 1px solid;border-right: 1px solid;">';

					if ($details_t->from_userid > 0)
						$detail.=$details_t->no_of_shares;
					
					$detail.= '</td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$this->getmemberName($details_t->from_userid).'<border-top: 1px solid;/p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$this->getmemberName($details_t->to_userid).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>';

					if  (!in_array($details_t->to_userid, $member_add))
						$detail.=ucfirst($details_t->consumeraddress1)." "." ".ucfirst($details_t->consumercity)." ,".$this->getProvinceName($details_t->consumerstate_id)." ".ucfirst($details_t->consumerzipcode);

					$detail.= '</p></td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p><br>'.$details_t->cert_no_issued_to.'</p></td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$details_t->no_of_shares .'</p></td>
					<td style="border-top: 1px solid; border-right: 1px solid;"></td>

				</tr>';
					$consumeruser_id = $details_t->to_userid;

					$certno_cancelled = $details_t->cert_no_issued_to;
					while(true)
					{
						$select="Select *   FROM  tbl_sharetransfer_data WHERE from_userid = '".$consumeruser_id."' and cert_no_cancelled = '".$certno_cancelled."'";

						$trans_query=mysqli_query($this->dbconnection,$select);

						if(mysqli_num_rows($trans_query)>0)
						{
							while($details_trans=mysqli_fetch_object($trans_query))
							{
								$detail.='<tr style="border-bottom: 1px solid;  text-align: center;">

								<td style=" border-right: 1px solid;">'.$details_trans->transfer_no.'</td>

								<td style=" border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($details_trans->date)).'</p></td>

								<td style=" border-right: 1px solid;"><p><p></td>

								<td style=" border-right: 1px solid;">'.$details_trans->cert_no_cancelled. '</td>

								<td style=" border-right: 1px solid;">'.$details_trans->no_of_shares.'</td>
								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->from_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->to_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p></p></td>
								<td style=" border-right: 1px solid;"><p><br>'.$details_trans->cert_no_issued_from.'</p></td>
								<td style=" border-right: 1px solid;"><p>-'.ucfirst($details_trans->no_of_shares).'<br>'.$details_trans->no_of_shares_from .'</p></td>

								<td style=" border-right: 1px solid;"></td>

							</tr>';		
							$certno_cancelled = $details_trans->cert_no_issued_from;	
							$intial_cert[] = $details_trans->cert_no_issued_from;				
							}						
						}
						else
						{
							break;
						}
					
					}
				}

				if ($details_t->from_userid > 0 and !(in_array($details_t->cert_no_issued_from, $intial_cert)))
					{
						$intial_cert[] = $details_t->cert_no_issued_from;
					
					$detail.= '<tr style="border-bottom: 1px solid;  text-align: center;">

					<td  style=" border-top: 1px solid;border-right: 1px solid;">'.$details_t->transfer_no.'</td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($details_t->date)).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.ucfirst($details_t->consumersharetype)." ".ucfirst($details_t->consumershareright).'<p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;">'.$details_t->cert_no_cancelled.'</td>

					<td style=" border-top: 1px solid;border-right: 1px solid;">';

					if ($details_t->from_userid > 0)
						$detail.=$details_t->no_of_shares;
					
					$detail.= '</td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$this->getmemberName($details_t->from_userid).'<border-top: 1px solid;/p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$this->getmemberName($details_t->to_userid).'</p></td>

					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>';

					if  (!in_array($details_t->to_userid, $member_add))
						$detail.=ucfirst($details_t->consumeraddress1)." "." ".ucfirst($details_t->consumercity)." ,".$this->getProvinceName($details_t->consumerstate_id)." ".ucfirst($details_t->consumerzipcode);

					$detail.= '</p></td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p><br>'.$details_t->cert_no_issued_from.'</p></td>
					<td style=" border-top: 1px solid;border-right: 1px solid;"><p>'.$details_t->no_of_shares .'</p></td>
					<td style="border-top: 1px solid; border-right: 1px solid;"></td>

				</tr>';
					$consumeruser_id = $details_t->to_userid;

					$certno_cancelled = $details_t->cert_no_issued_to;
					while(true)
					{
						$select="Select *   FROM  tbl_sharetransfer_data WHERE from_userid = '".$consumeruser_id."' and cert_no_cancelled = '".$certno_cancelled."'";

						$trans_query=mysqli_query($this->dbconnection,$select);

						if(mysqli_num_rows($trans_query)>0)
						{
							while($details_trans=mysqli_fetch_object($trans_query))
							{
								$detail.='<tr style="border-bottom: 1px solid;  text-align: center;">

								<td style=" border-right: 1px solid;">'.$details_trans->transfer_no.'</td>

								<td style=" border-right: 1px solid;"><p>'.date("jS \of F Y",strtotime($details_trans->date)).'</p></td>

								<td style=" border-right: 1px solid;"><p><p></td>

								<td style=" border-right: 1px solid;">'.$details_trans->cert_no_cancelled. '</td>

								<td style=" border-right: 1px solid;">'.$details_trans->no_of_shares.'</td>
								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->from_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p>'.$this->getmemberName($details_trans->to_userid).'</p></td>

								<td style=" border-right: 1px solid;"><p></p></td>
								<td style=" border-right: 1px solid;"><p><br>'.$details_trans->cert_no_issued_from.'</p></td>
								<td style=" border-right: 1px solid;"><p>-'.ucfirst($details_trans->no_of_shares).'<br>'.$details_trans->no_of_shares_from .'</p></td>

								<td style=" border-right: 1px solid;"></td>

							</tr>';		
							$certno_cancelled = $details_trans->cert_no_issued_from;	
							$intial_cert[] = $details_trans->cert_no_issued_from;				
							}						
						}
						else
						{
							break;
						}
					
					}
				}


					
				}						
			}




			 // start earlier


			$select= "Select a.*, b.consumeraddress1,b.consumercity, b.consumerstate_id,b.consumerzipcode, b.consumersharecertno, b.consumeruser_id  FROM  tbl_sharetransfer_data a,  tbl_consumeruser b WHERE from_userid >= 0 and to_userid = b.consumeruser_id and consumer_id = '".$this->consumer_id."'  order by date";

			$tt_query=mysqli_query($this->dbconnection,$select);

			if(mysqli_num_rows($tt_query)>0)
			{
				while($details_t=mysqli_fetch_object($tt_query))
				{
					$consumersharecertno = $details_t->consumersharecertno;
					//echo "<br>";
					$details_t->cert_no_issued_to;
					//echo "<br>";
					$details_t->to_userid;
					//echo "<br>";
					$consumeruser_id = $details_t->consumeruser_id;
					//echo "<br>";
					$details_t->from_userid;
					//echo "<br>";
					//$details_t->cert_no_issued_to != $consumersharecertno and

					


					
				}						
			}
			$detail.='</tbody>';
			$detail	= $detail.'</table>';
			return $detail; 


	}

	function checkDateofTransfer($consumeruser_id,$consumersharecertno)
	{
		$select="Select *   FROM  tbl_sharetransfer_data WHERE to_userid ='".$consumeruser_id."' and cert_no_issued_to ='".$consumersharecertno."'";

			$t_query=mysqli_query($this->dbconnection,$select);

			if(mysqli_num_rows($t_query)>0)
			{
				while($details_t=mysqli_fetch_object($t_query))
				{
					return $details_t->date;
				}
			}
			else
			{
				return "";
			}

	}

	function getCompanyShareholders()

	{		
		$select="Select *  , DATE_FORMAT(updatedDate,'%D day of %M, %Y') fieldValue FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$query=mysqli_query($this->dbconnection,$select);

		if(mysqli_num_rows($query)>0)
		{
			

			$detail	= '

			<style> 

			th 

			{

				border-bottom: 1px solid;

			}

			td 

			{

				border-bottom: 1px solid;

			}

			</style>

			<table style="width: 100%; border: 3px double;">';

			$detail	= $detail.'

					<thead>

					<tr style="border-bottom: 1px solid;text-align: center;">

						<th style="border-right: 1px solid;">FULL NAME</th>

                        <th style="border-right: 1px solid;">ADDRESS</th>

                        <th style="border-right: 1px solid;">OCCUPATION</th>

                        <th style="border-right: 1px solid;">DATE WHEN ENTERED AS A SHAREHOLDER</th>

                        <th style="border-right: 1px solid;">DATE WHEN CEASED AS A SHAREHOLDER</th>

                        <th style="border-right: 1px solid;">REPRESENTATIVE CAPACITY (IF ANY)</th>

					</tr> </thead>';

			while($details=mysqli_fetch_object($query))

			{
				$noofShares = $details->consumernoofshares;
				$cert_no_alloted = $details->consumersharecertno;
				$incorpdate = $details->fieldValue;
				$incorpdt = $details->updatedDate;

				$firstdt =    '';
				$from_userid = '';
				$sqlQry="SELECT from_userid,date FROM `tbl_sharetransfer_data` where (to_userid='".$details->consumeruser_id."' and cert_no_issued_to = '".$cert_no_alloted."') order by date";

				//$sqlQry="SELECT date FROM `tbl_sharetransfer_data` where (to_userid='".$this->consumeruser_id."' and  from_userid ='' ) order by date";


					$res=mysqli_query($this->dbconnection,$sqlQry);
					if(mysqli_num_rows($res)>0)
					{
						while($row=mysqli_fetch_object($res))
						{
							$firstdt = $row->date;
							$from_userid = $row->from_userid;
							break;
						}
					}
				if (!empty($firstdt))
				{
					if (strtotime($firstdt) != strtotime($incorpdt))
					{
						$incorpdate =  date("jS \of F Y",strtotime($firstdt));
					}
				}


				$sharesorgbuy = $noofShares;
				$Sqlquery = "SELECT * FROM `tbl_sharetransfer_data` where to_userid='".$details->consumeruser_id."' and cert_no_issued_to = '".$cert_no_alloted."'";

				$res=mysqli_query($this->dbconnection,$Sqlquery);
				if(mysqli_num_rows($res)>0)
				{
					while($row=mysqli_fetch_object($res))
					{
						$sharesorgbuy = 0  ;

						break;
					}
				}
				//echo $sharesorgbuy;
				//echo "<br>";


				$sharessold = 0;
				$Sqlquery = "SELECT sum(no_of_shares) as sharessold FROM `tbl_sharetransfer_data` where from_userid='".$details->consumeruser_id."'";

				$res=mysqli_query($this->dbconnection,$Sqlquery);
				if(mysqli_num_rows($res)>0)
				{
					while($row=mysqli_fetch_object($res))
					{
						$sharessold = $row->sharessold;

						break;
					}
				}

				//echo $sharessold;
				//echo "<br>";
				$sharesbuy = 0;
				$Sqlquery = "SELECT sum(no_of_shares) as sharesbuy FROM `tbl_sharetransfer_data` where to_userid='".$details->consumeruser_id."'";

				$res=mysqli_query($this->dbconnection,$Sqlquery);
				if(mysqli_num_rows($res)>0)
				{
					while($row=mysqli_fetch_object($res))
					{
						$sharesbuy = $row->sharesbuy;
						break;
					}
				}

				//echo $sharesbuy;
				//echo "<br>";

				$ShresBalance = $sharesbuy+$sharesorgbuy-$sharessold;
				$lastdate = '';
				if ($ShresBalance <= 0)
				{
					$Sqlquery = "SELECT date FROM `tbl_sharetransfer_data` where (to_userid='".$details->consumeruser_id."' or from_userid='".$details->consumeruser_id."') order by date desc";
					$res=mysqli_query($this->dbconnection,$Sqlquery);
					if(mysqli_num_rows($res)>0)
					{
						while($row=mysqli_fetch_object($res))
						{
							$lastdate = date("jS \of F Y",strtotime($row->date));
							break;
						}
					}

				}





				if($details->consumerofficertitle=='Other (enter value below)')

					$officertitle = $details->consumerotherofficertitle;

				else

					$officertitle = $details->consumerofficertitle;



				$detail=$detail.'

				<tbody>

				<tr style="border-bottom: 1px solid;  text-align: center;">

					<td style=" border-right: 1px solid;">'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

					<td style=" border-right: 1px solid;"><p>'.ucfirst($details->consumeraddress1)." "." ".ucfirst($details->consumercity)." ,".$this->getProvinceName($details->consumerstate_id)." ".ucfirst($details->consumerzipcode).'</p></td>

					<td style=" border-right: 1px solid;"></td>

					<td style=" border-right: 1px solid;">'.$incorpdate.'</td>

					<td style=" border-right: 1px solid;">'.$lastdate.'</td>

					<td style=" border-right: 1px solid;">'.$officertitle.'</td>

				</tr> </tbody>';

				

			}			

			$detail	= $detail.'</table>';

			return $detail; 

		}

	}

	function getCompanyShareholdersAddress()

	{

		if($this->consumeruser_id!='')

		{	

			$select="Select consumeraddress1,consumerzipcode,consumerstate_id,consumercity FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

		}

		else

		{

			$select="Select consumeraddress1,consumerzipcode,consumerstate_id,consumercity FROM  tbl_consumermaster,tbl_consumeruser WHERE tbl_consumermaster.consumer_id= tbl_consumeruser. consumer_id and tbl_consumeruser.consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		}

		$query=mysqli_query($this->dbconnection,$select);

		$details=mysqli_fetch_object($query);

			return ucfirst($details->consumeraddress1)." ".ucfirst($details->consumercity)." ,".$this->getProvinceName($details->consumerstate_id)." ".ucfirst($details->consumerzipcode);

	}

	

	function getCompanyShareholderNameD()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."'";

		return $sqlQry; 

	}

	function getCompanyShareholderSignatureD()

	{
		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$directors = '';
		$Shareholdersignature = '';

		if ($row->signatureMode != 'D')
		{		
			$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."'";

			$res=mysqli_query($this->dbconnection,$sqlQry);

			$row=mysqli_fetch_object($res);

			$Shareholdersignature = '<br><br>'.'__________________________'.'<br>'.$row->fieldValue;
		}

		return $Shareholdersignature;

	}



	function getCompanyOfficerDetails_corp()

	{
		$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;




		$sqlQry="SELECT * FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisofficer=1 or  consumerisofficer=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='officer') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='officer' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";





		//$sqlQry="SELECT * FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$detail='';

		

		if(mysqli_num_rows($query)>0)

		{

			$detail	= '

			<table style="width: 100%;">';

			$detail	= $detail.'

					<thead>

					<tr style="text-align: center;">

						<th>NAME</th>

                        <th>TITLE</th>

                      </tr> </thead>';

			while($details=mysqli_fetch_object($query))

			{

				if($details->consumerofficertitle=='Other (enter value below)')

					$officertitle = $details->consumerotherofficertitle;

				else

					$officertitle = $details->consumerofficertitle;

					

				$detail=$detail.'

				<tbody>

				<tr style="text-align: center;">

					<td >'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

					<td >'.$officertitle.'</td>

				</tr> </tbody>';

				

			}			

			$detail	= $detail.'</table>';

			return $detail; 

		}

	}


	function getCompanyOfficerDetails()

	{

		$sqlQry="SELECT * FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$detail='';

		

		if(mysqli_num_rows($query)>0)

		{

			$detail	= '

			<table style="width: 100%;">';

			$detail	= $detail.'

					<thead>

					<tr style="text-align: center;">

						<th>NAME</th>

                        <th>TITLE</th>

                      </tr> </thead>';

			while($details=mysqli_fetch_object($query))

			{

				if($details->consumerofficertitle=='Other (enter value below)')

					$officertitle = $details->consumerotherofficertitle;

				else

					$officertitle = $details->consumerofficertitle;

					

				$detail=$detail.'

				<tbody>

				<tr style="text-align: center;">

					<td >'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

					<td >'.$officertitle.'</td>

				</tr> </tbody>';

				

			}			

			$detail	= $detail.'</table>';

			return $detail; 

		}

	}

	function getCompanyShareholderDetails()

	{

		$sqlQry="SELECT * FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$detail='';

		if(mysqli_num_rows($query)>0)

		{

			$detail	= '

			<table style="width: 100%;">';

			$detail	= $detail.'

					<thead>

					<tr style="text-align: center;">

						<th>NAME</th>

                        <th>SHARES AND CLASS</th>

                        <th>CERT.#</th>

                      </tr> </thead>';

			while($details=mysqli_fetch_object($query))

			{

				$detail=$detail.'

				<tbody>

				<tr style="text-align: center;">

					<td >'.ucfirst($details->consumerfname)." ".ucfirst($details->consumermname)." ".ucfirst($details->consumerlname).'</td>

					<td >'.$details->consumernoofshares." ".'"'.$details->consumershareclass.'"'." ".$details->consumersharetype." ".$details->consumershareright.'</td>

					<td >'.$details->consumersharecertno .'</td>

				</tr> </tbody>';

				

			}			

			$detail	= $detail.'</table>';

			return $detail; 

		}

	}

	function getCompanyShareholderDetails_corp()

	{

		$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;



		$sqlQry="SELECT  *  FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 ";




		$res=mysqli_query($this->dbconnection,$sqlQry);
		$detail = '';

		if(mysqli_num_rows($res)>0)
		{

			//echo $sqlQry;
			$detail	= '

			<table style="width: 100%;">';

			$detail	= $detail.'

				<thead>

				<tr style="text-align: center;">

					<th>NAME</th>

	                <th>SHARES AND CLASS</th>

	                <th>CERT.#</th>

	                </tr> </thead>';

			while($row=mysqli_fetch_object($res))

			{
				$flag =0;
				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE cert_no_issued_to  = '".$row->consumersharecertno."' and to_userid ='".$row->consumeruser_id."'";

				$res1=mysqli_query($this->dbconnection,$sqlQry);

				if (mysqli_num_rows($res1) > 0)
				{
					while($row1=mysqli_fetch_object($res1))
					{
						if ($row1->date == $incorpdate)
							$flag =1;
					}
				}
				else
				{
					$flag =1;
				}			
				if($flag == 1)

				{
					$detail=$detail.'

					<tbody>

					<tr style="text-align: center;">

						<td >'.ucfirst($row->consumerfname)." ".ucfirst($row->consumermname)." ".ucfirst($row->consumerlname).'</td>

						<td >'.$row->consumernoofshares." ".'"'.$row->consumershareclass.'"'." ".$row->consumersharetype." ".$row->consumershareright.'</td>

						<td >'.$row->consumersharecertno .'</td>

					</tr> </tbody>';
				}

					

			}			

			$detail	= $detail.'</table>';


		}
		return $detail; 

	}



	function getDirectorNamecomma_incorp()
	{
			$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;




		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$directors="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($directors=='')

					$directors = $row->fieldValue;

				else

					$directors = $directors .",". $row->fieldValue;

			}

		}
		//echo $directors;

		return $directors;


	}

	function getDirectorNamecomma()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$directors="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($directors=='')

					$directors = $row->fieldValue;

				else

					$directors = $directors .",". $row->fieldValue;

			}

		}

		return $directors;

	}


	

	function getDirectorSignature()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$directors="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($directors=='')

					$directors = '__________________________'.'<br>'.$row->fieldValue;

				else

					$directors = $directors .'<br>'.'<br>'.'__________________________'.'<br>'. $row->fieldValue;

			}

		}

		return $directors;

	}


	function getDirectorSignature_corp()
	{

		//$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		//$res=mysqli_query($this->dbconnection,$sqlQry);


		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$directors = '';
		//global $errormsgRecordbook;

		
		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if (mysqli_num_rows($res) == 0)
		{
			$errormsgRecordbook = 'No Director is available to sign';
		}

		if ($row->signatureMode != 'D')
		{

			$directors="";

			//echo $sqlQry;

			while($row=mysqli_fetch_object($res))

			{

				if(isset($row->fieldValue))

				{
					$directors = $directors .'<div class="" style="float: left;width: 48%;margin: 0 1% ;margin-top: 112px;box-sizing: border-box; border-top: 1px solid;">'.$row->fieldValue.'</div>';

				}

			}
		}

		return $directors;

	}

	function getDirectorSignature_corp_shrcert()
	{

		//$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		//$res=mysqli_query($this->dbconnection,$sqlQry);


		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;

		$html = '';

		$html .= '<div style="float:left;width:40%;margin:12% 0 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0"></p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%"></p>

						</div>';
		$html .= '<div style="float:right;width:40%;margin:12% 5% 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0"></p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%"></p>

						</div>';

		if ($row->signatureMode != 'D')
		{

			$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";

			$res=mysqli_query($this->dbconnection,$sqlQry);

			$directors="";

			//echo $sqlQry;
			$html = '';
			$count = 1;

			while($row=mysqli_fetch_object($res))

			{

				if(isset($row->fieldValue) && $count <= 2)

				{
					if ($count == 1)
					{
						$html .= '<div style="float:left;width:40%;margin:12% 0 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0">_______________________</p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%">'.$row->fieldValue.'</p>

						</div>';
					}
					else
					{
						$html .= '<div style="float:right;width:40%;margin:12% 5% 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0">_____________________</p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%">'.$row->fieldValue.'</p>

						</div>';
					}
					
						$count++;			

				}

			}	
		}			

		return $html;

	}


	function getDirectorSignature_corp_shrcert_new()
	{
		//echo $this->getShareholder_transdate(1);
		$cert_date = date('Y-m-d', strtotime($this->getShareholder_transdate(1)));

		//$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisdirector=1";

		//$res=mysqli_query($this->dbconnection,$sqlQry);


		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$signatureMode = $row->signatureMode;

		$html = '';
		global $errormsgRecordbook;

		
	

		$sqlQry="SELECT  	consumeruser_id , concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisdirector=1 or consumerisdirector=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='director') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='director' and ((member_doj <= '".$cert_date."' and (member_dol is NULL or  member_dol >= '".$cert_date."')) or (member_doj IS NULL and member_dol >= '".$cert_date."')))) and share_signee =1";
		

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if (mysqli_num_rows($res) == 0)
		{
			$errormsgRecordbook = 'No Director is available to sign share certificate';
		}

		$directors="";

		//echo $sqlQry;
		$html = '';
		$count = 1;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue) && $count <= 2)
			{
				if ($signatureMode != 'D')
				{
					if ($count == 1)
					{
						$html .= '<div style="float:left;width:40%;margin:12% 0 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0">_______________________</p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%">'.$row->fieldValue.'</p>

						</div>';
					}
					else
					{
						$html .= '<div style="float:right;width:40%;margin:12% 5% 0 0;" >

							<p style="font-style: italic;float:left;margin: 4% 0 0 0">_____________________</p>

							<p style="font-style: italic;float:left;margin: 0 0 0 10%">'.$row->fieldValue.'</p>

						</div>';
					}
				}
				if (empty($this->UsersTobeSign))
				{
					$this->UsersTobeSign = $row->consumeruser_id;
				}
				else
				{
					$this->UsersTobeSign = $this->UsersTobeSign.",".$row->consumeruser_id;
				}

				//echo $this->UsersTobeSign;  
				
					$count++;			

			}

		}
		if ($signatureMode == 'D')
		{
			$html .= '<div style="float:left;width:40%;margin:12% 0 0 0;" >

								<p style="font-style: italic;float:left;margin: 4% 0 0 0"> </p>

								<p style="font-style: italic;float:left;margin: 0 0 0 10%"> </p>

							</div>';
			$html .= '<div style="float:right;width:40%;margin:12% 5% 0 0;" >

								<p style="font-style: italic;float:left;margin: 4% 0 0 0"> </p>

								<p style="font-style: italic;float:left;margin: 0 0 0 10%"> </p>

							</div>';
		}					
		return $html;

	}


	


	function getOfficerName()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$officers="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($officers=='')

					$officers = $row->fieldValue;

				else

					$officers = $officers .'<br>'.'<br>'. $row->fieldValue;

			}

		}

		return $officers;

	}

	

	function getOfficerTitle()

	{

		$sqlQry="SELECT UCASE(consumerofficertitle) fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$officertitle="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($row->fieldValue=='Other (enter value below)')

				{

					if($officertitle=='')

						$officertitle = $row->consumerotherofficertitle;

					else

						$officertitle = $officertitle .'<br>'.'<br>'. $row->consumerotherofficertitle;

				}

				else

				{

					if($officertitle=='')

						$officertitle = $row->fieldValue;

					else

						$officertitle = $officertitle .'<br>'.'<br>'. $row->fieldValue;

				}

			}

		}

		return $officertitle;

	}

	function getOfficerSignature()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$officersignature="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($officersignature=='')

					$officersignature = '__________________________'.'<br>'.$row->fieldValue;

				else

					$officersignature = $officersignature .'<br>'.'<br>'.'__________________________'.'<br>'. $row->fieldValue;

			}

		}

		return $officersignature;

	}

	function getOfficerSignature_corp()

	{

		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$officersignature="";

		//global $errormsgRecordbook;

		
		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname)) fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and (consumerisofficer=1 or  consumerisofficer=2) and  (consumeruser_id not in (select consumeruser_id  from tbl_members_servicerec where member_designation ='officer') or consumeruser_id in (select consumeruser_id  from tbl_members_servicerec where consumeruser_id = tbl_consumeruser.consumeruser_id and member_designation ='officer' and (member_doj = '".$incorpdate."' or (member_doj IS NULL and member_dol != ''))))";


		//$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisofficer=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		if (mysqli_num_rows($res) == 0)
		{
			$errormsgRecordbook = 'No officer is available to sign';
		}

		if ($row->signatureMode != 'D')
		{


			$officersignature="";

			//echo $sqlQry;
			$count = 1;

			while($row=mysqli_fetch_object($res))

			{

				if(isset($row->fieldValue))

				{

					if($officersignature=='')
					{
						$officersignature = '<div class="" style="float: left;width: 53%;margin: 0 1% ;margin-top: 110px;box-sizing: border-box; ">'.$row->fieldValue.'</div>';
					}
					else
					{
						 
						if (fmod($count,2) == 0)
						{
							$officersignature .= '<div class="" style="float: left;width: 43%;margin: 0 1% ;margin-top: 110px;box-sizing: border-box; ">'.$row->fieldValue.'</div>';
						}
						else
						{
							$officersignature .= '<div class="" style="float: left;width: 53%;margin: 0 1% ;margin-top: 110px;box-sizing: border-box; ">'.$row->fieldValue.'</div>';
						}										
					}
					$count++;
				}

			}
		}
		return $officersignature;

	}

	function getShareholderName()

	{

		$sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholder="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($shareholder=='')

					$shareholder = $row->fieldValue;

				else

					$shareholder = $shareholder .'<br>'.'<br>'. $row->fieldValue;

			}

		}

		return $shareholder;

	}

	function getShareholderfirst_date()
	{
		$sqlQry="select updatedDate from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);


		$row=mysqli_fetch_object($res);
		$incorpdate = 	$row->updatedDate;
		 

		$sqlQry="select consumersharecertno from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);
		$row=mysqli_fetch_object($res);
		$cert_no 	=	$row->consumersharecertno;	 

		$sqlQry="SELECT date FROM tbl_sharetransfer_data WHERE cert_no_issued_to  = '".$cert_no."' and to_userid ='".$this->consumeruser_id."' and from_userid = 0";

		$res1=mysqli_query($this->dbconnection,$sqlQry);
		if (mysqli_num_rows($res1) > 0)
		{
			$row1=mysqli_fetch_object($res1);
			$incorpdate = $row1->date;
		}
		//echo $incorpdate;
		return $incorpdate;
	}

	function getShareholderSignature()

	{

		//global $errormsgRecordbook;

		$sqlQry="select updatedDate, signatureMode from tbl_consumermaster where consumer_id='".$this->consumer_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$row=mysqli_fetch_object($res);
		$incorpdate = $row->updatedDate;
		$shareholdersignature="";
		
		//echo $sqlQry="SELECT concat(UCASE(consumerfname),' ',UCASE(consumermname), ' ' ,UCASE(consumerlname))  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 and consumershareright='Voting' and (consumeruser_id not in (select to_userid from tbl_sharetransfer_data where from_userid = 0) or consumeruser_id in (select to_uerid  from tbl_sharetransfer_data where from_userid = 0 and to_userid = tbl_consumeruser.to_userid and date = '".$incorpdate."'))";

		$sqlQry="SELECT consumeruser_id, consumersharecertno, concat(UCASE(TRIM(consumerfname)),' ',UCASE(TRIM(consumermname)), ' ' ,UCASE(TRIM(consumerlname))) fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1 and consumershareright='Voting' ";


		$res=mysqli_query($this->dbconnection,$sqlQry);

		if (mysqli_num_rows($res) == 0)
		{
			$errormsgRecordbook = 'No Share holder with voting right is available to sign';
		}

		if ($row->signatureMode != 'D')
		{

			$shareholdersignature="";

			//echo $sqlQry;

			while($row=mysqli_fetch_object($res))

			{
				$flag =0;
				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE cert_no_issued_to  = '".$row->consumersharecertno."' and to_userid ='".$row->consumeruser_id."'";

				$res1=mysqli_query($this->dbconnection,$sqlQry);

				if (mysqli_num_rows($res1) > 0)
				{
					while($row1=mysqli_fetch_object($res1))
					{
						if ($row1->date == $incorpdate)
							$flag =1;
					}
				}
				else
				{
					$flag =1;
				}


				if(isset($row->fieldValue) && $flag == 1)
				{
					$shareholdersignature = $shareholdersignature.'<div class="" style="float: left;width: 40%;margin: 0 1% ;margin-top: 100px;box-sizing: border-box; border-top: 1px solid;">'.$row->fieldValue.'</div>';

				}

			}
		}
		//echo $shareholdersignature;

		return $shareholdersignature;

	}

	function getShareholderDetails()

	{

		if($this->consumeruser_id!='')

		{	

			$sqlQry="SELECT concat( consumernoofshares, ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

		}

		else

		{

			$sqlQry="SELECT concat( consumernoofshares, ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		}

	

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholderdetails="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($shareholderdetails=='')

					$shareholderdetails = $row->fieldValue;

				else

					$shareholderdetails = $shareholderdetails .'<br>'.'<br>'. $row->fieldValue;

			}

		}

		return $shareholderdetails;

	}

	

	function getShareholderDetailsType()

	{

		if($this->consumeruser_id!='')

		{	

			$sqlQry="SELECT concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

		}

		else

		{

			$sqlQry="SELECT concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		}

	

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholderdetails="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($shareholderdetails=='')

					$shareholderdetails = $row->fieldValue;

				else

					$shareholderdetails = $shareholderdetails .'<br>'.'<br>'. $row->fieldValue;

			}

		}

	

		return $shareholderdetails;

	}

	function getShareholderDetailsType_transfer()
	{
		$sqlQry = '';
		$value = '';
		if ($this->cancelled == '')
		{
			if ($this->transfer_id == '0')
			{
				if($this->consumeruser_id!='')
				{	

					$sqlQry="SELECT concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

				}
			}
			else
			{
				$sqlQry="SELECT concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

				

			}
			//echo $sqlQry;
			if (!empty($sqlQry))
			{
				$res=mysqli_query($this->dbconnection,$sqlQry);
				while($row=mysqli_fetch_object($res))
				{
					$value = $row->fieldValue;
				}

			}
			

		}
		else
		{
			$value =  $this->getcancelled_info('4');


		}
		return $value;
				
	}

	function getShareholdercertificateno_trans($var='')
	{
		$cert_no = '';
		$no 	=	'';

		if ($this->cancelled == '')
		{
			if ($this->transfer_id == '0')
			{
				$sqlQry="SELECT consumersharecertno,consumernoofshares   FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

				$res_cert=mysqli_query($this->dbconnection,$sqlQry);
				while($row_cert=mysqli_fetch_object($res_cert))
				{
					if (empty($var))
					{
						return $row_cert->consumersharecertno;
					}
					else
					{
						return $row_cert->consumernoofshares;
					}

				}
			}
			else
			{
				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

				$res_cert=mysqli_query($this->dbconnection,$sqlQry);
				while($row_cert=mysqli_fetch_object($res_cert))
				{
					if ($this->consumeruser_id == $row_cert->from_userid)
					{
						$cert_no = $row_cert->cert_no_issued_from; 
						$no = $row_cert->no_of_shares_from ;

					}
					else
					{
						$cert_no = $row_cert->cert_no_issued_to; 
						$no = $row_cert->no_of_shares ;

					}
				}
				//echo $cert_no;
				if (empty($var))
				{
					return $cert_no;
				}
				else
				{
					return $no;
				}
			}

		}
		else
		{
			if (empty($var))
			{
				return $this->getcancelled_info('2');
			}
			else
			{
				return $this->getcancelled_info('3');
			}

	
			// $sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

			// 	$res_cert=mysqli_query($this->dbconnection,$sqlQry);
			// 	while($row_cert=mysqli_fetch_object($res_cert))
			// 	{
			// 		if ($this->consumeruser_id == $row_cert->from_userid)
			// 		{
			// 			$cert_no = $row_cert->cert_no_cancelled ; 
			// 			if (!empty($var))
			// 			{
			// 				echo $sqlQry="SELECT consumersharecertno,consumernoofshares   FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumersharecertno = '".$cert_no."'";

			// 				$res1=mysqli_query($this->dbconnection,$sqlQry);
			// 				if (mysqli_num_rows($res1) > 0)
			// 				{
			// 					while($row_1=mysqli_fetch_object($res1))
			// 					{
			// 						$no = $row_1->consumernoofshares;

			// 					}
			// 				}
			// 				else
			// 				{
			// 					echo $sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE (cert_no_issued_from = '".$cert_no."' and from_userid ='".$this->consumeruser_id."') or (cert_no_issued_to = '".$cert_no."' and to_userid ='".$this->consumeruser_id."') ";

			// 					$res_cert_can=mysqli_query($this->dbconnection,$sqlQry);
			// 					while($row_cert_can=mysqli_fetch_object($res_cert_can))
			// 					{
			// 						if ($row_cert_can->from_userid== $this->consumeruser_id)
			// 						{
			// 							$no = $row_cert_can->no_of_shares_from;

			// 						}
			// 						else
			// 						{
			// 							$no = $row_cert_can->no_of_shares_to;
			// 						}

			// 					}

			// 				}							
			// 				echo $no;

			// 			}						
			// 		}
			// 		else
			// 		{
			// 			//$cert_no = $row_cert->cert_no_issued_to; 
			// 			//$no = $row_cert->no_of_shares ;

			// 		}
			// 	}
				//echo $cert_no;
				
		}
		
	}

	function getcancelled_info($field_type)
	{
		$tran_date = '';
		$class = '';
		$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

		$res_cert=mysqli_query($this->dbconnection,$sqlQry);
		while($row_cert=mysqli_fetch_object($res_cert))
		{
			if ($this->consumeruser_id == $row_cert->from_userid)
			{
				$cert_no = $row_cert->cert_no_cancelled ; 
				if ($field_type == 3 || $field_type == 4)
				{
					$sqlQry="SELECT consumersharecertno,consumernoofshares,concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue   FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumersharecertno = '".$cert_no."'";

					$res1=mysqli_query($this->dbconnection,$sqlQry);
					if (mysqli_num_rows($res1) > 0)
					{
						while($row_1=mysqli_fetch_object($res1))
						{
							$no = $row_1->consumernoofshares;
							$class = $row_1->fieldValue;							
						}
					}
					else
					{
						$sqlQry="SELECT *,concat( ' \" ', consumershareclass, ' \" ', consumersharetype, ' ', consumershareright )  fieldValue FROM tbl_sharetransfer_data WHERE (cert_no_issued_from = '".$cert_no."' and from_userid ='".$this->consumeruser_id."') or (cert_no_issued_to = '".$cert_no."' and to_userid ='".$this->consumeruser_id."') ";

						$res_cert_can=mysqli_query($this->dbconnection,$sqlQry);
						while($row_cert_can=mysqli_fetch_object($res_cert_can))
						{
							$class = $row_cert_can->fieldValue;
							if ($row_cert_can->from_userid== $this->consumeruser_id)
							{
								$no = $row_cert_can->no_of_shares_from;

							}
							else
							{
								$no = $row_cert_can->no_of_shares_to;
							}

						}

					}							
					//echo $no;

				}						
			}
			else
			{
				//$cert_no = $row_cert->cert_no_issued_to; 
				//$no = $row_cert->no_of_shares ;

			}
		}
		//echo $cert_no;
		switch ($field_type) {
		 case '1':
	        return $tran_date;
	        break;
	    case '2':
	        return $cert_no;
	        break;
	    case '3':
	        return $no;
	        break;
	    case '4':
	        return $class;
	        break;
	    }

	}
	function getShareholdercertificate_status()
	{
		$cert_status = '';
		if ($this->transfer_id == '0' || $this->cancelled == '')
		{
			
			return '';
		}
		else
		{
			$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

			$res_cert=mysqli_query($this->dbconnection,$sqlQry);
			while($row_cert=mysqli_fetch_object($res_cert))
			{
				if ($this->consumeruser_id == $row_cert->from_userid)
				{
					$cert_status = 'Cancelled'; 

				}
				else
				{
					$cert_status = '';

				}
			}

			return $cert_status;

		}		

	}

	function getShareholder_transdate($var = 0)
	{
		$date = '';
		// echo '<br>';
		// echo 'certdate';
		// echo '<br>';
		$sqlQry="select updatedDate fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";
		$res=mysqli_query($this->dbconnection,$sqlQry);
		while($row_cert=mysqli_fetch_object($res))
		{
			$date = $row_cert->fieldValue;
		}
		//echo $this->transfer_id;

		if ($this->transfer_id != '0')
		{
			if ($this->cancelled == '')
			{
				$date = '';
				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

				$res_cert=mysqli_query($this->dbconnection,$sqlQry);
				while($row_cert=mysqli_fetch_object($res_cert))
				{
					if ($this->consumeruser_id == $row_cert->from_userid)
					{
						$date = $row_cert->date; 
					}
					else
					{
						$date = $row_cert->date; 

					}
				}
			}
			else
			{
				// $rdate = $this->getcancelled_info('1')
				// if (!empty($rdate))
				// 	$date = $rdate;

				$rdate= '';
				$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE transfer_id = '".$this->transfer_id."'";

				$res_cert=mysqli_query($this->dbconnection,$sqlQry);
				while($row_cert=mysqli_fetch_object($res_cert))
				{
					if ($this->consumeruser_id == $row_cert->from_userid)
					{
						$cert_no = $row_cert->cert_no_cancelled ; 
			
						$sqlQry="SELECT * FROM tbl_sharetransfer_data WHERE (cert_no_issued_from = '".$cert_no."' and from_userid ='".$this->consumeruser_id."') or (cert_no_issued_to = '".$cert_no."' and to_userid ='".$this->consumeruser_id."') ";

						$res_cert_can=mysqli_query($this->dbconnection,$sqlQry);
						while($row_cert_can=mysqli_fetch_object($res_cert_can))
						{						
							$rdate = $row_cert_can->date;

						}						
						//echo $rdate;											
					}
					else
					{
						//$cert_no = $row_cert->cert_no_issued_to; 
						//$no = $row_cert->no_of_shares ;

					}
				}
				if (!empty($rdate))
					$date = $rdate;
			}
		}
		if ($var == 0)
			return gmdate("jS \of F, Y",strtotime($date));
		else
			return gmdate("Y-m-d",strtotime($date));
		
	}





	function getShareholdercertificateno()

	{

		if($this->consumeruser_id!='')

			$sqlQry="SELECT consumersharecertno  fieldValue FROM tbl_consumeruser WHERE consumeruser_id ='".$this->consumeruser_id."' and consumerisshareholder=1";

		else

			$sqlQry="SELECT consumersharecertno  fieldValue FROM tbl_consumeruser WHERE consumer_id ='".$this->consumer_id."' and consumerisshareholder=1";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		$shareholdercertificateno="";

		//echo $sqlQry;

		while($row=mysqli_fetch_object($res))

		{

			if(isset($row->fieldValue))

			{

				if($shareholdercertificateno=='')

					$shareholdercertificateno = $row->fieldValue;

				else

					$shareholdercertificateno = $shareholdercertificateno .'<br>'.'<br>'. $row->fieldValue;

			}

		}

		return $shareholdercertificateno;

	}

	function getPricePerShare()

	{
		$number = '';
		//echo 'id';
		//echo $this->consumeruser_id;
		//echo 'id';
		if($this->consumeruser_id!='')
		{
			//echo "select consumerpricepershare fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'  and consumerisshareholder=1";

			$sqlQry=mysqli_query($this->dbconnection,"select consumerpricepershare fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'  and consumerisshareholder=1");
		}

		else
		{

			$sqlQry=mysqli_query($this->dbconnection,"select consumerpricepershare fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'  and consumerisshareholder=1");

		}

		$fetch=mysqli_fetch_object($sqlQry);

		//print_r($fetch);
		if (mysqli_num_rows($sqlQry) > 0)
		{
			//echo $fetch->fieldValue;

			$number = $fetch->fieldValue; // sprintf('%.9f', $fetch->fieldValue);

		}

			
		//die;

		return '$'.$number;

	}

	function gettotalPricePerShare()

	{

		if($this->consumeruser_id!='')

			$sqlQry=mysqli_query($this->dbconnection,"select consumertotalshare fieldValue from tbl_consumeruser where consumeruser_id='".$this->consumeruser_id."'");

		else

			$sqlQry=mysqli_query($this->dbconnection,"select consumertotalshare fieldValue from tbl_consumeruser where consumer_id='".$this->consumer_id."'");

		$fetch=mysqli_fetch_object($sqlQry);

		$number = sprintf('%.2f', $fetch->fieldValue);

		return '$'.$number;

	}

	function getTemplate()

	{

		$sqlQry="select * from tbl_template";

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function getStateName()

	{

		$slQry="select * from tbl_state ORDER BY `tbl_state`.`country` ASC";

		return mysqli_query($this->dbconnection,$slQry);

	}

	

	function getFolderName()

	{

		$sqlQry='select *  , tbl_sys_folder.sequence AS fsqu from tbl_sys_folder,enum_consumerfilestatus where parent_id!=0 and tbl_sys_folder.consumerfilestatus_id=enum_consumerfilestatus.consumerfilestatus_id';

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function updateSysFolderInfo()

	{

	

		$sqlQry='update  tbl_template set 

		sys_folder_id="'.$this->sys_folder_id.'", 

		is_template_for_specific_state ="'.$this->isSpecific.'"

		where template_id="'.$this->template_id.'"';

		

		$query=mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_template';

		$objUtility->datatableidField ='template_id';

		$objUtility->action='Updated';

		$objUtility->dataId=$this->template_id;

		$objUtility->user_id=$this->user_id;

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->description='Updated Template  details with Template Id:['.$this->template_id.'] ';

		$objUtility->logTrack();

			//return $this->lastInsertedId;

	}

	function isSpecificFound()

	{

		$sqlQry="select * from tbl_template_for_specific_state where template_id='".$this->template_id."' and state_id='".$this->state_id."'";

		$res=mysqli_query($this->dbconnection,$sqlQry);

		return mysqli_num_rows($res);

	}

	

	function addSpecificState()

	{

	

		$sqlQry='insert into  tbl_template_for_specific_state set 

		template_id="'.$this->template_id.'", 

		state_id ="'.$this->state_id.'"';
		
		//$query=mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_template';

		$objUtility->datatableidField ='template_id';

		$objUtility->action='Added';

		$objUtility->dataId=$this->template_id;

		$objUtility->user_id=$this->user_id;

		$objUtility->usertype=$_SESSION['usertype'];

		$objUtility->description='Added Template for specific state with Template Id:['.$this->template_id.'] and State Id';

		//$objUtility->logTrack();

		//return $this->lastInsertedId;

	}

	

	function updateSequence()

	{

		$sqlQry = "update tbl_sys_folder SET sequence='".$this->sequence."' WHERE sys_folder_id = '".$this->sys_folder_id."'";

		mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_sys_folder';

		$objUtility->dataId=$this->sys_folder_id;

		$objUtility->description='Order Changed for folder';

		$objUtility->logTrack();

	}

	function getDirectorMinutes()
	{
		$fValue = '';
		
		if ($this->state_id == 61)
		{
			$html = '<p class="body_heading">ARTICLES</p>
                <p class="body_txt2">2.<span>The Articles signed by the incorporating shareholder(s) are hereby approved and adopted.</p>';					}
		else
		{
			$sqlQry="select companyname fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";
			$res=mysqli_query($this->dbconnection,$sqlQry);

			while($row=mysqli_fetch_object($res))

			{

				$fValue = rtrim($row->fieldValue,',');

			}
			$html = '<p class="body_heading">BY-LAWS</p>

                <p class="body_txt2">2.<span>By-law No. 1, being a By-law relating generally to the transaction of the business and affairs of
                        <span class="body_sloat">'.$fValue.'</span> is hereby made as a By-law of the Corporation.</span></p>';
		}
		return $html;

		
	}

	function getContentforNotBC()
	{
		$fValue = '';
		
		if ($this->state_id == 61)
		{
			$html = '';					
		}
		else
		{
			$sqlQry="select companyname fieldValue from tbl_consumermaster where consumer_id='".$this->consumer_id."'";
			$res=mysqli_query($this->dbconnection,$sqlQry);

			while($row=mysqli_fetch_object($res))

			{

				$fValue = rtrim($row->fieldValue,',');

			}
			$html = '<p style="margin:5px 0 0 0;font-weight:bold; text-decoration:underline">CONFIRMATION OF BY-LAWS</p>

        			<p><span style="font-weight:bold"> BE IT RESOLVED</span> that By-Law No. 1,being a By-Law relating generally to the transaction of the Business and affairs

            		of <span>'.$fValue.'</span>, is hereby confirmed as a By-Law of the corporation.</p>';
		}
		return $html;
	}

	



}

?>