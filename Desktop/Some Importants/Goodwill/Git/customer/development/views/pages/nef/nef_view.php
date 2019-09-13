<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('nef');

if ($_GET['NEFID']) 
{
	$NEFID = base64_decode($_GET['NEFID']);
}
//$NEFID = 8;
$getGeneralInfo 	= getGeneralInfo($NEFID);
$getEmailAddress 	= getEmailAddress($NEFID);
$getMailAndResource = getMailAndResource($NEFID);
$getWorkstation 	= getWorkstation($NEFID);
$Software 			= Software($NEFID);
$CablingAndPrinting = CablingAndPrinting($NEFID);
$communicationDevices = communicationDevices($NEFID);

//echo $getGeneralInfo['EmplID'];
//echo "<pre>"; print_r($getGeneralInfo);

?>
<script type="text/javascript">
	var NEFID = <? echo $_GET['NEFID']; ?>
</script>
<form method="POST" action="/cc/nefController/PreviewMood">
	<input type="hidden" name="NEFID" value="<?php echo $_GET['NEFID'];?>">
	<div id="PreviewMood">
		<fieldset>
			<label>General Information:</label> 
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('generalinformation',this.value);">Edit</button>
			 <table id="getGeneralInfo" class="display" style="width:100%">
			 	<tr>
				    <th>Prepared Date </th>
				    <th>Preparer Name</th> 
				    <th>Employee's First Name</th>
				    <th>Employee's Last Name</th>
				    <th>Employee's Start Date</th>
				    <th>Employee's Department</th>
				    <th>Employee's Main Location</th>
				    <th>HR Issued Job Title</th>
				    <th>Employee GL Code(s)</th>
				    <th>Allocations</th>
				    <th>Purchase Approval Supervisor:</th> 
				</tr>
			 	<tr>
				    <td><?php echo date('Y-m-d',$getGeneralInfo['PreparedDate']);?></td>
				   	<td><?php echo $getGeneralInfo['PreparerName'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplFirstName'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplLastName'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplStartDate'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplDepartment'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplLocation'];?></td>
				   	<td><?php echo $getGeneralInfo['JobTitle'];?></td>
				   	<td><?php echo $getGeneralInfo['EmplGLcodes'];?></td>
				   	<td><?php echo $getGeneralInfo['Allocations'];?></td>
				   	<td><?php echo $getGeneralInfo['PurchaseApprovalPerson'];?></td>
				</tr>
				
				  
				</table>
				<input type="hidden" name="EmplID" value="<? echo $getGeneralInfo['EmplID'];?>">
				<input type="hidden" name="PreparedDate" value='<?php echo $getGeneralInfo['PreparedDate']?>'>
				<input type="hidden" name="PreparerName" value='<?php echo $getGeneralInfo['PreparerName']?>'>
				<input type="hidden" name="EmplFirstName" value='<?php echo $getGeneralInfo['EmplFirstName']?>'>
				<input type="hidden" name="EmplLastName" value='<?php echo $getGeneralInfo['EmplLastName']?>'>
				<input type="hidden" name="EmplStartDate" value='<?php echo $getGeneralInfo['EmplStartDate']?>'> 
				<input type="hidden" name="EmplDepartment" value='<?php echo $getGeneralInfo['EmplDepartmentID']?>'>
				<input type="hidden" name="EmplLocation" value='<?php echo $getGeneralInfo['EmplLocationID']?>'>
				<input type="hidden" name="JobTitle" value='<?php echo $getGeneralInfo['JobTitle']?>'>
				<input type="hidden" name="EmplGLcodes" value='<?php echo $getGeneralInfo['EmplGLcodes']?>'>
				<input type="hidden" name="Allocations" value='<?php echo $getGeneralInfo['Allocations']?>'>
				<input type="hidden" name="PurchaseApprovalPerson" value='<?php echo $getGeneralInfo['PurchaseApprovalPersonID']?>'> 
		</fieldset>
		<fieldset>
			<label>Email Address:</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('email_address',this.value);">Edit</button>
			<p>Email Address Required:<strong><?php echo $getEmailAddress['isRequireEmail']; ?></strong></p>
			<p>VPN Access Required:<strong><?php echo $getEmailAddress['isNeedVPN']; ?></p></strong>
			<p>Temporary Employee:<strong><?php echo $getEmailAddress['isTempEmpl']; ?></strong></p>
			<p>Previous Goodwill E-Mail Address:<strong><?php echo $getEmailAddress['havePreviousGWemail']; ?></strong></p>
			<p>Retail Store Manager:<strong><?php echo $getEmailAddress['isRetailStoreEmpl']; ?></strong></p>
			<p>Employee is:<strong><?php echo $getEmailAddress['EmpTypeIs']; ?></strong></p>

			<input type="hidden" name="isRequireEmail" value="<?php echo $getEmailAddress['isRequireEmail']; ?>">
			<input type="hidden" name="isNeedVPN" value="<?php echo $getEmailAddress['isNeedVPN']; ?>">
			<input type="hidden" name="isTempEmpl" value="<?php echo $getEmailAddress['isTempEmpl']; ?>">
			<input type="hidden" name="havePreviousGWemail" value="<?php echo $getEmailAddress['havePreviousGWemail']; ?>">
			<input type="hidden" name="isRetailStoreEmpl" value="<?php echo $getEmailAddress['isRetailStoreEmpl']; ?>">
			<input type="hidden" name="EmpTypeIs" value="<?php echo $getEmailAddress['EmpTypeIs']; ?>">
		</fieldset> 
<?php 
if(strpos($getEmailAddress['EmpTypeIs'],'Retail') !== false){} else{
?>
		<fieldset>
			<label>Mailing & Resource Groups</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('mailing_and_resource',this.value);">Edit</button><br>
			<strong>Grant the user access to the following groups:</strong>
			<span><?php echo $getMailAndResource['MailingList']; ?></span>
			<hr>
			<strong>Grant the user access to the following mailing lists:</strong>
			<span><?php echo $getMailAndResource['ResourceAccessGroups']; ?></span>

			<input type="hidden" name="MailingList" value="<?php echo $getMailAndResource['MailingList']; ?>">
			<input type="hidden" name="ResourceAccessGroups" value="<?php echo $getMailAndResource['ResourceAccessGroups']; ?>">
			
		</fieldset>

		<fieldset>
			<label>Workstation:</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('workstation',this.value);">Edit</button>
			<p>Workstation Required:<strong><?php echo $getWorkstation['Workstation']; ?></p></strong>
			<p>Configuration:<strong><?php echo $getWorkstation['WorkStationType']; ?>, Price:<strong>$<?php echo $getWorkstation['EstimatePrice']; ?></strong></p></strong>
			<p>The following workstation accessories are requested:</p>
			<?php 
			if (!empty($getWorkstation['WSAccessories'])) 
			{
				$WSAccessories = $getWorkstation['WSAccessories'];
				for ($i=0; $i <sizeof($WSAccessories); $i++) 
				{ 
			 		echo "<strong>* ".$WSAccessories[$i]['Name']."</strong>, Price: $".$WSAccessories[$i]['EstimatePrice']."</br>";
			 		$name = $WSAccessories[$i]['ID'];
			 		echo'<input type="hidden" name="WSAccessories[]" value="'.$name.'"> ';
			 	}
			}
			 //echo "<pre>"; print_r($getWorkstation);
			?>

			<input type="hidden" name="WorkstationID" value="<?php echo $getWorkstation['WorkstationID']; ?>">
			<input type="hidden" name="Workstation" value="<?php echo $getWorkstation['Workstation']; ?>">
			<input type="hidden" name="WorkStationType" value="<?php echo $getWorkstation['WorkStationType']; ?>">
			 
		</fieldset>

		<fieldset>
			<label>Softwares:</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('softwares',this.value);">Edit</button>
			<p>The following workstation accessories are requested:</p>
			<?php 
			if (!empty($Software['Softwares'])) 
			{
				$Softwares = $Software['Softwares'];
				for ($i=0; $i <sizeof($Softwares); $i++) 
				{ 
			 		echo "<strong>* ".$Softwares[$i]."</strong></br>";
			 	}
			}
			?> 
			<p>Other Software:<strong><? echo $Software['OtherSoftwares']; ?></strong></p>
			<p>The following is a list of special software purchases requested:</p>
			<?php 
			if (!empty($Software['NonSoftware'])) 
			{
				$NonSoftwares = $Software['NonSoftware'];
				for ($i=0; $i <sizeof($NonSoftwares); $i++) 
				{ 
			 		echo "<strong>* ".$NonSoftwares[$i]['Name']."</strong>, Price: $".$NonSoftwares[$i]['EstimatePrice']."</br>";
			 	}
			}
			?>
			<p>Other Special Software::<strong><? echo $Software['OtherSpecialSoftwares'];?></strong></p>
		</fieldset>

		<fieldset>
			<label>Cabling & Printing</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('cabling_and_printing',this.value);">Edit</button>
			<label>Cabling</label>
			<p>Cabling and Moving notes:<strong><?php echo $CablingAndPrinting['CablingAndPrinting']?></strong></p>

			<label>Printing</label>
			<p>Printer / Copier connections requested:<strong><?php echo $CablingAndPrinting['PrinterList']?></strong></p>
			<p>Scanner connections requested:<strong><?php echo $CablingAndPrinting['ScannerList']?></strong></p>
		</fieldset>

		<fieldset>
			<label>Communication Devices:</label>
			<label>Desk Phone</label>
			<button type="button" style="margin-left: 80%;" name="View" value="<? echo $_GET['NEFID']; ?>" onclick="changePage('communication_devices',this.value);">Edit</button>
			<p>Desk phone required:<strong><?php echo $communicationDevices['isRequireDeskPhone']; ?></strong></p>
			<p>Desk phone selected:<strong><?php echo $communicationDevices['DeskPhoneName']; ?></strong>,Price: <strong><?php echo $communicationDevices['DeskPhonePrice']; ?></strong></p>
			<p>The following are requested desk phone accessories and features:</p>
			<?php 
			if (!empty($communicationDevices['DeskPhnAccessories'])) 
			{
				$Devices = $communicationDevices['DeskPhnAccessories'];
				for ($i=0; $i <sizeof($Devices); $i++) 
				{ 
			 		echo "<strong>* ".$Devices[$i]['Name']."</strong>, Price: $".$Devices[$i]['EstimatePrice']."</br>";
			 	}
			}
			?>
			<p>Employee to use existing extension:<strong><? echo $communicationDevices['willUseExistingExtension']; ?></strong></p>
			<p>Extension:<strong><? echo $communicationDevices['ExistingExtension']; ?></strong></p>
			<p>Fax machine required:<strong><? echo $communicationDevices['willNeedFaxNumber']; ?></strong></p>
			<p>Fax service requested:<strong><? echo $communicationDevices['FaxMachine']; ?></strong></p>
			<hr>
			<label>Mobile Devices, Services and Accessories:</label>
			<p>Mobile phone required:<strong><? echo $communicationDevices['willRequireMobile']; ?></strong></p>
			<p>Mobile phone selected:<strong><? echo $communicationDevices['MobilePhoneName']; ?></strong>, Price: $<? echo $communicationDevices['MobilePhonePrice']; ?></p>
			<p>The following are requested mobile phone accessories and features:</p>
			<?php 
			if (!empty($communicationDevices['MobileAccessories'])) 
			{
				$Devices = $communicationDevices['MobileAccessories'];
				for ($i=0; $i <sizeof($Devices); $i++) 
				{ 
			 		echo "<strong>* ".$Devices[$i]['Name']."</strong>, Price: $".$Devices[$i]['EstimatePrice']."</br>";
			 	}
			}
			?>

			<p>Notes:<strong><? echo $communicationDevices['Notes']; ?></strong></p>
			<input type="hidden" name="RequireDeskPhone" value="<?php echo $communicationDevices['isRequireDeskPhone']; ?>">
			<input type="hidden" name="RequireMobile" value="<?php echo $communicationDevices['willRequireMobile']; ?>">
		</fieldset>
	<? } ?>
	</div>
	<input type="submit" name="submit">
</form>
<script type="text/javascript">
	
	function changePage(page,NEFID) 
	{
		//alert(NEFID);
		var url = "https://goodwillsocal.custhelp.com/app/nef/"+page+"?NEFID="+NEFID+"&callFrom=view";
		location.replace(url);
		//window.location = ;
		//alert('JS Alert!');
	}
</script>