<?php
/*===========================================================
CLASS DESCRIPTION
=============================================================

*/

include_once("dkpAward.php");
include_once("dkpGuild.php");
include_once("dkpUser.php");
include_once("dkpPointsTable.php");
include_once("dkpPointsHistoryTable.php");
include_once("dkpServer.php");
include_once("loottable/dkpLootTable.php");

class dkpAward2 extends dkpPointsHistoryTableEntry{
  var $newPlayer = false;
  function dkpAward(){
    global $siteUser;
    $this->date = "";
    $this->points = 0;
    $this->foritem = false;
    $this->awardedby = $siteUser->name;
    $this->location = "WebDKP";
  }
}


class dkpUtil {

  function GetGuildUrl($guildid){
    $guild = new dkpGuild();
    $guild->loadFromDatabase($guildid);
    return dkpUtil::GetGuildUrlByName($guild->name, $guild->server);
  }

  function GetGuildUrlByName($guildname, $server){
    global $siteRoot;

    $server = ( str_replace(" ","+",$server) );
    $name =  ( str_replace(" ","+",$guildname) );

    if(strpos($name,"/") !== false  ) {
      $name = str_replace("/", "%252f", $name );
    }
    if(strpos($name,"'")!== false)
      $name = urlencode($name);

    $url = $siteRoot;//."dkp/".$server."/".$name."/";

    return $url;
  }

  function GetGuild($guildname, $server){
    global $sql;

    $guildEscaped = sql::Escape($guildname);
    $serverEscaped = sql::Escape($server);

    $row = $sql->QueryRow("SELECT * FROM dkp_guilds WHERE gname = '$guildEscaped' AND gserver='$serverEscaped'");
    if($sql->a_rows == 0 )
      return null;

    $guild = new dkpGuild();
    $guild->loadFromRow($row);

    return $guild;
  }

  /*===========================================================
  Deletes a guild, all history, all settings, all loot tables,
  all users, etc.
  ============================================================*/
  function DeleteGuild($guildid){
    global $sql;

    $guildid = sql::Escape($guildid);

    //delete points
    $sql->Query("DELETE FROM dkp_points WHERE guild='$guildid'");

    //delete point history
    $sql->Query("DELETE FROM dkp_pointhistory USING dkp_awards, dkp_pointhistory
             WHERE dkp_awards.guild = '$guildid'
           AND dkp_pointhistory.award = dkp_awards.id");

    //delete awards
    $sql->Query("DELETE FROM dkp_awards WHERE dkp_awards.guild = '$guildid' AND dkp_awards.tableid='$tableid'");

    //now to delete the loot tables
    $result = $sql->Query("SELECT * FROM dkp_loottable WHERE guild='$guildid'");
    while($row = mysql_fetch_array($result)) {
      //iterate through all the users loot tables
      $lootid = $row["id"];

      //delete the sections
      $sql->Query("DELETE FROM dkp_loottable_section WHERE loottable = '$lootid'");

      //delete the loot
      $sql->Query("DELETE FROM dkp_loottable_data WHERE loottable = '$lootid'");

      //and delete the table
      $sql->Query("DELETE FROM dkp_loottable WHERE id = '$lootid'");
    }

    //now to delete the users who were managing it
    $sql->Query("DELETE FROM security_users WHERE guild='$guildid'");

    //open the guild so others can claim it
    $sql->Query("UPDATE dkp_guilds SET claimed='0' WHERE id='$guildid'");
  }


  /*===========================================================
  Returns a guild instance matching the given criteria. If a guild exists in the database
  with this data, that information is loaded. If it does not exist in the database,
  a new entry is generated and returned.
  $guild = A guild name. Does not not need to be slashed ("Totus Solus", etc.)
  $server = A server name. ("Stormscale", "Etc.")
  $faction = A faction name. ("Horde" "Alliance")
  ============================================================*/
  function EnsureGuildExists($guild, $server, $faction){
    global $sql;

    $guild = trim($guild);
    $serverSlashed = sql::Escape($server);
    $guildSlashed = sql::Escape($guild);

    //player doesn't exist. How about their guild?
    $row = $sql->QueryRow("SELECT * FROM dkp_guilds WHERE  gname = '$guildSlashed' AND gserver='$serverSlashed'");
    if($sql->a_rows == 0) {
      $this->message.="New guild found - adding $guild at $server - $faction<br />";
      //guild doesn't exist either, lets create that
      unset($newGuild);
      $newGuild = new dkpGuild();
      $newGuild->name = $guild;
      $newGuild->server = $server;
      $newGuild->faction = $faction;
      $newGuild->claimed = 0;
      //save the new guild entry
      $newGuild->saveNew();
      //grab its id so we can store it with the new player
      $toReturn = $newGuild;
    }
    else {
      unset($toReturn);
      $toReturn = new dkpGuild();
      $toReturn->loadFromRow($row);
    }
    return $toReturn;
  }

  function CreatePlayer($name, $class, $guildid, $server, $faction){
    $name = trim($name);
    $server = $server;
    $faction = $faction;

    unset($player);
    $player = new dkpUser();
    $player->name = $name;
    $player->class = $class;
    $player->server = $server;
    $player->faction = $faction;
    $player->server = $server;
    $player->guild = $guildid;
    $player->saveNew();

    return $player;
  }

  function PlayerExists($name, $server){
    global $sql;

    $server = sql::Escape(trim($server));
    $name = sql::Escape(trim($name));

    $sql->QueryRow("SELECT * FROM dkp_users WHERE name = '$name' AND server='$server'");

    return $sql->a_rows != 0;
  }

  function GetPlayer($name, $server){
    global $sql;
    $server = sql::Escape($server);
    $name = sql::Escape(trim($name));
    $row = $sql->QueryRow("SELECT * FROM dkp_users WHERE name = '$name' AND server='$server'");

    unset($toReturn);
    $toReturn = new dkpUser();
    $toReturn->loadFromRow($row);

    return $toReturn;
  }

  /*===========================================================
  Returns a player instance matching the given criteria. If a player exists in the database
  with this data, that information is loaded. If it does not exist in the database,
  a new entry is generated and returned.
  $name = Name of player
  $class = Name of class (first letter must be upper case)
  $guildid = The id of the guild they belong to (use getGuild() if creating from scratch)
  $server = The name of the server ("Stormscale")
  $faction = The name of the faction ("Alliance", "Horde")
  ============================================================*/
  function EnsurePlayerExists($name, $class, $guildid, $server, $faction){
    global $sql;

    $server = sql::Escape($server);
    $name = sql::Escape(trim($name));

    $row = $sql->QueryRow("SELECT * FROM dkp_users WHERE name = '$name' AND server='$server'");
    if($sql->a_rows == 0) {
      $this->message.="New player found - adding $name to the database<br />";
      //player doesn't exists
      unset($player);
      $player = new dkpUser();
      $player->name = $name;
      $player->class = $class;
      $player->server = $server;
      $player->faction = $faction;
      $player->server = $server;
      $player->guild = $guildid;
      $player->saveNew();
      $toReturn = $player;
    }
    else {
      unset($toReturn);
      $toReturn = new dkpUser();
      $toReturn->loadFromRow($row);

    }
    return $toReturn;
  }

  function AwardExistsForPlayer($playerid, $award){
    global $sql;

    //first, we need to find the id of the award
    $awardid = $award->id;
    if($awardid == "") {
      $temp = new dkpAward();
      $temp->loadFromDetails($award->guild, $award->tableid , $award->reason , $award->date );
      $awardid = $temp->id;
    }

    //if we still don't know the id of the award, it means it doesn't exist
    //in our database, therefore the user can't possibly have it
    if($temp->id == "")
      return false;

    //if the award exists, check to see if the user has the award assigned to
    //him/her
    $awardid = $temp->id;
    $exists = $sql->QueryItem("SELECT id FROM dkp_pointhistory WHERE award='$awardid'");

    return ($exists != "");
  }

  /*===========================================================
  Returns a server instance with the given name. If one can't be
  found, one is created.
  ============================================================*/
  function EnsureServerExists($servername) {
    $server = new dkpServer();
    $server->loadFromDatabaseByName($servername);
    if ($server->id == "") {
      $server->name = $servername;
      $server->saveNew();
    }
    return $server;
  }

  function GetServerList(){
    global $sql;
    $result = $sql->Query("SELECT * FROM dkp_servers ORDER BY name ASC");
    $servers = array();
    while($row = mysql_fetch_array($result)) {
      $server = new dkpServer();
      $server->loadFromRow($row);
      $servers[] = $server;
    }
    return $servers;
  }



  function IsGuildClaimed($name, $server){
    $guild = new dkpGuild();
    $guild->loadFromDatabaseByName($name, $server);

    return ( $guild->claimed == 1 );
  }

  function GetGuildsOnServer($server){
    global $sql;
    $server = sql::Escape($server);
    $result = $sql->Query("SELECT * FROM dkp_guilds WHERE gserver='$server' AND claimed='1' ORDER BY gname ASC");
    $guilds = array();
    while($row = mysql_fetch_array($result)) {
      $guild = new dkpGuild();
      $guild->loadFromRow($row);
      $guilds[] = $guild;
    }
    return $guilds;
  }

  function GetPopulatedServerList(){
    global $sql;
    $result = $sql->Query("SELECT dkp_servers.name as name,
                    dkp_servers.id as id,
                    COUNT(*) as total
                 FROM dkp_servers, dkp_guilds
                 WHERE dkp_guilds.claimed = 1
                 AND dkp_servers.name = dkp_guilds.gserver
                 GROUP BY dkp_servers.name");

    $servers = array();
    while($row = mysql_fetch_array($result)) {
      $server = new dkpServer();
      $server->loadFromRow($row);
      $server->urlname = str_replace(" ","+",$server->name);
      $server->total = $row["total"];

      $servers[] = $server;
    }

    return $servers;
  }

  function GetPlayerDKP($guildid, $tableid, $userid)
  {
    $userid = sql::Escape($userid);
    $guildid = sql::Escape($guildid);
    $tableid = sql::Escape($tableid);
    global $sql;
    $dkp = $sql->QueryItem("SELECT points FROM dkp_points WHERE guild='$guildid' AND tableid='$tableid' AND user='$userid'");
    return $dkp;
  }

  function GetPlayerHistory($guildid, $tableid, $userid, $sort = "date", $sortorder = "desc", $page = -1, &$maxpage = 0, $rowsPerPage = 50, &$total = 0)
  {
    $userid = sql::Escape($userid);
    $guildid = sql::Escape($guildid);
    $tableid = sql::Escape($tableid);
    $query = "SELECT *, dkp_awards.id as awardid , dkp_pointhistory.id as historyid
           FROM dkp_awards, dkp_pointhistory
          WHERE dkp_pointhistory.user = '$userid'
          AND dkp_pointhistory.guild = '$guildid'
          AND dkp_pointhistory.award = dkp_awards.id
          AND dkp_awards.tableid = '$tableid'";
    $awards = dkpUtil::LoadHistory($query, $sort, $sortorder, $page, $maxpage, $rowsPerPage, $totalBefore);
    $total = dkpUtil::GetPlayerDKP($guildid, $tableid, $userid)-$totalBefore;
    return $awards;
  }

  function GetPlayerLootHistory($guildid, $tableid, $userid)
  {
    $userid = sql::Escape($userid);
    $guildid = sql::Escape($guildid);
    $tableid = sql::Escape($tableid);
    $query = "SELECT *, dkp_awards.id as awardid , dkp_pointhistory.id as historyid
          FROM dkp_awards, dkp_pointhistory
          WHERE dkp_pointhistory.user = '$userid'
          AND dkp_pointhistory.guild = '$guildid'
          AND dkp_pointhistory.award = dkp_awards.id
          AND dkp_awards.tableid = '$tableid'
          AND dkp_awards.foritem = 1";

    return dkpUtil::LoadHistory($query);
  }

  function LoadHistory($query, $sort = "date", $sortorder = "desc", $page = -1, &$maxpage = 0, $rowsPerPage = 50, &$totalBefore=0)
  {
    if($sortorder == "asc")
      $sortorder = "ASC";
    else
      $sortorder = "DESC";

    if($sort == "award")
      $sortClause = " ORDER BY reason";
    else if($sort == "dkp")
      $sortClause = " ORDER BY points";
    else if($sort == "date")
      $sortClause = " ORDER BY date";
    else
      $sortClause = " ORDER BY date";
    $sortClause .= " $sortorder";


    $pageClause = "";
    if ($page != -1) {
      $offset = ($page - 1 ) * $rowsPerPage;
      $pageClause = " LIMIT $offset, $rowsPerPage";
    }

    global $sql;
    $patents = array();
    $result = $sql->Query($query."$sortClause $pageClause");

    $awards = array();
    while($row = mysql_fetch_array($result)) {
      $award = new dkpAward();
      $award->loadFromRow($row);
      $award->player = $row["name"];
      $awards[]  = $award;
    }

    //update the query to a count query.
    //Grab everything between SELECT and FROM and replace it with count(*)
    $start = strpos($query,"SELECT");
    $end = strpos($query,"FROM");
    $a = substr($query, 0, $start+7);
    $b = " count(*) ";
    $c = substr($query, $end, strlen($query)-$end);
    $temp = $a.$b.$c;
    $count = $sql->QueryItem($temp);

    $maxpage = ceil($count/$rowsPerPage);

    //load their total before this
    if($page != -1 ) {
      $temp = $rowsPerPage * ($page-1);
      $tempquery = str_replace("*","points",$query);
      $totalBefore = $sql->QueryItem("SELECT sum(points) FROM ( ( $tempquery $sortClause LIMIT 0, $temp) as TEMP)");
    }
    return $awards;
  }

  function LoadAwards($query, & $count, $sort = "date", $sortorder="desc", $page = -1, &$maxpage = 0, $rowsPerPage = 50){

    //$rowsPerPage = 50;

    if($sortorder == "asc")
      $sortorder = "ASC";
    else
      $sortorder = "DESC";

    if($sort == "award")
      $sortClause = " ORDER BY reason";
    else if($sort == "dkp")
      $sortClause = " ORDER BY points";
    else if($sort == "date")
      $sortClause = " ORDER BY date";
    else if($sort == "players")
      $sortClause = " ORDER BY playercount";
    else if ($sort == "player")
      $sortClause = " ORDER BY dkp_users.name";
    else
      $sortClause = " ORDER BY date";
    $sortClause .= " $sortorder";


    $pageClause = "";
    if ($page != -1) {
      $offset = ($page - 1 ) * $rowsPerPage;
      $pageClause = " LIMIT $offset, $rowsPerPage";
    }

    global $sql;
    $patents = array();
    $result = $sql->Query($query."$sortClause $pageClause");

    $awards = array();
    while($row = mysql_fetch_array($result)) {
      $award = new dkpAward();
      $award->loadFromRow($row);
      $award->player = $row["name"];
      $awards[]  = $award;
    }

    //update the query to a count query.
    //Grab everything between SELECT and FROM and replace it with count(*)
    $start = strpos($query,"SELECT");
    $end = strpos($query,"FROM");
    $a = substr($query, 0, $start+7);
    $b = " count(*) ";
    $c = substr($query, $end, strlen($query)-$end);
    $temp = $a.$b.$c;
    $count = $sql->QueryItem($temp);

    $maxpage = ceil($count/$rowsPerPage);

    return $awards;
  }

  function GetAwards($guildid, $tableid, & $count = 0, $sort = "date", $sortorder="desc", $page = -1,& $maxpage = 0, $rowsPerPage = 50){
    $query = "SELECT *, dkp_awards.id AS awardid FROM dkp_awards WHERE guild='$guildid' AND tableid='$tableid' AND foritem='0' AND transfer='0'";
    //if($filter!="")
    //  $query.=" WHERE $filter";
    return dkpUtil::LoadAwards($query, $count, $sort, $sortorder, $page, $maxpage, $rowsPerPage);
  }

  function GetLoot($guildid, $tableid, & $count = 0, $sort = "date", $sortorder="desc", $page = -1,& $maxpage = 0, $rowsPerPage = 50){
    $query = "SELECT *, dkp_awards.id AS awardid FROM dkp_awards, dkp_pointhistory, dkp_users
          WHERE dkp_awards.guild='$guildid' AND dkp_awards.tableid='$tableid' AND foritem='1' AND transfer='0'
          AND dkp_pointhistory.award = dkp_awards.id
          AND dkp_pointhistory.user = dkp_users.id";
    return dkpUtil::LoadAwards($query, $count, $sort, $sortorder, $page, $maxpage, $rowsPerPage);
  }

  function GetDKPTable($guildid, $tableid, & $count = 0, $sort = "date", $sortorder="desc", $page = -1,& $maxpage = 0, $filter = "", $rowsPerPage = 50){
    global $sql;
    $usertable = dkpUser::tablename;
    $guildtable = dkpGuild::tablename;
    $pointstable = dkpPointsTableEntry::tablename;
    if($filter != "")
      $filter = "AND $filter";

    $query =          "SELECT *,
                 $usertable.id AS userid,
                 $guildtable.id AS guildid,
                 $pointstable.guild AS pointsguildid,
                 $pointstable.id AS pointsid
                 FROM $pointstable, $usertable, $guildtable
                 WHERE $pointstable.guild='$guildid'
                 AND $pointstable.user = $usertable.id
                 AND $usertable.guild = $guildtable.id
                 AND $pointstable.tableid = '$tableid'
                 $filter";

    return dkpUtil::LoadDKPTable($query, $count, $sort, $sortorder, $page, $maxpage, $rowsPerPage);
  }

  function LoadDKPTable($query, & $count, $sort = "date", $sortorder="desc", $page = -1, &$maxpage = 0, $rowsPerPage = 50){

    if($sortorder == "asc")
      $sortorder = "ASC";
    else
      $sortorder = "DESC";

    if($sort == "player")
      $sortClause = " ORDER BY dkp_users.name";
    else if($sort == "dkp" || $sort == "tiers")
      $sortClause = " ORDER BY dkp_points.points";
    else if($sort == "lifetime")
      $sortClause = " ORDER BY dkp_points.lifetime";
    else if($sort == "class")
      $sortClause = " ORDER BY dkp_users.class";
    else if ($sort == "guild")
      $sortClause = " ORDER BY dkp_guilds.gname";
    else
      $sortClause = " ORDER BY dkp_points.points";
    $sortClause .= " $sortorder";
    $sortClause .= " , dkp_points.points DESC";

    $pageClause = "";
    if ($page != -1) {
      $offset = ($page - 1 ) * $rowsPerPage;
      $pageClause = " LIMIT $offset, $rowsPerPage";
    }

    global $sql;
    $patents = array();
    $result = $sql->Query($query."$sortClause $pageClause");

    $data = array();
    while($row = mysql_fetch_array($result)) {
      unset($tableEntry);
      $tableEntry = new dkpPointsTableEntry();
      $tableEntry->loadFromRow($row);
      $data[] = $tableEntry;
    }

    //$temp = str_replace("*","count(*)",$query);

    //replace the select clause with a count(*)
    $start = strpos($query,"SELECT");
    $end = strpos($query,"FROM");
    $a = substr($query, 0, $start+7);
    $b = " count(*) ";
    $c = substr($query, $end, strlen($query)-$end);
    $query = $a.$b.$c;

    $count = $sql->QueryItem($query);


    $maxpage = ceil($count/$rowsPerPage);

    return $data;
  }

  function GetLootAwards($guildid, $tableid){
    return dkpUtil::GetAllAwards($guildid, $tableid, 1);
  }

  function GetAllAwards($guildid, $tableid, $mode = 0){
    global $sql;


    if($mode == 1 )
      $extra = "AND foritem='1' AND transfer='0'";
    else if($mode == 2)
      $extra = "AND foritem='0' AND transfer='0'";
    else
      $extra = "";

    $guildid = sql::Escape($guildid);
    $tableid = sql::Escape($tableid);

    $result = $sql->Query("SELECT *
                 FROM dkp_awards
                  WHERE guild='$guildid'
                  AND tableid='$tableid'
                  $extra
                 GROUP BY reason, points, date
                  ORDER BY date DESC");

    $awards = array();
    while($row = mysql_fetch_array($result)) {
      $award = new dkpAward();
      $award->loadFromRow($row);
      $awards[]  = $award;
    }

    return $awards;
  }

  function GetLootTables($guildid){
    global $sql;
    $table = dkpLootTable::tablename;
    $guildid = sql::Escape($guildid);
    $result = $sql->Query("SELECT * FROM $table
                 WHERE guild='$guildid'
                 ORDER BY name ASC");
    $tables = array();
    while($row = mysql_fetch_array($result)) {
      $table = new dkpLootTable();
      $table->loadFromRow($row);
      $tables[] = $table;
    }
    return $tables;
  }
}
?>
