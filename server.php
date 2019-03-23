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
								<h3>State of the Thunix - Mar 22, 2019</h3>
								<p>So, March is upon us.  It's Spring now, or Fall, depending on the side of the globe you're on.  Thunix has had some big changes this month, and some not so big changes.</p>
								<p>First things first, discussion of money.  This is not an attempt to incite fear, or anything.  We're good for now, don't worry.  We recieved 1USD in donations this month.  Expenses work out to ~35USD/month.  I can give a breakdown, but this puts monthly net at - 34USD this month.</p>
								<p>Now that is out of the way, onto the cool stuff!  Since our last update, we've beefed up email services.  IMAP email is fully configured.  Our email is now TLS encrypted between servers, and your sending connections are also encrypted.  Google gives us a "grey lock" for emails now!</p>
								<p>On the subject of emails:  We also have webmail available for use, as well as IMAP mail.  Even better?  Thunderbird email client will auto-configure your client, based on email address now!  No more playing with your settings.</p>
								<p>We are now much closer to a fully CI/CD environment now.  Once a commit is recieved in our repos (ansible, www, thunix_gopher), our system will be notified, and the appropriate jobs will run to sync the appropriate deployments.</p>
								<p>In case you haven't noticed:  We've also moved domain names.  Thunix.cf served us well, and is still working for most basic applications, but you need to switch soon to thunix.net for everything.  Eventually, thunix.cf will be going away, and thunix.net will be the perm home.  .cf domains have a little quirk that when you get popular, they yank your domain from you, without notice.  Being on a .net domain precludes this.</p>
								<p>Lots of new users lately, too, and this means we're doing something good here!  You can help us with this by submitting issues or pull requests on <a href="https://tildegit.org/thunix/">tildegit</a>.  It really is the fastest way to get something changed you want changed.  We also want your name in the commit logs too, since we're trying to build this together!</p>
								<p>And with that, I'm hoping you're enjoy Thunix's services, but mostly I'm hoping you're enjoying the community we're building here.  Check back next month!</p>
								<p>
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
