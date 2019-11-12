<?php
// This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)
include "../config.php";

$name             = $_GET['contact_name'];
$email            = $_GET['email_address'];
$username         = $_GET['username'];
$interest         = $_GET['interest'];
$pubkey           = $_GET['pubkey'];
$tv               = $_GET['tv'];

$from 		  = 'From: www-data <www-data@thunix.net>';
$destination_addr = "newuser@thunix.net";
$subject          = "New User Registration";
$mailbody         = "A new user has tried to register.
Username:       $username
Real Name:      $name
Email Address:  $email
Interest:       $interest
Pubkey:         $pubkey";

if ( $tv != "tildeverse" ) {
    print "Spam attempt";
    header("Location: $site_root/?page=success1");
    die();
}

mail($destination_addr, $subject, $mailbody, $from);

// In the future, here, we *should* be able to build a process that 
// somehow auto-verifies the user, and instead of email, it'll kick off the new user process here

header("Location: $site_root/?page=success2");

die();

?>
