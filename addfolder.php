<?php $objFolder= new Folder();
  if(isset($_POST['addnewfolder']) && $_POST['addnewfolder']!='')
  {
	$value = $_POST['consumer_id'];
	$bool = ( !is_int($value) ? (ctype_digit($value)) : true );
	if($bool!='')
		$consumer_id=$_POST['consumer_id'];
	else
		$consumer_id=base64_decode($_POST['consumer_id']);
	
	$objFolder->sys_folder_name=$_POST['notebookName'];
	$objFolder->sys_folder_description=$_POST['description'];
	$objFolder->consumer_id=$consumer_id;
	$objFolder->sys_folder_type='folder';
	$objFolder->permission='V,A,E,D';
	$objFolder->permission='V,A,E,D';
	$objFolder->user_id=$_SESSION['sessuserid'];
	$objFolder->uploadtype='manual';
	$objFolder->isAutomatic='0';
	$objFolder->addFolder();
  }
  ?>
<div id="box" style="display:none;" class="lightbox-section">
  <span id="boxtitle"></span>
  <script>
  function validation()
  {
	var name=document.getElementById('notebookName').value;
	if(name!='')
	{
		return true;
	}
	else
	{
		if(name=='')
		{
			$('#error').show();
		}
		return false;
	}
  }
  
  </script>
  
  <div id="error" style="color:#C03; display:none;"> Please Provide Folder Name. </div>
  <form method="POST" action="showdetails.php?n=<?php echo $_GET['n']; ?>" target="_parent" onsubmit="return validation();">
  <input type="hidden" name="consumer_id" value="<?php echo $_GET['n']; ?>"/>
		<p>Name <span class="required">*</span>
			<input type="text" name="notebookName" value="" id="notebookName" maxlength="60" size="60">
		</p>
		<p>Description 
			<textarea style="height: 39px; width: 250px; border: 1px solid #ccc;" id="description" name="description"></textarea>
		</p>
		<div class="popup-submit"> 
			<input type="submit" name="addnewfolder" value="Submit" class="send">
			<input type="button" name="cancel" value="Cancel" onClick="closebox()" class="cancel">
		</div>
  </form>
</div>