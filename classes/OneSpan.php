<?php include_once(PATH."classes/Utility.php");
include_once(PATH."classes/clsTransaction.php");
ini_set('display_errors',1);
Class OneSpan
{
	var $Title = '';
	var $Subject ='';
	var $Message = '';
	var $SignerEmail = array();
	var $SignerName = array();
	var $SignerfName = array();
	var $SignerlName = array();
	var $File = '';
	var $Company = '';
	var $firstName = '';
	var $lastName= '';
	var $PageNo = array();
	var $Position = array();
	var $PackageId = '';
	var $Roles = array();
	var $document_name = '';
	var $onespan_key = '';
	var $link = '';
	var $para_user_email;

	function __construct()
	{
		// $mysqli_obj = new DataBase();
		// $this->dbconnection	=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
		// $this->userkey 	=	rand(111111,999999);
		// $this->consumer_password	=	$this->generateRandomPassword();	
		//$this->onespan_key = 'cXppcmtiQVRyRk1VOjBENHk2QmxCeGFZNA=='; //demo
		//$this->link = "https://sandbox.esignlive.com/api/packages";
		// dev link : https://sandbox.esignlive.com/api/packages
		$this->onespan_key =  'WWNuckVmT2pldElPOnpLZEFBYjl2QXd3QQ=='; 
		$this->link =  "https://apps.e-signlive.ca/api/packages";

	}

	function CreateSignatureRequestMulti()
	{
		$URL=$this->link;

		$headers = array(
		    'Content-type: multipart/form-data',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key
		);

		$data = '{
			"name": '.$this->Subject.',
			"language": "en",
			"autocomplete": true,
			"type": "PACKAGE",
			"status": "DRAFT",
			"emailMessage": '.$this->Message.',
			"description": "New Package",
			"roles": [{
				"id": "Role1",
				"signers": [{
					"email": '.$this->SignerEmail[0].',
					"firstName": '.$this->SignerfName[0].',
					"lastName": '.$this->SignerlName[0].',
					"company": '.$this->Company.'
				}]
			}],
			"documents": [{
				"approvals": [{
					"role": "Role1",
					"id": "signature1",
					"fields": [{
						"page": 16,
						"top": 170,
						"subtype": "FULLNAME",
						"height": 100,
						"left": 100,
						"width": 400,
						"type": "SIGNATURE",
						"subtype": "CAPTURE"
					}]
				}],
				"name": '.$this->Title.',
			}]
		}';

		//echo $data;

		$dataArray = array();

		$dataArray['name'] 			= $this->Subject;
		$dataArray['language'] 		=  "en";
		$dataArray['autocomplete']	= "true";
		$dataArray['type']			="PACKAGE";
		$dataArray['status']		="DRAFT";
		$dataArray['emailMessage']	=$this->Message;
		$dataArray['description']	="New Package";

		$rolesArr = array();

		for($i=0;$i<count($this->SignerEmail);$i++)
		{
			$rolesArr[$i]['id'] = "Role".($i+1); 
			$rolesArr[$i]['signers'][0]['email'] = $this->SignerEmail[$i];
			$rolesArr[$i]['signers'][0]['firstName'] = $this->SignerfName[$i];
			$rolesArr[$i]['signers'][0]['lastName'] = $this->SignerlName[$i];
			$rolesArr[$i]['signers'][0]['company'] = $this->Company;

		}
		$dataArray['roles'] = $rolesArr;


		$approvals = array();

		for($i=0;$i<count($this->Roles);$i++)
		{
			$approvals[$i]['role'] = $this->Roles[$i] ; 
			$approvals[$i]['id'] = "signature".($i+1); 
			$approvals[$i]['fields'][0]['page'] = $this->PageNo[$i];
			$approvals[$i]['fields'][0]['top'] = $this->Position['top'][$i];
			$approvals[$i]['fields'][0]['height'] = $this->Position['height'][$i];
			$approvals[$i]['fields'][0]['left'] = $this->Position['left'][$i];
			$approvals[$i]['fields'][0]['width'] = $this->Position['width'][$i];
			$approvals[$i]['fields'][0]['type'] = "SIGNATURE";
			$approvals[$i]['fields'][0]['subtype'] = "CAPTURE";
		}

		$dataArray['documents'][0] = array("approvals"=>$approvals,"name"=>$this->Title);

			//echo '<br><br>';

		//print_r($dataArray);

		$data = json_encode($dataArray);
		//echo 'cretae';

		$ch = curl_init();
		//echo $this->File;

		$cFile = curl_file_create($this->File);
		  $post = array( 'file'=>  $cFile,  'payload'=>$data);

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//echo "<pre>".print_r($result)."</pre>";
		$res = json_decode($result);

		// return '123';

		if (isset($res->id))
		{
			//echo $res->id;
			return $res->id;
		}
		else
		{
			return '';
		}
		
	

	}

	

	function UpdateSignatureRequestMulti()
	{
		$URL=$this->link.'/'.$this->PackageId.'/documents';

		$headers = array(
		    'Content-type: multipart/form-data',
		    'Accept: text/html',
		    'Authorization: Basic '.$this->onespan_key);

	$data = '{
			"approvals": [{
				"role": "Role1",
				"id": "signature1",
				"fields": [{
					"page": 1,
					"top": 330,
					"subtype": "FULLNAME",
					"height": 80,
					"left": 450,
					"width": 300,
					"type": "SIGNATURE",
					"subtype": "CAPTURE"
				}]
			},
			{
				"role": "Role2",
				"id": "signature2",
				"fields": [{
					"page": 1,
					"top": 450,
					"subtype": "FULLNAME",
					"height": 80,
					"left": 460,
					"width": 300,
					"type": "SIGNATURE",
					"subtype": "CAPTURE"
				}]
			}],
			"name": "DirMin"
		}';

		//echo $data;

		$dataArray = array();

		//$dataArray['name'] = $this->Subject;


		$rolesArr = array();

		// for($i=0;$i<count($this->SignerEmail);$i++)
		// {
		// 	$rolesArr[$i]['id'] = "Role".($i+1); 
		// 	$rolesArr[$i]['signers'][0]['email'] = $this->SignerEmail[$i];
		// 	$rolesArr[$i]['signers'][0]['firstName'] = $this->SignerfName[$i];
		// 	$rolesArr[$i]['signers'][0]['lastName'] = $this->SignerlName[$i];
		// 	$rolesArr[$i]['signers'][0]['company'] = $this->Company;
		// }
		// $dataArray['roles'] = $rolesArr;

		$approvals = array();

		for($i=0;$i<count($this->Roles);$i++)
		{
			$approvals[$i]['role'] = $this->Roles[$i]; 
			$approvals[$i]['id'] = "signature".($i+1); 
			$approvals[$i]['fields'][0]['page'] = $this->PageNo[$i];
			$approvals[$i]['fields'][0]['top'] = $this->Position['top'][$i];
			$approvals[$i]['fields'][0]['height'] = $this->Position['height'][$i];
			$approvals[$i]['fields'][0]['left'] = $this->Position['left'][$i];
			$approvals[$i]['fields'][0]['width'] = $this->Position['width'][$i];
			$approvals[$i]['fields'][0]['type'] = "SIGNATURE";
			$approvals[$i]['fields'][0]['subtype'] = "CAPTURE";

		}
		$dataArray = array("approvals"=>$approvals,"name"=>$this->Title); 

		//echo '<br><br>';
		//echo 'update';

		$data = json_encode($dataArray);
		//echo $data;
		//die;


		$ch = curl_init();
		//echo $this->File;

		$cFile = curl_file_create($this->File);
		$post = array( 'file'=>  $cFile,  'payload'=>$data);

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//print_r($result);
		$res = json_decode($result);
		if (isset($res->id))
		{
			//echo $res->id;
			return $res->id;
		}
		else
		{
			return '';
		}
		
		//return 'qqwqwwqwqw';

	}


	function sendSignatureRequestMulti()
	{
		$URL=$this->link.'/'.$this->PackageId;

		$headers = array(
		'Content-type: application/json',
		'Accept: application/json',
		'Authorization: Basic '.$this->onespan_key);

		$dataArray = array();

		$dataArray['name'] 			= $this->Subject;
		$dataArray['status']		="SENT";

		$data = json_encode($dataArray);

		$ch = curl_init();
	
		//echo $post =  '{"name": '. $this->Subject.', "status": "SENT"}';

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//print_r($result);
		$res = json_decode($result);
		if (isset($res->id))
		{
			//echo $res->id;
			return $res->id;
		}
		else
		{
			return '';
		}
		
		//return 'qqwqwwqwqw';

	}
	
	function sendSignatureRequest()
	{
		$URL=$this->link;

		$headers = array(
		    'Content-type: multipart/form-data',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key);

		$data = '{
			"name": '.$this->Subject.',
			"language": "en",
			"autocomplete": true,
			"type": "PACKAGE",
			"status": "SENT",
			"emailMessage": '.$this->Message.',
			"description": "New Package",
			"roles": [{
			"id": "Role1",
			  "signers": [{
					"email": '.$this->SignerEmail[0].',
					"firstName": '.$this->SignerfName[0].',
					"lastName": '.$this->SignerlName[0].',
					"company": '.$this->Company.'
				}]
			}],
			"documents": [{
				"approvals": [{
					"role": "Role1",
					"id": "signature1",
					"fields": [{
						"page": 16,
						"top": 170,
						"subtype": "FULLNAME",
						"height": 100,
						"left": 100,
						"width": 400,
						"type": "SIGNATURE",
						"subtype": "CAPTURE"
					}]
				}],
				"name": '.$this->Title.',
			}]
		}';

		//echo $data;

		$dataArray = array();

		$dataArray['name'] 			= $this->Subject;
		$dataArray['language'] 		=  "en";
		$dataArray['autocomplete']	= "true";
		$dataArray['type']			="PACKAGE";
		$dataArray['status']		="SENT";
		$dataArray['emailMessage']	=$this->Message;
		$dataArray['description']	="New Package";

		$rolesArr = array();

		for($i=0;$i<count($this->SignerEmail);$i++)
		{
			$rolesArr[$i]['id'] = "Role".($i+1); 
			$rolesArr[$i]['signers'][0]['email'] = $this->SignerEmail[$i];
			$rolesArr[$i]['signers'][0]['firstName'] = $this->SignerfName[$i];
			$rolesArr[$i]['signers'][0]['lastName'] = $this->SignerlName[$i];
			$rolesArr[$i]['signers'][0]['company'] = $this->Company;

		}
		$dataArray['roles'] = $rolesArr;

		$approvals = array();

		for($i=0;$i<count($this->SignerEmail);$i++)
		{
			$approvals[$i]['role'] = "Role".($i+1); 
			$approvals[$i]['id'] = "signature".($i+1); 
			if (isset($this->PageNo[$i]))
			{
				$approvals[$i]['fields'][0]['page'] = $this->PageNo[$i];
				$approvals[$i]['fields'][0]['top'] = $this->Position['top'][$i];
				$approvals[$i]['fields'][0]['height'] = $this->Position['height'][$i];
				$approvals[$i]['fields'][0]['left'] = $this->Position['left'][$i];
				$approvals[$i]['fields'][0]['width'] = $this->Position['width'][$i];
			}
			$approvals[$i]['fields'][0]['type'] = "SIGNATURE";
			$approvals[$i]['fields'][0]['subtype'] = "CAPTURE";

		}
		$dataArray['documents'][0] = array("approvals"=>$approvals,"name"=>$this->Title);


		$data = json_encode($dataArray);


		$ch = curl_init();
		//echo $this->File;

		$cFile = curl_file_create($this->File);
		  $post = array( 'file'=>  $cFile,  'payload'=>$data);

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//echo "<pre>$result</pre>";
		$res = json_decode($result);
		if (isset($res->id))
		{
			//echo $res->id;
			return $res->id;
		}
		else
		{
			return '';
		}
		
		//return 'qqwqwwqwqw';

	}

	function sendSignatureRequestManual()
	{
		$URL=$this->link;

		$headers = array(
		    'Content-type: multipart/form-data',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key);

		$data = '{
			"name": '.$this->Subject.',
			"language": "en",
			"autocomplete": true,
			"type": "PACKAGE",
			"status": "SENT",
			"emailMessage": '.$this->Message.',
			"description": "New Package",
			"roles": [{
				"id": "Role1",
				"signers": [{
					"email": '.$this->SignerEmail[0].',
					"firstName": '.$this->SignerfName[0].',
					"lastName": '.$this->SignerlName[0].',
					"company": '.$this->Company.'
				}]
			}],
			"documents": [{
				"approvals": [{
					"role": "Role1",
					"id": "signature1",
					"fields": [{
						"page": 16,
						"top": 170,
						"subtype": "FULLNAME",
						"height": 100,
						"left": 100,
						"width": 400,
						"type": "SIGNATURE",
						"subtype": "CAPTURE"
					}]
				}],
				"name": '.$this->Title.',
			}]
		}';

		//echo $data;

		$dataArray = array();
		$dataArray['name'] 			= $this->Subject;
		$dataArray['language'] 		=  "en";
		$dataArray['autocomplete']	= "true";
		$dataArray['type']			="PACKAGE";
		$dataArray['status']		="SENT";
		$dataArray['emailMessage']	=$this->Message;
		$dataArray['description']	="New Package";

		$rolesArr = array();

		for($i=0;$i<count($this->SignerEmail);$i++)
		{
			$rolesArr[$i]['id'] = "Role".($i+1); 
			$rolesArr[$i]['signers'][0]['email'] = $this->SignerEmail[$i];
			$rolesArr[$i]['signers'][0]['firstName'] = $this->SignerfName[$i];
			$rolesArr[$i]['signers'][0]['lastName'] = $this->SignerlName[$i];
			$rolesArr[$i]['signers'][0]['company'] = $this->Company;

		}
		$dataArray['roles'] = $rolesArr;

		$approvals = array();

		for($i=0;$i<count($this->SignerEmail);$i++)
		{
			$approvals[$i]['role'] = "Role".($i+1); 
			$approvals[$i]['id'] = "signature".($i+1); 
			if (isset($this->SignerEmail[$i]))
			{		
				$approvals[$i]['fields'][0]['top'] = $this->Position['top'][$i];				
				$approvals[$i]['fields'][0]['left'] = $this->Position['left'][$i];				
				$approvals[$i]['fields'][0]['extract'] = false;
				$approvals[$i]['fields'][0]['extractAnchor']['text'] = $this->text[$i];
				$approvals[$i]['fields'][0]['extractAnchor']['width'] = $this->Position['width'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['height'] = $this->Position['height'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['index'] = $this->Position['index'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['anchorPoint'] = $this->Position['anchorPoint'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['characterIndex'] = $this->Position['characterIndex'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['leftOffset'] = $this->Position['leftOffset'][$i];
				$approvals[$i]['fields'][0]['extractAnchor']['topOffset'] = $this->Position['topOffset'][$i];
			}
			$approvals[$i]['fields'][0]['type'] = "SIGNATURE";
			$approvals[$i]['fields'][0]['subtype'] = "CAPTURE";

		}
		$dataArray['documents'][0] = array("approvals"=>$approvals,"name"=>$this->Title);

		//print_r($approvals);

		$data = json_encode($dataArray);


		$ch = curl_init();
		//echo $this->File;

		$cFile = curl_file_create($this->File);
		  $post = array( 'file'=>  $cFile,  'payload'=>$data);

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//echo "<pre>$result</pre>";
		$res = json_decode($result);
		if (isset($res->id))
		{
			//echo $res->id;
			return $res->id;
		}
		else
		{
			return '';
		}
		
		//return 'qqwqwwqwqw';

	}



	function getSignatureStatus()
	{
		$headers = array(
	    'Content-type: application/json',
	    'Accept: application/json',
	    'Authorization: Basic '.$this->onespan_key);

		$URL=$this->link.'/'.$this->PackageId.'/signingStatus';
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		//curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		$res = json_decode($result);
		//echo $status_code;
		// echo "<pre>";
		// print_r($result);
		// //print_r(json_decode($result));
		// echo "</pre>";
		if (isset($res->status))
			return $res->status;
		else
			return '';
	}

	function downloadFile()
	{

		$headers = array(
	    'Content-type: multipart/form-data',
	    'Accept: application/json',
	    'Authorization: Basic '.$this->onespan_key);
	    
	$URL=$this->link.'/'.$this->PackageId;
	//$URL='https://sandbox.esignlive.com/api/packages/'.$this->PackageId."/pdf?flatten=false";

	//{"status":"SIGNING_PENDING"}
	 
	//for download pdf document
	// we need to save doc and then download by  header('Content-Description: File Transfer');
	// as in exposrtcsvdownload emerld
	 //$URL = "https://sandbox.esignlive.com/api/packages/2Do6GoaPvwN4ihaS6-EtbMxJEhg=/documents/288cedae6b924b194545e461760c24022605708bca6d93f8/pdf?flatten=false";

	 //$URL = "https://sandbox.esignlive.com/api/packages/2Do6GoaPvwN4ihaS6-EtbMxJEhg=/documents/288cedae6b924b194545e461760c24022605708bca6d93f8";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	//curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

	//curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
	//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

	$result=curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
	curl_close ($ch);
	//echo $status_code;

	$result;
	
	$resultA = json_decode($result);

	$document_id = '';

	//  echo "<pre>";
	// print_r($resultA);
	// // //print_r(json_decode($result));
	//  echo "</pre>";
	// echo "docname<br>";
	// echo $this->document_name;
	// echo "docname<br>";

	if (isset($resultA->results[0]->documents))
	{
		foreach ($resultA->results[0]->documents as $key => $value) {
			//echo $value->name;
			if (($value->name == $this->Title) || ($value->name == $this->document_name) )
				$document_id = $value->id;
		}
	}
	//echo "<br>";
	// echo $this->Title;
	// echo $this->document_name

	if (isset($resultA->documents))
	{
		foreach ($resultA->documents as $key => $value) {
			//echo $value->name;
			//echo "<br>";
			if (($value->name == $this->Title ) || ($value->name == $this->document_name))
				$document_id = $value->id;
		}
	}
	//echo $document_id;
	$result = '';

	// if ($document_id != '')
	// {


	$URL=$this->link.'/'.$this->PackageId.'/documents/'.$document_id.'/pdf?flatten=false';
	

	//$URL='https://sandbox.esignlive.com/api/packages/'.$this->PackageId."/pdf?flatten=false";

	//{"status":"SIGNING_PENDING"}
	 
	//for download pdf document
	// we need to save doc and then download by  header('Content-Description: File Transfer');
	// as in exposrtcsvdownload emerld
	 //$URL = "https://sandbox.esignlive.com/api/packages/2Do6GoaPvwN4ihaS6-EtbMxJEhg=/documents/288cedae6b924b194545e461760c24022605708bca6d93f8/pdf?flatten=false";

	 //$URL = "https://sandbox.esignlive.com/api/packages/2Do6GoaPvwN4ihaS6-EtbMxJEhg=/documents/288cedae6b924b194545e461760c24022605708bca6d93f8";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	//curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

	//curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
	//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

	$result=curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
	curl_close ($ch);
	//echo $status_code;

	//print_r($result);

	
	 $fp = fopen($this->File, 'w');
	    fwrite($fp, $result);
	    fclose($fp);

	    return $status_code;
	
	}

	function SendSignatureLink()
	{
		$headers = array(
		    'Content-type: application/json',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key);

		$URL=$this->link.'/'.$this->PackageId;


		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//echo "<pre>";
		//print_r($result);
		$resultA = json_decode($result);
		//print_r($resultA);
		//echo "</pre>";
		$roles = array();
		$pendingemail = array();

		if ($status_code == 200)
		{
			

			if (isset($resultA->results[0]->documents))
			{
				foreach ($resultA->results[0]->documents as $key => $value) {
					foreach ($value->approvals as $key1 => $value1) {
						if (empty($value1->signed))
						{
							if (!in_array($value1->role, $roles))
							{
								$roles[] = $value1->role;
								$roleid[] = $value1->id;
							}								
						}
					}
				}
				foreach ($resultA->results[0]->roles as $key => $value) {
						if (in_array($value->id, $roles))
						{
							foreach ($value->signers as $key1 => $value1) {
								//print_r($value1);
								if (!in_array($value1->email, $pendingemail))
								{
									$pendingemail[] = $value1->email;
								}						
							}					
						}					
				}
			}

			if (isset($resultA->documents))
			{
				$docname = $resultA->name;
				foreach ($resultA->documents as $key => $value) {
					foreach ($value->approvals as $key1 => $value1) {
						//echo '<br>';
						//echo $key1;
						//print_r($value1);
						//echo $value1->signed;
						if (empty($value1->signed))
						{
							if (!in_array($value1->role, $roles))
							{
								$roles[] = $value1->role;
								$roleid[] = $value1->id;
							}								
						}
					}
				}
				foreach ($resultA->roles as $key => $value) {
						if (in_array($value->id, $roles))
						{
							foreach ($value->signers as $key1 => $value1) {
								//print_r($value1);
								if (!in_array($value1->email, $pendingemail))
								{
									$pendingemail[] = $value1->email;
								}						
							}					
						}					
				}

				
			}
		}
		//print_r($roles);
		//print_r($pendingemail);

		$error = false;
		$email = '';

		for($i=0;$i < count($pendingemail);$i++)
		{
			if (count($pendingemail) > 0)
			{
				$data = '{
				  "email": "'.$pendingemail[$i].'",
				  "message": "Hello, the documents for '.$docname.' require your immediate attention. Please review and sign the documents. If you have any questions please reach out to : '.$this->para_user_email.'"
				}';


				$headers = array(
				    'Content-type: application/json',
				    'Accept: application/json',
				    'Authorization: Basic '.$this->onespan_key);

				 $URL=$this->link.'/'.$this->PackageId.'/notifications';

				// // //die;
				$ch = curl_init();

				//$cFile = curl_file_create('Sharecert.pdf');
				  //$post = array( 'file'=>  $cFile,  'payload'=>$data);

				curl_setopt($ch, CURLOPT_URL,$URL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
				//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
				//curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
				//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
				//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

				$result=curl_exec($ch);
				$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
				curl_close ($ch);
				//echo $status_code;
				//echo "<pre>$result</pre>";
				if ($status_code = 200)
				{
					$error = false;
					$email .= $pendingemail[$i].',';
				}
				else
				{
					$error = true;
				}

			}
		}

		if ($error)
			return false;
		else
			return $email;



	}


	function CheckSignaturesCompleted()
	{
		$headers = array(
		    'Content-type: application/json',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key);

		$URL=$this->link.'/'.$this->PackageId;


		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		//echo "<pre>";
		//print_r($result);
		$resultA = json_decode($result);
		//print_r($resultA);
		//echo "</pre>";
		$roles = array();
		$roles_pend = array();
		$roleid_pend = array();

		$singedemail = array();

		if ($status_code == 200)
		{
			
			if (isset($resultA->results[0]->documents))
			{
				foreach ($resultA->results[0]->documents as $key => $value) {
					foreach ($value->approvals as $key1 => $value1) {
						if (!empty($value1->signed))
						{
							if (!in_array($value1->role, $roles))
							{
								$roles[] = $value1->role;
								$roleid[] = $value1->id;
							}								
						}
						if (empty($value1->signed))
						{
							if (!in_array($value1->role, $roles_pend))
							{
								$roles_pend[] = $value1->role;
								$roleid_pend[] = $value1->id;
							}								
						}
					}
				}
				foreach ($resultA->results[0]->roles as $key => $value) {
						if (in_array($value->id, $roles) &&  !in_array($value->id, $roles_pend))
						{
							foreach ($value->signers as $key1 => $value1) {
								//print_r($value1);
								if (!in_array($value1->email, $singedemail))
								{
									$singedemail[] = $value1->email;
								}						
							}					
						}					
				}
			}

			if (isset($resultA->documents))
			{
				$docname = $resultA->name;
				foreach ($resultA->documents as $key => $value) {
					foreach ($value->approvals as $key1 => $value1) {
						//echo '<br>';
						//echo $key1;
						//print_r($value1);
						//echo $value1->signed;
						if (!empty($value1->signed))
						{
							if (!in_array($value1->role, $roles))
							{
								$roles[] = $value1->role;
								$roleid[] = $value1->id;
							}								
						}
						if (empty($value1->signed))
						{
							if (!in_array($value1->role, $roles_pend))
							{
								$roles_pend[] = $value1->role;						
							}								
						}
					}
				}
				foreach ($resultA->roles as $key => $value) {
						if (in_array($value->id, $roles) &&  !in_array($value->id, $roles_pend))
						{
							foreach ($value->signers as $key1 => $value1) {
								//print_r($value1);
								if (!in_array($value1->email, $singedemail))
								{
									$singedemail[] = $value1->firstName.' '.$value1->lastName.' ('.$value1->email.')';
								}						
							}					
						}					
				}

				
			}
		}
		//print_r($roles);
		//print_r($singedemail);

		return $singedemail;

		$error = false;
		$email = '';

		for($i=0;$i < count($singedemail);$i++)
		{
			if (count($singedemail) > 0)
			{
				$data = '{
				  "email": "'.$singedemail[$i].'",
				  "message": "Hi document related to  '.$docname.' send by OneSpan are pedning for your signaure, Please sign ASAP!"
				}';


				$headers = array(
				    'Content-type: application/json',
				    'Accept: application/json',
				    'Authorization: Basic '.$this->onespan_key);

				 $URL=$this->link.'/'.$this->PackageId.'/notifications';

				// // //die;
				$ch = curl_init();

				//$cFile = curl_file_create('Sharecert.pdf');
				  //$post = array( 'file'=>  $cFile,  'payload'=>$data);

				curl_setopt($ch, CURLOPT_URL,$URL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
				//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
				//curl_setopt($ch, CURLOPT_POST,1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
				//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
				//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

				$result=curl_exec($ch);
				$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
				curl_close ($ch);
				//echo $status_code;
				//echo "<pre>$result</pre>";
				if ($status_code = 200)
				{
					$error = false;
					$email .= $singedemail[$i].',';
				}
				else
				{
					$error = true;
				}

			}
		}

		if ($error)
			return false;
		else
			return $email;

	}

	function RemoveSignature()
	{
		$headers = array(
		    'Content-type: application/json',
		    'Accept: application/json',
		    'Authorization: Basic '.$this->onespan_key);

		$URL=$this->link.'/'.$this->PackageId;


		// $ch = curl_init();

		// curl_setopt($ch, CURLOPT_URL,$URL);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );

		// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

		// curl_setopt($ch, CURLOPT_HEADER, false);
		// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// $result=curl_exec($ch);
		// $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		// curl_close ($ch);
		// //echo $status_code;
		// //echo "<pre>";
		// //print_r($result);
		// $resultA = json_decode($result);
		// //print_r($resultA);
		// //echo "</pre>";

		// $error = false;
		// $email = '';

		// if ($status_code == 200)
		// 	return true;
		// else
		// 	return false;

		$post =  '{"status": "ARCHIVED"}';

		$ch = curl_init();
//$data = json_encode(array("name"=>"test"));




		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		//curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post);
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		//curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
		//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		curl_close ($ch);
		//echo $status_code;
		$r = json_decode($result);
		//print_r($r);
		if ($status_code == 200)
			return true;
		else
			return false;

	}




}