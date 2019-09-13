<rn:meta title="Approval Submit" template="standard.php" clickstream="incident_confirm"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<?php
if ($_GET['IncidentID'] || $_GET['Action']|| $_GET['time']) {
    $IncID = base64_decode($_GET['IncidentID']);
    $Action = base64_decode( $_GET['Action']);
    $time = base64_decode($_GET['time']);

}
?>
<div class="rn_Hero">
    <div class="rn_Container">
        <h1>Feedback has been submitted!</h1>
    </div>
</div>
<br/>
<hr>
<hr>
<div class="rn_Container">
    <div class="col-md-12">
        <h3>Details:</h3><br/>
        <div class="row">
        
            <div class="col-md-2">
                    <label><b>Work Order ID:</b></label> 
            </div>
            <div class="col-md-6">
                    <?php echo $IncID; ?> <br>
            </div>
        </div>
        <div class="row">
        
            <div class="col-md-2">
                    <label><b>Action Taken:</b></label> 
            </div>
            <div class="col-md-6">
                    <?php echo $Action; ?> <br>
            </div>
        </div>
        <div class="row">
        
            <div class="col-md-2">
                    <label><b>Time:</b></label> 
            </div>
            <div class="col-md-6">
                    <?php echo date('Y-m-d H:i:s A',$time); ?> <br>
            </div>
        </div><br/>
        <div class="row">
        
            <div class="col-md-6">
                <p>Thanks for your feedback.</p>
                    <br/> <br>
            </div>
        </div>

        
    </div>
</div>

<hr>
<hr>