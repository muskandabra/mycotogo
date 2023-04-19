<?php
  session_start();
// index includes
include_once("private/settings.php");

include_once("classes/User.php");

include_once("classes/clsConsumer.php");

//include('debug.php');


//classes initialliation
$msg='';

$objUser = new User();

$objConsumer=new Consumer();


//BEGIN STATIC SESSION INIT


// if(isset($_POST['actionprocess']) && $_POST['actionprocess']=="login_do")

// { 

    // $username = "assistant@corporatelegals.com";

    // $pass = "a3G98Sw";

	$username = "bobby@mycotogo.com";

    $pass = "8c1830fd30b6a1e9a3500fe171831ccc";

	echo "Received <br>";

	

	// if(isset($_POST['username']) && isset($_POST['username']) && $_POST['password']!="" && $_POST['password']!="")

	// {

	echo $username. "<br> " . $pass . "<br>";

	

		$objUser->useremail = $username;

		$objUser->password = $pass;

		$objUser->usertype ='paralegal';

		$resselect= $objUser->checkLogin(); 

         print_r($resselect);

		if(mysqli_num_rows($resselect)>0)

		{

			

			$row=mysqli_fetch_object($resselect);

			if($row->userstatus_id=='2')

			{

				$msg="Your Account has been deactivated";

				$objUtility = new Utility();

				$objUtility->dataTable = '';

				$objUtility->usertype='';

				$objUtility->action='Inactive';

				$objUtility->description= 'Logged In Denied of Username:['.$_POST['username'].'] || With Password: ['.$_POST['password'].']';

				$objUtility->logTrack();

			}

			else

			{
                print_r($row);

              

				$_SESSION['sessuserid']=$row->user_id;

				$_SESSION['sessusername']=$row->username;

				$_SESSION['sessfirstname']=$row->firstname;

				$_SESSION['sesslastname']=$row->lastname;

				$_SESSION['sessuseremail']=$row->useremail;

				$_SESSION['sessuserpswd']=$row->password;

				$_SESSION['sessrights']=$row->usertype_id;

				//console_log($_SESSION['sessuserid']);

					//echo $_SESSION['sessrights'];
				// echo $row->usertype_id;
				//die;

				$select=mysqli_query($dbconnection,"select * from  enum_usertype where usertype_id='".$_SESSION['sessrights']."'");

				if(mysqli_num_rows($select)>0)

				{

					$fetch=mysqli_fetch_object($select);

					$_SESSION['usertype']=$fetch->usertype;

					

				

				}

				$objUtility = new Utility();

				$objUtility->dataTable = 'tbl_user';

				$objUtility->datatableidField ='user_id';

				$objUtility->dataId=$_SESSION['sessuserid'];

				$objUtility->user_id=$_SESSION['sessuserid'];

				$objUtility->usertype =$_SESSION['usertype'];

				$objUtility->action='Logged In';

				$objUtility->description= 'Logged In Username:'.$_SESSION['sessuseremail'].' || with Password:['.$_SESSION['sessuserpswd'].']';

				$objUtility->logTrack();

				if(isset($_POST['task']) && $_POST['task']=='video')

				{

					print "<script>window.location='videotour.php'</script>";

				}

				else if(isset($_POST['task']) && $_POST['task']=='nextstep')

				{
					
					print "<script>window.location='nextstep.php'</script>";

				}

				else

				{

					

					$objUser->user_id = $_SESSION['sessuserid'];

					$numRows	=	$objUser->UserCountBook();

					//if(mysqli_num_rows($numRows) == 1 || $_SESSION['usertype'] == 7)

					if($_SESSION['usertype'] == 7)

					{

						print "<script>window.location='userbook.php'</script>";

					}

					else
                    $msg='';

                    $objUser = new User();
                    
                    $objConsumer=new Consumer();
						print "<script>window.location='dashboard.php'</script>";
                        print "<br> redirect <br>";
				}

			}



		}

		else

		{

			$objUtility = new Utility();

			$objUtility->dataTable = '';

			$objUtility->usertype ='';

			$objUtility->action='Trying to Log In';

			$objUtility->description= 'WARNING: HACKING for Username: ['.$username.'] with Password:['.$pass.']';

			$objUtility->logTrack();

			$msg="Please enter correct credentials";

		}

	// }

// }

echo @$_GET['msg']."<br>";
echo $msg;
?>

