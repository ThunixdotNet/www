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
								<h3>State of the Thunix - Mar 1, 2019</h3>
<p>We've come a long way since December, huh? What started as a pretty small droplet running in Digital Ocean, offering almost nothing besides a shell to where we are today.</p>
<ul>
<li>IRC leaf node on tilde.cha (thunix.tilde.chat)</li>
<li>Gopher Server</li>
<li>New Domain Name (thunix.net)</li>
<li>Fully configured email and webmail, with DMARC, DKIM, SPF, and Rainloop mail</li>
<li>ZNC</li>
<li>Minecraft</li>
<li>Minetest</li>
<li>BZFlag</li>
<li>New killer website</li>
<li>Dedicated hardware, with great specs</li>
<li>GNU Project mirror</li>
</ul>
<p>These are just some of the biggest projects finished up. We went from 2 users (Myself and amcclure), to 62 users! Welcome all newcomers!</p>
<p>We've also gotten the first donation(s) in. I promised to give you a proper state of accounting, so here it is:</p>
<h2 id="donations-in-usd-1">Donations (In USD) +$1</h2>
<p>Domain Name -$1 IP Address -$1 Server -$36 ----------------------- Total -$35</p>
<p>Now, I don't want people to be alarmed! I'm funding this because it's fun, not because I expect to be rich. Donations help, but are not required. I just want to make sure people have a full accounting of what is coming in, and going out.</p>
<p>When you get a moment, say thank you to TechEmporium for the website facelift. He did most of the heavy lifting, and it is appreciated.</p>
<p>For any service that requires a password, if you choose to use it, will require us to set a password on your account. Just ask a sysadmin for help with that (myself, amcclure, or naglfar), and we'll get you all set. Right now, only ZNC and Webmail/Email requires an account password.</p>
<p>And, I'm sure you're sick of hearing me say this, but feel free to open a PR to make a system config change! This community is about learning, and offering users things users want. You can open a PR at https://tildegit.org/thunix, and do so in the appropriate project.</p>
<p>Enough rambling from me. Have fun, and feel free to reach out to us, or me. I'm generally a nice guy :)</p>								
								<br>
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
