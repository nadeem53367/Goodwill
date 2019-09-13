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
if (isset($_GET['callFrom'])) 
{
	$callFrom = $_GET['callFrom'];
	if ($callFrom == 'view') 
	{
		$CI->load->helper('nef'); 
		$CablingAndPrinting = CablingAndPrinting($NEFID);
		 #print_r($CablingAndPrinting);   

	}
}
#echo "<pre>"; print_r($WorkstationType);

?>
<form method="POST" action="/cc/nefController/printing_and_cabling">
	<div id="printing_and_cabling">
		<fieldset>
			<legend>printing_and_cabling:</legend>
			<div id="printing_and_cabling">
				<label>Printing and Cabling</label>
				<textarea name="printing_and_cabling"><?echo $CablingAndPrinting['CablingAndPrinting']; ?></textarea>
				<input type="hidden" name="NEFID" value="<?php echo $NEFID ;?>"> 
				<input type="hidden" name="callFrom" value="<?php echo $callFrom ;?>">
			</div>
			 <div id="printing_and_scanning" >
			 	<label>Printing And Scanning</label>
				 <label>Will employee need to print/scan:</label> 
				 <input type="radio" name="needPrintOrScan" <?echo ($CablingAndPrinting['needPrintScan'] == 'Yes') ? 'checked' : ''; ?> value="YES">Yes
				 <input type="radio" <?echo ($CablingAndPrinting['needPrintScan'] == 'No') ? 'checked' : ''; ?> name="needPrintOrScan" value="NO">NO

				<label>List all printers or scanners that employee need:</label>
				<input type="text" name="printList" value="<?echo $CablingAndPrinting['PrinterList']; ?>">

				<label>Please list all copiers the employee will use to scan to their email:</label> 
				<input type="text" name="scanList" value="<?echo $CablingAndPrinting['ScannerList']; ?>">
			</div>
			<br>
        <input type="submit" name="form6" value="Next">
		</fieldset>
	</div>
</form>
<?php 
if ($callFrom != 'view') 
{
	echo '<input type="button" name="back" value="Back" onclick="javascript:history.go(-1)" style="">';
}
?>