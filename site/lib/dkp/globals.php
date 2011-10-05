<?php
include_once("dkpSettings.php");
include_once("dkpGuild.php");

$settings = new dkpSettings();
$GLOBALS["settings"] = $settings;
$guildid = $settings->GetGuild();
if($guildid == 0) {
	$GLOBALS["guildset"] = false;
}
else {
	$GLOBALS["guildset"] = true;
	$guild = new dkpGuild();
	$guild->loadFromDatabase($guildid);
	$GLOBALS["guildid"] = $guild->id;
	$GLOBALS["guild"] = $guild;
}

?>