
<?php

$CI =& get_instance();
$CI->load->helper('goodwill'); 
$MainCategories 	= getMainCategories();
$RetailStoreMngr 	= getRetailStoreMngr();
$getSClocations     = getSClocations();
$getContact 		= getContact();
$getSClevels        = getProduct(4);
//$getContactAssets   = getContactAssets(); 
$ContactType 		= $getContact->ContactType->ID; 

if ($_GET) 
{
    $Category                    = $_GET['Category'];
    $Department                  = (int) $_GET['Department']; 
    $Procurement                 = $_GET['Procurement']; 
}else{
    $Category       = $CI->session->getSessionData("Category"); 
    $Department     = $CI->session->getSessionData("Department"); 
    $Procurement    = $CI->session->getSessionData("Procurement");  
    $Subject        = $CI->session->getSessionData("AnswerSubject");  
    $AnswerID       = $CI->session->getSessionData("AnswerID");     
    $SubCat         = $CI->session->getSessionData("SubCat");     
    $ParentCat      = $CI->session->getSessionData("ParentCat");     
}
//echo "ParentCat is:".$ParentCat;
$TextCategories = ShowTextCategories($Category);
///echo "<pre>"; print_r($getContactAssets); echo "</pre>";
/*echo "<pre>"; print_r($Department); echo "</pre>";
echo "<pre>"; print_r($Procurement); echo "</pre>";*/
?>
<rn:meta title="#rn:msg:ASK_QUESTION_HDG#" template="standard.php" login_required="true" clickstream="incident_create"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<div class="rn_Hero">
    <div class="rn_HeroInner">
        <div class="rn_HeroCopy">
            <h1>Submit a Work Order.</h1>
            
        </div><br>
        <div class="translucent">
            <strong>#rn:msg:TIPS_LBL#:</strong>
            <ul>
                <li><i class="fa fa-thumbs-up"></i> #rn:msg:INCLUDE_AS_MANY_DETAILS_AS_POSSIBLE_LBL#</li>
            </ul>
        </div>
        <br>
        <!-- <p>#rn:msg:NEED_A_QUICKER_RESPONSE_LBL# <a href="/app/social/ask#rn:session#">#rn:msg:ASK_OUR_COMMUNITY_LBL#</a></p> -->
    </div>
</div>

<div class="rn_PageContent rn_AskQuestion rn_Container"> 
    <!-- <form id="rn_QuestionSubmit" method="post" action="/ci/ajaxRequest/sendForm"> -->
    <form id="rn_QuestionSubmit" method="post" action="/cc/GWController/askForm" enctype="multipart/form-data">
        <div id="rn_ErrorLocation"></div>
        <rn:condition logged_in="false">
        <rn:widget path="input/FormInput" name="Contact.Emails.PRIMARY.Address" required="true" initial_focus="true" label_input="#rn:msg:EMAIL_ADDR_LBL#"/>
        <rn:widget path="input/FormInput" value="abc" name="Incident.Subject" required="true" label_input="#rn:msg:SUBJECT_LBL#"/>

        </rn:condition>
        <rn:condition logged_in="true">
       <!--  <rn:widget path="input/FormInput" name="Incident.Subject" required="true" initial_focus="true" label_input="#rn:msg:SUBJECT_LBL#"/> -->
       <b>Subject <span style="color:red;">*</span></b>
        <input type="text" name="Incident.Subject" value="<? echo $Subject; ?>">
        </rn:condition>
        <rn:widget path="input/FormInput" name="Incident.Threads" required="true" label_input="Description" /> 
        <!-- <rn:widget path="input/ProductCategoryInput" name="Incident.Product"/> -->
        <!-- <rn:widget path="input/ProductCategoryInput" name="Incident.Category"/> -->
        <?php 
        if ($ParentCat !=4) {
        	echo "<div><p><b>Categories:</b>  ".$TextCategories['TopCategory']." <b>(</b> ".$TextCategories['MiddleCategory']." <b>/</b> ".$TextCategories['LastCategory']." <b>)</b></p></div>"; }
        if ($ContactType == 5 && $ParentCat ==4) {
         
        ?>
        <label>SC Locations *</label>
        <select id="SClocations"  name="SClocations" required  >  
            
            <?
	            for ( $i =0; $i < sizeof($getSClocations); $i++)
	            {
	                echo '<option value="'.$getSClocations[$i]['ID'].'">'.$getSClocations[$i]['Title'].' - ('.$getSClocations[$i]['SC_HUB'].')</option>'; 
	            } 
            ?> 
        </select>
    <? } ?>

        <input type="hidden" name="Category" value='<?php echo $Category ; ?> '>
        <input type="hidden" name="Department" value='<?php echo $Department;?>'>
        
        <? if ($ParentCat != 4)
        { ?>
        <input type="hidden" name="Procurement" value='<?php echo $Procurement;?>'>
        <input type="hidden" name="AnswerID" value='<?php echo $AnswerID;?>'>
        <? }else{

         ?>
        <br>
        <br>

        <label>Category *</label>
       <select id="MiddleCategory" style="float: left;" name="MiddleCategory" required >  
            
                <?
                for ( $i =0; $i < sizeof($getSClevels); $i++)
                {
                    if ($getSClevels[$i]['ID'] == $Category ) {
                        echo '<option selected value="'.$getSClevels[$i]['ID'].'">'.$getSClevels[$i]['LookupName'].'</option>';
                    }else
                    {
                        echo '<option value="'.$getSClevels[$i]['ID'].'">'.$getSClevels[$i]['LookupName'].'</option>';
                    }
                    
                }
                ?> 
        </select> 
        <br>
        <!-- <label>Category *</label>
       <select id="MainCategories" onchange="getSecondLevel(this.value);" style="float: left;" name="MainCategories" required >  
            
                <?
             //   for ( $i =0; $i < sizeof($MainCategories); $i++)
                {
             ///       echo '<option value="'.$MainCategories[$i]['ID'].'">'.$MainCategories[$i]['LookupName'].'</option>';
                }
                ?> 
        </select> 
        <div id="selectBox" style="display: none; float: left;"> 
        	<span style="float: left;">&nbsp + &nbsp</span>
            <select id="subCategory" onchange="subProduct(this.value);" style="float: left;" name="MiddleCategory" required >   
                <option>Select Any Sub Category</option>
            </select>
        </div>
        <div id=radio>
                                
        </div><br> -->
        <? } ?>
        <!-- <label>Asset</label>
        <select id="getContactAssets" style="float: left;" name="ContactAssets" required >  
            <option value="0" >Select asset</option>
                <?
            //    for ( $i =0; $i < sizeof($getContactAssets); $i++)
                {
                    
            //        echo '<option value="'.$getContactAssets[$i]['ID'].'">'.$getContactAssets[$i]['Name'].'</option>';
                  
                }
                ?> 
        </select>  
        <br>-->
        <!-- <rn:widget path="input/FileAttachmentUpload"/> -->
       <!--  <rn:widget path="input/FormSubmit" label_button="#rn:msg:SUBMIT_YOUR_QUESTION_CMD#" on_success_url="/app/ask_confirm" error_location="rn_ErrorLocation"/> -->
       <br>
       <input type="file" name="myfile" maxlength="50"><br>
       <input type="submit" value="Submit Your Work Order"/> 
        <rn:condition content_viewed="2" searches_done="1">
        <rn:condition_else/>
        <rn:widget path="input/SmartAssistantDialog" label_prompt="#rn:msg:OFFICIAL_SSS_MIGHT_L_IMMEDIATELY_MSG#"/>
        </rn:condition>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
            
            // Initialize select2
            $("#SClocations").select2();
            $("#MiddleCategory").select2();
        }); 
function getSecondLevel(value)
    {
        document.getElementById('selectBox').style.display ='block';
        $("#subCategory").empty();
        var url="/cc/GWController/subCategories";
      
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {id: value},
                    dataType: 'json',  
                    success: function(res)  
                    {

                        if (res)
                        { 
                             //alert(res);
                             var json=JSON.stringify(res);
                             json = $.parseJSON(json);
                            //alert(json);
                            var myNode = document.getElementById("radio");
                            while (myNode.firstChild) {
                            myNode.removeChild(myNode.firstChild);
                            } 
                            for (var i=0;i<json.length;i++)  
                            {
                                $("#subCategory").append('<option value="'+json[i].ID+'">'+json[i].LookupName+'</option>');
                            } 
                        }
                    }
                 
                });
            
    }

    function subProduct(value)
    {
        var myData= $('#subCategory :selected').val();
        //alert(myData);
        var url="/cc/GWController/subCategories";
      
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {id: myData},
                    dataType: 'json',  
                    success: function(res)  
                    {

                        if (res)
                        { 
                             //alert(res);
                             var json=JSON.stringify(res);
                             json = $.parseJSON(json);
                            //alert(json);
                            var myNode = document.getElementById("radio");
                            while (myNode.firstChild) {
                            myNode.removeChild(myNode.firstChild);
                            } 
                            for (var i=0;i<json.length;i++)  
                            {
                                $('#radio').append('<input type=radio name=Category value="'+json[i].ID+'"/>'+json[i].LookupName+'</br>');
                            }
                        }
                    }
                 
                });
            
    }
</script>