<?php

		

		

		

		$res=$objFolder->getSysFolder();

		{

			$objFolder->consumer_id=$consumer_id;

			$parent_id='0';

			while($row=mysqli_fetch_object($res))

			{
				$objFolder->sys_folder_name=$row->sys_folder_name;

				if($objFolder->isFileExist()==0)

				{

					$objFolder->sys_folder_name=$row->sys_folder_name;

					$objFolder->sys_folder_type=$row->sys_folder_type;

					if($row->sys_folder_type!='Folder')

					{

						$objFolder->foldername='Corporate Book';

						$parent_id=$objFolder->getParentId($consumer_id);

					}

					$objFolder->sys_folder_description=$row->sys_folder_description;

					$objFolder->parent_id=$parent_id;

					$objFolder->sequence_id=$row->sequence;

					$parent_id=$objFolder->addFolder();

					

					if($objConsumer->consumerfilestatus_id!='6')

					{

						$objTemplate->consumer_id=$consumer_id;

						$objTemplate->sys_folder_id=$row->sys_folder_id;

						$objTemplate->parent_id=$parent_id;

						$objTemplate->user_id=$_SESSION['sessuserid'];

						$objTemplate->permission='V,A,E';

						$objTemplate->generateTemplate();

					}

					

					

				}

				

			}

		}

	?>