<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');
$getWorstation 	= getWorstation();
$getContact 	= getContact();
$getDepartment 	= getDepartment();
$getAllLocations = getAllLocations();
$AllContacts 	= getContactObject(null);
$EmplType 		= NEFEmplType();
#echo "<pre>"; print_r($AllContacts);
$ContactName = $getContact->LookupName;
$ContactID = $getContact->ID; 

if ($_GET['NEFID']) 
{
	$NEFID = base64_decode($_GET['NEFID']); 
}
if (isset($_GET['callFrom'])) 
{
	$callFrom = $_GET['callFrom'];
	if ($callFrom == 'view') 
	{
		$CI->load->helper('nef'); 
		$getEmailAddress = getEmailAddress($NEFID);
		#print_r($getEmailAddress);  

	}
	
}
?>
<div>
<form method="POST" action="/cc/nefController/email_address">
<div id="email_address">
		<fieldset>
		<legend>Mailing:</legend>
		<input type="hidden" name="PreparedDate" value="<?php echo date('Y-m-d H:i:s') ;?>">
		<input type="hidden" name="PreparerID" value="<?php echo $ContactID ;?>">
		<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>"> 
		<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>">
		<label>This employe require email address?</label>
		
		<input type="radio" name="requireEmail" <? echo ($getEmailAddress['isRequireEmail']=='Yes' ? 'checked' : '');?> value="Yes">YES</input>
		<input type="radio" name="requireEmail" <? echo ($getEmailAddress['isRequireEmail']=='No' ? 'checked' : '');?> value="No">NO</input>

		<label>This employe is temprory employe?</label>
		<input type="radio" name="isTempEmpl" <? echo ($getEmailAddress['isTempEmpl']=='Yes' ? 'checked' : '');?> value="Yes">YES</input>
		<input type="radio" name="isTempEmpl" <? echo ($getEmailAddress['isTempEmpl']=='No' ? 'checked' : '');?> value="No">NO</input>

		<label>This employe previously have Goodwill email address?</label>
		<input type="radio" name="previousEmail" <? echo ($getEmailAddress['havePreviousGWemail']=='Yes' ? 'checked' : '');?> value="Yes">YES</input>
		<input type="radio" name="previousEmail" <? echo ($getEmailAddress['havePreviousGWemail']=='No' ? 'checked' : '');?> value="No">NO</input>

		<label>This employe need VPN access to connect to network remotely?</label>
		<input type="radio" name="needVPN" <? echo ($getEmailAddress['isNeedVPN']=='Yes' ? 'checked' : '');?> value="Yes">YES</input>
		<input type="radio" name="needVPN" <? echo ($getEmailAddress['isNeedVPN']=='No' ? 'checked' : '');?> value="No">NO</input>

		<label>This employe a retail store Manager?</label>
		<input type="radio" name="isRetailStoreMngr" <? echo ($getEmailAddress['isRetailStoreEmpl']=='Yes' ? 'checked' : '');?> value="Yes">YES</input>
		<input type="radio" name="isRetailStoreMngr" <? echo ($getEmailAddress['isRetailStoreEmpl']=='No' ? 'checked' : '');?> value="No">NO</input>

		<label>This employe is a</label>
		<?php 
			foreach ($EmplType as $key => $value) 
			{
				if ($callFrom == 'view' && $getEmailAddress['EmpTypeIs'] == $value['Name'] ) 
				{
					echo "<input type='radio' name='employeTypeIs' checked value=".$value['ID'].">".$value['Name']."</input><br>";
				}else
				{
					echo "<input type='radio' name='employeTypeIs' value=".$value['ID'].">".$value['Name']."</input><br>";	
				}
				
			}
		 ?><br>
		<br>
        <input type="submit" name="form2" value="Next"> 
          
		</fieldset>
	</div>

</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>

</div>

