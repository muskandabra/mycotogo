<?php
include_once("private/settings.php");
include_once("classes/clsNotification.php");
include_once("classes/User.php");
include_once(PATH."classes/clsConsumer.php");
include_once(PATH."classes/Utility.php");



//mail("netz.bimal@gmail.com","Mycotogo  cron ojob start",'Cron job');

$notificationObj= new Notification();
$userobj= new User();
$objUtility = new Utility();
		

$notificationObj->today ='someday' ;
$notificationObj->someday =  date('Y-m-d', strtotime('2020-05-15'));



$res=$notificationObj->shownotification();



$userPhone	= array();

$textsms	=	'';

//print_r($res);

// $mysqli_obj = new DataBase();
// $dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);

echo date("l jS \of F Y h:i:s A");
echo "<br>";

if(mysqli_num_rows($res)>0)

{
print_r($res);
	while($record	=	mysqli_fetch_object($res))
	{
				//print_r($record);
	echo "<br>";
	echo "<br>";




	//echo $record->user_id;

		if($record->issent==0)

		{

			$companyworkphone	=	$record->companycellphone;

			$textsms	=	trim($record->consumer_fileno).' : '.trim($record->notificationdescription);

			//$textsms	=	$record->consumer_fileno.' : '. preg_replace("/\s+/", "", $record->notificationdescription); 
			//echo $textsms;

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
	

			$userPhone	=	array_unique($userPhone);
			//$userPhone = '+91';
			//print_r($userPhone);
			$cellcontact;

			$notificationObj->notificationdescription= trim($textsms,' ');

			//echo $notificationObj->companyworkphone=$cellcontact;
			$notificationObj->companyworkphone='+919872616672';

			

			if (strpos($record->message_format, 'text') >= 0) {
				$result = $notificationObj->sendSmsNotification();
				print_r($result);
		    	echo 'sms';
		    	echo '<br>';
	
			}






		}

		//break;

	}

	/*

	*

	*SMS API Integration

	*/



	

		

}

?>

