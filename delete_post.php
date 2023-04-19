<?php
include_once("private/settings.php");
include_once("classes/clsPost.php");


$postObj= new Post();
$res=$postObj->getPost($image_id);

	if(isset($_GET['id']) && $_GET['id']!='')
	{

		$id	= $_GET['id'];

		$postObj->id = $id;

		$postObj->deletePost($id);

		$res=$postObj->getPost($image_id);

        $rows=mysqli_fetch_object($res);
		
		

		
		$postObj->title = $title;

		$postObj->description = $_POST['description'];

		$postObj->updated = $_POST['updated_at'];

		$postObj->created_at = $_POST['created_at'];

		$postObj->addTrash();

		//print "<script>window.location='manage_post.php?msg=del'</script>";

	}


?>