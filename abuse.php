<?php
if( !defined('FormmailMakerFormLoader') ){
	require_once( dirname(__FILE__).'/abuse.lib.php' );
    phpfmg_display_form();
};
function phpfmg_form( $sErr = false ){
		$style=" class='form_text' ";
?>
<div id='frmFormMailContainer'>
<form style="text-align: center;" name="frmFormMail" id="frmFormMail" target="submitToFrame" action='<?php echo PHPFMG_ADMIN_URL . '' ; ?>' method='post' enctype='multipart/form-data' onsubmit='return fmgHandler.onSubmit(this);'>
<input type='hidden' name='formmail_submit' value='Y'>
<input type='hidden' name='mod' value='ajax'>
<input type='hidden' name='func' value='submit'>
<ol class='phpfmg_form' >
<li class='field_block' id='field_0_div'><div class='col_label'>
	<label class='form_field'>Contact Name:</label></div>
	<div class='col_field'>
	<input type="text" name="field_0"  id="field_0" value="<?php  phpfmg_hsc("field_0", ""); ?>" class='text_box'>
	<div id='field_0_tip' class='instruction'></div>
	</div>
</li>
<li class='field_block' id='field_1_div'><div class='col_label'>
	<label class='form_field'>Email Address:</label></div>
	<div class='col_field'>
	<input type="text" name="field_1"  id="field_1" value="<?php  phpfmg_hsc("field_1", ""); ?>" class='text_box'>
	<div id='field_1_tip' class='instruction'></div>
	</div>
</li>
<li class='field_block' id='field_2_div'><div class='col_label'>
	<label class='form_field'>Subject:</label></div>
	<div class='col_field'>
	<input type="text" name="field_2"  id="field_2" value="<?php  phpfmg_hsc("field_2", ""); ?>" class='text_box'>
	<div id='field_2_tip' class='instruction'></div>
	</div>
</li>
<li class='field_block' id='field_3_div'><div class='col_label'>
	<label class='form_field'>Message:</label></div>
	<div class='col_field'>
	<textarea name="field_3" id="field_3" rows=4 cols=25 class='text_area'><?php  phpfmg_hsc("field_3"); ?></textarea>
	<div id='field_3_tip' class='instruction'></div>
	</div>
</li>
<li class='field_block' id='phpfmg_captcha_div'>
	<div class='col_label'><label class='form_field'>Security Code:</label></div><div class='col_field'>
	<?php phpfmg_show_captcha(); ?>
	</div>
</li>
<li>
	<div class='form_submit_block col_field'>
		<input type='submit' value='Send' class='form_button'>
		<div id='err_required' class="form_error" style='display:none;'>
			<label class='form_error_title'>Please fill in every field.</label>
		</div>
		<span id='phpfmg_processing' style='display:none;'>
			<img id='phpfmg_processing_gif' src='<?php echo PHPFMG_ADMIN_URL . '?mod=image&amp;func=processing' ;?>' border=0 alt='Processing...'> <label id='phpfmg_processing_dots'></label>
		</span>
	</div>
</li>
</ol>
</form>
<iframe name="submitToFrame" id="submitToFrame" src="javascript:false" style="position:absolute;top:-10000px;left:-10000px;"></iframe>
</div>
<!-- end of form container -->
<!-- [Your confirmation message goes here] -->
<div id='thank_you_msg' style='display:none;'>
Your report has been sent. Thank you!
</div>
<?php
    phpfmg_javascript($sErr);
}
# end of form
function phpfmg_form_css(){
    $formOnly = isset($GLOBALS['formOnly']) && true === $GLOBALS['formOnly'];
?>
<style>
ol.phpfmg_form{
    list-style-type:none;
    padding:0px;
    margin:0px;
}
.text_box, .text_area, .text_select {
    min-width:500px;
    max-width:500px;
}
.form_error_title{
    font-weight: bold;
    color: #FF0000;
}
<?php phpfmg_text_align();?>    
</style>
<?php
}
# end of css
?>