<?php
print "<!-- Begin autogen userdir list -->";
print "<ul style='list-style: none; margin-left: -40px;'>";
foreach (glob("/home/*") as $user):
	if (is_dir($user . "/public_html"))
	if (!file_exists($user . "/public_html/coming_soon"))
	if (count(scandir($user."/public_html")) != 2)
	{
		$user = basename($user);
		print"<li><a href='$site_root/~$user/'>~$user</a></li>";
	}
endforeach;
print "</ul></div>
<!-- End Autgen userdir list -->";
?>
