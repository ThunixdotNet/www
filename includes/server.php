<?php
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
?>
