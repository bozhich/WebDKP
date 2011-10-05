<?php
include_once("lib/dkp/dkpGuild.php");
include_once("lib/dkp/dkpSettings.php");
include_once("lib/dkp/dkpUploader.php");

//get post data
$event = util::getData("event");
$username = util::getData("user");
$password = util::getData("password");

if($event == "" && $username == "" && $password == "") {
	echo("Did you get here by mistake? This page is intended for client uploads only.");
	die();
}

//load the user
$user = new user();
$user->loadFromDatabaseByUser($username);
//make sure user exists
if( $user->id == "" || $user->username == "") {
	echo("UserError");
	die();
}
//make sure the password is ok
$ok = $user->passwordValid($password);
if( !$ok ) {
	echo("UserErrorPassword");
	die();
}
//store the user in the global site user object
$GLOBALS["siteUser"] = $user;

//now to upload their data
if (!ini_get("safe_mode"))
  set_time_limit(0);

$uploader = new dkpUploader();
$result = $uploader->UploadLog($user->guild, true);

echo($result);
die();

?>