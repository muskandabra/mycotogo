
<?php

include_once("private/settings.php");

include_once("classes/User.php");


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

    var $token_id = '';
    var $token_date = '';
    
    
    function __construct()
    {
        $mysqli_obj = new DataBase();
        $this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
        $this->userkey 	=	rand(111111,999999);
        // $this->consumer_password	=	$this->generateRandomPassword();
        
    }


		function generate_token($user_id) 
		{
			//echo "coming";
			//die();
			// Generate a random token
			// $token = bin2hex(random_bytes(16));
			$tokenGeneric = SECRET_KEY.$_SERVER["SERVER_NAME"];
			$token = hash('sha256', $tokenGeneric.$user_id);
			
			// Insert the token into the user table
			$query = "update tbl_user SET token_id = '$token' WHERE user_id = '$user_id'";
			$result = mysqli_query($this->dbconnection, $query);
			
			if ($result) {
			  // Return the generated token
			  return $token;
			} else {
			  // Handle the error
			  return false;
			}
		}
		

		  function createToken($user_id)
		  {
			  /* Create a part of token using secretKey and other stuff */
			  $tokenGeneric = SECRET_KEY.$_SERVER["SERVER_NAME"]; // It can be 'stronger' of course
		  
			  /* Encoding token */
			  $token = hash('sha256', $tokenGeneric.$user_id);
		  
			  return array('token' => $token, 'user_id' => $user_id);
		  }


		
		function auth($login, $password)
		{
			// we check user. For instance, it's ok, and we get his ID and his role.
			$userID = 1;
			$userRole = "admin";
		
			// Concatenating data with TIME
			$data = time()."_".$userID."-".$userRole;
			$token = createToken($data);
			echo json_encode($token);
		}

		function checkToken($receivedToken, $receivedData)
		{
		 /* Recreate the generic part of token using secretKey and other stuff */
		 $tokenGeneric = SECRET_KEY.$_SERVER["SERVER_NAME"];

		 // We create a token which should match
		 $token = hash('sha256', $tokenGeneric.$receivedData);   

		 // We check if token is ok !
		 if ($receivedToken != $token)
		{
		 echo 'wrong Token !';
		 return false;
		}

		 list($token_date, $user_id) = explode("_", $receivedData);
		 // here we compare tokenDate with current time using VALIDITY_TIME to check if the token is expired
		 // if token expired we return false

		  // otherwise it's ok and we return a new token
		 return createToken(time()."#".$user_id);   
		 }

         

        }

?>