<?php
 
include_once("private/settings.php");

$open = fopen('template-data_2023-04-11.csv','r');
 
while (!feof($open)) 
{

	$getTextLine = fgets($open);
    // print_r($open);
    //var_dump($getTextLine);
    //die;

	$explodeLine = explode(",",$getTextLine);
    
    // print_r($explodeLine);
    // die;
	list($template_title,$template_description,$user_id,$data_created) = $explodeLine;
	//print_r($explodeLine);
    //die;

	$qry = "insert into csvdata ( template_title,template_description,user_id,data_created) 
    values('".$template_title."','".$template_description."','".$user_id."','".$data_created."')";
	//print_r($qry);
    //die();
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

<!--?php
$CSVfp = fopen('template-data_2023-04-11.csv','r');
if ($CSVfp !== FALSE) {
?>
    <div>
        <table>
            <!-thead>
                <tr>
                    <th>Template Title</th>
                    <th>Template Description</th>
                    <th> Created Date </th>
                </tr>
            </thead>
<!?php
    while (! feof($CSVfp)) {
        $data = fgetcsv($CSVfp, 1000, ",");
        if (! empty($data)) {
            ?>
            <tr >
              
                <td><!?php echo @$data[1]; ?></td>
                <td><!?php echo @$data[2]; ?></td>
                <td><!?php echo @$data[4]; ?></td>
            
            </tr>
 <!?php }?>
<!?php
    }
    ?>
        </table>
    </div>
<!?php
}
fclose($CSVfp);
?-->


