<?php

use RightNow\Connect\v1_3 as RNCPHP;

function getContact()
{ 
	$contact = array(); 
	$CI =& get_instance();
		
	if(isset($CI->session->getProfile()->c_id->value))
	{
		$contact 		= RNCPHP\Contact::fetch( $CI->session->getProfile()->c_id->value );
		//$ContactType 	= $contact->ContactType->LookupName;
	}
			
	return $contact; 
}

function getNEFReportSupervisor($NEFID)
{
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	$supervisor = $NEF->PreparerName->LookupName;
	return $supervisor;

}

function getContactObject($id)
{
	if (empty($id)) 
	{
		$sql 		= "SELECT ID,LookupName FROM Contact";
		$Query 		= RNCPHP\ROQL::query($sql)->next();

		while($ContactInfo = $Query->next()) 
		{
	    	$Contact[] = array('ID' => $ContactInfo['ID'], 'LookupName'=>$ContactInfo['LookupName']);
		}
	}else
	{
		$id = (int) $id ;
		$Contact = RNCPHP\Contact::fetch( $id );
	}
	
	return $Contact;
}

function getIncident($IncidentID) 
{
	$IncidentID = (int) $IncidentID;
	$incident = RNCPHP\Incident::fetch($IncidentID); 
	return $incident; 
}
function getApprovalRecord($RecordID) 
{
	$RecordID = (int) $RecordID;
	$External_Approvals = RNCPHP\Goodwill\External_Approvals::fetch($RecordID); 
	return $External_Approvals; 
}

function getDepartment()
{
	$CI 		= & get_instance();
	$Department 	= array();
	$sql 		= "SELECT ID,LookupName FROM Goodwill.GWDepartments";
	$Query 		= RNCPHP\ROQL::query($sql)->next();

	while($GWDepartment = $Query->next()) 
	{
    	$Department[] = array('ID' => $GWDepartment['ID'], 'LookupName'=>$GWDepartment['LookupName']);
	}
	 //print_r($Department);
	return $Department; 
}
function getAllLocations()
{
	$CI 		= & get_instance();
	$Locations 	= array();
	$sql 		= "SELECT ID,LookupName FROM Goodwill.Location";
	$Query 		= RNCPHP\ROQL::query($sql)->next();

	while($GWLocations = $Query->next()) 
	{
    	$Locations[] = array('ID' => $GWLocations['ID'], 'LookupName'=>$GWLocations['LookupName']);
	}
	 //print_r($Department);
	return $Locations; 
}

function NEFEmplType()
{
	$EmplType 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID,Name FROM Goodwill.NEFEmpType"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($EmplTypeData = $Query->next()) 
		{
	    	$EmplType[] = array('ID' => $EmplTypeData['ID'], 'Name'=>$EmplTypeData['Name']);
		}
	}
	return $EmplType;
}

function WorkstationType()
{
	$WorkstationType 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID,Description,Type,EstimatePrice FROM Goodwill.NEFworkstationType"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($WorkstationTypeData = $Query->next()) 
		{
	    	$WorkstationType[] = array('ID' => $WorkstationTypeData['ID'], 'Description'=>$WorkstationTypeData['Description'], 'Type'=>$WorkstationTypeData['Type'], 'EstimatePrice'=>$WorkstationTypeData['EstimatePrice']);
		}
	}
	return $WorkstationType;
}

function WorkstationAccessorice()
{
	$WorkstationType 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID,Description,Item,EstimatedPrice FROM Goodwill.WorkstationAccessory"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($WorkstationTypeData = $Query->next()) 
		{
	    	$WorkstationType[] = array('ID' => $WorkstationTypeData['ID'], 'Description'=>$WorkstationTypeData['Description'], 'Item'=>$WorkstationTypeData['Item'], 'EstimatePrice'=>$WorkstationTypeData['EstimatedPrice']);
		}
	}
	return $WorkstationType;
}

function NEFSoftwares()
{
	$Softwares 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description FROM Goodwill.NEFSoftware"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($SoftwaresData = $Query->next()) 
		{
	    	$Softwares[] = array('ID' => $SoftwaresData['ID'], 'Description'=>$SoftwaresData['Description'], 'Item'=>$SoftwaresData['Item']);
		}
	}
	return $Softwares;
}
function NonStandardSoftwares()
{
	$NonSoftwares 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description,EstimatedPrice FROM Goodwill.NEFnonStandSoftware"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($NonSoftwaresData = $Query->next()) 
		{
	    	$NonSoftwares[] = array('ID' => $NonSoftwaresData['ID'], 'Description'=>$NonSoftwaresData['Description'], 'Item'=>$NonSoftwaresData['Item'], 'EstimatePrice'=>$NonSoftwaresData['EstimatedPrice']);
		}
	}
	return $NonSoftwares;
}

function DeskPhones()
{
	$DeskPhones 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description,EstimatedPrice FROM Goodwill.NEFDeskPhones"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($DeskPhonesData = $Query->next()) 
		{
	    	$DeskPhones[] = array('ID' => $DeskPhonesData['ID'], 'Description'=>$DeskPhonesData['Description'], 'Item'=>$DeskPhonesData['Item'], 'EstimatePrice'=>$DeskPhonesData['EstimatedPrice']);
		}
	}
	return $DeskPhones;
}

function DeskPhonesFeatures()
{
	$DeskPhonesFeatures 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description,EstimatedPrice FROM Goodwill.DeskPhonesFeatures"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($FeaturesData = $Query->next()) 
		{
	    	$DeskPhonesFeatures[] = array('ID' => $FeaturesData['ID'], 'Description'=>$FeaturesData['Description'], 'Item'=>$FeaturesData['Item'], 'EstimatePrice'=>$FeaturesData['EstimatedPrice']);
		}
	}
	return $DeskPhonesFeatures; 
}

function Mobiles()
{
	$Mobiles 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description,EstimatedPrice FROM Goodwill.NEFmobile"; 
	$Query 	= RNCPHP\ROQL::query($query)->next();  
	if ($Query) 
	{
		while($MobilesData = $Query->next()) 
		{
	    	$Mobiles[] = array('ID' => $MobilesData['ID'], 'Description'=>$MobilesData['Description'], 'Item'=>$MobilesData['Item'], 'EstimatePrice'=>$MobilesData['EstimatedPrice']);
		}
	}
	return $Mobiles; 
}
 
function MobilesFeatures()
{
	$MobilesFeatures 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, Item, Description,EstimatedPrice,MonthlyFee FROM Goodwill.NEFmobileAccessories"; 
	$Query 	= RNCPHP\ROQL::query($query)->next();  
	if ($Query) 
	{
		while($MobilesFeaturesData = $Query->next()) 
		{
	    	$MobilesFeatures[] = array('ID' => $MobilesFeaturesData['ID'], 'Description'=>$MobilesFeaturesData['Description'], 'Item'=>$MobilesFeaturesData['Item'], 'EstimatePrice'=>$MobilesFeaturesData['EstimatedPrice']);
		}
	}
	return $MobilesFeatures; 
}

function ShowTextCategories($ID)
{
	if ($ID) 
	{
		$CI 		= & get_instance();
		$TextCategory 	= array();
		$sql 		= "Select ServiceCategory.LookupName AS 'LastCategory', ServiceCategory.Parent.ID AS 'ParentID' ,ServiceCategory.Parent.LookupName AS 'MiddleCategory' FROM ServiceCategory where ServiceCategory.ID=".$ID;
		$Query 		= RNCPHP\ROQL::query($sql)->next();
		$Category 	= $Query->next();

		$TextCategory['LastCategory'] = $Category['LastCategory'];
		if ($Category['ParentID']) 
		{
			$TextCategory['MiddleCategory'] = $Category['MiddleCategory'];
		}	 
		if ($Category['MiddleCategory']) 
		{
			# code...

			$sql 		= "Select ServiceCategory.Parent.LookupName FROM ServiceCategory where ServiceCategory.ID=".$Category['ParentID'];
			$Query 		= RNCPHP\ROQL::query($sql)->next();
			$Category 	= $Query->next();
			$TextCategory['TopCategory'] = $Category['LookupName'];
		}
	}
	return $TextCategory; 
}

 function testCat($ID)
 {
 	$testString ='';
 	$sql 		= "Select ServiceCategory.LookupName As Name, ServiceCategory.ID FROM ServiceCategory where ServiceCategory.ID = ".$ID;
	$Query 		= RNCPHP\ROQL::query($sql)->next();
	$ActualCat 	= $Query->next();
	$testString .= $ActualCat['Name'];

 	$sql 		= "Select ServiceCategory.Parent.LookupName As Name, ServiceCategory.Parent.ID FROM ServiceCategory where ServiceCategory.ID = ".$ID;
	$Query 		= RNCPHP\ROQL::query($sql)->next();
	$Parent 	= $Query->next();
	if ($Parent['ID'] > 0) 
	{
		$testString .= ' / '.$Parent['Name'];
	}
	
	if ($Parent['ID'] > 0) {
		$testarray['Fparent'] = $Parent; 
		$sql 		= "Select ServiceCategory.Parent.LookupName As Name , ServiceCategory.Parent.ID FROM ServiceCategory where ServiceCategory.ID = ".$Parent['ID'];
		$Query 		= RNCPHP\ROQL::query($sql)->next();
		$Parent 	= $Query->next();
		$testString .= ' / '.$Parent['Name'];
	}
	return $testString;
 	# code...
 }
 
 function getMainCategories()
{
	$CI 		= & get_instance();
	$Category 	= array();
	$sql 		= "Select ServiceCategory FROM ServiceCategory where ServiceCategory.Parent is NULL";
	$Query 		= RNCPHP\ROQL::queryObject($sql)->next();

	while($ServiceCategory = $Query->next()) 
	{
    	$Category[] = array('ID' => $ServiceCategory->ID, 'LookupName'=>$ServiceCategory->LookupName);
	}
	 
	return $Category;
}

function getProduct($id)
{
	$CI 		= & get_instance();
	$Category 	= array();
	$Query = RNCPHP\ROQL::queryObject("Select ServiceCategory FROM ServiceCategory where ServiceCategory.Parent.ID = ".$id)->next(); 

	while($ServiceCategory = $Query->next()) 
	{
    	$Category[] = array('ID' => $ServiceCategory->ID, 'LookupName'=>$ServiceCategory->LookupName);
	}
	 
	return $Category;
}

function getRetailStoreMngr()
{
	$CI 	= & get_instance();
	$RSM 	= array();
	$Query 	= RNCPHP\ROQL::query("SELECT ID , LookupName As 'Name' , Contact.CustomFields.Goodwill.Location.ZipCode AS 'ZipCode' FROM Contact WHERE Contact.ContactType.ID = 6 ")->next(); 

	while($RetailStoreMngr = $Query->next()) 
	{
    	$RSM[] = array('ID' => $RetailStoreMngr['ID'], 'Name'=>$RetailStoreMngr['Name'], 'ZipCode'=>$RetailStoreMngr['ZipCode']);
	}
	 
	return $RSM;
}

function getSClocations()
{
	$CI 		= & get_instance();
	$Locations 	= array();
	$Query 	= RNCPHP\ROQL::query("SELECT ID, Title, SupplyChainHub.Name AS 'SC_HUB', Dispatcher FROM Goodwill.Location WHERE Goodwill.Location.SupplyChainHub.ID IS NOT NULL ")->next(); 

	while($SClocation = $Query->next()) 
	{
    	$Locations[] = array('ID' => $SClocation['ID'], 'Title'=>$SClocation['Title'], 'SC_HUB'=>$SClocation['SC_HUB']);
	}
	 
	return $Locations; 
}

function getContactAssets()
{
	$Asset 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID, LookupName AS 'Name' FROM Asset WHERE Asset.Contact.ID =".$CI->session->getProfile()->c_id->value; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($AssetData = $Query->next()) 
		{
	    	$Asset[] = array('ID' => $AssetData['ID'], 'Name'=>$AssetData['Name']);
		}
	}
	return $Asset;
}

function getWorstation()
{
	$workstation 	= array();
	$CI =& get_instance();
	$query  = "SELECT ID,Name FROM Goodwill.NEFWorkstation"; 
	$Query 	= RNCPHP\ROQL::query($query)->next(); 
	if ($Query) 
	{
		while($workstationData = $Query->next()) 
		{
	    	$workstation[] = array('ID' => $workstationData['ID'], 'Name'=>$workstationData['Name']);
		}
	}
	return $workstation;
}
function debug($Data)
{
	echo '<pre>';
	print_r($Data);
	echo '</pre>';
}

?>