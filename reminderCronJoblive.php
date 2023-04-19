<?php
include_once("private/settings.php");
include_once("classes/clsNotification.php");
include_once("classes/User.php");
include_once(PATH."classes/clsConsumer.php");
include_once(PATH."classes/Utility.php");

date_default_timezone_set('Asia/Kolkata');

//mail("netz.bimal@gmail.com","Mycotogo  cron ojob start",'Cron job');

$notificationObj= new Notification();
$userobj= new User();
$objUtility = new Utility();
		

$notificationObj->today ='today' ;
$notificationObj->user_id  = 46;

$res=$notificationObj->shownotification();

//rint_r($res);




$userPhone	= array();

$textsms	=	array();

//print_r($res);

// $mysqli_obj = new DataBase();
// $dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

echo date("l jS \of F Y h:i:s A");
echo "<br>";

if(mysqli_num_rows($res)>0)

{
//print_r($res);
	while($record	=	mysqli_fetch_object($res))
	{
				//print_r($record);
	echo "<br>";
	echo "<br>";





	//echo $record->user_id;

		if($record->issent==0)

		{

			$companyworkphone	=	$record->companycellphone;

			//$textsms	=	$record->consumer_fileno.' : '.$record->notificationdescription;
			$textsms	=	trim($record->companyname).' : '.trim($record->notificationdescription);
			echo $textsms;
			die;

			if($companyworkphone!='')

			{

				$con	=	explode('-',$companyworkphone);

				if(count($con)>1)

				{

					$cellcontact	=	'+'.$con['0'].$con['1'].$con['2'].$con['3'];

				}

				else

				{

					$cellcontact	=	$companyworkphone;

				}

				$userPhone[]	=	$cellcontact;

				

			}

			$notification_id	=	$record->notification_id;
			//echo $record->user_id;

			//$mysql	=	mysqli_query($dbconnection,"update tbl_notification set issent=1 where notification_id=$notification_id");
			//mail("bimal.chandna@ratiocinativesolutions.com","Mycotogo  cron ojob next",'Cron job');	

			$userPhone	=	array_unique($userPhone);
			//$userPhone = '+91';
			//print_r($userPhone);
			echo $cellcontact;

			echo $notificationObj->notificationdescription= ' '.trim($textsms,' ');

			$notificationObj->companyworkphone=$userPhone;

				echo $userobj->user_id = $record->user_id;
			$res1 = $userobj->selectUser();
				

			if(mysqli_num_rows($res1)>0)
			{
				while($record1 = mysqli_fetch_object($res1))
				{
					echo $notificationObj->useremail=$record1->useremail;
					echo $notificationObj->username = $record1->firstname." ".$record1->lastname ;
					echo "<br>";
				}
			}
			if ($record->cc_paralegal > 0 )
			{
				echo $userobj->user_id = $record->created_user_id;
				$res2 = $userobj->selectUser();
				if(mysqli_num_rows($res2)>0)
				{
					while($record2 = mysqli_fetch_object($res2))
					{
						echo $notificationObj->useremail_cc=$record2->useremail;
						echo "cc<br>";
					}
				}
			}

			

			if (strpos($record->message_format, 'text') >= 0) {
				 $notificationObj->sendSmsNotification();
		    	echo 'sms';
		    	echo '<br>';
		  //   	$objUtility->dataTable = 'tbl_notification';
				// $objUtility->datatableidField ='';
				// $objUtility->usertype='admin';
				// $objUtility->action='send sms '.$cellcontact;
				// $objUtility->user_id=0;
				// $objUtility->dataId=0;
				// $objUtility->description='cron job send sms reminder dated '.date('Y-m-d').' with  id:['.$record->consumer_fileno.']';
				// $objUtility->logTrack();
			}

			//echo '111'.strpos($record->message_format, 'mail').'1111';


			if (strpos($record->message_format, 'mail') >= 0) {
				//$notificationObj->sendSmsNotification();
				echo 'mail';
				// echo $userobj->user_id = $record->user_id;
				// $res1 = $userobj->selectUser();
				// if(mysqli_num_rows($res1)>0)
				// {
				// 	while($record1 = mysqli_fetch_object($res1))
				// 	{
				// 		echo $notificationObj->useremail=$record1->useremail;
				// 		echo "<br>";
				// 	}
				// }

				
				$notificationObj->sendMailNotificationCron();

				// $objUtility->dataTable = 'tbl_notification';
				// $objUtility->datatableidField ='';
				// $objUtility->usertype='admin';
				// $objUtility->action='send mail '.$notificationObj->useremail;
				// $objUtility->user_id=0;
				// $objUtility->dataId=0;
				// $objUtility->description='cron job send mail reminder dated '.date('Y-m-d').' with  id:['.$record->consumer_fileno.']';
				// $objUtility->logTrack();

		    	echo 'truemail';
			}



		}

	}

	/*

	*

	*SMS API Integration

	*/



	

		

}

?>

