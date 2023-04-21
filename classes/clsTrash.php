

<?php 

if(isset($_GET['id'])){
	$image_id=$_GET['id'];
}

	Class Trash
	{
		var $id='';

        var $dbconnection='';

		var $title='';

        var $description='';

		var $updated='';

		var $created_at='';

		
		function __construct()
		{
			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		}
		
	
   
		function getItem()
		{
			$qryString = "Select * from tbl_trash ";
			return mysqli_query($this->dbconnection,$qryString);
                  
		}
        

		function deleteItem($image_id)
	    {

		 $sqlQry = "Delete from tbl_trash where id='".$image_id."'";

		 $deleteitem=mysqli_query($this->dbconnection,$sqlQry);

		 return $deleteitem;

	    }

        function emptyTrash()
	    {

		 $sqlQry = "Delete from tbl_trash ";

		 $emptyTrash=mysqli_query($this->dbconnection,$sqlQry);

		 return $emptyTrash;

	    }
   
	}

?>