<?php
/*===========================================================
CLASS DESCRIPTION
=============================================================
Class Description should be placed here.
*/
include_once("dkpUser.php");
class dkpAward {
	/*===========================================================
	MEMBER VARIABLES
	============================================================*/
	var $id;
	var $tableid = 1;
	var $guild = 0;
	var $points = 0;
	var $reason = "";
	var $location = "WebDKP";
	var $awardedby = "Unknown";
	var $dateDate;
	var $dateTime;
	var $foritem = 0;
	var $playercount  = 0;
	var $transfer = 0;
	var $zerosumauto = 0;
	var $linked = 0;
	var $players = array();	//array of players with award. Filled with dkp_users
							//after loadPlayers() is called.
	const tablename = "dkp_awards";
	/*===========================================================
	DEFAULT CONSTRUCTOR
	============================================================*/
	function dkpAward()
	{
		$this->tablename = dkpAward::tablename;
	}
	/*===========================================================
	loadFromDatabase($id)
	Loads the information for this class from the backend database
	using the passed string.
	============================================================*/
	function loadFromDatabase($id)
	{
		global $sql;
		$row = $sql->QueryRow("SELECT * FROM $this->tablename WHERE id='$id'");
		$this->loadFromRow($row);
	}
	/*===========================================================
	loadFromDetails($id)
	Attempts to load details for an award given details about it
	============================================================*/
	function loadFromDetails($guildid, $tableid, $reason, $date){
		global $sql;
		$guildid = sql::Escape($guildid);
		$tableid = sql::Escape($tableid);
		$reason = sql::Escape($reason);
		$date = sql::Escape($date);
		$row = $sql->QueryRow("SELECT * FROM $this->tablename
							   WHERE guild='$guildid' AND tableid='$tableid' AND reason='$reason' AND date='$date'");
		$this->loadFromRow($row);
	}
	/*===========================================================
	loadFromRow($row)
	Loads the information for this class from the passed database row.
	============================================================*/
	function loadFromRow($row)
	{
		$this->id=$row["id"];
		if(isset($row["awardid"]))
			$this->id = $row["awardid"];
		if(isset($row["historyid"]))
			$this->historyid = $row["historyid"];
		$this->tableid = $row["tableid"];
		$this->guild = $row["guild"];
		$this->playercount = $row["playercount"];
		$this->points = $row["points"];
		$this->points = str_replace(".00", "", $this->points);
		$this->reason = $row["reason"];
		$this->location = $row["location"];
		$this->awardedby = $row["awardedby"];
		$this->date = $row["date"];
		if($row["date"]!="")
		{
			$this->dateDate = date("F j, Y", strtotime($row["date"]));
			$this->dateTime = date("g:i A", strtotime($row["date"]));
		}
		$this->foritem = $row["foritem"];
		$this->transfer = $row["transfer"];
		$this->zerosumauto = $row["zerosumauto"];
		$this->linked = $row["linked"];
	}
	/*===========================================================
	save()
	Saves data into the backend database using the supplied id
	============================================================*/
	function save()
	{
		global $sql;
		$reason = sql::Escape($this->reason);
		$location = sql::Escape($this->location);
		$awardedby = sql::Escape($this->awardedby);
		$date = sql::Escape($this->date);
		$transfer = sql::Escape($this->transfer);
		$zerosumauto = sql::Escape($this->zerosumauto);
		$linked = sql::Escape($this->linked);
		$sql->Query("UPDATE $this->tablename SET
					tableid = '$this->tableid',
					guild = '$this->guild',
					playercount = '$this->playercount',
					points = '$this->points',
					reason = '$reason',
					location = '$location',
					awardedby = '$awardedby',
					foritem = '$this->foritem',
					date ='$date',
					transfer = '$transfer',
					zerosumauto = '$zerosumauto',
					linked = '$linked'
					WHERE id='$this->id'");
	}
	/*===========================================================
	saveNew()
	Saves data into the backend database as a new row entry. After
	calling this method $id will be filled with a new value
	matching the new row for the data
	============================================================*/
	function saveNew()
	{
		global $sql;
		$reason = sql::Escape($this->reason);
		$location = sql::Escape($this->location);
		$awardedby = sql::Escape($this->awardedby);
		$date = sql::Escape($this->date);
		$transfer = sql::Escape($this->transfer);
		$zerosumauto = sql::Escape($this->zerosumauto);
		$linked = sql::Escape($this->linked);
		$sql->Query("INSERT INTO $this->tablename SET
					tableid = '$this->tableid',
					guild = '$this->guild',
					playercount = '$this->playercount',
					points = '$this->points',
					reason = '$reason',
					location = '$location',
					awardedby = '$awardedby',
					foritem = '$this->foritem',
					date = '$this->date',
					transfer = '$transfer',
					zerosumauto = '$zerosumauto',
					linked = '$linked'
					");
		$this->id=$sql->GetLastId();
	}
	/*===========================================================
	delete()
	Deletes the row with the current id of this instance from the
	database
	============================================================*/
	function delete()
	{
		global $sql;
		$sql->Query("DELETE FROM $this->tablename WHERE id = '$this->id'");
	}
	/*===========================================================
	exists()
	STATIC METHOD
	Returns true if the given entry exists in the database
	database
	============================================================*/
	function exists($id)
	{
		global $sql;
		$name = sql::escape($name);
		$tablename = dkpAward::tablename;
		$exists = $sql->QueryItem("SELECT id FROM $tablename WHERE id='$name'"); //MODIFY THIS LINE
		return ($exists != "");
	}

	/*===========================================================
	timestamp()
	Applies a timestamp to the award
	============================================================*/
	function timestamp() {
		global $sql;
		$table = $this->tablename;
		$sql->Query("UPDATE $table SET date=NOW() WHERE id='$this->id'");
	}

	/*===========================================================
	loadPlayers()
	Loads all players with this award and stores them in the
	$players member variable
	============================================================*/
	function loadPlayers(){
		global $sql;

		$awardid = sql::Escape($this->id);

		$result = $sql->Query("SELECT *, dkp_users.id as userid
		 					   FROM dkp_pointhistory, dkp_users
							   WHERE dkp_pointhistory.award = '$awardid'
							   AND dkp_pointhistory.user = dkp_users.id
							   ORDER BY dkp_users.name ASC");
		$this->players = array();
		while($row = mysql_fetch_array($result)) {
			$player = new dkpUser();
			$player->loadFromRow($row);
			$this->players[] = $player;
		}

		return $this->players;
	}
	/*===========================================================
	loadPlayer()
	Returns a single player that recieved this award. This is only
	helpful if the award is for an item (or a transfer), in which case only
	one person should have it anyways.
	============================================================*/
	function loadPlayer(){
		$awardid = sql::Escape($this->id);
		global $sql;
		$row = $sql->QueryRow("SELECT *, dkp_users.id as userid
		 					   FROM dkp_pointhistory, dkp_users
							   WHERE dkp_pointhistory.award = '$awardid'
							   AND dkp_pointhistory.user = dkp_users.id
							   ORDER BY dkp_users.name ASC LIMIT 1");

		$player = new dkpUser();
		$player->loadFromRow($row);

		$this->player = $player;

		return $player;
	}
	/*===========================================================
	getPlayerids()
	Returns an array of playerids of all players with this award.
	============================================================*/
	function getPlayerids(){
		$this->loadPlayers();
		$playerids = array();
		foreach($this->players as $player)
			$playerids[] = $player->id;
		return $playerids;
	}

	/*===========================================================
	calculatePlayerCount()
	Updates this awads current player could field by querying the
	database to see who all refers to it. You MUST call SAVE afterwards
	to save this new value;
	============================================================*/
	function calculatePlayerCount(){
		if($this->id == "")
			return;

		global $sql;
		$awardid = sql::Escape($this->id);
		$count = $sql->QueryItem("SELECT count(*) as total
								  FROM dkp_pointhistory
								  WHERE award='$awardid'
								  AND user != 0");

		$this->playercount = $count;
	}

	/*===========================================================
	setupTable()
	Checks to see if the classes database table exists. If it does not
	the table is created.
	============================================================*/
	function setupTable()
	{
		if(!sql::TableExists(dkpAward::tablename)) {
			$tablename = dkpAward::tablename;
			global $sql;
			$sql->Query("CREATE TABLE `$tablename` (
						`id` INT NOT NULL AUTO_INCREMENT ,
						`tableid` INT NOT NULL,
						`guild` INT NOT NULL,
						`playercount` INT NOT NULL,
						`points` DECIMAL(11,2) NOT NULL,
						`reason` VARCHAR (128) NOT NULL,
						`location` VARCHAR (64) NOT NULL,
						`awardedby` VARCHAR (32) NOT NULL,
						`date` DATETIME NOT NULL,
						`foritem` INT NOT NULL DEFAULT '0',
						`transfer` INT (1) NOT NULL DEFAULT '0',
						`zerosumauto` INT (1) NOT NULL DEFAULT '0',
						`linked` INT NOT NULL DEFAULT '0',
						PRIMARY KEY ( `id` ),
						KEY `key_date` (`guild`,`tableid`,`date`),
            KEY `key_points` (`guild`,`tableid`,`points`),
            KEY `key_reason` (`guild`,`tableid`,`reason`)
						) ENGINE = innodb;");
		}
	}
}
dkpAward::setupTable()
?>
