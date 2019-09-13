<?php

namespace Custom\Controllers;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Url,    
RightNow\Utils\Framework;

define("DMQueue",                  2);
define("Queue_IT_Tech",            10);
define("Queue_IT_VP",              12);
define("Queue_IT_Programer",       16);
define("Queue_IT_Procurement",     9);
define("Queue_IT_Ops_Mngr",        15);
define("Queue_IT_Network_Admin",   14);
define("Queue_IT_Help_Team",       8);
define("Queue_IT_Admin_Asst",      18);
define("Queue_Fac_Procurement",    4);
//define("Queue_Fac_Opr_Mngr",       10);
define("Queue_Fac_DM",             3);
//define("Queue_Fac_Director",       10);
//define("Queue_Fac_CSR",            10);

define("StatusUnreOpen",    111); 
define("StatusUnresolved",  1);
define("StatusComplete",  2);
define("readyForDeploy",  127);
define("AssetDeployed",  126);
define("StatusReassigned",  148);

class CustomAPI extends \RightNow\Controllers\Base
{
    //This is the constructor for the custom controller. Do not modify anything within
    //this function.
    function __construct()
    {
        parent::__construct();
        $this->load->helper('goodwill');
    }
    
    function getAssetIncidents()
    {
        $AssetID 	=  $_GET['AssetID'];
        $postData   = array();
        $sql        = "SELECT Goodwill.Assets.Incident.ID FROM Goodwill.Assets WHERE Goodwill.Assets.Asset.ID = ".$AssetID;
        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        if ($Query > 0 ) 
        {
        	while($result = $Query->next())
	        {
	          // $Assets[] 		= array('ID' => $result['ID']);
	           $incidentID 		= (int) $result['ID'];
	           $Incident 		= RNCPHP\Incident::fetch( $incidentID );
	           $ID  			= $Incident->ID;
	           $Subject 		= $Incident->Subject;
	           $Requestor  		= $Incident->PrimaryContact->LookupName; 
	           $File	  		= $Incident->FileAttachments; 


	           $pData['ID'] 		= $ID ;   
	           $pData['Subject'] 	= $Subject ;   
	           $pData['Requestor']  = $Requestor ;
               $postData[] = $pData;
	        }
           // echo "<pre>"; print_r($postData); echo "</pre>";
	        print json_encode($postData);
        	//return $postData;
        }else{
        	return false;
        }
        
    }   

    function getIncident()
    {
        $IncidentID = $_GET['IncidentID']; 
        if ($IncidentID > 0) 
        {   
            $Incident = RNCPHP\Incident::fetch($IncidentID);

            $subject        = $Incident->Subject;
            $Location       = $Incident->CustomFields->Goodwill->Location->LookupName;
            $Department     = $Incident->CustomFields->Goodwill->GWDepartments->LookupName;
            $Thread         = $Incident->Threads[0]->Text;
            $Category       = $Incident->Category->LookupName;
            $CreatedTime    = $Incident->CreatedTime;
            $Requestor      = $Incident->PrimaryContact->LookupName;

            $result['subject']      = $subject;
            $result['Location']     = $Location;
            $result['Department']   = $Department;
            $result['Thread']       = strip_tags($Thread);
            $result['Category']     = $Category;
            $result['CreatedTime']  = $CreatedTime;
            $result['Requestor']    = $Requestor;
            

            $FileObject = $Incident->FileAttachments;
            $TotalFiles = sizeof($FileObject);
            //echo "<pre>"; print_r($Thread); exit;
            if ($TotalFiles > 0 ) 
            {
                for ($i=0; $i < $TotalFiles ; $i++) 
                { 
                    $FileID     = $FileObject[$i]->ID;
                    $FileName   = $FileObject[$i]->FileName;
                    $FileType   = $FileObject[$i]->ContentType; 
                    //echo "FILE ID:".$FileName; exit();
                     if (!function_exists('curl_init'))
                    {
                        load_curl(); 
                    }
                       
                    $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://goodwillsocal.custhelp.com/services/rest/connect/latest/incidents/".$IncidentID."/fileAttachments/".$FileID."/data",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_SSL_VERIFYPEER => false,
                  CURLOPT_POSTFIELDS => "",
                  CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Authorization: Basic bmFkZWVtLmFsaTpFcGhsdXgxMjM=",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Type: application/json",
                    "Cookie: cp_session=fU711MgquVFL1C%7EuXUoNbePfNVVSqFJDIVlhTaGsJTG%7EW1nnsXwSxTblDHDYQj9tiaxKn7h2K4IoyO2XYVPjahRvzDwADHwHfKer9RQfOEp4qp06q%7Ec73AxDNJAQ4wuLcw77n8YcNAKIprj2IhxBaoKzkCFhpXAUPgsT82SOl6fj23egVdaaMR1icVM%7E3JC9i1mt08%7EwPy%7EIeVhVlbPTMj_H2v6ibI6JhNjdGVuX0w7K5EiT7l8rS9%7Eu9hVABK4Q5My5nVNnYZXPp_hTAbyp%7EN%7EYxjfxzMlT0X8ekxBpAJpiA_SU_IupxrI6W0K0CLSkiaMOqEZDCpFW8QxuR3J3V2VUxMHr96NwM2o7NLNyqSo1yqOiJVa_OCLzFmVf6F_IJlMK%7EoiJEA26%7EYwk5SIRUaelNX8eJQ4B6uWout06r6xy1kuXdyuXyHIBLUTLgcUa3yb4eUZK1kwuVmrg9Mqs2vtVzAUtsefR87ujejaXeWlzffuRnA61OOxA%21%21",
                    "Host: goodwillsocal.custhelp.com",
                    "OSvC-CREST-Application-Context: as",
                    "Postman-Token: f2959c5c-cc0a-451a-8a2e-23b0d609c3d6,bf853fa9-4654-4b72-a88d-e3972fc72c22",
                    "User-Agent: PostmanRuntime/7.16.3",
                    "cache-control: no-cache"
                    ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                      echo "cURL Error #:" . $err;
                    }else {
                      //echo $response;
                      $response = json_decode($response);
                      $fileArray = array('ContentType'=>$FileType,'FileName'=>$FileName,'Data'=>$response->data);
                      $result['File'][] = $fileArray;//$response;
                      //exit();
                    }
                }
            }
             
            print(json_encode($result)); 
        }else
        {
            $msg = 'Required Incident ID!';
            print json_encode($msg);
        }
        //
    }

    function getAsset()
    {
        //$AssetSerial = $_GET['SerialNumber'];
        //echo "string";
        if($_GET['SerialNumber'])
        {
            $SerialNumber = $_GET['SerialNumber']; 
            $append = " Asset.SerialNumber LIKE ".$SerialNumber;
            
        }
        if($_GET['ID'])
        {
            $AssetID      = $_GET['ID'];
            $append = " Asset.ID = ".$AssetID;  
        }
            
        $sql = "SELECT ID, Name ,Asset.StatusWithType.Status.LookupName AS 'Status', Asset.CustomFields.Goodwill.BarCode, Asset.SerialNumber, Asset.CustomFields.Goodwill.ServiceDepartment.LookupName AS 'Department',Asset.InstalledDate, Asset.CustomFields.Goodwill.AssignTo.LookupName AS 'Created By' ,Asset.CustomFields.Goodwill.Location.LookupName AS 'Location' from Asset where ".$append;
        $Query      = RNCPHP\ROQL::query($sql)->next();
        $result     = $Query->next();
        if ($result['ID'] > 0) 
        {
            $AssetID    = $result['ID'];

            $sql = "SELECT ID FROM Goodwill.AssetsAttachment WHERE Goodwill.AssetsAttachment.Assets.ID =".$AssetID;
            $Query      = RNCPHP\ROQL::query($sql)->next();
            
            while($AttachmentObjectID = $Query->next())
            {
                $AttachmentIds = $AttachmentObjectID['ID'];
                $AttachmentObject = RNCPHP\Goodwill\AssetsAttachment::fetch($AttachmentIds);
                $FileAtaachment = $AttachmentObject->FileAttachments;
                $FileID = $FileAtaachment[0]->ID;
                $FileType = $FileAtaachment[0]->ContentType;
                $FileName = $FileAtaachment[0]->FileName;
               // echo "<pre>"; print_r($FileAtaachment[0]->FileName); exit();
                //echo "Object ID : ".$AttachmentIds." And File ID : ".$FileID; 
                 if (!function_exists('curl_init'))
                {
                    load_curl(); 
                }
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://goodwillsocal.custhelp.com/services/rest/connect/latest/Goodwill.AssetsAttachment/".$AttachmentIds."/FileAttachments/".$FileID."/data",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_SSL_VERIFYPEER => false,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_POSTFIELDS => "",
                  CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Authorization: Basic bmFkZWVtLmFsaTpFcGhsdXgxMjM=",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Type: application/json",
                    "Cookie: cp_session=fUAOGgKWwArW4FonxqIU8jhFhm28EJOUk522zk9KVNGnZY80WxC8mSFMQSk3UQS4ALciWV0N2uM000dJiUOgJqkh34dzsgJeZZq1_9F3FRwgEHZzNn8XZb8YD7EsyRoYO5SDjFoXUeF7XtYll4w8R_3yQDmjZdBw8u34tDAYGn5wlwVNOnKqxWokcJ4BYsdgwOz9SAV6tqgDhDD_5X1OxE0a7T42LBLzwlAHY5vrWshBhZwFvrflsXVc_j4yeXnXmfPokgsB0ceKFD0yqQzWaGKR4U4jis931Ef7fZ7ag8hVrqFmUXpg757AmqusY%7E4_4BFx02_5LaMjaKspeUL2BPxmKWRzk%7EW38k3EBwzkCtPwWTimeVCztUS8%7EnHD%7EeDrecor1sZdcCPqIS3jc8lc26CfodenweYHyHlphOnnMWBt4wDhXBoByHelV4IhxRd_jZGlGpkREnkfS7hu_LyLnOn7gPAWtQTd98szDkB%7EfqS0zYijlf%7EKAPo6L92okpaeB5kiEud_xT8pj6UKxrY3Al_lRSG_0BkoQvZOb2X_Pyx75ybho6Xvn2hfAfSGcJh8wm3e9v077jd8zpMXuslfcYV0qjQJF2JGUVIhH0rKVTQ8rMs1LaVs_WRw1yjXqQObHL4PtW2byGIw9TvtszDwwbHGU%7EDwrOoYfvoHyu8EePjQqDRmIWBxiEt3GDhilBWnWYg1VvGsiUdQs9niWWsDEO%7Ek9B41ko_CwNSw6yJMdzTqZDnYyHe7iqxWRmlkQhkhiy1DsfmnLl7wvlc9evTOzRwlZtnPRLjDd76J9naKsbST_58rqwUfNPHAlsgpoe4W6Ds_9%7Ee8Ndh2s%21",
                    "Host: goodwillsocal.custhelp.com",
                    "OSvC-CREST-Application-Context: as",
                    "Postman-Token: 28d95390-92d2-4ac4-b9a9-1b273318afa0,5db8d7dd-9b30-4ac4-8bc0-d5f54ac979a5",
                    "User-Agent: PostmanRuntime/7.15.2",
                    "cache-control: no-cache"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                  //echo $response;
                  $response = json_decode($response);
                  $fileArray = array('ContentType'=>$FileType,'FileName'=>$FileName,'Data'=>$response->data);
                  $result['File'][] = $fileArray;//$response;
                }
                //exit;
            }

            print(json_encode($result));
        }else
        {
            $msg = 'No Asset Found!';
            print($msg);
        }
        
        //echo "<pre>"; print_r($result); exit();

    }
   
    function fileAPI()
    {
        $File = tmpfile();
       // echo $File; exit()
        $base64_string = 'iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAARSURBVBhXY3growJHRHFkVAD18xLRxI3V8QAAAABJRU5ErkJggg==';
        fwrite( $File, base64_decode( $base64_string ) );
        
        $path = stream_get_meta_data($File)['uri'];

        /*$aFile = [
        'name' => 'image.png',
        'type' => 'image/png',
        'tmp_name' => $path,
        'error' => 0,
        'size' => filesize($path),  
        ];*/
       //print_r(filesize($path)); 
        //exit();
        $signOff    = new RNCPHP\Goodwill\SignOff(); 
           // $aFile      = $File['myfile'];  
           /* if($aFile['name'])
                {*/
        $signOff->FileAttachments       = new RNCPHP\FileAttachmentArray();
        $fattach                        = new RNCPHP\FileAttachment();  
        $fattach->ContentType           = 'image/png';
        $fp                             = $fattach->makeFile();
        $mystring                       = file_get_contents($path, "wb");//$aFile['tmp_name'];//file_get_contents($aFile['tmp_name'], "wb");
        fwrite( $fp, $mystring); 
        //fclose( $fp );
        $fattach->FileName              = 'signoff.png';
        $fattach->Name                  = 'signoff.png';
        $signOff->FileAttachments[]    = $fattach; 
                //}
        $incident = RNCPHP\Incident::fetch(863);
        $signOff->Incident = $incident; 
        $signOff->save(); 
    }
   

    function CloseWorkOrder()
    { 
        $IncidentID     = (int) $_POST['ID'];
        $Technician     = (int) $_POST['Technician'];
        $contactID      = (int) $_POST['contactID'];
        $DeployTo       = (string) $_POST['DeployTo'];
        $Threads        = (string) $_POST['Notes'];
        $Disposition    = (int) $_POST['Disposition']; 
        $base64_string  = (string) $_POST['myfile']; 
        
        $incident = RNCPHP\Incident::fetch($IncidentID);

        $File = tmpfile();
        fwrite( $File, base64_decode( $base64_string ) ); 
        $path = stream_get_meta_data($File)['uri'];

        $signOff    = new RNCPHP\Goodwill\SignOff(); 
        
        $signOff->FileAttachments       = new RNCPHP\FileAttachmentArray();
        $fattach                        = new RNCPHP\FileAttachment();  
        $fattach->ContentType           = 'image/png';
        $fp                             = $fattach->makeFile();
        $mystring                       = file_get_contents($path, "wb");
        fwrite( $fp, $mystring); 
        //fclose( $fp );
        $fattach->FileName              = 'signoff.png';
        $fattach->Name                  = 'signoff.png';
        $signOff->FileAttachments[]    = $fattach; 
        
        $signOff->Incident = $incident;  
        $signOff->save();

        if ($Disposition > 0) 
        {
            $incident->Disposition = RNCPHP\ServiceDisposition::fetch($Disposition);
        } 
        if ($Threads) 
        {
            $incident->Threads                          = new RNCPHP\ThreadArray();
            $incident->Threads[0]                       = new RNCPHP\Thread();
            $incident->Threads[0]->EntryType            = new RNCPHP\NamedIDOptList();
            $incident->Threads[0]->EntryType->ID        = (int)  3;
            $incident->Threads[0]->Text                 = $Threads; 
        }

        $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
        $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
        $incident->StatusWithType->Status->ID       = (int) StatusComplete ;

        $incident->CustomFields->Goodwill->is_OpenForTech = false;

        $Requestor  =  $incident->PrimaryContact->ID; 
        $LocationID =  $incident->CustomFields->Goodwill->Location->ID;
         $sql  = "SELECT Asset.ID AS 'AssetID' FROM Goodwill.Assets WHERE Goodwill.Assets.Incident.ID =".$IncidentID;
        $Query   = RNCPHP\ROQL::query($sql)->next(); 
        
        while($result = $Query->next())
        {
            $AssetID = $result['AssetID'];
            if ($AssetID > 0) 
            {
                $Asset = RNCPHP\Asset::fetch( (int) $result['AssetID']);
                //print_r($Asset);
                $NewHistory             = new RNCPHP\Goodwill\AssetsHistory();
                $NewHistory->Asset      = $Asset;
                if ($DeployTo == 'Location') 
                {
                    $Asset->CustomFields->Goodwill->is_deployeToLocation = true;
                    $NewHistory->Location           = RNCPHP\Goodwill\Location::fetch( $LocationID );
                    $NewHistory->AssignmentAction   = "Asset assigned to Location";
                }elseif ($DeployTo == 'Employe') 
                {
                    if ($contactID > 0 ) 
                    {
                        $AssignTo = $contactID;
                    }else
                    {
                        $AssignTo = $Requestor; 
                    }
                    $Asset->CustomFields->Goodwill->is_deployeToLocation = false;
                    $NewHistory->AssignToEmploye    = RNCPHP\Contact::fetch( $AssignTo );
                    $NewHistory->AssignmentAction   = "Asset assigned to Employe"; 
                }
                $NewHistory->AssignToTech = RNCPHP\Contact::fetch( $Technician );
                $NewHistory->AssignedOn   = time(); 
                $Asset->StatusWithType->Status->ID       = (int) AssetDeployed ; 
                $Asset->save();
                $NewHistory->save();
            }
        }
        //$incident->ClosedTime = time();
        $incident->save();
        
        if ($incident) { 
            $msg = 'success';
            print(json_encode($msg)) ;
        }else
        { $msg = 'Failed to save file';
            print(json_encode($msg)) ; 
        }

    }
 
    function getServiceCategories()
    {
        $Categories =array();
        $key = 'IT';
        for ($i=2; $i <4 ; $i++) 
        { 
            //echo $i;
            
            $subCat ='';
            $IT = array();
            
            $sql     = "SELECT ID, Name FROM ServiceCategory WHERE ServiceCategory.Parent.ID =".$i;
            $Query   = RNCPHP\ROQL::query($sql)->next(); 
            if ($Query > 0 ) 
            {
                while($result = $Query->next())
                { 
                    
                    $sql = "SELECT ID, Name FROM ServiceCategory WHERE ServiceCategory.Parent.ID = ".$result['ID'];
                    $subQuery   = RNCPHP\ROQL::query($sql)->next();
                    while ( $subCat = $subQuery->next()) 
                    {
                        $sub[] = $subCat;
                        
                    }
                    $result['SUB'] =  $sub;
                    $IT[] = $result;
                    $sub = array();
                    $result = array();

                }
               
            }
            $Categories[$key] = $IT;
            $key = 'Facilities';

        }

        //echo "<pre>"; print_r($Categories);
        print(json_encode($Categories));
        
    }

    function getDisposition()
    {
        $Disposition =array();
        $sql     = "SELECT ID , Name FROM ServiceDisposition Where ServiceDisposition.Parent.ID = 293";
        $Query   = RNCPHP\ROQL::query($sql)->next(); 
        if ($Query > 0 ) 
        {
            while($result = $Query->next())
            {
                $IT[] = $result;
            }
            
        }

        $sql  = "SELECT ID , Name FROM ServiceDisposition Where ServiceDisposition.Parent.ID = 310";
        $Query   = RNCPHP\ROQL::query($sql)->next(); 
        if ($Query > 0) 
        {
             while($result = $Query->next())
            {
                $Facilities[] = $result;
            }

        }

        $Disposition['IT'] = $IT;
        $Disposition['Facilities'] = $Facilities;
        print json_encode($Disposition);
        //echo "<pre>"; print_r($Disposition); 
    }
    function addAssetToIncident()
    {
        $incidentID = (int) $_GET['IncidentID'];
        $AssetID    = (int) $_GET['AssetID'];
        $Tech       = (int) $_GET['Tech'];
        $Asset      = RNCPHP\Asset::fetch($AssetID);
        /*if ($_GET['Name'] ||$_GET['Contact'] ||$_GET['Barcode'] || $_GET['Department']) 
        {
            $Name = $Contact = $Barcode = $Department = '';
        }*/
        

        if ($AssetID > 0) 
        {
            $NewAssetObject             = new RNCPHP\Goodwill\Assets();
            $NewAssetObject->Incident   = RNCPHP\Incident::fetch($incidentID);
            $NewAssetObject->Asset      = $Asset; 
            $NewAssetObject->save();  
            if ($NewAssetObject) 
            { 
                $NewHistory             = new RNCPHP\Goodwill\AssetsHistory();
                $NewHistory->Asset      = $Asset;
                $NewHistory->AssignToTech = RNCPHP\Contact::fetch( $Tech );
                $NewHistory->AssignmentAction   = 'Self assignment from technician ';
                $NewHistory->AssignedOn         = time(); 
                $NewHistory->save();  

                //print_r($NewAssetObject);
                $msg = 'Success';
                print(json_encode($msg));
            }else{
                $msg = 'Failed';
                print(json_encode($msg)); 
            }
        }
       /* else{
            $Asset                                                  = new RNCPHP\Asset();
            $Asset->Name                                            = $Name;
            $Asset->Contact->ID                                     = $Contact; 
            $Asset->CustomFields->Goodwill->BarCode                 = $Barcode;
            $Asset->CustomFields->Goodwill->ServiceDepartment->ID   = $Department;
            $Asset->save();

            $NewAssetObject             = new RNCPHP\Goodwill\Assets();
            $NewAssetObject->Incident   = (int) $incidentID ; 
            $NewAssetObject->Asset      = RNCPHP\Asset::fetch($Asset->ID);  
            $NewAssetObject->save();
        }*/

    } 

    function assignBack()
    {
        if ($_GET['IncidentID']) 
        {
            $IncidentID = $_GET['IncidentID'];
        }
        if ($_GET['TechID']) 
        {
            $TechID     = $_GET['TechID'];
        }
        
        $incident   = RNCPHP\Incident::fetch($IncidentID);
        $AccountID  = $incident->AssignedTo->Account->ID;
        $Account    = RNCPHP\Account::fetch($AccountID);  
        if ($AccountID > 0) 
        {
            $incident->Queue     = new RNCPHP\NamedIDLabel();
            if ($AccountID == 955) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_VP;
            }
            elseif ($AccountID == 959) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Programer;
            }
            elseif ($AccountID == 937) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Procurement;
            }
            elseif ($AccountID == 936) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Ops_Mngr;
            }
            elseif ($AccountID == 956) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Network_Admin;
            }
            elseif ($AccountID == 933) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Help_Team;
            }
            elseif ($AccountID == 964) 
            {
                $incident->Queue->ID                    = (int) Queue_IT_Admin_Asst;
            }
            elseif ($AccountID == 15) 
            {
                $incident->Queue->ID                    = (int) Queue_Fac_Procurement;
            }
           /* elseif ($AccountID == 932) 
            {
                $incident->Queue->ID                    = (int) Queue_Fac_Opr_Mngr;
            }*/
            elseif ($AccountID == 9 || $AccountID == 8 || $AccountID == 10) 
            {
                $incident->Queue->ID                    = (int) Queue_Fac_DM;
            }
            /*elseif ($AccountID == 6 ) 
            {
                $incident->Queue->ID                    = (int) Queue_Fac_Director;
            }*/
            /*elseif ($AccountID == 18 ) 
            {
                $incident->Queue->ID                    = (int) Queue_Fac_CSR;
            }*/
        }
        //print_r($Account); exit();
        if ($TechID) 
        {
            $Tech       = RNCPHP\Contact::fetch($TechID);
        }
         

        $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
        $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
        $incident->StatusWithType->Status->ID       = (int) StatusReassigned ; 

        $incident->save();
        if ($incident) 
        {
            $msg = 'Success';
            print(json_encode($msg));
        }else
        {
            $msg = 'Failed';
            print(json_encode($msg));
        }
        

    }

    function CreateAsset()
    {

        $json = file_get_contents('php://input');
        $postData   = (array) json_decode($json); 

        $Name           = (string) $postData['Name'] ;
        $Barcode        = $postData['Barcode'] ;
        $SerialNumber   = $postData['SerialNumber'] ;
        $Department     = (int) $postData['Department'];
        $Price          = $postData['Price'];
        $DatePurchased  = $postData['DatePurchased'];
        $Tech           = (int) $postData['Tech']; 
        $FileType       = (string) $postData['FileType'];
        $FileName       = (string) $postData['FileName']; 
        $base64_string  = (string) $postData['FileString'];

        if (!empty($FileName)) 
        {
            $Typearray = explode('/', $FileType);
            $ext = '.'.end($Typearray);
            $callFor = 'Asset';
            $AssetsAttachment = new RNCPHP\Goodwill\AssetsAttachment(); 
            $this->createFile($AssetsAttachment,$base64_string,$FileType,$ext,$FileName,$callFor);
        } 
         
        $NewAsset                                   = new RNCPHP\Asset();
        if (!empty($Name)) 
        {
        	$NewAsset->Name                         = $Name;
        }
        if (!empty($SerialNumber)) 
        {
        	$NewAsset->SerialNumber                 = $SerialNumber;
        }
        
        
        if (!empty($DatePurchased)) 
        {
        	$DatePurchased   = strtotime($DatePurchased);
        	$NewAsset->PurchasedDate = $DatePurchased;
        }
       
        //$NewAsset->Price                            = $Price;
        if ($Price > 0) 
        {
            $NewAsset->Price = new RNCPHP\MonetaryValue();
            $NewAsset->Price->Value=$Price;
            $NewAsset->Price->Currency= new RNCPHP\NamedIDOptList() ;
            $NewAsset->Price->Currency->LookupName= 'USD';
        }
        

        $NewAsset->Product                          = RNCPHP\SalesProduct::fetch(1);
        if ($Barcode > 0) 
        {
            $NewAsset->CustomFields->Goodwill->BarCode  = $Barcode;
        } 
        
        if ($Department > 0) 
        {
            $NewAsset->CustomFields->Goodwill->ServiceDepartment  = RNCPHP\Goodwill\ServiceDepartment::fetch($Department );
        }
        
        ///$NewAsset->StatusWithType                   = new RNCPHP\StatusWithType() ;
        //$NewAsset->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
        $NewAsset->StatusWithType->Status->ID       = (int) readyForDeploy ; 
        $NewAsset->save();
        $AssetsAttachment->Assets = $NewAsset; 
        $AssetsAttachment->save();

        /**/
        if ($NewAsset) 
        {
            $msg = 'Success';
            print(json_encode($msg));
        }else{
            $msg = 'Failed';
            print(json_encode($msg));
        }

    }
    function getDate()
    {
        $ScheduleDate= date('Y-m-d H:i:s');
        $ScheduleDate=strtotime($ScheduleDate);
        print_r($ScheduleDate);
    }

    function createFile($object,$base64_string,$FileType,$ext,$FileName,$callFor)
    {
        if ($callFor == 'Incident') 
        {
            $FileAttachArray = new RNCPHP\FileAttachmentIncidentArray();
            $FileAttach      = new RNCPHP\FileAttachmentIncident(); 
        }elseif ($callFor == 'Asset') 
        {
            $FileAttachArray    = new RNCPHP\FileAttachmentArray();
            $FileAttach         = new RNCPHP\FileAttachment();
        }
        $File = tmpfile();
        fwrite( $File, base64_decode( $base64_string ) ); 
        $path = stream_get_meta_data($File)['uri'];

        $object->FileAttachments        = $FileAttachArray;
        $fattach                        = $FileAttach; 
        $fattach->ContentType           = $FileType;
        $fp                             = $fattach->makeFile();
        $mystring                       = file_get_contents($path, "wb");
        fwrite( $fp, $mystring); 
        fclose( $fp );
        $fattach->FileName              = $FileName;
        $fattach->Name                  = $FileName; 
        $object->FileAttachments[]    = $fattach; 

        return $object;
    }

    function CreateWO()
    {
        $json = file_get_contents('php://input');
        $postData   = (array) json_decode($json); 
        //print_r($postData);
//exit();
        
        $contactID  = (int) $postData['ContactID'];
        $TecID      = (int) $postData['TechID'];
        $Subject    = (string) $postData['Subject']; 
        $Department = (int) $postData['Department'];
        $Threads    = (string) $postData['Thread'];
        $ScheduleDate   = $postData['ScheduleDate'];
        $DueDate        = $postData['DueDate'];
        $Category       = (int) $postData['Category'];
        $FileType       = (string) $postData['FileType'];
        $FileName       = (string) $postData['FileName']; 
        $base64_string  = (string) $postData['FileString']; 


        $ScheduleDate   = strtotime($ScheduleDate);
        $DueDate        = strtotime($DueDate);
        $Contact        = RNCPHP\Contact::fetch( $contactID );
        $ContactDM      = $Contact->CustomFields->Goodwill->Location->Account->ID;
        $Location       = $Contact->CustomFields->Goodwill->Location->ID; 
        $GWDepartment   = (int) $Contact->CustomFields->Goodwill->GWDepartments->ID;
        
        $CI                                         = & get_instance(); 
        $incident                                   = new RNCPHP\Incident();
        if (!empty($FileName)) 
        {
            $Typearray = explode('/', $FileType);
            $ext = '.'.end($Typearray);
            $callFor = 'Incident';
            $this->createFile($incident,$base64_string,$FileType,$ext,$FileName,$callFor);
        }
                
       
        if (!empty($Subject)) 
        {
            $incident->Subject                          = (string) $Subject;
         } 
        
        if ($ScheduleDate) {
            $incident->CustomFields->Goodwill->JobScheduledOn = $ScheduleDate; 
        }
        if ($DueDate) {
            $incident->CustomFields->Goodwill->JobDueDate     = $DueDate;
        }
       // 
        if (!empty($Threads)) 
        {
            $incident->Threads                          = new RNCPHP\ThreadArray();
            $incident->Threads[0]                       = new RNCPHP\Thread();
            $incident->Threads[0]->EntryType            = new RNCPHP\NamedIDOptList();
            $incident->Threads[0]->EntryType->ID        = (int)  3;
            $incident->Threads[0]->Text                 = $Threads;
        }
        
        if ($TecID > 0) 
        {
            $incident->CustomFields->Goodwill->Facilities_Technician = RNCPHP\Contact::fetch($TecID);
        } 
        
        if ($Contact) {
            $incident->PrimaryContact  = $Contact; 
        }
        
        if ($GWDepartment > 0) 
        {
            $incident->CustomFields->Goodwill->GWDepartments = RNCPHP\Goodwill\GWDepartments::fetch($GWDepartment);

        }
        if ($Location > 0) 
        {
            $incident->CustomFields->Goodwill->Location = RNCPHP\Goodwill\Location::fetch($Location);
        }
        
        if (!empty($Department)) 
        {
            $incident->CustomFields->Goodwill->ServiceDepartment  = RNCPHP\Goodwill\ServiceDepartment::fetch($Department ); 
            if ($Department == 1) //Facilities
            {
                $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
                $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
                $incident->StatusWithType->Status->ID       = (int) StatusUnreOpen ;

                if ($ContactDM > 0) 
                {
                    $queryResult               = RNCPHP\ROQL::query("Select * from Goodwill.DM_Delegation where From_DM =".$ContactDM)->next();
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
                                $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($ContactDM);
                                $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($ContactDM);
                            }

                        }else
                        {
                            $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                            $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($ContactDM);
                            $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($ContactDM);
                        }

                    }else  
                    {
                        $incident->AssignedTo                           = new RNCPHP\GroupAccount();
                        $incident->AssignedTo->Account                  = RNCPHP\Account::fetch($ContactDM);
                        $incident->CustomFields->Goodwill->AssignedBy   = RNCPHP\Account::fetch($ContactDM);
                    }
                    
                    $incident->Queue                                = new RNCPHP\NamedIDLabel(); 
                    $incident->Queue->ID                            = (int) DMQueue;
                }

            }elseif ($Department == 2) //IT
            {
                $incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
                $incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
                $incident->StatusWithType->Status->ID       = (int) StatusUnresolved ;

                $queryResult               = RNCPHP\ROQL::queryObject("SELECT Account FROM Account WHERE Account.Profile.ID = 9 AND Account.CustomFields.Goodwill.ProfileMainAccount = 1 LIMIT 1")->next();
                $IT_Account                = $queryResult->next();

                 if($IT_Account)
                {
                    $incident->AssignedTo                               = new RNCPHP\GroupAccount();
                    $incident->AssignedTo->Account                      = $IT_Account;
                    $incident->CustomFields->Goodwill->CurrentlyAssign  = $IT_Account;
                }
                $incident->Queue                        = new RNCPHP\NamedIDLabel(); 
                $incident->Queue->ID                    = (int) Queue_IT_Tech;
            } 
        }       
        if ($Category > 0) 
        {
            $incident->Category   = RNCPHP\ServiceCategory::fetch($Category);
        }
        //print_r($_POST);
        //print_r($incident);
        
        
        if ($contactID > 0 && $TecID > 0 && !empty($Subject) ) 
        {
            $incident->save();
            if ($incident->ID > 0) 
            {
              //  echo "yes";
              $msg = 'Work Order Created, Successfully!'.$incident->ID ; 
              print(json_encode($msg)); 
               
            }else
            {
                $msg = 'Not Created, Error!'; 
              print(json_encode($msg));
            }
            
        }else
        {//echo "string";
        $msg= 'Please Insert Proper Data, Error!';
        print(json_encode($msg)); 
        }

    }
 
    function getIncidentThread()
    {
        $IncidentID = (int) $_GET['IncidentID'];
        $Incident   =  RNCPHP\Incident::fetch($IncidentID);
        $aThread    = $Incident->Threads;
       
        for ($i=0; $i <sizeof($aThread) ; $i++) 
        { 
            //print_r($aThread[$i]->CreatedTime);
            $postData[$i]['From']       = $aThread[$i]->EntryType->LookupName; 
            $postData[$i]['Msg']        = $aThread[$i]->Text; 
            $postData[$i]['Contact']    = $aThread[$i]->Contact->LookupName;
            $postData[$i]['Account']    = $aThread[$i]->Account->LookupName;
            $postData[$i]['Time']       = $aThread[$i]->CreatedTime;
        }
        $postData = array_reverse($postData);
        //echo "<pre>"; print_r($postData);
        print json_encode($postData);


    }

    function getSignOff()
    {
        $IncidentID = $_GET['IncidentID'];
        if ($IncidentID > 0) 
        {
            $Incident   =  RNCPHP\Incident::fetch($IncidentID);
            $Notes      = $Incident->Threads[0]->Text;
            $ResolutionCodeID = $Incident->Disposition->ID; 
        // print_r($Incident->Threads[0]->Text);exit();
            $sql = "SELECT ID AS 'SignOffID' ,Goodwill.SignOff.FileAttachments.ID AS 'FileID' ,Goodwill.SignOff.FileAttachments.FileName AS 'FileName',Goodwill.SignOff.FileAttachments.contentType AS 'contentType' FROM Goodwill.SignOff WHERE Goodwill.SignOff.Incident.ID =".$IncidentID;
            $Query      = RNCPHP\ROQL::query($sql)->next();
            $result     = $Query->next();
            if ($result > 0 ) 
            {
                //print_r($result);
                $SignOffID      = $result['SignOffID'];
                $FileID         = $result['FileID'];
                $FileName       = $result['FileName'];
                $contentType    = $result['contentType'];

                if (!function_exists('curl_init'))
                {
                    load_curl(); 
                }
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://goodwillsocal.custhelp.com/services/rest/connect/latest/Goodwill.SignOff/".$SignOffID."/FileAttachments/".$FileID."/data",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_SSL_VERIFYPEER => false,
                  CURLOPT_POSTFIELDS => "",
                  CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Accept-Encoding: gzip, deflate",
                    "Authorization: Basic bmFkZWVtLmFsaTpFcGhsdXgxMjM=",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Type: application/json",
                    "Host: goodwillsocal.custhelp.com",
                    "OSvC-CREST-Application-Context: as",
                    "Postman-Token: dc692d28-416b-4519-8fb9-44f5d36dd767,90c464ac-268a-41f2-9ea4-83ce1aa0dc16",
                    "User-Agent: PostmanRuntime/7.16.3",
                    "cache-control: no-cache"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                    if (!empty($response)) 
                    {
                        $response = json_decode($response);
                        $fileArray = array('ContentType'=>$contentType,'FileName'=>$FileName,'Data'=>$response->data);
                        $fileResult['ClosingNotes'] = $Notes;
                        $fileResult['ResolutionCodeID'] = $ResolutionCodeID; 
                        $fileResult['File'][] = $fileArray;
                        print json_encode($fileResult);
                    }else
                    {
                        $msg = 'Signoff not found';
                        print json_encode($msg);
                    }
                  
                }
                
            }
        }else
        {
            $msg = 'Incident Id required';
            print json_encode($msg);
        }
        
    }

    function addAssetFile()
    {
        $json 		= file_get_contents('php://input');
        $postData   = (array) json_decode($json); 
        $AssetID 	= (int) $postData['assetID'];
        if ($AssetID > 0 ) 
        {
        	$Asset 		= RNCPHP\Asset::fetch($AssetID);
	        $FileType 	= (string) $postData['fileType'];
	        $FileName 	= (string) $postData['fileName'];
	        $base64_string = (string) $postData['fileString'];
        	
        	$callFor 	= 'Asset';
        	if (!empty($base64_string)) 
        	{
        		$AssetsAttachment = new RNCPHP\Goodwill\AssetsAttachment(); 
		        $this->createFile($AssetsAttachment,$base64_string,$FileType,$ext,$FileName,$callFor);
		        $AssetsAttachment->Assets = $Asset; 
		        $AssetsAttachment->save();
		        if ($AssetsAttachment->ID > 0) 
		        {
		        	$msg = 'Successfully Added!';
        			print json_encode($msg);
		        }else
		        {
		        	$msg = 'Filing Error!';
        			print json_encode($msg);
		        }
        	}else
        	{
        		$msg = 'File is required!';
        		print json_encode($msg);
        	}
        	
	        
        }else
        {
        	$msg = 'Asset Id required!';
        	print json_encode($msg);
        }
       
        
    }

}
?>