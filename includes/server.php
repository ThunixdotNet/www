<?php
/*
This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)
Parsedown is licensed under the MIT license.
*/

include('../config.php');
include('../parsedown-1.7.3/Parsedown.php');
include('../parsedown-extra-0.7.1/ParsedownExtra.php');

if(isset($_GET['page']))
	$page = $_GET['page'];
else
	$page = "server";

if(isset($_GET['style']))
	$site_style = $_GET['style'];

$Parsedown = new Parsedown();
$Parsedown->setMarkupEscaped(true);
$ParsedownExtra = new ParsedownExtra();

if (empty($site_style))
	$site_style="site";

$header  = file_get_contents("$doc_root/includes/header.md");
$sidebar = file_get_contents("$doc_root/includes/sidebar.md");
$content = file_get_contents("$doc_root/articles/server.md");
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

// Monitoring section

$hosts="all";

$f = fopen("$doc_root/report", "r");

echo "Last update: " . date ("H:i", filemtime('./report'))."<p>\n";
echo "<table style='width:80%'>";
echo " <tr>
       <th>Host</th>
       <th>Check</th>
       <th>Status</th>
       </tr>";
while (($line = fgetcsv($f)) !== false) {
  echo "<tr>";
  if ($hosts == "failed" ) {
    if ($line[2] == "FAILED") {
      foreach ($line as $cell) {
        if ($cell == "FAILED") {
            echo '<td style="color:#FF0000">' . htmlspecialchars($cell) . '</td>';
        }
        else {
          echo "<td>" .htmlspecialchars($cell) . "</td>";
        }
      }
    }
  }
  elseif ($hosts == "all") {
    foreach ($line as $cell) {
      if ($cell == "FAILED") {
      echo '<td style="color:#FF0000">' . htmlspecialchars($cell) . '</td>';
    }
    elseif ($cell=="GOOD") {
      echo '<td style="color:#00FF00">' . htmlspecialchars($cell) . "</td>";
    }
    else {
      echo "<td>" .htmlspecialchars($cell) . "</td>";
      }
    }
  }
  echo "</tr>\n";
}
echo "\n</table>\n";
fclose($f);

// End monitoring section
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
