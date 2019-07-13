<?php
include "../config.php";

$name             = $_GET['contact_name'];
$email            = $_GET['email_address'];
$username         = $_GET['username'];
$interests        = $_GET['interest'];
$pubkey           = $_GET['pubkey'];
$tv               = $_GET['tv'];

$destination_addr = "ubergeek@thunix.net";
$subject          = "New User Registration";
$mailbody         ="A new user has tried to register.
Username:       $username
Real Name:      $name
Email Address:  $email
Interest:       $interest
Pubkey:         $pubkey";

if ( $tv != "tildeverse" ) {
    print "Spam attempt";
    header("Location: $site_root/success1");
    die();
}

shell_exec("echo $mailbody | /usr/bin/mail -s 'New User Registration' $destination_addr ");

// In the future, here, we *should* be able to build a process that 
// somehow auto-verifies the user, and instead of email, it'll kick off the new user process here

header("Location: $site_root/success2");

die();

?>
