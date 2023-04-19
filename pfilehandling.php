<!--?php
$myfile = fopen("template-data_2023-04-11.csv", "r") or die("Unable to open file!");

echo fread($myfile,filesize("template-data_2023-04-11.csv"));
// read all file
fclose($myfile);
?-->


<!--?php
$myfile = fopen("template-data_2023-04-11.csv", "r") or die("Unable to open file!");
echo fgets($myfile); //for  reading single line

fclose($myfile);
?-->


<!--?php
$myfile = fopen("template-data_2023-04-11.csv", "r") or die("Unable to open file!");

// Output one line until end-of-file
while(!feof($myfile)) {
  echo fgets($myfile) . "<br>";
}

fclose($myfile);
?-->


<?php
$myfile = fopen("template-data_2023-04-11.csv", "r") or die("Unable to open file!");

// Output one character until end-of-file
while(!feof($myfile)) {
  echo fgetc($myfile); //read a single character from a file
}

fclose($myfile);
?>

<!--?php
$myfile = fopen("template-data_2023-04-11.csv", "w") or die("Unable to open file!");
$txt = "John Doe\n";
fwrite($myfile, $txt);
$txt = "Jane Doe\n";
fwrite($myfile, $txt);
fclose($myfile);
?-->