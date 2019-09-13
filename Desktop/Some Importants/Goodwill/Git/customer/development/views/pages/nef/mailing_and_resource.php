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
		$getMailAndResource = getMailAndResource($NEFID);
		#print_r($getEmailAddress);  

	}
}
?>
<form method="POST" action="/cc/nefController/mailing_and_resource">
	<div id="MailingAndResource">
		<fieldset>
		<legend>Mailing And Resource:</legend>
		<label>Mailing And Resource Groups</label>
		<textarea name="MailingAndResource"  ><? echo ($callFrom=='view' && !empty($getMailAndResource['MailingList'])) ? $getMailAndResource['MailingList'] : '';?></textarea>
		<label>Resource Access Groups</label>
		<textarea name="ResourceAccessGrp"><? echo ($callFrom=='view' && !empty($getMailAndResource['ResourceAccessGroups'])) ? $getMailAndResource['ResourceAccessGroups'] : '';?></textarea>
		<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>"> 
		<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>">
		<br>
        <input type="submit" name="form3" value="Next">
		</fieldset>
	</div>
</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>