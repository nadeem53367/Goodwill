<?php

//define('Incident_Status',111);

namespace Custom\Models;
use RightNow\Connect\v1_3 as RNCPHP;

define("DMQueue",                           2);
define("Queue_IT_HELP_NewWorkOrders",       7);
define("Queue_IT_OPS_NewWorkOrders",        13);
define("Queue_IT_Procurement",              9);
define("Queue_IT_VP",                       12);
define("Queue_IT_AdminAsstNewWO",           17);
define("Queue_IT_ProgrammerNewWO",          16);
define("Queue_IT_NetworkAdminWO",           14);
define("Queue_SupplyChain",                 24);
define("Queue_SC_DispOntario",              27);
define("Queue_SC_DispFletcherSquare",       28);

define("StatusUnreOpen",    111); 
define("StatusUnresolved",  1); 
define("StatusOpen",  145); 

define("IT_Supervisor",     9); //profile ID
define("IT_OPS_Manager",    11); //profile ID
define("IT_Procurement",    10); //profile ID
define("IT_VP",             14); //profile ID
define("IT_AdminAsst",      17); //profile ID
define("IT_Programmer",     16); //profile ID
define("IT_NetworkAdmin",   15); //profile ID


class GWModel extends \RightNow\Models\Base
{
    function __construct()
    {
        parent::__construct();
    }
     
    /**
     * This function can be executed a few different ways depending on where it's being called:
     *
     * From a widget or another model: $this->CI->model('custom/Sample')->sampleFunction();
     *
     * From a custom controller: $this->model('custom/Sample')->sampleFunction();
     *
     * Everywhere else: $CI = get_instance();
     *                  $CI->model('custom/Sample')->sampleFunction();
     */
    function create_incident($postData)
    { 
        if ($postData) 
        {   
           // echo "<pre>"; print_r($postData);
            $contactID              = (int) $postData['contactID'];
            $subject                = $postData['Incident_Subject'];
            $Incident_Threads       = $postData['Incident_Threads'];
            $Category               = (int) $postData['Category'];
            $Location               = (int) $postData['Location'];
            $DM                     = (int) $postData['DM'];
            $Procurement            = $postData['Procurement'];
            $aFile                  = $postData['aFile']['myfile'];
            $SubDepartment          = (int) $postData['SubDepartment'];
            $ContactAssets          = (int) $postData['ContactAssets'];

            if ($postData['CallFrom'] == 'Agent_Desktop')  
            {
                $IncidentId         = (int) $postData['IncidentId'];
                $incident           = RNCPHP\Incident::fetch($IncidentId);
                $RecordDepartment   = $incident->CustomFields->Goodwill->ServiceDepartment->ID ;
 
                if ($RecordDepartment == 2 ) // IT  
                {
                    $incident  = $this->Manager_IT_Ticket_For_CSR($postData);
                }
                else
                {
                    
                    if ($Category) 
                    {
                        $incident->Category                         = RNCPHP\ServiceCategory::fetch($Category);
                    }
                    if ($Location != '') 
                    {
                        $incident->CustomFields->Goodwill->Location = RNCPHP\Goodwill\Location::fetch($Location); 
                    }
                    if ($DM !='')  
                    {
                        
                            $queryResult               = RNCPHP\ROQL::query("Select * from Goodwill.DM_Delegation where From_DM =".$DM)->next();
                            $DMDelegation                = $queryResult->next();
                            if ($DMDelegation) 
                            {
                                if ($DMDelegation['Status'] == 1 || $DMDelegation['Status'] == 'Yes') 
                                {
                                    if ($DMDelegation['Valid_Till'] > date('Y-m-d') || $DMDelegation['Valid_Till'] == date('Y-m-d')) 
                                    {
                                        $ChangeDM = RNCPHP\Account::fetch($DMDelegation['To_DM']);
                                        $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                        $incident->AssignedTo->Account                  = $ChangeDM;
                                        $incident->CustomFields->Goodwill->AssignedBy   = $ChangeDM;
                                    }else
                                    {
                                        $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                        $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                                        $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                                    }

                                }else
                                {
                                    $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                    $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                                    $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                                }

                            }
                            else
                            {
                                $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                                $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                            }
                        
                       

                        $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
                        $incident->Queue->ID                            = (int) DMQueue;
                    }
                    if ($SubDepartment > 0) 
                    {
                        $incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($SubDepartment);
                    }
                } 
            }else
            {    
                /*if incident is related to facilities department*/
                if ($postData['Department'] == 1) 
                {
                     
                    $CI                                         = & get_instance(); 
                    $incident                                   = new RNCPHP\Incident(); 
                    $incident->Subject                          = (string) $subject;
                    $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
                    $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
                    $incident->StatusWithType->Status->ID       = (int) StatusUnreOpen ; 

                    $incident->Threads                          = new RNCPHP\ThreadArray();
                    $incident->Threads[0]                       = new RNCPHP\Thread();
                    $incident->Threads[0]->EntryType            = new RNCPHP\NamedIDOptList();
                    $incident->Threads[0]->EntryType->ID        = (int)  3;
                    $incident->Threads[0]->Text                 = $Incident_Threads; 

                    if ($Category) 
                    {
                        $incident->Category                     = RNCPHP\ServiceCategory::fetch($Category);
                    }
                    
                    if ($Location != '') 
                    {
                        $incident->CustomFields->Goodwill->Location = RNCPHP\Goodwill\Location::fetch($Location); 
                    } 
                    
                    if ($postData['Procurement']) 
                    {
                        if ($postData['Procurement'] == 'Yes') 
                        {
                            $incident->CustomFields->Goodwill->IsProcurementRequired = true; 
                        }else 
                        {
                            $incident->CustomFields->Goodwill->IsProcurementRequired = false;  
                        }
                        
                    }
                    if ($DM !='') 
                    {
                        $queryResult               = RNCPHP\ROQL::query("Select * from Goodwill.DM_Delegation where From_DM =".$DM)->next();
                        $DMDelegation                = $queryResult->next();
                        if ($DMDelegation) 
                        {
                            if ($DMDelegation['Status'] == 1 || $DMDelegation['Status'] == 'Yes') 
                            {
                                if ($DMDelegation['Valid_Till'] > date('Y-m-d') || $DMDelegation['Valid_Till'] == date('Y-m-d')) 
                                {
                                    $ChangeDM = RNCPHP\Account::fetch($DMDelegation['To_DM']);
                                    $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                    $incident->AssignedTo->Account                  = $ChangeDM;
                                    $incident->CustomFields->Goodwill->AssignedBy   = $ChangeDM;
                                }else
                                {
                                    $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                    $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                                    $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                                }

                            }else
                            {
                                $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                                $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                                $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                            }

                        }else  
                        {
                            $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                            $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                            $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                        }
                        
                        $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
                        $incident->Queue->ID                            = (int) DMQueue;
                    }
                    
                    if ($postData['Department']) 
                    {
                        $department = (int) $postData['Department'];
                       // $user = new RNCPHP\Goodwill\ServiceDepartment(); 
                    $incident->CustomFields->Goodwill->ServiceDepartment = RNCPHP\Goodwill\ServiceDepartment::fetch($department);
                         
                    }
                          
                   /* $incident->Channel = new RNCPHP\NamedIDLabel();
                    $incident->Channel->LookupName = "Service Web"; */
                    if ($SubDepartment > 0) 
                    {
                        $incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($SubDepartment);
                    }

                }
                else if ($postData['Department'] == 2) 
                {
                   $incident  = $this->Manager_IT_Ticket($postData);
                }
                else if ($postData['Department'] == 3) 
                {
                  //  echo "In Depart";
                   $incident  = $this->Manager_SC_Ticket($postData);
                }

                if($aFile['name'])
                {
                    $incident->FileAttachments      = new RNCPHP\FileAttachmentIncidentArray();
                    $fattach                        = new RNCPHP\FileAttachmentIncident();
                    $fattach->ContentType           = $aFile['type'];
                    $fp                             = $fattach->makeFile();
                    $mystring                       = file_get_contents($aFile['tmp_name'], "wb");
                    fwrite( $fp, $mystring);
                    fclose( $fp );
                    $fattach->FileName              = $aFile['name'];
                    $fattach->FileName              = $aFile['name']; 
                    $fattach->Name                  = $aFile['name'];
                    $incident->FileAttachments[]    = $fattach; 
                }
            } 
           
            if($incident)
            {
                $incident->PrimaryContact           = RNCPHP\Contact::fetch( $contactID );   
                $incident->save();   
            }
            if ($postData['Department'] == 2 AND $postData['CallFrom'] != 'Agent_Desktop') 
            {
               // print_r($incident);
                 if ($ContactAssets > 0) 
                {
                    $NewAssetObject             = new RNCPHP\Goodwill\Assets();
                    $NewAssetObject->Incident   = (int) $incident->ID ;
                    $NewAssetObject->Asset      = RNCPHP\Asset::fetch($ContactAssets); 
                    $NewAssetObject->save(); 
                }
            }
            
            return $incident;     
        }
    }

    /*
    @author : Umair Ahmed
    @date   : 12-April-19
    @Desc   : This function manages all the tickets for IT Dept  
    */
    function Manager_IT_Ticket($postData)
    {
        $subject                = $postData['Incident_Subject'];
        $Incident_Threads       = $postData['Incident_Threads'];
        $Category               = (int) $postData['Category'];
        $Location               = (int) $postData['Location'];
        $aFile                  = $postData['aFile']['myfile'];
        $AnswerID               = (int) $postData['AnswerID'];
        $SubDepartment          = (int) $postData['SubDepartment'];
        

        $incident                                   = new RNCPHP\Incident(); 
        $incident->Subject                          = (string) $subject;
        $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
        $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
        $incident->StatusWithType->Status->ID       = (int) StatusUnresolved ; 

        $incident->Threads                          = new RNCPHP\ThreadArray();
        $incident->Threads[0]                       = new RNCPHP\Thread();
        $incident->Threads[0]->EntryType            = new RNCPHP\NamedIDOptList();
        $incident->Threads[0]->EntryType->ID        = (int)  3;
        $incident->Threads[0]->Text                 = $postData['Incident_Threads']; 


        if ($Category)  { $incident->Category                           = RNCPHP\ServiceCategory::fetch($Category);                                 }
        if ($Location)  { $incident->CustomFields->Goodwill->Location   = RNCPHP\Goodwill\Location::fetch($Location);                               } 
        if ($postData['Department']) 
        {
            $incident->CustomFields->Goodwill->ServiceDepartment        = RNCPHP\Goodwill\ServiceDepartment::fetch((int) $postData['Department']);  
        }
       
        if ($SubDepartment > 0) 
        {
            $incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($SubDepartment);
        }

       
        $Resource   = IT_Supervisor;  
        $Queue      = Queue_IT_HELP_NewWorkOrders;

        if($AnswerID > 0 )
        {
            $Answer = RNCPHP\Answer::fetch($AnswerID);

            if($Answer->CustomFields->Goodwill->Approval->ID == 1 and $Answer->CustomFields->Goodwill->Approval->LookupName == "Yes")
            {
                $Resource   = IT_OPS_Manager; 
                $Queue      = Queue_IT_OPS_NewWorkOrders;  
            }
        }

        $queryResult               = RNCPHP\ROQL::queryObject("SELECT Account FROM Account WHERE Account.Profile.ID=".$Resource." AND Account.CustomFields.Goodwill.ProfileMainAccount = 1 LIMIT 1")->next();
        $IT_Account                = $queryResult->next();

         if($IT_Account)
        {
            $incident->AssignedTo                               = new RNCPHP\GroupAccount();
            $incident->AssignedTo->Account                      = $IT_Account;
            $incident->CustomFields->Goodwill->CurrentlyAssign  = $IT_Account;
        }
        
        $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
        $incident->Queue->ID                            = (int) $Queue;

       
        return $incident;     
    }

    /*
    @author : Umair Ahmed
    @date   : 23-April-19
    @Desc   : This function manages all the tickets for IT Dept created from technicians 
    */
    function Manager_IT_Ticket_For_CSR($postData)
    {
        
        $Location               = (int) $postData['Location'];
        $SubDepartment          = (int) $postData['SubDepartment'];

        $IncidentId             = (int) $postData['IncidentId'];
        $incident               = RNCPHP\Incident::fetch($IncidentId);
        $incidentAssigne        = $incident->AssignedTo->Account->ID;
        $incident->CustomFields->Goodwill->CurrentlyAssign   = RNCPHP\Account::fetch($incidentAssigne);  

        if ($Location != '') 
        {
            $incident->CustomFields->Goodwill->Location = RNCPHP\Goodwill\Location::fetch($Location); 
        }
        if ($SubDepartment > 0) 
        {
            $incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($SubDepartment);
        }

        if ($incident->AssignedTo->Account->Profile->ID == IT_OPS_Manager) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_OPS_NewWorkOrders;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_Supervisor) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_HELP_NewWorkOrders;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_Procurement) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_Procurement;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_VP) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_VP;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_AdminAsst) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_AdminAsstNewWO;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_Programmer) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_ProgrammerNewWO;
        }
        if ($incident->AssignedTo->Account->Profile->ID == IT_NetworkAdmin) 
        {
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_IT_NetworkAdminWO;
        }

        

        /*$incident->Queue                                = new RNCPHP\NamedIDLabel(); 
        $incident->Queue->ID                            = (int) Queue_IT_HELP_NewWorkOrders;*/
        
        return $incident;
    }
    /**************     END     ******************/

    function Manager_SC_Ticket($postData)
    {
        $subject                = $postData['Incident_Subject'];
        $Incident_Threads       = $postData['Incident_Threads'];
        $Category               = (int) $postData['MiddleCategory']; 
        //$Location               = (int) $postData['RSMLocation'];
        //$RSM                    = (int) $postData['RSM'];
       
        $RSMdepartment          = (int) $postData['RSMdepartment'];
        //$RSMdispetcher          = (int) $postData['RSMdispetcher'];
        $SClocations            = (int) $postData['SClocations'];

        $incident                                   = new RNCPHP\Incident(); 
        $incident->Subject                          = (string) $subject;
        $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
        $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
        $incident->StatusWithType->Status->ID       = (int) StatusOpen ; 

        $incident->Threads                          = new RNCPHP\ThreadArray();
        $incident->Threads[0]                       = new RNCPHP\Thread();
        $incident->Threads[0]->EntryType            = new RNCPHP\NamedIDOptList();
        $incident->Threads[0]->EntryType->ID        = (int)  3;
        $incident->Threads[0]->Text                 = $postData['Incident_Threads'];  

        if ($Category)  
        { 
            $incident->Category                                 = RNCPHP\ServiceCategory::fetch($Category);
        }
        if ($SClocations)  
        { 
            $Location       = RNCPHP\Goodwill\Location::fetch($SClocations);
            $incident->CustomFields->Goodwill->Location         = $Location;
            if ($Location) 
            {
                $SC_HUB                     = $Location->SupplyChainHub->ID;
                $RouteTrashCleanService     = $Location->RouteTrashCleanService;
                
                if ($SC_HUB > 0) 
                {

                    if ($SC_HUB == 1) 
                    {
                        if ($RouteTrashCleanService == 1 || $RouteTrashCleanService == true) 
                        {
                            if ($Category  == 289) 
                            {
                                $incident->Queue                                    = new RNCPHP\NamedIDLabel(); 
                                $incident->Queue->ID                                = (int) Queue_SC_DispOntario;
                            }else
                            {
                                $incident->Queue                                    = new RNCPHP\NamedIDLabel(); 
                                $incident->Queue->ID                                = (int) Queue_SC_DispFletcherSquare;
                            }
                            
                        }else
                        {
                            $incident->Queue                                    = new RNCPHP\NamedIDLabel(); 
                            $incident->Queue->ID                                = (int) Queue_SC_DispFletcherSquare;
                        } 
                    }else
                    {
                        $incident->Queue                                    = new RNCPHP\NamedIDLabel(); 
                        $incident->Queue->ID                                = (int) Queue_SC_DispOntario;  
                    } 
                }
            }
             
        }                                
        if ($postData['Department']) 
        {
            $incident->CustomFields->Goodwill->ServiceDepartment = RNCPHP\Goodwill\ServiceDepartment::fetch((int) $postData['Department']);  
        }
       
       /* if ($RSMdepartment > 0) 
        {
            $incident->CustomFields->Goodwill->GWDepartments    = RNCPHP\Goodwill\GWDepartments::fetch($RSMdepartment);
        }*/
        /*if ($RSM > 0) 
        {
            $incident->CustomFields->Goodwill->StoreManager    = RNCPHP\Contact::fetch($RSM);
        }*/
        /*if ($RSMdispetcher) 
        {
            $incident->AssignedTo                           = new RNCPHP\GroupAccount();
            $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($RSMdispetcher); 
            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) Queue_SupplyChain;  
        }*/    

        return $incident;
    }

    function getSubCategories($data)   
    {
        $id                     =  $data;
        $ServiceCategory        = array();
        $sql                = "Select ServiceCategory FROM ServiceCategory where ServiceCategory.Parent.ID=".$id." LIMIT 25";
        $Query  = RNCPHP\ROQL::queryObject($sql)->next(); 
        while($result = $Query->next())
        {
           $ServiceCategory[] = array('ID' => $result->ID, 'LookupName'=>$result->LookupName );
        }
        return $ServiceCategory;  
    }
  
 
    function createAssetsHisstory($Data)
    {
        
        $incidentID = $Data['IncidentID'];
        $Assets     = array();
        $sql        = "SELECT Asset FROM Asset WHERE Asset.CustomFields.Goodwill.Incident.ID = ".$incidentID;
        $Query      = RNCPHP\ROQL::queryObject($sql)->next(); 
        while($result = $Query->next())
        {
           $Assets[] = array('ID' => $result->ID);
        }

        return $Assets;  
    }

    function getApprovalRecords($Data) 
    {
        
        $incidentID = $Data;//['IncidentID'];
        $External_Approvals     = array(); 
        $sql        = "Select * FROM Goodwill.External_Approvals WHERE Goodwill.External_Approvals.Approval_Action IS NULL AND Goodwill.External_Approvals.Incident.ID = ".$incidentID ;
        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        while($result = $Query->next())
        {
           $External_Approvals[] = array('ID' => $result['ID']);
          
        }
      //  print_r($External_Approvals);
        return $External_Approvals;

    }

    function getExternalApprovalRecord($Incident_ID, $RecordFilter,$NotRespond) 
    {
       
        $External_Approvals     = array();   
         
        if ($NotRespond == '') 
        {
            $sql    = "Select ID FROM Goodwill.External_Approvals WHERE Incident.ID = ".$Incident_ID. " AND  ApprovalContactStatus ='".$RecordFilter."' ";
              
        }elseif ($NotRespond == 'notRespond') 
        {
            $sql = "Select ID FROM Goodwill.External_Approvals WHERE Incident.ID = ".$Incident_ID. " AND  ApprovalContactStatus ='".$RecordFilter."' AND Approval_Action.ID IS NULL ";
        }
       
        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        while($result = $Query->next())
        {
           $External_Approvals[] = $result;
          
        } 
      //  print_r($External_Approvals);
        return $External_Approvals;

    } 

    public function saveExternalFeedBack($postData)
    {
        $Comments               = $postData['Incident_Threads'];
        $IncidentID             = (int) $postData['IncidentId'];
        $ContactID              = (int)$postData['ApproverContactID']; 
        $ApprovalRecordID       = (int)$postData['RecordID'];
        $ActionType             = $postData['ActionType']; 
        
        $FeedBack                        = RNCPHP\Goodwill\External_Approvals::fetch( $ApprovalRecordID ); 
        $FeedBack->ApproverComments      = $Comments;

        $FeedBack->Action_Time           = time();
        
        if ($ActionType == 'Approve') 
        {
            $FeedBack->Approval_Action              = 1;
        }
        elseif ($ActionType == 'Reject') 
        {
            $FeedBack->Approval_Action              = 2;
        }
        elseif ($ActionType == 'RFI') 
        {
            $FeedBack->Approval_Action      = 3;  
            $RecordCreatedBy                = (int) $FeedBack->CreatedByAccount->ID ;

            $new_task = new RNCPHP\Task();
    
            //Set assigned account
            $new_task->AssignedToAccount = RNCPHP\Account::fetch($RecordCreatedBy); 
         
            //Set comment
            $new_task->CustomFields->Goodwill->Comments= $Comments;    
            
            //Set contact
            $new_task->Contact = RNCPHP\Contact::fetch($ContactID); 
                            
            //Set due time
            $new_task->DueTime=strtotime(date('Y-m-d'). ' + 2 days'); 
            
            //Set Name
            $new_task->Name="New RFI recieved for work order #".$IncidentID; 
         
            
            //Set Priority
            $new_task->Priority= new RNCPHP\NamedIDOptList();
            $new_task->Priority->ID=2;
             
          
            //Add Answer and Incident
            $new_task->ServiceSettings=new RNCPHP\TaskServiceSettings();
            $new_task->ServiceSettings->Incident=RNCPHP\Incident::fetch($IncidentID);
          
            //Set the current status
            $new_task->StatusWithType = new RNCPHP\StatusWithType();
            $new_task->StatusWithType->Status->LookupName = 'Waiting';  
         
            //Set Task type
            $new_task->TaskType=new RNCPHP\NamedIDOptList();
            $new_task->TaskType->LookupName='Incidents'; 
         
            $new_task->save(); 
            //echo "Task object has been created successfully with ID {$new_task->ID}";
        }
      
        
        $FeedBack->save(); 
        return $FeedBack; 

       
    }
    /*DM Delegation */
    public function DM_Delegation($DM,$IncidentId) 
    {
        //echo "In DM fun";
        if ($DM !='')  
        {
            $incident           = RNCPHP\Incident::fetch($IncidentId);
           // print_r($incident);
            $queryResult               = RNCPHP\ROQL::query("Select * from Goodwill.DM_Delegation where From_DM =".$DM)->next();
            $DMDelegation                = $queryResult->next();
            if ($DMDelegation) 
            {
                if ($DMDelegation['Status'] == 1 || $DMDelegation['Status'] == 'Yes') 
                {
                    if ($DMDelegation['Valid_Till'] > date('Y-m-d') || $DMDelegation['Valid_Till'] == date('Y-m-d')) 
                    {
                        $ChangeDM = RNCPHP\Account::fetch($DMDelegation['To_DM']);
                        $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                        $incident->AssignedTo->Account                  = $ChangeDM;
                        $incident->CustomFields->Goodwill->AssignedBy   = $ChangeDM;
                    }else
                    {
                        $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                        $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                        $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                    }

                }else
                {
                    $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                    $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                    $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
                }

            }
            else
            {
                $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($DM);
                $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($DM);
            }

            $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                            = (int) DMQueue;
        }
        return $incident;
    }
    /*DM delegation end*/
    function ShiftDepartment($postData) 
    {
        $contactID          = (int) $postData['contactID'];
        $DM                 = (int) $postData['DM'];
        $IncidentId         = (int) $postData['IncidentId'];
        $incident           = RNCPHP\Incident::fetch($IncidentId);
       // echo "DM is:".$DM;
        if ($incident->CustomFields->Goodwill->ShiftingFrom == 'From IT To Facilities')  
        {
            //echo "From IT To Facilities";
            $ServiceDepartment  = (int) 1; // Facilities
            $incident->CustomFields->Goodwill->ServiceDepartment = RNCPHP\Goodwill\ServiceDepartment::fetch($ServiceDepartment);
            $incident = $this->DM_Delegation($DM,$IncidentId); 
           // print_r($incident);
        }else
        {
            //echo "From Facility to IT";
            $ServiceDepartment           = (int) 2; // IT 
            $incident->CustomFields->Goodwill->ServiceDepartment = RNCPHP\Goodwill\ServiceDepartment::fetch($ServiceDepartment);
            $incident->Queue                                    = new RNCPHP\NamedIDLabel(); 
            $incident->Queue->ID                                = (int) Queue_IT_HELP_NewWorkOrders;
            $queryResult    = RNCPHP\ROQL::queryObject("SELECT Account FROM Account WHERE Account.Profile.ID= 9 AND Account.CustomFields.Goodwill.ProfileMainAccount = 1 LIMIT 1")->next(); 
            $IT_Account                = $queryResult->next();

            if($IT_Account)
            {
                $incident->AssignedTo                               = new RNCPHP\GroupAccount();
                $incident->AssignedTo->Account                      = $IT_Account;
                $incident->CustomFields->Goodwill->CurrentlyAssign  = $IT_Account;
            }
        
        } 
        if($incident)
        {
            $incident->PrimaryContact           = RNCPHP\Contact::fetch( $contactID );   
            $incident->save(RNCPHP\RNObject::SuppressAll);  
            //echo "string"; 
        }
        return $incident;
    }

    
    
}