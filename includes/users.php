<?php
print "<!-- Begin autogen userdir list -->";
print "<ul style='list-style: none; margin-left: -40px;'>";
foreach (glob("/home/*") as $user):
	if (is_dir($user . "/public_html") && (file_exists($user . "/public_html/index.html") || file_exists($user . "/public_html/index.php")))
	{
		$user = basename($user);
		print"<li><a href='$site_root/~$user/'>~$user</a></li>";
	}
endforeach;
print "</ul></div>
<!-- End Autgen userdir list -->";
?>
