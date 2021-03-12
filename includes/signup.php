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
  exec("id $username 2>&1", $null, $retval);
  if($retval == 0)
    $success = 'success3';

  // Check SSH public key format:
  exec("echo $pubkey | ssh-keygen -l -f - 2>&1", $null, $retval);
  if($retval != 0)
    $success = 'success4';

  if ( $success == "success2" )
  {
    mail($destination_addr, $subject, $mailbody, $from);
    $fp = fopen($user_queue, 'a');
    fwrite($fp, "'$username','$email','$pubkey'\n");
    fclose($fp);
  }
}

header("Location: $site_root/?page=$success");
die();

?>
