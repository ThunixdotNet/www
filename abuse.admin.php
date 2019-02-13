<?php
require_once( dirname(__FILE__).'/abuse.lib.php' );

define( 'PHPFMG_USER', "abuse@thunix.net" ); // must be a email address. for sending password to you.
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
			'F152' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHaY6IIkFNDAGsDYwBASgiLECxRgdRFDEgHqnMjSIILkvNGpV1NLMrFVRSO4DqQOSjQ5oeoFkKwO6eQ0BU9DFGB0dAlDFWEMZQhlDQwZB+FERYnEfAAa9y0AwZOMeAAAAAElFTkSuQmCC',
			'E0D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUIdkMQCGhhDWBsdHQJQxFhbWYGkCIqYSKMrkAxAcl9o1LSVqauilmYhuQ9NHYqYCEE7MN2Czc0DFX5UhFjcBwAy2c6h5bRojwAAAABJRU5ErkJggg==',
			'DA83' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUIdkMQCpjCGMDo6OgQgi7WytrI2BDSIoIiJNAKVNQQguS9q6bSVWaGrlmYhuQ9NHVRMNNQVi3kYYlNAelHdEhog0uiA5uaBCj8qQizuAwBqnM8Xn/TN4wAAAABJRU5ErkJggg==',
			'F9B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGUNDkMQCGlhbWRsdGkRQxEQaXUEkuhhQXQCS+0Kjli5NDV21MgvJfQENjIFAda0MKHoZQOZNQRVjAYkFMGC4xdEBVQzsZhSxgQo/KkIs7gMAAKfOPXKC2kUAAAAASUVORK5CYII=',
			'A527' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QsQ2AQAhFobgNcB/cAJOjcQSnwIINzhEsdEqv84iWGuUnFK+Alw/7ZQz+lFf8kDsFRc0NS0KGPRs1jApZMglMnHLdNaffuC7rvk3b1PiJw8wO3v5VraxAgXhvZgGJLDkycmSYkw6BfdXfg7nxOwDfRcvyKTTVMwAAAABJRU5ErkJggg==',
			'5FA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMIQ6IIkFNIg0MIQyOgSgiTE6OoBk4DAwQKSBFSgTgOS+sGlTw5auilqahey+VhR1CLHQABTzAqDqkMVEpoDEAlHcwgqxF8XNAxV+VIRY3AcAHwPN0GMD2nwAAAAASUVORK5CYII=',
			'243D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYWhlDGUMdkMREpjBMZW10dAhAEgtoZQhlaAh0EEHW3croygBUJ4LsvmlLl66aujJrGrL7AkRakdSBIaODKNBOVPNYgSai2wFkt6K7JTQU080DFX5UhFjcBwC4z8sQZNDF3QAAAABJRU5ErkJggg==',
			'EF3D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNEQx1DGUMdkMQCGkQaWBsdHQLQxBgaAh1E0MWA6kSQ3BcaNTVs1dSVWdOQ3IemDr95WMTQ3RIaItLAiObmgQo/KkIs7gMAgHLNOkpES64AAAAASUVORK5CYII=',
			'48F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37pjCGsIYGTHVAFgthbWVtYAgIQBJjDBFpdG1gdBBBEmOdAlbXIILkvmnTVoYtDV21KgrJfQEQdY3IdoSGgsxjaEV1C1hsCqoYxC0Ybm5gDA0ZDOFHPYjFfQBilMuTRyxtdgAAAABJRU5ErkJggg==',
			'5CAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMIaGIIkFNLA2OoQyOjCgiIk0ODo6oogFBog0sDYEwsTATgqbNm3V0lWRoVnI7mtFUYcQC0UVCwCKuaKpE5nC2oguxhrAGIph3gCFHxUhFvcBAP3RyzAmLqHvAAAAAElFTkSuQmCC',
			'19B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGVqRxVgdWFtZGx2mOiCJiTqINLo2BAQEoOgFijU6OogguW9l1tKlqaErs6YhuQ9oRyCSOqgYA9C8QDQxFix2YHFLCKabByr8qAixuA8AyM3KUvjtMMoAAAAASUVORK5CYII=',
			'2105' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRHAIAwDReENGAiK9ErhIkxDwwYkG6RhykDnHCmTu1idTrL/jDZNxp/0CZ8QRHVK4/nqCHXB5liELsabhwJKXpdg+Y6WzralZPk4cszedPv2yZOeHDes50dbQcunKoqKPfzgfy/qge8CQVvIaymcbJsAAAAASUVORK5CYII=',
			'0F97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUNDkMRYA0QaGB0dGkSQxESmiDSwNgSgiAW0QsQCkNwXtXRq2MrMqJVZSO4DqWMICWhlQNMLJKcwoNnB2BAQwIDhFkcHVDcD9YYyoogNVPhREWJxHwAt+8sjvlP2ygAAAABJRU5ErkJggg==',
			'77F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0NDkEVbGRpdgbQIIbEpDK2sQDoA2X1Rq6YtDV21MgvJfYwODAGsIBOQ9LICRVlBJiCJiQBFgWIByGIBYFGgCQTEBir8qAixuA8A5/nK1kyB4VAAAAAASUVORK5CYII=',
			'2160' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGVqRxUSmMAYwOjpMdUASC2hlDWBtcAgIQNbdygAUY3QQQXbftFVRS6euzJqG7D6gHayOjjB1YMjoANIbiCLG2gASC0CxAyiP4ZbQUNZQdDcPVPhREWJxHwBFackYlJahVAAAAABJRU5ErkJggg==',
			'6978' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZWgICAhAEgtoEWl0aAh0EEEWawCKNTrA1IGdFBm1dGnW0lVTs5DcFzKFMdBhCgOqea0MQJ2MqOa1sjQ6OqCKgdzC2oCqF+zmBgYUNw9U+FERYnEfAJtEzRM1OoL1AAAAAElFTkSuQmCC',
			'CE84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WENEQxlCGRoCkMREWkUaGB0dGpHFAhpFGlgbAlpRxBrA6qYEILkvatXUsFWhq6KikNwHUefogK6XtSEwNATTDmxuQRHD5uaBCj8qQizuAwDuiM2jcyLlCAAAAABJRU5ErkJggg==',
			'00F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA1qRxVgDGENYGximIouJTGFtBYqFIosFtIo0ujYwwPSCnRS1dNrK1NBVS5Hdh6YOpxjUDmxuQREDuxnoloBBEH5UhFjcBwAh48qTb0HaGAAAAABJRU5ErkJggg==',
			'684F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUNDkMREprC2MrQ6OiCrC2gRaXSYiibWAFQXCBcDOykyamXYyszM0Cwk94UAzWNtRNPbKtLoGhqIIeaApg7sFjQxqJtRxAYq/KgIsbgPANY4ywmw5yzvAAAAAElFTkSuQmCC',
			'39CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCHUNDkMQCprC2MjoEOqCobBVpdG0QRBWbAhJjhImBnbQyaunS1FUrQ7OQ3TeFMRBJHdQ8hkZMMRYMO7C5BepmVL0DFH5UhFjcBwAZu8mPc4I00gAAAABJRU5ErkJggg==',
			'E7A3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMIQ6IIkFNDA0OoQyOgSgiTk6OjSIoIq1sgLJACT3hUatmrZ0VdTSLCT3AeUDkNRBxRgdWEMD0MxjbQCpQxUTAYoForglNAQkFoDi5oEKPypCLO4DAE94zskcm8o0AAAAAElFTkSuQmCC',
			'07B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGaY6IImxBjA0ujY6BAQgiYlMAYo1BDqIIIkFtDK0siLUgZ0UtXTVtKWhq6ZmIbkPqC6AFc28gFZGB1Y080SmsDagi7EGiDSg62UEqmBFc/NAhR8VIRb3AQDGIcyLWw1K1AAAAABJRU5ErkJggg==',
			'719B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUMdkEVbGQMYHR0dAlDEWANYGwIdRJDFpjCAxQKQ3Re1KmplZmRoFpL7GB2AdoQEopjH2gAUQzMPyA5gRBMLAImhuSWggTUUw80DFH5UhFjcBwCmSsirRUkFjQAAAABJRU5ErkJggg==',
			'691C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDCECCCJBbQItLoGMLowIIs1iDS6DCF0QHZfZFRS5dmTVuZhey+kCmMgUjqIHpbGRoxxVjAYsh2gN0yBdUtIDczhjqguHmgwo+KEIv7APkGy0sLSVsmAAAAAElFTkSuQmCC',
			'B561' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMRRFrFWlgbXAIRVMXwtoA1wt2UmjU1KVLp65aiuy+gCkMja6ODqh2tALFQCSqHZhiU1hbGdH0hgYwhgDdHBowCMKPihCL+wAEBs3FJNZm/wAAAABJRU5ErkJggg==',
			'EFEB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATUlEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHUMdkMQCGkQaWBsYHQKwiIngVgd2UmjU1LCloStDs5DcR6p5eOyAuhkohubmgQo/KkIs7gMAzNXL2UVo758AAAAASUVORK5CYII=',
			'2BC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQxhCHUIdkMREpoi0MjoEOgQgiQW0ijS6Ngg0iCDrbhVpZQXJIbtv2tSwpatWLc1Cdl8AijowZHQAmceAYh5rA6YdIg2YbgkNxXTzQIUfFSEW9wEAnUDMi4E+pBwAAAAASUVORK5CYII=',
			'3209' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7RAMYQximMEx1QBILmMLayhDKEBCArLJVpNHR0dFBBFlsCkOja0MgTAzspJVRq5YuXRUVFYbsvikMU1gbAqai6G1lCACKNaCKMTowOjqg2AF0SwO6W0QDREMd0Nw8UOFHRYjFfQBU/8uCqGVKHAAAAABJRU5ErkJggg==',
			'9F5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUMDkMREpog0sDYwOiCrC2jFITYVLgZ20rSpU8OWZmaGZiG5j9VVBEgGouhlaMUUEwDbgSoGcgujoyOKGGsAUG8oI4qbByr8qAixuA8A/WjJho2W8hAAAAAASUVORK5CYII=',
			'766B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkEVbWVsZHR0dAlDERBpZGxwdRJDFpog0sDYwwtRB3BQ1LWzp1JWhWUjuY3QQbWVFM4+1QaTRtSEQxTwRLGIBDZhuCWjA4uYBCj8qQizuAwBOc8rh+GXEWAAAAABJRU5ErkJggg==',
			'DBB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGVqRxQKmiLSyNjpMRRFrFWl0bQgIRRMDqYPpBTspaunUsKWhq5Yiuw9NHbJ5hMWmYOqFujk0YBCEHxUhFvcBAK32zxANYpv3AAAAAElFTkSuQmCC',
			'271F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIaGIImJTGFodAhhdEBWF9DK0OiIJsbQCoRT4GIQN00DwZWhWcjuCwDCKah6GR2AfDQxViBEFxMBQnSx0FCRBsZQR1S3DFD4URFicR8AxEDIdp5oglcAAAAASUVORK5CYII=',
			'9F9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUNDkMREpog0MDo6OiCrC2gVaWBtCMQnBnbStKlTw1ZmRoZmIbmP1VWkgSEEVS8DUC8DmnkCQDFGNDFsbmENAOoNZUQ1b4DCj4oQi/sAsh/JLHYl18IAAAAASUVORK5CYII=',
			'DEDD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGUMdkMQCpog0sDY6OgQgi7UCxRoCHURwi4GdFLV0atjSVZFZ05DcR4Re3GJY3ILNzQMVflSEWNwHAKFizVh9TwSKAAAAAElFTkSuQmCC',
			'9F74' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ11DAxoCkMREpogAyYBGZLGAVrBYK4ZYo8OUACT3TZs6NWzV0lVRUUjuY3UFqpvC6ICslwGkN4AxNARJTAAoxujAgOEW1gZUMdYATLGBCj8qQizuAwBiZM2fhhaKUAAAAABJRU5ErkJggg==',
			'9317' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANYQximMIaGIImJTBFpZQgB0khiAa0MjY6YYq0MU4A0kvumTV0VtmraqpVZSO5jdQWra0WxGWiewxSQbgQUgIgFMKC7ZQqjA7qbGUMdUcQGKvyoCLG4DwDlGMrwZVHhOwAAAABJRU5ErkJggg==',
			'1917' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQximMIaGIImxOrC2MoQwNIggiYk6iDQ6ookxAsUcpjA0BCC5b2XW0qVZ04AUkvuAdgQC1bWi2ssA0jsFVYwFJBaAKgZ0yxSgamS3hDCGMIY6oogNVPhREWJxHwD7/siyYXw2RAAAAABJRU5ErkJggg==',
			'DED8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGaY6IIkFTBFpYG10CAhAFmsFijUEOohgiAXA1IGdFLV0atjSVVFTs5Dch6aOgHloYljcgs3NAxV+VIRY3AcAIfXOioZ7rn0AAAAASUVORK5CYII=',
			'177F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA0NDkMRYHRgaHRoCHZDViWIRY3RgaGVodISJgZ20MmvVtFVLV4ZmIbkPqC6AYQojml6QKLoYK0QcRUykASSK4pYQTLGBCj8qQizuAwDvO8a5dD0LpAAAAABJRU5ErkJggg==',
			'5BC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCHaY6IIkFNIi0MjoEBASgijW6Ngg6iCCJBQaItLI2MMDUgZ0UNm1q2NJVq6ZmIbuvFUUdTAxoHiOKeQGtmHaITMF0C2sAppsHKvyoCLG4DwARFczDMjPWawAAAABJRU5ErkJggg==',
			'9803' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIQ6IImJTGFtZQhldAhAEgtoFWl0dHRoEEERY21lbQhoCEBy37SpK8OWropamoXkPlZXFHUQCDTPFSiCbJ4AFjuwuQWbmwcq/KgIsbgPAKMBzINB7vefAAAAAElFTkSuQmCC',
			'6280' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gRaXRtCAgIQBZrYGh0dHR0EEFyX2TUqqWrQldmTUNyX8gUhimMCHUQva0MAawNgWhijA6saHYA3dKA7hbWANFQBzQ3D1T4URFicR8ACIvMEh8Xdc4AAAAASUVORK5CYII=',
			'8FB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7WANEQ11DGaY6IImJTBFpYG10CAhAEgtoBYo1BDqI4FYHdtLSqKlhS0NXTc1Cch+x5hFhB9TNQDE0Nw9U+FERYnEfAKlyzV+mY8KbAAAAAElFTkSuQmCC',
			'A19B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxgDGB0dHQKQxESmsAawNgQ6iCCJBbQygMUCkNwXtXRV1MrMyNAsJPeB1DGEBKKYFxoKFMNiHiM2MTS3BLSyhqK7eaDCj4oQi/sAaB/JdbkYSI0AAAAASUVORK5CYII=',
			'0621' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mIouJTBFpZG0ICEUWC2gVAZPI7otaOi1s1cqspcjuC2gVbWVoRbUDqLfRYQqqGMgOhwAsbnFAFQO5mTU0IDRgEIQfFSEW9wEAfkTK5QtOmK0AAAAASUVORK5CYII=',
			'3536' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQxlDGaY6IIkFTBFpYG10CAhAVtkqAiQDHQSQxaaIhDA0Ojogu29l1NSlq6auTM1Cdt8UhkaHRkc084BiQPNEUO3AEAuYwtqK7hbRAMYQdDcPVPhREWJxHwCuwMypBIcJeAAAAABJRU5ErkJggg==',
			'1864' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGRoCkMRYHVhbGR0dGpHFRB1EGl0bHFoDUPSytrI2MEwJQHLfyqyVYUunroqKQnIfWJ2jowOqXpB5gaEhGGIBDeh2AN2CIiYagunmgQo/KkIs7gMAhrfK/CzBCWwAAAAASUVORK5CYII=',
			'989B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iKCIsbayAsUCkNw3berKsJWZkaFZSO5jdWVtZQgJRDGPAWieA5p5AkAxRzQxbG7B5uaBCj8qQizuAwDITssEUwJrEwAAAABJRU5ErkJggg==',
			'B6B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaWRtCHQQQFEn0sDa6OiA7L7QqGlhS0NXpmYhuS9giijQPEcM81yB5okQEsPiFmxuHqjwoyLE4j4AOlTN7ga1Fu8AAAAASUVORK5CYII=',
			'72EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMDkEVbWVtZGxgdUFS2ijS6ootNYUAWg7gpatXSpaErQ7OQ3AdUMQXdPNYGhgB0MREgH10sAKgSU0w01BXdzQMUflSEWNwHABodyPUcVJ3GAAAAAElFTkSuQmCC',
			'7E97' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGUNDkEVbRRoYHR0aRNDEWBsCUMWmQMQCkN0XNTVsZWbUyiwk9zE6AHWFBLQi28sKNilgCrKYCBAyNgQEIIuBbGR0dHRAFQO7GUVsoMKPihCL+wDklssFmdWEOwAAAABJRU5ErkJggg==',
			'440F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37pjC0MkxhDA1BFgthmMoQyuiArI4xBCji6IgixjqF0ZW1IRAmBnbStGlLly5dFRmaheS+gCkirUjqwDA0VDTUFU0M5BZ0O8DuQ3ML1M2oYgMVftSDWNwHAPTvyNZ7zm8zAAAAAElFTkSuQmCC',
			'34C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RAMYWhlCHUIdkMQCpjBMZXQIdAhAVglUxdog0CCCLDaF0ZUVpB7JfSujli5dumrV0ixk900RaUVSBzVPNNQVSIug2tGKbgfQLa3obsHm5oEKPypCLO4DAAuZzB0lWiQvAAAAAElFTkSuQmCC',
			'1238' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGaY6IImxOrC2sjY6BAQgiYk6iDQ6NAQ6iKDoZWh0QKgDO2ll1qqlq6aumpqF5D6guikMaOYBxQIYMMwDimKIsTZguCVENNQRzc0DFX5UhFjcBwC8z8o6e7NA2QAAAABJRU5ErkJggg==',
			'9F61' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGVqRxUSmiDQwOjpMRRYLaBVpYG0AqsQQg+sFO2na1KlhS6euWorsPlZXoDpHBxQ7GMB6A1DEBLCIQd2CIsYaINLAEMoQGjAIwo+KEIv7AKMhy7vZmmZiAAAAAElFTkSuQmCC',
			'D71B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIY6IIkFTGFodAhhdAhAFmtlaHQEiomgirUyTIGrAzspaumqaaumrQzNQnIfUF0AkjqoGKMDSAzVPNYGDLEpIg3oekMDRBoYQx1R3DxQ4UdFiMV9AK1fzIIAqTpVAAAAAElFTkSuQmCC',
			'8D6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gVaXRtcAgIQFUHFGN0EEFy39KoaStTp67MmobkPrA6R0eYOiTzAkNDMMVQ1EHcgqoX4mZGFLGBCj8qQizuAwDXU8yXXzpOTgAAAABJRU5ErkJggg==',
			'8A01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGWYiiwW0MrayujoEIqqTqTRFSiD7L6lUdNWpq6KWorsPjR1UPNEQzHFRBodHR1a0e0A2ooixhoAFJvCEBowCMKPihCL+wDO8s0Ouu10XgAAAABJRU5ErkJggg==',
			'D41E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYWhmmMIYGIIkFTGGYyhDC6ICsLqCVIZQRQ4zRFagXJgZ2UtTSpUtXTVsZmoXkvoBWkVYkdVAx0VAHDDEGTHVTMMVAbmYMdURx80CFHxUhFvcBAEtlyshnNF60AAAAAElFTkSuQmCC',
			'B548' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHaY6IIkFTBFpYGh1CAhAFmsFik11dBBBVRfCEAhXB3ZSaNTUpSszs6ZmIbkvYApDo2sjunlAsdBAVPNaRRodGtHtYAWqRNUbGsAYgu7mgQo/KkIs7gMApzrPAPZcqtYAAAAASUVORK5CYII=',
			'82E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoFWl0bWB0EEBRxwAWQ3bf0qhVS5eGrkzNQnIfUN0U1gZGNPMYAoBiDiIoYowO6GJAtzSgu4U1QDTUFc3NAxV+VIRY3AcAAHrLO3bNnVcAAAAASUVORK5CYII=',
			'755A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHVpRRFtFGlgbGKY6YIoFBCCLTREJYZ3K6CCC7L6oqUuXZmZmTUNyH6MDQ6NDQyBMHRgCzQKJhYYgiYk0iDS6oqkLaGBtZXR0RBNjDGEIZUQRG6jwoyLE4j4AGL/LSQRkim4AAAAASUVORK5CYII=',
			'47B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37poiGuoYyhoYgi4UwNLo2OjSIIIkxgsQaAlDEWKcwtLIC1QUguW/atFXTloauWpmF5L6AKQwBQHWtyPaGhjI6sIJkUNzC2gAUC0AVE2lgbXR0wBAD6h8U4Uc9iMV9ALB6zFXVdFMnAAAAAElFTkSuQmCC',
			'50A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM3QMQ6AIAyF4XboDTgQDu6PBEx09x514AbKHeSU6laioya22xcS/pTqbZT+tJ/0pUiglROMQTlSYk+NSeauayzALb2G3pu+oZR9ruM02b58vYM6+/NlqTVkyaLBW3MrR1HA9gkIp23+B/d7cR/6DrSdzBO2IUYPAAAAAElFTkSuQmCC',
			'57E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6iCCJBQYwtLIixMBOCpu2atrS0FVRYcjua2UIYG1gmIqsl6GV0QEo1oAsFgA0DSiGYofIFBGQGIpbWAOAYmhuHqjwoyLE4j4AmHPLfGhT9RQAAAAASUVORK5CYII=',
			'6D7B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA0MdkMREpoi0MjQEOgQgiQW0iDQ6AMVEkMUagGKNjjB1YCdFRk1bmbV0ZWgWkvtCpgDVTWFENa8VKBbAiGoeUMzRAVUM5BbWBlS9YDc3MKK4eaDCj4oQi/sAI9DMuukE9a0AAAAASUVORK5CYII=',
			'C647' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQxgaHUNDkMREWllbGVodGkSQxAIaRRoZpqKJgXiBDkAa4b6oVdPCVmZmrcxCcl9Ag2gra6NDKwOq3kbX0IApDGh2ODQ6BDCgu6XR0QGLm1HEBir8qAixuA8AhQnNF/54iuIAAAAASUVORK5CYII=',
			'D4EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYWllDHaYGIIkFTGGYytrAECCCLNbKEMrawOjAgiLG6AoSQ3Zf1FIgCF2Zhey+gFaRViR1UDHRUFcMMYZWDDumgMRQ3YLNzQMVflSEWNwHAKEXy8OxdmEIAAAAAElFTkSuQmCC',
			'12E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaY6IImxOrC2sjYwBAQgiYk6iDS6AlWLoOhlQBYDO2ll1qqlS0NXRYUhuQ+obgrQvKloegOAYg2oYowOQDE0O1gbMNwSIhrqiubmgQo/KkIs7gMASZfIT2qs2jIAAAAASUVORK5CYII=',
			'8C7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDA1qRxUSmsDY6NARMdUASC2gVaQCKBQSgqBNpYGh0dBBBct/SqGlAYmXWNCT3gdVNYYSpg5vHALQ7BE3M0QFVHcgtrg2oYmA3o4kNVPhREWJxHwAAWcxuPXMklgAAAABJRU5ErkJggg==',
			'C521' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WENEQxlCGVqRxURaRRoYHR2mIosFNIo0sDYEhKKINYiEAEmYXrCTolZNXbpqZdZSZPcFNDA0OrSi2gEWm4Im1ijS6BCA7hbWVkYHVDHWEMYQ1tCA0IBBEH5UhFjcBwAy0sw4WrqJcgAAAABJRU5ErkJggg==',
			'95C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxlCHVqRxUSmiDQwOgRMRRYLaBVpYG0QCEUTC2FtYIDpBTtp2tSpS5euWrUU2X2srgyNrgh1ENiKKSbQKgIUE0BzC2sr0C0oYqwBjCFAN4cGDILwoyLE4j4AzazL4ocIyxoAAAAASUVORK5CYII=',
			'04F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDA6Y6IImxBjBMZW1gCAhAEhOZwhDKClQtgCQW0MroChJDdl/UUiAIXZmaheS+gFaRVqA6FPMCWkVDXYF6RVDtAKlDEQO6pRXdLWA3NzCguHmgwo+KEIv7AI8nyiFBRONoAAAAAElFTkSuQmCC',
			'664D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUMdkMREprC2MrQ6OgQgiQW0iDQyTHV0EEEWawDyAuFiYCdFRk0LW5mZmTUNyX0hU0RbWRvR9LaKNLqGBmKIOaCpA7ulEdUt2Nw8UOFHRYjFfQBA4cxWv33QTgAAAABJRU5ErkJggg==',
			'B7EA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHVqRxQKmMDS6NjBMdUAWawWLBQSgqmtlbWB0EEFyX2jUqmlLQ1dmTUNyH1BdAJI6qHmMDkCx0BAUMdYGDHVTRDDEQgOAYqGOKGIDFX5UhFjcBwC2isxJIZwHMgAAAABJRU5ErkJggg==',
			'79AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMIY6IIu2srYyhDI6BKCIiTQ6Ojo6iCCLTRFpdG0IhIlB3BS1dGnqqsisaUjuY3RgDERSB4asDQyNrqGoYiINLI3o6gIaWFtZgWIBKGKMIUAxVDcPUPhREWJxHwCF+Mv4IvVbMQAAAABJRU5ErkJggg==',
			'EAEF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHUNDkMQCGhhDWBsYHRhQxFhbMcVEGl0RYmAnhUZNW5kaujI0C8l9aOqgYqKhmGLY1GGKhYYAxUIdUcQGKvyoCLG4DwBiIsroECbW4wAAAABJRU5ErkJggg==',
			'E62B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMdkMQCGlhbGR0dHQJQxEQaWRsCHURQxYBkIEwd2EmhUdPCVq3MDM1Ccl9Ag2grQysjhnkOUxjRzWt0CEAXA7rFAVUvyM2soYEobh6o8KMixOI+ALZqy83M+3s7AAAAAElFTkSuQmCC',
			'F4E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZWllDHaY6IIkFNDBMZW1gCAhAFQtlbWB0EEERY3RFEgM7KTRq6dKloauiwpDcF9Ag0go0byqqXtFQVyCNKsYAUueARQzdLRhuHqjwoyLE4j4AIAbMRYqIlIAAAAAASUVORK5CYII=',
			'D85A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMdUAWaxVpdG1gCAhAEQOqm8roIILkvqilK8OWZmZmTUNyH0gdQ0MgTB3cPIeGwNAQDDvQ1AHdwujoiCIGcjNDKCOK2ECFHxUhFvcBAJG1zSNI9gjKAAAAAElFTkSuQmCC',
			'4E5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGsoY6hjogi4WINLA2MDoEIIkxQsVEkMRYpwDFpsLVgZ00bdrUsKWZmaFZSO4LmALSFYhiXmgoREwExS0gOzDFGB0dUfSC3MwQyojq5oEKP+pBLO4DAAZCypGY43Q5AAAAAElFTkSuQmCC',
			'9A39' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGaY6IImJTGEMYW10CAhAEgtoZW1laAh0EEERE2l0aHSEiYGdNG3qtJVZU1dFhSG5j9UVpM5hKrJehlbRUIeGgAZkMQGQeQ0BKHaITBFpdEVzC2uASKMjmpsHKvyoCLG4DwBdRM1nwSL3cwAAAABJRU5ErkJggg==',
			'27C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQx1AEElMZApDo6NDoEMAklhAK0Oja4NAgwiy7laGVlaQHLL7pq2atnTVqqVZyO4LYAhAUgeGjA6MDiAxZPNYwRDVDhEgZERzS2goUAWamwcq/KgIsbgPAEQfzBoT5lTqAAAAAElFTkSuQmCC',
			'1F60' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgdRBoYHR2mOiCJiQLFWBscAgJQ9ILEQCTCfSuzpoYtnboyaxqS+8DqHB1h6pD0BmIRC8CwA8MtIUBdaG4eqPCjIsTiPgA/8skaq3bH7wAAAABJRU5ErkJggg==',
			'98D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoFWl0bQh0EEERA6prCICpAztp2tSVYUtXRU3NQnIfqyuKOgjEYp4AFjFsbsHm5oEKPypCLO4DAExozQggqthMAAAAAElFTkSuQmCC',
			'F874' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDAxoCkMQCGlhbgWQjqphIo0NDQCuGukaHKQFI7guNWhm2aumqqCgk94HVTWF0wDAvgDE0BE3M0YEBwy2sDehiQDejiQ1U+FERYnEfAF0Dz2BFrXwyAAAAAElFTkSuQmCC',
			'002D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxhDGB0dHQKQxESmsLayNgQ6iCCJBbSKNDogxMBOilo6bWXWysysaUjuA6trZcTUOwVVDGQHQwCqGNgtDowobgG5mTU0EMXNAxV+VIRY3AcAEBnJpP1QmcoAAAAASUVORK5CYII=',
			'AF79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxBogAyYCAACQxkSkgsUAHESSxgFYgr9ERJgZ2UtTSqWGrlq6KCkNyH1jdFIapyHpDQ4G8AKC5aOYxOjBg2MEKVBmAKYbi5oEKPypCLO4DAJvXzJhy1J4dAAAAAElFTkSuQmCC',
			'1E70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA1qRxVgdRIBkwFQHJDFRiFhAAIpeoFijI1gG5r6VWVPDVi1dmTUNyX1gdVMYYeoQYgGYYowODBh2sDYwoLolBOjmBgYUNw9U+FERYnEfADwGyNdF7VwnAAAAAElFTkSuQmCC',
			'83A8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANYQximMEx1QBITmSLSyhDKEBCAJBbQytDo6OjoIIKijqGVtSEApg7spKVRq8KWroqamoXkPjR1cPNcQwNRzAOLNQSi2SGCoRfkZqAYipsHKvyoCLG4DwBIls1Ts1jRDAAAAABJRU5ErkJggg==',
			'5453' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMYWllDHUIdkMQCGhimsjYwOgSgioWyAmkRJLHAAEZX1qlgObj7wqYtXbo0M2tpFrL7WkVaQaqQzWNoFQXaGYBiXkAr0C1oYiJTGFoZHR1R3MIawNDKEMqA4uaBCj8qQizuAwC1xMyH7XMbKgAAAABJRU5ErkJggg==',
			'D938' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGaY6IIkFTGFtZW10CAhAFmsVaXRoCHQQQRdDqAM7KWrp0qVZU1dNzUJyX0ArY6ADhnkMWMxjwRTD4hZsbh6o8KMixOI+ABlRz0pe3VkyAAAAAElFTkSuQmCC',
			'9163' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUIdkMREpjAGMDo6OgQgiQW0sgawNjg0iKCIMQDFgDSS+6ZNXRW1dOqqpVlI7mN1BapzdGhANo8BrDcAxTwBLGIiUxgw3AJ0SSi6mwcq/KgIsbgPAKy+yiVsZzuiAAAAAElFTkSuQmCC',
			'40EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpI37pjAEsIY6hjogi4UwhrA2MDoEIIkBRVpBYiJIYqxTRBpdEerATpo2bdrK1NCVoVlI7gtAVQeGoaEQMREUt2DawTAF0y1Y3TxQ4Uc9iMV9AHLlyhu9k5SfAAAAAElFTkSuQmCC',
			'561D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQximMIY6IIkFNLC2MoQwOgSgiIk0MgLFRJDEAgOAvClwMbCTwqZNC1s1bWXWNGT3tYq2IqmDiok0OqCJBWARE5nCCtaL7BbWAKBLQh1R3DxQ4UdFiMV9AETryqt/mmzSAAAAAElFTkSuQmCC',
			'0951' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHVqRxVgDWFtZGximIouJTBFpdG1gCEUWC2gFik1lgOkFOylq6dKlqZlZS5HdF9DKGOgAJFH1MjSii4lMYQHaEYDhFkZHVPeB3Ax0SWjAIAg/KkIs7gMAox3LzSafPvQAAAAASUVORK5CYII=',
			'9333' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANYQxhDGUIdkMREpoi0sjY6OgQgiQW0MjQ6NAQ0iKCKQUUR7ps2dVXYqqmrlmYhuY/VFUUdBGIxTwCLGDa3YHPzQIUfFSEW9wEA1/HNeY5OYhUAAAAASUVORK5CYII=',
			'88B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoFWl0bQh0EMGtDuykpVErw5aGrpqaheQ+Ys0jwg6cbh6o8KMixOI+ALskzXrMxfy/AAAAAElFTkSuQmCC',
			'5F59' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNIg0sDYwBARgiDE6iCCJBQYAxabCxcBOCps2NWxpZlZUGLL7WkEqAqYi64WKNSCLBbSC7AhAsUNkikgDo6MDiltYgfYyhDKguHmgwo+KEIv7ACDdzAmhHWYrAAAAAElFTkSuQmCC',
			'9D43' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUIdkMREpoi0MrQ6OgQgiQW0ijQ6THVoEEEXC3RoCEBy37Sp01ZmZmYtzUJyH6urSKNrI1wdBAL1uoYGoJgnADKvEdUOsFsaUd2Czc0DFX5UhFjcBwAgDc58de4yqgAAAABJRU5ErkJggg=='        
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
