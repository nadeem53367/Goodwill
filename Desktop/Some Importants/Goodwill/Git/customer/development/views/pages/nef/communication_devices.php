<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');
$DeskPhones = DeskPhones();
$DeskPhonesFeatures = DeskPhonesFeatures();
$Mobiles = Mobiles();
$MobilesFeatures = MobilesFeatures();
#echo "<pre>"; print_r($WorkstationType);
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
		$getcommDevices = communicationDevices($NEFID);
		#echo "<pre>"; print_r($getcommDevices); echo "</pre>";  

	}
}

?>
<form method="POST" action="/cc/nefController/communication_devices">
	<div id="CommunicationDevices">
		<fieldset>
			<legend>Communication Devices:</legend>
			<label>Communication Devices:</label>
			<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>">
			<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>"> 
			<p>Will employee require a desk phone?</p>
			<input type='radio' name='isRequirePhone' <?echo ($callFrom== 'view' && $getcommDevices['isRequireDeskPhone'] == 'Yes' ? 'checked' : ''); ?> value="Yes"/>YES
			<input type='radio' name='isRequirePhone' <?echo ($callFrom== 'view' && $getcommDevices['isRequireDeskPhone'] == 'No' ? 'checked' : ''); ?>  value="No"/>NO 

				<br>
			
			<div id="CommunicationDevices" >
			 <label>Desk Phone:</label>
			 <table style="width:100%;">
			 	<tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
				foreach ($DeskPhones as $key => $value) 
				{			
					if ($callFrom == 'view' && !empty($getcommDevices['DeskPhoneName'])) 
					{
						if ($getcommDevices['DeskPhoneName'] == $value['Item']) 
						{
								echo "<tr>
						    <td>".$value['Item']."</td> 
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' checked value=".$value['ID']." name='DeskPhones'></td>
						  </tr>";
						}
						else
						{
								echo "<tr>
						    <td>".$value['Item']."</td> 
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' value=".$value['ID']." name='DeskPhones'></td>
						  </tr>";
						}
					}
					else
					{
						echo "<tr>
					    <td>".$value['Item']."</td> 
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='radio' value=".$value['ID']." name='DeskPhones'></td>
					  </tr>";
					}
				}
				
			 	?>
				  
				</table>

				<label>DeskPhones Features And Accessories:</label>
				<table style="width:100%; ">
				 <tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
				foreach ($DeskPhonesFeatures as $key => $value)  
				{			
					if ($callFrom == 'view' && !empty($getcommDevices['DeskPhnAccessories']))
					{
						if (in_array($value['Item'], array_column($getcommDevices['DeskPhnAccessories'], 'Name'))) 
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' checked value=".$value['ID']."name='DeskPhoneAccessories[]'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' value=".$value['ID']." name='DeskPhoneAccessories[]'></td>
						  </tr>";
						}
					}else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='checkbox' value=".$value['ID']." name='DeskPhoneAccessories[]'></td>
					  </tr>";
					}
					
				}
			 	?>
				</table>

				<p>Will employee be using an existing extension?</p>
				<input type='radio' name='usingExtension' <?echo ($getcommDevices['willUseExistingExtension'] == 'Yes') ? 'checked' : '' ;?> value="Yes"/>YES
				<input type='radio' name='usingExtension'<?echo ($getcommDevices['willUseExistingExtension'] == 'No') ? 'checked' : '' ;?> value="No"/>NO

				<p>What is the existing extension?</p>
				<input type="text" name="extension" value="<?echo $getcommDevices['ExistingExtension'] ;?>" >

				<p>Will employee need a dedicated fax number?</p>
				<input type='radio' name='usingFax' <?echo ($getcommDevices['willNeedFaxNumber'] == 'Yes') ? 'checked' : '' ;?> value="Yes"/>YES
				<input type='radio' name='usingFax' <?echo ($getcommDevices['willNeedFaxNumber'] == 'No') ? 'checked' : '' ;?> value="No"/>NO

				<p>Will Employe need :</p>
				 <input type="radio" name="FaxType" <?echo ($getcommDevices['FaxMachine'] == 'Fax To Email') ? 'checked' : '' ;?> value="FaxToEmail">Fax to email ($5/mo)</input><br>
				 <input type="radio" name="FaxType" <?echo ($getcommDevices['FaxMachine'] == 'Fax Machine') ? 'checked' : '' ;?> value="FaxMachine">Fax Machine ($200 one time purchase)</input><br> 
			</div>
			<div>
				<label>Mobile Devices:</label>
				<p>Will Employe require a mobile device?:</p>
				 <input type="radio" name="Requiremobile" <?echo ($getcommDevices['willRequireMobile'] == 'Yes') ? 'checked' : '' ;?> value="Yes">Yes</input><br>
				 <input type="radio" name="Requiremobile" <?echo ($getcommDevices['willRequireMobile'] == 'No') ? 'checked' : '' ;?> value="No">No</input><br> 

				<label>Mobile Devices, Services and Accessories:</label>
				<table style="width:100%; ">
				 <tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
				foreach ($Mobiles as $key => $value)  
				{			
					if ($callFrom == 'view' && !empty($getcommDevices['MobilePhoneName']) ) 
					{
						if ($getcommDevices['MobilePhoneName'] == $value['Item']) 
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' checked value=".$value['ID']." name='MobileDevices'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' value=".$value['ID']." name='MobileDevices'></td>
						  </tr>";			
						}
					}else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='radio' value=".$value['ID']." name='MobileDevices'></td>
					  </tr>";
					}
					
				}
			 	?>
				</table>

				<label>Mobile Device Accessories:</label>
				<table style="width:100%; ">
				 <tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
				foreach ($MobilesFeatures as $key => $value)  
				{			
					if ($callFrom == 'view' && !empty($getcommDevices['MobileAccessories'])) 
					{
						if (in_array($value['Item'], array_column($getcommDevices['MobileAccessories'],'Name'))) 
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' checked value=".$value['ID']." name='MobileAccessories[]'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' value=".$value['ID']." name='MobileAccessories[]'></td>
						  </tr>";
						}
					}else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='checkbox' value=".$value['ID']." name='MobileAccessories[]'></td>
					  </tr>";
					}
					
				}
			 	?>
				</table>
				<label>Notes:</label>
				<input type="text" name="Notes" value="<?echo $getcommDevices['Notes'] ;?>"> 
			</div>
			<br>
        <input type="submit" name="form4" value="Preview And Submit">
		</fieldset>
	</div>
</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>