<?php include_once(PATH."classes/Utility.php");?>
<?php 

Class File
{
	var $folder_id='';
	var $file_id;
	var $name='Untitled';
	var $Description='';
	var $createddate='';
	var $isdeleted=0; 
	var $consumer_id='';
	var $documenttype='File';
	var $parent_id=0;
	var $permission='';
	var $isAutomatic='';
	var $fileType='';
	var $attachmentfolderName='';
	var $attachmentfolderDescription='';
	var $message='';
	var $attachmentfilesformail='';
	var $email1='';
	var $email2='';
	var $sequence_id='0';
	var $uploadtype='';
	var $dbconnection	=	'';
	var $oneSpanSignId = '';
	var $onespanStaus = '';
	var $document_id='';
	var $oneSpan_id = '';
	var $signstatus = 0;
	var $user_email = '';
	var $UsersTobeSign= '';

	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

	function Update_document_signname()
	{
		if($this->document_id!=0)
		{
			$slQry="update tbl_document set signed_docname   = '".$this->signed_docname."' where document_id='".$this->document_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}

	function delete_packageInfo()
	{
		if($this->oneSpanSignId !='')
		{
			$slQry="update tbl_document set oneSpanSignId  = '', oneSpanSign_Status = '', sign_send_date  = NULL, signed_docname = '' where oneSpanSignId='".$this->oneSpanSignId."' and oneSpanSign_Status != 'COMPLETED'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}


	function markDeleted()
	{
		if($this->document_id!=0)
		{
			$slQry="update tbl_document set isdeleted  = '".$this->isdeleted."' where document_id='".$this->document_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}


	function updateCron_Status()
	{
		if($this->oneSpan_id!= '')
		{
			$slQry="update tbl_document set check_by_cron   = '1' where oneSpanSignId='".$this->oneSpan_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}

	function updateUserEmail_Status()
	{
		if($this->oneSpan_id!= '')
		{
			echo $slQry="update tbl_document set users_signed  = CONCAT(IFNULL(users_signed, ''), ',".$this->user_email."') where oneSpanSignId='".$this->oneSpan_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}



	function oneSpanSign_Status()
	{
		if($this->document_id!=0)
		{
			$slQry="update tbl_document set oneSpanSign_Status  = '". $this->onespanStaus ."' where document_id='".$this->document_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}

	function oneSpanSign_StatusAll()
	{
		if($this->oneSpan_id !='')
		{
			$slQry="update tbl_document set oneSpanSign_Status  = '". $this->onespanStaus ."' where oneSpanSignId ='".$this->oneSpan_id."'";
			return mysqli_query($this->dbconnection,$slQry);
		}
	}


	function Update_document_sign()
	{
		$cur_date = date('Y-m-d H:i:s');
		
		if($this->document_id!=0)
		{
			$slQry="update tbl_document set oneSpanSignId  = '". $this->oneSpanSignId ."', sign_send_date  = '". $cur_date ."' where document_id='".$this->document_id."'";
			mysqli_query($this->dbconnection,$slQry);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_document';
			$objUtility->datatableidField ='oneSpanSignId';
			$objUtility->usertype=$_SESSION['usertype'];
			$objUtility->action='update onespan id ';
			$objUtility->dataId=$this->document_id;
			$objUtility->user_id=$_SESSION['sessuserid'];
			$objUtility->description='update onespan id of document with id:['.$this->oneSpanSignId.']';
			$objUtility->logTrack();
			return '';
		}
		
	}

	function ShowPendingSign()
	{
		$sql = "SELECT a.*,b.created_user_id as paralegal_id,c.useremail, d.useremail as usermail, consumer_fileno FROM `tbl_document` a, tbl_consumermaster b, tbl_user c, tbl_user d WHERE (`oneSpanSignId` is not NULL and oneSpanSignId != '') and `oneSpanSign_Status` = 'SIGNING_PENDING' and a.consumer_id = b.consumer_id and b.created_user_id = c.user_id and b.user_id = d.user_id and check_by_cron = 0 group by oneSpanSignId limit 10 ";
		$que=mysqli_query($this->dbconnection,$sql);
		return $que;
	}
 
	function ResetPendingSign_status()
	{
		$slQry="update tbl_document set check_by_cron='0' where check_by_cron='1' and `oneSpanSign_Status` = 'SIGNING_PENDING'";
			return mysqli_query($this->dbconnection,$slQry);
	}


	function selectFile()
	{
		if($this->consumer_id!='' && $this->parent_id!=0)
		{
			$sel="select * from tbl_document where parent_id='".$this->parent_id."'and consumer_id='".$this->consumer_id."' and isdeleted=0 ORDER BY sequence_id ASC";
			$que=mysqli_query($this->dbconnection,$sel);
		}
		else
		{
			if($this->file_id!='')
			{
				$sel="select * from tbl_document where document_id='".$this->file_id."' and isdeleted=0 ORDER BY `document_id` DESC";
				$que=mysqli_query($this->dbconnection,$sel);
			}
			else
			{
				if($this->consumer_id!='' )
				{
					$sel="select * from tbl_document where consumer_id='".$this->consumer_id."' and isdeleted=0 ORDER BY sequence_id ASC";
					$que=mysqli_query($this->dbconnection,$sel);
				}
				else
				{
					$sel="select * from tbl_document where parent_id!=0 and isdeleted=0 ORDER BY `document_id` DESC";
					$que=mysqli_query($this->dbconnection,$sel);
				}
			}
		}	
		//echo $sel;
		return $que;
	}

	function selectAllFiles()
	{
		if($this->consumer_id!='' && $this->parent_id!=0)
		{
			$sel="select * from tbl_document where parent_id='".$this->parent_id."'and consumer_id='".$this->consumer_id."' ORDER BY sequence_id ASC";
			$que=mysqli_query($this->dbconnection,$sel);
		}
		else
		{
			if($this->file_id!='')
			{
				$sel="select * from tbl_document where document_id='".$this->file_id."'  ORDER BY `document_id` DESC";
				$que=mysqli_query($this->dbconnection,$sel);
			}
			else
			{
				if($this->consumer_id!='' )
				{
					$sel="select * from tbl_document where consumer_id='".$this->consumer_id."' ORDER BY sequence_id ASC";
					$que=mysqli_query($this->dbconnection,$sel);
				}
				else
				{
					$sel="select * from tbl_document where parent_id!=0  ORDER BY `document_id` DESC";
					$que=mysqli_query($this->dbconnection,$sel);
				}
			}
		}	
		//echo $sel;
		return $que;
	}

	function selectCancelledFile()
	{
		if($this->consumer_id!='' && $this->parent_id!=0)
		{
			$sel="select * from tbl_document where parent_id='".$this->parent_id."'and consumer_id='".$this->consumer_id."' and isdeleted=2 ORDER BY sequence_id ASC";
			$que=mysqli_query($this->dbconnection,$sel);
			return $que;
		}

	}

	function addFile()
	{ 
		$sqlQry="insert into tbl_document set
		parent_id='".$this->folder_id."',
		consumer_id='".$this->consumer_id."',
		created_user_id='".$this->user_id."',		
		sequence_id='".$this->sequence_id."',		
		name='".addslashes($this->name)."',
		Description='".addslashes($this->Description)."',
		documenttype='".$this->documenttype."',
		createddate=CURDATE(),
		permission='".$this->permission."',
		uploadtype='".$this->uploadtype."',
		signature_required='".$this->signstatus."',
		isdeleted='".$this->isdeleted."'";
		$query=mysqli_query($this->dbconnection,$sqlQry);
		$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
		$objUtility = new Utility();
		$objUtility->document_id = $this->lastInsertedId;
		$objUtility->documenttype=$this->documenttype;
		$objUtility->consumer_id=$this->consumer_id;
		$objUtility->description='Created '.$this->documenttype.' With '.$this->documenttype.' Name:['.$this->name.']';
		$objUtility->user_id=$this->user_id;
		$objUtility->isAutomatic=$this->isAutomatic;
		$objUtility->action='Created';
		
		$objUtility->documentLogTrack();
		return $this->lastInsertedId;
	}

	function checkSignedFile()
	{
		$sel="select document_id from tbl_document where parent_id='".$this->folder_id."'and consumer_id='".$this->consumer_id."' and name='".$this->name."' and documenttype='".$this->documenttype."' and uploadtype='".$this->uploadtype."' and (oneSpanSignId != '' and oneSpanSignId is NOT NULL) ";
		$res=mysqli_query($this->dbconnection,$sel);
		
		if (mysqli_num_rows($res) > 0)
		{
			$row = mysqli_fetch_object($res);
			return $row->document_id;
		}
		else
		{
			return '';
		}		
	}

	
	function editFile()
	{
		$sqlselect="Update tbl_document set
		name='".addslashes($this->name)."',
		Description='".addslashes($this->Description)."'
		where document_id='".$this->file_id."'";
		$resselect=mysqli_query($this->dbconnection,$sqlselect);
		$objUtility = new Utility();
		$objUtility->document_id = $this->file_id;
		$objUtility->documenttype=$this->documenttype;
		$objUtility->consumer_id=$this->consumer_id;
		$objUtility->description='Updated '.$this->documenttype.' With '.$this->documenttype.' Name:['.$this->name.']';
		$objUtility->user_id=$this->user_id;
		$objUtility->isAutomatic=$this->isAutomatic;
		$objUtility->action='Edited';
		$objUtility->documentLogTrack();
	}

	function updateUsersTobeSign()
	{
		$sqlselect="Update tbl_document set
		UsersTobeSign='".$this->UsersTobeSign."'
		where document_id='".$this->document_id."'";
		
		$resselect=mysqli_query($this->dbconnection,$sqlselect);		
	}

	


	function sendMailWithAttachment()
	{
		if($this->attachmentfilesformail!='')
		{
			$files=$this->attachmentfilesformail;
			//$htmlbody = $this->attachmentfolderDescription."\r\n".$this->message;
			$htmlbody = $this->message;
			$to = $this->email1.",";
			$to .= $this->email2;
			$data = '';

			$subject = $this->attachmentfolderName; //Email Subject

			$headers = "From: documents@mycotogo.com\r\nReply-To: documents@mycotogo.com";

			$random_hash = md5(date('r', time()));
			
			$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
			//$file='http://zeroguess.com/002/mycotogo/report/template_pdf/O17163GJ/SLOATDIRMIN.pdf';
			
			$message = "--PHP-mixed-$random_hash\r\n"."Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
			$message .= "--PHP-alt-$random_hash\r\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\r\n"."Content-Transfer-Encoding: 7bit\r\n\r\n";

			//Insert the html message.
			$message .= $htmlbody;
			$message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";

			if( ini_get('allow_url_fopen') ) {
			    //echo 'allow_url_fopen is enabled. file_get_contents should work well';
			} else {
			   // die('allow_url_fopen is disabled. file_get_contents would not work');
			}

			//include attachment
			for($x=0;$x<count($files);$x++){
					$attachment =chunk_split(base64_encode(file_get_contents($files[$x])));
					$data .= chunk_split(base64_encode(file_get_contents($files[$x])));
					$message .= "--PHP-mixed-$random_hash\r\n"."Content-Type: application/zip; name=\"$files[$x]\"\r\n"."Content-Transfer-Encoding: base64\r\n"."Content-Disposition: attachment\r\n\r\n";
					$message .= $data;
				}
			
			$message .= "/r/n--PHP-mixed-$random_hash--";

			//send the email
			$mail = mail( $to, $subject , $message, $headers );

			echo $mail ? "Mail sent" : "Mail failed";
		}
		else
		{
			$name1="MYCOTOGO Files";
			$to1 = $this->email1.",";
			$to1 .= $this->email2;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = $this->attachmentfolderName;
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>MYCOTOGO</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2"> '.stripslashes($this->attachmentfolderDescription). '<br/></td>
				</tr>
				<tr>
					<td colspan="2">'.stripslashes($this->message). '   <br/></td>
				</tr>
				
				<tr>
					<td><br/><b>MYCOTOGO Admin</b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>';
			
			if(APP_MODE == 'live')
			{
				
				SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");  
				
			}
			else
			{
				mysqli_query($this->dbconnection,"UPDATE tbl_user SET userstatus_id=1 WHERE user_id='".$this->lastInsertedId."'");
			}
		} 
	}
}
?>