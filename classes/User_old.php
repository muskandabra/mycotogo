<?php include_once(PATH."classes/Utility.php");
include_once(PATH."classes/clsTransaction.php");
	Class User
	{
		var $user_id;
		var $useremail;
		var $password;
		var $firstname;
		var $lastname;
		var $address='';
		var $state='';
		var $contactno='';
		var $userkey=262050;
		var $registrationDate;
		var $userstatus_id=0;
		var $group_id;
		var $isDeleted=0;
		var $lastInsertedId = 0;
		var $confirmPassword = '';
		var $IP = '0.0.0.0';
		var $usertype_id=0;
		var $companyname;
		var $companyaddress;
		var $fax;
		var $phone;
		var $consumerUserEmail='';
		var $consumer_password='';
		var $consumerCompany_name='';
		var $usertype='';
		var $generatedUserkey='';
		var $userExits='';
		var $isWelcome=1;
		var  $mailSent='yes';
		var $dbconnection	=	'';
		var $created_by='';
		var $consumerrec_id = '';
		var $para_legal = '';
		
		
		function __construct()
		{
			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			$this->userkey 	=	rand(111111,999999);
			$this->consumer_password	=	$this->generateRandomPassword();
			
		}
		
		function activateAccount()
		{
			$sqlselect="select * from tbl_user where user_id ='".$this->user_id."' and userkey='".$this->userkey."'";
			$resFound=mysqli_query($this->dbconnection,$sqlselect);
			$activated = 'false'; 
			if (mysqli_num_rows($resFound) > 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				userstatus_id=1 
				where user_id='".$row->user_id."'";
				$resselect=mysqli_query($this->dbconnection,$sql);			
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
		
			if($this->usertype=='admin')
			{
				$sqlselect="select * from tbl_user where useremail ='".mysqli_real_escape_string($this->dbconnection,$this->useremail)."' and password='".md5($this->password)."' and usertype_id='0' and isDeleted=0 and userstatus_id=1";
			}
			else
			{
				$sqlselect="select * from tbl_user where useremail ='".mysqli_real_escape_string($this->dbconnection,$this->useremail)."' and password='".md5($this->password)."' and usertype_id!='0' and isDeleted=0 and userstatus_id!=0";
			}
			$resselect=mysqli_query($this->dbconnection,$sqlselect);
			$objUtility = new Utility();
			$objUtility->dataTable = '';
			$objUtility->usertype='';
			$objUtility->action='Login';
			$objUtility->description= 'Login with Username:['.$this->useremail.'] || With Password: ['.$this->password.']';
			$objUtility->logTrack();
			return $resselect;
			
		}
		
		function resetPassword()
		{
			$newPassword = "nP".rand(10,99)."S$".rand(1111,9999);
			$sqlselect="select * from tbl_user where useremail ='".mysqli_real_escape_string($this->dbconnection,$this->useremail)."' and isDeleted=0 and userstatus_id = 1";
			$resFound=mysqli_query($this->dbconnection,$sqlselect);
			$changed = 0; // 0 for Not Changed, 1 for Changed
			if (mysqli_num_rows($resFound) > 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				password='".md5($newPassword)."'
				where user_id='".$row->user_id."'";
				$resselect=mysqli_query($this->dbconnection,$sql);			
				
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
			
			$resFound=mysqli_query($this->dbconnection,$sqlselect);
			$changed = 0; // 0 for not changed, 1 for changed
			//echo mysqli_num_rows($resFound);
			
			if (mysqli_num_rows($resFound) != 0)
			{
				$row=mysqli_fetch_object($resFound);
				$sql="Update tbl_user set
				password='".md5($this->confirmPassword)."'
				where user_id='".$row->user_id."'";
				$resselect=mysqli_query($this->dbconnection,$sql);
				$changed = 1; // Changed
			}
			else
				$changed = 0; // Not Changed

			//echo $sqlselect;
			
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

		function selectUserBytype()
		{

		 		$qryString="SELECT *  FROM tbl_user, tbl_userstatus, enum_usertype WHERE tbl_user.userstatus_id = tbl_userstatus.userstatus_id AND tbl_userstatus.userstatus_id = 1 AND tbl_user.isDeleted =0
								AND tbl_user.usertype_id IN (3,4,5) AND tbl_user.usertype_id = enum_usertype.usertype_id    and useremail = '".$this->para_legal."'ORDER BY firstname";
				
				//echo $qryString;	
			
			$res=mysqli_query($this->dbconnection,$qryString);
			if (mysqli_num_rows($res) > 0)
			{
				 while ($row = mysqli_fetch_object($res))
                    {  
                    	return $row->user_id;
                    }

			}
			else
			{
				return 0;
			}
		}
		
		function selectUser()
		{
		 
			if ($this->user_id == 0)
			{
				if ($this->created_by== '')
				{
					$qryString="SELECT *   FROM tbl_user, tbl_userstatus, enum_usertype WHERE tbl_user.userstatus_id = tbl_userstatus.userstatus_id AND tbl_user.isDeleted =0 
								AND tbl_user.usertype_id != '0' AND tbl_user.usertype_id = enum_usertype.usertype_id ORDER BY firstname";
				}
				else
				{
					 $qryString="SELECT *  FROM tbl_user, tbl_userstatus, enum_usertype WHERE tbl_user.userstatus_id = tbl_userstatus.userstatus_id AND tbl_user.isDeleted =0
								AND tbl_user.usertype_id != '0' AND tbl_user.usertype_id = enum_usertype.usertype_id  and tbl_user.user_id in (select user_id from tbl_consumermaster where created_user_id='".$this->created_by."') ORDER BY firstname";
				}
				//echo $qryString;
				// $qryString="SELECT * FROM tbl_user, tbl_userstatus, enum_usertype WHERE tbl_user.userstatus_id = tbl_userstatus.userstatus_id AND tbl_user.isDeleted =0
				// 				AND tbl_user.usertype_id != '0' AND tbl_user.usertype_id = enum_usertype.usertype_id ORDER BY firstname";
			}
			else
				$qryString="Select * from tbl_user, tbl_userstatus, enum_usertype where tbl_user.userstatus_id=tbl_userstatus.userstatus_id   and tbl_user.isDeleted=0 and tbl_user.usertype_id!='0' and tbl_user.user_id='".$this->user_id."' AND tbl_user.usertype_id = enum_usertype.usertype_id order by firstname";

			//echo $qryString;
			
			$res=mysqli_query($this->dbconnection,$qryString);
			return $res;
		}

		function selectUserPara()
		{
		 
			if ($this->user_id == 0)
			{
				

			}
			else
				$qryString="Select *, tbl_consumermaster.companycellphone as companycellphonessmaster , tbl_consumermaster.companyname as companynamemaster from tbl_user, tbl_userstatus, enum_usertype,  tbl_consumermaster where tbl_user.userstatus_id=tbl_userstatus.userstatus_id  and  tbl_consumermaster.user_id = tbl_user.user_id and tbl_user.isDeleted=0 and tbl_user.usertype_id!='0' and tbl_user.user_id='".$this->user_id."' AND tbl_user.usertype_id = enum_usertype.usertype_id order by firstname";
			
			$res=mysqli_query($this->dbconnection,$qryString);
			return $res;
		}
		
		
		function UserCountBook()
		{
			$qryString = "Select consumer_id from tbl_consumermaster where user_id	=	'".$this->user_id."'";
			return mysqli_query($this->dbconnection,$qryString);
		}
		
		function addUser()
		{
			if($this->consumerUserEmail!='')
			{
				
				
				$this->lastInsertedId=$this->lastUserInserted_id;
				// $objUtility = new Utility();
				// $objUtility->dataTable = 'tbl_user';
				// $objUtility->datatableidField ='user_id';
				// $objUtility->usertype_id=$this->usertype_id;
				// $objUtility->action='Added User';
				// $objUtility->user_id=$_SESSION['sessuserid'];
				// $objUtility->usertype=$_SESSION['usertype'];
				// $objUtility->dataId= $this->lastInsertedId;
				// $objUtility->description='Added User with Username: ['.$this->consumerUserEmail.'] || Password:['.$this-> consumer_password.']';
				// $objUtility->logTrack();
				//if($this->mailSent=='yes')
					//$this->sendConsumerLoginEmail();
				//return $this->lastInsertedId;
				
			}
			else
			{
				$sqlselect="Insert into tbl_user set firstname='".$this->firstname."', lastname='".$this->lastname."',
				useremail ='".$this->useremail."',
				username ='".$this->useremail."',
				password='".md5($this->password)."', userstatus_id=0,
				usertype_id='".$this->usertype_id."',
				userkey ='".$this->userkey."',
				
			    companyname='".addslashes($this->companyname)."',
				companyaddress='".addslashes($this->companyaddress)."',
				
				fax='".$this->fax."',
				phone='".$this->phone."',
				isWelcome='".$this->isWelcome."',
				registrationDate=NOW()";
				$resselect=mysqli_query($this->dbconnection,$sqlselect);
				$this->lastInsertedId=mysqli_insert_id($this->dbconnection);
				//echo $this->lastInsertedId;
				$objUtility = new Utility();
				$objUtility->datatableidField ='user_id';
				$objUtility->dataTable = 'tbl_user';
				$objUtility->usertype_id=$this->usertype_id;
				$objUtility->action='Added User';
				$objUtility->user_id=$_SESSION['sessuserid'];
				$objUtility->usertype=$_SESSION['usertype'];
				$objUtility->dataId=$this->lastInsertedId;
				$objUtility->description='Added user with Username:['.$this->useremail.'] || With Password: ['.$this->password.'] || and Usertype:['.$this->getUserType($this->usertype_id).']';
				$objUtility->logTrack();
				if($this->mailSent=='yes')
				{
					//$this->sendUserEmail();
				}
				return $this->lastInsertedId;
			}
		}
		
		function editUser()
		{
			$password='';
			if($this->password!='')
			{
				$password="password='".md5($this->password)."',";
			}
			$sqlQry="update  tbl_user set ";
			if($this->firstname!='')
				$sqlQry = $sqlQry."firstname='".$this->firstname."',";
			if($this->lastname!='')
				$sqlQry = $sqlQry."lastname='".$this->lastname."',";
			if($this->useremail!='')
			{
				$sqlQry = $sqlQry."useremail='".$this->useremail."',";
				$sqlQry = $sqlQry."username='".$this->useremail."',";
			}
			// if($this->username!='')
			// 	$sqlQry = $sqlQry."username='".$this->username."',";
			if($this->contactno!='')
				$sqlQry = $sqlQry."contactno='".$this->contactno."',";
			if($this->password!='')
				$sqlQry = $sqlQry.$password;
			if($this->state!='')
				$sqlQry = $sqlQry."state='".$this->state."',";
			if($this->address!='')
				$sqlQry = $sqlQry."address='".addslashes($this->address) ."',";
			if($this->usertype_id!='')
				$sqlQry = $sqlQry."usertype_id='".$this->usertype_id."',";
			if($this->userstatus_id!='')
				$sqlQry = $sqlQry."userstatus_id='".$this->userstatus_id."',";
			if($this->companyname!='')
				$sqlQry = $sqlQry."companyname='".addslashes($this->companyname)."',";
			if($this->companyaddress!='')
				$sqlQry = $sqlQry."companyaddress='".addslashes($this->companyaddress)."',";
			if($this->fax!='')
				$sqlQry = $sqlQry."fax='".$this->fax."',";
			if($this->isWelcome!='')
				$sqlQry = $sqlQry."isWelcome='".$this->isWelcome."',";
			if($this->user_id!='')
				$sqlQry = $sqlQry."phone='".$this->phone."' where user_id='".$this->user_id."'";
			//echo $sqlQry;

			$resselect=mysqli_query($this->dbconnection,$sqlQry);	
			//echo $sqlQry;
			$objUtility = new Utility();
			$objUtility->dataTable = 'tbl_user';
			$objUtility->datatableidField ='user_id';
			$objUtility->usertype_id=$this->usertype_id;
			$objUtility->action='Updated User';
			$objUtility->dataId=$this->user_id;
			$objUtility->user_id=$this->user_id;
			$objUtility->usertype=$this->usertype;
			$objUtility->description='Updated User info with User Id:['.$this->user_id.']';
			$objUtility->logTrack();
			if($this->userstatus_id=='2')
			{
				//$this->sendUserInactiveEmail();
			}
			if($this->userstatus_id=='1')
			{
				//$this->sendUserActiveEmail();
			}
			//die;

		}
		function isFound()
		{
			if ($this->user_id == 0)
			{
				$sqlPK="Select * from tbl_user where useremail='".$this->useremail."' and isDeleted=0";
			}
			else
			{
				$sqlPK="Select * from tbl_user where useremail='".$this->useremail."' and user_id!='".$this->user_id."'  and isDeleted=0  ";
			}
			$resFound=mysqli_query($this->dbconnection,$sqlPK);
			return mysqli_num_rows($resFound);
		}
		function isFoundUser()
		{
			$sqlPK="Select * from tbl_user where useremail='".$this->useremail."' and isDeleted=0 and userstatus_id	=	'1'";
			$resFound=mysqli_query($this->dbconnection,$sqlPK);
			return mysqli_num_rows($resFound);
		}
		function selectAdmin()
		{
			$qryString="Select * from tbl_user where tbl_user.isDeleted=0 and usertype_id = '0' order by firstname";
			$res=mysqli_query($this->dbconnection,$qryString);
			return $res;
		}
		function RecentUsers()
		{
			$selUser="SELECT dataTable,dataId,dateentered FROM `tbl_log` where  action='Added User' and  dateentered >= NOW()- INTERVAL 6 HOUR";
			return mysqli_query($this->dbconnection,$selUser);
		}
		
		function editUserProfile()
		{
			 $sqlselect="update  tbl_user set 
				firstname='".$this->firstname."', 
				lastname='".$this->lastname."',
				contactno ='".$this->contactno."',
				useremail ='".$this->useremail."',
				companyname ='".addslashes($this->companyname)."',
				companyaddress ='".addslashes($this->companyaddress)."',
				fax ='".addslashes($this->fax)."'
				where user_id='".$this->user_id."'";
				
				$resselect=mysqli_query($this->dbconnection,$sqlselect);
				//echo $sqlselect;			
				$objUtility = new Utility();
				$objUtility->dataTable = 'tbl_user';
				$objUtility->dataId=$this->user_id;
				$objUtility->description='Updated['.$this->user_id.']';
				$objUtility->logTrack();

		}
		
		function sendUserEmail()
		{
			$name1="MYCOTOGO - Administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'MYCOTOGO - Paralegal Registration - Activate Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>Welcome mail from MYCOTOGO</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi '.ucfirst($this->firstname). ' '. ucfirst($this->lastname).',<br /><br> You have been registered as a  Paralegal / Admin User on the MYCOTOGO website. <br />
					You must activate your account for further access to the system. 
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
					<td><br/><b>MYCOTOGO Admin</b></td>
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
				mysqli_query($this->dbconnection,"UPDATE tbl_user SET userstatus_id=1 WHERE user_id='".$this->lastInsertedId."'");
			}
		}
		
		function sendUserInactiveEmail()
		{
			$name1="MYCOTOGO - Administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'MYCOTOGO - Deactivatation Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>User Account Deactivation mail from MYCOTOGO</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi '.ucfirst($this->firstname). ' '. ucfirst($this->lastname).',<br /><br> Your account has been deactivated  from  MYCOTOGO website.Please contact to MYCOTOGO Admin for re-activate your account <br />
					
				</tr>
				
				<tr>
					<td><br/><b>MYCOTOGO Admin</b></td>
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
				echo 'Hi '.ucfirst($this->firstname). ' '. ucfirst($this->lastname).',<br /><br> Your account has been deactivated  from  MYCOTOGO website."';
			}
		}
		
		function sendUserActiveEmail()
		{
			$name1="MYCOTOGO - Administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'MYCOTOGO - Account Activation';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b>User Account Activation MYCOTOGO</b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi '.ucfirst($this->firstname). ' '. ucfirst($this->lastname).',<br /><br> Your account is successfully activated! <br />
					
				</tr>
				
				<tr>
					<td><br/><b>MYCOTOGO Admin</b></td>
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
				echo 'Hi '.ucfirst($this->firstname). ' '. ucfirst($this->lastname).',<br /><br> Your account has been re-activated  from  MYCOTOGO website."';
				
			}
		}
		
		function sendResetMail()
		{
			$name1="MYCOTOGO- administrator";
			$to1 = $this->useremail;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'MYCOTOGO- Paralegal / Admin User Registration- Activate Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b><span style="color:#76923c;">Request to reset your password</span></b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">Hi <span style="color:#76923c;">'.ucfirst($this->firstname). '</span>,<br> <br>Your request to reset your password has been received.  Your temporary password is located below.  
					<br> <br><br>Once you have logged into your account you may update your password and user information via the �manage user profile� link on your company dashboard.
					<br></br>Temporary Account Access</td>
				</tr>
				<tr>
					<td style="color:#76923c;">Username: ' .$this->useremail.'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td style="color:#76923c;">Password: ' .$this->password.'</td>
				</tr>
				<tr>
					<td><br/><b>Thank you,</b></td>
				</tr>
				<tr>
					<td style="color:#76923c;"><br/><b>MYCOTOGO Admin</b></td>
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
			}	
		}
		function showUserType()
		{
			$sqlQry="select * from enum_usertype where isEnable=1 and usertype_id!=0";
			return mysqli_query($this->dbconnection,$sqlQry);
		}
		
		function generateRandomPassword($length = 8)
		{
			$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomPassword = '';
			$startPos=10;
			$endPos=1;
			for ($i = 0; $i < $length; $i++) 
			{
				
				$randomPassword .= $characters[rand($startPos, strlen($characters)-$endPos)];
				$lenth= strlen($randomPassword);
				if($lenth >= '6')
				{
					$startPos=10;
					$endPos=1;
				}
				else
				{
					$startPos=0;
					$endPos=27;
				}
			}
			return $randomPassword;
		}
		
		function sendConsumerLoginEmail()
		{
			$name1="MYCOTOGO - Administrator";
			$to1 = $this->consumerUserEmail;
			$this->consumer_password."<br/>";
			$this->userkey;
			$email1=ADMIN_EMAIL_FROM;
			$e_subject1 = 'MYCOTOGO - User Registration - Activate Your Account';
			$e_content1 ='
			<table>
				<tr>
					<td colspan="2"><b></b></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2"> Welcome !,<br /><br> You have been registered as a  User on the MYCOTOGO website. <br />
					You must activate your account within 24 hours for further access to the system. 
					<br />Please <a href='.URL.'index.php?activateid='.base64_encode($this->lastInsertedId).'&key='.base64_encode($this->userkey).'&type='.$this->usertype_id.'>click here</a> to activate your account. </td>
				</tr>
				
				
				
				<tr>
					<td><br/><b>MYCOTOGO Admin</b></td>
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
				mysqli_query($this->dbconnection,"UPDATE tbl_user SET userstatus_id=1 WHERE user_id='".$this->lastInsertedId."'");
			}
		}
		function getUserType($usertype_id)
		{
			$sqlQuery="select usertype from  enum_usertype where usertype_id='".$usertype_id."'";
			$res=mysqli_query($this->dbconnection,$sqlQuery);
			$row= mysqli_fetch_object($res);
			$usertype= $row->usertype;
			return $usertype;
			
		}
		
		function getUserDetails($user_id)
		{
			$sqlQuery="select * from  tbl_user where user_id='".$user_id."'";
			$res=mysqli_query($this->dbconnection,$sqlQuery);
			$row= mysqli_fetch_object($res);
			return $row;
		}
		
		function selectDocumentLogUser()
		{
			$qryString="Select * from tbl_user, tbl_userstatus where tbl_user.userstatus_id=tbl_userstatus.userstatus_id and tbl_user.isDeleted=0 and usertype_id!='0'  order by username";
			$res=mysqli_query($this->dbconnection,$qryString);
			return $res;
		}
		
		function getUserStatus($user_id)
		{
			$sqlQry="select * from tbl_user USER,tbl_userstatus STATUS where user_id='".$user_id."' and USER.userstatus_id=STATUS.userstatus_id";
			$res=mysqli_query($this->dbconnection,$sqlQry);
			$row=mysqli_fetch_object($res);
			return $row->userstatus;
		}
		
		
		
	}
?>