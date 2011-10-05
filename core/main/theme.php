<?php
/**
 * Represents a way of styling the site with a particular layout
 * and set of css.
 */
class theme {

  /** Id of the theme in the database. */
  var $id;

  /** Folder name. */
  var $name;

  /** Directory of the theme. */
  var $directory;

  /** User friendly description. */
  var $description;

  /** Who created the theme. */
  var $createdby;

  /** When the theme was added to the database. */
  var $dateadded;
  var $dateaddedDate;
  var $dateaddedTime;

  /** User friendly name. */
  var $safeName;

  /** Cache for getNumberOfBorders. */
  var $numberOfBorders = -1;

  const tablename = "site_themes";

  function theme() {
    $this->tablename = theme::tablename;
  }

  /**
   * Loads the information for this class from the backend database
   * using the passed string.
   */
  function loadFromDatabase($id) {
    global $sql;
    $row = $sql->QueryRow("SELECT * FROM $this->tablename WHERE id='$id'");
    $this->loadFromRow($row);
  }

  /**
   * Loads the information for a instance by looking up the themes
   * system name (directory name);
   */
  function loadFromDatabaseBySystemName($name) {
    global $sql;
    $name = sql::Escape($name);
    $row = $sql->QueryRow("SELECT * FROM $this->tablename WHERE directory='$name'");
    $this->loadFromRow($row);
  }

  /**
   * Loads the information for this class from the passed database row.
   */
  function loadFromRow($row) {
    $this->id=$row["id"];
    $this->name = $row["name"];
    $this->directory = $row["directory"];
    $this->description = $row["description"];
    $this->createdby = $row["createdby"];
    $this->dateadded = $row["dateadded"];
    $this->safeName = $row["safeName"];
    if($this->dateadded){
      $this->dateaddedDate = date("F j, Y", strtotime($row["dateadded"]));
      $this->dateaddedTime = date("g:i A", strtotime($row["dateadded"]));
    }
  }

  /**
   * Saves data into the backend database using the supplied id
   */
  function save() {
    global $sql;
    $name = sql::Escape($this->name);
    $directory = sql::Escape($this->directory);
    $safeName = sql::Escape($this->safeName);
    $description = sql::Escape($this->description);
    $createdby = sql::Escape($this->createdby);
    $sql->Query("UPDATE $this->tablename SET
          name = '$name',
          directory = '$directory',
          description = '$description',
          createdby = '$createdby'
          WHERE id='$this->id'");
  }

  /**
   * Saves data into the backend database as a new row entry. After
   * calling this method $id will be filled with a new value
   * matching the new row for the data
   */
  function saveNew() {
    global $sql;
    $name = sql::Escape($this->name);
    $directory = sql::Escape($this->directory);
    $safeName = sql::Escape($this->safeName);
    $description = sql::Escape($this->description);
    $createdby = sql::Escape($this->createdby);
    $sql->Query("INSERT INTO $this->tablename SET
          name = '$name',
          directory = '$directory',
          description = '$description',
          createdby = '$createdby',
          dateadded = NOW()
          ");
    $this->id=$sql->GetLastId();
  }

  /**
   * Deletes the row with the current id of this instance from the
   * database
   */
  function delete() {
    global $sql;
    $sql->Query("DELETE FROM $this->tablename WHERE id = '$this->id'");
  }

  /**
   * Returns the number of border types available for this theme.
   * Note: Border0 counts as a border, so if border 0-4 were present,
   * this method would return 5.
   */
  function numberOfBorderTypes() {
    //is cache available?
    if ( $this->numberOfBorders != -1 ) {
        return $this->numberOfBorders;
    }

    //no cache, determine number of borders via file scan
    $dir = $this->getDirectory()."borders/";
    $counter = 0;
    while(fileutil::file_exists_incpath($dir."border".$counter.".tmpl.php")) {
      $counter++;
    }
    $counter--;

    //save result in cache (per page load)
    $this->numberOfBorders = $counter;
    return $counter;
  }

  /**
   * Returns an array of css files that this theme has specified
   * that it wishes to include. These paths are NOT friendly paths
   * that can be passed and linked to the browser directly.
   */
  function getCssFiles() {
    $toReturn = array();
    //css files are defined in the themes info file
    $file = $this->getDirectory()."info.php";
    if( !fileutil::file_exists_incpath($file) ) {
      return $toReturn;
    }
    include($file);

    if(!is_array($css))
      return $toReturn;

    foreach($css as $k => $file) {
      $toReturn[$k] = $this->getDirectory().$file;
    }

    return $toReturn;
  }

  /**
   * Returns true if the given entry exists in the database
   * database
   */
  function exists($name) {
    global $sql;
    $name = sql::Escape($name);
    $tablename = theme::tablename;
    $exists = $sql->QueryItem("SELECT id FROM $tablename WHERE name='$name'"); //MODIFY THIS LINE
    return ($exists != "");
  }

  /**
   * Returns true if a theme exists with the given id
   */
  function idExists($id) {
    global $sql;
    $tablename = theme::tablename;
    $exists = $sql->QueryItem("SELECT id FROM $tablename WHERE id='$id'"); //MODIFY THIS LINE
    return ($exists != "");
  }

  /**
   * Returns the directory of the theme.
   * If passed true, it will return the absolute path. The absolute
   * path is appropriate when linking to this directory for the user
   * The non absolute path is used when including files from the
   * theme directory
   */
  function getDirectory($absolute = false) {
    if($absolute)
      return $GLOBALS["SiteRoot"] . "themes/" . strtolower($this->directory)."/";
    return "site/themes/".strtolower($this->directory)."/";
  }
  /**
   * Returns the absolute directory to the theme directory.
   * Such as http://www.site.com/themes/etc.
   */
  function getAbsDirectory() {
    return  $GLOBALS["SiteRoot"] . "themes/".strtolower($this->directory)."/";
  }

  /**
   * Returns a path to the common theme directory. These is a directory
   * that contains information that all themes share.
   * If passed true, it will return the absolute path. The absolute
   * path is appropriate when linking to this directory for the user
   * The non absolute path is used when including files from the
   * theme directory
   */
  function getCommonDirectory($absolute = false){
    if($absolute)
      return $GLOBALS["SiteRoot"] . "themes/common/";
    return "site/themes/common/";
  }

  /**
   * Returns the absolute directory to the common theme directory.
   * Such as http://www.site.com/themes/etc.
   */
  function getAbsCommonDirectory() {
    return $GLOBALS["SiteRoot"] . "themes/common/";
  }

  /**
   * Returns the id of the theme with the given directory name
   */
  function getThemeIdBySystemName($name) {
  	unset($theme);
    $theme = new theme();
    $theme->loadFromDatabaseBySystemName($name);
    return $theme->id;
  }

  /**
   * Returns true if a theme with the specified name exists.
   */
  function layoutExists($layoutName) {
    return $this->getLayoutPath($layoutName) !== false;
  }

  /**
   * Returns a path to the layout file with the specified name. If the layout
   * can not be found false will be returned. This will first look in the
   * themes directory for a layout, then the common themes folder as a backup.
   */
  function getLayoutPath($layoutName) {
    if (stripos($layoutName, ".tmpl.php") === false)
      $layoutName .= ".tmpl.php";

    $themePath = $this->getDirectory() . "layouts/" . $layoutName;
    $commonPath = $this->getCommonDirectory() . "layouts/" . $layoutName;

    // Look for the requested layout in the theme directory first.
    // If it can't be found there, revert to the common directory.
    if (fileutil::file_exists_incpath($themePath))
      return $themePath;
    else if (fileutil::file_exists_incpath($commonPath))
      return $commonPath;
    else
      return false;
  }

  /**
   * Checks to see if the classes database table exists. If it does not
   * the table is created.
   */
  function setupTable() {
    if(!sql::TableExists(theme::tablename)) {

      $tablename = theme::tablename;
      global $sql;
      $sql->Query("CREATE TABLE `$tablename` (
            `id` INT NOT NULL AUTO_INCREMENT ,
            `name` VARCHAR (256) NOT NULL,
            `directory` VARCHAR (256) NOT NULL,
            `description` VARCHAR (256) NOT NULL,
            `createdby` VARCHAR (256) NOT NULL,
            `dateadded` VARCHAR (256) NOT NULL,
            PRIMARY KEY ( `id` )
            ) TYPE = innodb;");

      unset($defaultTheme);
      $defaultTheme = new theme();
      $defaultTheme->createdby   = "Scott Bailey";
      $defaultTheme->description  = "The default theme.";
      $defaultTheme->name      = "Default";
      $defaultTheme->directory   = "default";
      $defaultTheme->saveNew();
    }
  }
}
theme::setupTable();
?>
