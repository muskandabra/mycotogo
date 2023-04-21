  <?php 
  include_once(PATH."classes/Utility.php");
  include_once(PATH."classes/gateway.php");

Class Notification

{

	var $notificationcreatedby='';

	var $created_id='';

	var $consumer_id='';

	var $notification_category_id='';

	var $notificationdate='';

	var $notificationdescription='';

	var $parent_id='';

	var $notificationstatus='';

	var $created_user_id='';

	var $today='';

	var $notification_id='';

	var $consumer_fileno='';

	var $user_id='0';

	var $usertype='';

	var $Edit;

	var $dbconnection	=	'';

	var $notification_template_id	=	'';

	var $message_format='';

	var $duedate='0';

	var $sentnow='0';

	var $companyname = '';

	var $useremail = '';

	var $username = '';

	var $useremail_cc = '';

	var $someday = '';

	var $companyworkphone='';

	var $cc_paralegal = 0;


	function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		$this->userkey = rand(111111,999999);
	}


	function showNotification()
	{
		$todaydt = date('Y-m-d');
		$sqlQry="select * from tbl_notification,tbl_consumermaster where tbl_consumermaster.consumer_id=tbl_notification.consumer_id and tbl_consumermaster.is_deleted = 0 ";
		
		if($this->today=='today')
		{
			$sqlQry = $sqlQry . " and  notificationdate ='".$todaydt."' ";
		}

		if($this->today=='someday')
		{
			$sqlQry = $sqlQry . " and  notificationdate ='".$this->someday."' ";
		}

		if($this->user_id!='0')
		{
			$sqlQry = $sqlQry . " and tbl_notification.created_id='".$this->user_id."' ";
		}

		if($this->consumer_id!='')
		{
			$sqlQry = $sqlQry . "and tbl_notification.consumer_id='".$this->consumer_id."' ";
		}

		if($this->notificationstatus!='')
		{
			$sqlQry = $sqlQry . "and tbl_notification.notificationstatus='".$this->notificationstatus."' ";
		}

		if($this->consumer_fileno!='')
		{
			$sqlQry=$sqlQry . "and  tbl_consumermaster.consumer_fileno = '".$this->consumer_fileno."'";
		}

		if($this->companyname!='')
		{
			$sqlQry=$sqlQry . "and  tbl_consumermaster.companyname like '%".$this->companyname."%'";
		}

		//echo $sqlQry;
		$sqlQry=$sqlQry . "order by notificationdate asc";
		return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function showUserNotification()
	{
		$todaydt = date('Y-m-d');
		$sqlQry="select * from tbl_consumermaster,tbl_notification where tbl_consumermaster.consumer_id=tbl_notification.consumer_id and tbl_consumermaster.is_deleted = 0 ";

		if($this->today=='today')

		{

			$sqlQry = $sqlQry . " and  notificationdate ='".$todaydt."' ";

		}

		if($this->user_id!='0')

		{

			$sqlQry=$sqlQry . " and tbl_consumermaster.user_id ='".$this->user_id."' ";

		}

		if($this->notificationstatus!='')

		{

			$sqlQry=$sqlQry . "and tbl_notification.notificationstatus='".$this->notificationstatus."' ";

		}

		if($this->consumer_fileno!='')

		{

			$sqlQry=$sqlQry . "and tbl_consumermaster.consumer_fileno like '%".$this->consumer_fileno."%' or tbl_consumermaster.companyname like '%".$this->consumer_fileno."%'";

		}

		if($this->consumer_id!='')
		{

			$sqlQry=$sqlQry . "and tbl_consumermaster.consumer_id = '".$this->consumer_id."' ";

		}

		if($this->companyname!='')
		{
			$sqlQry=$sqlQry . "and  tbl_consumermaster.companyname like '%".$this->companyname."%'";
		}

		$sqlQry=$sqlQry . "order by notificationdate asc";

		//echo $sqlQry;

		return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function add_notification()

	{

		 $date1 = $this->notificationdate;

		 $notificationdate = date("Y-m-d", strtotime($date1));

		 $sqlQry="insert into tbl_notification set

			notificationcreatedby='".@$this->notificationcreatedby."',

			created_id='".$this->created_id."',

			consumer_id='".$this->consumer_id."',

			notification_category_id='".$this->notification_category_id."',

			notificationdate='".$notificationdate."', 

			notificationdescription='".addslashes($this->notificationdescription)."',

			parent_id='".$this->parent_id."',

			notificationstatus='".$this->notificationstatus."',

			message_format='".$this->message_format."',

			cc_paralegal='".$this->cc_paralegal."', 

			duedate='".$this->duedate."',

			sentnow='".$this->sentnow."',

			issent='".@$this->issent."',

			date_updated=NOW()";

           //ECHO $sqlQry;

		 mysqli_query($this->dbconnection,$sqlQry);

		 $this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		 $objUtility = new Utility();

		 $objUtility->dataTable = 'tbl_notification';

		 $objUtility->datatableidField ='notification_id';

		 $objUtility->usertype=$this->usertype;

		 $objUtility->action='Added Notification';

		 $objUtility->user_id=$this->created_id;

		 $objUtility->dataId=$this->lastInsertedId;

		 $objUtility->description='Added Notification with Notification Id:['.$this->lastInsertedId.'] Under File No:['.$this->consumer_fileno.']';

		 $objUtility->logTrack();

			//die();

	}

	

	function add_notificationTemplate()

	{

		 $sqlQry="insert into tbl_notification_template set

		 template_title='".$this->template_title."',

		 user_id='".$this->user_id."',

		 template_description='".addslashes($this->template_description)."',

		 date_created=NOW()";

		 
			
		 mysqli_query($this->dbconnection,$sqlQry);
		 //echo $sqlQry;

		 $this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		 $objUtility = new Utility();

		 $objUtility->dataTable = 'tbl_notification_template';

		 $objUtility->datatableidField ='notification_template_id';

		 $objUtility->usertype=$this->usertype;

		 $objUtility->action='Added Notification Template';

		 $objUtility->user_id=$this->user_id;
 
		 $objUtility->dataId=$this->lastInsertedId;

		 $objUtility->description='Added Notification with notification template Id:['.$this->lastInsertedId.'] Under File No:['.$this->consumer_fileno.']';

		 $objUtility->logTrack();

	}

	

	function update_notificationTemplate()
	{

		 $sqlQry="Update tbl_notification_template set

		 template_title='".$this->template_title."',

		 user_id='".$this->user_id."',

		 template_description='".addslashes($this->template_description)."'

		 where notification_template_id	=	'".$this->notification_template_id."'";

		 mysqli_query($this->dbconnection,$sqlQry);

		 $this->lastInsertedId=mysqli_insert_id($this->dbconnection);

		 $objUtility = new Utility();

		 $objUtility->dataTable = 'tbl_notification_template';

		 $objUtility->datatableidField ='notification_template_id';

		 $objUtility->usertype=$this->usertype;

		 $objUtility->action='Update Notification Template';

		 $objUtility->user_id=$this->user_id;

		 $objUtility->dataId=$this->notification_template_id;

		 $objUtility->description='Update Notification with notification template Id:['.$this->lastInsertedId.'] Under File No:['.$this->consumer_fileno.']';

		 $objUtility->logTrack();
		 //die();

	}

	

	function selectNotificationTemplate()
	{

		 $sqlQry="select * from tbl_notification_template where 1";

		 if($this->user_id!=0)

			$sqlQry= $sqlQry. " and user_id='".$this->user_id."'";

		 if($this->notification_template_id!='')

			$sqlQry	= $sqlQry. " and notification_template_id='".$this->notification_template_id."'";

		 return mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function deleteNotification()
	{

		 $sqlQry = "Delete  from tbl_notification where notification_id='".$this->notification_id."'";

		 mysqli_query($this->dbconnection,$sqlQry);

	}

	

	function deleteNotificationTemplate()
	{
		 $sqlQry = "Delete  from tbl_notification_template where notification_template_id='".$this->notification_template_id."'";
		 mysqli_query($this->dbconnection,$sqlQry);
	}

	

	function getNotificationDetails()
	{

		$sqlQry = "select * from tbl_notification where notification_id='".$this->notification_id."' || parent_id='".$this->notification_id."' order by notificationdate desc";
		return mysqli_query($this->dbconnection,$sqlQry);
	
	}

	

	function update_notification()
	{

		if($this->Edit!='')

		{

			$sqlQry="update tbl_notification set

			notificationdate='".$this->notificationdate."',

			notificationdescription='".addslashes($this->notificationdescription)."',

			notificationstatus='".$this->notificationstatus."',

			date_updated=NOW()

			where notification_id='".$this->notification_id."' || parent_id='".$this->notification_id."'";

		}

		else

		{

			$date1 = $this->notificationdate;

			$notificationdate = date("Y-m-d", strtotime($date1));

			$sqlQry="update tbl_notification set

			notificationstatus='".$this->notificationstatus."',

			date_updated=NOW()

			where notification_id='".$this->notification_id."' || parent_id='".$this->notification_id."'";

		}

		mysqli_query($this->dbconnection,$sqlQry);

		$objUtility = new Utility();

		$objUtility->dataTable = 'tbl_notification';

		$objUtility->datatableidField ='notification_id';

		$objUtility->usertype=$this->usertype;

		$objUtility->action='Updated Notification';

		$objUtility->user_id=$this->user_id;

		$objUtility->dataId=$this->notification_id;

		$objUtility->description='Update Notification with Parent Id:['.$this->notification_id.'] Under consumer File No:['.$this->consumer_fileno.'] || Notification status ['.$this->notificationstatus.']';

		$objUtility->logTrack();

		//die();

	}

	function searchNotification()
	{

		if($this->notificationstatus=='all')

		{

			 $sqlQry="SELECT * FROM tbl_consumermaster, tbl_notification WHERE tbl_consumermaster.consumer_id = tbl_notification.consumer_id AND consumer_fileno = '".$this->consumer_fileno."' and created_user_id ='".$this->created_id."' and tbl_consumermaster.is_deleted = 0";

		}

		else

		{

			 $sqlQry="SELECT * FROM tbl_consumermaster, tbl_notification WHERE tbl_consumermaster.consumer_id = tbl_notification.consumer_id AND consumer_fileno = '".$this->consumer_fileno."' and notificationstatus='".$this->notificationstatus."' and created_user_id ='".$this->created_id."' and tbl_consumermaster.is_deleted = 0";

		}

		return mysqli_query($this->dbconnection,$sqlQry);
		
	}

	function sendMailNotification()
	{
		$name1="Notification";

		$to1 = $this->useremail;
		
		$email1=$_SESSION['sessuseremail'];

		$e_subject1 = 'Notification';

		$e_content1 ='

		<table>

			<tr>

				<td colspan="2"><b>Notification</b></td>

			</tr>

			<tr>

				<td colspan="2">&nbsp;</td>

			</tr>

			<tr>

				<td colspan="2">'.ucfirst($this->notificationdescription). '</td>

			</tr>

			<tr>

				<td><br/><b>Admin</b></td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

			</tr>

		</table>';

		

		if(APP_MODE == 'live')

		{

			 //require_once(PATH."mail/smtpwork.php");

			SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");

			return true; 			

		}

		else

		{

			return false;

		}

	}



	function sendMailNotificationCron()
	{
		 $name1="Notification";

		 $e_subject1 = "Notification - email";

		 $to1 = $this->useremail;

		 $toname = $this->username;
	
		 $email1=ADMIN_EMAIL_FROM;

		 $e_content1 ="

		<table>

			<tr>

				<td colspan='2'><b>Notification</b></td>

			</tr>

			<tr>

				<td colspan='2'>&nbsp;</td>

			</tr>

			<tr>

				<td colspan='2'>".ucfirst($this->notificationdescription). "</td>

			</tr>

			<tr>

				<td><br/><b>Admin</b></td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

			</tr>

		</table>";


		if (APP_MODE == 'live')

		{

		//echo 'cc1';
			
			 //require_once(PATH."mail/smtpwork.php");

			echo "to1".$to1;
			echo 'sendmail to';
			SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");
			//$CC1 = $this->useremail_cc;

			if ($this->useremail_cc != '')
			{
				echo "<br>";
				echo 'sendmail to paralegal';
				echo $to = $this->useremail_cc;
				//echo $to = "netz.bimal@gmail.com";
				echo "<br>";
				echo $email1;
				echo "<br>";
				echo $e_subject1;
				echo "<br>";
				
				echo $e_content = "The following messages has been sent via email to ".$toname ." ".$to1. "<br><br>".$e_content1;	
				//echo $e_content = "test";
				echo "<br>";			
				SendMail($to, $email1, $name1  , $e_subject1,$e_content,"");
				//SendMail("netz.bimal@gmail.com", 'test@mycotogo.com', "name1"  , "email1","content","");

			}

			return true; 			

		}

		else

		{

			return false;

		}

	}

	

	function MailNotificationDetails()
	{

		$sqlQry="select tbl_user.useremail as useremail from tbl_consumermaster, tbl_user WHERE tbl_user.user_id = tbl_consumermaster.user_id AND consumer_fileno='".$this->consumer_fileno."' and tbl_consumermaster.is_deleted = 0";
		$res=mysqli_query($this->dbconnection,$sqlQry);
		return $res;

	}

	

	function sendSmsNotification()
	{

		//include_once('gateway.php');
		$objgateway = new Gateway();
		$objgateway->companyworkphone = $this->companyworkphone;
		$e_content1 = $objgateway->notificationdescription = $this->notificationdescription;
		$objgateway->sendsms();

		 $to1 = $this->useremail;
		 $toname = $this->username;
		 $email1=ADMIN_EMAIL_FROM;
		 $name1="Notification";
		 $e_subject1 = "Notification - sms";

		if ($this->useremail_cc != ''  && APP_MODE == 'live')
		{
			 echo $to = $this->useremail_cc;
			 echo $e_content = "The following messages has been sent via text to ".$toname ." ".implode($this->companyworkphone, ","). "<br><br>\"".$e_content1."\"";				
			echo 'ssm1';
			SendMail($to, $email1, $name1 , $e_subject1,$e_content,"");
			//SendMail($to, $email1, $name1  , $e_subject1,$e_content,$CC1="");
			//SendMail("netz.bimal@gmail.com", 'test@mycotogo.com', "name1"  , "sms1","content","");
			echo 'ssm3';

		}



		$name1="Notification";

		$to1 = 'lgriffiogriffioen@gmail.com';

		$email1=ADMIN_EMAIL_FROM;

		$e_subject1 = 'Notification SMS';

		$e_content1 ='

		<table>

			<tr>

				<td colspan="2"><b>Notification</b></td>

			</tr>

			<tr>

				<td colspan="2">&nbsp;</td>

			</tr>

			<tr>

				<td colspan="2">Phone:</td>

				<td colspan="2">001'.implode($this->companyworkphone, ","). '</td>

			</tr>

			<tr>

				<td colspan="2">Message</td>

				<td colspan="2">'.ucfirst($this->notificationdescription). '</td>

			</tr>

			<tr>

				<td><br/><b>Admin</b></td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

			</tr>

		</table>';

		

		if(APP_MODE == 'live')

		{

			//SendMail($to1, $email1, $name1  , $e_subject1,$e_content1,$CC1="");

			return true; 			

		}

		else

		{

			return false;

		}

	}

}

