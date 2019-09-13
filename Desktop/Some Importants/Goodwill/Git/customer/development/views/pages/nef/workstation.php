<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');
$getWorstation 	= getWorstation();
$getContact 	= getContact();
$getDepartment 	= getDepartment();
$getAllLocations = getAllLocations();
$AllContacts 	= getContactObject(null);
$WorkstationType 	= WorkstationType();
$WorkstationAccessorice 	= WorkstationAccessorice();
#echo "<pre>"; print_r($WorkstationType);
$ContactName = $getContact->LookupName;
$callFrom='';
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
		$agetWorkstation = getWorkstation($NEFID);
		//echo "<pre>"; print_r($agetWorkstation);   

	}
}
?>
<form method="POST" action="/cc/nefController/workstation">
	<div id="Workstation">
		<fieldset>
			<legend>Workstation:</legend>
			<label>Workstation Requirements:</label>
			<?php 
				foreach ($getWorstation as $key => $value) 
				{
					if ($callFrom == 'view' && !empty($agetWorkstation['Workstation'])) 
					{
						if ($agetWorkstation['Workstation'] == $value['Name']) 
						{
							echo "<input type='radio' name='Workstation' checked onchange='showWorkstation(this.value);' value=".$value['ID'].">".$value['Name']."</input><br>";
						}else
						{
							echo "<input type='radio' name='Workstation'  onchange='showWorkstation(this.value);' value=".$value['ID'].">".$value['Name']."</input><br>";
						}
					}else
					{
						echo "<input type='radio' name='Workstation'  onchange='showWorkstation(this.value);' value=".$value['ID'].">".$value['Name']."</input><br>";
					}
					 
				}
			 ?><br>
			 <input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>">
			 <input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>"> 
			 <label>Workstation Info:</label>
			 <input type="text" name="workstationInfo" value="<? echo($callFrom == 'view' && !empty($agetWorkstation['WorkstationInfo'])) ? $agetWorkstation['WorkstationInfo'] : '' ; ?>"> 
					
		 	<div id="WorkstationDetails" >
			 <p>Please Select Desktop or Laptop:</p> 
			 <table style="width:100%;">
			 	<tr>
				    <th>Type </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
				foreach ($WorkstationType as $key => $value) 
				{			
					if ($callFrom == 'view' && !empty($agetWorkstation['WorkStationType'])) 
					{
						if ($agetWorkstation['WorkStationType'] == $value['Type']) 
						{
							echo "<tr>
						    <td>".$value['Type']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' checked value=".$value['ID']."  name='WorkstationType'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Type']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='radio' value=".$value['ID']."  name='WorkstationType'></td>
						  </tr>";
						}
					}else
					{
						echo "<tr>
					    <td>".$value['Type']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='radio' value=".$value['ID']."  name='WorkstationType'></td>
					  </tr>";
					}
					
				}
			 	?>
				  
				</table>

				<label>Addional Workstation Accessories:</label>
				<table style="width:100%; ">
				 <tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
			 	//echo "<pre>"; print_r($WorkstationAccessorice); 
			 	$WSAccessories = $agetWorkstation['WSAccessories'];
			 	for ($i=0; $i <sizeof($WorkstationAccessorice) ; $i++) 
			 	{ 
			 		if ($callFrom == 'view' && !empty($WSAccessories)) 
			 		{
				 		if (in_array($WorkstationAccessorice[$i]['ID'], array_column($WSAccessories,'ID')))
				 		{
				 			echo "<tr>
						    <td>".$WorkstationAccessorice[$i]['Item']."</td>
						    <td>".$WorkstationAccessorice[$i]['Description']."</td> 
						    <td>".$WorkstationAccessorice[$i]['EstimatePrice']."</td>
						    <td> <input type='checkbox' checked value = ".$WorkstationAccessorice[$i]['ID']." name='WorkstationAccessories[]' ></td>
						  </tr>";
				 		}else
				 		{
				 			echo "<tr>
						    <td>".$WorkstationAccessorice[$i]['Item']."</td>
						    <td>".$WorkstationAccessorice[$i]['Description']."</td> 
						    <td>".$WorkstationAccessorice[$i]['EstimatePrice']."</td>
						    <td> <input type='checkbox'  value = ".$WorkstationAccessorice[$i]['ID']." name='WorkstationAccessories[]' ></td>
						  </tr>";
				 		}
				 	} 
			 		else
			 		{
			 			echo "<tr>
					    <td>".$WorkstationAccessorice[$i]['Item']."</td>
					    <td>".$WorkstationAccessorice[$i]['Description']."</td> 
					    <td>".$WorkstationAccessorice[$i]['EstimatePrice']."</td>
					    <td> <input type='checkbox'  value = ".$WorkstationAccessorice[$i]['ID']." name='WorkstationAccessories[]' ></td>
					  </tr>";
			 		}
			 		
			 	}
			/*	foreach ($WorkstationAccessorice as $key => $value) 
				{			
					if ($callFrom == 'view' && $agetWorkstation[$key]['ID'] == $value['ID'] ) 
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='checkbox' checked value=".$value['ID']." name='WorkstationAccessories[]' ></td>
					  </tr>";
					}else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='checkbox' value=".$value['ID']." name='WorkstationAccessories[]'></td>
					  </tr>";
					}
					
				}*/
			 	?>
				</table>
			</div>
			<br>
        <input type="submit" name="form4" value="Next">
		</fieldset>
	</div>
</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>