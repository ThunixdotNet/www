<?php
include "../config.php";
// This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)

// Optional: keep the terminal UI flow inside /terminal/ without changing the classic site.
//
// Prefer an explicit flag (terminal=1). As a fallback, detect a terminal embed
// by referrer so the classic site behavior stays unchanged.
$terminalMode = (isset($_REQUEST['terminal']) && (string) $_REQUEST['terminal'] === '1')
    || (isset($_SERVER['HTTP_REFERER']) && strpos((string) $_SERVER['HTTP_REFERER'], '/terminal/') !== false);
$name             = $_GET['contact_name'];
$return_addr      = $_GET['email_address'];
$type             = $_GET['type'];
$body             = $_GET['message'];

$tv               = $_GET['tv'];

$destination_addr = "root@thunix.net";
$subject          = "Contact Form";
$mailbody         = "The following submission via the contact form was recieved:

Real Name:      $name
Type:           $type
Message:        $body";

if ( $tv != "tildeverse" ) {
    print "Spam attempt";
    $redirect = $terminalMode
        ? $site_root . "/terminal/view.php?page=success1"
        : $site_root . "/?page=success1";
    header("Location: $redirect");
    die();
}

shell_exec("echo '$mailbody' | /usr/bin/mail -s '$subject' -r '$return_addr' $destination_addr ");

// In the future, here, we *should* be able to build a process that 
// auto opens an issue in the tildegit project

$redirect = $terminalMode
    ? $site_root . "/terminal/view.php?page=success2"
    : $site_root . "/?page=success2";
header("Location: $redirect");
die()

?>
