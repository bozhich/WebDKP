<?php
include_once("theme.php");
include_once("themeMap.php");

/**
 * Keeps track of current site settings, such as the active theme
 * and whether the site is setup.
 *
 * @author Scott Bailey (scott@zeddic.com)
 */
class siteStatus {

  /** Id in the database. Why do we need this? */
  var $id;

  /** The theme that should be used to render content. */
  var $defaultTheme;

  /** The currently active theme. */
  var $theme;

  /** Whether the site still needs to be setup. */
  var $setup;

  const tablename = "site_status";

  function siteStatus() {
    $this->tablename = siteStatus::tablename;
  }

  /**
   * Loads the current site status
   */
  function load($url = "-1"){
    $this->loadFromDatabase(1,$url);
  }

  /**
   * Loads the information for this class from the backend database
   * using the passed string.
   */
  function loadFromDatabase($id,$url) {
    global $sql;
    $row = $sql->QueryRow("SELECT * FROM $this->tablename WHERE id='$id'");
    $this->loadFromRow($row, $url);
  }

  /**
   * Loads the information for this class from the passed database row.
   */
  function loadFromRow($row, $url) {
    $this->id=$row["id"];
    $themeid = $row["theme"];

    if($themeid == 0) {
      $themeid = theme::getThemeIdBySystemName("default");
      $mustResave = true;
    }

    //load the default theme
    $this->defaultTheme = null;
    $this->defaultTheme = new theme();
    $this->defaultTheme->loadFromDatabase($themeid);

    //load the theme for this page now (may end up being the default theme)
    $this->loadTheme($url);

    $this->setup = $row["setup"];

    if($mustResave) {
      $this->save();
    }
  }

  /**
   * Loads the theme for the current page. Note that the theme may vary
   * from page to page, based on the pages path. Themes may be
   * defined for different site paths, such as "ControlPanel\", resulting
   * in all pages in that path getting a different theme. If no
   * special theme is discovered for a given path, the default theme is assumed.
   */
  function loadTheme($url) {
    //if($url != "-1")
    $this->theme = themeMap::getThemeForPath($url);

    if($this->theme == "") {
      $this->theme = $this->defaultTheme;
    }
  }

  /**
   * Saves data into the backend database using the supplied id
   */
  function save() {
    global $sql;
    if(is_a($this->defaultTheme,"theme"))
      $defaultTheme = $this->defaultTheme->id;
    else
      $defaultTheme = $this->defaultTheme;

    $sql->Query("UPDATE $this->tablename SET
          theme = '$defaultTheme',
          setup ='$this->setup'
          WHERE id='$this->id'");
  }
  /**
   * Saves data into the backend database as a new row entry. After
   * calling this method $id will be filled with a new value
   * matching the new row for the data
   */
  function saveNew() {
    global $sql;
    if(is_a($this->defaultTheme,"theme"))
      $defaultTheme = $this->defaultTheme->id;
    else
      $defaultTheme = $this->defaultTheme;

    $sql->Query("INSERT INTO $this->tablename SET
          theme = '$defaultTheme',
          setup ='$this->setup'
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
   * Returns true if the given entry exists in the database
   * database
   */
  function exists($name) {
    global $sql;
    $name = sql::escape($name);
    $tablename = siteStatus::tablename;
    $exists = $sql->QueryItem("SELECT id FROM $tablename WHERE id='$name'");
    return ($exists != "");
  }

  /**
   * Checks to see if the classes database table exists. If it does not
   * the table is created.
   */
  function setupTable() {
    if(!sql::TableExists(siteStatus::tablename)) {
      $tablename = siteStatus::tablename;
      global $sql;
      $sql->Query("CREATE TABLE `$tablename` (
            `id` INT NOT NULL AUTO_INCREMENT ,
            `theme` INT NOT NULL,
            `setup` INT NOT NULL,
            PRIMARY KEY ( `id` )
            ) TYPE = innodb;");
      //create the intial site status
      unset($status);
      $status = new siteStatus();
      $status->theme = theme::getThemeIdBySystemName("default");
      $status->setup = 0;
      $status->saveNew();
    }
  }
}
siteStatus::setupTable();
?>
