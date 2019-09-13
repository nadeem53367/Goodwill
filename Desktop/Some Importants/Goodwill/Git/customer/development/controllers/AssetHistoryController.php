<?php

namespace Custom\Controllers;
use RightNow\Connect\v1_3 as RNCPHP;
use RightNow\Utils\Url,    
RightNow\Utils\Framework;

define('StatusToITHelpTeam', 136);
define('StatusToOprManager', 137); 
define('StatusToVP', 138);
define('StatusToNetworkAdmin', 139);
define('StatusToProcurement', 131);
define('StatusToTech', 106);
define('StatusToAdminAsst', 143);
define('StatusToProgrammer', 144);

class AssetHistoryController extends \RightNow\Controllers\Base
{
    //This is the constructor for the custom controller. Do not modify anything within
    //this function.
    function __construct()
    {
        parent::__construct();
        $this->load->helper('goodwill');
    }
    
    function ITAssetHistory() 
    {
        
        $CI 		= & get_instance();
        $IncidentID = (int) $_POST['IncidentID'];   

        $Incident 	= RNCPHP\Incident::fetch($IncidentID); 
        $InternallyAssignFrom 	= $Incident->CustomFields->Goodwill->InternallyAssignedFrom;

        $this->load->model('custom/AssetHistoryModel');    
      	//$AssetsData 		= $this->AssetHistoryModel->getAssets($IncidentID); 
        $AssetsData         = $this->AssetHistoryModel->getAssetsCO($IncidentID); 

		if ($AssetsData )   
		{ 
	        foreach ($AssetsData as $AssetID )  
            {
            	//$AssetTechHistory 	= $this->AssetHistoryModel->getAssetsHisstory($IncidentID,$AssetID['ID']);

            	/*if($AssetTechHistory == false) 
            	{*/
            		$ID                     = $AssetID['ID'];  
	                $Asset                  = RNCPHP\Asset::fetch( $ID );
	                $iAssetsCreatedBy       = $Asset->CustomFields->Goodwill->AssignTo->ID;
	                $LocationID 			= (int) $Incident->CustomFields->Goodwill->Location->ID;
	                $LocationObject = RNCPHP\Goodwill\Location::fetch( $LocationID );
	                $Asset->CustomFields->Goodwill->Location = $LocationObject;
	                $Asset->save(RNCPHP\RNObject::SuppressAll);  
	                
	                $iIncCurrntAssgn         = $Incident->CustomFields->Goodwill->CurrentlyAssign->ID;

	                if($iIncCurrntAssgn)
	                {
	                	$oIncCurrntAssgn = RNCPHP\Account::fetch( $iIncCurrntAssgn );
	                }
	                $IncidentTech 			= $Incident->CustomFields->Goodwill->Facilities_Technician->ID;

	                $iIncAssignee 			= $Incident->AssignedTo->Account->ID;
	                if($iIncAssignee)
	                {
	                	$oIncAssignee = RNCPHP\Account::fetch( $iIncAssignee );
	                }
	                

	                $NewHistory             = new RNCPHP\Goodwill\AssetsHistory();
	                $NewHistory->Asset      = $Asset;
	                if($IncidentTech > 0 )
	                {
	                	$NewHistory->AssignToTech = RNCPHP\Contact::fetch( $IncidentTech );
	                } 
	               
	                if ($oIncCurrntAssgn) 
		            {
		               $NewHistory->AssignedBy = $oIncCurrntAssgn;
		            }
	               	if ($oIncAssignee) 
	                {
		                $NewHistory->AssignTo   = $oIncAssignee; 
		            } 
		                
		            
	                $NewHistory->AssignedOn 		= time();
	                $NewHistory->AssignmentAction 	= $InternallyAssignFrom;
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
	                
            	//}
            }    
		}

        $Incident->Severity = new RNCPHP\NamedIDOptList();
        $Incident->Severity->ID  = 2; // Stop CPM

		/*if($InternallyAssignFrom == 'Self Assignment By IT Help Desk' || $InternallyAssignFrom == 'From IT Opr Manager To IT Help Team' || $InternallyAssignFrom == 'From IT - Network Admin To IT Help Team' || $InternallyAssignFrom == 'From IT Procurement To IT Help Team' || $InternallyAssignFrom == 'From IT - VP To IT Help Team' || $InternallyAssignFrom == 'From IT Admin Asst To IT Help Team'  || $InternallyAssignFrom == 'From IT Programmer To IT Help Team' )  
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType();
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList();
            $Incident->StatusWithType->Status->ID       = (int) StatusToITHelpTeam ; 
        }
        if($InternallyAssignFrom == 'Self Assignment By Opr Manager' || $InternallyAssignFrom == 'From IT Team To Opr Manager' || $InternallyAssignFrom == 'From IT - Network Admin To Opr Manager' || $InternallyAssignFrom == 'From IT Procurement To Opr Manager' || $InternallyAssignFrom == 'From IT - VP To Opr Manager' || $InternallyAssignFrom == 'From IT Admin Asst To Opr Manager' || $InternallyAssignFrom == 'From IT Programmer To Opr Manager' )  
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToOprManager ;
        }
        if($InternallyAssignFrom == 'Self Assignment By IT VP' || $InternallyAssignFrom == 'From IT Procurement To IT VP' || $InternallyAssignFrom == 'From IT Opr Manager To VP' || $InternallyAssignFrom == 'From IT Network Admin To VP' || $InternallyAssignFrom == 'From IT Team To VP'  || $InternallyAssignFrom == 'From IT Admin Asst To VP'  || $InternallyAssignFrom == 'From IT Programmer To VP' )  
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToVP ;
        }
        if($InternallyAssignFrom == 'Self Assignment By IT Network Admin' || $InternallyAssignFrom == 'From IT - VP To Network Admin' || $InternallyAssignFrom == 'From IT Procurement To IT Network Admin' || $InternallyAssignFrom == 'From IT Team To Network Admin' || $InternallyAssignFrom == 'From IT Opr Manager To Network Admin' || $InternallyAssignFrom == 'From IT Admin Asst To IT Network Admin'  || $InternallyAssignFrom == 'From IT Programmer To IT Network Admin' )  
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ; 
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToNetworkAdmin ;
        }
        if ($InternallyAssignFrom == 'Self Assignment By Procurement Clerk' || $InternallyAssignFrom == 'From IT Team To Procurement Clerk' || $InternallyAssignFrom == 'From IT Opr Manager To Procurement Clerk' || $InternallyAssignFrom == 'From IT Network Admin To Procurement Clerk' || $InternallyAssignFrom == 'From IT VP To Procurement Clerk' || $InternallyAssignFrom == 'From IT Admin Asst To Procurement Clerk'  || $InternallyAssignFrom == 'From IT Programmer To Procurement Clerk' )  
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToProcurement ;
        }
        if ($InternallyAssignFrom == 'Self Assignment As Technician' || $InternallyAssignFrom == 'From IT Team To Tech' || $InternallyAssignFrom == 'From Network Admin To Tech' || $InternallyAssignFrom == 'From Opr Manager To Tech' || $InternallyAssignFrom == 'From Procurement To Tech' || $InternallyAssignFrom == 'From VP To Tech' )   
        {
        	$Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToTech ; 
        }
        if ($InternallyAssignFrom == 'Self Assignment By IT Administrative Assistant' || $InternallyAssignFrom == 'From IT Opr Manager To Administrative Assistant' || $InternallyAssignFrom == 'From IT Network Admin To Administrative Assistant' || $InternallyAssignFrom == 'From IT Procurement To Administrative Assistant' || $InternallyAssignFrom == 'From IT Help Desk To Administrative Assistant' || $InternallyAssignFrom == 'From IT VP To Administrative Assistant'  || $InternallyAssignFrom == 'From IT Programmer To Administrative Assistant' )   
        {
            $Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToAdminAsst ; 
        }
        if ($InternallyAssignFrom == 'Self Assign By IT Programmer' || $InternallyAssignFrom == 'From IT Admin Asst To IT Programmer' || $InternallyAssignFrom == 'From IT Network Admin To IT Programmer' || $InternallyAssignFrom == 'From IT Procurement To IT Programmer' || $InternallyAssignFrom == 'From IT Help Desk To IT Programmer' || $InternallyAssignFrom == 'From IT VP To IT Programmer' )   
        {
            $Incident->StatusWithType                   = new RNCPHP\StatusWithType() ;
            $Incident->StatusWithType->Status           = new RNCPHP\NamedIDOptList() ;
            $Incident->StatusWithType->Status->ID       = (int) StatusToProgrammer ; 
        }*/
 

		$Incident->CustomFields->Goodwill->CurrentlyAssign = RNCPHP\Account::fetch($Incident->AssignedTo->Account->ID);
		$Incident->save(RNCPHP\RNObject::SuppressAll);   
    }
} 