<?php
// This code is licensed under the AGPL 3 or later by ubergeek (https://tildegit.org/ubergeek)
include "../config.php";

$name             = $_GET['contact_name'];
$email            = $_GET['email_address'];
$username         = $_GET['username'];
$interest         = $_GET['interest'];
$pubkey           = $_GET['pubkey'];
$tv               = $_GET['tv'];

// username passed lowercased
$username = strtolower($username);

// strip new line characters from the end
$pubkey = trim($pubkey);

$from 		  = 'From: www-data <www-data@thunix.net>';
$destination_addr = "newuser@thunix.net";
$subject          = "New User Registration";
$mailbody         = "A new user has tried to register.
Username:       $username
Real Name:      $name
Email Address:  $email
Interest:       $interest
Pubkey:         $pubkey";

// In the future, here, we *should* be able to build a process that 
// somehow auto-verifies the user, and instead of email, it'll kick off the new user process here

$user_queue       = '/dev/shm/userqueue';

// Spam attempt
$success = 'success1';
if ( $tv == "tildeverse" )
{
  // Success!
  $success = 'success2';
  
// Check if username already taken
if (posix_getpwnam($username)) {
    $success = 'success3';
}

// Simple SSH public key format check
$valid_key_starts = ['ssh-rsa', 'ssh-dss', 'ecdsa-sha2', 'ssh-ed25519'];
$key_parts = explode(' ', $pubkey, 3);
if (!in_array($key_parts[0], $valid_key_starts) || count($key_parts) < 2) {
    $success = 'success4';
}

if ($success == "success2") {
    mail($destination_addr, $subject, $mailbody, $from);
    $fp = fopen($user_queue, 'a');
    fwrite($fp, "'$username','$email','$pubkey'\n");
    fclose($fp);
}
}

header("Location: $site_root/?page=$success");
die();

?>