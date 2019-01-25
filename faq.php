<?php include 'HEADER.php';?>
<title>Frequently Asked Questions - thunix Community</title>
</head>
<body>
	<div id="top">
		<div id="header">
			<div id="logo">
				<h1>&#9763; thunix</h1>
				<p>Community</p>
			</div>
		</div>
	<div id="page">
		<div id="page-bgtop">
			<div id="page-bgbtm">
				<div id="content">
					<div class="post">
						<h2 class="title">Frequently Asked Questions</h2>
						<div style="clear: both;">&nbsp;</div>
						<div class="entry">
							<p><b style="color: #FF00FF">How do I sign up for an account?</b><br>You can ask in #thunix on tilde.chat, or you can email amcclure (with the instructions on the main page).</p>
							<p><b style="color: #FF00FF">Who is running thunix?</b><br>The current system administrators are <a href="/~amcclure">amcclure</a> and <a href="/~ubergeek">ubergeek</a>.</p>
							<p><b style="color: #FF00FF">What happened to the old thunix?  Why the name change?</b><br>The original machine and founder dissappeared without any warning to anyone, including server staff. For this reason, most things were not backed up, and we needed to obtain a new domain name, and a new set of machines.</p>
							<p><b style="color: #FF00FF">I want a new package installed, or I want something changed on Thunix!</b></br>Excellent!  We're looking to make this system useful for the community!  You can submit a PR or an issue <a href="https://tildegit.org/thunix/ansible">here</a> to request the system change.</p>
							<p><b style="color: #FF00FF">Can I get password-based login?  Old thunix had it!</b></br>No.  Sorry.  Not for shell access.  For other integrated services, password auth will be enabled, but not for your ssh connection.  We use key based authentication, as it's more secure, and more convienent for you, to be honest.</p>
							<p><b style="color: #FF00FF">I want to run X server, but I can't seem to access it? </b></br>The only exposed ports to the internet are services as defined in our ansible playbook.  If there is a public service you want to see, open an issue, or do a pull request for it, and we'll probably enable it without much question.</p>
							<p><b style="color: #FF00FF">That's too hard!  Can you just open the port up for this service I have running?</b></br>No.  Due to security issues, we cannot.  HOWEVER!  You can certainly use an <a href="https://duckduckgo.com/?q=ssh+tunnnel">SSH tunnel</a> to access it.</p>
							<p><b style="color: #FF00FF">Old thunix did X, and now it doesn't.  Make it work like it used to!</b></br>There was a huge changeover.  Maybe we can get something going old thunix had, and maybe not.  You can mention it in the IRC channel, and we'll see what we can do.</p>
						</div>
					</div>
				<div style="clear: both;">&nbsp;</div>
				</div>
<?php include 'MENU.php'; ?>
<?php include 'FOOTER.php';?>
