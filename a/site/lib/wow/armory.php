<?php
/**
 * Downloads player statistics for a guild from the World of Warcraft
 * Armory.
 *
 * @author Scott Bailey (scott@zeddic.com)
 */

include_once("lib/dkp/dkpGuild.php");
include_once("lib/dkp/dkpUser.php");
include_once("lib/dkp/dkpUtil.php");

class armory {
	const americanUrl = "http://www.wowarmory.com/";
	const euroUrl = "http://armory.wow-europe.com/";

	const AMERICAN = 0;
	const EURO = 1;

  /**
   * Returns an array of players in a guild with the given ID from the armory.
   */
	function GetPlayersInGuild($guild, $armorySite = armory::AMERICAN){
		if(!is_a($guild,"dkpGuild")) {

			$guildid = $guild;
			$guild = new dkpGuild();
			$guild->loadFromDatabase($guildid);
			if($guild->id == "")
				return array();
		}

		return armory::GetPlayersInGuildByName($guild->name, $guild->server, $armorySite);
	}

  /**
   * Returns a URL that can be used to download stats for a guild with the
   * given id.
   */
	function GetArmoryUrl($guild, $armorySite = armory::AMERICAN){
		if(!is_a($guild,"dkpGuild")) {

			$guildid = $guild;
			$guild = new dkpGuild();
			$guild->loadFromDatabase($guildid);
			if($guild->id == "")
				return "http://www.wowarmory.com";
		}
		return armory::GetUrlByName($guild->name, $guild->server, $armorySite);
	}

  /**
   * Returns a URL that can be used to download status for a guild with the
   * given name.
   */
	function GetArmoryUrlByName($guildname, $server, $armorySite = armory::AMERICAN){
		if($armorySite == armory::AMERICAN)
			$base = armory::americanUrl;
		else
			$base = armory::euroUrl;

		$server = stripslashes($server);
		$guildname = stripslashes($guildname);
		$url = $base . "guild-info.xml?r=".urlencode(trim($server))."&n=".urlencode($guildname);

		return $url;
	}

  /**
   * Returns an array of all players that belong to the guild on the given
   * server.
   */
	function GetPlayersInGuildByName($guildname, $server, $armorySite = armory::AMERICAN ) {

		$toReturn = array();

		$url = armory::GetArmoryUrlByName($guildname, $server, $armorySite);
		$xml = armory::GetUrl($url);
		$xml = utf8_encode($xml);

		if($xml == "")
			return $toReturn;

		$doc = new DOMDocument();
		$doc->loadXML($xml);

    // XML files from the armory track classes by ID.
    // This array maps ID to the class name.
		$classId2class = array(
			1 => 'Warrior',
			2 => 'Paladin',
			3 => 'Hunter',
			4 => 'Rogue',
			5 => 'Priest',
			6 => 'Death Knight',
			7 => 'Shaman',
			8 => 'Mage',
			9 => 'Warlock',
			11 => 'Druid'
		);

		$members = $doc->getElementsByTagName("character");
		foreach($members as $member) {

			$name = utf8_decode($member->getAttribute("name"));
			$level = $member->getAttribute("level");
			$class = $classId2class[$member->getAttribute("classId")];

			$user = new dkpUser();
			$user->name = $name;
			$user->class = $class;
			$user->level = $level;
			$user->guild = $guildname;
			$toReturn[] = $user;
		}

		return $toReturn;
	}

  /**
   * Fetchs raw data from the provided URL using CURL.
   */
	function GetUrl($url){
		$ch = curl_init($url);
		$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_USERAGENT, $useragent);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
?>