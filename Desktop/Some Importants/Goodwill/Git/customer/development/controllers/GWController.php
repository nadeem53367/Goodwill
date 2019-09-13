<?php

namespace Custom\Controllers;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Url,    
RightNow\Utils\Framework;

define('Status_PendingPurchaseApproval', 103);
define('Status_ReminderSentForApprovals', 132);

class GWController extends \RightNow\Controllers\Base
{
    //This is the constructor for the custom controller. Do not modify anything within
    //this function.
    function __construct()
    {
        parent::__construct();
        $this->load->helper('goodwill');
    }

    public function askForm()  
	{
		$CI 		=& get_instance();
		$postData 	= $_POST;
		$CallFrom 	= $postData['CallFrom'];
		
		if ($postData) 
		{
		
			$postData['aFile'] = $_FILES;   
			
			if ($CallFrom == 'Agent_Desktop') 
			{ 	
				$contID 	= (int) $postData['contactID']; 
				$contact 	= RNCPHP\Contact::fetch($contID);
			}else
			{
				if(isset($CI->session->getProfile()->c_id->value))
				{
					$contact 				= RNCPHP\Contact::fetch( $CI->session->getProfile()->c_id->value );
					$postData['contactID'] 	= $contact->ID;
				}
				
			}		

			if ($CI->session->getSessionData("Department") && $CI->session->getSessionData("Department") == 3) 
			{
				$RSMID = (int)$postData['RSM'];
				if ($RSMID)
				{
					$RSM 							= RNCPHP\Contact::fetch( $RSMID );
					$postData['RSMLocation']   		= $RSM->CustomFields->Goodwill->Location->ID; 
					$postData['RSMdispetcher'] 		= $RSM->CustomFields->Goodwill->Location->Dispatcher->ID;  
					$postData['RSMdepartment'] 		= $RSM->CustomFields->Goodwill->GWDepartments->ID;
				}
			}			 
			  
			if ($contact) 
			{
				$Location 						= $contact->CustomFields->Goodwill->Location->ID;
				$DM 							= $contact->CustomFields->Goodwill->Location->Account->ID;
				$postData['Location'] 			= $Location;
				$postData['DM'] 				= $DM;
				$postData['SubDepartment'] 		= $contact->CustomFields->Goodwill->GWDepartments->ID; 
			}
		
			if ($CallFrom == '') 
			{
				/*if (array_key_exists('Category',$postData)) 
				{
						
				}
				else
				{
					$postData['Category'] = $postData['MiddleCategory']; 
				}*/
					
				/*if ($postData['MainCategories'] == 2) {
					$postData['Department'] = 2; // IT
				}if ($postData['MainCategories'] == 3) {
					$postData['Department'] = 1; // facilities
				}if ($postData['MainCategories'] == 4) {
					$postData['Department'] = 3; // Supply chain 
				}*/
			} 
		//echo "<pre>"; print_r($postData); exit();
			$this->load->model('custom/GWModel');
			$Ticket = $this->GWModel->create_incident($postData);
			if ($CallFrom == '') 
			{
				$this->load->helper('url');
				if($Ticket)
				{		
						$sShortUrl = 	getShortEufBaseUrl().'/app/ask_confirm/refno/?ID='.$Ticket->ID.'&Date='.$Ticket->CreatedTime;
						header('Location: '.$sShortUrl); 
						die;
				}
			
			}else 
			{ 	print_r($Ticket);
				return $Ticket;
				
			}
			
		}
	}

	function subCategories()
	{
		$data=  $_POST['id']; 
		$this->load->model('custom/GWModel');
		$returnData=$this->GWModel->getSubCategories($data);  
		if ($returnData ) {
			echo json_encode($returnData);
		}
		//echo $data;
		
	}

	function createSession()
	{
		$CI 						= & get_instance();
		$mySession['Category']  	= null;
		$mySession['Department'] 	= null;
		$mySession['Procurement'] 	= null;
		$mySession['AnswerSubject'] = null; 
		$mySession['AnswerID'] 		= null; 
		$mySession['SubCat'] 		= null;
		$mySession['ParentCat'] 	= null;
		$Parent = array();
		$ParentCat = 0;
		
		$this->session->setSessionData($mySession); 
		if ($_GET['ID']) 
		{
			$Answer 		= RNCPHP\Answer::fetch($_GET['ID']);
  			$AnswerSummary 	= $Answer->Summary;
  			$Department 	= $Answer->CustomFields->Goodwill->Department->ID;
  			$Approval 		= $Answer->CustomFields->Goodwill->Approval->LookupName;
  			$Category 		= $Answer->Categories[0]->ID;
		}
		if ($Category) 
		{
			$sql  	= "Select ServiceCategory.Parent.ID  FROM ServiceCategory where ServiceCategory.ID =".$Category;
			$Query  = RNCPHP\ROQL::query($sql)->next(); 
			if ($Query > 0 ) 
			{
				$Parent =  $Query->next();
				$ParentCat = $Parent['ID'];
			}
	        
		}

  		/*echo $ParentCat;
  		echo "<pre>"; print_r($Parent); echo "</pre>";
  		exit();*/
		$param  = $_SERVER['argv'][0];
	    
	    $CI->session->setSessionData(
    	array
    	(
			"param"			=>	$param,
			"Category" 		=>	$Category , 
			"AnswerSubject"	=>	$AnswerSummary , 
			"Department"	=>	$Department,
			"Procurement"	=>	$Approval,
			"AnswerID"		=>	$_GET['ID'],
			"ParentCat"		=>	$ParentCat,
			
	    )); 

	    $ShortUrl = 	getShortEufBaseUrl().'/app/ask?';
	    header('Location: '.$ShortUrl); 
	    exit();
	}

	function SetAssetHistory() 
    {
    //$TestArray = array('IncidentID' =>104); 
           
        $CI 		= & get_instance();
        $IncidentID = (int)  $_POST['IncidentID']; //$TestArray['IncidentID']; // // 
        
        $Incident 	= RNCPHP\Incident::fetch($IncidentID); 
        $this->load->model('custom/GWModel');   
		$AssetsData = $this->GWModel->createAssetsHisstory($_POST);    
		if ($AssetsData )   
		{
	         foreach ($AssetsData as $AssetID ) 
	            {
	                $ID                     = $AssetID['ID']; 
	                $Asset                  = RNCPHP\Asset::fetch( $ID ); 
	                $AssetsAssign           = $Asset->CustomFields->Goodwill->AssignTo->ID;
	                $Asset->CustomFields->Goodwill->IsDeployedFinally = true;
	                $LocationID 	= (int) $Incident->CustomFields->Goodwill->Location->ID;
	                $LocationObject = RNCPHP\Goodwill\Location::fetch( $LocationID );
	                $Asset->CustomFields->Goodwill->Location = $LocationObject;
	                $Asset->save();  
	                								
			        if ($AssetsAssign) 
			        {
			        	$AssignProcurement          = RNCPHP\Account::fetch( $AssetsAssign );
			        } 
	                
	                $IncidentsDM            = $Incident->CustomFields->Goodwill->AssignedBy->ID;
	                $IncidentTech 			= $Incident->CustomFields->Goodwill->Facilities_Technician->ID;
	                $ClerkAssignTo 			= $Incident->CustomFields->Goodwill->AssignByProcurementTo;

	                $NewHistory             = new RNCPHP\Goodwill\AssetsHistory();
	                $NewHistory->Asset      = $Asset;
	                
	                if ($ClerkAssignTo == 'From Procurement To District Manager') 
	                {
	                	if ($AssignProcurement) 
		                {
		                	 $NewHistory->AssignedBy = $AssignProcurement;
		                }
	                	if ($IncidentsDM ) 
	                	{
		                	$NewHistory->AssignTo   = RNCPHP\Account::fetch( $IncidentsDM ); 
		                }
	                }elseif ($ClerkAssignTo == 'From Procurement To Technician') 
	                {
	                	if ($AssignProcurement) 
		                {
		                	 $NewHistory->AssignedBy = $AssignProcurement;
		                }
	                	if ($IncidentTech) 
	                	{ 
	                		$NewHistory->AssignToTech = RNCPHP\Contact::fetch( $IncidentTech );
	                	} 
	                	
	                }elseif ($ClerkAssignTo == 'From District Manager To Technician') 
	                {
	                	if ($IncidentsDM) 
		                {
		                	$NewHistory->AssignedBy = $IncidentsDM;
		                }
	                	if ($IncidentTech) 
	                	{ 
	                		$NewHistory->AssignToTech = RNCPHP\Contact::fetch( $IncidentTech );
	                	} 
	                	  
	                }
	                
	                $NewHistory->AssignedOn 		= time();
	                $NewHistory->AssignmentAction 	= $ClerkAssignTo;
	                if ($LocationObject) 
	                {
	                	$NewHistory->Location = $LocationObject;
	                }
	                if ($Incident ) 
	                {
	                	$NewHistory->Incident = $Incident;  
	                } 
	                  
	                $NewHistory->save(RNCPHP\RNObject::SuppressAll);  

	                $Incident->CustomFields->Goodwill->AssetHistoryLastSavedOn = time();
	                $Incident->save(RNCPHP\RNObject::SuppressAll);
	               
	            } 
		} 
        
		
    }

    function ChangeIncidentType()
    {
    	$postData	=  $_POST;  
    	$contID 	= (int) $postData['contactID']; 
		$contact 	= RNCPHP\Contact::fetch($contID);
		if ($contact) 
		{
			$Location 						= $contact->CustomFields->Goodwill->Location->ID;
			$DM 							= $contact->CustomFields->Goodwill->Location->Account->ID;
			$postData['Location'] 			= $Location;
			$postData['DM'] 				= $DM;
			$postData['SubDepartment'] 		= $contact->CustomFields->Goodwill->GWDepartments->ID; 
		}
		echo "YEs in controller";
		$this->load->model('custom/GWModel');
		$this->GWModel->ShiftDepartment($postData);  
    }
    public function TestingEmploye()
    {
    	$AllEmploye  = array();
    	$sql = "SELECT ID , Contact.CustomFields.Goodwill.ReportingSupervisor_Dayforce As 'DayforceId' FROM Contact where Contact.CustomFields.Goodwill.ReportingSupervisor_Dayforce IS Not Null LIMIT 1500";
    	$Query  = RNCPHP\ROQL::query($sql)->next(); 
        while($result = $Query->next())
        {

           $AllEmploye[] = array('ID' => $result['ID'], 'DayforceId'=>$result['DayforceId']);
        }
        /*echo "<pre>"; print_r($AllEmploye); echo "</pre>";
       exit;*/
        if ($AllEmploye) 
        { 
        	$SupervisorsContact = array();
        	foreach ($AllEmploye as $key => $value) 
        	{
        		$OSvCContactID = $value['ID'];
        		$supervisorID = $value['DayforceId'];
        		$EmployeID = $value['EmployeId'];
        		if ($supervisorID) {
        			# code...
        		
        			$sql = "SELECT ID  FROM Contact where Contact.CustomFields.Goodwill.EmployeeId  = ".$supervisorID." LIMIT 1";
			    	$Query  = RNCPHP\ROQL::query($sql)->next(); 

			        $SupervisorContactID = $Query->next();

			        $A = $SupervisorContactID['ID'];
			       // print_r($SupervisorContactID);exit();
			        if ($A > 0) 
			        {
			        	 $Contact = RNCPHP\Contact::fetch( $OSvCContactID );
			        	 $SuperVisor = RNCPHP\Contact::fetch( $A );
			        	 echo "<pre>"; print_r($Contact->LookupName); echo "</pre>";
       					 echo "<pre>"; print_r($SuperVisor->LookupName); echo "</pre> <br>";
			        	 $Contact->CustomFields->Goodwill->ReportingSupervisor = $SuperVisor;
			        	 $Contact->save();
			        }
			       
        		}
        	}
        }
    }
 	public function ExternalFeedback()
    { 
    	//print_r($_POST);
    	/*$TestArray = array('Incident_Threads' =>'blass' , 'IncidentId' =>508 , 'ContactID' =>8 , 'RecordID' => 121, 'ActionType' =>'RFI' );*/
    	$this->load->model('custom/GWModel');
		$returnData = $this->GWModel->saveExternalFeedBack($_POST);
		if ($returnData) 
		{ 
			$Action 		=  base64_encode ($returnData->Approval_Action->LookupName);
			$IncidentID 	= base64_encode ($returnData->Incident->ID) ;
			$approveTime 	= base64_encode ($returnData->UpdatedTime );
			$requestoremail = $returnData->CreatedByAccount->Emails[0]->Address;
			$ApproverName 	= $returnData->ApproverContact_ID->LookupName;
			$AproverComment = $returnData->ApproverComments;
			if ($requestoremail > 0 || !empty($requestoremail)) 
			{
				$this->emailToRequestor($requestoremail,$IncidentID,$ApproverName,$Action,$AproverComment); 
			}
			echo "<script>window.location ='https://goodwillsocal.custhelp.com/app/Approval_confirm?IncidentID=$IncidentID&Action=$Action&time=$approveTime'</script>";
			//print_r($returnData->ApproverContact_ID->LookupName);
			//print_r($returnData->CreatedByAccount->Emails[0]->Address);
			 
			  
		    exit();
		}

    }

    function emailToRequestor($requestoremail,$IncidentID,$ApproverName,$Action,$comments)
	{
		try
		{
			$email_1 = $requestoremail;
			$orderId = base64_decode($IncidentID);
			$Action  = base64_decode($Action);
			$text_body = "A New feedback recieved.";
			$html_body = 

<<<EMAIL
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div>
		<p>A new feedback has been received.</p><br/> 
		<h4>Details:</h4>
		<label>Order ID: $orderId</label><br/>
		<label>Feedback From: $ApproverName</label><br/>
		<label>Action Taken: $Action</label><br/>
		<label>Comments: $comments</label> <br/>
 
	</div>
</body>
</html>


EMAIL;


 
		 //create mail message object
		    $mm = new RNCPHP\MailMessage();
		 
		//set TO,CC,BCC fields as necessary
		    $mm->To->EmailAddresses = array($email_1);
		    //$mm->FromMailbox = 'info@ephlux.com'; --> not Working
		 
		//set subject
		    $mm->Subject = "Feedback recieved for work order #".$orderId;
		 
		//set body of the email
		    $mm->Body->Text = $text_body;
		    $mm->Body->Html = $html_body;
		 
		//set marketing options
			$mm->Options->IncludeOECustomHeaders = false;
		 
		//send email
		    $mm->send();
		    //echo "<br>Email Sent to ".$email_1.".";
		}
		catch ( Exception $err )
		{
    		echo "<br><b>Exception</b>: line ".__LINE__.": ".$err->getMessage()."</br>";
		}
		return true;
	}

    function SendEmailForApproval()
    {

		try
		{

			$Incident_ID = $_POST['IncidentID'];       
			$Incident 				= RNCPHP\Incident::fetch( $Incident_ID );
			$IncID 					= $Incident->ID;
	        $IncSubject 			= $Incident->Subject;
	        $IncCategory 			= $Incident->Category->ID;
	        $IncidentID 			= base64_encode($Incident_ID); 

	        $CI =& get_instance();
			$CI->load->helper('goodwill_helper');
			if ($IncCategory) 
			{
				//$TextCategories 	= testCat($IncCategory);
				//print_r($TextCategories);
				$TextCategories 	= ShowTextCategories($IncCategory); 
				if ($TextCategories['TopCategory']) {
					$TopCategory 		= $TextCategories['TopCategory'];
				}
				
				if ($TextCategories['MiddleCategory']) {
					$MiddleCategory 	= "<b>* </b>" .$TextCategories['MiddleCategory'] ."<b> / </b> ";
				}
				if ($TextCategories['LastCategory']) {
					$LastCategory 		= $TextCategories['LastCategory']; 
				}
				
				
			} 


			if ($Incident->CustomFields->Goodwill->ExternalApproval_ActionType == "SendApprovalEmail") 
			{ 
				$NotRespond = '';
				//echo "Yes";
				$RecordFilter = 'SendFirstTimeApprovalEmail'; 
				$this->load->model('custom/GWModel');     
				$ApprovalId = $this->GWModel->getExternalApprovalRecord($Incident_ID,$RecordFilter,$NotRespond);
				if ($ApprovalId) 
				{ //echo "In Approval function";
					foreach ($ApprovalId as $key => $value) 
					{ 
						$ApprovalRecordID 	= $value['ID'];
						$ApprovalRecord   	= RNCPHP\Goodwill\External_Approvals::fetch( $ApprovalRecordID );
						$this->SendApproval($ApprovalRecordID,$TopCategory,$MiddleCategory,$LastCategory);  	
						$ApprovalRecord->ApprovalContactStatus = 'InviteSentAlready';
						$ApprovalRecord->save(RNCPHP\RNObject::SuppressAll); 
						
					}
					  
				}
				$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
	            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
	            $Incident->StatusWithType->Status->ID       = (int) Status_PendingPurchaseApproval ; 
	            $Incident->save(RNCPHP\RNObject::SuppressAll);
			

			}
			elseif ($Incident->CustomFields->Goodwill->ExternalApproval_ActionType == "SendReminderEmail") 
			{
				$NotRespond = 'notRespond';
				//echo "Yes";
				$RecordFilter = 'InviteSentAlready'; 
				$this->load->model('custom/GWModel');     
				$ApprovalId = $this->GWModel->getExternalApprovalRecord($Incident_ID,$RecordFilter,$NotRespond);
					//print_r($ApprovalId);
				if ($ApprovalId) 
				{
					foreach ($ApprovalId as $key => $value) 
					{ 
						$ApprovalRecordID 	= $value['ID'];
						$ApprovalRecord   	= RNCPHP\Goodwill\External_Approvals::fetch( $ApprovalRecordID );
						$RecordID 			= $ApprovalRecord->ID;
						$ContactID 			= $ApprovalRecord->ApproverContact_ID->ID; 
						$ApproverEmail 		= $ApprovalRecord->ApproverContact_ID->Emails[0]->Address;
						$SenderComments 	= $ApprovalRecord->SenderComments;
						
						$this->SendReminderEmail($ApprovalRecordID,$TopCategory,$MiddleCategory,$LastCategory);  
							
					}
					  
				}
				$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
	            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
	            $Incident->StatusWithType->Status->ID       = (int) Status_ReminderSentForApprovals ;
	            $Incident->save(RNCPHP\RNObject::SuppressAll);
			}

			
	}
		catch ( Exception $err )
		{
    		echo "<br><b>Exception</b>: line ".__LINE__.": ".$err->getMessage()."</br>";
		}
				
	}

	function SendApproval($ApprovalRecordID,$TopCategory,$MiddleCategory,$LastCategory)
	{
		//echo "In send Approval function";
		$ApprovalRecord   		= RNCPHP\Goodwill\External_Approvals::fetch( $ApprovalRecordID );
		$Incident_ID 			= $ApprovalRecord->Incident->ID;
		$IncSubject 			= $ApprovalRecord->Incident->Subject;
		$Record_ID 				= $ApprovalRecord->ID;
		$Contact_ID 			= $ApprovalRecord->ApproverContact_ID->ID; 
		$ApproverEmail 			= $ApprovalRecord->ApproverContact_ID->Emails[0]->Address;
		$SenderComments 		= $ApprovalRecord->SenderComments;
		  	
		$IncidentID 			= base64_encode($Incident_ID);
		$ApproverContact_ID 	= base64_encode($Contact_ID);
		$RecordID 				= base64_encode($Record_ID);
		$ApproverName 			= $ApproverContact->LookupName;
		
		$SenderName 	 		= $ApprovalRecord->CreatedByAccount->LookupName; 

		$email_1 = $ApproverEmail; 

		$text_body = "Sample text body:\nThe text part of the message.";
		$html_body = <<<EMAIL

					<html>		
					<head>
					  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					  <title>Simple Call to Action</title>
					  <style type="text/css">
					    /* /\/\/\/\/\/\/\/\/ CLIENT-SPECIFIC STYLES /\/\/\/\/\/\/\/\/ */
					    #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" message */
					    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
					    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
					    body, table, td, p, a, li, blockquote{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
					    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
					    td ul li {
					      font-size: 16px;
					    }
					    /* /\/\/\/\/\/\/\/\/ RESET STYLES /\/\/\/\/\/\/\/\/ */
					    body {margin: 0; padding: 0; min-width: 100%!important;}
					    img{
					      max-width:100%;
					      border:0;
					      line-height:100%;
					      outline:none;
					      text-decoration:none;
					    }
					    table{border-collapse:collapse !important;}
					    .content {width: 100%; max-width: 600px;}
					    .content img { height: auto; min-height: 1px; }

					    #bodyCellFooter{margin:0; padding:0; width:100% !important;padding-top:39px;padding-bottom:15px;}

					    #templateContainer{
					      border: 1px solid #e2e2e2;
					      border-radius: 4px;
					      background-clip: padding-box;
					      border-spacing: 0;
					    }

					    /**
					    * @tab Page
					    * @section heading 1
					    * @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
					    * @style heading 1
					    */
					    h1{
					      color:#2e2e2e;
					      display:block;
					      font-family:Helvetica;
					      font-size:26px;
					      line-height:1.385em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }

					    /**
					    * @tab Page
					    * @section heading 2
					    * @tip Set the styling for all second-level headings in your emails.
					    * @style heading 2
					    */
					    h2{
					      color:#2e2e2e;
					      display:block;
					      font-family:Helvetica;
					      font-size:22px;
					      line-height:1.455em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }

					    /**
					    * @tab Page
					    * @section heading 3
					    * @tip Set the styling for all third-level headings in your emails.
					    * @style heading 3
					    */
					    h3{
					      color:#545454;
					      display:block;
					      font-family:Helvetica;
					      font-size:18px;
					      line-height:1.444em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }

					    /**
					    * @tab Page
					    * @section heading 4
					    * @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
					    * @style heading 4
					    */
					    h4{
					      color:#545454;
					      display:block;
					      font-family:Helvetica;
					      font-size:14px;
					      line-height:1.571em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }


					    h5{
					      color:#545454;
					      display:block;
					      font-family:Helvetica;
					      font-size:13px;
					      line-height:1.538em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }


					    h6{
					      color:#545454;
					      display:block;
					      font-family:Helvetica;
					      font-size:12px;
					      line-height:2.000em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }

					    p {
					      color:#545454;
					      display:block;
					      font-family:Helvetica;
					      font-size:16px;
					      line-height:1.500em;
					      font-style:normal;
					      font-weight:normal;
					      letter-spacing:normal;
					      margin-top:0;
					      margin-right:0;
					      margin-bottom:15px;
					      margin-left:0;
					      text-align:left;
					    }

					    .unSubContent a:visited { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
					    .unSubContent a:focus   { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
					    .unSubContent a:hover   { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
					    .unSubContent a:link   { color: #a1a1a1 ; text-decoration:underline; font-weight:normal;}
					    .unSubContent a .yshortcuts   { color: #a1a1a1 ; text-decoration:underline; font-weight:normal;}

					    .unSubContent h6 {
					      color: #a1a1a1;
					      font-size: 12px;
					      line-height: 1.5em;
					      margin-bottom: 0;
					    }

					    .bodyContent{
					      color:#505050;
					      font-family:Helvetica;
					      font-size:14px;
					      line-height:150%;
					      padding-top:3.143em;
					      padding-right:3.5em;
					      padding-left:3.5em;
					      padding-bottom:3.143em;
					      text-align:left;
					    }

					    /**
					    * @tab Body
					    * @section body link
					    * @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
					    */
					    a:visited { color: #3386e4; text-decoration:none;}
					    a:focus   { color: #3386e4; text-decoration:none;}
					    a:hover   { color: #3386e4; text-decoration:none;}
					    a:link   { color: #3386e4 ; text-decoration:none;}
					    a .yshortcuts   { color: #3386e4 ; text-decoration:none;}

					    .bodyContent img{
					      height:auto;
					      max-width:498px;
					    }

					    /**
					    * @tab Footer
					    * @section footer link
					    * @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
					    */
					    a.blue-btn {
					       background-color: #5098ea;
						  border: none;
						  color: white;
						  padding: 15px 32px;
						  text-align: center;
						  text-decoration: none;
						  display: inline-block;
						  font-size: 16px;
						  margin: 4px 2px;
						  cursor: pointer;
					    }
					    a.btn-danger {
						  background-color: red;
						  border: none;
						  color: white;
						  padding: 15px 32px;
						  text-align: center;
						  text-decoration: none;
						  display: inline-block;
						  font-size: 16px;
						  margin: 4px 2px;
						  cursor: pointer;
						}
						a.btn-success {
						  background-color: green;
						  border: none;
						  color: white;
						  padding: 15px 32px;
						  text-align: center;
						  text-decoration: none;
						  display: inline-block;
						  font-size: 16px;
						  margin: 4px 2px;
						  cursor: pointer;
						}



					    @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
					      body[yahoo] .hide {display: none!important;}
					      body[yahoo] .buttonwrapper {background-color: transparent!important;}
					      body[yahoo] .button {padding: 0px!important;}
					      body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
					      body[yahoo] .unsubscribe { font-size: 14px; display: block; margin-top: 0.714em; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important;}
					    }
					    /*@media only screen and (min-device-width: 601px) {
					      .content {width: 600px !important;}
					    }*/
					    @media only screen and (max-width: 480px), screen and (max-device-width: 480px) {
					      h1 {
					        font-size:34px !important;
					      }
					      h2{
					        font-size:30px !important;
					      }
					      h3{
					        font-size:24px !important;
					      }
					      h4{
					        font-size:18px !important;
					      }
					      h5{
					        font-size:16px !important;
					      }
					      h6{
					        font-size:14px !important;
					      }
					      p {
					        font-size: 18px !important;
					      }
					      .bodyContent {
					        padding: 6% 5% 6% 6% !important;
					      }
					      .bodyContent img {
					        max-width: 100% !important;
					      }
					      #bodyCellFooter {padding-top: 20px !important;}
					      .hide {display:none !important;}
					    }
					    .ii a[href] {color: inherit !important;}
					    span > a, span > a[href] {color: inherit !important;}
					    a > span, .ii a[href] > span {text-decoration: inherit !important;}
					  </style>
					</head>

					<body yahoo bgcolor="#ffffff">
					<table width="100%" bgcolor="#ffffff" border="0" cellpadding="10" cellspacing="0">
					<tr>
					  <td>
					    <!--[if (gte mso 9)|(IE)]>
					      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
					        <tr>
					          <td>
					    <![endif]-->
					    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
					      <tr>
									<td align="center" valign="top">
											<!-- BEGIN BODY // -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
												<tr>
													<td valign="top" class="bodyContent" mc:edit="body_content">
					                  <p>Hi $ApproverName,</p>
														<p>$SenderComments</p>
														<h4>Work Order Details:</h4>
														<p>ID: $Incident_ID</p>
														<p>Subject: $IncSubject</p> 
														<p>Categories: $TopCategory $MiddleCategory $LastCategory </p>  
					                  <p>We would love to get your feedback using below mentioned links:</p> 
					                  
					                  <a class="btn-success" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=Approve&RecordID=$RecordID"><strong>Approve</strong></a>
					                  <a class="btn-danger" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=Reject&RecordID=$RecordID"><strong>Reject</strong></a>
					                  <a class="blue-btn" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=RFI&RecordID=$RecordID"><strong>Request For Information</strong></a>
					                  <br><br><br>
					                    <p>Thanks,</p>
										<p>$SenderName</p>
										<p>Team Goodwill | Helpdesk</p> 
													</td>
												</tr>
												
											</table>
											<!-- // END BODY --> 
										</td>
								</tr>
								

					    </table>
					    <!--[if (gte mso 9)|(IE)]>
					          </td>
					        </tr>
					    </table>
					    <![endif]-->
					    </td>
					  </tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" class="hide" width="600">
					  <tr>
					    <td height="1" class="hide" style="min-width:600px; font-size:0px;line-height:0px;">
					      <img height="1" width="600" src="http://c0185784a2b233b0db9b-d0e5e4adc266f8aacd2ff78abb166d77.r51.cf2.rackcdn.com/blank.jpg" style="min-width: 600px; width: 600px; max-height: 1px; min-height: 1px; text-decoration: none; border: none; -ms-interpolation-mode: bicubic;" />
					    </td>
					  </tr>
					</table>
					</body>
					</html>
EMAIL;


				 //create mail message object
				$mm = new RNCPHP\MailMessage();
				 
				//set TO,CC,BCC fields as necessary
				    $mm->To->EmailAddresses = array($email_1);
				    //$mm->FromMailbox = 'info@ephlux.com'; --> not Working
				 
				//set subject
				    $mm->Subject = "Message from Oracle Service Cloud For Work Order #".$Incident_ID;
				 
				//set body of the email
				    $mm->Body->Text = $text_body;
				    $mm->Body->Html = $html_body;
				 
				//set marketing options
					$mm->Options->IncludeOECustomHeaders = false;
				 
				//send email
				    $mm->send();
				   
				    echo "<br>Email Sent to ".$email_1.".";
				

	}

	function SendReminderEmail($ApprovalRecordID,$TopCategory,$MiddleCategory,$LastCategory)
	{ 
		$ApprovalRecord   		= RNCPHP\Goodwill\External_Approvals::fetch( $ApprovalRecordID );
		$Incident_ID 			= $ApprovalRecord->Incident->ID;
		$IncSubject 			= $ApprovalRecord->Incident->Subject;
		$Record_ID 				= $ApprovalRecord->ID;
		$Contact_ID 			= $ApprovalRecord->ApproverContact_ID->ID; 
		$ApproverEmail 			= $ApprovalRecord->ApproverContact_ID->Emails[0]->Address;
		$SenderComments 		= $ApprovalRecord->SenderComments;
		  	
		$IncidentID 			= base64_encode($Incident_ID); 
		$ApproverContact_ID 	= base64_encode($Contact_ID);
		$RecordID 				= base64_encode($Record_ID); 
		$ApproverName 			= $ApproverContact->LookupName;  
		
		$SenderName 	 		= $ApprovalRecord->CreatedByAccount->LookupName;

		    $email_1 = $ApproverEmail; 

			$text_body = "Sample text body:\nThe text part of the message.";
			$html_body = <<<EMAIL
<html>		
			<head>
			  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			  <title>Simple Call to Action</title>
			  <style type="text/css">
			    /* /\/\/\/\/\/\/\/\/ CLIENT-SPECIFIC STYLES /\/\/\/\/\/\/\/\/ */
			    #outlook a{padding:0;} /* Force Outlook to provide a "view in browser" message */
			    .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
			    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing */
			    body, table, td, p, a, li, blockquote{-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
			    table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;} /* Remove spacing between tables in Outlook 2007 and up */
			    td ul li {
			      font-size: 16px;
			    }
			    /* /\/\/\/\/\/\/\/\/ RESET STYLES /\/\/\/\/\/\/\/\/ */
			    body {margin: 0; padding: 0; min-width: 100%!important;}
			    img{
			      max-width:100%;
			      border:0;
			      line-height:100%;
			      outline:none;
			      text-decoration:none;
			    }
			    table{border-collapse:collapse !important;}
			    .content {width: 100%; max-width: 600px;}
			    .content img { height: auto; min-height: 1px; }

			    #bodyCellFooter{margin:0; padding:0; width:100% !important;padding-top:39px;padding-bottom:15px;}

			    #templateContainer{
			      border: 1px solid #e2e2e2;
			      border-radius: 4px;
			      background-clip: padding-box;
			      border-spacing: 0;
			    }

			    /**
			    * @tab Page
			    * @section heading 1
			    * @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
			    * @style heading 1
			    */
			    h1{
			      color:#2e2e2e;
			      display:block;
			      font-family:Helvetica;
			      font-size:26px;
			      line-height:1.385em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }

			    /**
			    * @tab Page
			    * @section heading 2
			    * @tip Set the styling for all second-level headings in your emails.
			    * @style heading 2
			    */
			    h2{
			      color:#2e2e2e;
			      display:block;
			      font-family:Helvetica;
			      font-size:22px;
			      line-height:1.455em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }

			    /**
			    * @tab Page
			    * @section heading 3
			    * @tip Set the styling for all third-level headings in your emails.
			    * @style heading 3
			    */
			    h3{
			      color:#545454;
			      display:block;
			      font-family:Helvetica;
			      font-size:18px;
			      line-height:1.444em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }

			    /**
			    * @tab Page
			    * @section heading 4
			    * @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
			    * @style heading 4
			    */
			    h4{
			      color:#545454;
			      display:block;
			      font-family:Helvetica;
			      font-size:14px;
			      line-height:1.571em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }


			    h5{
			      color:#545454;
			      display:block;
			      font-family:Helvetica;
			      font-size:13px;
			      line-height:1.538em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }


			    h6{
			      color:#545454;
			      display:block;
			      font-family:Helvetica;
			      font-size:12px;
			      line-height:2.000em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }

			    p {
			      color:#545454;
			      display:block;
			      font-family:Helvetica;
			      font-size:16px;
			      line-height:1.500em;
			      font-style:normal;
			      font-weight:normal;
			      letter-spacing:normal;
			      margin-top:0;
			      margin-right:0;
			      margin-bottom:15px;
			      margin-left:0;
			      text-align:left;
			    }

			    .unSubContent a:visited { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
			    .unSubContent a:focus   { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
			    .unSubContent a:hover   { color: #a1a1a1; text-decoration:underline; font-weight:normal;}
			    .unSubContent a:link   { color: #a1a1a1 ; text-decoration:underline; font-weight:normal;}
			    .unSubContent a .yshortcuts   { color: #a1a1a1 ; text-decoration:underline; font-weight:normal;}

			    .unSubContent h6 {
			      color: #a1a1a1;
			      font-size: 12px;
			      line-height: 1.5em;
			      margin-bottom: 0;
			    }

			    .bodyContent{
			      color:#505050;
			      font-family:Helvetica;
			      font-size:14px;
			      line-height:150%;
			      padding-top:3.143em;
			      padding-right:3.5em;
			      padding-left:3.5em;
			      padding-bottom:3.143em;
			      text-align:left;
			    }

			    /**
			    * @tab Body
			    * @section body link
			    * @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
			    */
			    a:visited { color: #3386e4; text-decoration:none;}
			    a:focus   { color: #3386e4; text-decoration:none;}
			    a:hover   { color: #3386e4; text-decoration:none;}
			    a:link   { color: #3386e4 ; text-decoration:none;}
			    a .yshortcuts   { color: #3386e4 ; text-decoration:none;}

			    .bodyContent img{
			      height:auto;
			      max-width:498px;
			    }

			    /**
			    * @tab Footer
			    * @section footer link
			    * @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
			    */
			    a.blue-btn {
			       background-color: #5098ea;
				  border: none;
				  color: white;
				  padding: 15px 32px;
				  text-align: center;
				  text-decoration: none;
				  display: inline-block;
				  font-size: 16px;
				  margin: 4px 2px;
				  cursor: pointer;
			    }
			    a.btn-danger {
				  background-color: red;
				  border: none;
				  color: white;
				  padding: 15px 32px;
				  text-align: center;
				  text-decoration: none;
				  display: inline-block;
				  font-size: 16px;
				  margin: 4px 2px;
				  cursor: pointer;
				}
				a.btn-success {
				  background-color: green;
				  border: none;
				  color: white;
				  padding: 15px 32px;
				  text-align: center;
				  text-decoration: none;
				  display: inline-block;
				  font-size: 16px;
				  margin: 4px 2px;
				  cursor: pointer;
				}



			    @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
			      body[yahoo] .hide {display: none!important;}
			      body[yahoo] .buttonwrapper {background-color: transparent!important;}
			      body[yahoo] .button {padding: 0px!important;}
			      body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
			      body[yahoo] .unsubscribe { font-size: 14px; display: block; margin-top: 0.714em; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important;}
			    }
			    /*@media only screen and (min-device-width: 601px) {
			      .content {width: 600px !important;}
			    }*/
			    @media only screen and (max-width: 480px), screen and (max-device-width: 480px) {
			      h1 {
			        font-size:34px !important;
			      }
			      h2{
			        font-size:30px !important;
			      }
			      h3{
			        font-size:24px !important;
			      }
			      h4{
			        font-size:18px !important;
			      }
			      h5{
			        font-size:16px !important;
			      }
			      h6{
			        font-size:14px !important;
			      }
			      p {
			        font-size: 18px !important;
			      }
			      .bodyContent {
			        padding: 6% 5% 6% 6% !important;
			      }
			      .bodyContent img {
			        max-width: 100% !important;
			      }
			      #bodyCellFooter {padding-top: 20px !important;}
			      .hide {display:none !important;}
			    }
			    .ii a[href] {color: inherit !important;}
			    span > a, span > a[href] {color: inherit !important;}
			    a > span, .ii a[href] > span {text-decoration: inherit !important;}
			  </style>
			</head>

			<body yahoo bgcolor="#ffffff">
			<table width="100%" bgcolor="#ffffff" border="0" cellpadding="10" cellspacing="0">
			<tr>
			  <td>
			    <!--[if (gte mso 9)|(IE)]>
			      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
			        <tr>
			          <td>
			    <![endif]-->
			    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
			      <tr>
							<td align="center" valign="top">
									<!-- BEGIN BODY // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateContainer">
										<tr>
											<td valign="top" class="bodyContent" mc:edit="body_content">
			                  <p>Hi $ApproverName,</p>
												<p>$SenderComments</p>
												<h4>Work Order Details:</h4>
												<p>ID: $Incident_ID</p>
												<p>Subject: $IncSubject</p> 
												<p>Categories: $TopCategory $MiddleCategory $LastCategory </p> 
												   
			                  <p>We would love to get your feedback using below mentioned links:</p> 
			                  
			                  <a class="btn-success" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=Approve&RecordID=$RecordID"><strong>Approve</strong></a>
			                  <a class="btn-danger" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=Reject&RecordID=$RecordID"><strong>Reject</strong></a>
			                  <a class="blue-btn" href="https://goodwillsocal.custhelp.com/app/ExternalFeedBack?IncidentID=$IncidentID&ContactID=$ApproverContact_ID&Action=RFI&RecordID=$RecordID"><strong>Request For Information</strong></a>
			                  <br><br><br> 
			                    <p>Thanks,</p>
								<p>$SenderName</p>
								<p>Team Goodwill | Helpdesk</p> 
											</td>
										</tr>
										
									</table>
									<!-- // END BODY --> 
								</td>
						</tr>
						

			    </table>
			    <!--[if (gte mso 9)|(IE)]>
			          </td>
			        </tr>
			    </table>
			    <![endif]-->
			    </td>
			  </tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" class="hide" width="600">
			  <tr>
			    <td height="1" class="hide" style="min-width:600px; font-size:0px;line-height:0px;">
			      <img height="1" width="600" src="http://c0185784a2b233b0db9b-d0e5e4adc266f8aacd2ff78abb166d77.r51.cf2.rackcdn.com/blank.jpg" style="min-width: 600px; width: 600px; max-height: 1px; min-height: 1px; text-decoration: none; border: none; -ms-interpolation-mode: bicubic;" />
			    </td>
			  </tr>
			</table>
			</body>
			</html>

EMAIL;




			 //create mail message object
			    $mm = new RNCPHP\MailMessage();
			 
			//set TO,CC,BCC fields as necessary
			    $mm->To->EmailAddresses = array($email_1);
			    //$mm->FromMailbox = 'info@ephlux.com'; --> not Working
			 
			//set subject
			    $mm->Subject = "Reminder Email From Goodwill For Work Order #".$Incident_ID; 
			 
			//set body of the email
			    $mm->Body->Text = $text_body;
			    $mm->Body->Html = $html_body;
			 
			//set marketing options
				$mm->Options->IncludeOECustomHeaders = false;
			 
			//send email
			    $mm->send();
			    echo "<br> Now reminder Email Sent to ".$email_1.".";  

	}

	function WOassignToTech()
	{
		$TechID 	= $_POST['TechID'];
		$IncID 		= $_POST['IncidentId'];
		if ($TechID > 0) 
		{
			$Technician	= RNCPHP\Contact::fetch($TechID);
			$Email 		= $Technician->Emails[0]->Address;
		}
		if ($IncID) {
			$Incident 	= RNCPHP\Incident::fetch($IncID);
			$Category 	= $Incident->Category->LookupName;
			$Subject 	= $Incident->Subject;
		}
		//echo"<pre>"; print_r($Email) ;exit();
		$EmailSubject = "A New Work Order Assigned.";

		$text_body = "A New Work Order Assigned.";
		$html_body = 

<<<EMAIL
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<div>
		<p>A new Work Order assigned.</p><br/> 
		<h4>Details:</h4>
		<label>Order ID: $IncID</label><br/>
		<label>Subject: $Subject</label> <br/>
		<label>Category: $Category</label><br/>
		
 
	</div>
</body>
</html>


EMAIL;
		
		$Incident->Severity->ID = 2;
		$Incident->save();
		$this->EmailRequirement($Email,$IncID,$text_body,$html_body,$EmailSubject);
	}

	function EmailRequirement($email_1,$Incident_ID,$text_body,$html_body,$Subject)
	{
			 //create mail message object
			    $mm = new RNCPHP\MailMessage();
			 
			//set TO,CC,BCC fields as necessary
			    $mm->To->EmailAddresses = array($email_1);
			    //$mm->FromMailbox = 'info@ephlux.com'; --> not Working
			 
			//set subject
			    $mm->Subject = $Subject; 
			 
			//set body of the email
			    $mm->Body->Text = $text_body;
			    $mm->Body->Html = $html_body;
			 
			//set marketing options
				$mm->Options->IncludeOECustomHeaders = false;
			 
			//send email
			    $mm->send();
			    //echo "<br> Now reminder Email Sent to ".$email_1."."; 
	}
	public function reoccurringWO()
	{
		print_r($_POST);
		if ($_POST['firstDate'] > date('Y-m-d')) {
			# code...
		}
	}

	public function tech_app()
	{
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
	}
}

