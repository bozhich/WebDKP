<?php
include_once("adminmain.php");
include_once("lib/dkp/dkpAccountUtil.php");
include_once("lib/dkp/dkpUtil.php");
/*=================================================

=================================================*/
class pageUpdateGuild extends pageAdminMain {

	/*=================================================

	=================================================*/
	function area2()
	{
		global $sql;

		$this->title = "Update Guild Settings";
		$this->border = 1;

		if($this->getData("update")) {
			$this->setEventResult(true,"Guild Updated!");
		}

		return $this->fetch("updateguild.tmpl.php");
	}

	function eventUpdateGuild(){

    if (!ini_get("safe_mode"))
		  set_time_limit(0);

		//get post data
		$name = util::getData("name");
		$servername = util::getData("server");
		$faction = util::getData("faction");

		if(strpos($name, "'")!== false || strpos($name,"\"") !== false || strpos($name,"/") !== false) {
			return $this->setEventResult(false, "You can not have special characters such as ', \", or / in your guild name.");
		}

		//perform the update
		$result = dkpAccountUtil::UpdateGuild($this->guild->id, $name, $servername, $faction);

		//load our updated guild. If the guild name or server our url we change
		//For example, we would change from dkp/Stormscale/Totus+Solus to dkp/NewServer/NewName
		$updatedGuild = new dkpGuild();
		$updatedGuild->loadFromDatabase($this->guild->id);
		//check to see if there was a change
		if($updatedGuild->name != $this->guild->name || $updatedGuild->server != $this->guild->server) {
			//yes, change to our url, generate a new url and forward to it immediatly
			$serverUrlName = str_replace(" ","+",$updatedGuild->server);
			$guildUrlName = str_replace(" ","+",$updatedGuild->name);

			//forward!
			util::forward($this->baseurl."Admin/UpdateGuild?update=true");
			die();
		}

		//check if the update was ok
		if($result != dkpAccountUtil::UPDATE_OK)
			$this->setEventResult(false, dkpAccountUtil::GetErrorString($result));
		else
			$this->setEventResult(true,"Guild Updated!");
	}
}
?>