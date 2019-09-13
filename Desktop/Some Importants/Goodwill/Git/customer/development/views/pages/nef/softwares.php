<rn:meta title="NEF" template="standard.php" login_required="false"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');

$NEFSoftwares 	= NEFSoftwares();
$NonStandardSoftwares 	= NonStandardSoftwares();
if ($_GET['NEFID']) 
{
	$NEFID = base64_decode($_GET['NEFID']);
}
#echo "<pre>"; print_r($WorkstationType);
if (isset($_GET['callFrom'])) 
{
	$callFrom = $_GET['callFrom'];
	if ($callFrom == 'view') 
	{
		$CI->load->helper('nef'); 
		$getSoftware = Software($NEFID);
		$Softwares = $getSoftware['Softwares'];
		$otherSoftwares = $getSoftware['NonSoftware'];
		// print_r($getSoftware);   

	}
}
?>
<form method="POST" action="/cc/nefController/NEFSoftwares">
	<div id="Softwares">
		<fieldset>
			<legend>Softwares:</legend>
			 <div id="WorkstationDetails" >
			 <label>Softwares:</label> 
			 <table style="width:100%;">
			 	<tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Action</th>
				</tr>
			 	<?php  
			 	
				foreach ($NEFSoftwares as $key => $value) 
				{			
					if ($callFrom == 'view' && !empty($Softwares)) 
					{
						if ( in_array($value['Item'], $Softwares)) {
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td> <input type='checkbox' checked value=".$value['ID']." name='Softwares[]'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td> <input type='checkbox' value=".$value['ID']." name='Softwares[]'></td>
						  </tr>";
						}
					}else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td> <input type='checkbox' value=".$value['ID']." name='Softwares[]'></td>
					  </tr>";
					}
				}
			 	?>
				  
				</table>
				<label>Other Department or programes not listed:</label>
				<input type="text" name="otherProgrames" value="<?echo $getSoftware['OtherSoftwares'];?>">
				<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>">
				<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>">  
				<label>Non - Standard Softwares:</label>
				<table style="width:100%; ">
				 <tr>
				    <th>Item </th>
				    <th>Description</th> 
				    <th>Estimated Price</th>
				    <th>Action</th>
				</tr>
			 	<?php 
			 	//print_r(array_column($otherSoftwares,'ID'));
				foreach ($NonStandardSoftwares as $key => $value) 
				{			
					if ($callFrom == 'view' && !empty($otherSoftwares)) 
					{
						if (in_array($value['ID'], array_column($otherSoftwares,'ID')))
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' checked value=".$value['ID']." name='NonStandardSoftwares[]'></td>
						  </tr>";
						}else
						{
							echo "<tr>
						    <td>".$value['Item']."</td>
						    <td>".$value['Description']."</td> 
						    <td>".$value['EstimatePrice']."</td>
						    <td> <input type='checkbox' value=".$value['ID']." name='NonStandardSoftwares[]'></td>
						  </tr>"; 
						}
					}
					else
					{
						echo "<tr>
					    <td>".$value['Item']."</td>
					    <td>".$value['Description']."</td> 
					    <td>".$value['EstimatePrice']."</td>
					    <td> <input type='checkbox' value=".$value['ID']." name='NonStandardSoftwares[]'></td>
					  </tr>";
					}
					
				}
			 	?>
				</table>
				<label>Other special softwares or comments:</label> 
				<input type="text" name="otherSoftware" value="<? echo($getSoftware['OtherSoftwares']) ?>">
			</div>
			<br>
        <input type="submit" name="form5" value="Next">
		</fieldset>
	</div>
</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>