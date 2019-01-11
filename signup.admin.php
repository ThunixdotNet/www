<?php
require_once( dirname(__FILE__).'/signup.lib.php' );

define( 'PHPFMG_USER', "newuser@thunix.cf" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "a31add" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'3B4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RANEQxgaHUMdkMQCpoi0MrQ6OgQgq2wVaXSY6ugggiwGUhcIFwM7aWXU1LCVmZlZ05DdB1TH2oimF2iea2gghpgDmjqwWxpR3YLNzQMVflSEWNwHAEOFzEh9UajnAAAAAElFTkSuQmCC',
			'7BFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0MdkEVbRVpZGxgdAlDFGl2BYiLIYlMg6kSQ3Rc1NWxp6MqsaUjuA6pAVgeGrA2Y5olgEQtowHRLQAPQzQ2MqG4eoPCjIsTiPgAe5cqdZczn0gAAAABJRU5ErkJggg==',
			'E261' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mooqJNLo2OISiijEAxeB6wU4KjVq1dOnUVUuR3QdUN4XV0QHNDoYA1oYANDFGB0wx1gZGNL2hIaKhQJeEBgyC8KMixOI+AEt9zRwxrfAxAAAAAElFTkSuQmCC',
			'F3FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDA1qRxQIaRFpZGximOqCIMTS6NjAEBKCKAdUxOogguS80alXY0tCVWdOQ3IemDsk8xtAQTDE0dSJY9ALdjCY2UOFHRYjFfQBC9swX+iY1kQAAAABJRU5ErkJggg==',
			'3386' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7RANYQxhCGaY6IIkFTBFpZXR0CAhAVtnK0OjaEOgggCw2hQGoztEB2X0ro1aFrQpdmZqF7D6IOqzmiRAQw+YWbG4eqPCjIsTiPgDfEcs3qL/F3gAAAABJRU5ErkJggg==',
			'73F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNZQ1hDA0MDkEVbRVpZGxgdUFS2MjS6ootNYQCpc3VAdl/UqrCloSujopDcB9QFVMfQIIKkF8gHmocqJtIAsQNZLKAB5BaGgAAUMaCbGximOgyC8KMixOI+ANLhyovUCikUAAAAAElFTkSuQmCC',
			'B262' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ7AIAhFcfAG9j44dGeQxdPo4A30CC6csrpB2rFNyk8YXn7CCyC3KfCnfOLH5BIwDFSMum8uIpFmLdSzRAymB4tBCcqPs8w5RLLyW73uI1ZzowH5vQ1z6HfbupTtYp0PRnacfvC/F/PgdwEm382td3YaZgAAAABJRU5ErkJggg==',
			'F5BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGUNDkMQCGkQaWBsdHRjQxRoC0cVCkNSBnRQaNXXp0tCVoVlI7gtoYGh0xTAPKIZpHhYx1lZMtzCGAN2MIjZQ4UdFiMV9AOWey+1Mo0I6AAAAAElFTkSuQmCC',
			'BA88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQVEn0uiIUAd2UmjUtJVZoaumZiG5D00d1DzRUFd081pFGjHEsOgNDRBpdEBz80CFHxUhFvcBAHtuzlbmq/ekAAAAAElFTkSuQmCC',
			'6C4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxkaHaYGIImJTGFtdGh1CBBBEgtoEWlwmOrowIIs1gBUEejogOy+yKhpq1ZmZmYhuy9kikgDayNcHURvK1AsNBBDzKER1Q6wWxpR3YLNzQMVflSEWNwHAExOzReeVE0xAAAAAElFTkSuQmCC',
			'DC89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlCGaY6IIkFTGFtdHR0CAhAFmsVaXBtCHQQQRNjBCoUQXJf1NJpq1aFrooKQ3IfRJ3DVHS9rA0BDehirg0BqHZgcQs2Nw9U+FERYnEfAHUVzhCfseuZAAAAAElFTkSuQmCC',
			'B081' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMRRFrZW1lbQgIRVUn0ujo6ADTC3ZSaNS0lVmhq5Yiuw9NHdQ8kUZXIInFDmxuQRGDujk0YBCEHxUhFvcBAB3yzQV11H/IAAAAAElFTkSuQmCC',
			'D2DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUMDkMQCprC2sjY6OiCrC2gVaXRtCEQTY0AWAzspaumqpUtXRYZmIbkPqG4KK6beAEwxRgcMMaBOdLeEBoiGuqK5eaDCj4oQi/sAprvMgJ1US90AAAAASUVORK5CYII=',
			'E68F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUNDkMQCGlhbGR0dHRhQxEQaWRsC0cUakNSBnRQaNS1sVejK0Cwk9wU0iGI1zxXTPCximG6BuhlFbKDCj4oQi/sA8Z3KXXTYSDMAAAAASUVORK5CYII=',
			'A922' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeklEQVR4nM2QsQ3DMAwEn4C1AQeii/QUIDYewVNIBTdQRlARTxmXFJLSBsyveAV5eBw/U/Gk3OJHQgWGtwSWNDmtohoYd26vmoUDU+cmVSsHv22MsX/2Ywt+6pTF0eIPs3PvcEz3liaKPrPTRaAzo5IsW3lAfxfmj98XmmfMqKLRd5gAAAAASUVORK5CYII=',
			'7CD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZQ1mBOABZtJW10bXR0QFFZatIg2tDIKrYFJEG1oZAVwdk90VNW7V0VWRUFJL7GB1A6gIaRJD0sjZgiok0QOxAFgtoALnFISAARQzkZoapDoMg/KgIsbgPAKJZzNL+vfIeAAAAAElFTkSuQmCC',
			'D8BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUMDkMQCprC2sjY6OiCrC2gVaXRtCEQTQ1EHdlLU0pVhS0NXhmYhuQ9NHR7zsIhhcQs2Nw9U+FERYnEfAKa8zJUZqDIjAAAAAElFTkSuQmCC',
			'112B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYHRgDGB0dHQKQxEQdWANYGwIdRND1AsUCkNy3MmtV1KqVmaFZSO4Dq2tlRDEPLDaFEdO8AEwxRgdUvaIhrKGsoYEobh6o8KMixOI+AOF5xYVFWapZAAAAAElFTkSuQmCC',
			'7E6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUMDkEVbRRoYHR0dGNDEWBvQxKaAxBhhYhA3RU0NWzp1ZWgWkvsYHYDq0MxjbQDpDUQRE8EiFtCA6ZaABixuHqDwoyLE4j4AUqXJOfrOpKkAAAAASUVORK5CYII=',
			'9172' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AIAxFy9DdgQOxuH8TWTxNHbwBeAOXntIyWaKjJtKkw8sjvEB6O0J/mk/6GATOqMmxWAJIADiGzUyZUuwYgdYk0fXtVRc91NbVx6N5pZnu5XYXth0bjIVkZtdifWJm18yZJeT5B//34jz0nanMycv9CYlsAAAAAElFTkSuQmCC',
			'1821' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mIouJOog0ujYEhKLqZQXqC4DpBTtpZdbKMCCxFNl9YHWtqHYwAs1zmIJFLABdDOgWB1Qx0RDGENbQgNCAQRB+VIRY3AcA0jzIwAMNFVMAAAAASUVORK5CYII=',
			'3370' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANYQ1hDA1qRxQKmiAD5AVMdkFW2MjQ6NAQEBCCLTQGJOjqIILlvZdSqsFVLV2ZNQ3YfSN0URpg6hHkBmGKODgwodoDcwtrAgOIWsJtBqgdB+FERYnEfACDyy+17/RnJAAAAAElFTkSuQmCC',
			'4FDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpI37poiGuoYyhjogi4WINLA2OjoEIIkxgsQaAh1EkMRYp6CIgZ00bdrUsKWrIrOmIbkvYAqm3tBQTDEGLOrAYmhuAYuhu3mgwo96EIv7AJ3My9RK68YCAAAAAElFTkSuQmCC',
			'4A4E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjAEMDQ6hgYgi4UwhjC0Ojogq2MMYW1lmIoqxjpFpNEhEC4GdtK0adNWZmZmhmYhuS8AqM61EVVvaKhoqGtooAOqW4DmoanDI4bq5oEKP+pBLO4DANejy4ToPbVqAAAAAElFTkSuQmCC',
			'381E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7RAMYQximMIYGIIkFTGFtZQhhdEBR2SrS6IguBlI3BS4GdtLKqJVhq6atDM1Cdh+qOrh5DkSIBWDRC3IzY6gjipsHKvyoCLG4DwCDMclZ4HATBgAAAABJRU5ErkJggg==',
			'7C09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMEx1QBZtZW10CGUICEARE2lwdHR0EEEWmyLSwNoQCBODuClq2qqlq6KiwpDcx+gAUhcwFVkvawNYrAFZTKQBZIcDih0BDZhuCWjA4uYBCj8qQizuAwATscxANMnEiwAAAABJRU5ErkJggg==',
			'B18E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUMDkMQCpjAGMDo6OiCrC2hlDWBtCEQVm8KArA7spNCoVVGrQleGZiG5D00d1DwGTPOwiWHRGxrAGoru5oEKPypCLO4DAAcdyOK2z/uBAAAAAElFTkSuQmCC',
			'DAAA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMLQiiwVMYQxhCGWY6oAs1srayujoEBCAIibS6NoQ6CCC5L6opdNWpq6KzJqG5D40dVAx0VDX0MDQENzmQd2CKRYagCk2UOFHRYjFfQDwH865R4QP8AAAAABJRU5ErkJggg==',
			'61ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjAGsDYwOgQgiQW0sILFRJDFGhiQxcBOioxaFbU0dGXWNCT3hUxhwNTbSpyYCFQvsluALglFd/NAhR8VIRb3AQCR1siDjlalSAAAAABJRU5ErkJggg==',
			'0A24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGRoCkMRYAxhDGB0dGpHFRKawtrI2BLQiiwW0ijQ6NARMCUByX9TSaSuzVmZFRSG5D6yuldEBVa9oqMMUxtAQFDuA6gLQ3SLS6OiAKsboINLoGhqAIjZQ4UdFiMV9AJD2zYxT1n0AAAAAAElFTkSuQmCC',
			'C0E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WEMYAlhDHRoCkMREWhlDWBsYGpHFAhpZW4FirShiDSKNrg0MUwKQ3Be1atrK1NBVUVFI7oOoY3TA1MsYGoJpBza3oIhhc/NAhR8VIRb3AQCVuM1V0409bQAAAABJRU5ErkJggg==',
			'74B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZWllDGUIdkEVbGaayNjo6BKCKhbI2BDSIIItNYXRlbXRoCEB2X9TSpUtDVy3NQnIfo4NIK5I6MGRtEA11RTMPyG5FtyMAJIbmFrAYupsHKPyoCLG4DwAG680IoYzDzAAAAABJRU5ErkJggg==',
			'7A7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MDkEVbGUMYGgIdUFS2srZiiE0RaXRodISJQdwUNW1l1tKVoVlI7mN0AKqbwoiil7VBNNQhAFVMpEEEaBqqWABQzLUBqxiqmwco/KgIsbgPAAFcypYSgkVsAAAAAElFTkSuQmCC',
			'D503' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIQ6IIkFTBFpYAhldAhAFmsVaWB0dGgQQRULYW0IaAhAcl/U0qlLlwLJLCT3BbQyNLoi1KGIoZnX6IhuxxTWVnS3hAYwhqC7eaDCj4oQi/sAM0POpHsVDboAAAAASUVORK5CYII=',
			'80A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQytrK6OjoIIKiTqTRtSEApg7spKVR01amroqamoXkPjR1UPOAYqGBKOaB7GBtCESzgzGEFU0vyM1AMRQ3D1T4URFicR8A4hLM/qeWwtgAAAAASUVORK5CYII=',
			'FCB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDGaY6IIkFNLA2ujY6BASgiIk0uDYEOoigibE2OsLEwE4KjZq2amnoqqgwJPdB1DlMxdALJtHtCECzA5tbMN08UOFHRYjFfQBIy87EigEC4AAAAABJRU5ErkJggg==',
			'23FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANYQ1hDA0MdkMREpoi0sjYwOgQgiQW0MjS6AsVEkHW3MiCrg7hp2qqwpaErQ7OQ3RfAgGEeowOmeawNmGIiDZhuCQ0FurmBEcXNAxV+VIRY3AcAi7nKCkJFVakAAAAASUVORK5CYII=',
			'0979' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA6Y6IImxBrC2MjQEBAQgiYlMEWl0aAh0EEESC2gFijU6wsTATopaunRp1tJVUWFI7gtoZQx0mMIwFVUvQ6NDANBcFDtYgKYxoNgBcgtrAwOKW8BubmBAcfNAhR8VIRb3AQD/YsvXhNFv2wAAAABJRU5ErkJggg==',
			'1AD1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGVqRxVgdGENYGx2mIouJOrC2sjYEhKLqFWl0bQiA6QU7aWXWtJWpq6KWIrsPTR1UTDQUUwybOqBYowOKmGgIUCyUITRgEIQfFSEW9wEAJavK9ihcb3IAAAAASUVORK5CYII=',
			'7933' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhDGUIdkEVbWVtZGx0dAlDERBodGgIaRJDFpgDFwKJI7otaujRr6qqlWUjuY3RgDERSB4asDQwY5ok0sGCIBTRguiWgAYubByj8qAixuA8AMdzN5ey1N+8AAAAASUVORK5CYII=',
			'B3D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDGaY6IIkFTBFpZW10CAhAFmtlaHRtCHQQQVHH0MraEABTB3ZSaNSqsKWroqZmIbkPTR1u87DagekWbG4eqPCjIsTiPgBZ/c6+pSgHzQAAAABJRU5ErkJggg==',
			'C515' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRHAIAhFoXADsg9NegtsnEYLN9BskCJOGUpMLJM7+d07PvcO6K9JsFJ+8XOyBagYvGFUKIEg2z2fKeGTJRLt7mz8Ym9nP64YjZ/ezlz17tCdsEzKkGlwcQUqeOvnRE0CN17gfx9m4ncDIaLLo3yxb4MAAAAASUVORK5CYII=',
			'AB33' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhDGUIdkMRYA0RaWRsdHQKQxESmiDQ6NAQ0iCCJBbSKtDKARRHui1o6NWzV1FVLs5Dch6YODENDsZqH1Q50twS0Yrp5oMKPihCL+wAB+M7FephSdgAAAABJRU5ErkJggg==',
			'5FCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CHVqRxQIaRBoYHQKmOqCJsTYIBAQgiQUGgMQYHUSQ3Bc2bWrY0lUrs6Yhu68VRR2yWGgIsh1gMUEUdSJTQG4JRBFjBdrLEOqIat4AhR8VIRb3AQC9E8t8qCu8WQAAAABJRU5ErkJggg==',
			'0EF5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA0MDkMRYA0QaWIEyyOpEpmCKBbSCxVwdkNwXtXRq2NLQlVFRSO6DqAOagaEXVQxmB7IYxC0MAcjuA7u5gWGqwyAIPypCLO4DABa7ydn88w+OAAAAAElFTkSuQmCC',
			'4CAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjCGgrADslgIa6NDKKNDAJIYY4hIg6Ojo4MIkhjrFJEG1oZAmDqwk6ZNm7Zq6arI0Cwk9wWgqgPD0FCgWGgginkMQHWuDehirI2uaHpB7gWah+rmgQo/6kEs7gMANRDMh2SUV2IAAAAASUVORK5CYII=',
			'9D79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoFWl0aAh0EEEXa3SEiYGdNG3qtJVZS1dFhSG5j9UVqG4Kw1RkvQwgvQFAu5DEBIBijg4MKHaA3MLawIDiFrCbGxhQ3DxQ4UdFiMV9ACd8zLIWW3OUAAAAAElFTkSuQmCC',
			'C846' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEMYQxgaHaY6IImJtLK2MrQ6BAQgiQU0igBVOToIIIs1ANUFOjoguy9q1cqwlZmZqVlI7gOpY210RDWvQaTRNTTQQQTdjkZHFDGwWxpR3YLNzQMVflSEWNwHAHOYzUVBjUrkAAAAAElFTkSuQmCC',
			'CD84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WENEQxhCGRoCkMREWkVaGR0dGpHFAhpFGl0bAlpRxBpEGh0dHaYEILkvatW0lVmhq6KikNwHUefogK7XtSEwNATTDmxuQRHD5uaBCj8qQizuAwCmhs8EJGHMdwAAAABJRU5ErkJggg==',
			'24E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYWllDHaY6IImJTGGYytrAEBCAJBbQyhDK2sDoIIKsu5XRFUkdxE3Tli5dGrpqahay+wJEWtHNY3QQDXVFMw+ophXdDhGwGKre0FBMNw9U+FERYnEfAD/Qyqr8MZyzAAAAAElFTkSuQmCC',
			'27EA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQ11DHVqRxUSmMDS6NjBMdUASC2gFiwUEIOtuZWhlbWB0EEF237RV05aGrsyahuy+AIYAJHVgyOjA6AAUCw1BdgsYoqoTAUJ0sdBQoFioI4rYQIUfFSEW9wEAEwbKLGpLu0QAAAAASUVORK5CYII=',
			'5F02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMEx1QBILaBBpYAhlCAhAE2N0dHQQQRILDBBpYIWohrsvbNrUsKWrooAQyX2tYHWNyHZAxVqR3RLQCrID6BokMZEpELcgi7EC7WWYwhgaMgjCj4oQi/sAtsvMZosFNiwAAAAASUVORK5CYII=',
			'E517' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIaGIIkFNIg0MIQwgEgUMUZMsRCGKSAa4b7QqKlLV01btTILyX1A+UaHKQytDCh6wWJTUMVEQGIBqGKsrUD3OaC6mTGEMdQRRWygwo+KEIv7AOp9zKdA/I00AAAAAElFTkSuQmCC',
			'B7D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGUIdkMQCpjA0ujY6OgQgi7UCxRoCGkRQ1bWyAsUCkNwXGrVq2tJVUUuzkNwHVBeApA5qHqMDK7p5QNMwxKaINLCiuSU0ACiG5uaBCj8qQizuAwA4/89LBb7x1QAAAABJRU5ErkJggg==',
			'3CDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDGUMdkMQCprA2ujY6OgQgq2wVaXBtCHQQQRabItLAihADO2ll1LRVS1dFZk1Ddh+qOrh52MTQ7cDmFmxuHqjwoyLE4j4AjRrMe5rkSpEAAAAASUVORK5CYII=',
			'913A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGVqRxUSmMAawNjpMdUASC2gFqmwICAhAEWMIYGh0dBBBct+0qauiVk1dmTUNyX2srijqIBCktyEwNARJTAAihqJOZAoD0C2oelkDWEMZQxlRzRug8KMixOI+AHGBycFywSvGAAAAAElFTkSuQmCC',
			'3349' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RANYQxgaHaY6IIkFTBFpZWh1CAhAVtkKUuXoIIIsNgUoGggXAztpZdSqsJWZWVFhyO4DqmMF6hZBM881NKABXcyh0QHFDrBbGlHdgs3NAxV+VIRY3AcAwFDMuPPKh68AAAAASUVORK5CYII=',
			'B742' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgNEQx0aHaY6IIkFTGFodGh1CAhAFmsFik11dBBBVdfKEOjQIILkvtCoVdNWZmatikJyH1BdAGsj0BYU8xgdWEOBpqKIsTYAbZmCIjZFBCQWgOpmkJhjaMggCD8qQizuAwC5M87T/bVB7QAAAABJRU5ErkJggg==',
			'7D9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMDkEVbRVoZHR0dGFDFGl0bAlHFpqCIQdwUNW1lZmZkaBaS+xgdRBodQlD1sjYAxdDMEwGKOaKJBTRguiWgAYubByj8qAixuA8ADr7Kl8Etk1gAAAAASUVORK5CYII=',
			'A845' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUMDkMRYA1hbGVodHZDViUwRaXSYiioW0ApUF+jo6oDkvqilK8NWZmZGRSG5D6SOtdGhQQRJb2ioSKMr0FYRFPOAdjQ6Ooig29HoEBCAIgZys8NUh0EQflSEWNwHALWGzRnVHtv+AAAAAElFTkSuQmCC',
			'4234' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsRGAMAhFScEGug/ZAO9Ck2lI4QaYDWwypdoRtdRTfvfuc7wD2mUU/pR3/CykIKDsWcIZCxXPQhoKKc+eocHeImPnV2tb29Jydn5ssDcj+V0RYNBJUu9Ch0nnYqh4XO7YKPHs/NX/nsuN3wbyG8576cF6dQAAAABJRU5ErkJggg==',
			'6474' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QMQ6AIBAEl4If4H9o7K+AQl5zJN4PzifQ8EqNFaClRm+TLSbZZHKol2P8Ka/4WYLYSEwNc4oNTLlltCIeLR1jMyN7pcZvSaXUUlNq/II6gRrfbWWKnkwMHYMYj9FFLPfsdB7YV/97MDd+O4zDzestw3HjAAAAAElFTkSuQmCC',
			'D933' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUIdkMQCprC2sjY6OgQgi7WKNDo0BDSIoIuBRRHui1q6dGnW1FVLs5DcF9DKGIikDirGgMU8FkwxLG7B5uaBCj8qQizuAwDHYc/ZwyISiAAAAABJRU5ErkJggg==',
			'B1BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUMdkMQCpjAGsDY6OgQgi7WyBrA2BDqIoKhjAKsTQXJfaNSqqKWhK7OmIbkPTR3UPAZM87CJQfUiuyUU6GJ0Nw9U+FERYnEfANEcyxx8YmJdAAAAAElFTkSuQmCC',
			'801B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIY6IImJTGEMYQhhdAhAEgtoZW1lBIqJoKgTaXSYAlcHdtLSqGkrs6atDM1Cch+aOqh5EDERNDsYpqDbAXQLml6QmxlDHVHcPFDhR0WIxX0APkrKvXdRW2wAAAAASUVORK5CYII=',
			'F06A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGVqRxQIaGEMYHR2mOqCIsbayNjgEBKCIiTS6NjA6iCC5LzRq2srUqSuzpiG5D6zO0RGmDklvYGgIhh2BaOpAbkHXC3IzI4rYQIUfFSEW9wEAfv3MWOzpzCAAAAAASUVORK5CYII=',
			'E641' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHVqRxQIaWFsZWh2mooqJNDJMdQhFE2tgCITrBTspNGpa2MrMrKXI7gtoEG1lxbBDpNE1NABDzAGbW9DEoG4ODRgE4UdFiMV9AH4PziqOQid7AAAAAElFTkSuQmCC',
			'90AF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYQhldEBWF9DK2sro6IgmJtLo2hAIEwM7adrUaStTV0WGZiG5j9UVRR0EgvSGoooJAO1gRVMHcgu6GMjN6GIDFX5UhFjcBwAHH8mxHMr2CQAAAABJRU5ErkJggg==',
			'F632' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nM2QsQ3AIAwETcEGZB+P4Eg4BdPYBRtAhvCUSToIlInEf3eyrNODDRFYqb/4MbvoGCo2jMRnr0jUsaAgO4aeCShKaPw4nYdVs9T4kWz5vlN8/UOhDCMrMHHp2ePsOC6w34ed+F0GlM6EDMd6sgAAAABJRU5ErkJggg==',
			'59A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMYQximMDQEIIkFNLC2MoQyNKKKiTQ6Ojq0IosFBog0ujYETAlAcl/YtKVLU1dFRUUhu6+VMdC1IdABWS9DK0Oja2hgaAiyHa0sIPNQ3CIyhbWVFU2MNYAxBF1soMKPihCL+wDGH88nX8x7dgAAAABJRU5ErkJggg==',
			'C92F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGUNDkMREWllbGR0dHZDVBTSKNLo2BKKKNYg0OiDEwE6KWrV0adbKzNAsJPcFNDAGOrQyoullaHSYgibWyNLoEIAqBnaLA6oYyM2soahuGajwoyLE4j4Ad+PJzH4VaYoAAAAASUVORK5CYII=',
			'A66C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWlkbXB0YEESC2gVaWAFmoDsvqil08KWTl2Zhey+gFbRVlZHRwdke0NDRRpdGwJRxIDmgcVQ7cB0S0ArppsHKvyoCLG4DwDHfMtw1BiCIgAAAABJRU5ErkJggg==',
			'7DF9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA6Y6IIu2irSyNjAEBKCKNbo2MDqIIItNQRGDuClq2srU0FVRYUjuA6oAqmOYiqyXtQEs1oAsJgIRQ7EjoAHTLQENQDcDzUNx8wCFHxUhFvcBANqIzAhkFCpQAAAAAElFTkSuQmCC',
			'283B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUMdkMREprC2sjY6OgQgiQW0ijQ6NAQ6iCDrbmVtZUCog7hp2sqwVVNXhmYhuy8ARR0YMjpgmsfagCkm0oDpltBQTDcPVPhREWJxHwCjVMvGbT84vwAAAABJRU5ErkJggg==',
			'7366' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGaY6IIu2irQyOjoEBKCIMTS6Njg6CCCLTWFoZW1gdEBxX9SqsKVTV6ZmIbkPqKKV1dERxTzWBpB5gQ4iSGIiWMQCGjDdEtCAxc0DFH5UhFjcBwD0QstjZd/I6QAAAABJRU5ErkJggg==',
			'BF3C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1DGaYGIIkFTBFpYG10CBBBFmsF8QIdWNDUMTQ6OiC7LzRqatiqqSuzkN2Hpg7FPGxi6HaguyUUyGNEc/NAhR8VIRb3AQDsac2NauEDoQAAAABJRU5ErkJggg==',
			'885F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUNDkMREprC2sjYwOiCrC2gVaXRFEwOrmwoXAztpadTKsKWZmaFZSO4DqWNoCMQwzwGLmCuaGEgvo6MjihjIzQyhqG4ZqPCjIsTiPgBYKcnVBcbU6wAAAABJRU5ErkJggg==',
			'DCAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQxmmMIY6IIkFTGFtdAhldAhAFmsVaXB0dHQQQRNjbQiEqQM7KWrptFVLV0WGZiG5D00dQiw0EMM81wY0MaBbXNH0gtwMNA/FzQMVflSEWNwHAMULznV/b9ftAAAAAElFTkSuQmCC',
			'BDB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGaY6IIkFTBFpZW10CAhAFmsVaXRtCHQQQFXX6Nro6IDsvtCoaStTQ1emZiG5D6oOq3kihMSwuAWbmwcq/KgIsbgPAKgLzwOqYoQ0AAAAAElFTkSuQmCC',
			'4B41' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37poiGMDQ6tKKIhYi0MrQ6TEUWYwwRaXSY6hCKLMY6BaguEK4X7KRp06aGrczMWorsvgCgOlY0O0JDRRpdQwNQ7Z0CtAPdLSA7MMTAbg4NGAzhRz2IxX0AVtTNTk0MYckAAAAASUVORK5CYII=',
			'FD0E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNFQximMIYGIIkFNIi0MoQyOjCgijU6OjpiiLk2BMLEwE4KjZq2MnVVZGgWkvvQ1OEVw2IHFrdgunmgwo+KEIv7AAuRzB9JPfdyAAAAAElFTkSuQmCC',
			'852A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQFUXwtAQ6CCC5L6lUVOXrlqZmTUNyX0iUxgaHVoZYeqg5gHFpjCGhqDa0egQgKpOZAorUCeqGGsAYwhraCCK2ECFHxUhFvcBAFxZy1Z99b8CAAAAAElFTkSuQmCC',
			'9F57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUNDkMREpog0sIJoJLGAVhxiU4E0kvumTZ0atjQza2UWkvtYXUXAqlFsbgWLTUEWEwDbERCALAZyC6OjowOqm4F6QxlRxAYq/KgIsbgPAItNy1U7f0upAAAAAElFTkSuQmCC',
			'3638' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RAMYQxhDGaY6IIkFTGFtZW10CAhAVtkq0sjQEOgggiw2BchDqAM7aWXUtLBVU1dNzUJ23xTRVgYs5jmgm4dFDJtbsLl5oMKPihCL+wAB8czpoVQXcgAAAABJRU5ErkJggg==',
			'AC13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMIQ6IImxBrA2OoQwOgQgiYlMEWlwDAHSSGIBrUDeFCCN5L6opdNWrZq2amkWkvvQ1IFhaChEDN08BwwxoFumoLoloJUxlDHUAcXNAxV+VIRY3AcAcMHNjzTEe8AAAAAASUVORK5CYII=',
			'8E74' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDAxoCkMREpogAyYBGZLGAVrBYK4a6RocpAUjuWxo1NWzV0lVRUUjuA6ubwuiAYV4AY2gImhijAwOGW1gbUMXAbkYTG6jwoyLE4j4AcebNysJDn8gAAAAASUVORK5CYII=',
			'A54F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUNDkMRYA0QaGFodHZDViUwBik1FFQtoFQlhCISLgZ0UtXTq0pWZmaFZSO4LaGVodG1E1RsKtNU1NBDdvEaHRnQ7WIG60cUYQ9DFBir8qAixuA8A1X7LUJmWWHAAAAAASUVORK5CYII=',
			'1D93' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUIdkMRYHURaGR0dHQKQxEQdRBpdGwIaRFD0QsQCkNy3MmvayszMqKVZSO4DqXMIgatDiGExzxFTDNMtIZhuHqjwoyLE4j4AZf3K081wjzwAAAAASUVORK5CYII=',
			'B8A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMIQ6IIkFTGFtZQhldAhAFmsVaXR0dGgQQVPH2hDQEIDkvtColWFLV0UtzUJyH5o6uHmuoQGo5oHEGgKw2BGI4haQm4Hmobh5oMKPihCL+wCyVM9BIO1ZIwAAAABJRU5ErkJggg==',
			'9C91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGVqRxUSmsDY6OjpMRRYLaBVpcG0ICEUXYwWSyO6bNnXaqpWZUUuR3cfqKtLAEBKAYgcDUC9DA6qYAFDMEU0M6hYUMaibQwMGQfhREWJxHwBzRMxf5vFIVgAAAABJRU5ErkJggg==',
			'7419' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZWhmmMEx1QBZtZZjKEMIQEIAqFsoYwugggiw2hdEViGFiEDdFLV26atqqqDAk9wFVgO1A1svaIBrqMIWhAVlMpAHsFhQ7AiBiKG4BiTGGOqC6eYDCj4oQi/sAKFDK2OaqTk0AAAAASUVORK5CYII=',
			'781A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMLSiiLaytjKEMEx1QBETaXQMYQgIQBabAlQ3hdFBBNl9USvDVk1bmTUNyX2MDijqwJC1QaTRYQpjaAiSmAhEDEVdQAOm3oAGxhDGUEcUsYEKPypCLO4DAGlJys5dCBx1AAAAAElFTkSuQmCC',
			'145C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YWllDHaYGIImxOjBMZW1gCBBBEhN1YAhlBapmQdHL6Mo6FWgCkvtWZi1dujQzMwvZfYwOIq0MDYEOqPaKhjpgiAHdAhRjQRNjdHRAdUsIQytDKAOKmwcq/KgIsbgPAG6Mx6zF4tyGAAAAAElFTkSuQmCC',
			'7331' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNZQxhDGVpRRFtFWlkbHaaiijE0OjQEhKKITQGLwvRC3BS1KmzV1FVLkd3H6ICiDgxZG8DmoYiJYBELaAC7BU0M7ObQgEEQflSEWNwHAL4CzNFx34rrAAAAAElFTkSuQmCC',
			'5FDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DGUMdkMQCGkQaWBsdHQLQxRoCHUSQxAIDUMTATgqbNjVs6arIrGnI7mvF1ItNLACLmMgUTLewguxFc/NAhR8VIRb3AQBrGcw6kn7AzgAAAABJRU5ErkJggg==',
			'55EF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHUNDkMQCGkQaWBsYHRgIiAUGiIQgiYGdFDZt6tKloStDs5Dd18rQ6IqmF5tYQKsIhpjIFNZWdHtZAxhDgG5GNW+Awo+KEIv7AOO5yVKY4EAIAAAAAElFTkSuQmCC',
			'8488' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggqKO0RVJHdhJS6OWLl0VumpqFpL7RKaItGKaJxrqimYe0I5WTDsYMPRic/NAhR8VIRb3AQC4J8vmHxEM0gAAAABJRU5ErkJggg==',
			'AF2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaYGIImxBog0MDo6AEmEmMgUkQbWhkAHFiSxgFaQikAHZPdFLZ0atmplZhay+8DqWhkdkO0NDQWKTUEVA6sLYMSwA6gKxS0gMdbQABQ3D1T4URFicR8Ai/jLD1sSajUAAAAASUVORK5CYII=',
			'105D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHUMdkMRYHRhDWIEyAUhiog6srSAxERS9Io2uU+FiYCetzJq2MjUzM2sakvtA6hwaAjH0YoqB7EAXYwxhdHREdUsIQwBDKCOKmwcq/KgIsbgPAIpgx7v+WAmDAAAAAElFTkSuQmCC',
			'019C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjAGMDo6BIggiYlMYQ1gbQh0YEESC2hlAIshuy9q6aqolZmRWcjuA6ljCIGrQ4g1oIqJTGEIYESzgzWAAcMtjA6soehuHqjwoyLE4j4AGn7IJHjHWsQAAAAASUVORK5CYII='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>