<?php 
include_once('private/settings.php');
$select	=	"select document_id,name,permission,uploadtype from tbl_document where 1";

$query=mysql_query($select);
if(mysqli_num_rows($query)>0)
{
	while($record	=	mysqli_fetch_object($query))
	{
		//echo $record->name;
		//echo '</br>';
		// Checking for system genrated folder from  tbl_sys_folder
		$selectFolder	=	"select sys_folder_id from  tbl_sys_folder where sys_folder_name='".$record->name."'";
		$queryselect	=	mysql_query($selectFolder);
		if(mysqli_num_rows($queryselect)>0)
		{
			$sysFolder[]	=	$record->document_id;
			$updatefolderPermission	=	"update tbl_document set permission ='V,A,E' , uploadtype	= '' where document_id='".$record->document_id."'";
			mysql_query($updatefolderPermission);
		}
		else
		{
			if (strpos($record->name,'_'))
			{
				$filetemp	=	explode('_',$record->name);
				//$filename	=	$filename[0];
			}
			else
			{
				$filetemp	=	explode('.',$record->name);
			}
			
			echo $selectFiles	=	"select template_id,template_name from tbl_template where template_name like '%".$filetemp[0]."%'";
			$queryselectFile	=	mysql_query($selectFiles);
			//echo mysqli_num_rows($queryselectFile);
			if(mysqli_num_rows($queryselectFile))
			{
				$sysFiles[]	=	$record->document_id;
				echo $updatefilePermission	=	"update tbl_document set permission ='V,A,E' , uploadtype	= '' where document_id='".$record->document_id."'";
				mysql_query($updatefilePermission);
			}
			else
			{
				$updateOtherfilePermission	=	"update tbl_document set permission ='V,A,E,D', uploadtype	= 'manual' where document_id='".$record->document_id."'";
				mysql_query($updateOtherfilePermission);
			}
		}
	}
}
print_r($sysFolder);
echo '</br>';
?>