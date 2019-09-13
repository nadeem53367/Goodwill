<?php

use RightNow\Connect\v1_3 as RNCPHP;


function getGeneralInfo($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID); 
	$postArray['PreparedDate'] 		= $NEF->PreparedDate;
	$postArray['PreparerName'] 		= $NEF->PreparerName->LookupName;
	$postArray['EmplID'] 			= $NEF->Contact->ID;
	$postArray['EmplFirstName'] 	= $NEF->EmplFirstName;
	$postArray['EmplLastName'] 		= $NEF->EmplLastName;
	$postArray['EmplDepartment'] 	= $NEF->EmplDepartment->LookupName;
	$postArray['EmplLocation'] 		= $NEF->EmplLocation->LookupName;
	$postArray['EmplDepartmentID'] 	= $NEF->EmplDepartment->ID;
	$postArray['EmplLocationID'] 	= $NEF->EmplLocation->ID;
	$postArray['EmplGLcodes'] 		= $NEF->EmplGLcodes;
	$postArray['Allocations'] 		= $NEF->Allocations;
	$postArray['JobTitle'] 			= $NEF->JobTitle;
	$postArray['EmplStartDate'] 	= $NEF->EmplStartDate;
	$postArray['PurchaseApprovalPerson'] 	= $NEF->PurchaseApprovalPerson->LookupName;
	$postArray['PurchaseApprovalPersonID'] 	= $NEF->PurchaseApprovalPerson->ID;
	return $postArray;
}

function getEmailAddress($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	if ( $NEF->isRequireEmail == true || $NEF->isRequireEmail == '1') 
	{
		$postArray['isRequireEmail'] = 'Yes';
	}else
	{
		$postArray['isRequireEmail'] = 'No';
	}
	if ($NEF->isTempEmpl == true || $NEF->isTempEmpl == '1') 
	{
		$postArray['isTempEmpl']  			= 'Yes'; 
	}else
	{
		$postArray['isTempEmpl']  			= 'No'; 
	}
	if ($NEF->havePreviousGWemail == true || $NEF->havePreviousGWemail == '1') 
	{
		$postArray['havePreviousGWemail']  	= 'Yes';
	}else
	{
		$postArray['havePreviousGWemail']  	= 'No';

	}if ($NEF->isNeedVPN == true || $NEF->isNeedVPN == '1') 
	{
		$postArray['isNeedVPN']  			= 'Yes'; 
	}else
	{
		$postArray['isNeedVPN']  			= 'No'; 
	}
	if ($NEF->isRetailStoreEmpl == true || $NEF->isRetailStoreEmpl == '1') 
	{
		$postArray['isRetailStoreEmpl']  	= 'Yes';
	}else
	{
		$postArray['isRetailStoreEmpl']  	= 'No';
	}
	 
	$postArray['EmpTypeIs']  			= $NEF->EmpTypeIs->LookupName; 
	return $postArray;
}

function getMailAndResource($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	$postArray['MailingList']  			= $NEF->MailingList;
	$postArray['ResourceAccessGroups']  = $NEF->ResourceAccessGroups;
	return $postArray;
}

function getObject($query)
{
	
	$Query 		= RNCPHP\ROQL::query($query)->next();

	while($ObjectInfo = $Query->next()) 
	{
		if (!empty($ObjectInfo)) 
		{
			$Object[] = $ObjectInfo;
		}
    	
	}
	return $Object;
}
function getWorkstation($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	$postArray['WorkstationID']  		= $NEF->Workstation->ID;
	$postArray['Workstation']  			= $NEF->Workstation->LookupName;
	$postArray['WorkstationInfo']  			= $NEF->WorkstationInfo;
	$query='SELECT ID FROM Goodwill.WSTypeMiddleLayer WHERE Goodwill.WSTypeMiddleLayer.NEF.ID ='.$NEFID;
	$WSMidLayer = getObject($query);
	if (!empty($WSMidLayer)) 
	{
		$WSType = RNCPHP\Goodwill\WSTypeMiddleLayer::fetch($WSMidLayer[0]['ID']);
		$WorkStationType  = $WSType->WSType->LookupName;
		$WorkStationPrice = $WSType->WSType->EstimatePrice;
		//print_r($Type);
		$postArray['WorkStationType'] = $WorkStationType; 
		$postArray['EstimatePrice'] = $WorkStationPrice; 
	}

	$query='SELECT ID FROM Goodwill.WSAccessMiddleLayer WHERE Goodwill.WSAccessMiddleLayer.NEF.ID ='.$NEFID;
	$WSAccessMidLayerID = getObject($query);
	if (!empty($WSAccessMidLayerID)) 
	{
		for ($i=0; $i <sizeof($WSAccessMidLayerID) ; $i++) 
		{ 
			$ID = $WSAccessMidLayerID[$i]['ID'];
			$WSAccessMidLayer 	= RNCPHP\Goodwill\WSAccessMiddleLayer::fetch($ID);
			$Accessories[$i]['ID'] 		=  $WSAccessMidLayer->WSAccessories->ID;
			$Accessories[$i]['Name']  	=  $WSAccessMidLayer->WSAccessories->Item;
			$Accessories[$i]['EstimatePrice']  	=  $WSAccessMidLayer->WSAccessories->EstimatedPrice;
			
			//echo "ID is:".$ID;
		}
		$postArray['WSAccessories'] = $Accessories;
	}
	//echo "<pre>"; print_r($postArray);
	//$postArray['ResourceAccessGroups']  = $NEF->ResourceAccessGroups;
	return $postArray;
}

function Software($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	
	$query='SELECT ID FROM Goodwill.SoftwareMiddleLayer WHERE Goodwill.SoftwareMiddleLayer.NEF.ID ='.$NEFID;
	$SoftwareMidLayerIDs = getObject($query);
	if (!empty($SoftwareMidLayerIDs)) 
	{
		for ($i=0; $i <sizeof($SoftwareMidLayerIDs) ; $i++) 
		{ 
			$ID = $SoftwareMidLayerIDs[$i]['ID'];
			$SoftwareMidLayer 	= RNCPHP\Goodwill\SoftwareMiddleLayer::fetch($ID);
			$Softwares[]  	=  $SoftwareMidLayer->NEFSoftware->Item;
			
			//echo "ID is:".$ID;
		}
		$postArray['Softwares']=$Softwares;
	}
	

	$query='SELECT ID FROM Goodwill.NonSoftwareMidLayer WHERE Goodwill.NonSoftwareMidLayer.NEF.ID ='.$NEFID;
	$NonSoftwareMidLayerID = getObject($query);
	if (!empty($NonSoftwareMidLayerID)) 
	{
		for ($i=0; $i <sizeof($NonSoftwareMidLayerID) ; $i++) 
		{ 
			$ID = $NonSoftwareMidLayerID[$i]['ID'];
			$NonSoftwareMidLayer 	= RNCPHP\Goodwill\NonSoftwareMidLayer::fetch($ID);
			$NonSoftware[$i]['ID']  	=  $NonSoftwareMidLayer->NEFnonStandSoftware->ID;
			$NonSoftware[$i]['Name']  	=  $NonSoftwareMidLayer->NEFnonStandSoftware->Item;
			$NonSoftware[$i]['EstimatePrice']  	=  $NonSoftwareMidLayer->NEFnonStandSoftware->EstimatedPrice;
			
			//echo "ID is:".$ID;
		}
		$postArray['NonSoftware'] = $NonSoftware;
	}
	$postArray['OtherSoftwares'] = $NEF->OtherSoftwares;
	$postArray['OtherSpecialSoftwares'] = $NEF->OtherSpecialSoftwares;
	//echo "<pre>"; print_r($postArray);
	//$postArray['ResourceAccessGroups']  = $NEF->ResourceAccessGroups;
	return $postArray;
}

function CablingAndPrinting($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	$postArray['CablingAndPrinting'] = $NEF->CablingAndPrinting;
	$postArray['PrinterList'] = $NEF->PrinterList;
	$postArray['ScannerList'] = $NEF->ScannerList;
	if ($NEF->needPrintingAndScanning == true || $NEF->needPrintingAndScanning == '1') 
	{
		$postArray['needPrintScan']  			= 'Yes'; 
	}else
	{
		$postArray['needPrintScan']  			= 'No'; 
	}
	
	return $postArray;
}

function communicationDevices($NEFID)
{
	$postArray = array();
	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
	if ($NEF->isRequireDeskPhone == true || $NEF->isRequireDeskPhone == 1) 
	{
		$postArray['isRequireDeskPhone']  = 'Yes';
	}else
	{
		$postArray['isRequireDeskPhone']  = 'No';
	}
	

	$query='SELECT ID FROM Goodwill.DeskPhoneMiddleLayer WHERE Goodwill.DeskPhoneMiddleLayer.NEF.ID ='.$NEFID;
	$DeskPhoneMiddleLayerID = getObject($query);
	if (!empty($DeskPhoneMiddleLayerID)) 
	{
		$DeskPhoneMiddleLayer = RNCPHP\Goodwill\DeskPhoneMiddleLayer::fetch($DeskPhoneMiddleLayerID[0]['ID']);
		$DeskPhoneName  = $DeskPhoneMiddleLayer->NEFDeskPhones->Item;
		$DeskPhonePrice = $DeskPhoneMiddleLayer->NEFDeskPhones->EstimatedPrice;
		//print_r($Type);
		$postArray['DeskPhoneName'] 	= $DeskPhoneName; 
		$postArray['DeskPhonePrice'] 	= $DeskPhonePrice;  
	}

	$query='SELECT ID FROM Goodwill.DeskPhnAccesMidLayer WHERE Goodwill.DeskPhnAccesMidLayer.NEF.ID ='.$NEFID;
	$DeskPhnAccesMidLayerID = getObject($query);
	if (!empty($DeskPhnAccesMidLayerID)) 
	{
		for ($i=0; $i <sizeof($DeskPhnAccesMidLayerID) ; $i++) 
		{ 
			$ID = $DeskPhnAccesMidLayerID[$i]['ID'];
			$DeskPhnAccesMidLayer 		= RNCPHP\Goodwill\DeskPhnAccesMidLayer::fetch($ID);
			$Accessories[$i]['Name']  	=  $DeskPhnAccesMidLayer->DeskPhonesFeatures->Item;
			$Accessories[$i]['EstimatePrice']  	=  $DeskPhnAccesMidLayer->DeskPhonesFeatures->EstimatedPrice;
			
			//echo "ID is:".$ID;
		}
		$postArray['DeskPhnAccessories'] = $Accessories;
	}

	if ($NEF->willUseExistingExtension == true || $NEF->willUseExistingExtension == 1) 
	{
		$postArray['willUseExistingExtension']  	= 'Yes';
	}else
	{
		$postArray['willUseExistingExtension']  	= 'No';
	}

	if ($NEF->willNeedFaxNumber == true || $NEF->willNeedFaxNumber == 1) 
	{
		$postArray['willNeedFaxNumber']  	= 'Yes';
	}else
	{
		$postArray['willNeedFaxNumber'] 	= 'No';
	}
	
	$postArray['ExistingExtension']  		= $NEF->ExistingExtension;
	
	$postArray['FaxMachine'] 		  		= $NEF->NeedFax->Name;


	if ($NEF->willRequireMobile == true || $NEF->willRequireMobile == 1) 
	{
		$postArray['willRequireMobile']  	= 'Yes';
	}else
	{
		$postArray['willRequireMobile'] 	= 'No';
	}
	
	$query='SELECT ID FROM NEF.MobileDeviceMidLayer WHERE NEF.MobileDeviceMidLayer.NEF.ID ='.$NEFID;
	$MobileDeviceMidLayerID = getObject($query);
	if (!empty($MobileDeviceMidLayerID)) 
	{
		$MobileDeviceMidLayer = RNCPHP\NEF\MobileDeviceMidLayer::fetch($MobileDeviceMidLayerID[0]['ID']);
		$MobilePhoneName  = $MobileDeviceMidLayer->NEFmobile->Item;
		$MobilePhonePrice = $MobileDeviceMidLayer->NEFmobile->EstimatedPrice; 
		//print_r($Type);
		$postArray['MobilePhoneName'] 	= $MobilePhoneName; 
		$postArray['MobilePhonePrice'] 	= $MobilePhonePrice;  
	}




	$query='SELECT ID FROM NEF.MobileAccessMidLayer WHERE NEF.MobileAccessMidLayer.NEF.ID ='.$NEFID;
	$MblAccesMidLayerID = getObject($query);
	if (!empty($MblAccesMidLayerID)) 
	{
		for ($i=0; $i <sizeof($MblAccesMidLayerID) ; $i++) 
		{ 
			$ID = $MblAccesMidLayerID[$i]['ID'];
			$MobAccesMidLayer 		= RNCPHP\NEF\MobileAccessMidLayer::fetch($ID);
			$Accessories[$i]['Name']  	=  $MobAccesMidLayer->NEFmobileAccessories->Item;
			$Accessories[$i]['EstimatePrice']  	=  $MobAccesMidLayer->NEFmobileAccessories->EstimatedPrice;
			
			//echo "ID is:".$ID;
		}
		$postArray['MobileAccessories'] = $Accessories;
	}
	$postArray['Notes'] = $NEF->Note; 
	return $postArray;
} 
?>
