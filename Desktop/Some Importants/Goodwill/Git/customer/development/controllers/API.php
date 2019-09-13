<?php

namespace Custom\Controllers;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Url,    
RightNow\Utils\Framework;

define("PassPhrase" , 'Goodwill1');

class API extends \RightNow\Controllers\Base
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('goodwill');
    }

    function Auth($GivenPassPhrase)
    {
        if($GivenPassPhrase == PassPhrase)
        {
           return true;
           
        }else
        {
            return false;
        }
    }

    function headerMsg($status,$data,$msg)
    {
        header('Content-Type: application/json');
        print json_encode(array('Status'=>$status,'Data'=>$data,'Message' => $msg));
        exit;
    }

    function GetDepartment()
    { 
        $GivenPassPhrase    = (string) $_POST['passphrase'];
        $DepartmentID       = (int) $_POST['DepartmentID'];

        $Auth = $this->Auth($GivenPassPhrase);
        $result=array();
        if ($Auth == true) 
        {
            if ($DepartmentID > 0) 
            {
                $sql  = "SELECT ID, Title, Goodwill.GWDepartments.Type.LookupName AS 'DepartmentType' FROM Goodwill.GWDepartments WHERE Goodwill.GWDepartments.External_ID = ".$DepartmentID;
                $Query      = RNCPHP\ROQL::query($sql)->next();
                $result = $Query->next();
                if ($result > 0 ) 
                { 
                    $this->headerMsg(true,$result,'Record Found');   
                }else
                {
                    $this->headerMsg(false,$result,'No Record Found'); 
                }
            }else
            {
                 $this->headerMsg(false,$result,'No Record Found');
            }
        }else
        { 
            $this->headerMsg(false,$result,'Invalid attempt'); 
        }

    }

    function GetLocation($LocationID=null)
    {
        if ($_POST['passphrase']) 
        {
            $GivenPassPhrase   = (string) $_POST['passphrase'];
        }
        if ($_POST['LocationID']) 
        {
            $LocationID        = (int) $_POST['LocationID'];
        }
    	
    	$Auth = $this->Auth($GivenPassPhrase);
        $result=array();
        if ($Auth == true) 
        {
            if ($LocationID > 0)  
            {
                $sql  = "SELECT ID,Title,Phone,ZipCode FROM Goodwill.Location WHERE Goodwill.Location.LocationID_Dayforce =".$LocationID;
                $Query      = RNCPHP\ROQL::query($sql)->next();
                $result = $Query->next();
                if ($result > 0 ) 
                {
                    $this->headerMsg(true,$result,'Record Found');   
                }else
                {
                    $this->headerMsg(false,$result,'No Record Found'); 
                }
            }else
            {
                 $this->headerMsg(false,$result,'No Record Found');
            }
        }else
        { 
            $this->headerMsg(false,$result,'Invalid attempt');  
        }
    }

    function getID($queryString)
    {
        $sql    = $queryString;
        $Query  = RNCPHP\ROQL::query($sql)->next();
        $result = $Query->next();
        //print_r($result['ID']);
        return $result['ID'];
    }

    function CreateDepartment()
    {
        $GivenPassPhrase   = (string) $_POST['passphrase'];
        $Title      = (string) $_POST['Title'];
        $Type       = (string) trim($_POST['DepartmentType']);
        $ExternalID = (int) $_POST['DepartmentID'];  

        $Auth = $this->Auth($GivenPassPhrase);
        if ($Auth == true) 
        {
            $NewDepartment = new RNCPHP\Goodwill\GWDepartments();

            if (!empty($Title)) 
            {
                $NewDepartment->Title   = $Title; 
            
                if ($Type) 
                {
                    if ($Type == "Retail") 
                    {
                        //$NewDepartment->Type    = new RNCPHP\NamedIDOptList();
                        $NewDepartment->Type    = 1;
                    } 
                    if ($Type == "Non-Retail") 
                    {
                        //$NewDepartment->Type    = new RNCPHP\NamedIDOptList(); 
                        $NewDepartment->Type    = 2;
                    }                
                } 
                if ($ExternalID > 0 ) 
                {
                    $NewDepartment->External_ID = $ExternalID;
                }
                
                $NewDepartment->save();
                //print_r($NewDepartment->ID);
                
                if ($NewDepartment->ID > 0) 
                {
                    $result['ID']       = $NewDepartment->ID;
                    $result['Title']    = $NewDepartment->Title;
                    $result['Type']     = $NewDepartment->Type;
                    $this->headerMsg(true,$result,'New Department Created Successfully.'); 
                }else
                {
                    $this->headerMsg(false,null,'Department Not Created. Error!'); 
                }
            }else
            {
                $this->headerMsg(false,null,'Department Title is required. Error!'); 
            }
        }else
        {
             $this->headerMsg(false,null,'Invalid attempt'); 
        }
        
    }

    function CreateLocation()
    {
        $GivenPassPhrase        = (string)$_POST['passphrase'];
        $LocationID_Dayforce    = (int)$_POST['LocationID'];
        $Street                 = (string) $_POST['Street'];
        $ZipCode                = $_POST['ZipCode'];
        $Phone                  = $_POST['Phone'];
        $Title                  = (string)$_POST['Title'];

        $Auth = $this->Auth($GivenPassPhrase);
        if ($Auth == true) 
        {
            if ($LocationID_Dayforce || $Street || $ZipCode || $Phone || $Title) 
            {
                $NewLocation = new RNCPHP\Goodwill\Location();
                if ($LocationID_Dayforce > 0) 
                {
                    $NewLocation->LocationID_Dayforce   = $LocationID_Dayforce;
                }
                if ($Phone > 0 ) 
                {
                    $NewLocation->Phone                 = $Phone;
                }
                if ($Street) 
                {
                    $NewLocation->Street                = $Street;
                }
                if ($ZipCode > 0 ) 
                {
                    $NewLocation->ZipCode               = $ZipCode;
                }
                if ($Title) 
                {
                    $NewLocation->Title                 = $Title;
                }
                
                $NewLocation->save();
                
                if ($NewLocation->ID > 0) 
                {
                    $result['ID']       = $NewLocation->ID;
                    $result['Title']    = $NewLocation->Title;
                    $result['ExternalID'] = $NewLocation->LocationID_Dayforce;
                    $this->headerMsg(true,$result,'New Location Created Successfully.');
                }else
                {
                    $this->headerMsg(false,null,'Location Not Created. Error!');  
                }
            }else{
                $this->headerMsg(false,null,'Please send valid data.');
            }    
        }else
        {
             $this->headerMsg(false,null,'Invalid attempt'); 
        }
        

    }

        function email_validation($str) 
        { 
            return (!preg_match( 
        "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str)) 
                ? FALSE : TRUE; 
        }                                   
    function CreateEmploye()
    {
        $GivenPassPhrase   = (string) $_POST['passphrase'];
        $LocationID        = (int) $_POST['LocationID'];
        $DepartmentID      = (int) $_POST['DepartmentID'];
        $EmplID            = (int) $_POST['EmplID'];
        $FirstName         = (string) $_POST['FirstName'];
        $LastName          = (string) $_POST['LastName'];
        $Email             = $_POST['Email'];
        $RepSupervisor     = (int) $_POST['RepSupervisorID'];  

        $Auth = $this->Auth($GivenPassPhrase);
        if ($Auth == true) 
        {
            $contact = new RNCPHP\Contact();
            //add email addresses
            if ($Email) 
            {   // Function call 
                $valid = $this->email_validation($Email);
                if($valid) 
                { 
                    $emailQuery    = "SELECT ID FROM Contact WHERE Contact.Emails.Address = '".$Email."'";
                    $isEmailExist  = $this->getID($emailQuery);
                    if ($isEmailExist > 0) 
                    {   
                        $this->headerMsg(false,null,'Employe exist with same email, Error!');
                    }else
                    {
                        $contact->Emails = new RNCPHP\EmailArray(); 
                        $contact->Emails[0] = new RNCPHP\Email();
                        $contact->Emails[0]->AddressType=new RNCPHP\NamedIDOptList();
                        $contact->Emails[0]->AddressType->LookupName = "Email - Primary";
                        $contact->Emails[0]->Address = $Email;
                    }
                     
                } 
                else 
                { 
                    $this->headerMsg(false,null,'Invalid Email, Error!'); 
                } 
            }else
            {
                $this->headerMsg(false,null,'Email Required, Error!'); 
            }
            
            if ($FirstName && $LastName) 
            {
                $contact->Name          = new RNCPHP\PersonName();
                $contact->Name->First   = $FirstName;
                $contact->Name->Last    = $LastName;
            }else
            {
                $this->headerMsg(false,null,'First and Last Name Required, Error!');
            }
            
            if (!empty($EmplID)) 
            {
                if ($EmplID > 0) 
                {
                    $contact->CustomFields->Goodwill->EmployeeId = $EmplID;
                }

            }else
            {
                $this->headerMsg(false,null,'Employe ID is Required, Error!');
            }
            if (!empty($LocationID))
            {
                if ($LocationID > 0) 
                {
                    $contact->CustomFields->Goodwill->LocationID_Dayforce = $LocationID;
                    $LocQuery    = "SELECT ID FROM Goodwill.Location WHERE Goodwill.Location.LocationID_Dayforce =".$LocationID;
                    $Loc        = $this->getID($LocQuery);
                    if ($Loc > 0) 
                    {
                        $Location   = RNCPHP\Goodwill\Location::fetch( $Loc );
                        $contact->CustomFields->Goodwill->Location          = $Location;
                    }else
                    {
                        $this->headerMsg(false,null,'Location not Exist, Error!');
                    }
                    
                }
            }else
            {
                $this->headerMsg(false,null,'Location ID is Required, Error!');
            }
            
            if (!empty($DepartmentID))
            {
                if ($DepartmentID > 0) 
                {
                    $contact->CustomFields->Goodwill->Department_DayForce = $DepartmentID;
                    $DepartQuery = "SELECT ID FROM Goodwill.GWDepartments WHERE Goodwill.GWDepartments.External_ID = ".$DepartmentID;
                    $Depart     = $this->getID($DepartQuery);
                    if ($Depart > 0 ) 
                    {
                        $Department = RNCPHP\Goodwill\GWDepartments::fetch( $Depart );
                        $contact->CustomFields->Goodwill->GWDepartments     = $Department;
                    }else
                    {
                        $this->headerMsg(false,null,'Department not Exist, Error!');
                    }
                    
                }
            }else
            {
                $this->headerMsg(false,null,'Department ID is Required, Error!');
            }
            
            if (!empty($RepSupervisor))
            {
                if ($RepSupervisor > 0) 
                {
                    $contact->CustomFields->Goodwill->ReportingSupervisor_Dayforce = $RepSupervisor;
                    $SupervisorQuery = "SELECT ID FROM Contact WHERE Contact.CustomFields.Goodwill.EmployeeId = ".$RepSupervisor;
                    $RSupervisor= $this->getID($SupervisorQuery);
                    if ($RSupervisor > 0) 
                    {
                        $Supervisor = RNCPHP\Contact::fetch( $RSupervisor );
                        $SupervisorEmail = $Supervisor->Emails[0]->Address  ;
                        $contact->CustomFields->Goodwill->ReportingSupervisor = $Supervisor;
                    }else
                    {
                        $this->headerMsg(false,null,'Reporting Supervisor not exist, Error!');
                    }
                    
                } 
            }else
            {
                $this->headerMsg(false,null,'Reporting Supervisor ID is Required, Error!');
            }
           
            $contact->save();
            
            if ($contact->ID > 0 ) 
            {
                $NewNEF = new RNCPHP\Goodwill\NEF();
                if (!empty($Supervisor)) 
                {
                    $NewNEF->PreparerName = $Supervisor;
                    $code = time();
                    $NewNEF->validationCode = $code;
                    $NewNEF->Contact = $contact; 
                } 
                $NewNEF->save();
                
                $this->emailForm($SupervisorEmail,$contact->ID,$code);
                /*$this->emailForm($SupervisorEmail,$contact->ID,$NewNEF->ID);*/
                $result['ID']           = $contact->ID;
                $result['ExternalID']   = $contact->CustomFields->Goodwill->EmployeeId;
                $result['Email']        = $contact->Emails[0]->Address;
                $this->headerMsg(true,$result,'New Employe Created!');
            }else
            {
                $this->headerMsg(false,null,'No record created. Error!'); 
            }
            //print_r($contact->ID);  
        }else
        {
            $this->headerMsg(false,null,'Invalid attempt'); 
        }
        
    }


    function emailForm($requestoremail,$ContactID,$code)
    {
        /*<a class="btn-success" href="https://goodwillsocal.custhelp.com/app/nef/generalinformation?NEFID=$NewNEFID&EmplID=$ContactID"><strong>Click</strong></a>*/
        try
        {
            $NewNEFID = base64_encode($NEFID);
            $email_1    = $requestoremail;
            $ContactID  = $ContactID;
            $text_body = "A Employe Created.";
            $html_body = 

<<<EMAIL
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <div>
        <p>New Employe Created in Oracle Service Cloud with ID# $ContactID</p><br/>
        
        <p>Passcode: $code</p> 
        <p>Please do not share this passcode to any one. Use this <a class="btn-success" href="https://goodwillsocal.custhelp.com/app/nef/validation"><strong>Link</strong></a> for work order creation.</p><br/>Thanks.
            
 
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
            $mm->Subject = "New Employe Created in Oracle Service Cloud";
         
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

    function UpdateEmploye()
    { 
        $GivenPassPhrase   = (string) $_POST['passphrase'];
        $status     = (string) $_POST['EmployeStatus'];
        $contactID  = (int) $_POST['EmployeID'];

        $Auth = $this->Auth($GivenPassPhrase);
        if ($Auth == true) 
        {
            if ($contactID > 0) 
            {
                $sql = 'SELECT ID FROM Contact WHERE Contact.CustomFields.Goodwill.EmployeeId = '.$contactID;
                $ContID        = $this->getID($sql);
                $contact       = RNCPHP\Contact::fetch( $ContID );
                if ($status == 'Active') 
                {
                    //$contact->CustomFields->Goodwill->Status =  new RNCPHP\NamedIDOptList();
                    $contact->CustomFields->Goodwill->Status = 1;
                }else
                {
                    //$contact->CustomFields->Goodwill->Status = new RNCPHP\NamedIDOptList();
                    $contact->CustomFields->Goodwill->Status = 2; 
                }  

                
                if ($contact->save())  
                {
                    $result['ExternalID'] = $contact->CustomFields->Goodwill->EmployeeId;
                    $result['Status'] = $contact->CustomFields->Goodwill->Status->LookupName;
                    $result['email'] = $contact->Emails[0]->Address;

                    $this->headerMsg(true,$result,'Employe Updated!'); 
                }else
                {
                    $this->headerMsg(false,null,'Not Updated. Error!'); 
                }
            }
        }else
        {
            $this->headerMsg(false,null,'Invalid attempt'); 
        }
        
    }

}

