<div id="box" style="display:none;" class="lightbox-section">
  <span id="boxtitle"></span>
<div id="error" style="color:#C03; display:none;"> Please Provide Folder Name. </div>
  <form method="POST" action="showdetails.php?n=<?php echo $_GET['n']; ?>" target="_parent" onsubmit="return validation();" id="addholders">
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
  <script>
	function validation()
	{
		var name=document.getElementById('notebookName').value;
		if(name!='')
		{
			var query	=	'task=addfolders&'+$('#addholders').serialize();
			var URL =	$('#viewUrl1').val();
			$.ajax
			({
				type: "POST",
				url: URL+'includes/bookInfo.php',
				data: query,
				success: function(response)
				{
					showfolders();
					document.getElementById('shadowing').style.display='none';
					document.getElementById('box').style.display='none';
				}
			});
			return false;
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
  
	function showfolders()
	{
		var prodId 	=	getParameterByName('n');
		var URL	=	$('#viewUrl1').val();
		var task =	'showRecordFolders';
		var query	= "task="+task+"&consumer_id="+prodId;
		$.ajax
		({
			type: "POST",
			url: URL+'includes/bookInfo.php',
			data: query,
			success: function(response)
			{
				jQuery('.note-single').html(response);
			}
		});
		
	}
  
  </script>