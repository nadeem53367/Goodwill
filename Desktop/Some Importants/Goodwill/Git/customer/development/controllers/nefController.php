<?php

namespace Custom\Controllers;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Url,    
RightNow\Utils\Framework;

class nefController extends \RightNow\Controllers\Base
{
    //This is the constructor for the custom controller. Do not modify anything within
    //this function.
    function __construct()
    {
        parent::__construct();
        $this->load->helper('goodwill');
        $this->load->model('custom/nefModel');
    }

    function nextForm($nextPage,$iNEFID,$EmplID=null)
    {
        if (empty($EmplID)) 
        {
            $NEFID = $iNEFID;
        }else
        {
            $NEFID = $iNEFID.'&EmplID='.$EmplID;  
        }
        $this->load->helper('url');
        $sShortUrl =    getShortEufBaseUrl().'/app/nef/'.$nextPage.'?NEFID='.$NEFID;
        header('Location: '.$sShortUrl); 
        die;
        
    }

    function nef_Validation()
    {
        $code = (int) $_POST['code'];
        $codeType =  gettype($code);
        
        if ($codeType == 'integer') 
        {
            $result = $this->nefModel->validateNEF($code); 
            if ($result > 0 ) 
            {
                $NEF = RNCPHP\Goodwill\NEF::fetch( $result );
                $EmplID = $NEF->Contact->ID;
                /*echo "Emp ID :".$EmplID;
                print_r($NEF);
                exit();*/
                $NEFID = base64_encode($result);
                $this->nextForm('generalinformation',$NEFID,$EmplID);
                //echo "string";
            }else
            {
                $this->load->helper('url');
                $sShortUrl =    getShortEufBaseUrl().'/app/error404';
                header('Location: '.$sShortUrl);
            }

        }else
        {
            $this->load->helper('url');
            $sShortUrl =    getShortEufBaseUrl().'/app/error404';
            header('Location: '.$sShortUrl);
        }
    }

    function employeCreation()
    {
    	//echo "YES here!";
    	//echo "<pre>"; print_r($_POST); 
    	$Ticket = $this->nefModel->createNEFObject($_POST); 

    }

    function generalinformation()
    {
        $EmplID = null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'generalinformation';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                $this->nextForm('email_address',$NEFID,$EmplID);   
            }
            
        }
        
    }

    function email_address()
    {
        $EmplID =null;
        $postData = $_POST;
        //print_r($postData);exit();
        $employeTypeIs = $postData['employeTypeIs'];
        $postData['CallForFunction'] = 'email_address';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                if ($employeTypeIs == 1 || $employeTypeIs == 2 || $employeTypeIs == 3  ) 
                {  
                    $this->nextForm('nef_view',$NEFID,$EmplID);
                }else
                {
                    $this->nextForm('mailing_and_resource',$NEFID,$EmplID);
                }
                
            }
            
        }
    }

    function mailing_and_resource()
    {
        $EmplID = null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'mailing_and_resource';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                $this->nextForm('workstation',$NEFID,$EmplID);
            }
        }
    } 

    function workstation()
    {
        $EmplID = null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'workstation';
        $Object = $this->nefModel->createNEFObject($postData);
        //echo "object returns";
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        { //echo "object returns";
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                $this->nextForm('softwares',$NEFID,$EmplID);
            }
        }
    }

    function NEFSoftwares()
    {
        $EmplID = null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'NEFSoftwares';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                $this->nextForm('cabling_and_printing',$NEFID,$EmplID);
            }
        }
    } 
    function printing_and_cabling()
    {
        $EmplID = null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'printing_and_cabling';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            if ($callFrom == 'view') 
            {
                $this->nextForm('nef_view',$NEFID,$EmplID);
            }else
            {
                $this->nextForm('communication_devices',$NEFID,$EmplID);
            }
        }
    }
    function communication_devices()
    {
        $EmplID =null;
        $postData = $_POST;
        $postData['CallForFunction'] = 'communication_devices';
        $Object = $this->nefModel->createNEFObject($postData);
        $NEFID  = base64_encode( $_POST['NEFID'] );
        $callFrom  =  $_POST['callFrom'] ;
        if ($Object == true)
        {
            $this->nextForm('nef_view',$NEFID,$EmplID);   
        }
    }

    function createWO($Subject,$Thread=null,$employee,$Category,$NEFID)
    {

        $employeeObject   = RNCPHP\Contact::fetch( $employee );
        $LocID    = $employeeObject->CustomFields->Goodwill->Location->ID;
        $DepartID = $employeeObject->CustomFields->Goodwill->GWDepartments->ID;
        $Incident = new RNCPHP\Incident();

        if (!empty($Subject)) 
        {
            $Incident->Subject  = $Subject; 
        }
        if ($Category > 0 ) 
        {
            $Incident->Category = RNCPHP\ServiceCategory::fetch($Category);
        }
        
        if ($NEFID > 0) 
        {
            $Incident->CustomFields->Goodwill->NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
        
        }
       
        if (!empty($Thread)) 
        {
            $Incident->Threads      = new RNCPHP\ThreadArray();
            $Incident->Threads[0]   = new RNCPHP\Thread();
            $Incident->Threads[0]->EntryType = new RNCPHP\NamedIDOptList();
            $Incident->Threads[0]->EntryType->ID = 3; 
            $Incident->Threads[0]->Text = $Thread;
        }

        $queryResult               = RNCPHP\ROQL::queryObject("SELECT Account FROM Account WHERE Account.Profile.ID=9 AND Account.CustomFields.Goodwill.ProfileMainAccount = 1 LIMIT 1")->next();
        $IT_Account                = $queryResult->next(); 
        if($IT_Account)
        {
            $Incident->AssignedTo                               = new RNCPHP\GroupAccount();
            $Incident->AssignedTo->Account                      = $IT_Account;
            $Incident->CustomFields->Goodwill->CurrentlyAssign  = $IT_Account;
        }
        if ($LocID > 0 || $DepartID > 0) 
        {
            $Incident->CustomFields->Goodwill->Location = RNCPHP\Goodwill\Location::fetch($LocID);
            $Incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($DepartID);
        }
       
        $Incident->PrimaryContact           =   $employeeObject;   
        $Incident->save();
    }
    function PreviewMood()
    {
        /*echo "IN PreviewMood";
        echo "<pre>"; print_r($_POST);
        exit(); */
        $employee     = $_POST['EmplID'];
        
        $NEFID      = base64_decode($_POST['NEFID']);

        $isRequireEmail      = $_POST['isRequireEmail'];
        $EmpTypeIs           = $_POST['EmpTypeIs'];
        $isNeedVPN           = $_POST['isNeedVPN'];
        $isTempEmpl          = $_POST['isTempEmpl'];
        $isRetailStoreEmpl   = $_POST['isRetailStoreEmpl'];
        $PreviousGWemail     = $_POST['havePreviousGWemail'];

        $MailingList            = $_POST['MailingList'];
        $ResourceAccessGroups   = $_POST['ResourceAccessGroups'];
        
        $WorkstationID    = $_POST['WorkstationID'];
        $Workstation      = $_POST['Workstation'];
        $RequireDeskPhone = $_POST['RequireDeskPhone'];
        $RequireMobile    = $_POST['RequireMobile'];

        $softwareWO = true;
        $cabling_and_printing = true; 

        if ($isRequireEmail == 'Yes' || $isNeedVPN == 'Yes' || $isTempEmpl == 'Yes' || $PreviousGWemail == 'Yes'  || $isRetailStoreEmpl == 'Yes') 
        {
            $Subject = 'Email required for employe.';
            $Thread ="NEF - Require email for new employee </br>".$MailingList.'</br>'.$ResourceAccessGroups;
            $Category = 15;
            $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);

        }
        if (strpos($EmpTypeIs,'Retail') !== false) {}else
        {
            if ($WorkstationID == 1 || $WorkstationID == 2 || $WorkstationID == 3  ) 
            {
                $Subject = 'Workstation for new employee';
                $Thread ="NEF - Employee to receive a Desktop Workstation";
                $Category = 2;
                $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);
            }

             if ($softwareWO == true) 
             {
                $Subject = 'Software required for new employee';
                $Thread ="NEF - Employee need following softwares.";
                $Category = 8;
                $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);
             }

             if ($cabling_and_printing == true) 
             {
                $Subject = 'Required cables/ (Printer/Scanner connections).';
                $Thread  ="NEF - Employee need following hardwares.";
                $Category =77;
                $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);
             }

             if ($RequireDeskPhone == 'Yes') 
             {
                $Subject = 'Required desk phone.';
                $Thread  ="NEF - Employee need desk phone.";
                $Category =78;
                $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);
             }
             if ($RequireMobile == 'Yes') 
             {
                $Subject = 'Required Mobile.';
                $Thread  ="NEF - Employee need following mobile.";
                $Category =86;
                $this->createWO($Subject,$Thread,$employee,$Category,$NEFID);
             }  

         }
         echo "<script>alert('You have successfully created work orders against new employe!');</script>";
         echo "<script>window.close();</script>"; 
    } 

}