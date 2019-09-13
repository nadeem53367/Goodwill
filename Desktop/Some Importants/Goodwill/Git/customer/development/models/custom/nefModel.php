<?php

//define('Incident_Status',111);

namespace Custom\Models;
use RightNow\Connect\v1_3 as RNCPHP;

class nefModel extends \RightNow\Models\Base
{
    function __construct()
    {
        parent::__construct();
    }

    function validateNEF($code)
    {
        $sql='SELECT ID FROM Goodwill.NEF WHERE Goodwill.NEF.validationCode = '.$code;
        $Query  = RNCPHP\ROQL::query($sql)->next();
        $ObjectInfo = $Query->next();
        $ID = $ObjectInfo['ID'];
        if ($ID > 0 ) 
        {
            return $ID;
        }else{
            $msg = 0;
            return $msg; 
        }
        //print_r($ObjectInfo['ID']);
    }
    function generalinformation($Object,$postData)
    {
        $NEF = $Object;
        
        $EmplID  = $postData['EmplID'];
        $EmplFirstName  = $postData['Fname'];
        $EmplLastName   = $postData['Lname'];
        $Department     = $postData['Department'];
        $Location       = $postData['Location'];
        $GLCodes        = $postData['GLCodes'];
        $Allocation     = $postData['Allocation'];
        $JobTitle       = $postData['JobTitle'];
        $startDate      = $postData['startDate'];
        $ApprovalPerson = $postData['ApprovalPerson'];

        if ($EmplID > 0) 
        {
            $NEF->Contact = RNCPHP\Contact::fetch( $EmplID );
        }
        

        if (!empty($EmplFirstName)) 
        {
            $NEF->EmplFirstName = $EmplFirstName;
        }
        if (!empty($EmplLastName)) 
        {
            $NEF->EmplLastName  = $EmplLastName;
        }
        if (!empty($Department)) 
        {
            $NEF->EmplDepartment = RNCPHP\Goodwill\GWDepartments::fetch( $Department );
        }
        if ($Location) 
        {
            $NEF->EmplLocation   = RNCPHP\Goodwill\Location::fetch( $Location );
        }
        
        if (!empty($GLCodes)) 
        {
            $NEF->EmplGLcodes   = $GLCodes;
        }
        
        if (!empty($Allocation))
        {
            $NEF->Allocations   = $Allocation;
        }
        
        if (!empty($JobTitle)) 
        {
            $NEF->JobTitle      = $JobTitle;
        }
        
        if (!empty($startDate)) 
        {
            $NEF->EmplStartDate     = strtotime($startDate);
        }
        if ($ApprovalPerson > 0 ) 
        {
            $NEF->PurchaseApprovalPerson    = RNCPHP\Contact::fetch( $ApprovalPerson );
        }
        return $NEF;
    }

    function email_address($Object,$postData)
    {
        $NEF = $Object;
        $requireEmail   = $postData['requireEmail'];
        $isTempEmpl     = $postData['isTempEmpl'];
        $previousEmail  = $postData['previousEmail'];
        $needVPN        = $postData['needVPN'];
        $isRetailStoreMngr  = $postData['isRetailStoreMngr'];
        $employeTypeIs  = $postData['employeTypeIs'];

        if (!empty($requireEmail)) 
        {
            if ($requireEmail == 'Yes') 
            {
                $NEF->isRequireEmail        = true;
            }else {
                $NEF->isRequireEmail        = false;
            }
        }
        
        if (!empty($isTempEmpl)) 
        {
            if ($isTempEmpl == 'Yes') 
            {
                $NEF->isTempEmpl        = true;
            }else {
                $NEF->isTempEmpl        = false;
            }
        }
        
        if (!empty($previousEmail)) 
        {
             if ($previousEmail == 'Yes') 
            {
                $NEF->havePreviousGWemail       = true;
            }else {
                $NEF->havePreviousGWemail       = false;
            }
        }
       
        if (!empty($needVPN)) 
        {
            if ($needVPN == 'Yes') 
            {
                $NEF->isNeedVPN         = true;
            }else {
                $NEF->isNeedVPN         = false;
            }
        }
        
        if (!empty($isRetailStoreMngr)) 
        {
            if ($isRetailStoreMngr == 'Yes') 
            {
                $NEF->isRetailStoreEmpl         = true;
            }else {
                $NEF->isRetailStoreEmpl         = false;
            }
        }
        

        if (!empty($employeTypeIs)) 
        {
            $NEF->EmpTypeIs         = RNCPHP\Goodwill\NEFEmpType::fetch( $employeTypeIs );
        }

        return $NEF;
    }

    function mailing_and_resource($Object,$postData)
    {
        $NEF = $Object;
        $MailingAndResource = $postData['MailingAndResource'];
        $ResourceAccessGrp  = $postData['ResourceAccessGrp'];

        if (!empty($MailingAndResource)) 
        {
            $NEF->MailingList           = $MailingAndResource;
        }
        if (!empty($ResourceAccessGrp)) 
        {
            $NEF->ResourceAccessGroups  = $ResourceAccessGrp;
        }
        return $NEF;
    }

    function destroyData($pakageName,$objectArray,$NEFID)
    {
        //echo "Indestroy";
        //print_r($objectArray);
        $aObject =$objectArray['objectName'];
        for ($i=0; $i <sizeof($aObject) ; $i++) 
        { 
            $objectName = $aObject[$i];
            
            $sql='SELECT ID FROM '.$pakageName.'.'.$objectName.' WHERE '.$pakageName.'.'.$objectName.'.NEF.ID ='.$NEFID;

            $Query  = RNCPHP\ROQL::query($sql)->next();
           // $ObjectInfo = $Query->next() 
        
            while($ObjectInfo = $Query->next()) 
            {   
                if (!empty($ObjectInfo)) 
                {
                    //print_r($ObjectInfo);
                    
                    if ($objectName == 'WSTypeMiddleLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\WSTypeMiddleLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                        
                    }
                    if ($objectName == 'WSAccessMiddleLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\WSAccessMiddleLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                    } 

                     if ($objectName == 'SoftwareMiddleLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\SoftwareMiddleLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                        
                    }
                    if ($objectName == 'NonSoftwareMidLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\NonSoftwareMidLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                    }

                
                    if ($objectName == 'MobileAccessMidLayer') 
                    {
                        $Obj = RNCPHP\NEF\MobileAccessMidLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                    }
                    if ($objectName == 'MobileDeviceMidLayer') 
                    {
                        $Obj = RNCPHP\NEF\MobileDeviceMidLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                    }
                    
                    if ($objectName == 'DeskPhoneMiddleLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\DeskPhoneMiddleLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                        
                    }
                    if ($objectName == 'DeskPhnAccesMidLayer') 
                    {
                        $Obj = RNCPHP\Goodwill\DeskPhnAccesMidLayer::fetch( $ObjectInfo['ID'] );
                        //print_r($Obj);
                    }

                    $Obj->destroy();
                    //exit();
                }
               
            
            }
        }
        //return true;
    }

    function worstation($Object,$postData)
    {

        $NEF = $Object;
        if ( isset($postData['callFrom']) && $postData['callFrom'] == 'view') 
        {
            $NEFID      = $NEF->ID;
            $ObjData    = array('objectName' => array('WSTypeMiddleLayer','WSAccessMiddleLayer' ));
            $pakageName = 'Goodwill';
            $DestroyedObject = $this->destroyData($pakageName,$ObjData,$NEFID);                   
           
        } 
        $Workstation        = $postData['Workstation'];
        $workstationInfo    = $postData['workstationInfo'];
        $WorkstationType    = $postData['WorkstationType'];
        $WorkstationAccessories    = $postData['WorkstationAccessories'];

        if ($Workstation > 0 ) 
        {
            $NEF->Workstation   = RNCPHP\Goodwill\NEFWorkstation::fetch( $Workstation );
            if (!empty($workstationInfo)) 
            {
                $NEF->WorkstationInfo = $workstationInfo;
            }
            if ($WorkstationType > 0 ) 
            { //echo "string";
                $WSTypeMiddleLayer      =  new RNCPHP\Goodwill\WSTypeMiddleLayer();
                $WSTypeMiddleLayer->NEF = $NEF;
                $WSTypeMiddleLayer->WSType = RNCPHP\Goodwill\NEFworkstationType::fetch($WorkstationType)  ; 
                $WSTypeMiddleLayer->save();
                
            }
            
            if (!empty($WorkstationAccessories)) 
            {
                //echo sizeof($WorkstationAccessories);exit();
                
                for ($j=0; $j <sizeof($WorkstationAccessories); $j++) 
                {
                    $WSAccessoriesMiddleLayer      =  new RNCPHP\Goodwill\WSAccessMiddleLayer();
                    $WSAccessoriesMiddleLayer->WSAccessories = RNCPHP\Goodwill\WorkstationAccessory::fetch( $WorkstationAccessories[$j]) ;
                    $WSAccessoriesMiddleLayer->NEF = $NEF;
                    $WSAccessoriesMiddleLayer->save();
                }
                
                //print_r($WorkstationAccessories[0]); 
            }
        }
        return $NEF;


    }

    function NEFSoftwares($Object,$postData)
    {
        $NEF = $Object;

        if ( isset($postData['callFrom']) && $postData['callFrom'] == 'view') 
        {
            $NEFID      = $NEF->ID;
            $ObjData    = array('objectName' => array('SoftwareMiddleLayer','NonSoftwareMidLayer' ));
            $pakageName = 'Goodwill';
            $DestroyedObject = $this->destroyData($pakageName,$ObjData,$NEFID);                   
           
        } 

        $otherProgrames     = $postData['otherProgrames'];
        $otherSoftware      = $postData['otherSoftware'];
        $Softwares          = $postData['Softwares'];
        $NonStandardSoftwares      = $postData['NonStandardSoftwares'];

        if ($otherProgrames) 
        {
            $NEF->OtherSoftwares = $otherProgrames;
        }
        if ($otherSoftware) 
        {
            $NEF->OtherSpecialSoftwares = $otherSoftware;
        }
        if (!empty($Softwares)) 
        {
            for ($i=0; $i <sizeof($Softwares) ; $i++) 
            { 
                $SoftwareMiddleLayer = new RNCPHP\Goodwill\SoftwareMiddleLayer();
                $SoftwareMiddleLayer->NEF = $NEF;
                $SoftwareMiddleLayer->NEFSoftware = RNCPHP\Goodwill\NEFSoftware::fetch($Softwares[$i]);
                $SoftwareMiddleLayer->save();
            }
        }
        if (!empty($NonStandardSoftwares)) 
        {
            for ($i=0; $i <sizeof($NonStandardSoftwares) ; $i++) 
            { 
                $NonSoftwareMidLayer = new RNCPHP\Goodwill\NonSoftwareMidLayer();
                $NonSoftwareMidLayer->NEF = $NEF;
                $NonSoftwareMidLayer->NEFnonStandSoftware = RNCPHP\Goodwill\NEFnonStandSoftware::fetch($NonStandardSoftwares[$i]);
                $NonSoftwareMidLayer->save();
            }
        }
        return $NEF;

    }

    function printing_and_cabling($Object,$postData)
    {
        $NEF = $Object; 
        $printing_and_cabling   = $postData['printing_and_cabling'];
        $needPrintOrScan        = $postData['needPrintOrScan'];
        $printList              = $postData['printList'];
        $scanList               = $postData['scanList'];

        if (!empty($printing_and_cabling)) 
        {
            $NEF->CablingAndPrinting = $printing_and_cabling;
        }
        if (!empty($needPrintOrScan)) 
        {
            if ($needPrintOrScan == 'Yes' || $needPrintOrScan == true) 
            {
                $NEF->needPrintingAndScanning = true;
            }else{
                $NEF->needPrintingAndScanning = false;
            }
        }
        
        if (!empty($printList)) 
        {
            $NEF->PrinterList = $printList;
        }
        if (!empty($scanList)) 
        {
            $NEF->ScannerList = $scanList;
        }

        return $NEF;
    }

    function communication_devices($Object,$postData)
    {
        $NEF = $Object;

        if ( isset($postData['callFrom']) && $postData['callFrom'] == 'view') 
        {
            $NEFID      = $NEF->ID;

            $ObjData    = array('objectName' => array('DeskPhoneMiddleLayer','DeskPhnAccesMidLayer' ));
            $pakageName = 'Goodwill';
            for ($i=0; $i <2 ; $i++) 
            { 
                $DestroyedObject = $this->destroyData($pakageName,$ObjData,$NEFID);
                $ObjData    = array('objectName' => array('MobileDeviceMidLayer','MobileAccessMidLayer' ));
                $pakageName = 'NEF';
            }
                               
           
        } 

        $isRequirePhone         = $postData['isRequirePhone'];
        $DeskPhones             = $postData['DeskPhones'];
        $DeskPhoneAccessories   = $postData['DeskPhoneAccessories'];
        $usingExtension         = $postData['usingExtension'];
        $extension              = $postData['extension'];
        $usingFax               = $postData['usingFax'];
        $FaxType                = $postData['FaxType'];

        $Requiremobile          = $postData['Requiremobile']; 
        $MobileDevices          = $postData['MobileDevices']; 
        $MobileAccessories      = $postData['MobileAccessories']; 

        $Notes                  = $postData['Notes']; 

        if (!empty($isRequirePhone)) 
        {
            if ($isRequirePhone == 'Yes') 
            {
                $NEF->isRequireDeskPhone = true; //
            }else
            {
                $NEF->isRequireDeskPhone = false;
            }
        }
       

        if ($DeskPhones > 0) 
        {
            $DeskPhoneMiddleLayer = new RNCPHP\Goodwill\DeskPhoneMiddleLayer();
            $DeskPhoneMiddleLayer->NEF = $NEF;
            $DeskPhoneMiddleLayer->NEFDeskPhones = RNCPHP\Goodwill\NEFDeskPhones::fetch($DeskPhones);
            $DeskPhoneMiddleLayer->save();
        }

        if (!empty($DeskPhoneAccessories)) 
        {
            for ($i=0; $i <sizeof($DeskPhoneAccessories) ; $i++) 
            { 
                $DeskPhnAccesMidLayer = new RNCPHP\Goodwill\DeskPhnAccesMidLayer();
                $DeskPhnAccesMidLayer->NEF = $NEF;
                $DeskPhnAccesMidLayer->DeskPhonesFeatures = RNCPHP\Goodwill\DeskPhonesFeatures::fetch($DeskPhoneAccessories[$i]);
                $DeskPhnAccesMidLayer->save();
            }
        }
        if (!empty($usingExtension)) 
        {
            if ($usingExtension == 'Yes') 
            {
                $NEF->willUseExistingExtension = true;
            }else
            {
                $NEF->willUseExistingExtension = false;
            }
        }
        

        if (!empty($extension)) 
        {
            $NEF->ExistingExtension = $extension;
        }

        if (!empty($usingFax)) 
        {
            if ($usingFax == 'Yes') 
            {
                $NEF->willNeedFaxNumber = true;
            }else{
                $NEF->willNeedFaxNumber = false;
            }
        }
        

        if ($FaxType == 'FaxToEmail') 
        {
            $NEF->NeedFax = RNCPHP\Goodwill\NEFfax::fetch(1);

        }elseif ($FaxType == 'FaxMachine') 
        {
            $NEF->NeedFax = RNCPHP\Goodwill\NEFfax::fetch(2);
        }

        if (!empty($Requiremobile)) 
        {
            if ($Requiremobile == 'Yes') 
            {
                $NEF->willRequireMobile = true;
            }else
            {
                $NEF->willRequireMobile = false;
            }
        }
        

        if ($MobileDevices >0 ) 
        {
             
            $MobileDeviceMidLayer       = new RNCPHP\NEF\MobileDeviceMidLayer();
            $MobileDeviceMidLayer->NEF  = $NEF;
            $MobileDeviceMidLayer->NEFmobile = RNCPHP\Goodwill\NEFmobile::fetch($MobileDevices);
            $MobileDeviceMidLayer->save();
        }

        if (!empty($MobileAccessories)) 
        {
            for ($i=0; $i <sizeof($MobileAccessories) ; $i++) 
            { 
                $MobileAccessMidLayer       = new RNCPHP\NEF\MobileAccessMidLayer();
                $MobileAccessMidLayer->NEF  = $NEF;
                $MobileAccessMidLayer->NEFmobileAccessories = RNCPHP\Goodwill\NEFmobileAccessories::fetch($MobileAccessories[$i]); 
                $MobileAccessMidLayer->save();
            
            } 
        }

        if (!empty($Notes)) 
        {
            $NEF->Note = $Notes; 
        }
        
        return $NEF;
    }

    function createNEFObject($postData)
    {
        $PreparerID    = $postData['PreparerID'];
        $NEFID         = (int) $postData['NEFID'];

        $callForFunction = $postData['CallForFunction'];
       
    	$NEF = RNCPHP\Goodwill\NEF::fetch($NEFID);
      
        //$NEF->PreparerName = RNCPHP\Contact::fetch( $PreparerID );
        $NEF->PreparedDate = strtotime(date('Y-m-d H:i:s')); 
        
    	/*Form 1*/ 
        if ($callForFunction == 'generalinformation') 
        {
            $this->generalinformation($NEF,$postData);
        }
        

        /*Form 2*/
        if ($callForFunction == 'email_address') 
        {
            $this->email_address($NEF,$postData);
        }
        

        /*Form 3*/
        if ($callForFunction == 'mailing_and_resource') 
        {
            $this->mailing_and_resource($NEF,$postData);
        }
        

        /*Form 4*/
        if ($callForFunction == 'workstation') 
        {
            $this->worstation($NEF,$postData);
        }
        

        /*Form 5*/
        if ($callForFunction == 'NEFSoftwares') 
        {
            $this->NEFSoftwares($NEF,$postData);
        }
         

        /*Form 6*/
        if ($callForFunction == 'printing_and_cabling') 
        {
            $this->printing_and_cabling($NEF,$postData);
        }
        

        /*Form 7*/
        if ($callForFunction == 'communication_devices') 
        {
            $this->communication_devices($NEF,$postData);
        }
        
        
        $NEF->save(RNCPHP\RNObject::SuppressAll); 
        
        if ($NEF->ID > 0) 
        { 
            return true;
        }else
        {
            return false;
        }
        
    }
}