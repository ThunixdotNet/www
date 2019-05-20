<?php include 'HEADER.php'; ?>
<title>Status of thunix Servers and Services - thunix</title>
<?php include 'HEADER2.php'; ?>
							<h2 class="title">Service Status and Information</h2>
							<div style="clear: both;">&nbsp;</div>
              <div class="entry">
									<h3><p>Minetest</p></h3>
									<p>Connect to thunix.net:30000 in your minetest client</p>
									<hr/>
									<h3><p>Minecraft</p></h3>
									<p>Connect to thunix.net in your minecraft client.  Running the Paperclip minecraft server.<p>
									<hr/>
									<h3><p>Mail Services</p></h3>
									<p>Thunix offers webmail at <a href="/webmail">https://thunix.net/webmail</a>.  You can also connect via imap.  Thunderbird will autodetect your settings, and we recommend the use of Thunderbird email client, so you can get the best-of-breed email experience.  We also recommend using enigmail with Thunderbird as well.  You can get it from your distro's package manager, or from <a href="https://www.thunderbird.net/">the Thunderbird project's site</a></p>
									<hr/>
									<h3><p>Onion Service</p></h3>
									<p>Thunix's services are available as a onion site as well.  Our onion address is thunixme5v4rnoby.onion.</p>
									<hr/>
									<h3><p>BZFlag Game Server</p></h3>
									<p>BZFlag runs on the standard port.  If you have a specific map you want loaded, send an email to root, and we'll look at getting the map swapped out</p>
									<hr/>
									<h3><p>IRC Chat</p></h3>
									<p>Thunix is part of the tilde.chat network.  You can access chat via the terminal, with the 'chat' command, via your favorite email client at irc.tilde.chat/6697, or via a web chat interface located <a href="">here</a>.</p>
									<hr/>
									<h3><p>FOSS Project Mirrors</p></h3>
									<p>Thunix hosts mirrors for several FOSS projects.  You can see the full mirror list in the sidebar link.</p>
									<hr/>
							</div>

<!-- Placeholder for format hints
              <div style="clear: both;">&nbsp;</div>
              <div class="entry">
                <p>(Coming soon)</p>
              </div>
            </div>
            <div class="post">
              <h2 class="title">Server Status</h2>
              <div style="clear: both;">&nbsp;</div>
-->
            </div>
            <div class="post">
              <h2 class="title">Server Status</h2>
              <div style="clear: both;">&nbsp;</div>
							<div class="entry">
								<p>
<!-- Have to clean this section up a bit, styling, really -->
<?php

$hosts="all";

$f = fopen("./report", "r");

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
?>
<!-- End of the monitoring script portion -->

								</p>
							</div>
						</div>
					<div style="clear: both;">&nbsp;</div>
					</div>
<?php include 'MENU.php'; ?>
<?php include 'FOOTER.php'; ?>
