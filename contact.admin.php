<?php
require_once( dirname(__FILE__).'/contact.lib.php' );

define( 'PHPFMG_USER', "root@thunix.net" ); // must be a email address. for sending password to you.
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
			'F29A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGVqRxQIaWFsZHR2mOqCIiTS6NgQEBKCIMQDFAh1EkNwXGrVq6crMyKxpSO4DqpvCEAJXBxMLYGgIDA1BEWN0YGxAV8fawOjoiCYmGuoQyogiNlDhR0WIxX0A4WHMkXyw53UAAAAASUVORK5CYII=',
			'80EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMDkMREpjCGsDYwOiCrC2hlbUUXE5ki0uiKEAM7aWnUtJWpoStDs5Dch6YOah42MWx2YLoFm5sHKvyoCLG4DwB5nMk7XGkkZgAAAABJRU5ErkJggg==',
			'B46A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QgMYWhlCgRhJLGAKw1RGR4epDshiQFWsDQ4BASjqGF1ZGxgdRJDcFxq1dOnSqSuzpiG5L2CKSCuroyNMHdQ80VDXhsDQEFQ7WlkbAlHVTWFoZUTTC3EzI4rYQIUfFSEW9wEAr5nMfCHD8SgAAAAASUVORK5CYII=',
			'0413' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YWhmmMIQ6IImxBjBMZQhhdAhAEhMBqmEMAdJIYgGtjK5AvQ0BSO6LWrp06appq5ZmIbkvoFWkFUkdVEw01GEKqnlAO8DqRFDdAhRDdQvIzYyhDihuHqjwoyLE4j4ADQbLbmTNXLkAAAAASUVORK5CYII=',
			'0F6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgDRBoYHR2mOiCJiUwRaWBtcAgIQBILaAWJMTqIILkvaunUsKVTV2ZNQ3IfWJ2jI0wdkt7A0BAMOwJR1EHcgqoXbGMoI4rYQIUfFSEW9wEAquPK0uIwXQgAAAAASUVORK5CYII=',
			'BD0D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNEQximMIY6IIkFTBFpZQhldAhAFmsVaXR0dHQQQVXX6NoQCBMDOyk0atrK1FWRWdOQ3IemDm4eNjEsdmC4BZubByr8qAixuA8AwMvNc5TosDQAAAAASUVORK5CYII=',
			'68BA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMdUASC2gRaXRtCAgIQBZrAKlzdBBBcl9k1MqwpaErs6YhuS9kCoo6iN5WkHmBoSGYYijqRLDohbiZEUVsoMKPihCL+wD/Lsy5IxWk1AAAAABJRU5ErkJggg==',
			'D4D3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYWllDGUIdkMQCpjBMZW10dAhAFmtlCGVtCGgQQRFjdAWJBSC5L2opEADJLCT3BbSKtCKpg4qJhrpimMfQimHHFKAYmluwuXmgwo+KEIv7AMmbzx9SYeT/AAAAAElFTkSuQmCC',
			'854D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMdkMREpog0MLQ6OgQgiQW0AsWmOjqIoKoLYQiEi4GdtDRq6tKVmZlZ05DcJzKFodG1EVVvQCtQLDQQTUyk0aER3Q5WoEpUt7AGMIagu3mgwo+KEIv7AJAIzI6R5ixaAAAAAElFTkSuQmCC',
			'51F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6Y6IIkFNDAGsDYwBASgiLECxRgdRJDEAgMYkMXATgqbtipqaeiqqDBk97WC1DFMRdYLFWtAFguAiKHYITKFAcMtQJeEgsxDdvNAhR8VIRb3AQCTZMksXTMt4AAAAABJRU5ErkJggg==',
			'7B92' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIu2irQyOjoEBKCKNbo2BDqIIItNEWllbQhoEEF2X9TUsJWZUauikNzH6CDSyhAS0IhsB2uDCJAf0IrsFhGgmGNDwBRkMaDpYLegioHczBgaMgjCj4oQi/sAaLTMcMk0b84AAAAASUVORK5CYII=',
			'574E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHUMDkMSA7EaHVkcHBnSxqahigQEMrQyBcDGwk8KmrZq2MjMzNAvZfa0MAayNqHoZWhkdWEMDUe1oZQXagqpOZIoIhhhrAFgMxc0DFX5UhFjcBwAoyssY9VovqwAAAABJRU5ErkJggg==',
			'490D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37pjCGAHGoA7JYCGsrQyijQwCSGGOISKOjo6ODCJIY6xSRRteGQJgY2EnTpi1dmroqMmsakvsCpjAGIqkDw9BQhkZ0MYYpLBh2MEzBdAtWNw9U+FEPYnEfAK7EyxVLCx+mAAAAAElFTkSuQmCC',
			'D791' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGVqRxQKmMDQ6OjpMRRFrZWh0bQgIRRNrZQWRSO6LWrpq2spMIInkPqCKAIaQAFQ7WhkdGBrQxVgbGNHFpog0MDo6oIiFBog0MIQyhAYMgvCjIsTiPgA4hM25VMJ7sgAAAABJRU5ErkJggg==',
			'31A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYAhimMLQiiwVMYQxgCGWYiqKylTWA0dEhFEVsCkMAa0MATC/YSSujVkUtBSFk96Gqg5oHFAvFIoamLgCLXlGgTqBYaMAgCD8qQizuAwCOfcpeId7qcAAAAABJRU5ErkJggg==',
			'FEDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAS0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGUMDkMQCGkQaWBsdHRjQxRoC8YmBnRQaNTVs6arI0Cwk9xGhF78Yhlsw3TxQ4UdFiMV9AKsKy9JN9QjrAAAAAElFTkSuQmCC',
			'3F73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQ11DA0IdkMQCpogAyUCHAGSVrSCxgAYRZDGQukaHhgAk962Mmhq2aumqpVnI7gOpm8LQgGFeAAOqeUAxRgdUMZBbWIGiyHpFA0BiDChuHqjwoyLE4j4AKqbMtgG9VzwAAAAASUVORK5CYII=',
			'B182' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYHR0CAhAFmtlDWBtCHQQQVHHAFLXIILkvtCoVVGrQlcBCYT7oOoaUexoZQCaByQxxaYwYNoRgOpm1lCGUMbQkEEQflSEWNwHABXny0eZ4YibAAAAAElFTkSuQmCC',
			'6796' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTGFodHR0CAhAEgtoYWh0bQh0EEAWa2BoZQWKIbsvMmrVtJWZkalZSO4LmcIQwBASiGpeKyNQX6CDCIoYawMjmpjIFJEGRjS3sAYAVaC5eaDCj4oQi/sACG3L+wx5ue0AAAAASUVORK5CYII=',
			'811F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYAhhdEBWF9DKGsCIJiYyBawXJgZ20tKoVVGrpq0MzUJyH5o6qHnEiWHTyxrAGsoY6ogiNlDhR0WIxX0AxR/HCPQ88Y0AAAAASUVORK5CYII=',
			'6B8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGVqRxUSmiLQyOjpMdUASC2gRaXRtCAgIQBZrAKlzdBBBcl9k1NSwVaErs6YhuS9kCoo6iN5WkHmBoSGYYijqRLDohbiZEUVsoMKPihCL+wAF1Mv0tdDYEgAAAABJRU5ErkJggg==',
			'C620' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGVqRxURaWVsZHR2mOiCJBTSKNLI2BAQEIIs1iADJQAcRJPdFrZoWtmplZtY0JPcFNIi2MrQywtTB9DY6TEETA9rhEMCAYgfYLQ4MKG4BuZk1NADFzQMVflSEWNwHAMP9y9uPvArYAAAAAElFTkSuQmCC',
			'D385' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMDkMQCpoi0Mjo6OiCrC2hlaHRtCEQXA6lzdUByX9TSVWGrQldGRSG5D6LOoUEEw7wALGKBDiIYbnEIQHYfxM0MUx0GQfhREWJxHwDM8MzjPUmFtAAAAABJRU5ErkJggg==',
			'187E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MDkMRYHVhbGRoCHZDViTqINDqgiTGC1DU6wsTATlqZtTJs1dKVoVlI7gOrm8KIphdoXgCmmKMDuhhrK2sDqphoCNDNDYwobh6o8KMixOI+ADZOxzvRVDR6AAAAAElFTkSuQmCC',
			'14E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YWllDHUNDkMRYHRimsgJpESQxUQeGUHQxRgdGV5BYAJL7VmYtXbo0FEghuY/RQaQVqK4V1V7RUNcGhikYbmlgCMAUA5LIbgkBuxlFbKDCj4oQi/sA7ZnH0+jlxKYAAAAASUVORK5CYII=',
			'D865' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUMDkMQCprC2Mjo6OiCrC2gVaXRtQBdjbWVtYHR1QHJf1NKVYUunroyKQnIfWJ2jQ4MIhnkBWMQCHUQw3OIQgOw+iJsZpjoMgvCjIsTiPgAwAM00MlE58gAAAABJRU5ErkJggg==',
			'F8E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHaY6IIkFNLC2sjYwBASgiIk0ujYwOojgVgd2UmjUyrCloaumZiG5j3jzCNoBFcN080CFHxUhFvcBAD48zRfy81C7AAAAAElFTkSuQmCC',
			'CA3A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhDGVqRxURaGUNYGx2mOiCJBTSyAtUEBAQgizWINDo0OjqIILkvatW0lVlTV2ZNQ3IfmjqomGioQ0NgaAiKHUB1DYEo6kRaRRpd0fSyhog0OoYyoogNVPhREWJxHwDYzs2lyy20iwAAAABJRU5ErkJggg==',
			'2329' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoZWh0bQh0EEHW3QqECDGIm6atClu1MisqDNl9AWCVU5H1MjowNDpMAdqF7JYGoFgAA4odIg1AtzgwoLglNJQ1hDU0AMXNAxV+VIRY3AcAcW3KyQgjqfMAAAAASUVORK5CYII=',
			'CF1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WENEQx2mMIaGIImJtIo0MIQwOiCrC2gUaWBEF2sAqpsCFwM7KWrV1LBV01aGZiG5D00dbrFGTDGwW9DEWEOAbgl1RBEbqPCjIsTiPgD2u8mLBFJTfQAAAABJRU5ErkJggg==',
			'07C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHaY6IImxBjA0OjoEBAQgiYlMYWh0bRB0EEESC2hlaGVtYICpAzspaumqaUtXrZqaheQ+oLoAJHVQMUYHVqDtIih2sDawotnBGiACVIXqFrAuNDcPVPhREWJxHwCh/8uavOaBBQAAAABJRU5ErkJggg==',
			'B0AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIY6IIkFTGEMYQhldAhAFmtlbWV0dHQQQVEn0ujaEAhTB3ZSaNS0lamrIkOzkNyHpg5qHlAsNBDVPKAdrA1oYkC3sKLpBbkZKIbi5oEKPypCLO4DAD4xzUOE3JRsAAAAAElFTkSuQmCC',
			'6087' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUNDkMREpjCGMDo6NIggiQW0sLayNgSgijWINDoC1QUguS8yatrKrNBVK7OQ3BcyBayuFdnegFaRRteGgCmoYmA7Ahgw3OLogMXNKGIDFX5UhFjcBwCUOsuEYex17wAAAABJRU5ErkJggg==',
			'FE4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNFQxkaHUNDkMQCGkQaGFodHRjQxaZiEQuEi4GdFBo1NWxlZmZoFpL7QOpYGzH1soYGYpqHRR2mGNjNKGIDFX5UhFjcBwBj0ct6alE+fAAAAABJRU5ErkJggg==',
			'FB52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHaY6IIkFNIi0sjYwBASgijW6NjA6iKCrm8rQIILkvtCoqWFLM7NWRSG5D6QOSDai2QHkB7QyYNgRMAVNrJXR0SEAVUw0hCGUMTRkEIQfFSEW9wEAa+TN/Kxz5qoAAAAASUVORK5CYII=',
			'9E9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIwBALdBBBct+0qVPDVmZGZk1Dch+rK1BFCFwdBLaCeIGhIUhiAkAxxgZUdRC3OKKIQdzMiGreAIUfFSEW9wEAh4nKni0dVVUAAAAASUVORK5CYII=',
			'DEB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGaY6IIkFTBFpYG10CAhAFmsFijUEOgigizU6OiC7L2rp1LCloStTs5DcB1WH1TwRQmJY3ILNzQMVflSEWNwHADErzdROdvXrAAAAAElFTkSuQmCC',
			'3137' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGUNDkMQCpjAGsDY6NIggq2xlDQDKoIpNYQhgAKoLQHLfyqhVUaumrlqZhew+iLpWFJtbGUDmTcEiFsCA4hYGoFscHVDdzAp0MSOK2ECFHxUhFvcBAPdeyi4ZP7jqAAAAAElFTkSuQmCC',
			'A059' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHaY6IImxBjCGsDYwBAQgiYlMYW1lBaoWQRILaBVpdJ0KFwM7KWrptJWpmVlRYUjuA6lzaAiYiqw3NBQs1oBqHsiOADQ7GEMYHR1Q3BLQyhDAEMqA4uaBCj8qQizuAwCWCcwO1X6SGQAAAABJRU5ErkJggg==',
			'DA89' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtCHQQQRETaXR0dISJgZ0UtXTayqzQVVFhSO6DqHOYiqpXNNS1IaAB3TygGKodU8B6UdwSGiDS6IDm5oEKPypCLO4DAELXzjUb4LxeAAAAAElFTkSuQmCC',
			'145A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDHVqRxVgdGKayNjBMdUASE3VgCAWKBQSg6GV0ZZ3K6CCC5L6VWUuXLs3MzJqG5D6gCqD5gTB1UDHRUIeGwNAQdLdgqGNoZXR0RBETDWFoZQhlRBEbqPCjIsTiPgAUqsgGVvOZ5wAAAABJRU5ErkJggg==',
			'B140' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHVqRxQKmMAYwtDpMdUAWa2UNYJjqEBCAog6oN9DRQQTJfaFRq6JWZmZmTUNyH0gdayNcHdQ8oFhoIIYY0C2YdjSiuiUUqBPdzQMVflSEWNwHAEJBzET1uSmSAAAAAElFTkSuQmCC',
			'8388' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoZWh0bQh0EEFRx4CsDuykpVGrwlaFrpqaheQ+NHU4zcNuB6ZbsLl5oMKPihCL+wA3t8xGly2EVwAAAABJRU5ErkJggg==',
			'D2B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGVqRxQKmsLayNjpMdUAWaxVpdG0ICAhAEWNodG10dBBBcl/U0lVLl4auzJqG5D6guimsCHUwsQDWhkA0MUYHVnQ7prA2oLslNEA01BXNzQMVflSEWNwHAB1uzn5TG2tzAAAAAElFTkSuQmCC',
			'B091' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMRRFrZW1lbQgIRVUn0ugKlEF2X2jUtJWZmVFLkd0HUucQEoBqRytQrAFdjLWVEV0M4hYUMaibQwMGQfhREWJxHwBgDs00+56rEAAAAABJRU5ErkJggg==',
			'E65F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUNDkMQCGlhbWRsYHRhQxEQasYg1sE6Fi4GdFBo1LWxpZmZoFpL7AhpEWxkaAjHMc8Ai5oohxtrK6OiIIgZyM0MoqlsGKvyoCLG4DwAsKsqF7rTx0QAAAABJRU5ErkJggg==',
			'2CDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDGUNDkMREprA2ujY6OiCrC2gVaXBtCEQRYwCKsSLEIG6aNm3V0lWRoVnI7gtAUQeGjA6YYqwNmHYAVWG4JTQU7GZUtwxQ+FERYnEfAFa0ypR0XieSAAAAAElFTkSuQmCC',
			'F24C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZQxgaHaYGIIkFNLC2MrQ6BIigiIkAVTk6sKCIAXUGOjoguy80atXSlZmZWcjuA6qbwtoIVwcTC2ANDUQTY3RgaES3gxVkC5pbREMd0Nw8UOFHRYjFfQCQfM1JUgXJRAAAAABJRU5ErkJggg==',
			'83A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANYQximMIYGIImJTBFpZQhldEBWF9DK0Ojo6IgiJjKFoZW1IdDVAcl9S6NWhS1dFRkVheQ+iLqABhE081xDsYg1BDqIoLkFqDcA2X0gNwPFpjoMgvCjIsTiPgAGIcx+c+wNSgAAAABJRU5ErkJggg==',
			'6D82' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoEWl0bQh0EEEWaxBpdHR0aBBBcl9k1LSVWaGrVkUhuS9kClhdI7IdAa0g8wJaGTDFpjBgcQummxlDQwZB+FERYnEfALp7zWqE64TCAAAAAElFTkSuQmCC',
			'8E0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIaGIImJTBFpYAhldEBWF9Aq0sDo6IgiBlLH2hAIEwM7aWnU1LClqyJDs5Dch6YObh42MWx2oLsF6mYUsYEKPypCLO4DALOmyUwL4QKcAAAAAElFTkSuQmCC',
			'EDE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHaY6IIkFNIi0sjYwBASgijW6NjA6CGARQ3ZfaNS0lamhK1OzkNwHVYfVPBHCYhhuwebmgQo/KkIs7gMAb17NOLndlp8AAAAASUVORK5CYII=',
			'1083' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUIdkMRYHRhDGB0dHQKQxEQdWFtZGwIaRFD0ijQClTUEILlvZda0lVmhq5ZmIbkPTR1czBXDPGx2YHFLCKabByr8qAixuA8AfoXJVB5lVNgAAAAASUVORK5CYII=',
			'18F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA6Y6IImxOrC2sjYwBAQgiYk6iDS6AlWLoOhFUQd20sqslWFLQ1dNzUJyHyMW8xixmkfQDohbQoBubmBAcfNAhR8VIRb3AQD3YMjVwccB2gAAAABJRU5ErkJggg==',
			'39E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUNDkMQCprC2sgJpEWSVrSKNruhiUyBiAUjuWxm1dGlq6KqVWcjum8IYCFTXimJzKwNI7xRUMRaQWACyGMQtjA5Y3IwiNlDhR0WIxX0AVLvLPNH3z8oAAAAASUVORK5CYII=',
			'0F58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxBog0sDYwBAQgiYlMAYkxOoggiQW0AsWmwtWBnRS1dGrY0sysqVlI7gOpA5Io5kHEAlHMg9iBKgZyC6OjA4pesCtCGVDcPFDhR0WIxX0AhJ3LpHB1nNUAAAAASUVORK5CYII=',
			'09BF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUNDkMRYA1hbWRsdHZDViUwRaXRtCEQRC2gFiiHUgZ0UtXTp0tTQlaFZSO4LaGUMdEUzL6CVAcM8kSksGGLY3AJ1M4rYQIUfFSEW9wEA0e/KLv366UIAAAAASUVORK5CYII=',
			'D1A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgMYAhimMDQEIIkFTGEMYAhlaEQRa2UNYHR0aEUVYwhgBaoOQHJf1FIwiopCch9EXaADht7QwNAQTPPQ3IIpFgrUiS42UOFHRYjFfQAx/M4JdITG7AAAAABJRU5ErkJggg==',
			'3AB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGaY6IIkFTGEMYW10CAhAVtnK2sraEOgggiw2RaTRFaEO7KSVUdNWpoaumpqF7D5UdVDzRENd0c1rBapDEwvAolc0ACiG5uaBCj8qQizuAwCuNc2vco1wjQAAAABJRU5ErkJggg==',
			'2F5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUNDkMREpog0sDYwOiCrC2jFFGMAiU2Fi0HcNG1q2NLMzNAsZPcFiADJQBS9jA6YYqwNIDtQxUSAkNHREUUsNBSoNxTNLQMUflSEWNwHADmSyN30YwPwAAAAAElFTkSuQmCC',
			'6649' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoEWlkmOroIIIs1gDkBcLFwE6KjJoWtjIzKyoMyX0hU0RbWYF2oOhtFWl0DQWbgCLm0OiAYgfYLY2obsHm5oEKPypCLO4DAEe/zTUUXAjJAAAAAElFTkSuQmCC',
			'20B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMIawNjpMdUASC2hlbWVtCAgIQNbdKtLo2ujoIILsvmnTVqaGrsyahuy+ABR1YMjoABRrCEQRY23AtEOkAdMtoaGYbh6o8KMixOI+ANp0y/M65z1NAAAAAElFTkSuQmCC',
			'F86E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMDkMQCGlhbGR0dHRhQxEQaXRvQxVhbWRsYYWJgJ4VGrQxbOnVlaBaS+8DqsJoXSIQYNrdgunmgwo+KEIv7APuKy1HfG5BPAAAAAElFTkSuQmCC',
			'8B84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGRoCkMREpoi0Mjo6NCKLBbSKNLoCSSzqpgQguW9p1NSwVaGroqKQ3AdR5+iAaV5gaAimHdjcgiKGzc0DFX5UhFjcBwCuh85GN1cvcwAAAABJRU5ErkJggg==',
			'DE12' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMEx1QBILmCLSwBDCEBCALNYq0sAYwugggiYG1NsgguS+qKVTw1ZNA9JI7oOqa3TA1NvKgCk2hQHdLVMYAtDdzBjqGBoyCMKPihCL+wBgmc0n6qWaWAAAAABJRU5ErkJggg==',
			'FC1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMIaGIIkFNLA2OoQwOjCgiIk0OGIRA+qFiYGdFBo1bdWqaStDs5Dch6YOr5gDhhjQLRhijEBXO6KIDVT4URFicR8AzI/LEVWHcP4AAAAASUVORK5CYII=',
			'C6A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WEMYQximMEx1QBITaWVtZQhlCAhAEgtoFGlkdHR0EEEWaxBpYG0IhImBnRS1alrY0lVRUWFI7gtoEG1lbQiYiqa30TUUZAKqHa4NASh2gNwC1IviFpCbQeYhu3mgwo+KEIv7AOMtzRjEtnl1AAAAAElFTkSuQmCC',
			'2775' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0MDkMREpjA0OjQEOiCrC2jFFGNoBcJGR1cHZPdNA8KlK6OikN0XAIRTgOYi6WV0YHQAiqKIsQIhSBxZTAQIWYEmILsvNBQsNtVhEIQfFSEW9wEA37rK64pnlHwAAAAASUVORK5CYII=',
			'9831' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGVqRxUSmsLayNjpMRRYLaBVpdGgICEUVY21laHSA6QU7adrUlWGrpq5aiuw+VlcUdRAIMQ9FTACLGNQtKGJQN4cGDILwoyLE4j4ABPLM3Rc723EAAAAASUVORK5CYII=',
			'EE05' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIYGIIkFNIg0MIQyOjCgiTE6OmKIsTYEujoguS80amrY0lWRUVFI7oOoA5uKphdTDGQHuhhDKEMAsvsgbmaY6jAIwo+KEIv7ANgdzBgKIsVgAAAAAElFTkSuQmCC',
			'A510' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2Quw2AMAxEzwUbmH08goukYQSmCIU3CNmAAqYkosF8ShD4uqeT/WQsl0n4U17xI2kjMsyzRjkhYBTHOHOiAFXH1Dggk7Dz66ZxWsrcF+enhkH23pYYr6zuq+x8o7Hqd3BRo0BRDs5f/e/B3Pit3JTMWCJAbPsAAAAASUVORK5CYII=',
			'0774' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nM2QsRGAIAxFk4INcJ809r+QhmlCwQacG9AwpZxVEEs9ze/e/Vzehdo0Sn/KK34sS1gDFIY5UBJFssyXk2XLkCl3WmD8Ym17qy1G49d7oMIy7rIQOGzDDdd96OLi1enIWGb21f8ezI3fARsJzWDPOQA+AAAAAElFTkSuQmCC',
			'0F78' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxBogAyYCAACQxkSkgsUAHESSxgFYgr9EBpg7spKilU8NWLV01NQvJfWB1UxhQzAOLBTCimAeyg9EBVQzkFtYGVL0gFUAxFDcPVPhREWJxHwAQqMvVcHElcQAAAABJRU5ErkJggg==',
			'66E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoEWlkbWB0EEAWaxBpAIkhuy8yalrY0tCVqVlI7guZIgo0jxHVvFaRRlegXhECYtjcgs3NAxV+VIRY3AcA+v/LVezBmYoAAAAASUVORK5CYII=',
			'8564' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ3AIAwETeENnH2gSO9I0DCNKdgARqBhylAakjJR4u9OL/tk6JcR+FNe8UPeAgQQVowKiXE2acaZBMXmpedRoLDya7G2VnuMyo8KpN05O+8bTI7g5xuD8eKCebhMDNn41fmr/z2YG78TpBPOQiecYIkAAAAASUVORK5CYII=',
			'6150' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHVqRxUSmMAawNjBMdUASC2hhBYkFBCCLAfmsUxkdRJDcFxm1KmppZmbWNCT3hUxhCGBoCISpg+htxS7G2hCAYocIUC+jowOKW4AuCWUIZUBx80CFHxUhFvcBAHASygaQAM4/AAAAAElFTkSuQmCC',
			'BB5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHVqRxQKmiLSyNjBMdUAWaxVpdG1gCAhAVzeV0UEEyX2hUVPDlmZmZk1Dch9IHUNDIEwd3DyHhsDQEAw70NQB9TI6OqKIgdzMEMqIIjZQ4UdFiMV9ALWqzUHNtgc3AAAAAElFTkSuQmCC',
			'CF6F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WENEQx1CGUNDkMREWkUaGB0dHZDVBTSKNLA2oIk1gMQYYWJgJ0Wtmhq2dOrK0Cwk94HVoZsH1huIxQ5UMWxuYQ0RaWAIZUQRG6jwoyLE4j4AjSXJ9uiwkoUAAAAASUVORK5CYII=',
			'B0C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCHVqRxQKmMIYwOgRMdUAWa2VtZW0QCAhAUSfS6NrA6CCC5L7QqGkrU1etzJqG5D40dVDzsIlhswPTLdjcPFDhR0WIxX0AWeXNH+c/IFcAAAAASUVORK5CYII=',
			'5744' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHRoCkMSA7EaHVodGDLGpDq3IYoEBDK0MgQ5TApDcFzZt1bSVmVlRUcjua2UIYG10dEDWy9DK6MAaGhgagmxHKyvIFhS3iEwRwRBjDcAUG6jwoyLE4j4AAbvPAAhYjjUAAAAASUVORK5CYII=',
			'2E45' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxkaHUMDkMREpog0MLQ6OiCrC2gFik1FFWMAiQU6ujogu2/a1LCVmZlRUcjuCxBpYG10aBBB0svoABQD2oosxgriNTo6IIuJgMUcApDdFxoKcrPDVIdBEH5UhFjcBwAGjMtvlSqn6wAAAABJRU5ErkJggg==',
			'869F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6OiCrC2gVaWRtCEQRE5ki0oAkBnbS0qhpYSszI0OzkNwnMkW0lSEkEMM8hwZMMUcMOzDdAnUzithAhR8VIRb3AQAL6Mmjz2O9lwAAAABJRU5ErkJggg==',
			'AAD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGUMDkMRYAxhDWBsdHZDViUxhbWVtCEQRC2gVaXRtCHR1QHJf1NJpK1NXRUZFIbkPoi6gQQRJb2ioaCi6GNQ8BwyxRoeAAHSxUIapDoMg/KgIsbgPADHwzcH/rz0jAAAAAElFTkSuQmCC',
			'5CF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDA6Y6IIkFNLA2ujYwBASgiIk0uDYwOgggiQUGiDSwAsWQ3Rc2bdqqpaErU7OQ3dcKVodiHlTMQQTZjlaIHchiIlMw3cIaAHRzAwOKmwcq/KgIsbgPAAPzy+QDMi3bAAAAAElFTkSuQmCC',
			'ED9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUNDkMQCGkRaGR0dHRhQxRpdGwLxiYGdFBo1bWVmZmRoFpL7QOocQjD1OmAxzxFTDMMtUDejiA1U+FERYnEfAKF/y6FiqBQaAAAAAElFTkSuQmCC',
			'725C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHaYGIIu2srayNjAEiKCIiTS6NjA6sCCLTWFodJ3K6IDivqhVS5dmZmYhuw+oYgpDQ6ADsr0g89HFRIAqWYFiyHYEAFUyOjqguCWgQTTUIZQB1c0DFH5UhFjcBwAo5sqdAUi9TwAAAABJRU5ErkJggg==',
			'968D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijSyNgQ6iKCKNYDUiSC5b9rUaWGrQldmTUNyH6uraCuSOggEmueKZp4AFjFsbsHm5oEKPypCLO4DAOxPymXydAQuAAAAAElFTkSuQmCC',
			'DA91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMIYwOjpMRRFrZW1lbQgIRRUTaXQFksjui1o6bWVmZtRSZPeB1DmEBKDa0Soa6tCALibS6IguNgUo5uiAIhYaADQvlCE0YBCEHxUhFvcBALSqzosDsdMJAAAAAElFTkSuQmCC',
			'36A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYQximMEx1QBILmMLayhDKEBCArLJVpJHR0dFBBFlsikgDa0NAgwiS+1ZGTQtbuioKCJHcN0W0Faiu0QHNPNfQgFYGdDGg7QxobgHqDUB3M2tDYGjIIAg/KkIs7gMAp1XMy0GkH0oAAAAASUVORK5CYII=',
			'C35E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEOAMNQxNABJTKRVpJW1gdEBWV1AI0OjK7pYA0Mr61S4GNhJUatWhS3NzAzNQnIfSB1DQyC63kYHdDGwHahiILcwOjqiiIHczBDKiOLmgQo/KkIs7gMAyTvKSW3ChloAAAAASUVORK5CYII=',
			'3E15' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RANEQxmmMIYGIIkFTBFpYAhhdEBR2SrSwIguBlI3hdHVAcl9K6Omhq2atjIqCtl9YHUMDSJo5mEXY3QQQXfLFIYAZPeB3MwY6jDVYRCEHxUhFvcBAI67ylZN/A5jAAAAAElFTkSuQmCC',
			'2F65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwE7SIbmH2cgt4Upsg0btiAsAEFTElC5QhKkOLvTv/SyXA+zqCn/OIXZFBWVHGMVjKMkX1PFrJgLYOb4cjeb8vzno+UvJ+UXmQjt0WuW2lYsMom9oysurB4P9XSUMjcwf8+zIvfBTmsysrEdM0vAAAAAElFTkSuQmCC',
			'E60C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMEwNQBILaGBtZQhlCBBBERNpZHR0dGBBFWtgbQh0QHZfaNS0sKWrIrOQ3RfQINqKpA5unisWMUcMOzDdgs3NAxV+VIRY3AcA4X7MAwCJ8VoAAAAASUVORK5CYII=',
			'7B44' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQxgaHRoCkEVbRVoZWh0a0cQaHaY6tKKITQGqC3SYEoDsvqipYSszs6KikNzH6CDSytro6ICsl7VBpNE1NDA0BElMBCjmgOaWgAagHRhiWNw8QOFHRYjFfQCIBc8FR+n6agAAAABJRU5ErkJggg==',
			'7C16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMEx1QBZtZW10CGEICEARE2lwDGF0EEAWmyICxIwOKO6LmrZq1bSVqVlI7mN0AKtDMY+1AaJXBElMBAgd0MQCGoBumYLqloAGRqCrHVDdPEDhR0WIxX0APT3LoyIZ2K4AAAAASUVORK5CYII=',
			'1129' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxOjAGMDo6BAQgiYk6sAawNgQ6iKDrRYiBnbQya1UUkIgKQ3IfWF0rw1QMvVMYGjDEAhgw7ABhFLeEsIayhgaguHmgwo+KEIv7AKJixi3WghgIAAAAAElFTkSuQmCC',
			'038B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUMdkMRYA0RaGR0dHQKQxESmMDS6NgQ6iCCJBbQyIKsDOylq6aqwVaErQ7OQ3IemDiaGYR42O7C5BZubByr8qAixuA8A/DPKfflFnKkAAAAASUVORK5CYII=',
			'FD96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6CGARQ3ZfaNS0lZmZkalZSO4DqXMICcQwzwGoVwRNzBFTDItbMN08UOFHRYjFfQBCEs3uUm25TAAAAABJRU5ErkJggg==',
			'BD0C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQximMEwNQBILmCLSyhDKECCCLNYq0ujo6OjAgqqu0bUh0AHZfaFR01amrorMQnYfmjq4edjEsNiB4RZsbh6o8KMixOI+AL4DzW+cuJRxAAAAAElFTkSuQmCC',
			'25AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIY6IImJTBFpYAhldAhAEgtoFWlgdHR0EEHW3SoSwtoQCBODuGna1KVLV0VmTUN2XwBDoytCHRgyOgDFQlHFWBtEMNSJNLC2guxAdktoKCPIXhQ3D1T4URFicR8A/K3LfS3XHTAAAAAASUVORK5CYII='        
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
