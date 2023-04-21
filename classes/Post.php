<?php 

//include_once(PATH."classes/Utility.php");
//include_once(PATH."classes/clsTransaction.php");

	Class Post
	{
		var $user_id;
		var $title;
		var $description;
		var $status;
		var $updated;
		var $dbconnection	=	'';
		var $created_at='';
	
		
		
		function __construct()
		{
			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			
		}
		
		function selectpost()
		{
			$qryString = "Select * from tbl_post where user_id	=	'".$this->user_id."'";
			return mysqli_query($this->dbconnection,$qryString);
		}
		
		function addPost()
		{
			
				$sqlselect="Insert into tbl_post set title='".$this->title."',
                description='".$this->description."',
				status ='".$this->status."',
				updated_at ='".$this->updated."',
				created_at=NOW()";
				$resselect=mysqli_query($this->dbconnection,$sqlselect);
				
                return $resselect;
				
		}
		
		
		function editPost()
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