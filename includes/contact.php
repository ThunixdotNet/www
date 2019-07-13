<?php
include "../config.php";

$name             = $_GET['contact_name'];
$email            = $_GET['email_address'];
$subject          = $_GET['type'];
$body             = $_GET['message'];

$tv               = $_GET['tv'];

$destination_addr = "ubergeek@thunix.net";
$subject          = "Contact Form";
$mailbody         = "The following submission via the contact form was recieved:

Real Name:      $name
Email Address:  $email
Type:           $subject
Message:        $body";

if ( $tv != "tildeverse" ) {
    print "Spam attempt";
    header("Location: $site_root/?page=success1");
    die();
}

shell_exec("echo '$mailbody' | /usr/bin/mail -s '$subject' $destination_addr ");

// In the future, here, we *should* be able to build a process that 
// somehow auto-verifies the user, and instead of email, it'll kick off the new user process here

header("Location: $site_root/?page=success2");
die()

?>