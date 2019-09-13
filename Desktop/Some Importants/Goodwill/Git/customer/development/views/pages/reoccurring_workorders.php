<rn:meta title="Reoccurring Work Orders" template="standard.php" login_required="false" />
<br>
<div class="rn_Container">

	<form action="/cc/GWController/reoccurringWO" method="post" >
		<fieldset>
		<legend>Work Order Schedule:</legend>
			
		<div class="form-group row">
			
		     <label for="subject" class="col-sm-3 col-form-label">Subject:</label>
		     <div class="col-sm-3">
		      <input type="text" id="subject" name="subject">
		     </div>
		      <label for="Date" class="col-sm-3 col-form-label">Date Of First Occurrence:</label>
		     <div class="col-sm-3">
		      <input type="Date" id="Date" name="firstDate">
		     </div>
	  	</div>
		<div class="form-group row">
			 <label for="thread" class="col-sm-3 col-form-label">Summary:</label>
		     <div class="col-sm-3">
		      <textarea name="thread" cols="40" rows="3"></textarea> 
		     </div>
	  	</div>
	  	<div class="form-group row">
		    <label for="Tech" class="col-sm-3 col-form-label">Technician:</label>
		    <div class="col-sm-3">
		      <input type="text"   id="Tech" name="Tech">
		    </div>
	  	</div>
	  
	  	<div class="form-group">
	  		<span class="row">
	  		<fieldset>
		    <LEGEND>Resource Pattern:</LEGEND>  
		    </span>
		    <span class=" form-group col-sm-3">
		      	<input type="radio" id="rpattern" value="Hourly" name="rpattern" onclick="showDiv(this.value);"> <label>Hourly</label><br>
			    <input type="radio" id="rpattern" value="Daily" name="rpattern" onclick="showDiv(this.value);"> <label>Daily</label><br>
			    <input type="radio" id="rpattern" value="Weekly" name="rpattern" onclick="showDiv(this.value);"> <label>Weekly</label><br>
			    <input type="radio" id="rpattern" value="Monthly" name="rpattern" onclick="showDiv(this.value);"> <label>Monthly</label><br>
		    </span>
		    <span class="col-sm-8" id="patternside">
		    	<div class="col-sm-8" style="display: none;" id="Hourly">
		    		<input type="time" name="Htime">
		    	</div>
		    	<div class="col-sm-8" style="display: none;" id="Daily">
		    		<input type="text"   id="Tech" name="Tech">
		    	</div>
		    	<div class="col-sm-8" style="display: none;" id="Weekly">
		    		<label>Run Time:<input type="time" class="input-sm" name="Wtime"></label> <label>Run Every:<input type="number" class="input-sm" ></label><label> Weeks</label>
		    		<input type="checkbox" name="Sunday"><label>Sunday</label> <input type="checkbox" name="Monday"><label>Monday </label> <input type="checkbox" name="Tuesday"><label>Tuesday  </label> <input type="checkbox" name="Wednesday"><label>Wednesday </label><br> <input type="checkbox" name="Thursday"><label>Thursday </label> <input type="checkbox" name="Friday"><label>Friday </label> <input type="checkbox" name="Saturday"><label>Saturday </label>
		    	</div>
		    	<div class="col-sm-8"  style="display: none;" id="Monthly">
		    		<input type="text"   id="Tech" name="Tech">
		    	</div>
		    	
		    </span>
			</fieldset>
	    </div>
	  	<div class="form-group row">
		    <label  class="col-sm-12 col-form-label"><input type="checkbox" name="Enable"> Enable schedule </label>
		</div>

	  	<div class="form-group">
	  		<input type="submit" value="Confirm" style="float: left; margin-right: 10px; margin-left: 85%;">
	  	</div>
	  	</fieldset>

	</form>

	<form action="/cc/CustomAPI/CloseWorkOrder" method="POST" enctype="multipart/form-data">
		<input type="integer" name="ID">
		<input type="text" name="Notes">
		<input type="file" name="myfile">
		<input type="submit" name="submit"> 
	</form>
</div>

<script type="text/javascript">
	function showDiv(argument) 
	{
		var Hourly 	= document.getElementById("Hourly");
		var Daily 	= document.getElementById("Daily");
		var Weekly 	= document.getElementById("Weekly");
		var Monthly = document.getElementById("Monthly");

		if (argument == "Hourly") 
		{
			Daily.style.display 	= "none";
			Weekly.style.display 	= "none";
			Monthly.style.display 	= "none";
			Hourly.style.display 	= "block";
		}
		if (argument == "Daily") 
		{
			Weekly.style.display 	= "none";
			Monthly.style.display 	= "none";
			Hourly.style.display 	= "none";
			Daily.style.display 	= "block";
		}
		if (argument == "Weekly") 
		{
			Daily.style.display 	= "none";
			Monthly.style.display 	= "none";
			Hourly.style.display 	= "none";
			Weekly.style.display 	= "block";
		}
		if (argument == "Monthly") 
		{
			Daily.style.display 	= "none";
			Weekly.style.display 	= "none";
			Hourly.style.display 	= "none";
			Monthly.style.display 	= "block";
		}
	}
</script>