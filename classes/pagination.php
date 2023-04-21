<?php



  
   class pagingRecords
    {
	   var $pageQuery="";
	   var $maxRecords="";
	   var $setWhere="";
	   var $totalPages="";
	   var $tableName="";
	   var $pageNumber="";
	   var $setLimit="";
	   var $queryString="";
	   var $queryStringMultiple="";
	   var $pageQueryLimit="";
	   
	   // function to set the name of the table on which the query will be executed
	   function setTableName($tableName)
	    {
		  $this->tableName=$tableName;
		}
		
		// function to provide which page number is currently the user looking into from the total pages formed by setting some particular records per page
		function setPageNumber($pageNo)
		 {
		   $this->pageNumber=$pageNo;
		   if($this->pageNumber=="")
		     $this->pageNumber=1;
		 }
		
		// function to be used if user want to set the Where clause of the sql query, separately.
		function setWhere($setwhere)
		 {
		   $this->setWhere=$setwhere;
		 } 
		
		// function tell about the maximum number of records to be displayed on a page
		function setMaxRecords($maxRecords)
		 {
		   $this->maxRecords=$maxRecords;
		 } 
		
		// function sets the query to be executed in accordance with the different parameters for pagination process 
		function setQuery($query)
		 {
		   global $_GET;
		   if(!isset($_GET['pageNo']))	
		    $this->pageNumber=1;
		   else
			{
				$this->pageNumber=$_GET['pageNo'];
			}
			$offset = ($this->maxRecords * ($this->pageNumber - 1));
				$limit= $this->maxRecords;
		   		   
		   $this->setLimit=" limit ".$offset.", ".$limit." ";
		   
		   $this->pageQuery=$query;
		   
		   $this->pageQueryLimit=$query.$this->setLimit;
		   
		   return $this->pageQueryLimit;
		 } 
		 
		 
		 //function displays the links to access all the pages formed due to application of the pagination query
		 function displayLinksFront()
		  {
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
			
			if($key	!="pageNo"){
				$this->queryString.="&";
				$this->queryString.=$key."=";
				$this->queryString.=$value;}
			}
			if (strlen($this->queryString)>0 )
			 {
				$this->queryStringMultiple=substr($this->queryString,1);
			}	
			
			
			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<table border='0' width='100%'>";
			$s.="<tr>";
			
			
			if($this->pageNumber!=1)
			{
				$s.="<th align='left'>";
				$s.="<a href='?".$this->queryString."&pageNo=".$prevRec."' class=copyblue13b><img src='".URL."images/btnPrevious.jpg' border='0' alt='' /></a>"; 
				$s .= "</th>";
			}
			
			
			/*
			for($pageCounter=1;$pageCounter<=$this->totalPages;$pageCounter++)
			 {
			   if($pageCounter==$this->pageNumber)
			   {
			     $s.="<span class='yellow' style='text-decoration:underline;font-family:Arial;'>".$pageCounter."</span>&nbsp;";			
				  if ($pageCounter%35==0)
				 $s.="<br>";		
				}
			   else
			   {
			   	
			     $s.="<a href='?".$this->queryStringMultiple."&pageNo=".$pageCounter."' style='text-decoration:none;font-family:Arial;' class='style1'>".$pageCounter."</a>&nbsp;";
				 if ($pageCounter%35==0)
				$s.="<br>";			
				 
				 }
			 }
			*/
			 if($this->pageNumber!=$this->totalPages)
			{
				$s.="<th align='right'>";
				$s.="<a href='?".$this->queryString."&pageNo=".$nextRec."' class=copyblue13b><img src='".URL."images/btnNext.jpg' border='0' alt='' /></a>";
				$s .= "</th>";
			}
			
			$s.="</tr>";
			$s.="</table>";
			
			if($this->totalPages > 1)
			 {
			   print $s;
			 }
		  }
		  
		 function displayLinks_Front()
		  {
		  	global $currentTemplate;
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
				if($key	!="pageNo"){
					$this->queryString.="&";
					$this->queryString.=$key."=";
					$this->queryString.=$value;}
				}
			
			if (strlen($this->queryString)>0 )
			$this->queryString=substr($this->queryString,1);

			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<div align='center'>";
			
			
			if($this->pageNumber!=1)
			{
				$s.="<a href='?".$this->queryString."&pageNo=1' class=browntext12link>First</a> &nbsp;
			      <a href='?".$this->queryString."&pageNo=".$prevRec."' class=browntext12link>Prev</a>&nbsp;|&nbsp;&nbsp;&nbsp;"; 
			}
			
			$s .= "<span class='darkgrey12'>Pages :</span> ";
			
			//-----changes made by rohit bhatia 6 june----------
			
			 $pageMultiple=ceil($this->pageNumber/9);
			 $totalPages=10*$pageMultiple;
			if ($totalPages>$this->totalPages)
			$totalPages=$this->totalPages;
			
			if ($pageMultiple>1)
			$ctrVal=$totalPages-10;
			else
			$ctrVal=$totalPages-9;
			
			if($ctrVal<=0)
			{
				$ctrVal=1;
			}
			//--------------end changes----------
			for($pageCounter=$ctrVal;$pageCounter<=$totalPages;$pageCounter++)
			 {
			 	
			   if($pageCounter==$this->pageNumber)
			     $s.="<font class=browntext12link style='color:#69841C;'>".$pageCounter."</font>&nbsp;";			
			   else
			     $s.="<a href='?".$this->queryString."&pageNo=".$pageCounter."' class='browntext12link'>".$pageCounter."</a>&nbsp;";			
			 }
			 
			 if($this->pageNumber!=$this->totalPages)
			 {
				$s.="&nbsp;&nbsp;|&nbsp;<a href='?".$this->queryString."&pageNo=".$nextRec."' class=browntext12link>Next</a>&nbsp;&nbsp;
			      <a href='?".$this->queryString."&pageNo=".$this->totalPages."' class=browntext12link>Last</a>&nbsp;&nbsp;";
			}
			
		    
			$s.="</div>";
			
			if($this->totalPages > 1)
			 {
			   print $s;
			 }
			
		  }
		  
		 function displayLinks_Front_new()
		  {
				global $currentTemplate;
			  //db connection
			   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
			  
				global $_GET;
				
			
				 while(list($key, $value) = each($_GET))
				{
					if($key	!="pageNo"){
						$this->queryString.="&";
						$this->queryString.=$key."=";
						$this->queryString.=$value;}
					}
				
				if (strlen($this->queryString)>0 )
				$this->queryString=substr($this->queryString,1);
				
				
				$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
				$totalRecs=mysqli_num_rows($resultRes);
				
				if($totalRecs > 0 )
				 {
				   $this->totalPages=ceil($totalRecs/$this->maxRecords);
				 }
			
				$prevRec=$this->pageNumber-1;
				$nextRec=$this->pageNumber+1;
				if($prevRec<=0)
				  $prevRec="";
				if($nextRec > $this->totalPages)
				  $nextRec=$this->totalPages;
				  
			//	$s="<div class='pages'>";
				
				
				if($this->pageNumber!=1)
				{
					
					$s="<span class='manage_title'>Page: &nbsp;<strong>".$this->pageNumber." of ".$this->totalPages."</strong></span><a href='?".$this->queryString."&pageNo=".$prevRec."' class='link_prev'>Previous</a><a href='#' class='link_next'></a>";
					
					
//					$s.="<a href='?".$this->queryString."&pageNo=1' class=browntext12link>First</a> &nbsp;
//					  <a href='?".$this->queryString."&pageNo=".$prevRec."' class=browntext12link>Prev</a>&nbsp;|&nbsp;&nbsp;&nbsp;"; 
				}
				

				
								
				//$s .= "<span class='darkgrey12'>Pages :</span> ";
				
				//-----changes made by rohit bhatia 6 june----------
				
				 $pageMultiple=ceil($this->pageNumber/9);
				 $totalPages=10*$pageMultiple;
				if ($totalPages>$this->totalPages)
				$totalPages=$this->totalPages;
				
				if ($pageMultiple>1)
				$ctrVal=$totalPages-10;
				else
				$ctrVal=$totalPages-9;
				
				if($ctrVal<=0)
				{
					$ctrVal=1;
				}
				//--------------end changes----------
				//for($pageCounter=$ctrVal;$pageCounter<=$totalPages;$pageCounter++)
				// {
					
				  // if($pageCounter==$this->pageNumber)
					// $s.="<font class=browntext12link style='color:#69841C;'>".$pageCounter."</font>&nbsp;";			
				  // else
					// $s.="<a href='?".$this->queryString."&pageNo=".$pageCounter."' class='browntext12link'>".$pageCounter."</a>&nbsp;";			
				// }
				if($this->pageNumber==1)
				{
					
					$s="<span class='manage_title'>Page: &nbsp;<strong>".$this->pageNumber." of ".$this->totalPages."</strong></span><a  href='#' class='link_prev'>Previous</a><a href='?".$this->queryString."&pageNo=".$nextRec."' class='link_next'></a>";
					
					
//					$s.="<a href='?".$this->queryString."&pageNo=1' class=browntext12link>First</a> &nbsp;
//					  <a href='?".$this->queryString."&pageNo=".$prevRec."' class=browntext12link>Prev</a>&nbsp;|&nbsp;&nbsp;&nbsp;"; 
				}elseif($this->pageNumber!=$this->totalPages)
				 {
					
					$s="<span class='manage_title'>Page: &nbsp;<strong>".$this->pageNumber." of ".$this->totalPages."</strong></span><a href='?".$this->queryString."&pageNo=".$prevRec."' class='link_prev'>Previous</a>
					<a href='?".$this->queryString."&pageNo=".$nextRec."' class='link_next'></a>";
					//$s.="&nbsp;&nbsp;|&nbsp;<a href='?".$this->queryString."&pageNo=".$nextRec."' class=browntext12link>Next</a>&nbsp;&nbsp;
					//  <a href='?".$this->queryString."&pageNo=".$this->totalPages."' class=browntext12link>Last</a>&nbsp;&nbsp;";
				}
				
				
				//$s.="</div>";
				
				if($this->totalPages > 1)
				 {
				   print $s;
				 }
			
		  }		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		  
		 function displayLinks()
		  {
		  	global $currentTemplate;
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
				if($key	!="pageNo"){
					$this->queryString.="&";
					$this->queryString.=$key."=";
					$this->queryString.=$value;}
				}
			
			if (strlen($this->queryString)>0 )
			$this->queryString=substr($this->queryString,1);
			
			
			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<tr><td align='center'><table border='0' style='text-align:center;'  align='center' width='100%'>";
			$s.="<tr>";
			$s.="<th class='pagination'>";
			$s.="<table align='center' cellspadding='0' cellspacing='3'>";
			$s.="<tr>";		
			if($this->pageNumber!=1)
			{
				$s.="<td><a href='?".$this->queryString."&pageNo=".$prevRec."' class=pagination-active></a></td>"; 
			}
			
			
			
			
			//-----changes made by rohit bhatia 6 june----------
			
			 $pageMultiple=ceil($this->pageNumber/9);
			 $totalPages=10*$pageMultiple;
			if ($totalPages>$this->totalPages)
			$totalPages=$this->totalPages;
			
			if ($pageMultiple>1)
			$ctrVal=$totalPages-10;
			else
			$ctrVal=$totalPages-9;
			
			if($ctrVal<=0)
			{
				$ctrVal=1;
			}
			//--------------end changes----------
			for($pageCounter=$ctrVal;$pageCounter<=$totalPages;$pageCounter++)
			 {
			 	
			   if($pageCounter==$this->pageNumber)
			     $s.="<td class='numberis'>".$pageCounter."</td>&nbsp;";			
			   else
			     $s.="<td class='numberis'><a href='?".$this->queryString."&pageNo=".$pageCounter."' class='purple'>".$pageCounter."</a></td>";			
			 }
			 
			 if($this->pageNumber!=$this->totalPages)
			 {
				$s.="<td><a href='?".$this->queryString."&pageNo=".$nextRec."' class='pagination-active'></a></td>
			      ";
			}
			
			$s.="</tr>";	
			$s.="</table>";
		    $s.="</th>"; 
			$s.="</tr>";
			$s.="</table></td></tr>";
			
			if($this->totalPages > 1)
			 {
			   print $s;
			 }
			
		  }
		  
		  //DISPLAY LINKS FUNCTION FOR FRONT STARTS HERE
		  function displayLinksRed()
		  {
		  	global $currentTemplate;
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
				if($key	!="pageNo"){
					$this->queryString.="&";
					$this->queryString.=$key."=";
					$this->queryString.=$value;}
				}
				$newQuery=$this->queryString;
			if (strlen($this->queryString)>0 )
			$this->queryString=substr($this->queryString,1);
			
			
			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<tr><td align='center'><table border='0' style='text-align:center;font-size:13px;' background='".URL.FOLDER_ADMIN.FOLDER_ADMIN_TEMPLATES.$currentTemplate."/images/background.jpg"."' align='center' width='100%'>";
			$s.="<tr>";
			$s.="<td>";
							
			if($this->pageNumber!=1)
			{
				//<a href='?".$this->queryString."&pageNo=1' class=pagenumber>First</a> &nbsp;
				$s.="<a href='?".$this->queryString."&pageNo=".$prevRec."' class=pagenumber>Previous </a>&nbsp;"; 
			}
			
			//$s .= "Pages : ";
			
			//-----changes made by rohit bhatia 6 june----------
			
			 $pageMultiple=ceil($this->pageNumber/9);
			 $totalPages=10*$pageMultiple;
			if ($totalPages>$this->totalPages)
			$totalPages=$this->totalPages;
			
			if ($pageMultiple>1)
			$ctrVal=$totalPages-10;
			else
			$ctrVal=$totalPages-9;
			if($ctrVal<=0)
			{
				$ctrVal=1;
			}
			//--------------end changes----------
			for($pageCounter=$ctrVal;$pageCounter<=$totalPages;$pageCounter++)
			 {
			 	
			   if($pageCounter==$this->pageNumber)
			     $s.="<span style='font-size:13px;'>| ".$pageCounter."</span>&nbsp;";			
			   else
			     $s.="<a href='".URL."index.php?".$this->queryString."&pageNo=".$pageCounter."' style='text-decoration:none;font-family:Arial;font-size:13px;' class='pagenumber'>| ".$pageCounter." </a>&nbsp;";			
			 }
			 
			 if($this->pageNumber!=$this->totalPages)
			 {
				$s.="|&nbsp;<a href='".URL."index.php?".$this->queryString."&pageNo=".$nextRec."' class=pagenumber>Next</a>&nbsp;&nbsp;";
				//<a href='?".$this->queryString."&pageNo=".$this->totalPages."' class=pagenumber>Last</a>&nbsp;&nbsp;
			}
			
		    $s.="</td>"; 
			$s.="</tr>";
			$s.="</table></td></tr>";
			
			if($this->totalPages > 1)
			 {
			   print $s;
			 }
			  $this->queryString="";
			 $this->queryString=$newQuery;
		  }
		  
		  //DISPLAY LINKS FUNCTION FOR FRONT ENDS HERE
		  
		   function displayLinksForImageOfDay()
		  {
		  	
			
			
			global $currentTemplate;
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
			if($key	!="pageNo"){
				$this->queryString.="&";
				$this->queryString.=$key."=";
				$this->queryString.=$value;}
			}
			if (strlen($this->queryString)>0 )
			$this->queryString=substr($this->queryString,1); 
			
			
			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<tr><td align='center'><table border='0' style='text-align:center;font-size:12px;' background='".URL.FOLDER_ADMIN.FOLDER_ADMIN_TEMPLATES.$currentTemplate."/images/background.jpg"."' align='center' width='100%'>";
			$s.="<tr>";
			$s.="<td>";
			
			if($this->pageNumber!=1)
			{
				$s.="<a href='?".$this->queryString."&pageNo=".$prevRec."' class=sidelinks style=text-decoration:none>Previous</a>&nbsp;|&nbsp;"; 
			}
			
			//-----changes made by rohit bhatia 6 june----------
			
			 $pageMultiple=ceil($this->pageNumber/9);
			 $totalPages=10*$pageMultiple;
			if ($totalPages>$this->totalPages)
			$totalPages=$this->totalPages;
			
			if ($pageMultiple>1)
			$ctrVal=$totalPages-10;
			else
			$ctrVal=$totalPages-9;
			if($ctrVal<=0)
			{
				$ctrVal=1;
			}
			//--------------end changes----------
			
			//for($pageCounter=1;$pageCounter<=$this->totalPages;$pageCounter++)
			for($pageCounter=$ctrVal;$pageCounter<=$totalPages;$pageCounter++)
			{
				
			 	$pgsecond=$pageCounter*10;	
				$pgfirst=$pgsecond-9; 	
			   if($pageCounter==$this->pageNumber){
							  
			     $s.="<span style='font-size:12px;'>".$pgfirst."-".$pgsecond."</span>&nbsp;|&nbsp;";			
				 }
			   else
			   	 
			     $s.="<a href='?".$this->queryString."&pageNo=".$pageCounter."' style='text-decoration:none;font-family:Arial;font-size:12px;' class='sidelinks'>".$pgfirst."-".$pgsecond."</a>&nbsp;|&nbsp;";									  
				 
			 }
			
			 if($this->pageNumber!=$this->totalPages)
			 {
				$s.="<a href='?".$this->queryString."&pageNo=".$nextRec."' class=sidelinks style=text-decoration:none>Next</a>&nbsp;&nbsp;
			      ";
			}
			
		    $s.="</td>"; 
			$s.="</tr>";
			$s.="</table></td></tr>";
			
			if($this->totalPages > 1)
			 {
			   print $s;
			 }
		  }
		  
		 function displayLinksAJAX()
		  {
		  //db connection
		   $d2=new DataBase(DBHOST,DBUSER,DBPASS,DBNAME);
		  
		    global $_GET;
			
			
		 while(list($key, $value) = each($_GET))
			{
			if($key	!="pageNo"){
				$this->queryString.="&";
				$this->queryString.=$key."=";
				$this->queryString.=$value;}
			}
			if (strlen($this->queryString)>0 )
			$this->queryString=substr($this->queryString,1);
			
			
			$resultRes=mysql_query($this->pageQuery) or die ("unable to execute query $this->pageQuery ".mysql_error());
			$totalRecs=mysqli_num_rows($resultRes);
			
			if($totalRecs > 0 )
			 {
			   $this->totalPages=ceil($totalRecs/$this->maxRecords);
			 }
			
			$prevRec=$this->pageNumber-1;
			$nextRec=$this->pageNumber+1;
			if($prevRec<=0)
			  $prevRec="";
			if($nextRec > $this->totalPages)
			  $nextRec=$this->totalPages;
			  
			$s="<tr><td align='right' width='100%'><table border='0' width='100%' align='right'>";
			$s.="<tr>";
			$s.="<th align='right'><span class='copyblue10b'> Pages : </span>";
			if($this->pageNumber!=1)
			{
				//$s.="<a style='cursor:pointer;' onClick=\'javascript:getTheDivModification('".$_GET['id']."########1"."','pagination');\' class=copyblue13b><small>First</small></a> &nbsp;
			      //<a style='cursor:pointer;' onClick='?".$this->queryString."&pageNo=".$prevRec."' class=copyblue13b><small>Prev</small></a>&nbsp;|&nbsp;&nbsp;&nbsp;"; 
			}
			for($pageCounter=1;$pageCounter<=$this->totalPages;$pageCounter++)
			 {
			   if($pageCounter==$this->pageNumber)
			     $s.="<small>".$pageCounter."</small>&nbsp;&nbsp;";			
			   else
			     $s.="<a style='cursor:pointer;' onClick=\"javascript:getTheDivModification('".$_GET['id']."########".$pageCounter."','pagination');\" class=copyblue13b><small>".$pageCounter."</small></a>&nbsp;&nbsp;";			
			 }
			 if($this->pageNumber!=$this->totalPages)
			 {
				//$s.="&nbsp;&nbsp;|&nbsp;<a style='cursor:pointer;' onClick=\'javascript:getTheDivModification('".$_GET['id']."########".$this->totalPages."','pagination');\' class=copyblue13b><small>Next</small></a>&nbsp;&nbsp;
			      //<a style='cursor:pointer;' onClick=\'javascript:getTheDivModification('".$_GET['id']."########".$this->totalPages."','pagination');\' class=copyblue13b><small>Last</small></a>&nbsp;&nbsp;";
			}
		    $s.="</th>"; 
			$s.="</tr>";
			$s.="</table></td></tr>";
			
			if($this->totalPages > 1)
			 {
			   return $s;
			 }
		  }
	}
  
?>