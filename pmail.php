<html>
<head>
</head>
<body>
 <form id="contact" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"> 
  <table class="contact" width="400"  cellspacing="2" cellpadding="0"> 
    <tr>
      <td >Your name:</td> 
      <td ><input name="name" type="text" id="name" size="32"></td> 
    </tr>
    <tr> 
      <td>Email address:</td> 
      <td><input name="email" type="text" id="email" size="32"></td> 
    </tr>
    <tr> 
      <td>Comment:</td> 
      <td>
        <textarea name="comment" cols="45" rows="6" id="comment" ></textarea>
      </td> 
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;">
        <input type="submit"  value="Submit" style="padding: 3px 22px;" />
      </td>
    </tr>
  </table> 
</form>
<?php
   if(isset($_POST))
   {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $ToEmail = 'muskan.netzoptimize@gmail.com';
      $EmailSubject = 'Site contact form '; 
      $mailheader = "From: ".$_POST["email"]."\r\n"; 
      $mailheader .= "Reply-To: ".$_POST["email"]."\r\n"; 
      $mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
      $MESSAGE_BODY = "Name: ".$_POST["name"]."<br>"; 
      $MESSAGE_BODY .= "Email: ".$_POST["email"]."<br>"; 
      $MESSAGE_BODY .= "Subject:".$_POST['subject']."<br />";  
      $MESSAGE_BODY .= "Comment: ".nl2br($_POST["comment"])."<br>"; 
      if(mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader))
       {
        echo "Success ";
       }
      else
      {
       echo "Failure";
      }
   }
 ?>
</body>
</html>