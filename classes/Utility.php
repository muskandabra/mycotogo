<?php include_once(PATH."classes/User.php");?>



<?php

	Class Utility

	{

		var $dataTable='';

		var $dataId=0;

		var $description='';

		var $user_id=0;

		var $ipAddress='';

		var $usertype='';

		var $action='';
		var $document_id='';
		var $documenttype='';
		var $consumer_id='';
	//	var $documentaction='';

	//	var $created_id="";
		var $datatableidField="";
		var $useremail="";
		var $isAutomatic='';
		var $dbconnection	=	'';

		

		function __construct()

		{

			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			$this->ipAddress=$_SERVER['REMOTE_ADDR'];

		}

		

		static function debug($pValue="--NO MESSAGE--",$pDie="0",$pColor = "red")

		{

			echo ('<br><font face="verdana" size="1px"  color="'.$pColor.'">START DEBUG CODE<br>');

			echo ($pValue);

			echo ('<br></font>');

			if($pDie==1)

				die;

		}

		

		function logTrack()

		{

		

			$sqlString = "insert into tbl_log set 

				dataTable='".$this->dataTable."', 	

				dataId='".$this->dataId. "',

				usertype='".$this->usertype."',

				description='".$this->description. "',

				user_id='".$this->user_id. "',

				action='".$this->action."',

				datatableidField='".$this->datatableidField."',

				ipAddress='".$this->ipAddress. "',

				dateentered=NOW()";

				//echo ($sqlString);

			mysqli_query($this->dbconnection,$sqlString);

			//die;

		}

		

		

		function selectlogTrack()

		{

			//$sqlString="select * from tbl_log order by dateentered Desc";

			if($this->useremail!='')

			{

				$sqlQry="SELECT * FROM tbl_log LOG, tbl_user USER WHERE  USER.isDeleted = 0 AND LOG.user_id = USER.user_id and USER.useremail='".$this->useremail."' and DATE(LOG.dateentered) Between '".$this->start_date."' AND '".$this->end_date."' ORDER BY LOG.dateentered , log_id Desc";

				

			}

			else

			{

				//echo $sqlQry="SELECT * FROM tbl_log LOG, tbl_user USER WHERE  USER.isDeleted = 0 AND LOG.user_id = USER.user_id and dateentered>=NOW() - INTERVAL 3 DAY ORDER BY LOG.dateentered Desc";

				$sqlQry="SELECT * FROM tbl_log LOG LEFT OUTER JOIN tbl_user USER  ON  LOG.user_id = USER.user_id and USER.isDeleted = 0 and dateentered>=NOW() - INTERVAL 3 DAY ORDER BY LOG.dateentered ,log_id Desc ";

				

				

			}



			return mysqli_query($this->dbconnection,$sqlQry);

		}

		

		function documentLogTrack()

		{

				$sqlString = "insert into tbl_documentlog set 

				document_id='".$this->document_id."', 	

				documenttype='".$this->documenttype. "',

				user_id='".$this->user_id."',

				description='".$this->description. "',

				consumer_id='".$this->consumer_id. "',

				ipAddress='".$this->ipAddress. "',

				isAutomatic='".$this->isAutomatic."',

				action='".$this->action."',

				dateentered=NOW()";

				mysqli_query($this->dbconnection,$sqlString);

		}

		function selectDocumentlog()

		{

			//$sqlString="select * from tbl_log order by dateentered Desc";

			if($this->useremail!='')

			{

				

				$sqlQry="SELECT username,consumer_fileno,action,description,dateentered,documenttype,ipAddress FROM tbl_documentlog LOG, tbl_user USER,tbl_consumermaster CONSUMER WHERE  USER.isDeleted = 0 AND LOG.user_id = USER.user_id and CONSUMER.consumer_id=LOG.consumer_id and USER.useremail='".$this->useremail."' and DATE(LOG.dateentered) Between '".$this->start_date."' AND '".$this->end_date."' ORDER BY LOG.documentlog_id Desc";

				

			}

			else

			{

				$sqlQry='SELECT username,consumer_fileno,action,description,dateentered,documenttype,ipAddress FROM tbl_documentlog LOG, tbl_user USER,tbl_consumermaster CONSUMER WHERE  USER.isDeleted = 0 AND LOG.user_id = USER.user_id and CONSUMER.consumer_id=LOG.consumer_id and dateentered >=NOW() - INTERVAL 3 DAY ORDER BY LOG.documentlog_id  Desc';				

			}



			return mysqli_query($this->dbconnection,$sqlQry);

		}

	}

?>