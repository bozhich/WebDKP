<?php
include_once("dkpmain.php");
class pageIndex extends pageDkpMain {
	var $layout = "Columns1";
	var $pageurl = "";
	/*=================================================
	Shows a list of posts to the user. The user has
	links to skip to any page of the posts
	=================================================*/
	function area2()
	{
    util::forward($GLOBAL["SiteRoot"]."Dkp");
	}
}
?>