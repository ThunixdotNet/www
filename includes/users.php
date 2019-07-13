<?php
/*
This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)
Parsedown and Parsedown Extra is licensed under the MIT license.
*/

include('../config.php');
include('../parsedown-1.7.3/Parsedown.php');
include('../parsedown-extra-0.7.1/ParsedownExtra.php');

$page = $_GET['page'];
$style = $_GET['style'];
$Parsedown = new Parsedown();
$Parsedown->setMarkupEscaped(true);
$ParsedownExtra = new ParsedownExtra();

if ( $page == "") {
	$page = "main";
	}

if ( $style == "") {
	if ( $site_style == "") {
		$site_style="site";
	}
}
else {
	$site_style=$style;
}

$header  = file_get_contents("$doc_root/includes/header.md");
$sidebar = file_get_contents("$doc_root/includes/sidebar.md");
$content = file_get_contents("$doc_root/articles/userdir.md");
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

print "<!-- Begin autogen userdir list -->";
print "<ul style='list-style: none; margin-left: -40px;'>";
foreach (glob("/home/*") as $user):
		if (!is_dir($user . "/public_html") || (!file_exists($user . "/public_html/index.html") && !file_exists($user . "/public_html/index.php")))
	continue;
	$user = basename($user);
	print"<li><a href='$site_root/~$user/'>~$user</a></li>";
endforeach;
print "</ul></div>
<!-- End Autgen userdir list -->";

print "		</div>
<!-- End Body -->

	</div>

<!-- Begin Footer -->
	<div id='footer'>
	<hr>
";

echo $Parsedown->text($footer);

print "	</div>
<!-- End Footer -->

	</body>
</html>";
?>
