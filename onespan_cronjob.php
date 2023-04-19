<?php
include_once("private/settings.php");
include_once("classes/clsFile.php");
include_once("classes/User.php");
include_once(PATH."classes/clsConsumer.php");

include_once(PATH."classes/OneSpan.php");
include_once(PATH."classes/Utility.php");

											
//mail("netz.bimal@gmail.com","Mycotogo  cron ojob start sign",'Cron job  sign');


$fileObj= new File();
$userobj= new User();



$res=$fileObj->ShowPendingSign();



$userPhone	= array();

$textsms	=	array();

// $mysqli_obj = new DataBase();
// $dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);



if(mysqli_num_rows($res)>0)
{
	while($record	=	mysqli_fetch_object($res))
	{
		//print_r($record);
		//print_r($record->document_id);
		$to = $record->useremail;
		//$to = "netz.bimal@gmail.com";
		echo $consumer_file = $record->consumer_fileno;
		$filename = $record->name;
		$signed_by = $record->usermail;
		//print_r($record->name);
		echo "<br>";
		$link = "https://app.hellosign.com/attachment/downloadCopy/guid/".$record->oneSpanSignId;
		$e_content = '
				<table>
					<tr>
						<td colspan="2"><b>Information</b></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">'.$signed_by.' has signed the document '.$filename.' relatated to file '.$consumer_file.' </td>
					</tr>
					<tr>
						<td><br/><b>MYCOTOGO</b></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>';

		

		$sign_id = $record->oneSpanSignId; 
		$consumer_id =  $record->consumer_id;
		$client = new OneSpan(); 
		echo "<br>";
		echo $client->PackageId = $sign_id;
		echo $sign_status = $client->getSignatureStatus();
		
		$email=ADMIN_EMAIL_FROM;
		$name = 'Admin';
		echo 'to: '.$to;

		$usersingn_status =  $client->CheckSignaturesCompleted();
		print_r($usersingn_status);

		if (count($usersingn_status) > 0)
		{
			$objConsumer = new Consumer();
			$objConsumer->consumer_id = $consumer_id;
			$row = $objConsumer->selectConsumer();
			$consumerDetail	= mysqli_fetch_object($row);
			$comp_name = $consumerDetail->usercname;

			for($i=0;$i < count($usersingn_status);$i++)
			{
				if (strpos($record->users_signed , $usersingn_status[$i]) == false)
				{

					echo 'send mail'.$usersingn_status[$i];
					$fileObj->user_email = $usersingn_status[$i];
					$fileObj->oneSpan_id = $sign_id;	
					echo $e_subject = $usersingn_status[$i]." has completed signing for ".$comp_name;
					echo $e_content = "
							<table>
								<tr>
									<td>".$usersingn_status[$i]." has successfully signed the documents for ".$comp_name."</td>
								</tr>					
							</table>";
					SendMail($to, $email, $name  , $e_subject,$e_content,"");	
					$fileObj->updateUserEmail_Status();
					$objUtility = new Utility();
					$objUtility->dataTable = 'tbl_document';
					$objUtility->datatableidField ='document_id';
					$objUtility->usertype= 0;
					$objUtility->action='Signature completed email to paralegal '.$to ;
					$objUtility->dataId=$record->document_id;
					$objUtility->user_id=0;
					$objUtility->description=addslashes($usersingn_status[$i]." has completed signing for ".$comp_name);
					$objUtility->logTrack();
				}
			
			}
		}
		
		if ($sign_status == "COMPLETED")
		{
			echo 'signed';
			$objConsumer = new Consumer();
			$objConsumer->consumer_id = $consumer_id;
			$row = $objConsumer->selectConsumer();
			$consumerDetail	= mysqli_fetch_object($row);
			$comp_name = $consumerDetail->usercname;

			echo $e_subject = "Document signing complete for ".$comp_name;
			echo $e_content = "
				<table>
					<tr>
						<td>Everyone has signed the documents for ".$comp_name."</td>
					</tr>
		
				</table>";
			 
			$fileObj->document_id = $record->document_id;
			$fileObj->onespanStaus=$sign_status;
			$fileObj->oneSpan_id = $sign_id;
			$fileObj->oneSpanSign_StatusAll();
			SendMail($to, $email, $name  , $e_subject,$e_content,"");	
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_document';
			$objUtility->datatableidField ='document_id';
			$objUtility->usertype=0;
			$objUtility->action='Document Signature completed email to paralegal '.$to ;
			$objUtility->dataId=$record->document_id;
			$objUtility->user_id= 0;
			$objUtility->description=addslashes("Document signing complete for ".$comp_name);
			$objUtility->logTrack();	
		}
		else
		{
			$fileObj->document_id = $record->document_id;	
			$fileObj->oneSpan_id = $sign_id;	
			$fileObj->updateCron_Status();
		}
	}
}
else
{
	$fileObj->ResetPendingSign_status();
	echo 'reset';
}

?>

<!-- Please download from Onespan Site <a href="'.$link.'">Hello Sign</a> -->