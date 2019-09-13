<rn:meta title="Feedback" template="standard.php" login_required="false" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<?php
$CI =& get_instance();
$CI->load->helper('goodwill_helper');

$IncidentID = base64_decode ($_GET['IncidentID']);
$ContactID  = base64_decode ($_GET['ContactID']);
$Action 	= $_GET['Action'];
$RecordID 	= base64_decode ($_GET['RecordID']); 
$ActionType = array('Approve','Reject','RFI' ); 

$Record 	= getApprovalRecord($RecordID);
$Incident 	= getIncident($IncidentID);
$Contact 	= getContactObject($ContactID); 
$CategoryID = $Incident->Category->ID;
if ($CategoryID) 
{
	$TextCategories 	= ShowTextCategories($CategoryID);
	$TopCategory 		= $TextCategories['TopCategory'];
	if ($TextCategories['MiddleCategory']) {
		$MiddleCategory 	= "<b> * </b>" .$TextCategories['MiddleCategory'] ."<b> / </b> ";
	}
	
	$LastCategory 		= $TextCategories['LastCategory'];
}

/*$testCat = testCat($CategoryID);
echo "<pre>"; print_r($testCat); echo "</pre>";*/ 

$SenderName 		= $Record->CreatedByAccount->LookupName;
$SenderProfile 		= $Record->CreatedByAccount->Profile->LookupName;
$SenderComments 	= $Record->SenderComments;
$ApproverComments 	= $Record->ApproverComments;
$ApproverContactID 	= $Record->ApproverContact_ID->ID;
$IncSubject 		= $Incident->Subject;
//print_r($Record->ApproverContact_ID->ID); //SenderComments
//print_r($Record->ApproverContact_ID->LookupName); //SenderComments
?>
<div class="rn_Hero">
    <div class="rn_HeroInner">
        <div class="rn_HeroCopy">
            <h1>YOUR FEEDBACK.</h1>
             
        </div>
        
    </div>
</div>
<hr>
<hr>
<div class="rn_Container">
	<div class="col-md-12">
		<div class="row">
		
			<div class="col-md-3">
					<label><b>Work Order ID:</b></label> 
			</div>
			<div class="col-md-6">
					#<?php echo $Incident->ID; ?> <br>
			</div>
		</div>
		<div class="row">
		
			<div class="col-md-3">
					<label><b>Work Order Subject:</b></label> 
			</div>
			<div class="col-md-6">
					<?php echo $IncSubject; ?> <br>
			</div>
		</div>

		<div class="row">
		
			<div class="col-md-3">
					<label><b>Date Created:</b></label> 
			</div>
			<div class="col-md-6">
					<?php echo date('Y-m-d', $Incident->CreatedTime); ?> <br> 
			</div>
		</div>

		<div class="row">
		
			<div class="col-md-3">
					<label><b>Category:</b></label> 
			</div>
			<div class="col-md-6">
					<?php 
			        echo "<div><p>".$TopCategory."".$MiddleCategory." ".$LastCategory." </p></div>"; 
			       
			        ?> <br>
			</div>
		</div>
		
		
		<div class="row">
		
			<div class="col-md-3">
					<label><b>Approver Name:</b></label> 
			</div>
			<div class="col-md-6">
					<?php echo $Contact->LookupName.' <b>(</b> '. $Contact->Title.' <b>)</b>' ;?> <br>
			</div>
		</div>

	


		<div class="row">
		
			<div class="col-md-3">
					<label><b>Requestor Name:</b></label> 
			</div>
			<div class="col-md-6">
					<?php echo $SenderName .' <b>(</b> '. $SenderProfile.' <b>)</b>' ; ?> <br>
			</div>
		</div>

		<div class="row">
		
			<div class="col-md-3">
					<label><b>Requestor Comments:</b></label> 
			</div>
			<div class="col-md-6">
					<?php echo $SenderComments; ?> <br>
			</div>
		</div>
		
	</div>
</div>

<?php if ($ApproverComments != '') { ?>
<div class="rn_Container">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-3">
					<label><b>Your Comments:</b></label> 
			</div>
			<div class="col-md-6">
				<?php echo $ApproverComments; ?> <br>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
					<label><b>Action Taken:</b></label> 
			</div>
			<div class="col-md-6">
				<?php echo $Record->Approval_Action->LookupName; ?> <br>
			</div>
		</div>
	</div>
</div>
<hr>
<hr>
		<?php } else { ?>

<div class="rn_PageContent rn_AskQuestion rn_Container"> 
	
	<form onsubmit="return confirm('Are you sure you want to proceed ?');" action="/cc/GWController/ExternalFeedback" method="post">
		
		<div class="row">
			<div class="form-group col-md-8">
				<rn:widget path="input/FormInput" name="Incident.Threads" required="true" label_input="Comments" />
			</div>
		</div>
		
		<div class="row">
			<div class="form-group col-md-8">
			    <label>Action Type:</label>
				<select name="ActionType" id="selection">
					<?php 
					for ($i=0; $i <sizeof($ActionType) ; $i++) 
					{ 
						if ($Action == $ActionType[$i]) 
						{
							echo '<option selected value='.$ActionType[$i].'>'.$ActionType[$i].'</option>';
						}else
						{
							echo '<option value='.$ActionType[$i].'>'.$ActionType[$i].'</option>';
						}
						
					}
						
					?>
					
				</select>
			</div>
		</div>
		<input type="hidden" name="IncidentId" value="<?php echo $IncidentID ?>" />
		<input type="hidden" name="ContactID" value="<?php echo $ContactID ?>" />
		<input type="hidden" name="ApproverContactID" value="<?php echo $ApproverContactID ?>" />
		<input type="hidden" name="RecordID" value="<?php echo $RecordID ?>" />
		<button type="submit" class="btn btn-default">Submit</button>
		
	</form>
	

</div>
<?php } ?>
<script type="text/javascript">
	function changeBtn(argument) 
	{ 
		$('#selection').empty()
		if (argument !='') 
		{
			$('#selection').append('<option value='+argument+' selected="selected">'+argument+'</option>');
		}
	}
</script>