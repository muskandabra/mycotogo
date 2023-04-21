<?php
     class Thumbnail
	 {
	   var $width="";
	   var $height="";
	   var $imageName="";
	   var $saveName="";
	   var $imageTempName="";
	   var $extension="";
	   var $id="";
	   var $saveNameNew="";
	   
	   function Thumbnail($path,$imageObject,$width,$height,$saveName,$id)
	     {
		    $this->path	=	$path;
			$this->imageName	=	$imageObject['name'];
			$this->imageTempName	=	$imageObject['tmp_name'];
			$this->width	=	$width;
			$this->height	=	$height;
			$this->saveName	=	$saveName;
			$this->id	=	$id;
		 }
	   function getExtension()
	    {
		   $this->extension	=	substr($this->imageName,strpos($this->imageName,"."));
		   return $this->extension;
		}	 
	   function createThumbnail()
	   {
	     $checkExt=$this->getExtension($this->imageName);
		 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
		  {
		    if(!file_exists($this->path))
			  {
			    mkdir($this->path,0777);
			  }
			
			$imageNew	=	$this->path."Image".$this->id.$this->extension;
			
			if(copy($this->imageTempName,$imageNew))
			  {
			    $i = imagecreatefromjpeg($imageNew);
				$this->saveNameNew	=	$this->path.$this->saveName.$this->id.$this->extension;
				$this->generateImage($imageNew, imagesx($i), imagesy($i), $this->saveNameNew);
			  }
			 return "yes"; 
		  }
		  elseif($checkExt==".gif" || $checkExt==".GIF")  
		  {
		    if(!file_exists($this->path))
			  {
			    mkdir($this->path,0777);
			  }
			
			$imageNew	=	$this->path."Image".$this->id.$this->extension;
			
			if(copy($this->imageTempName,$imageNew))
			  {
			    	$i = imagecreatefromgif($imageNew);
				$this->saveNameNew	=	$this->path.$this->saveName.$this->id.$this->extension;
				$this->generateImage($imageNew, imagesx($i), imagesy($i), $this->saveNameNew);
			  }
			 return "yes"; 
		  }
		 else
		  {
		    return "no";
		  } 
	   }
	   	   
	   function generateImage($iname, $old_x, $old_y, $saveAs)
				{
			    $new_w=$this->width;
				$new_h=$this->height;
				
					//===============================
				 $checkExt=$this->getExtension($this->imageName);
				 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
					$i = imagecreatefromjpeg($iname);
				else
					$i = imagecreatefromgif($iname);
				
				$old_x=imagesx($i);
				$old_y=imagesy($i);
				
				/*
				if ($old_x > $old_y) {
				$new_iw=$new_w;
				$new_ih=$old_y*($new_h/$old_x);
				}
				if ($old_x < $old_y) {
				$new_iw=$old_x*($new_w/$old_y);
				$new_ih=$new_h;
				}
				if ($old_x == $old_y) {
				$new_iw=$new_w;
				$new_ih=$new_h;
				}
				*/
				
				 $ratio_img=$old_x/$old_y;
				 $ratio_max=$new_w/$new_h;
				
				
				if ($ratio_img>=$ratio_max)
				{
				$new_iw=$new_w;
				$new_ih=$new_iw/$ratio_img;
				
				
				}
				
				if ($ratio_img<$ratio_max)
				{
				 $new_ih=$new_h;
				$new_iw=$new_ih*$ratio_img;
				
				}
				
				
				//print $new_ih;
				//print $new_iw;
					
				
				
				
				//===========
				$dst_img=imagecreatetruecolor($new_iw,$new_ih);
				imagecopyresampled($dst_img,$i,0,0,0,0,$new_iw,$new_ih,$old_x,$old_y); 
				//============================
				 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
					imagejpeg($dst_img, $saveAs);
				else
					imagegif($dst_img, $saveAs);
					
			}
		
		
		function createThumbnailFix()
	   {
	     $checkExt=$this->getExtension($this->imageName);
		 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
		  {
		    if(!file_exists($this->path))
			  {
			    mkdir($this->path,0777);
			  }
			
			$imageNew	=	$this->path."Image".$this->extension;
			
			if(copy($this->imageTempName,$imageNew))
			  {
			    	$i = imagecreatefromjpeg($imageNew);
				$this->saveNameNew	=	$this->path.$this->saveName.$this->id.$this->extension;
				$this->generateImageFix($imageNew, imagesx($i), imagesy($i), $this->saveNameNew);
			  }
			 return "yes"; 
		  }
		  elseif($checkExt==".gif" || $checkExt==".GIF")  
		  {
		    if(!file_exists($this->path))
			  {
			    mkdir($this->path,0777);
			  }
			
			$imageNew	=	$this->path."Image".$this->extension;
			
			if(copy($this->imageTempName,$imageNew))
			  {
			    	$i = imagecreatefromgif($imageNew);
				$this->saveNameNew	=	$this->path.$this->saveName.$this->id.$this->extension;
				$this->generateImageFix($imageNew, imagesx($i), imagesy($i), $this->saveNameNew);
			  }
			 return "yes"; 
		  }
		 else
		  {
		    return "no";
		  } 
	   }
			function generateImageFix($iname, $old_x, $old_y, $saveAs)
				{
				$new_w=$this->width;
				$new_h=$this->height;
				
				//===============================
			 $checkExt=$this->getExtension($this->imageName);
			 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
				$i = imagecreatefromjpeg($iname);
			else
				$i = imagecreatefromgif($iname);
				$old_x=imagesx($i);
				$old_y=imagesy($i);
				if ($old_x > $old_y) {
				$new_iw=$new_w;
				$new_ih=$old_y*($new_h/$old_x);
				}
				if ($old_x < $old_y) {
				$new_iw=$old_x*($new_w/$old_y);
				$new_ih=$new_h;
				}
				if ($old_x == $old_y) {
				$new_iw=$new_w;
				$new_ih=$new_h;
				}
				$new_iw=$this->width;
				$new_ih=$this->height;
				//===========
				$dst_img=imagecreatetruecolor($new_iw,$new_ih);
				imagecopyresampled($dst_img,$i,0,0,0,0,$new_iw,$new_ih,$old_x,$old_y); 
				//============================
				 if($checkExt==".jpg" || $checkExt==".jpeg" || $checkExt==".JPG" || $checkExt==".JPEG")  
					imagejpeg($dst_img, $saveAs);
				else
					imagegif($dst_img, $saveAs);
				}
		
		function updateTable($field,$table,$idField)
		  {
		     global $db;
			 $image=$this->saveName.$this->id.$this->extension;
			 $sqlQuery	=	"UPDATE ".$table." set
				                      ".$field."='".$image."' where ".$idField."='".$this->id."'";
									  
		
			 						  
			 $db->query($sqlQuery);						  
		  }			
	    
		function uploadImage($path,$imageObject,$saveName,$id)
		 {
		    if(!file_exists($path))
			  {
			    mkdir($path,0777);
			  }
		    $ext=$this->getExtension($imageObject['name']); 
		    if(!copy($imageObject['tmp_name'],$path.$saveName.$id.$ext))
			 {
			   return "no"; 
			 }
			 else
			  {
				return "yes";
			  }
		 }	
	 }
?>
