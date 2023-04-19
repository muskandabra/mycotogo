<?php include('private/settings.php');
include_once("classes/Utility.php");
	
		$objUtility = new Utility();
		$objUtility->dataTable = 'tbl_user';
		$objUtility->dataId=$_SESSION['sessuserid'];
		$objUtility->user_id=$_SESSION['sessuserid'];
		$objUtility->usertype=$_SESSION['usertype'];
		$objUtility->action='Logged Out';
		$objUtility->description= 'Logged Out :'.$_SESSION['sessuseremail'];
		$objUtility->logTrack();
		
		session_destroy();	
		session_unset();
		print "<script language=javascript>window.location='".URL."index.php'</script>";
		//header('Location: '.URL.'login.php');
?>