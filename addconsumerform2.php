<?php
include_once("private/settings.php");
include_once("classes/User.php");
include_once("classes/clsConsumer.php");
include_once(PATH."classes/Module.php");
include_once(PATH."classes/clsProvince.php");
include_once(PATH."includes/accessRights/manageConsumers.php");
include_once("classes/clsSharetransfer.php");
if($consumerAdd!=1 || $consumerEdit!=1)
{
	print "<script language=javascript>window.location='index.php'</script>";
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->

<?php
$consumerObj= new Consumer();
$provinceObj= new Province();
$sharetransObj = new ShareTransfer();
$code='';
$userType='director';
$consumerfname='';
$consumermname='';
$consumerlname='';
$consumeremail = '';
$consumerisofficer='';
$consumerofficertitle='';
$consumerotherofficertitle='';
$consumerisshareholder='0';
$consumernoofshares='0';
$consumershareclass='';
$consumersharecolor='';
$consumersharetype='';
$consumershareright='';
$consumersharecertno='';
$consumeraddress1='';
$consumercity='';
$consumerstate_id='';
$consumerzipcode='';
$consumer_id='';
$mode='';
$Checksothers='';
$ShareSignee = '';
$ParalegalDirector1cetificateprice='0';
$officer_dol = $officer_doj= $director_doj = $DateofTransfer = "";
$consumer_userid = '';
$error = '';
$workspace=0;
$parameter = '';

if(isset($_GET['workspace']) && $_GET['workspace']=='1')
{
	$workspace = 1;
	$parameter = "&workspace=1";
}

if(isset($_SESSION['workspace']) && !empty($_SESSION['workspace']))
{
	$workspace = 1;
	$parameter = "&workspace=1";
}



if(isset($_GET['id']) && $_GET['id']!='')
{
	$consumer_userid=$_GET['id'];
}


		
if(isset($_GET['code']) && $_GET['code']!='')
{
	$code=base64_decode($_GET['code']);
}

if(isset($_GET['action']) && $_GET['action']!='')
{
	$actionvalue=explode('_',$_GET['action']);
	$director=$actionvalue[1];
	//$mode='edit';
	$mode = $actionvalue[0];
	if ($mode == 'transfer')
	{
		$consumer_id=$consumerObj->getconsumer_id($code);
		$consumerTypeCount=$consumerObj->getconsumer_count($consumer_id,$userType);
		$director=$consumerTypeCount+1;
		$objConsumer=new Consumer();
		$objConsumer->consumer_id=$consumer_id;
		$objConsumer->consumeruser_id = $_GET['id'];
		$SharesMember= $objConsumer->showMembers();

		$sharetransObj->consumeruser_id = $_GET['id'];
		$shares_cert = $sharetransObj->certificate_details();

		//print_r($shares_cert);


		$consumerisofficer = 0;
		$consumerisofficer = 1;
		$consumerofficertitle = 0;
		$consumerisshareholder =1;
		//print_r($SharesMember);
	}
	if ($mode ==  'transferfrmtreasury')
	{
		$consumer_id=$consumerObj->getconsumer_id($code);
		$consumerTypeCount=$consumerObj->getconsumer_count($consumer_id,$userType);
		$director=$consumerTypeCount+1;
		$objConsumer=new Consumer();
		$objConsumer->consumer_id=$consumer_id;

		$consumerObj->consumeruser_id=$_GET['id'];
		

		$res=$consumerObj->showDirector();
		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);
			$consumerfname=$records->consumerfname;
			$consumerlname=$records->consumerlname;
			$consumermname=$records->consumermname;
			$consumeremail=$records->consumeremail;
		}
		$consumerisofficer = 0;
		$consumerofficertitle = 0;
		$consumerisshareholder =1;

	}
} 
else
{
	$consumer_id=$consumerObj->getconsumer_id($code);
	$consumerTypeCount=$consumerObj->getconsumer_count($consumer_id,$userType);
	$director=$consumerTypeCount+1;
}


if(isset($_POST['consumer_id']) && $_POST['consumer_id'] != '')
{

	$consumer_id = base64_decode($_POST['consumer_id']);
	if (!empty($consumer_id))
	{	
		$consumerObj->consumer_id=$consumer_id ;
		if ($consumerObj->getNoofSignees() > 2 && isset($_POST['ShareSignee']))
		{
			$error = "Error! Maximum two directors are allowed for share certificate signture";
		
		}
	}
}


if(isset($_POST['transferfrmtreasuryInfo']) && $_POST['transferfrmtreasuryInfo']!='' && empty($error))
{
	if ($_POST['DateofTransfer']!='' && $_POST['consumernoofshares']!='' )
	{
		$from_balance = 0;
		$cancelled_certno = '';
		$to_balance 	=	0;
		$ret_id 		=	'';
		$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
		// $res=$consumerObj->showDirector();
		// if(mysqli_num_rows($res)>0)
		// {
		// 	$records=mysqli_fetch_object($res);		
		// 	$from_balance=$records->balance_shares;		
		// 	//$cancelled_certno=$records->consumersharecertno;				
		// }
		// if (empty($from_balance) || $from_balance <= 0 )
		// {
		// 	 $code_id=$_POST['code'];
		//  	print"<script language=javascript>window.location='consumer.php?no=".$code_id."'</script>";
	 // 		die;
		// }

		//$consumerObj->consumeruser_id=$_POST['Shares_transfer_to'];
		$res=$consumerObj->showDirector();
		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);		
			$to_balance=$records->balance_shares;						
		}
		
		 $consumerObj->consumer_fileno=base64_decode($_POST['code']);
		 //$newmember_id = $_POST['Shares_transfer_to'];

		 $newmember_id = $_POST['consumeruser_id'];
		 $sharetransObj->date 				= 	date('Y-m-d',strtotime($_POST['DateofTransfer']));
		 $sharetransObj->cert_no_issued_from = 	'';
		 $sharetransObj->cert_no_cancelled	=  	'';
		 //$sharetransObj->transfer_no 		= 	$_POST['transfer_no'];
		 $sharetransObj->from_userid 		=	0;
		 $sharetransObj->to_userid 			=	$newmember_id;
		 $sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
		 $sharetransObj->folio 				=	'';
		 $sharetransObj->no_of_shares 		=	$_POST['consumernoofshares'];
		 $sharetransObj->no_of_shares_from  = 	0;
		 $sharetransObj->from_balance 		=  0;
		 $sharetransObj->to_balance 		= $to_balance+$_POST['consumernoofshares'];
		 $sharetransObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
		$sharetransObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
		$sharetransObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
		$sharetransObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
		$sharetransObj->consumershareright =$_POST['ParalegalDirector1sharerights'];
		$sharetransObj->usertype=$_SESSION['usertype'];
		$sharetransObj->user_id=$_SESSION['sessuserid'];
		$duplicert = '';
		$sharetransObj->consumer_id=base64_decode($_POST['consumer_id']);
		$duplicert = $sharetransObj->checkdupliCert();
		//die

		if (empty($duplicert))
		{
			 $ret_id = $sharetransObj->addtransfer();
			 if (!empty($ret_id))
			 {		 		
				$consumerObj->consumeruser_id=$newmember_id;
				$consumerObj->balance_shares=$to_balance+$_POST['consumernoofshares'];
				$consumerObj->update_balance_shares();
			 }
			 //die;
			 $code_id=$_POST['code'];		
			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";

		}
		else
		{
			$code_id=$_POST['code'];
		
			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorcert&msg1=".$duplicert."'</script>";
		}

		 //$ret_id = $sharetransObj->addtransfer();
		 // if (!empty($ret_id))
		 // {
			// $consumerObj->consumeruser_id=$newmember_id;
			// $consumerObj->balance_shares=$to_balance+$_POST['consumernoofshares'];
			// $consumerObj->update_balance_shares();
		 // }
		 // //die;
		 // $code_id=$_POST['code'];
		 // print"<script language=javascript>window.location='consumer.php?no=".$code_id."'</script>";
	 	
		 die;

	}

}

if(isset($_POST['transferInfo']) && $_POST['transferInfo']!='' && empty($error))
{
	//$error = '';
	if ($_POST['Shares_transfer_to']!='')
	{
		$from_balance = 0;
		$cancelled_certno = '';
		$to_balance 	=	0;
		$ret_id 		=	'';
		$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
		$res=$consumerObj->showDirector();
		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);		
			$from_balance=$records->balance_shares;		
			//$cancelled_certno=$records->consumersharecertno;				
		}
		if (empty($from_balance) || $from_balance <= 0 || ($_POST['no_of_shares_from']-$_POST['consumernoofshares']) < 0)
		{
			 $code_id=$_POST['code'];
			

		 	print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorbalance'</script>";
	 		die;
		}

		$consumerObj->consumeruser_id=$_POST['Shares_transfer_to'];
		$res=$consumerObj->showDirector();
		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);		
			$to_balance=$records->balance_shares;						
		}
		
		 $consumerObj->consumer_fileno=base64_decode($_POST['code']);
		 $newmember_id = $_POST['Shares_transfer_to'];

		 $sharetransObj->date 				= 	date('Y-m-d',strtotime($_POST['DateofTransfer']));
		 $sharetransObj->cert_no_issued_from = 	$_POST['ParalegalDirector1cetificateno_from'];
		 $sharetransObj->cert_no_cancelled	=  	$_POST['cert_transfer_no'];
		 $sharetransObj->transfer_no 		= 	$_POST['transfer_no'];
		 $sharetransObj->from_userid 		=	$_POST['consumeruser_id'];
		 $sharetransObj->to_userid 			=	$newmember_id;
		 $sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
		 $sharetransObj->folio 				=	'';
		 $sharetransObj->no_of_shares 		=	$_POST['consumernoofshares'];
		 $sharetransObj->no_of_shares_from  = 	$_POST['no_of_shares_from']-$_POST['consumernoofshares'];
		 $sharetransObj->from_balance 		=  $from_balance-$_POST['consumernoofshares'];
		 $sharetransObj->to_balance 		= $to_balance+$_POST['consumernoofshares'];
		 $sharetransObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
		$sharetransObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
		$sharetransObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
		$sharetransObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
		$sharetransObj->consumershareright =$_POST['ParalegalDirector1sharerights'];
		$sharetransObj->usertype=$_SESSION['usertype'];
		$sharetransObj->user_id=$_SESSION['sessuserid'];

		$duplicert = '';
		$sharetransObj->consumer_id=base64_decode($_POST['consumer_id']);
		$duplicert = $sharetransObj->checkdupliCert();

		 //$ret_id = $sharetransObj->addtransfer();
		if (empty($duplicert))
		{
			 $ret_id = $sharetransObj->addtransfer();
			 if (!empty($ret_id))
			 {		 		
				$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
				$consumerObj->balance_shares=$from_balance-$_POST['consumernoofshares'];
				$consumerObj->update_balance_shares();

				$consumerObj->consumeruser_id=$newmember_id;
				$consumerObj->balance_shares=$to_balance+$_POST['consumernoofshares'];
				$consumerObj->update_balance_shares();
			 }
			 //die;
			 $code_id=$_POST['code'];
		

			 print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";

		}
		else
		{
			$code_id=$_POST['code'];
		

			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorcert&msg1=".$duplicert."'</script>";
		}

		 if (!empty($ret_id))
		 {
	 	// 	$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
			// $consumerObj->balance_shares=$from_balance-$_POST['consumernoofshares'];
			// $consumerObj->update_balance_shares();

			// $consumerObj->consumeruser_id=$newmember_id;
			// $consumerObj->balance_shares=$to_balance+$_POST['consumernoofshares'];
			// $consumerObj->update_balance_shares();

		 }
		 //die;
		 //$code_id=$_POST['code'];
		//print"<script language=javascript>window.location='consumer.php?no=".$code_id."'</script>";
	 	die;

	}
	else
	{
		$from_balance = '';
		$cancelled_certno = '';
		$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
		$res=$consumerObj->showDirector();
		if(mysqli_num_rows($res)>0)
		{
			$records=mysqli_fetch_object($res);		
			$from_balance=$records->balance_shares;		
			//$cancelled_certno=$records->consumersharecertno;				
		}
		if (empty($from_balance) || $from_balance <= 0 || ($_POST['no_of_shares_from']-$_POST['consumernoofshares']) < 0)
		{
			$code_id=$_POST['code'];
		

		 	print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorbalance'</script>";
	 		die;
		}
		if($_POST['ParalegalDirector1state']=='Other')
		{
			$otherProvinceTitle=ucfirst($_POST['otherProvinceTitle']);
			$provinceObj->name=$otherProvinceTitle;
			$provinceObj->country=ucfirst($_POST['ParalegalDirector1state']);
			$provinceObj->consumer_id=$consumer_id;
			$provinceObj->usertype=$_SESSION['usertype'];
			$provinceObj->user_id=$_SESSION['sessuserid'];
			if($provinceObj->isfound()==0)
			{
					$consumerstate_id=$provinceObj->addProvince();
			}
			else
			{
				$consumerstate_id=$provinceObj->isfound();
			}
		}
		else
		{
			$consumerstate_id=$_POST['ParalegalDirector1state'];
		}
		$consumertotalshare=((float)$_POST['consumernoofshares'])*((float)$_POST['ParalegalDirector1cetificateprice']);
		$consumerObj->consumerfname=addslashes($_POST['ParalegalDirector1firstname']);
		$consumerObj->consumerlname=addslashes($_POST['ParalegalDirector1lastname']);
		$consumerObj->consumermname=addslashes($_POST['ParalegalDirector1middlename']);
		$consumerObj->consumeremail=addslashes($_POST['ParalegalDirector1Email']);
		$consumerObj->consumerisdirector=$_POST['Checksothers'];
		$consumerObj->consumerisShareSignee=isset($_POST['ShareSignee'])?'1':'0';		
		$consumerObj->consumerisofficer=$_POST['OfficerChecks'];
		$consumerObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
		$consumerObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
		$consumerObj->consumerisshareholder=$_POST['ParalegalDirector1shareholder'];
		$consumerObj->consumernoofshares=$_POST['consumernoofshares'];
		$consumerObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
		$consumerObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
		$consumerObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
		$consumerObj->consumershareright=$_POST['ParalegalDirector1sharerights'];
		$consumerObj->consumersharecertno=$_POST['ParalegalDirector1cetificateno'];
		$consumerObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
		$consumerObj->consumertotalshare=$consumertotalshare;
		$consumerObj->consumeraddress1=addslashes($_POST['ParalegalDirector1address']);
		$consumerObj->consumercity=addslashes($_POST['ParalegalDirector1city']);
		$consumerObj->consumerstate_id=$consumerstate_id;
		$consumerObj->consumerzipcode=$_POST['ParalegalDirector1zipcode'];
		$consumerObj->consumer_id=$consumer_id;
		$consumerObj->balance_shares=intval($_POST['consumernoofshares']);
		$consumerObj->usertype=$_SESSION['usertype'];
		$consumerObj->user_id=$_SESSION['sessuserid'];

		$consumerObj->consumeremail=$_POST['ParalegalDirector1Email'];
		$consumerObj->consumer_id=$consumer_id;
		$consumerObj->consumeruser_id=(isset($_POST['consumeruser_id']))?$_POST['consumeruser_id']:0;
		if ($consumerObj->checkMemberDupliEmail())
		{
			//$error = 'erroremail';
		}
	}


	$sharetransObj->consumer_id=base64_decode($_POST['consumer_id']);
	$sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
	$duplicert = $sharetransObj->checkdupliCert();

	if (empty($duplicert) && empty($error))
	{

		if (isset($_POST['director_doj']) and !empty($_POST['director_doj']))
		{
			$sharetransObj->dir_doj = $_POST['director_doj'];
		}
		if (isset($_POST['director_dol']) and !empty($_POST['director_dol']))
		{
			$sharetransObj->dir_dol = $_POST['director_dol'];
		}
		
		if (isset($_POST['officer_doj']) and !empty($_POST['officer_doj']))
		{
			$sharetransObj->officer_doj = $_POST['officer_doj'];
			$sharetransObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
			$sharetransObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
		}
		if (isset($_POST['officer_dol']) and !empty($_POST['officer_dol']))
		{
			$sharetransObj->officer_dol = $_POST['officer_dol'];
			$sharetransObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
			$sharetransObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
		}
	
		$sharetransObj->usertype=$_SESSION['usertype'];
		$sharetransObj->user_id=$_SESSION['sessuserid'];


		 $consumerObj->consumer_fileno=base64_decode($_POST['code']);
		 $newmember_id = $consumerObj->addConsumerUser();
		 $sharetransObj->consumeruser_id = $newmember_id;

		 if ($_POST['Checksothers'] == '0')
		 {
		 	$sharetransObj->dir_doj = '';
		 	$sharetransObj->dir_dol = '';
		 }
		 if ($_POST['OfficerChecks'] == '0')
		 {
		 	$sharetransObj->officer_doj = '';
		 	$sharetransObj->officer_dol = '';
		 }

		if ($_POST['Checksothers'] != '0' || $_POST['OfficerChecks'] != '0')
		{
			$sharetransObj->addmember_servicerec();
		}

		 $sharetransObj->date 				= 	date('Y-m-d',strtotime($_POST['DateofTransfer']));
		 $sharetransObj->cert_no_issued_from = 	$_POST['ParalegalDirector1cetificateno_from'];
		 $sharetransObj->cert_no_cancelled	=  	$_POST['cert_transfer_no'];
		 $sharetransObj->transfer_no 		= 	$_POST['transfer_no'];
		 $sharetransObj->from_userid 		=	$_POST['consumeruser_id'];
		 $sharetransObj->to_userid 			=	$newmember_id;
		 $sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
		 $sharetransObj->folio 				=	'';
		 $sharetransObj->no_of_shares 		=	$_POST['consumernoofshares'];
		 $sharetransObj->no_of_shares_from  = 	$_POST['no_of_shares_from']-$_POST['consumernoofshares'];
		 $sharetransObj->from_balance 		=  $from_balance-$_POST['consumernoofshares'];
		 $sharetransObj->to_balance 		= $_POST['consumernoofshares'];
		 $sharetransObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
		$sharetransObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
		$sharetransObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
		$sharetransObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
		$sharetransObj->consumershareright =$_POST['ParalegalDirector1sharerights'];
		$sharetransObj->usertype=$_SESSION['usertype'];
		$sharetransObj->user_id=$_SESSION['sessuserid'];
		$duplicert = '';
		$sharetransObj->consumer_id=base64_decode($_POST['consumer_id']);
		$duplicert = $sharetransObj->checkdupliCert();
		//$ret_id = $sharetransObj->addtransfer();


			 $ret_id = $sharetransObj->addtransfer();
			 if (!empty($ret_id))
			 {		 		
				$consumerObj->consumeruser_id=$_POST['consumeruser_id'];
				$consumerObj->balance_shares=$from_balance-intval($_POST['consumernoofshares']);
				$consumerObj->update_balance_shares();
			 }
			 //die;
			 $code_id=$_POST['code'];


			 print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";
			 die;

	}
	else
	{
		if (!empty($duplicert)) 
		{
	
			$code_id=$_POST['code'];
			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorcert&msg1=".$duplicert."'</script>";
			die;
		}		
	}
}
if(isset($_POST['directorInfo']) && $_POST['directorInfo']!='' && empty($error) )
{	
	$consumer_id=$consumerObj->getconsumer_id($code);
	if (!empty($_POST['ParalegalDirector1Email']))
	{
		$consumerObj->consumeremail=$_POST['ParalegalDirector1Email'];
		$consumerObj->consumer_id=$consumer_id;
		$consumerObj->consumeruser_id=(isset($_POST['consumeruser_id']))?$_POST['consumeruser_id']:0;
		if ($consumerObj->checkMemberDupliEmail())
		{
			//$error = 'erroremail';
		}
	}
}
	

if(isset($_POST['directorInfo']) && $_POST['directorInfo']!='' && empty($error))
{	
	
//print_r($_POST);

	
	if($_POST['ParalegalDirector1state']=='Other')
	{
		$otherProvinceTitle=ucfirst($_POST['otherProvinceTitle']);
		$provinceObj->name=$otherProvinceTitle;
		$provinceObj->country=ucfirst($_POST['ParalegalDirector1state']);
		$provinceObj->consumer_id=$consumer_id;
		$provinceObj->usertype=$_SESSION['usertype'];
		$provinceObj->user_id=$_SESSION['sessuserid'];
		if($provinceObj->isfound()==0)
		{
				$consumerstate_id=$provinceObj->addProvince();
		}
		else
		{
			$consumerstate_id=$provinceObj->isfound();
		}
	}
	else
	{
		$consumerstate_id=$_POST['ParalegalDirector1state'];
	}
	//print_r($_POST); 
	$consumertotalshare=((float)$_POST['consumernoofshares'])*((float)$_POST['ParalegalDirector1cetificateprice']);
	$consumerObj->consumerfname=addslashes($_POST['ParalegalDirector1firstname']);
	$consumerObj->consumerlname=addslashes($_POST['ParalegalDirector1lastname']);
	$consumerObj->consumermname=addslashes($_POST['ParalegalDirector1middlename']);
	$consumerObj->consumeremail=addslashes($_POST['ParalegalDirector1Email']);
	$consumerObj->consumerisdirector=$_POST['Checksothers'];
	$consumerObj->consumerisShareSignee=isset($_POST['ShareSignee'])?'1':'0';	
	$consumerObj->consumerisofficer=$_POST['OfficerChecks'];
	$consumerObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
	$consumerObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
	$consumerObj->consumerisshareholder=$_POST['ParalegalDirector1shareholder'];
	$consumerObj->consumernoofshares=$_POST['consumernoofshares'];
	$consumerObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
	$consumerObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
	$consumerObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
	$consumerObj->consumershareright=$_POST['ParalegalDirector1sharerights'];
	$consumerObj->consumersharecertno=$_POST['ParalegalDirector1cetificateno'];
	$consumerObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
	$consumerObj->consumertotalshare=$consumertotalshare;
	$consumerObj->consumeraddress1=addslashes($_POST['ParalegalDirector1address']);
	$consumerObj->consumercity=addslashes($_POST['ParalegalDirector1city']);
	$consumerObj->consumerstate_id=$consumerstate_id;
	$consumerObj->consumerzipcode=$_POST['ParalegalDirector1zipcode'];
	$consumerObj->consumer_id=$consumer_id;
	$consumerObj->usertype=$_SESSION['usertype'];
	$consumerObj->user_id=$_SESSION['sessuserid'];
	$consumerObj->balance_shares=intval($_POST['consumernoofshares']);


	if (isset($_POST['director_doj']) and !empty($_POST['director_doj']))
	{
		$sharetransObj->dir_doj = $_POST['director_doj'];
	}
	if (isset($_POST['director_dol']) and !empty($_POST['director_dol']))
	{
		$sharetransObj->dir_dol = $_POST['director_dol'];
	}
	
	if (isset($_POST['officer_doj']) and !empty($_POST['officer_doj']))
	{
		$sharetransObj->officer_doj = $_POST['officer_doj'];
		$sharetransObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
		$sharetransObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
	}
	if (isset($_POST['officer_dol']) and !empty($_POST['officer_dol']))
	{
		$sharetransObj->officer_dol = $_POST['officer_dol'];
		$sharetransObj->consumerofficertitle=$_POST['ParalegalDirector1title'];
		$sharetransObj->consumerotherofficertitle=$_POST['ParalegalOtherTitle'];
	}
	
	$sharetransObj->usertype=$_SESSION['usertype'];
	$sharetransObj->user_id=$_SESSION['sessuserid'];
	
	if(isset($_POST['EditDirector'])&& ($_POST['EditDirector'])!='')
	{ 
		//die;
		$code_id=$_POST['code'];
		$consumerObj->consumeruser_id	=$_POST['consumeruser_id'];
		$sharetransObj->consumeruser_id = $_POST['consumeruser_id'];
		//$consumerObj->user_id=$_SESSION['sessuserid'];
		
		 if ($_POST['Checksothers'] == '0')
		 {
		 	$sharetransObj->dir_doj = '';
		 	$sharetransObj->dir_dol = '';
		 }
		 if ($_POST['OfficerChecks'] == '0')
		 {
		 	$sharetransObj->officer_doj = '';
		 	$sharetransObj->officer_dol = '';
		 }


		if ($_POST['Checksothers'] != '0' || $_POST['OfficerChecks'] != '0')
		{
			//echo "memupdate";
			$report = $sharetransObj->editmember_servicerec();
			//print_r($report);
		}
		if (isset($report[0]) && $report[0] != 0)
		{
			print"<script language=javascript>window.location='consumer.php?msg1=".$report[1].$parameter."&msg=errordate&no=".$code_id."'</script>";

		}
			
				
		if (isset($_GET['task']) && $_GET['task'] == 'finish')
		{
			if (isset($_POST['anothermember']) && $_POST['anothermember'] == 1)
			{
				$consumerObj->updateConsumerUser();
				print"<script language=javascript>window.location='addconsumerform2.php?code=".$code_id.$parameter."&task=finish'</script>";					
			}	
			else
			{
						
				if (!isset($_POST['anothermember']))
				{
					echo 'here';
					$consumerObj->consumer_id=$consumer_id;	
					$consumerObj->consumeruser_id = '';					
					$resDirector = $consumerObj->showDirector();
					$position = 999;
					$consumerObj->consumeruser_id = $_POST['consumeruser_id'];
					//print_r($resDirector);
					// die;
					if (isset($_GET['action']))
					{
						//echo strpos($_GET['action'], "_");
						//echo strlen($_GET['action']);
						echo $position = substr($_GET['action'], strpos($_GET['action'], "_")+1, strlen($_GET['action'])); 
					}
					echo mysqli_num_rows($resDirector);
					if(mysqli_num_rows($resDirector)>0 && $position < mysqli_num_rows($resDirector))
					{ 
						$directorcount=1;
						
						while($rowDirector=mysqli_fetch_object($resDirector))
						{
							if ($directorcount == ($position+1))
							{
								$consumerObj->updateConsumerUser();								
								echo  "<script language=javascript>window.location='".URL."addconsumerform2.php?task=finish&id=".$rowDirector->consumeruser_id."&action=edit_".$directorcount."&code=".$code_id."'</script>";	
							}
							$directorcount++;										
						}
					}
				}

				else
				{
					$consumerObj->updateConsumerUser();
					print"<script language=javascript>window.location='additional_reg.php?code=".$code_id.$parameter."'</script>";
				}

			}				
		}
		else
		{
			$consumerObj->updateConsumerUser();
			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";
		}
		exit;
		
	
		die;
	}
	else
	{
	

		$duplicert = '';
		$sharetransObj->consumer_id=base64_decode($_POST['consumer_id']);
		 $sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
		$duplicert = $sharetransObj->checkdupliCert();

		 //$ret_id = $sharetransObj->addtransfer();

		if (empty($duplicert))
		{			
			$consumerObj->consumer_fileno=base64_decode($_POST['code']);
			$retu_id = $consumerObj->addConsumerUser();	
			$sharetransObj->consumeruser_id = $retu_id;

			if ($_POST['Checksothers'] == '0')
			 {
			 	$sharetransObj->dir_doj = '';
			 	$sharetransObj->dir_dol = '';
			 }
			if ($_POST['OfficerChecks'] == '0')
			 {
			 	$sharetransObj->officer_doj = '';
			 	$sharetransObj->officer_dol = '';
			 }


		 	//echo $_POST['OfficerChecks'];
		 	//echo $_POST['Checksothers'];

		 	//die;


			if ($_POST['Checksothers'] != '0' || $_POST['OfficerChecks'] != '0')
			{
				$sharetransObj->addmember_servicerec();
			}

			 $sharetransObj->date 				= 	date('Y-m-d',strtotime($_POST['DateofTransfer']));
			 //$sharetransObj->cert_no_issued_from = 	$_POST['ParalegalDirector1cetificateno_from'];
			 //$sharetransObj->cert_no_cancelled	=  	$_POST['cert_transfer_no'];
			 //$sharetransObj->transfer_no 		= 	$_POST['transfer_no'];
			 $sharetransObj->from_userid 		=	0;
			 $sharetransObj->to_userid 			=	$retu_id;
			 $sharetransObj->cert_no_issued_to  = $_POST['ParalegalDirector1cetificateno'];
			 $sharetransObj->folio 				=	'';
			 $sharetransObj->no_of_shares 		=	$_POST['consumernoofshares'];
			 $sharetransObj->no_of_shares_from  = 	0;
			 $sharetransObj->from_balance 		=  0;
			 $sharetransObj->to_balance 		= $_POST['consumernoofshares'];
			 $sharetransObj->consumershareclass=$_POST['ParalegalDirector1shareclass'];
			$sharetransObj->consumersharecolor=$_POST['Paralegalsharecertificatecolor'];
			$sharetransObj->consumersharetype=$_POST['ParalegalDirector1sharetype'];
			$sharetransObj->consumerpricepershare=$_POST['ParalegalDirector1cetificateprice'];
			$sharetransObj->consumershareright=$_POST['ParalegalDirector1sharerights'];
			$sharetransObj->usertype=$_SESSION['usertype'];
			$sharetransObj->user_id=$_SESSION['sessuserid'];
			
			if ($_POST['ParalegalDirector1shareholder'] != '0')
			{
			 	$ret_id = $sharetransObj->addtransfer();
			}
			 // if (!empty($ret_id))
			 // {		 		
				// $consumerObj->consumeruser_id=$_POST['consumeruser_id'];
				// $consumerObj->balance_shares=$from_balance-intval($_POST['consumernoofshares']);
				// $consumerObj->update_balance_shares();
			 // }
			 //die;
			 $code_id=$_POST['code'];
			if (isset($_GET['task']) && $_GET['task'] == 'finish')
			{
				if(isset($_POST['anotherDirector'])&& ($_POST['anotherDirector'])!='')
				{ 
					print"<script language=javascript>window.location='addconsumerform2.php?code=".$code_id."&task=finish'</script>";
				}
				if(isset($_POST['addOfficer'])&& ($_POST['addOfficer'])!='')
				{ 
					if(isset($_GET['type'])&& ($_GET['type']) =='addmember')
					{
						 print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";
					}
					else
					{
						print"<script language=javascript>window.location='additional_reg.php?code=".$code_id."&task=finish'</script>";
					}					
				}
			}
			else
			{
				if(isset($_POST['anotherDirector'])&& ($_POST['anotherDirector'])!='')
				{ 
					print"<script language=javascript>window.location='addconsumerform2.php?code=".$code_id."'</script>";
				}
				if(isset($_POST['addOfficer'])&& ($_POST['addOfficer'])!='')
				{ 
					if(isset($_GET['type'])&& ($_GET['type']) =='addmember')
					{
						print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."'</script>";
					}
					else
					{
						print"<script language=javascript>window.location='additional_reg.php?code=".$code_id."'</script>";
					}					
				}
			}
		}
		else
		{
			$code_id=$_POST['code'];
			print"<script language=javascript>window.location='consumer.php?no=".$code_id.$parameter."&msg=errorcert&msg1=".$duplicert."'</script>";
		}

		 //die;
		// $code_id=$_POST['code'];
		// if(isset($_POST['anotherDirector'])&& ($_POST['anotherDirector'])!='')
		// { 
		// 	print"<script language=javascript>window.location='addconsumerform2.php?code=".$code_id."'</script>";
		// }
		// if(isset($_POST['addOfficer'])&& ($_POST['addOfficer'])!='')
		// { 
		// 	print"<script language=javascript>window.location='additional_reg.php?code=".$code_id."'</script>";
		// }
	}
	die;
}
if($mode=='edit')
{
	$consumerObj->consumeruser_id=$_GET['id'];
	$sharetransObj->consumeruser_id = $_GET['id'];

	$info = $sharetransObj->showlastdetails();
	//print_r($info);
	
	$officer_dojlast = $info['officer_dojlast'];
	$officer_dollast = $info['officer_dollast'];
	$director_dojlast = $info['director_dojlast'];
	$director_dollast = $info['director_dollast'];

	$res=$consumerObj->showDirector();
	if(mysqli_num_rows($res)>0)
	{
		$records=mysqli_fetch_object($res);
		$consumerfname=$records->consumerfname;
		$consumerlname=$records->consumerlname;
		$consumermname=$records->consumermname;
		$consumeremail=$records->consumeremail;
		$Checksothers=$records->consumerisdirector;
		$ShareSignee = $records->share_signee ;
		$consumerisofficer=$records->consumerisofficer;
		$consumerofficertitle=$records->consumerofficertitle;
		$consumerotherofficertitle=$records->consumerotherofficertitle;
		$consumerisshareholder=$records->consumerisshareholder;
		$consumernoofshares=$records->consumernoofshares;
		$consumershareclass=$records->consumershareclass;
		$consumersharecolor=$records->consumersharecolor;
		$consumersharetype=$records->consumersharetype;
		$consumershareright=$records->consumershareright;
		$consumersharecertno=$records->consumersharecertno;
		$consumeraddress1= stripslashes($records->consumeraddress1);
		$consumercity=stripslashes($records->consumercity);
		$consumerstate_id=$records->consumerstate_id;
		$consumerzipcode=$records->consumerzipcode;
		$consumer_id=$records->consumer_id;
		$ParalegalDirector1cetificateprice=$records->consumerpricepershare;

		if ($info['director_dojlast'] == '' && $records->consumerisdirector)
		{
			$director_dojlast = $info['director_dojlast']=$consumerObj->get_incorp_date($code);
		}
		
		if ($info['officer_dojlast'] == '' && $records->consumerisofficer)
		{
			$officer_dojlast = $info['officer_dojlast']=$consumerObj->get_incorp_date($code);
		}	

	}
}
else
{
	$consumerisofficer=0;
	//$consumerisshareholder=0;
	if (empty($mode))
	{
		$officer_doj= $director_doj = $consumerObj->get_incorp_date($code);
		$DateofTransfer = $consumerObj->get_incorp_date($code);
	}

}
?>
<head>
	<meta charset="utf-8" />
	<title>MYCOTOGO | Form Stuff - Form Validation</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style-metro.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style.css?ver=1" rel="stylesheet" type="text/css"/>
	<link href="assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<link href="style.css?ver=1" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/select2/select2_metro.css" />
	<link rel="stylesheet" type="text/css" href="assets/plugins/chosen-bootstrap/chosen/chosen.css" />
	<link rel="stylesheet" type="text/css" href="style.css?ver=1" />
	<!-- END PAGE LEVEL STYLES -->
	<link rel="shortcut icon" href="favicon.ico" />
</head>

<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed" onload="return values('<?php echo $consumerisofficer;?>','<?php echo $mode; ?>'),valuesShareholder('<?php echo $consumerisshareholder;?>','<?php echo $mode; ?>'),paralegalDirectorField('<?php echo $consumerofficertitle; ?>')">
	<!-- BEGIN HEADER -->
	<div class="header navbar navbar-inverse navbar-fixed-top">
		<?php include_once("elements/header.php");?>
	</div>
	<!-- END HEADER -->
	<!-- BEGIN CONTAINER -->
	<div class="page-container row-fluid form-section add_consumer_main">
		<?php include("elements/left.php");?>
		<!-- END TOP NAVIGATION BAR -->
		<!-- END SIDEBAR MENU -->
		</div>
		<!-- BEGIN PAGE -->  
		<div class="page-content add_consumer_sub">
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<div id="portlet-config" class="modal hide">
				<div class="modal-header">
					<button data-dismiss="modal" class="close" type="button"></button>
					<h3>portlet Settings</h3>
				</div>
				<div class="modal-body">
					<p>Here will be a configuration form</p>
				</div>
			</div>
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN PAGE CONTAINER-->
			<div class="container-fluid">
				<!-- BEGIN PAGE HEADER-->   
				<div class="row-fluid">
					<div class="span12">
						<!-- BEGIN STYLE CUSTOMIZER -->
						<!-- END BEGIN STYLE CUSTOMIZER -->     
						
					</div>
				</div>
				<!-- END PAGE HEADER-->
				<!-- BEGIN PAGE CONTENT-->
			
				
				<div class="row-fluid add-consumer-form-row">
					<div class="span12">
							<!-- BEGIN VALIDATION STATES-->
						<div class="portlet box green">
							<div class="portlet-title">
								<div class="caption"><i class="icon-reorder"></i>Paralegal From Submission Page3-Member information</div>
							</div>
							<div class="portlet-body form">
								<div class="inner-wrapper">
									<div class="form">
										<h3 class="required">Fields marked with as* are required</h3>
										<form action="" id="form_sample_1" class="form-horizontal form1" method="POST">
										<?php
										if ($mode == 'transfer')
										{
											echo '<input type="hidden" name="transferInfo" value="transferInfo">';
										}
										elseif ($mode == 'transferfrmtreasury')
										{
											echo '<input type="hidden" name="transferfrmtreasuryInfo" value="transferfrmtreasuryInfo">';
										}										
										else
										{
											echo '<input type="hidden" name="directorInfo" value="directorinfo">';

										}

										?>
										<input type="hidden" name="no_of_shares_from" id="no_of_shares_from" value="">
										<input type="hidden" name="Additiontype" id="actionType" value="">
										<input type="hidden" name="code" value="<?php if(isset($_GET['code'])) echo $_GET['code'];?>">
										<input type="hidden" name="consumer_id" value="<?php if($consumer_id) echo base64_encode($consumer_id);?>">
											<?php
											if ($error && $error == 'erroremail')
											{
												?>
												<div class="alert alert-error ">
												<button class="close" data-dismiss="alert"></button>
												Error in Memeber Email, <?php echo $_POST['ParalegalDirector1Email']; ?> already exist.
												</div>
											<?php
											}
											else
											{
												if (!empty($error))
												{
													?>
													<div class="alert alert-error ">
													<button class="close" data-dismiss="alert"></button>
													<?php echo $error; ?> 
													</div>
												<?php
												}
											}
											?>																			
											<div class="alert alert-error hide">
												<button class="close" data-dismiss="alert"></button>
												You have some form errors. Please check below.
											</div>
										
											<div class="alert alert-success hide">
												<button class="close" data-dismiss="alert"></button>
												Your form validation is successful!
											</div>
											<?php
											if ($mode == 'transfer')
											{?>

											<div class="control-group forms">
												<label>Share Transfer To<span class="required">*</span> </label>
												<select class="input-m selection" name="Shares_transfer_to" id="Shares_transfer_to">
												<option value="">--Select Member--</option>
												<option value="0">Treasury</option>
													<?php 	
													while($rowMember=mysqli_fetch_object($SharesMember))
														{
															//print_r($rowMember);
															//echo $rowMember->consumeruser_id

															?>
													<option value="<?php echo $rowMember->consumeruser_id;  ?>"><?php echo $rowMember->consumerfname; if($rowMember->consumermname!=''){echo " ".$rowMember->consumermname;} if($rowMember->consumerlname!=''){echo " ".$rowMember->consumerlname;}?></option>
													<?php } ?>
												</select>
											</div>

											<div class="control-group forms">
												<label>Certificate to be Transfered (cancelled)<span class="required">*</span> </label>
												<select class="input-m selection" name="cert_transfer_no" id="cert_transfer_no">
												<option value="">--Select Certificate--</option>
													<?php 	

													
													for ($cn=0; $cn < count($shares_cert); $cn++)
														{
															?>
													<option data="<?php echo $shares_cert[$cn]['no_of_shares'];?>" value="<?php echo $shares_cert[$cn]['cert_no_issued_to'];  ?>"><?php echo $shares_cert[$cn]['cert_no_issued_to'].' ['.$shares_cert[$cn]['no_of_shares'].']';?></option>
													<?php } ?>
												</select>
											</div>

											
											<div class="control-group forms">
												<label>Transfer No.<span class="required">*</span></label>
												<input type="input" name="transfer_no"  class="input-m" id="transfer_no"/>
											</div>
											<?php
											}
											?>

										

											<div class="control-group forms">
												<label>Member <?php //echo $director; ?> First Name<span class="required">*</span></label>
												<input type="text" name="ParalegalDirector1firstname" value="<?php if($consumerfname!=''){ echo $consumerfname;} ?>" class="input-m" id="ParalegalDirector1firstname"/>
											</div>
											<div class="control-group">
												<label>Member <?php //echo $director; ?> Middle Name</label>
												<input type="text" name="ParalegalDirector1middlename" value="<?php if($consumermname!=''){ echo $consumermname;} ?>" class="input-m" id="ParalegalDirector1middlename"/>
											</div>
											<div class="control-group">
												<label>Member <?php //echo $director; ?> Last Name<span class="required">*</span></label>
												<input type="text" name="ParalegalDirector1lastname" value="<?php if($consumerlname!=''){ echo $consumerlname;} ?>" class="input-m" id="ParalegalDirector1lastname"/>
											</div>

											<div class="control-group">
												<label>Member Email</label>
												<input type="email" name="ParalegalDirector1Email" value="<?php if($consumeremail!=''){ echo $consumeremail;} ?>" class="input-m" id="ParalegalDirector1Email"/>
											</div>
											<div id="dialog-confirm"></div>
											
											<div class="control-group forms" id ="dir_div">
												<label>Director?<span class="required">*</span><span class="required showTip L1">
														<img src="img/help_icon.png" style="margin-bottom: -4px; margin-left: 6px;">
														<span class="tooltip-text">
															<img src="img/arrow-img.png">
															<p>Choose a maximum of 2 directors to sign the digital share certificate.</p>
														</span>
													</span></label>
												<div class="control-area director-area">
													<div class="checkbox-line">
														<input type="radio" <?php if($Checksothers!='' && $Checksothers==1){ echo "checked=checked";} else{ echo "checked=checked";} ?> value="1" name="Checksothers" id="Checksothers"/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" <?php if($Checksothers!='' && $Checksothers==0){ echo "checked=checked";} ?> value="0" name="Checksothers" id="Checksothers" />No
													</div>
													<div class="checkbox-line">
														<input type="radio" <?php if($Checksothers!='' && $Checksothers==2){ echo "checked=checked";} ?> value="2" name="Checksothers" id="Checksothers" />Resigned
													</div>
													<div class="checkbox-line">
														<input type="checkbox" <?php if($ShareSignee!='' && $ShareSignee==1){ echo "checked=checked";} ?> value="1" name="ShareSignee" id="ShareSignee" /> Share Certificate Signature ?
													</div>
												
														
													<span class="help-block"></span>
													<div id="form_2_service_error"></div>
												</div>
												<div class="control-group forms directordoj" <?php if ($mode=='edit') echo 'style="display:none"'; ?>>
													<label>Director Date of Joining<span class="required">*</span></label>
													<input type="date" name="director_doj" value="<?php echo $director_doj; ?>" id="director_doj" class="input-m"/>
												</div>
												<div class="control-group forms  directordol" style="display:none;">
													<label>Director Date of Resignation<span class="required">*</span></label>
													<input type="date" name="director_dol" value="" id="director_dol" class="input-m"/>
												</div>
												<?php
												if ($mode =='edit')
												{
												?>

												<div class="control-group directorlastinfo" >
													<label style="
													">&nbsp;</label>
													<span class="input-m">Director Appointment date (Latest)
													<span name="director_dojlast"  id="director_dojlast"  ><b><?php echo $director_dojlast; ?></b></span>
													<span>Resignation date (Latest)</span>
													<span name="director_dollast"  id="director_dollast"><b><?php echo $director_dollast; ?></b></span>
													</span>
												</div>
												<?php
												}
												?>

																									
											</div>

											
											<div class="control-group forms" id ="officer_div">
												<label>Officer?<span class="required">*</span></label>
												<div class="control-area director-area">
													<div class="checkbox-line">
														<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==1){ echo "checked=checked";} ?> value="1" name="OfficerChecks" id="OfficerChecks" onclick="return values('1');"/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==0){ echo "checked=checked";} ?> value="0" name="OfficerChecks" id="OfficerChecks" onclick="return values('0');"/>No
													</div>
													<div class="checkbox-line">
														<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==2){ echo "checked=checked";} ?> value="2" name="OfficerChecks" id="OfficerChecks" onclick="return values('2');"/>Resigned
													</div>
														
													<span class="help-block"></span>
													<div id="form_2_service_error"></div>
												</div>
											</div>
																												
											<div class="control-group forms officerTitle">
												<label>Officer title<span class="required">*</span></label>
												<select class="input-m selection" name="ParalegalDirector1title" id="ParalegalDirector1title" onchange="return paralegalDirectorField(this.value);">
												<option value="">--Title--</option>
												<?php $OfficerTitles=Paralegalinfo::getOfficerTitle();
													foreach($OfficerTitles as $OfficerTitle) {?>
													<option value="<?php echo $OfficerTitle; ?>"<?php if($consumerofficertitle!='' && $consumerofficertitle==$OfficerTitle){echo "selected=selected";}?>><?php echo $OfficerTitle; ?></option>
													<?php }?>
												</select>
												</div>
											<div class="control-group" id="otherTitle"  style="display:none;">
											<label></label>
											<input type="text" class="input-m " value="<?php if($consumerotherofficertitle!=''){ echo $consumerotherofficertitle;} ?>" name="ParalegalOtherTitle" id="ParalegalOtherTitle" />
											</div>
											<div class="control-group forms officerTitle" style="display:none;">
												<label>Officer Date of Joining<span class="required">*</span></label>
												<input type="date" name="officer_doj"  id="officer_doj" class="input-m" value="<?php echo $officer_doj; ?>"/>
											</div>
											<div class="control-group forms offcerdol">
												<label>Officer Date of Resignation<span class="required">*</span></label>
												<input type="date" name="officer_dol" value="<?php echo $officer_dol; ?>" id="officer_dol" class="input-m"/>
											</div>
											<?php
											if ($mode =='edit')
											{
											?>											
											<div class="control-group offcerdollast">
												<label style="
													">&nbsp;</label><span class="input-m">Officer Election date (Latest)
												<span  name="officer_dojlast" id="officer_dojlast"><b><?php echo $officer_dojlast; ?></b></span>
												<span>Resignation date (Latest)</span>
												<span name="officer_dollast"id="officer_dollast"><b><?php echo $officer_dollast; ?></b></span>
											</span>
											</div>
											<?php
											}
											?>

											<div class="control-group forms">
												<label>Shareholder?<span class="required">*</span></label>
												<div class="control-area director-area">
													<div class="checkbox-line">
														<input type="radio" value="1" <?php if($consumerisshareholder!='' && $consumerisshareholder==1){ echo "checked=checked";} ?> name="ParalegalDirector1shareholder" id="ParalegalDirector1shareholder" onclick="return valuesShareholder('1','<?php echo $mode; ?>');"/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" value="0" <?php if($consumerisshareholder!='' && $consumerisshareholder==0){ echo "checked=checked";} ?> name="ParalegalDirector1shareholder" id="ParalegalDirector1shareholder" onclick="return valuesShareholder('0','<?php echo $mode; ?>');"/>No
													</div>
													<span class="help-block"></span>
													<div id="form_2_service_error"></div>
												</div>
											</div>
											<div class="shareinfo" style="display: block;">
											<?php if($mode !='edit')
											{
											?>
											<div class="control-group forms">
												<label>Date of Transfer<span class="required">*</span></label>
												<input type="date" name="DateofTransfer"  class="input-m" id="DateofTransfer" value="<?php echo $DateofTransfer; ?>"/>
											</div>
											<?php
											}
											?>
											<div class="control-group forms">
												<label>Number of Shares <span class="required">*</span></label>
												<input type="text" class="input-m" value="<?php if($consumernoofshares!=''){ echo $consumernoofshares;} ?>" id="no" name="consumernoofshares"/>
													
											</div>
											
											<div class="control-group forms">
												<label>Enter Share Class<span class="required">*</span> </label>
												<select class="input-m selection" name="ParalegalDirector1shareclass" id="ParalegalDirector1shareclass">
												<option value="">--Share Class--</option>
													<?php 	$ShareClass=Paralegalinfo::getShareClass();
														foreach ($ShareClass as $classes){?>
													<option value="<?php echo $classes;  ?>"<?php if($consumershareclass!='' && $consumershareclass==$classes){echo "selected=selected";}?>><?php echo $classes;  ?></option>
													<?php } ?>
												</select>
											</div>
											
											<div class="control-group forms">
												<label>Enter Share Certificate Color<span class="required">*</span> </label>
												<select class="input-m selection" name="Paralegalsharecertificatecolor" id="Paralegalsharecertificatecolor">
												<option value="">--Share Certificate Color--</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Red'){ echo "Selected=Selected"; } ?> value="Red">Red</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Green'){ echo "Selected=Selected"; } ?> value="Green">Green</option>
												<option <?php if($consumersharecolor!='' && $consumersharecolor=='Blue'){ echo "Selected=Selected"; } ?> value="Blue">Blue</option>
												</select>
											</div>
											
											<div class="forms control-group">
												<label>Share Type<span class="required">*</span> </label>
												<select class="input-m selection" name="ParalegalDirector1sharetype" id="ParalegalDirector1sharetype">
													<option value="">--Share Type--</option>
													<?php $ShareTypes=Paralegalinfo::getShareType();
															foreach($ShareTypes as $ShareType){?>
													<option value="<?php echo $ShareType;?>"<?php if($consumersharetype!='' && $consumersharetype==$ShareType){echo "selected=selected";}?>><?php echo $ShareType;?></option>
													<?php }?>
													
												</select>
											</div>
											
											<div class="forms control-group">
												<label>Share Rights<span class="required">*</span></label>
												<select class="input-m selection" name="ParalegalDirector1sharerights" id="ParalegalDirector1sharerights">
													<option value=""></option>
													<?php $ShareRights=Paralegalinfo::getShareRights();
												foreach($ShareRights as $ShareRight){?>
													<option value="<?php echo $ShareRight; ?>"<?php if($consumershareright!='' && $consumershareright==$ShareRight){echo "selected=selected";}?>><?php echo $ShareRight; ?></option>
												<?php } ?>
												</select>
											</div>
											
											<div class="forms control-group">
												<label>Share Cert Number (Transferee)<span class="required">*</span></label>
												<input type="text" name="ParalegalDirector1cetificateno" id="ParalegalDirector1cerificateno" class="input-m" value="<?php if($consumersharecertno!=''){ echo $consumersharecertno;} ?>"/>  
											</div>
											<?php
											if ($mode == 'transfer')
											{?>
											<div class="forms control-group">
												<label>New Share Cert. No. of Transferor<span class="required">*</span></label>
												<input type="text" name="ParalegalDirector1cetificateno_from" id="ParalegalDirector1cetificateno_from" class="input-m"/>  
											</div>
											<?php
											}
											?>
											
											<div class="forms control-group">
												<label>Price per Share<span class="required">*</span></label>
												<input type="text" name=""  class="dollarInput" id="" value="$"/>  
												<input type="text" name="ParalegalDirector1cetificateprice" id="ParalegalDirector1cetificateprice" class="input_share" value="<?php if($ParalegalDirector1cetificateprice!=''){ echo $ParalegalDirector1cetificateprice;} ?>"/>  
											</div>
											
											</div>

											<div id="address_details">
											<div class="forms control-group">
												<label>Address 1<span class="required">*</span></label>
												<input type="text" value="<?php if($consumeraddress1!=''){ echo $consumeraddress1;} ?>" name="ParalegalDirector1address" id="ParalegalDirector1address" class="input-m"/>
											</div>
											
											<div class="forms control-group" id="add1">
												<label>City<span class="required">*</span></label>
												<input type="text" name="ParalegalDirector1city" value="<?php if($consumercity!=''){ echo $consumercity;} ?>" id="ParalegalDirector1city" class="input-m"/>
											</div>
											
											<div class="forms control-group">
												<label>State/Province<span class="required">*</span></label>
												<select class="input-m selection" id="ParalegalDirector1state" name="ParalegalDirector1state" onchange="checkOtherProvince(this.value);">
													<option value="" >--State/Province--</option>
													<?php 
														$province = $provinceObj->selectProvince();
														if(mysqli_num_rows($province)>0)
														{
															while($state=mysqli_fetch_object($province))
															{ ?>
																<option value="<?php echo $state->state_id; ?>" <?php if($consumerstate_id!='' && $consumerstate_id==$state->state_id){echo "selected=selected";}?>><?php echo $state->name; ?></option>
																<?php 
															} 
														} ?>
														<option value="Other">Other</option>
														<?php 
														$provinceObj->country='other';
														$province = $provinceObj->selectProvince();
														if(mysqli_num_rows($province)>0)
														{
															while($state=mysqli_fetch_object($province))
															{ ?>
																<option value="<?php echo $state->state_id; ?>" <?php if($consumerstate_id!='' && $consumerstate_id==$state->state_id){echo "selected=selected";}?>><?php echo $state->name; ?></option>
																<?php 
															} 
														} ?>
												</select>
											</div>
											<div id="otherProvince"  style="display:none;">
												<div class="forms control-group">
													<label></label>
													<input type="text" class="input-m " id="otherProvinceTitle" name="otherProvinceTitle"/>
												</div>
												
											</div>
										
											<div class="forms control-group">
												<label>Zip/Post Code</label>
												<input type="text" name="ParalegalDirector1zipcode" value="<?php if($consumerzipcode!=''){ echo $consumerzipcode;} ?>" id="ParalegalDirector1zipcode" class="input-m"/>
											</div>
										</div>
											<?php if(!(isset($_GET['action']))) 
											{ ?>
													 <div class="forms control-group">
													 <label>Add another member?</label>
													 <div class="checkbox-line">
														<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==1){ echo "checked=checked";} ?> value="1" name="anothermember" id="anothermember" onchange=" return anothermembersubmit(this.value);"/> Yes
													</div>
													<div class="checkbox-line">
														<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==0){ echo "checked=checked";} ?> value="0" name="anothermember" id="anothermember" onchange=" return anothermembersubmit(this.value);"/>No
													</div>
													 <select style="display:none;" class="input-m selection" id="anothermember" name="anothermember" onchange=" return anothermembervalue(this.value);">
															<option value="">--MemberType--</option>
															<option value="Officer">Officer</option>
															<option value="shareholder">Shareholder</option>
															<option value="director">Director</option>
															</select>
													 </div>
											<?php } else{ ?>
											<input type="hidden" name="consumeruser_id" value="<?php if(isset($_GET['id']) && $_GET['id']!='') { echo $_GET['id'];}?>">
												<div class="forms control-group">
													<?php
													
													if (isset($_GET['task']) && $_GET['task'] == 'finish')
													{
														$consumerObj->consumer_id=$consumer_id;	
														$consumerObj->consumeruser_id = '';					
														$resDirector = $consumerObj->showDirector();
														$position = 999;
														//print_r($resDirector);
														// die;
														if (isset($_GET['action']))
														{
															//echo strpos($_GET['action'], "_");
															//echo strlen($_GET['action']);
															$position = substr($_GET['action'], strpos($_GET['action'], "_")+1, strlen($_GET['action'])); 
														}
														//echo mysqli_num_rows($resDirector);
														if(mysqli_num_rows($resDirector)>0 && $position < mysqli_num_rows($resDirector))
														{ 
															$directorcount=1;
															
															// while($rowDirector=mysqli_fetch_object($resDirector))
															// { 
																?>
																<input type="submit" name="EditDirector" class="button1" id="addDirector"  value="Next" />
															<?php									
															//}
														}
														else
														{
														?>
															 <div class="forms control-group">
																 <label>Add another member?</label>
																 <div class="checkbox-line">
																	<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==1){ echo "checked=checked";} ?> value="1" name="anothermember" id="anothermember" onchange=" return anothermembersubmit1(this.value);"/> Yes
																 </div>
																
																 <div class="checkbox-line">
																	<input type="radio" <?php if($consumerisofficer!='' && $consumerisofficer==0){ } ?> value="0" name="anothermember" id="anothermember" onchange=" return anothermembersubmit1(this.value);"/>No
																 </div>

																<div class="forms control-group">
																	<input type="submit" name="EditDirector" class="button1" id="adddirector1" value="Next" style="display:none;" />
																	<input type="submit" name="EditDirector" class="button1" id="addofficer1" value="Next" style="display:none;" />
																	<input type="submit" name="addShareholder" class="button1" id="addshareholder" style="display:none;" value="Next" />
																</div>
															
															</div>

														<?php
														}

													} 
													else
													{													
														?>
														<input type="submit" name="EditDirector" class="button1" id="adddirector" value="FINISH"/>
														<?php
														if(isset($_GET['code']) && $_GET['code']!='')
														{
															$code_id=$_GET['code'];					
															?>
															<input type="button" class="button1" value="Back" name="btnGoBack" id="btnGoBack" onclick="window.location='consumer.php?no=<?php echo $code_id.$parameter; ?>'" />
														<?php
														}
													}
													?>													
											</div>

											<?php } ?>
											
											<div class="forms control-group">
												<input type="submit" name="anotherDirector" class="button1" id="adddirector" value="Next" style="display:none;" />
												<input type="submit" name="addOfficer" class="button1" id="addofficer" value="Next" style="display:none;" />
												<input type="submit" name="addShareholder" class="button1" id="addshareholder" style="display:none;" value="Next" />
											</div>
										 
										</form>
									</div>
								</div>
						<!-- END VALIDATION STATES-->
							</div>
						</div>
								<!-- END PAGE CONTENT--> 

						<!-- Popup Code Start -->
						<div class="timeout_reminder add-new-form-popup" style="display:none">
							<div class="portlet box green">
								<span class="fas fa-times" id="popup-close-btn"></span>
								<div class="portlet-title">
									<div class="caption"><i class="icon-reorder"></i>Reminder - Session Time Out </div>
								</div>
								<div class="portlet-body form">
									<div class="inner-wrapper">
									You Session is going to expire with in <span class="time_out_time">5 minutes</span>.Please Save your record.
									</div>
								</div>
							</div>
						</div>
						<!-- Popup Code End -->

						 <audio id="audio" src="https://secure.mycotogo.com/beep-07.wav" autoplay="false" ></audio>      
					</div>
							<!-- END PAGE CONTAINER-->
				</div>
			</div>
						<!-- END PAGE -->  
		</div>
	<!-- END CONTAINER -->
	<!-- BEGIN FOOTER -->
	<div class="footer">
		<div class="footer-inner">			<?php echo FOOTER_NAME;?>
		</div>
		<div class="footer-tools">
			<span class="go-top">
			<i class="icon-angle-up"></i>
			</span>
		</div>
	</div>
	<script src="<?php echo URL;?>assets/plugins/jquery-1.10.1.min.js" type="text/javascript"></script>
	<!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	<script src="<?php echo URL;?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
	<script src="<?php echo URL;?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="assets/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<script src="assets/scripts/app.js"></script>
	<script src="assets/scripts/form-validation.js"></script> 
	<script src="assets/scripts/bootstrap-session-timeout.min.js"></script> 
	<script src="assets/scripts/session-timeout.js"></script>

	<!-- END PAGE LEVEL STYLES -->    
	<script>
	jQuery(document).ready(function() { 
	

	/*** Popup Close Js Strat ***/
	jQuery('#popup-close-btn').click(function(){
		jQuery('.add-new-form-popup').fadeOut();
	});
	/*** Popup Close Js End ***/

		App.init();
		FormValidation.init();  
		  	var dt = new Date();
		var time_old = dt.getMinutes()
	   // initiate layout and plugins

	   var mode = "<?php echo $mode; ?>";
	   //check_timeout();
	   sflag = true;
	   function check_timeout()
	   {
	   		//$('.timeout_reminder').hide();
		   	setTimeout(function(){ 
			   	//alert(time_old); 
			   	var dt = new Date();
				var time_now= dt.getMinutes(); 
				//alert(time_now);
				if (time_now >= time_old)
				{
					lapse_min = time_now-time_old;

				}
				else
				{
					lapse_min = time_now+60-time_old;

				}
				console.log(lapse_min);
				console.log(location.hostname);
				if (lapse_min >= 18)
				{
					var sound = document.getElementById("audio");
					try
					{
						if (sflag)
						{
          					sound.play();
          					sflag = false;
						}
          			}
          			catch(e) {}
					$('.timeout_reminder').show();
					$(".time_out_time").html((23-lapse_min)+" minutes");
					//alert("Time out Left "+(24-lapse_min));
				} 
				if (23-lapse_min <= 0)
				{	
					location.href = "logout.php";
					 //location.reload();
				}								
				check_timeout();
								
			}, 60000);
		
	   }
	   

	   if (mode ==  'transferfrmtreasury' )
	   {
	   		$('#officer_div').hide();
			$('#dir_div').hide();
			$('.officerTitle').hide();
			//valuesShareholder('1');
			//sharetransfer('0');
			$('#address_details').hide();
			RemoveExtraValidations();

	   }

	    $('body').on('change','#Shares_transfer_to',function(){
		   	//alert("1");
		   	var member_id = $('#Shares_transfer_to').val();
		   	//alert(member_id);
		   	if (member_id == '')
		   	{
		   		$('#officer_div').show();
				$('#dir_div').show();
				$('.officerTitle').show();
				valuesShareholder('1');
				sharetransfer('0');
				$('#address_details').show();
				$('#ParalegalDirector1firstname').val('');
				$('#ParalegalDirector1middlename').val('');
				$('#ParalegalDirector1lastname').val('');
				$('#ParalegalDirector1Email').val('');
				return false;
		   	}
		   	if (member_id == '0')
		   	{
		   		$('#officer_div').hide();
				$('#dir_div').hide();
				$('.officerTitle').hide();
				valuesShareholder('1');
				sharetransfer('0');
				$('#address_details').hide();
				RemoveExtraValidations();
				RemoveExtraValidationsShares();
				$('#ParalegalDirector1firstname').val('N/A');
				$('#ParalegalDirector1middlename').val('N/A');
				$('#ParalegalDirector1lastname').val('N/A');
				$('#ParalegalDirector1Email').val('N/A');
				return false;
		   	}
		   	var str = "task=searchmember&member_id="+member_id;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{						
						//alert(response);	
						//alert(response['consumerfname']);
						$('#ParalegalDirector1firstname').val(response['consumerfname']);
						$('#ParalegalDirector1middlename').val(response['consumermname']);
						$('#ParalegalDirector1lastname').val(response['consumerlname']);
						$('#ParalegalDirector1Email').val(response['consumeremail']);
						$('#officer_div').hide();
						$('#dir_div').hide();
						$('.officerTitle').hide();
						valuesShareholder('1');
						sharetransfer('1');
						$('#address_details').hide();
						RemoveExtraValidations();						
						//$('#rem_data').html(response);
					}
				});
		});

		
		 $('body').on('change','#ParalegalDirector1Email',function(){
		 	var memEmail = $(this).val();
		 	var consumer_id = "<?php echo $consumer_id; ?>";
		 	var member_id = "<?php echo (isset($_GET['id']))?$_GET['id']:0; ?>";
		 	if (consumer_id == '')
		   	{
		   		alert('Not a valid Entry');
		   		return false;
		   	}
	   	  	var str = "task=CheckdupliEmail&consumer_id="+consumer_id+"&email="+memEmail+"&member_id="+member_id;
				//alert(str);
				$.ajax({
				type:"POST",
				url:"showResults.php",
				dataType: 'json',
				data:str,
				success:function(response)
				{							
					if (response == '1')
					{
						 $("#dialog-confirm").html("This email ("+memEmail+") is already in use by a different member. Do you wish to proceed?");

						  // Define the Dialog and its properties.
						  $("#dialog-confirm").dialog({
						    resizable: false,
						    modal: true,
						    title: "Warning! ",
						    height: 150,
						    width: 400,
						    buttons: {
						      "Yes": function() {
						        	$(this).dialog('close');						      
						      },
						      "No": function() {
						        	$(this).dialog('close');
						        	$("#ParalegalDirector1Email").val("");
									$("#ParalegalDirector1Email").focus();
						      }
						    }
						  });

						// if (!confirm("Warning! This email ("+memEmail+") is already in use by a different member. Do you wish to proceed?"))
						// {
						// 	$("#ParalegalDirector1Email").val("");
						// 	$("#ParalegalDirector1Email").focus();			
						// }						
					}
				}
			});
		 });

		 $('body').on('change','#ParalegalDirector1cetificateno_from,#ParalegalDirector1cerificateno',function(){
		   	var cert_no = $(this).val();
		   	var consumer_id = "<?php echo $consumer_id; ?>";
		   	// alert(cert_no);
		   	// alert(consumer_id);
		   	//alert(member_id);
		   	if (consumer_id == '')
		   	{
		   		alert('Not a valid Entry');
		   		return false;
		   	}

		   	var str = "task=CheckdupliCert&consumer_id="+consumer_id+"&cert_no="+cert_no;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{							
						//alert(response['consumerfname']);
						if (response == '1')
						{
							alert('Warning! Certificate already in use '+cert_no)
						}

					}
				});
		});


		

		$('body').on('change','#no',function(){
			var no_of_shares = 0;
		    no_of_shares = $('option:selected', '#cert_transfer_no').attr('data');
		    console.log(no_of_shares);
		    console.log($(this).val());

		   	if (parseInt(no_of_shares) > 0 && parseInt($(this).val()) >= parseInt(no_of_shares))
		   	{
		   		$("#ParalegalDirector1cetificateno_from").rules("remove");
				$("#ParalegalDirector1cetificateno_from").val('');
				$("#ParalegalDirector1cetificateno_from").parent().removeClass('error');
				$("#ParalegalDirector1cetificateno_from").parent().find('.help-inline').text('');
				console.log('err');
		   	}
		   	else
		   	{
		   		$("#ParalegalDirector1cetificateno_from").rules("add", {
				required:true,
				messages: {
				required: "Enter Certificate No."
				}
			 	});
		   	}
		   	if (parseInt(no_of_shares) > 0 && parseInt($(this).val()) > parseInt(no_of_shares))
		   	{
		   		alert('Warning ! No. of shares are more than certicate shares');
		   		return false;
		   	}
		   	
		   });

		  $('body').on('change','#cert_transfer_no',function(){
		   	var no_of_shares = $('option:selected', this).attr('data');
		   	var certno = $('option:selected', this).val();
		   	var member_id = "<?php echo $consumer_userid; ?>";
		   	$('#no_of_shares_from').val(no_of_shares);
		   	var str = "task=searchCertificateInfo&certificate="+certno+"&member="+member_id;
					//alert(str);
			$.ajax({
				type:"POST",
				url:"showResults.php",
				dataType: 'json',
				data:str,
				success:function(response)
				{						
					//alert(response);	
					//alert(response['consumerfname']);
					//$('#ParalegalDirector1firstname').val(response['consumerfname']);
					$("#ParalegalDirector1shareclass").val(response['class']);
					$("#ParalegalDirector1sharetype").val(response['type']);
					$("#ParalegalDirector1sharerights").val(response['right']);
					$("#Paralegalsharecertificatecolor").val(response['color']);
				}
			});

		   });

		

		$('body').on('change','#Checksothers',function(){
		   	//alert("1");
		   	 showdir($(this).val());
		   });  

		 $('body').on('change','#ShareSignee',function(){
		 	var consumer_id = "<?php echo $consumer_id; ?>";
		 	var shareCheckbox = $(this);		
		 	if ($('input[name=Checksothers]:checked').val() != 1 && $(this).prop("checked"))
		 	{
		 		shareCheckbox.prop("checked", false);
		 		alert('Not a Director');
			   	return false;
		 	}
			if ($(this).prop("checked"))
			{
				if (consumer_id == '')
			   	{
			   		alert('Not a valid Entry');
			   		return false;
			   	}
			   	var str = "task=CheckMaxSignee&consumer_id="+consumer_id;
					//alert(str);
					$.ajax({
					type:"POST",
					url:"showResults.php",
					dataType: 'json',
					data:str,
					success:function(response)
					{							
						//alert(response['consumerfname']);
						if (response == '1')
						{
							shareCheckbox.prop("checked", false);
							alert("Error! Two directors have already been assigned this task. If you wish to allow this director to sign the share certificates, please remove this task from an existing director.");
						}
						else
						{
							shareCheckbox.val('1');
						}

					}
				});
				
			}
			else
			{
				$(this).val('0');
			}
		 }); 

	

		
	  
	});

	function showdir(para)
	{
		  	if (para == 1)
		   	{
		   		$('.directordoj').show();
		   		$('.directordol').hide();
		   		$("#director_doj").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Date of Joining "
				}
			 });
				$("#director_dol").rules("remove");
		   	}
		   	else
		   	{
		   		$('.directordol').hide();
		   		$('.directordoj').hide();
		   		$("#director_dol").rules("remove");
		   		$("#director_doj").rules("remove");
		   	}
		   	if (para == 2)
		   	{
		   		$('.directordoj').hide();
		   		$('.directordol').show();
		   		$("#director_dol").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Date of Resignation "
				}
			 });
				$("#director_doj").rules("remove");
		   	}

	}

	function RemoveExtraValidations()
	{
		$("#OfficerChecks").rules("remove");
		$("#OfficerChecks").value = 0;
		$("#ParalegalDirector1shareholder").rules("remove");
		$("#ParalegalDirector1shareholder").value = 0;
		$("#ParalegalDirector1title").rules("remove");
			document.getElementById("ParalegalDirector1title").value = "";
			$('.officerTitle').hide();
			$("#ParalegalDirector1address").rules("remove");
			$("#ParalegalDirector1city").rules("remove");
			$("#ParalegalDirector1state").rules("remove");
			//$("#ParalegalDirector1zipcode").rules("remove");
	}

	function RemoveExtraValidationsShares()
	{
		$("#ParalegalDirector1cerificateno").rules("remove");
		$("#ParalegalDirector1cerificateno").val('');
			//document.getElementById("ParalegalDirector1cerificateno").value = "";
		$("#ParalegalDirector1cerificateno").parent().removeClass('error');
				$("#ParalegalDirector1cerificateno").parent().find('.help-inline').text('');
		//$("#ParalegalDirector1cetificateno_from").rules("remove");
			//document.getElementById("ParalegalDirector1cetificateno_from").value = "";
	}
	function checkOtherProvince(pVal)
	{
		if(pVal=='Other')
		{
			document.getElementById('otherProvince').style.display = 'block';
			$("#otherProvinceTitle").rules("add", {
				required:true,
				messages: 
				{
					required: "You did not enter Province"
				}
			});
		}
		else
		{
			$("#otherProvinceTitle").rules("remove");
			document.getElementById("otherProvinceTitle").value = "";
			document.getElementById('otherProvince').style.display = 'none';
		}
		return false;
	}
	function values(val,mode = '')
	{
		//alert(mode);
		if(val==1)
		{
			$('.officerTitle').show();
			$('.offcerdol').hide();
			$("#ParalegalDirector1title").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Officer title "
				}
			 });
			if (mode != 'edit')
			{
				$("#officer_doj").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Date of Joining "
				}
			 	});
			}
			
			$("#officer_dol").rules("remove");
		}
		else
		{
			$("#ParalegalDirector1title").rules("remove");
			$("#officer_doj").rules("remove");
			//document.getElementById("ParalegalDirector1title").value = "";
			$('.officerTitle').hide();
			$('.offcerdol').hide();
			
		}
		if (val==2)
		{
			$('.officerTitle').hide();
			$('.offcerdol').show();
			$("#officer_doj").rules("remove");
			$('.offcerdol').hide();
			if (mode != 'edit')
			{
				$('.offcerdol').show();
				$("#officer_dol").rules("add", {
					required:true,
					messages: {
					required: "You did not enter Officer Date of Resignation"
					}
				 });
			}
		}
	}
	function sharetransfer(val)
	{
		//alert("9"+val);
		if(val==1)
		{
			$("#DateofTransfer").rules("add", {
				required:true,
				messages: {
				required: "Enter Date of Transfer."
				}
			 });
			$("#transfer_no").rules("add", {
				required:true,
				messages: {
				required: "Enter Date of Transfer."
				}
			 });
			$("#ParalegalDirector1cetificateno_from").rules("add", {
				required:true,
				messages: {
				required: "Enter Certificate No."
				}
			 });
			$("#cert_transfer_no").rules("add", {
				required:true,
				messages: {
				required: "Select Certificate"
				}
			 });
		}
		else
		{
			$("#DateofTransfer").rules("add", {
				required:true,
				messages: {
				required: "Enter Date of Transfer."
				}
			 });
			$("#transfer_no").rules("add", {
				required:true,
				messages: {
				required: "Enter  Transfer No."
				}
			 });
			$("#cert_transfer_no").rules("add", {
				required:true,
				messages: {
				required: "Select Certificate"
				}
			 });
		}
	}

	function valuesShareholder(shareholderVal,mode = '')
	{
		//alert("3"+shareholderVal);
		//alert(mode);
		if(shareholderVal==1)
		{
			$('.shareinfo').show();
			$("#ParalegalDirector1shareclass").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share holder class."
				}
			 });
			 $("#Paralegalsharecertificatecolor").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share certificate color."
				}
			 });
			 $("#ParalegalDirector1sharetype").rules("add", {
				required:true,
				messages: {
				required: "You did not enter share type"
				}
			 });
			 $("#no").rules("add", {
				required:true,
				number:true,
				min:1,
				messages: {
				required: "You did not Enter Number Of Share"
				}
			 });
			 if (mode != 'edit')
			{
			 $("#DateofTransfer").rules("add", {
				required:true,
				messages: {
				required: "Enter Date of Transfer."
				}
			 });
			}
			 $("#ParalegalDirector1sharerights").rules("add", {
				required:true,
				messages: {
				required: "You did not enter sharerights."
				}
			 });
			  $("#ParalegalDirector1cerificateno").rules("add", {
				required:true,
				messages: {
				required: "You did not enter cerificateno ."
				}
			 });
			 $("#ParalegalDirector1cetificateprice").rules("add", {
				required:true,
				number:true,
				messages: {
				required: "You did not enter price of share",
				number:"only numeric value"
				}
			 });
		}
		else
		{
			$("#no").rules("remove");
			$("#ParalegalDirector1shareclass").rules("remove");
			if (mode != 'edit')
			{
				$("#DateofTransfer").rules("remove");	
				//document.getElementById("DateofTransfer").value = "";	
			}	
			document.getElementById("ParalegalDirector1shareclass").value = "";
			$("#Paralegalsharecertificatecolor").rules("remove");
			document.getElementById("Paralegalsharecertificatecolor").value = "";
			$("#ParalegalDirector1sharetype").rules("remove");
			document.getElementById("ParalegalDirector1sharetype").value = "";
			$("#ParalegalDirector1sharerights").rules("remove");
			document.getElementById("ParalegalDirector1sharerights").value = "";
			$("#ParalegalDirector1cerificateno").rules("remove");
			document.getElementById("ParalegalDirector1cerificateno").value = "";
			$("#ParalegalDirector1cetificateprice").rules("remove");
			document.getElementById("ParalegalDirector1cetificateprice").value = "0";
			
			$('.shareinfo').hide();
		}
	}
	function paralegalDirectorField(val)
	{
		if(val=='Other (enter value below)')
		{
			document.getElementById('otherTitle').style.display = 'block';
			 $("#ParalegalOtherTitle").rules("add", {
				required:true,
				messages: {
				required: "You did not enter Cerificate no"
				}
			 });
		}
		else
		{
			$("#ParalegalOtherTitle").rules("remove");
			document.getElementById("ParalegalOtherTitle").value = "";
			document.getElementById('otherTitle').style.display = 'none';
		}
	}
	function anothermembervalue(val)
	{
		if(val=='director')
		{
			document.getElementById('adddirector').style.display = 'block';
			$('#addofficer').hide();
			$('#addshareholder').hide();
		}
		else if(val=='shareholder')
		{
			document.getElementById('adddirector').style.display = 'none';
			$('#addofficer').hide();
			$('#addshareholder').show();
		}
		else if(val=='Officer')
		{
			document.getElementById('adddirector').style.display = 'none';
			$('#addofficer').show();
			$('#addshareholder').hide();
		}
		else if(val=='')
		{
			document.getElementById('adddirector').style.display = 'none';
			$('#addofficer').hide();
			$('#addshareholder').hide();
		}
	}
	
	function anothermembersubmit(val)
	{
		if(val=='1')
		{
			document.getElementById("actionType").value = "Users";
			$('#adddirector').show();
			$('#addofficer').hide();
		}
		else if(val=='0')
		{
			document.getElementById("actionType").value = "Reminder";
			$('#addofficer').show();
			$('#adddirector').hide();
		}
		
	}

	function anothermembersubmit1(val)
	{
		if(val=='1')
		{			
			$('#adddirector1').show();
			$('#addofficer1').hide();
		}
		else if(val=='0')
		{			
			$('#addofficer1').show();
			$('#adddirector1').hide();
		}
		
	}

	$(document).ready(function(){
		$(".btn-navbar").click(function(){
			$(".page-container .page-sidebar.nav-collapse").removeAttr("style");
			$(".page-sidebar .page-sidebar-menu").slideToggle(500);
		});
	});
	</script> 
	<!-- END JAVASCRIPTS -->   
</body>
<!-- END BODY -->
</html>