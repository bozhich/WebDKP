<?php
/*===========================================================
CLASS DESCRIPTION
=============================================================
Performs setup operations for the site on its first installation.
*/

class setup
{
	/*===========================================================
	MEMBER VARAIBLES
	============================================================*/

	/*===========================================================
	DEFAULT CONSTRUCTOR
	============================================================*/
	function setup()
	{

	}

	/*===========================================================
	STATIC METHOD
	Runs first time setup procedures for the site. This is to be
	called when the site has first been installed

	Note: Table structures would have already been created by this
	point and filled with some default values. This is always done
	in the class that wraps that particular table. For example,
	the page class creates the page table, the siteStatus class creates
	the site_status table, etc.

	The setup function's main responsibility
	is to create any basic pages and put default parts on them.
	============================================================*/
	function run(){
		//make sure we know of all themes
		themeLibrary::scanForThemes();

		//setup a default site status
		unset($siteStatus);
		$siteStatus = new siteStatus();
		$siteStatus->load();
		if($siteStatus->id == "") {
			$siteStatus->defaultTheme = theme::getThemeIdBySystemName("default");
			$siteStatus->setup = 0;
			$siteStatus->saveNew();
			$siteStatus->load();
		}

		//setup default user groups
		unset($usergroup);
		$usergroup = new userGroup();
		$usergroup->name = "Visitor";
		$usergroup->visitor = 1;
		$usergroup->system = 0;
		$usergroup->saveNew();

		unset($usergroup);
		$usergroup = new userGroup();
		$usergroup->name = "User";
		$usergroup->default = 1;
		$usergroup->system = 0;
		$usergroup->saveNew();

		$usergroup->name = "Admin";
		$usergroup->system = 1;
		$usergroup->saveNew();

		//set flag that setup is complete.
		$siteStatus->setup = 1;
		$siteStatus->save();
	}
}
?>