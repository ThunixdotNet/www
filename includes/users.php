<?php
$html_skel='/etc/skel/public_html/index.html';
print "<!-- Begin autogen userdir list -->";
print "<ul style='list-style: none; margin-left: -40px;'>";
foreach (glob("/home/*") as $userpath):
    if (is_dir("$userpath/public_html"))
    {
	$user = basename($userpath);
	if(sha1_file($html_skel) == sha1_file("$userpath/public_html/index.html") || count(scandir("$userpath/public_html")) == 2)
	  print"<li>~$user</li>\n";
	else
	  print"<li><a href='$site_root/~$user/'>~$user</a></li>\n";
    }
endforeach;
print "</ul></div>
<!-- End Autgen userdir list -->";
?>
