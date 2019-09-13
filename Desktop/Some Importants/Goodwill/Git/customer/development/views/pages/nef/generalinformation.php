<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');
$getWorstation 	= getWorstation();
$getContact 	= getContact();
$getDepartment 	= getDepartment();
$getAllLocations = getAllLocations();
$AllContacts 	= getContactObject(null);

#echo "<pre>"; print_r($AllContacts);
$ContactName = $getContact->LookupName;
$ContactID = $getContact->ID;
if ($_GET['NEFID']) 
{
	$NEFID = base64_decode($_GET['NEFID']);
	$Preparer 	= getNEFReportSupervisor($NEFID);
}
if (isset($_GET['callFrom'])) 
{
	$callFrom = $_GET['callFrom'];
	if ($callFrom == 'view') 
	{
		$CI->load->helper('nef'); 
		$getGeneralInfo = getGeneralInfo($NEFID);
		//print_r($getGeneralInfo); 

	}
}

if ($_GET['EmplID'])
{
	$EmplID = $_GET['EmplID'];
}

?>
<form method="POST" action="/cc/nefController/generalinformation">
<div id="generalinformation">
		<fieldset>
		<legend>GeneralInfo:</legend>
		<label>Prepared Date:</label><label><?php echo date('Y-m-d');?></label><br>
		<label>Preparer Name:</label><label><?php echo $Preparer ;?></label>
		<input type="hidden" name="PreparedDate" value="<?php echo date('Y-m-d H:i:s') ;?>">
		<input type="hidden" name="PreparerID" value="<?php echo $ContactID ;?>">
		<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>"> 
		<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>"> 
		<input type="hidden" name="EmplID" value="<?php echo $EmplID ;?>"> 
		<label>Employe First Name:</label><input type="text" name="Fname" value="<? echo $getGeneralInfo['EmplFirstName']; ?>" >
		<label>Employe Last Name:</label><input type="text" name="Lname" value="<? echo $getGeneralInfo['EmplLastName']; ?>">
		<label>Department:</label>
		<select id="Department"  name="Department" required  >  
            <?
	            for ( $i =0; $i < sizeof($getDepartment); $i++)
	            {
	            	if ($callFrom == 'view' && $getGeneralInfo['EmplDepartmentID']> 0 && ($getDepartment[$i]['ID'] == $getGeneralInfo['EmplDepartmentID']) ) 
	            	{

	            		echo '<option value="'.$getDepartment[$i]['ID'].'" selected>'.$getDepartment[$i]['LookupName'].'</option>'; 
	            	}else
	            	{
	                	echo '<option value="'.$getDepartment[$i]['ID'].'">'.$getDepartment[$i]['LookupName'].'</option>'; 
	            	}
	            } 
            ?> 
        </select>
		<label>Employe Location:</label>
		<select id="Location"  name="Location" required  >  
            <?
	            for ( $i =0; $i < sizeof($getAllLocations); $i++)
	            {
	            	if ($callFrom == 'view' && $getGeneralInfo['EmplLocationID']> 0 && ($getAllLocations[$i]['ID'] == $getGeneralInfo['EmplLocationID']) ) 
	            	{
	            		echo '<option value="'.$getAllLocations[$i]['ID'].'" selected>'.$getAllLocations[$i]['LookupName'].'</option>'; 
	            	}else
	            	{
	            		echo '<option value="'.$getAllLocations[$i]['ID'].'">'.$getAllLocations[$i]['LookupName'].'</option>'; 
	            	}
	                
	            } 
            ?> 
        </select>
		<label>Employe Department GL codes</label><input type="text" name="GLCodes" value="<? echo $getGeneralInfo['EmplGLcodes']; ?>">
		<label>Allocation:</label><input type="text" name="Allocation" value="<? echo $getGeneralInfo['Allocations']; ?>">
		<label>HR issued Job title:</label><input type="text" name="JobTitle" value="<? echo $getGeneralInfo['JobTitle']; ?>">
		<label>Employe Start Date:</label><input type="date" name="startDate" value="<? echo date('Y-m-d',$getGeneralInfo['EmplStartDate']); ?>">
		<label>Purchase Approval Person:</label>
		<select id="ApprovalPerson"  name="ApprovalPerson" required  >  
            <?
	            for ( $i =0; $i < sizeof($AllContacts); $i++)
	            {
	            	if ($callFrom == 'view' && $getGeneralInfo['PurchaseApprovalPersonID']> 0 && ($AllContacts[$i]['ID'] == $getGeneralInfo['PurchaseApprovalPersonID']) ) 
	            	{
	            		echo '<option value="'.$AllContacts[$i]['ID'].'" selected>'.$AllContacts[$i]['LookupName'].'</option>'; 
	            	}else
	            	{
	            		echo '<option value="'.$AllContacts[$i]['ID'].'">'.$AllContacts[$i]['LookupName'].'</option>'; 
	            	}
	                
	            } 
            ?> 
        </select>
        <br>
        
        <input type="submit" name="form1" value="Next">
		</fieldset>
	</div>

</form>
<!-- <script type="text/javascript">
	window.onload = function() {
  AutoLoadFields();
};

function AutoLoadFields()
{

}
</script> -->