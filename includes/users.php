<?php
$html_skel = '/etc/skel/public_html/index.html';

print "<!-- Begin autogen userdir list -->";
print "<ul style='list-style: none; margin-left: -40px;'>";

foreach (glob("/home/*") as $userpath) {
    if (is_dir("$userpath/public_html")) {
        $user = basename($userpath);

        // Use @ to suppress warnings in case the user directory/files are not readable.
        $skeletonMatch = (@sha1_file($html_skel) === @sha1_file("$userpath/public_html/index.html"));
        $isEmptyPubhtml = (@count(@scandir("$userpath/public_html")) === 2); // 2 => "." and ".."

        if ($skeletonMatch || $isEmptyPubhtml) {
            // If it matches the skeleton index.html or is empty, display without a link
            print "<li>~$user</li>\n";
        } else {
            // Otherwise, link to the user's directory
            print "<li><a href='$site_root/~$user/'>~$user</a></li>\n";
        }
    }
}

print "</ul></div>\n<!-- End Autgen userdir list -->";
?>
