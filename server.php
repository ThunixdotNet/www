<?php include 'HEADER.php'; ?>
<title>Status of thunix Servers and Services - thunix Community</title>
</head>
<body>
<div id="top">
	<div id="header">
		<div id="logo">
			<h1>&#9763; thunix</h1>
		</div>
	</div>
		<div id="page">
			<div id="page-bgtop">
				<div id="page-bgbtm">
					<div id="content">
						<div class="post">
							<h2 class="title">Information and Service Status</h2>
							<div style="clear: both;">&nbsp;</div>
              <div class="entry">
								<h3>State of the Thunix - Feb 6, 2019</h3>
								<p>So, here were are, almost one month post-new server!  Some exciting things, and some things that possible have people a little upset.</p>
								<p>A BIG change are the ports being wide open on the system.  I tried this for about a week, and got a notice from the provider, as a warning.  So, we had to enable a firewall on the system, and open ports, as requested by users.  But, in order to get a port opened up, you need to either a) do a pull request on tildegit.org configuring the daemon you want to run, or b) open an issue there, with your request.  Be mindful, in order for us to set it up for you, the software must be free and open source software.</p>
								<p>Another big change, and I feel this is super-empowering for the users is that our configuration is maintained in a central source control.  This allows all users the ability to see how things work, and also enabled them to request the exact changes they desire.  It also removes the Bus Factor of 1, which old-thunix was prone to, and eventually succumbed to.  Anyone can create a new thunix from the work done here, to include any member of the admin team (As they also maintain backups of key files, and most of the home dirs).</p>
								<p>But!  We also added some stuff, to make it feel more like home as well.  There's currently a bzflag server, some shell customizations to help you feel a bit more comfortable, all the build tools you need.  If some are missing, ask in IRC, or do a PR, or create an issue on tildegit.  We are working on getting a Doom multiplayer server up and running, as well as a GlowstoneMC instance.</p>
								<p>While not a huge deal, I am paying out of pocket for this, which I don't mind doing at all.  It's fun, after all!  But, if you want, you can donate via Liberapay, Paypal, and BitCoin.  Just click on the donate link in the sidebar.</p>
								<p>So, welcome back to Thunix, any questions, I'm a nice guy, as are amcclure and Naglfar (The other admins), and we're more than happy to answer questions.</p>
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
