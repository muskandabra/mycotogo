<?php

include_once("classes/dbHandler.php");

include_once("private/settings.php");

include_once(PATH."classes/clsNotification.php");

class File{

    var $dbconnection =	'';

    function __construct()
	{
		$mysqli_obj = new DataBase();
		$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
	}

    function export()
    {
    $notificationObj= new Notification();

    $notificationObj->user_id = $_SESSION['sessuserid'];

    $selectNotificationTemplate = $notificationObj->selectNotificationTemplate();

    if(mysqli_num_rows($selectNotificationTemplate)>0)
    {
        $filename = "template-data_" . date('Y-m-d') . ".csv"; 					 
        
        $f = fopen('php://memory', 'w');
        $col= array('Consumer Id', 'Template Title', 'Template Description', 'User Id', 'Created Date');
        
        fputcsv($f, $col); 
        $srno=1;
        
		while($row=mysqli_fetch_object($selectNotificationTemplate))
		{ 
		 $srno++;
         $row= json_decode(json_encode($row, true), true); 

         fputcsv($f, $row); 

		}
    
         fseek($f, 0); 
        
         header('Content-type: application/csv');
        
         header('Content-Disposition: attachment; filename="' . $filename . '";'); 
        
         header("Content-Transfer-Encoding: UTF-8");

         ob_end_clean(); //remove extra space

         fpassthru($f); 
    }

    }

function insertcsvdata(){

    $open = fopen('template-data_2023-04-11.csv','r');

    while (!feof($open)) 
    {
        $getTextLine = fgets($open); 
    
        $explodeLine = explode(",",$getTextLine); 
        
        list($template_title,$template_description,$user_id,$data_created) = $explodeLine;
    
        $qry = "insert into csvdata (template_title,template_description,user_id,data_created) 
        values('".$template_title."','".$template_description."','".$user_id."','".$data_created."')";
    
        $data= mysqli_query($this->dbconnection,$qry);

       
    }
     return $data;

    fclose($open); 

}

}

$fileObj= new File();

if(isset($_POST['download']))
{
  
    $fileObj->export();

}

if(isset($_POST['insert']))
{

   $insert=$fileObj->insertcsvdata();

   if($insert)
   {  
         echo "<script>alert('Successfully Inserted');
         window.location.href='templates.php';</script>";  
   }
   else
   {  
         echo '<div class="alert alert-danger" role="alert">
         unsuccessfull to insert data
         </div>';  
   }
    
}

?>