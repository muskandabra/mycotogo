<?php
 
include_once("private/settings.php");

$open = fopen('template-data_2023-04-11.csv','r');



while (!feof($open)) 
//The feof() function checks if the "end-of-file" (EOF) has been reached for an open file.
{

	$getTextLine = fgets($open); //Read one line from the open file:

    // echo $getTextLine;

	$explodeLine = explode(",",$getTextLine); //Break a string into an array
    
	list($template_title,$template_description,$user_id,$data_created) = $explodeLine;

	$qry = "insert into csvdata ( template_title,template_description,user_id,data_created) 
    values('".$template_title."','".$template_description."','".$user_id."','".$data_created."')";

    mysqli_query($dbconnection,$qry);

    if($qry)
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


 
fclose($open);

?>



