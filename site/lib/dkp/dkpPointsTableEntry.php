<?php
/*===========================================================
CLASS DESCRIPTION
=============================================================
Contains a single entry within a dkp table. This contains
the current dkp points for a single person within a table.
This will also contain lifetime dkp information
*/
include_once("dkpUser.php");
include_once("dkpGuild.php");

class dkpPointsTableEntry {
	/*===========================================================
	MEMBER VARIABLES
	============================================================*/
	var $id;			//unique id for this entry
	var $guild;			//the guild that this dkp entry is for
	var $tableid;		//the dkp table that this entry is for (unique within a guild)
	var $user;			//the id of a user show this entry is for
	var $points;		//The number of points
	var $lifetime;		//the lifetime dkp for the person in this table
	const tablename = "dkp_points";
	/*===========================================================
	DEFAULT CONSTRUCTOR
	============================================================*/
	function dkpPointsTableEntry()
	{
		$this->tablename = dkpPointsTableEntry::tablename;
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
	loadFromDatabaseByGuild($id)
	Loads information for a single entry
	============================================================*/
	function loadFromDatabaseByGuild($guildid, $playerid, $tableid = 1) {
		global $sql;
		$usertable = dkpUser::tablename;
		$pointstable = $this->tablename;
		$guildid = sql::Escape($guildid);
		$playerid = sql::Escape($playerid);
		$tableid = sql::Escape($tableid);

		$row = $sql->QueryRow("SELECT *,
							   $usertable.id as userid,
							   $pointstable.id as pointsid,
							   $pointstable.guild as pointsguildid
							   FROM $usertable, $pointstable
							   WHERE  $pointstable.tableid='$tableid'
							   AND $usertable.id='$playerid'
							   AND $pointstable.guild='$guildid'
							   AND $pointstable.user=$usertable.id");
		$this->loadFromRow($row);
	}
	/*===========================================================
	loadGuildDetails()
	Replaces the $this->guild variable with an actual dkpGuild instance
	containting degailed information about the guild
	============================================================*/
	function loadGuildDetails(){
		if(is_a($this->guild,"dkpGuild"))
			return;
		unset($guild);
		$guild = new dkpGuild();
		$guild->loadFromDatabase($this->guild);
		$this->guild = $guild;
	}
	/*===========================================================
	loadFromRow($row)
	Loads the information for this class from the passed database row.
	============================================================*/
	function loadFromRow($row)
	{
		$this->id=$row["id"];
		if($row["pointsid"])
			$this->id = $row["pointsid"];
		$this->guild = $row["guild"];
		if($this->guild=$row["pointsguildid"])
			$this->guild = $row["pointsguildid"];
		$this->tableid = $row["tableid"];
		$this->user = $row["user"];
		$this->points = $row["points"];
		$this->points = str_replace(".00", "", $this->points);
		$this->lifetime = $row["lifetime"];
		$this->lifetime = str_replace(".00", "", $this->lifetime);

		//load the user. We may have enough data to load the full user instance
		//(in the case of a compound query) or we may only have the userid
		if($row["userid"]!="") {
			$this->user = null;
			$this->user = new dkpUser();
			$this->user->loadFromRow($row);
			$this->user->id = $row["userid"];
		}
		//no compound query... just load it as an id
		else {
			$this->user=$row["user"];
		}
	}
	/*===========================================================
	save()
	Saves data into the backend database using the supplied id
	============================================================*/
	function save()
	{
		global $sql;
		if(is_a($this->user,"dkpUser"))
			$userid = $this->user->id;
		else
			$userid = $this->user;

		$sql->Query("UPDATE $this->tablename SET
					guild = '$this->guild',
					tableid = '$this->tableid',
					user = '$userid',
					points = '$this->points',
					lifetime = '$this->lifetime'
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
		if(is_a($this->user,"dkpUser"))
			$userid = $this->user->id;
		else
			$userid = $this->user;

		$sql->Query("INSERT INTO $this->tablename SET
					guild = '$this->guild',
					tableid = '$this->tableid',
					user = '$userid',
					points = '$this->points',
					lifetime = '$this->lifetime'
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
	function exists($name)
	{
		global $sql;
		$name = sql::escape($name);
		$tablename = dkpPointsTableEntry::tablename;
		$exists = $sql->QueryItem("SELECT id FROM $tablename WHERE id='$name'"); //MODIFY THIS LINE
		return ($exists != "");
	}
	/*===========================================================
	setupTable()
	Checks to see if the classes database table exists. If it does not
	the table is created.
	============================================================*/
	function setupTable()
	{
		if(!sql::TableExists(dkpPointsTableEntry::tablename)) {
			$tablename = dkpPointsTableEntry::tablename;
			global $sql;
			$sql->Query("CREATE TABLE `$tablename` (
						`id` INT NOT NULL AUTO_INCREMENT ,
						`guild` INT NOT NULL,
						`tableid` INT NOT NULL,
						`user` INT NOT NULL,
						`points` DECIMAL(11,2) NOT NULL,
						`lifetime` DECIMAL(11,2) NOT NULL,
						PRIMARY KEY ( `id` ),
            KEY `user` (`guild`,`tableid`,`user`),
            KEY `points` (`guild`,`tableid`,`points`),
            KEY `lifetime` (`guild`,`tableid`,`lifetime`)
						) ENGINE = innodb;");
		}
	}
}
dkpPointsTableEntry::setupTable();
?>
