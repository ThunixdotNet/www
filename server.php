<?php include 'HEADER.php'; ?>
<title>Status of thunix Servers and Services - thunix</title>
<?php include 'HEADER2.php'; ?>
					<div id="content">
						<div class="post">
							<h2 class="title">Information and Service Status</h2>
							<div style="clear: both;">&nbsp;</div>
              <div class="entry">
								<h3>State of the Thunix - May 09, 2019</h3>
              	<p>Another month, another update!</p>

<p>Not too much to announce, as far as front end changes.  New user requests should happen much faster now that we've written/stolen a tool from tilde.team that we can use to expedite user creation, and automation of a bunch of the steps we take to onboard users.</p>
<p>We're considering adding in user aging for accounts.  Lots of accounts get created, and then never logged into.  This can actually post a security problem for the system, as often times, accounts get created now, to be used months from today for botnets and the like.  Let us know what your thoughts are on a reasonable time to age off users.  At this time, I am personally leaning towards 180 days.  Plenty of time to log into your shell, and use it a bit.  And, once every 6 months isn't much to ask, since we expect members to actually contribute to the community, anyways.</p>
<p>We've terminated one account already for running a botnet member from here, and we'll keep a vigilant eye for any others.</p>
<p>This leads to another point:  Just running a znc process doesn't count for login.  Neither does checking your email.  You'll need to actually log into the shell, in order to reset the counter.  You should, anyways, since we have a lot of services internally, that we don't offer external access too.</p> 
<p>We are also looking at terminating the minecraft and minetest instances here.  If anyone is using them, speak up now, or forever hold you peace!  Not really.  We wouldn't be deleting anything, just shutting down the processes, and removing them from the backup scheme.</p>
<p>We do need to welcome our newest sysadmin here:  fosslinux.  Give them a warm welcome if you see them around.</p>
<p>And again, any questions, or concerns, feel free to drop myself, or any of the other admins a line.</p>
</p>
<p>Ubergeek/ub3g33k</p>
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
