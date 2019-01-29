<?php include 'HEADER.php'; ?>
<title>User Web Directories - thunix Community</title>
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
							<h2 class="title">User Web Directories</h2>
							<div style="clear: both;">&nbsp;</div>
							<div class="entry">
								<p>Below is a list of user web directories:</p>
								<ul style="list-style: none; margin-left: -40px;">
									<?php
									foreach (glob("/home/*") as $user):
										if (!is_dir($user . "/public_html") || (!file_exists($user . "/public_html/index.html") && !file_exists($user . "/public_html/index.php")))
										continue;
									$user = basename($user);?>
									<li><a href="/~<?=$user?>/">~<?=$user?></a></li>
									<?php endforeach; ?>
									</ul>
								<p>Note that most content on thunix are provided by thunix's users, not the owner of the server. Comments should be addressed to the owner of the web directory in the first instance - their email address here is <code>&lt;username&gt;@thunix.cf</code>, unless otherwise specified within their web directory.</p>
								<p>If there is a problem which is not resolved by the owner of the web directory, please send us an <a href="/abuse.php">abuse report</a>.</p>
								<p>If you want to be added to this list, simply create an index.html or index.php file and upload it to your public_html directory. If you want to be removed from the list, simply remove your index file from your public_html directory.</p>
							</div>
						</div>
					<div style="clear: both;">&nbsp;</div>
					</div>
<?php include 'MENU.php'; ?>
<?php include 'FOOTER.php'; ?>
