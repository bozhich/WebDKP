<?php
include_once("lib/dkp/dkpUtil.php");
include_once("lib/dkp/dkpUserPermissions.php");
/*=================================================
The news page displays news to the user.
=================================================*/
class pageSetup extends page {

	var $layout = "Columns1";
	var $pagetitle = "Setup";
	/*=================================================
	Shows a list of posts to the user. The user has
	links to skip to any page of the posts
	=================================================*/
	function area2()
	{
		global $guildset;
		$this->title = "Setup WebDKP";
		$this->border = 1;

		if($guildset) {
			return "Setup has already been run. You can change these settings from your control panel instead.";
		}

		$servers = dkpUtil::GetServerList();

		$this->set("servers",$servers);

		$this->set("username",util::getData("username"));
		$this->set("guild",util::getData("guild"));
		$this->set("servername",util::getData("server"));
		$this->set("faction",util::getData("faction"));
		$this->set("email",util::getData("email"));
		return $this->fetch("join/setup.tmpl.php");
		//return $this->fetch("posts.tmpl.php");
	}

	/*=================================================
	Warning for the top right
	=================================================*/
	function area1(){
		$this->border = 0;

		return $this->fetch("join/joinnotes.tmpl.php");
	}

	/*=================================================
	Main event - user submitting the register
	form. This must attempt to regeiser the user
	and create their guild. It will display any
	errors to the user. If successful, users
	are automattically forwarded to the welcome page.
	=================================================*/
	function eventSetup(){
		global $guildset;
		if($guildset) {
			return $this->setEventResult(false, "You have already completed setup. You cannot run setup again.");
		}

		$username = strip_tags(util::getData("username"));
		$password = util::getData("password");
		$password2 = util::getData("password2");
		$guildname = strip_tags(util::getData("guild"));
		$server = util::getData("server");
		$faction = util::getData("faction");
		$email = strip_tags(util::getData("email"));

		if(strpos($guildname, "'")!== false || strpos($guildname,"\"") !== false || strpos($guildname,"/") !== false) {
			return $this->setEventResult(false, "You can not have special characters such as ', \", or / in your guild name.");
		}

		//step 1 - register the account
		$user = new user();
		$result = $user->register($username, $password, $password2);
		if($result != user::REGISTER_OK)
		{
			$this->setEventResult($result, user::getRegisterErrorString($result));
			return;
		}

		//step 3 - create the guild (or set a preknown guilds claim flag to true)
		if (dkpGuild::Exists($guildname, $server)) {
			$guild = new dkpGuild();
			$guild->loadFromDatabaseByName($guildname, $server);
			$guild->claimed = 1;
			$guild->save();
		}
		else {
			$guild = new dkpGuild();
			$guild->name = $guildname;
			$guild->server = $server;
			$guild->faction = $faction;
			$guild->claimed = 1;
			$guild->saveNew();
		}

		//load default settings for this guild
		$settings = new dkpSettings($guild->id);
		$settings->LoadDefaultSettings();
		$settings->SetGuild($guild->id);

		//make the new user an admin for the guild
		$permissions = new dkpUserPermissions($user->id);
		$permissions->user = $user->id;
		$permissions->isAdmin = 1;
		if($permissions->id == "")
			$permissions->saveNew();
		else
			$permissions->save();

		//if they are currently logged in as a different user, log them out
		global $siteUser;
		$siteUser->logout();

		//update the user account with the new guild id
		$user->guild = $guild->id;
		$user->email = $email;
		$user->usergroup = userGroup::getUserGroupIdByName("Admin");
		$user->save();
		$user->setCookie();

		global $SiteRoot;
		util::forward($SiteRoot."Welcome");
	}
}
?>