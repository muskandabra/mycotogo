<?php 
 
// Load the database configuration file 
include_once("private/settings.php");

include_once(PATH."classes/clsNotification.php");

$notificationObj= new Notification();

$notificationObj->user_id = $_SESSION['sessuserid'];

$selectNotificationTemplate = $notificationObj->selectNotificationTemplate();

    if(mysqli_num_rows($selectNotificationTemplate)>0)
    {
        //$delimiter = ","; 
        $filename = "template-data_" . date('Y-m-d') . ".csv"; 
        //ob_end_clean();							 
        // Create a file pointer 
        $f = fopen('php://memory', 'w');
        $col= array('Consumer Id', 'Template Title', 'Template Description', 'User Id', 'Created Date');
        //$col=$selectNotificationTemplate->getStyle($array)->getFont()->setBold(true);
        fputcsv($f, $col); //The fputcsv() function formats a line as CSV and writes it to an open file. 
        $srno=1;
        
		while($row=mysqli_fetch_object($selectNotificationTemplate))
		{ 
      
		 $srno++;
         $row= json_decode(json_encode($row, true), true); 
         //convert json object to php object

         fputcsv($f, $row); 

		}
    
         fseek($f, 0); 
         // The fseek() function seeks in an open file.

         // This function moves the file pointer from its current position to a new position, 
         // forward or backward, specified by the number of bytes.

         // Set headers to download file rather than displayed 
         header('Content-type: application/csv');
         // Set the file name option to a filename of your choice.
         header('Content-Disposition: attachment; filename="' . $filename . '";'); 
         // Set the encoding
         header("Content-Transfer-Encoding: UTF-8");

          //output all remaining data on a file pointer 
          fpassthru($f); 

    }
      
?>




