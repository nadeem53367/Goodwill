<rn:meta title="NEF" template="standard.php" login_required="true"  clickstream="incident_create"/>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill');
$getWorstation 	= getWorstation();
$getContact 	= getContact();
$getDepartment 	= getDepartment();
$getAllLocations = getAllLocations();
#echo "<pre>"; print_r($getDepartment);
$ContactName = $getContact->LookupName;

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<form method="POST" action="/cc/nefController/employeCreation">
<div>
	<div id="GeneralInfo">
		<fieldset>
		<legend>GeneralInfo:</legend>
		<label>Prepared Date:</label><label><?php echo date('Y-m-d');?></label><br>
		<label>Preparer Name:</label><label><?php echo $ContactName ;?></label>
		<label>Employe First Name:</label><input type="text" name="Fname">
		<label>Employe Last Name:</label><input type="text" name="Lname">
		<label>Department:</label>
		<select id="Department"  name="Department" required  >  
            <?
	            for ( $i =0; $i < sizeof($getDepartment); $i++)
	            {
	                echo '<option value="'.$getDepartment[$i]['ID'].'">'.$getDepartment[$i]['LookupName'].'</option>'; 
	            } 
            ?> 
        </select>
		<label>Employe Location:</label>
		<select id="Location"  name="Location" required  >  
            <?
	            for ( $i =0; $i < sizeof($getAllLocations); $i++)
	            {
	                echo '<option value="'.$getAllLocations[$i]['ID'].'">'.$getAllLocations[$i]['LookupName'].'</option>'; 
	            } 
            ?> 
        </select>
		<label>Employe Department GL codes</label><input type="text" name="GLCodes">
		<label>Allocation:</label><input type="text" name="Allocation">
		<label>HR issued Job title:</label><input type="text" name="JobTitle">
		<label>Employe Start Date:</label><input type="date" name="startDate">
		<label>Purchase Approval</label><input type="text" name="Approval">
		</fieldset>
	</div>
	<div id="Mailing">
		<fieldset>
			<legend>Mailing:</legend>
		
		<label>This employe require email address?</label>
		<input type="radio" name="requireEmail" value="Yes">YES</input>
		<input type="radio" name="requireEmail" value="No">NO</input>

		<label>This employe is temprory employe?</label>
		<input type="radio" name="isTempEmpl" value="Yes">YES</input>
		<input type="radio" name="isTempEmpl" value="No">NO</input>

		<label>This employe previously have Goodwill email address?</label>
		<input type="radio" name="previousEmail" value="Yes">YES</input>
		<input type="radio" name="previousEmail" value="No">NO</input>

		<label>This employe need VPN access to connect to network remotely?</label>
		<input type="radio" name="needVPN" value="Yes">YES</input>
		<input type="radio" name="needVPN" value="No">NO</input>

		<label>This employe a retail store Manager?</label>
		<input type="radio" name="isRetailStoreMngr" value="Yes">YES</input>
		<input type="radio" name="isRetailStoreMngr" value="No">NO</input>

		<label>This employe is a</label>
		<input type="radio" name="employeIs" value="">Retail Store Manager</input><br>
		<input type="radio" name="employeIs" value="">Retail Assistant Manager</input><br>
		<input type="radio" name="employeIs" value="">Retail Key Holder</input><br>
		<input type="radio" name="employeIs" value="">ADC Attendend</input><br>
		<input type="radio" name="employeIs" value="">E - Commerce Employe</input><br>  
		</fieldset>
	</div>
	<div id="MailingAndResource">
		<fieldset>
		<legend>Mailing And Resource:</legend>
		<label>Mailing And Resource Groups</label>
		<textarea name="MailingAndResource" ></textarea>
		<label>Resource Access Groups</label>
		<textarea name="ResourceAccessGrp"></textarea>
		</fieldset>
	</div>

	<div id="Workstation">
		<fieldset>
		<legend>Workstation:</legend>
		<label>Workstation Requirements:</label>
		<?php 
			foreach ($getWorstation as $key => $value) 
			{
				echo "<input type='radio' name='Workstation'  onchange='showWorkstation(this.value);' value=".$value['ID'].">".$value['Name']."</input><br>";
			}
		 ?><br>
		 <div id="WorkstationDetails" style="display: none;">
			 <p>Please Select Desktop or Laptop:</p>
			 <table style="width:100%;">
				  <tr>
				    <th>Firstname</th>
				    <th>Lastname</th> 
				    <th>Age</th>
				  </tr>
				  <tr>
				    <td>Jill</td>
				    <td>Smith</td> 
				    <td>50</td>
				  </tr>
				  <tr>
				    <td>Eve</td>
				    <td>Jackson</td> 
				    <td>94</td>
				  </tr>
				</table>

				<label>Addional Workstation Accessories:</label>
				<table style="width:100%; ">
				  <tr>
				    <th>Firstname</th>
				    <th>Lastname</th> 
				    <th>Age</th>
				  </tr>
				  <tr>
				    <td>Jill</td>
				    <td>Smith</td> 
				    <td>50</td>
				  </tr>
				  <tr>
				    <td>Eve</td>
				    <td>Jackson</td> 
				    <td>94</td>
				  </tr>
				</table>
			</div>
		</fieldset>
	</div>

	<div id="Software">
		<fieldset>
		<legend>Software:</legend>
		<label>Software:</label>
		<br>
		 <p>Check all addional departmental software:</p>
		 <table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			<label>Other Programes</label>
			<input type="text" name="otherProgrames">

			<label>Non - standard Softwares:</label>
			<table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			<label>Other Special Software:</label>
			<input type="text" name="otherSoftware">
		</fieldset>
	</div>

	<div id="MailingAndPrinting">
		<fieldset>
		<legend></legend>
		<label>Mailing And Printing:</label>
		<textarea></textarea> 
		<label>Printing And Scanning:</label>
		<p>Employe need to print/scan</p>
		<input type="radio" name="" value="Yes">Yes</input><br>
		<input type="radio" name="" value="No">No</input><br> 
		</fieldset>
	</div>

	<div id="Communication Devices">
		<fieldset>
		<legend>Communication Devices:</legend>
		<label>Communication Devices:</label>
		<br>
		 <p>Will Employe require a desk phone?:</p>
		 <input type="radio" name="" value="Yes">Yes</input><br>
		 <input type="radio" name="" value="No">No</input><br> 
		 <p>Desk Phones:</p>
		 <table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			<label>Other Programes</label>
			<input type="text" name="otherProgrames">

			<p>Desk phones features and accessories:</p>
			<table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			<p>Will Employe using existing extension?</p>
			 <input type="radio" name="useExistingExtension" value="Yes">Yes</input><br>
			 <input type="radio" name="useExistingExtension" value="No">No</input><br> 

			<label>What is an existing extension?</label>
			<input type="text" name="existingExtension">

			<p>Will Employe need a dedicated fax number?</p>
			 <input type="radio" name="dedicatedFax" value="Yes">Yes</input><br>
			 <input type="radio" name="dedicatedFax" value="No">No</input><br> 

			 <p>Will Employe need :</p>
			 <input type="radio" name="FaxType" value="FaxToEmail">Fax to email ($5/mo)</input><br>
			 <input type="radio" name="FaxType" value="FaxMachine">Fax Machine ($200 one time purchase)</input><br> 
		</fieldset>
	</div>

	<div id="Mobile Devices">
		<fieldset>
		<legend>Mobile Devices:</legend>
		<label>Mobile Devices:</label>
		<br>
		 <p>Will Employe require a mobile device?:</p>
		 <input type="radio" name="mobile" value="Yes">Yes</input><br>
		 <input type="radio" name="mobile" value="No">No</input><br> 
		 <p>Mobile Devices, Services Accessories:</p>
		 <table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			<label>Other Programes</label>
			<input type="text" name="otherProgrames">

			<p>Mobile Devices Accessories:</p>
			<table style="width:100%">
			  <tr>
			    <th>Firstname</th>
			    <th>Lastname</th> 
			    <th>Age</th>
			  </tr>
			  <tr>
			    <td>Jill</td>
			    <td>Smith</td> 
			    <td>50</td>
			  </tr>
			  <tr>
			    <td>Eve</td>
			    <td>Jackson</td> 
			    <td>94</td>
			  </tr>
			</table>
			 
		</fieldset>
	</div>
	<div>
		<fieldset>
			<label>Notes:</label>
			<textarea name="Notes"></textarea>
			<input type="submit" name="Submit" onclick="generateReport();" value="Submit">
		</fieldset>
		

	</div>


</div>
</form>

<script type="text/javascript">
	$(document).ready(function(){
            
            // Initialize select2
            $("#SClocations").select2();
            $("#MiddleCategory").select2();
        }); 

	function showWorkstation(value)
	{
		//alert(value);
		if (value == 4) 
		{
			document.getElementById("WorkstationDetails").style.display = "none";
		}else{
			document.getElementById("WorkstationDetails").style.display = "block";
		}
	}

	function generateReport()
	{
	swal({    title: "Are you sure?",
              text: "You will not be able to recover this imaginary file!",   
              type: "warning",   
              showCancelButton: true,   
              confirmButtonColor: "#DD6B55",   
              confirmButtonText: "Yes, delete it!",   
              closeOnConfirm: false }, 
              function(){   
              swal("Deleted!", "Your imaginary file has been deleted.", "success"); 
              });
	}
</script>