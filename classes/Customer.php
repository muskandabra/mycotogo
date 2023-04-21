<?php include_once(PATH."classes/Utility.php");?>
<?php
	Class User
	{
		var $user_id;
		var $useremail;
		var $username;
		var $password;
		var $firstname;
		var $lastname;
		var $address='';
		var $state='';
		var $zipcode='';
		var $contactno='';
		var $userkey=262050;
		var $registrationDate;
		var $userstatus_id=1;
		var $group_id;
		var $examflag=0;
		var $passflag=0;
		var $videoflag=0;
		var $isDeleted=0;
		var $lastInsertedId = 0;
		var $imagePath='';
		var $aboutme = '';
		var $confirmPassword = '';
		var $IP = '0.0.0.0';
		var $isCompleted='';
		var $usertype;
		
		
		function User()
		{
			$dbconnection= new Database(DBHOST,DBUSER,DBPASS,DBNAME);
			$this->userkey = rand(111111,999999);
		}
		
		function activateAccount()
		{
			$sqlselect="select * from tbl_user where user_id ='".$this->user_id."' and userkey='".$this->userkey."'";
			$resFound=mysql_query($sqlselect);
			$activated = 'false'; 
			if (mysqli_num_rows($resFound) > 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				userstatus_id=1 
				where user_id='".$row->user_id."'";
				$resselect=mysql_query($sql);			
				$activated='true';
				
				$objUtility = new Utility();
				$objUtility->dataTable = 'tbl_user';
				$objUtility->dataId=$row->user_id;
				$objUtility->description= 'Activated Account';
				$objUtility->logTrack();
			}
			return $activated;
		}
		
		function checkLogin()
		{
			$sqlselect="select * from tbl_user where useremail ='".mysql_real_escape_string($this->useremail)."' and password='".md5($this->password)."' and usertype='".$this->usertype."' and isDeleted=0 and userstatus_id=1";
			$resselect=mysql_query($sqlselect);
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->action='Login';
			$objUtility->description= 'Login with username:'.$this->useremail.' and Password: '.$this->password;
			$objUtility->logTrack();
			return $resselect;
			
		}
		
		
		function resetPassword()
		{
			$newPassword = "nP".rand(10,99)."S$".rand(1111,9999);
			$sqlselect="select * from tbl_user where useremail ='".mysql_real_escape_string($this->useremail)."' and isDeleted=0";
			$resFound=mysql_query($sqlselect);
			$changed = 0; // 0 for Not Changed, 1 for Changed
			if (mysqli_num_rows($resFound) > 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				password='".md5($newPassword)."'
				where user_id='".$row->user_id."'";
				$resselect=mysql_query($sql);			
				
				$this->user_id = $row->user_id;
				$this->firstname = $row->firstname;
				$this->password = $newPassword;
				$this->lastname = $row->lastname;
				$this->useremail = $row->useremail;
				$this->sendResetMail();
				$changed = 1;
			}
			else
				$changed = 0;
			
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			if ($changed==0){
				$objUtility->dataId=0;
				$objUtility->description= 'WARNING: '.$this->useremail.' Tried to Hack';
			}
			else{
				$objUtility->dataId=$this->user_id;
				$objUtility->description= $this->useremail.' Logged In';
			}
			
			$objUtility->logTrack();
			
			
			return $resselect;
		}
		
		function changePassword()
		{
			$sqlselect="select * from tbl_user where password='".md5($this->password)."' and isDeleted=0 and user_id='".$this->user_id."'";
			
			$resFound=mysql_query($sqlselect);
			$changed = 0; // 0 for not changed, 1 for changed
			//echo mysqli_num_rows($resFound);
			
			if (mysqli_num_rows($resFound) != 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				password='".md5($this->confirmPassword)."'
				where user_id='".$row->user_id."'";
				$resselect=mysql_query($sql);
				$changed = 1; // Changed
			}
			else
				$changed = 0; // Not Changed
			
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->dataId=$this->user_id;
			if ($changed==0)
				$objUtility->description= ' WRONG PASSWORD Tried to Changed from '.$this->password.' to '.$this->confirmPassword;
			else
				$objUtility->description= ' Changed PASSWORD from '.$this->password.' to '.$this->confirmPassword;
			$objUtility->logTrack();
			return $changed;
		}
		
		function selectUser()
		{
			if ($this->user_id == 0)
				$qryString="Select * from tbl_user, tbl_userstatus where tbl_user.userstatus_id=tbl_userstatus.userstatus_id and tbl_user.isDeleted=0 and usertype='paraLegal' order by firstname";
			else
				$qryString="Select * from tbl_user, tbl_userstatus where tbl_user.userstatus_id=tbl_userstatus.userstatus_id and tbl_user.isDeleted=0 and usertype='paraLegal' and user_id='".$this->user_id."' order by firstname";
			$res=mysql_query($qryString);
			return $res;
		}
		function addUser()
		{
			$sqlselect="Insert into tbl_user set firstname='".$this->firstname."', lastname='".$this->lastname."',
			useremail ='".$this->useremail."',
			username ='".$this->useremail."',
			password='".md5($this->password)."', userstatus_id=0 ,
			usertype='".$this->usertype."',
			userkey ='".$this->userkey."',
			registrationDate=curdate()";
			$resselect=mysql_query($sqlselect);
			$this->lastInsertedId=mysqli_insert_id();
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->usertype=$this->usertype;
			$objUtility->action='Added'.$this->usertype;
			$objUtility->dataId=$this->lastInsertedId;
			$objUtility->description='Added ['.$this->usertype.'] with username ['.$this->useremail.'] and password ['.$this->password.']';
			$objUtility->logTrack();
			$this->sendUserEmail();
		}
		
		function editUser()
		{
				$sqlselect="update  tbl_user set firstname='".$this->firstname."', lastname='".$this->lastname."',
				useremail ='".$this->useremail."',
				contactno ='".$this->contactno."',
				state ='".$this->state."',
				address ='".$this->address."',
				userstatus_id='".$this->userstatus_id."'
				where user_id='".$this->user_id."'";
				$resselect=mysql_query($sqlselect);			
				$objUtility = new Utility();
				$objUtility->dataTable = 'tbl_user';
				$objUtility->usertype=$this->usertype;
				$objUtility->action='Updated'.$this->usertype;
				$objUtility->dataId=$this->user_id;
				$objUtility->description='Updated ['.$this->usertype.'] with username ['.$this->useremail.'] and password ['.$this->password.']';
				$objUtility->logTrack();
		}
		function isFound()
		{
			if ($this->user_id == 0)
				$sqlPK="Select * from tbl_user where useremail='".$this->useremail."' and isDeleted=0 and usertype='paraLegal'";
			else
				$sqlPK="Select * from tbl_user where useremail='".$this->useremail."' and user_id!='".$this->user_id."'  and isDeleted=0 ";
			$resFound=mysql_query($sqlPK);
			return mysqli_num_rows($resFound);
		}
		function selectAdmin()
		{
			$qryString="Select * from tbl_user where tbl_user.isDeleted=0 and usertype = 'admin' order by firstname";
			$res=mysql_query($qryString);
			return $res;
		}
		function sendUserEmail()
		{
			$name1="NPLEX - Administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'NPLEX - Item Writer Registration - Activate Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>Welcome mail from NPLEX</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi '.$this->firstname. ' '. $this->lastname.',<br /><br> You have been registered as an Item Writer on the NPLEX website. <br />
					You must activate your account within 24 hours for further access to the system. 
					<br />Please <a href='.URL.'index.php?activateid='.base64_encode($this->lastInsertedId).'&key='.base64_encode($this->userkey).'>click here</a> to activate your account. Following are your credentials:</td>
				</tr>
				<tr>
					<td>Username:</td>
					<td>'.$this->useremail.'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>'.$this->password.'</td>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
					<td><br/><b>NPLEX Admin</b></td>
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
				mysql_query("UPDATE tbl_user SET userstatus_id=1 WHERE user_id='".$this->lastInsertedId."'");
			}
		}
		
		function sendResetMail()
		{
			$name1="NPLEX- administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'NPLEX- Item Writer Registration- Activate Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>Password reset request from NPLEX</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi '.$this->firstname. ' '. $this->lastname.',<br> <br>You have been registered as an Item Writer on the NPLEX website.
					<br> Your password has been changed.</td>
				</tr>
				<tr>
					<td>Username:</td>
					<td>'.$this->useremail.'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>'.$this->password.'</td>
				</tr>
				<tr>
					<td><br/><b>NPLEX Admin</b></td>
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
				echo ('<font color="white">New Password has been Changed Successfully to <br><b> ['.$this->password. '] <br> '.md5($this->password).'</b></font>');
				die ();
			}	
		}
	}
?>