<?php
/*=================================================
The news page displays news to the user.
=================================================*/
class pageWelcome extends page {

	var $layout = "Columns1";
	var $pagetitle = "Welcome";
	/*=================================================
	Shows a list of posts to the user. The user has
	links to skip to any page of the posts
	=================================================*/
	function area2()
	{
		global $guildset;
		$this->title = "Welcome";
		$this->border = 1;

		$guildurl = dkpUtil::GetGuildUrl($siteUser->guild)."Admin";
		$this->set("guildurl",$guildurl);
		return $this->fetch("join/welcome.tmpl.php");
	}
}
?>