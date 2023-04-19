<?php
	include_once("private/settings.php");
	include_once("classes/clsConsumer.php");
	include_once("classes/pagination.php");
	include_once(PATH."classes/Module.php");
	include_once(PATH."includes/accessRights/manageConsumers.php");
	include_once(PATH."classes/clsTemplate.php");
	include_once(PATH."classes/clsSystem.php");
	include_once(PATH."classes/clsFolder.php");
	include_once(PATH."classes/clsFile.php");
	include_once(PATH."classes/clsTransaction.php");
	include_once(PATH."classes/User.php");
	include_once(PATH."includes/accessRights/manageLeftNav.php");
	
	$objConsumer = new Consumer();
	$objTransaction= new Transaction();
	$objTemplate = new Template();
	$objSystemSetting= new SystemSetting();
	$objUser=new User();
	$objFolder= new Folder();
	$objFile= new File();
	global $errormsgRecordbook;
	$errormsgRecordbook = '';
	
	
		if($_POST['task']=='update')
		{			
			$objFolder->consumerfilestatus_id='5';
			$resfolder_list=$objFolder->getSysFolder();
			$consumer_id=base64_decode($_POST['consumer_id']);
			$objFolder->consumer_id=$consumer_id;
			$objFolder->user_id=$_SESSION['sessuserid'];
			$parent_id='0';
			
			// Remove files from folder
			$folder=PATH."report/template_pdf/";
			$query=$objFolder->selectRandomFolderName();
			if(mysqli_num_rows($query)>0)
			{
				$row=mysqli_fetch_object($query);
				if($row->consumerfolder_id!='')
				{
					$selectDelFiles=$objFolder->selectDelFiles();
					if(mysqli_num_rows($selectDelFiles)>0)
					{
						while($fetchDelFiles= mysqli_fetch_object($selectDelFiles))
						{
							//echo $fetchDelFiles->name;
							if (substr($fetchDelFiles->name,0,8) == 'SHARESUB' || substr($fetchDelFiles->name,0,8) == 'SHAREMIN' || substr($fetchDelFiles->name,0,19) == 'SHAREHOLDERREGISTER' || substr($fetchDelFiles->name,0,13) == 'SHARETRANSFER' || substr($fetchDelFiles->name,0,6) == 'LEDGER'  || substr($fetchDelFiles->name,0,6) == 'DIRREG' || substr($fetchDelFiles->name,0,9) == 'Sharecert')								
							{
								if (is_null($fetchDelFiles->oneSpanSignId) || $fetchDelFiles->oneSpanSignId == '')
								{
									$file_name	=	$folder.$row->consumerfolder_id."/".$fetchDelFiles->name;
									if (file_exists($file_name))	
										unlink($file_name);
								}
							}
						}
					}
				}
			}


			while($row_l=mysqli_fetch_object($resfolder_list))
			{
				//print_r($row_l);
				//echo $row_l->sys_folder_id;
				//echo "<br>";
				//die;
				$attachedParentId=array();
				
				$objFolder->sys_folder_name=$row_l->sys_folder_name;
				if($objFolder->isFileExist()>0)
				{
					//echo 'qqq'.$row_l->sys_folder_id;
					$document_id=$objFolder->isFileExist();
					$objFolder->document_id=$document_id;
					if  ($row_l->sys_folder_id == 7 || $row_l->sys_folder_id == 9 || $row_l->sys_folder_id == 10 || $row_l->sys_folder_id == 11 || $row_l->sys_folder_id == 12 || $row_l->sys_folder_id == 14 || $row_l->sys_folder_id == 6 )
					{
						if($row_l->parent_id!='0')
							//$objFolder->DeleteFileRecreate();
					
						$attachfiles = $objFolder->selectAttachmentFiles();			
						if(mysqli_num_rows($attachfiles)>0)
						{
							while($fetchattachments = mysqli_fetch_object($attachfiles))
							{
								//echo "attach";
								//print_r($fetchattachments);
								if($row_l->parent_id!='0')
								{
									if ((is_null($fetchattachments->oneSpanSignId) || $fetchattachments->oneSpanSignId == ''))
									{
										if ($row_l->sys_folder_id == 6)
										{											
											if (substr($fetchattachments->name,0,10) == 'Dirconsent' )
											{
												$objFolder->document_id = $fetchattachments->document_id;
												$objFolder->DeleteAttachmentFileEach();
											}
										}
										else
										{
											$objFolder->document_id = $fetchattachments->document_id;
											$objFolder->DeleteAttachmentFileEach();
										}
									}
									else
									{
										if ($row_l->sys_folder_id == 14)
										{
											$objFile->document_id = $fetchattachments->document_id;
											$objFile->isdeleted = 2;
											$objFile->markDeleted();
											//print_r($fetchattachments);
										}
									}
									if($fetchattachments->uploadtype=='manual')
									{
										$attachedParentId[]=$fetchattachments->document_id;
									}
								}
							}
						}
					}
				}

// echo "<br>start";
// echo "<br>";
// echo $row_l->sys_folder_id;
// echo "<br>";


				if ( ($row_l->sys_folder_id == 7 || $row_l->sys_folder_id == 9 || $row_l->sys_folder_id == 10 || $row_l->sys_folder_id == 11 || $row_l->sys_folder_id == 12 || $row_l->sys_folder_id == 14 || $row_l->sys_folder_id == 6))
				{
					//echo 'bbb'.$row_l->sys_folder_id;
					$objFolder->sys_folder_name=$row_l->sys_folder_name;
					$objFolder->sys_folder_type=$row_l->sys_folder_type;
					if($row_l->sys_folder_type!='Folder')
					{
						$objFolder->foldername='Corporate Book';
						$parent_id=$objFolder->getParentId($consumer_id);
					}
					$objFolder->permission='V,A,E';
					$objFolder->sys_folder_description=$row_l->sys_folder_description;
					$objFolder->parent_id=$parent_id;
					$objFolder->sequence_id=$row_l->sequence;
					$parent_id = $objFolder->isFileExist();
					if ($parent_id == 0)
					{
						$parent_id=$objFolder->addFolder();
						//echo 'add';
					}
					//echo $parent_id;
	
					
					
					// if($objConsumer->consumerfilestatus_id!='6')
					// {
						$objTemplate->state_id=base64_decode($_POST['state_id']);
						$objTemplate->consumer_id=$consumer_id;
						$objTemplate->sys_folder_id=$row_l->sys_folder_id;
						$objTemplate->parent_id=$parent_id;
						$objTemplate->user_id=$_SESSION['sessuserid'];
						$objTemplate->permission='V,A,E';
						$objTemplate->update = 1;
						$objTemplate->generateTemplate();
					//}
					
					if(!empty($attachedParentId))
					{
						//print_r($attachedParentId);
						//echo '<br>';
						$objFolder->parent_id=$parent_id;
						foreach($attachedParentId as $ParentId)
						{	
							$objFolder->document_id=$ParentId;
							$parent_id=$objFolder->updateFolder();
						}
					}
					//echo 'aaa'.$row_l->sys_folder_id;
					//echo 'bbb';
				}

			}
			$objConsumer->consumer_id = $consumer_id;
			$objConsumer->updateRecordbookStatus();

			if (empty($errormsgRecordbook))
				echo json_encode(array("Done"));
			else
			{				
				echo json_encode(array("Warning! ".str_replace("\r\n","",$errormsgRecordbook)));
			}

			
		}


		if($_POST['task']=='recreate')
		{
			
			$objFolder->consumerfilestatus_id='5';
			$resfolder_list=$objFolder->getSysFolder();
			$consumer_id=base64_decode($_POST['consumer_id']);
			$objFolder->consumer_id=$consumer_id;
			$objFolder->user_id=$_SESSION['sessuserid'];
			$parent_id='0';
			
			// Remove files from folder
			$folder=PATH."report/template_pdf/";
			$query=$objFolder->selectRandomFolderName();
			if(mysqli_num_rows($query)>0)
			{
				$row=mysqli_fetch_object($query);
				if($row->consumerfolder_id!='')
				{
					$selectDelFiles=$objFolder->selectDelFiles();
					if(mysqli_num_rows($selectDelFiles)>0)
					{
						while($fetchDelFiles	= mysqli_fetch_object($selectDelFiles))
						{
							$file_name	=	$folder.$row->consumerfolder_id."/".$fetchDelFiles->name;
							if (file_exists($file_name))
								unlink($file_name);
							$Filepath = substr($file_name,0,strlen($file_name)-4)."_signed.pdf";
							try
							{
								if (file_exists($Filepath))
									unlink($Filepath);
							}
							catch(Exception $e) {
							  echo 'Message: ' .$e->getMessage();
							}
							
						}
					}
				}
			}
			//print_r($resfolder_list);
			while($row_l=mysqli_fetch_object($resfolder_list))
			{
				//print_r($row_l);
				//echo $row_l->sys_folder_id;
				//echo "<br>";
				$attachedParentId=array();
				
				$objFolder->sys_folder_name=$row_l->sys_folder_name;
				if($objFolder->isFileExist()>0)
				{
					//echo 'qqq'.$row_l->sys_folder_id;
					$document_id=$objFolder->isFileExist();
					$objFolder->document_id=$document_id;
					if($row_l->parent_id!='0')
						$objFolder->DeleteFileRecreate();
					
					$attachfiles = $objFolder->selectAttachmentFiles();
					if(mysqli_num_rows($attachfiles)>0)
					{
						while($fetchattachments = mysqli_fetch_object($attachfiles))
						{
							if($row_l->parent_id!='0')
							{
								$objFolder->DeleteAttachmentFile();
								if($fetchattachments->uploadtype=='manual')
								{
									$attachedParentId[]=$fetchattachments->document_id;
								}
							}
						}
					}
				}
				
				if($objFolder->isFileExist()==0)
				{
					//echo 'bbb'.$row_l->sys_folder_id;
					$objFolder->sys_folder_name=$row_l->sys_folder_name;
					$objFolder->sys_folder_type=$row_l->sys_folder_type;
					if($row_l->sys_folder_type!='Folder')
					{
						$objFolder->foldername='Corporate Book';
						$parent_id=$objFolder->getParentId($consumer_id);
					}
					$objFolder->permission='V,A,E';
					$objFolder->sys_folder_description=$row_l->sys_folder_description;
					$objFolder->parent_id=$parent_id;
					$objFolder->sequence_id=$row_l->sequence;
					$parent_id=$objFolder->addFolder();
					
					
					// if($objConsumer->consumerfilestatus_id!='6')
					// {
						$objTemplate->state_id=base64_decode($_POST['state_id']);
						$objTemplate->consumer_id=$consumer_id;
						$objTemplate->sys_folder_id=$row_l->sys_folder_id;
						$objTemplate->parent_id=$parent_id;
						$objTemplate->user_id=$_SESSION['sessuserid'];
						$objTemplate->permission='V,A,E';
						$objTemplate->update = 0;
						$objTemplate->generateTemplate();						
					//}
					
					if(!empty($attachedParentId))
					{
						//print_r($attachedParentId);
						//echo '<br>';
						$objFolder->parent_id=$parent_id;
						foreach($attachedParentId as $ParentId)
						{	
							$objFolder->document_id=$ParentId;
							$parent_id=$objFolder->updateFolder();
						}
					}
					//echo 'aaa'.$row_l->sys_folder_id;
				}
			}		
			if (empty($errormsgRecordbook))
				echo json_encode(array("Done"));
			else
			{				
				echo json_encode(array("Warning! ".str_replace("\r\n","",$errormsgRecordbook)));
			}						
		}
		
	?>