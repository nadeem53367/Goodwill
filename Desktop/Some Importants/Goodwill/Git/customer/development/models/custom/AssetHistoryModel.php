<?php

//define('Incident_Status',111);

namespace Custom\Models;
use RightNow\Connect\v1_3 as RNCPHP;

define("DMQueue",                           2);
define("Queue_IT_HELP_NewWorkOrders",       7);
define("Queue_IT_OPS_NewWorkOrders",        13);
define("Queue_IT_Procurement",              9);
define("Queue_IT_VP",                       12);

define("StatusUnreOpen",    111); 
define("StatusUnresolved",  1); 

define("IT_Supervisor",     9); //profile ID
define("IT_OPS_Manager",    11); //profile ID
define("IT_Procurement",    10); //profile ID
define("IT_VP",             14); //profile ID


class AssetHistoryModel extends \RightNow\Models\Base
{
    function __construct()
    {
        parent::__construct();
    }

    function getAssets($incidentID)
    {
        
        $Assets     = array();
        $sql        = "SELECT ID FROM Asset WHERE Asset.CustomFields.Goodwill.Incident.ID = ".$incidentID;
        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        while($result = $Query->next())
        {
           $Assets[] = array('ID' => $result['ID']);
        }

        return $Assets;  
    }
    function getAssetsCO($incidentID)
    {
        
        $Assets     = array();
        $sql        = "SELECT Asset.ID AS 'AssetID' FROM Goodwill.Assets WHERE Goodwill.Assets.Incident.ID = ".$incidentID;
        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        while($result = $Query->next())
        {
           $Assets[] = array('ID' => $result['AssetID']);
        }

        return $Assets;  
    }

    function getAssetsHisstory($incidentID,$AssetID)
    {
        $sql = "SELECT Goodwill.AssetsHistory FROM Goodwill.AssetsHistory WHERE Goodwill.AssetsHistory.Asset.ID=".$AssetID." AND Goodwill.AssetsHistory.Incident.ID=".$incidentID." AND ( AssignmentAction ='From IT Team To Tech' OR AssignmentAction = 'From Opr Manager To Tech' OR AssignmentAction = 'From Procurement To Tech' OR AssignmentAction = 'From VP To Tech' OR AssignmentAction = 'From Network Admin To Tech' OR AssignmentAction = 'From Admin Asst To Tech' OR AssignmentAction = 'From IT Programmer To Tech' ) ";

        $Query      = RNCPHP\ROQL::query($sql)->next(); 
        $result = $Query->next();

        if($result > 0)
        {
           return true;  
        }else 
        {
            return false;
        }
        
    }
} 