<?php 

if(isset($_GET['id'])){
	$image_id=$_GET['id'];
}

	Class Post
	{
		var $id='';

		var $title='';

		var $description='';

		var $status='';

		var $updated='';

		var $dbconnection='';

		var $created_at='';
		
		function __construct()
		{
			$mysqli_obj = new DataBase();
			$this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		}
		
		function selectPost()
		{
			$qryString = "Select * from tbl_post";
			return mysqli_query($this->dbconnection,$qryString);
		}
		
		function addPost()
		{
			$sqlselect = "Insert into tbl_post set 
            title='".$this->title."',
            description='".$this->description."',
			updated_at ='".$this->updated."',
			created_at=NOW()";

			$addpostdata=mysqli_query($this->dbconnection,$sqlselect);
            return $addpostdata;
		}

		function addTrash()
		{
			$sqlselect = "Insert into tbl_trash set 
            title='".$this->title."',
            description='".$this->description."',
			updated_at ='".$this->updated."',
			created_at=NOW() ";

			$addtrashdata=mysqli_query($this->dbconnection,$sqlselect);
            return $addtrashdata;
		}

		function getPost($image_id)
		{
			$qryString = "Select * from tbl_post where id='".$image_id."'";
			return mysqli_query($this->dbconnection,$qryString);
		}
		
		function editPost()
		{
			$sqlselect = "update tbl_post set 
            title='".$this->title."',
            description='".$this->description."',
			updated_at ='".$this->updated."',
			created_at=NOW()
			where id='".$this->id."'";

			$editpostdata=mysqli_query($this->dbconnection,$sqlselect);
            return $editpostdata;
		}

		function deletePost($image_id)
	    {

		 $sqlQry = "Delete from tbl_post where id='".$image_id."'";

		 $deletepostdata=mysqli_query($this->dbconnection,$sqlQry);

		 return $deletepostdata;

	    }

	}

?>