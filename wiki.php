<?php
/*
This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)
Parsedown is licensed under the MIT license.
*/

include('config.php');
include('parsedown-1.7.3/Parsedown.php');
include('parsedown-extra-0.7.1/ParsedownExtra.php');

if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = "main";

if(isset($_GET['style']))
	$site_style = $_GET['style'];

$Parsedown = new Parsedown();
$Parsedown->setMarkupEscaped(true);
$ParsedownExtra = new ParsedownExtra();

if (empty($site_style))
	$site_style="site";

$header  = file_get_contents("$doc_root/includes/header.md");
$sidebar = file_get_contents("$doc_root/includes/sidebar.md");
$content = file_get_contents("$doc_root/articles/$page.md");
$footer  = file_get_contents("$doc_root/includes/footer.md");
 
print "<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>$site_name - $page</title>
		<link rel='stylesheet' type='text/css' href='$site_root/includes/$site_style.css'>
	</head>
	<body>
<!-- Begin Header -->

	<div id='header'>";

print $Parsedown->text($header);

print "
		</div>
<!-- End Header -->
";

print "<hr>
	<div id='body'>

<!-- Begin Sidebar  -->
		<div id='sidebar'>
";

echo $Parsedown->text($sidebar);

print "		</div>
<!-- End Sidebar -->

<!-- Begin Body -->
		<div id='content'>";

echo $ParsedownExtra->text($content);

switch ($page)
{
	case 'users':
	case 'server':
	require "includes/$page.php";
}

print "		</div>
<!-- End Body -->

	</div>

<!-- Begin Footer -->
	<div id='footer'>
	<hr>
";

echo $Parsedown->text($footer);

print "	<a href=\"https://github.com/ThunixdotNet/www\">Site Source</a> | <a href=\"https://github.com/ThunixdotNet/www/tree/master/articles/$page.md\">Page Source</a>
	</div>
<!-- End Footer -->

	</body>
</html>";
?>
