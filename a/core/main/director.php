<?php
include_once("core/sql/sql.php");
director::connectToSQL();
include_once("core/main/dispatcher.php");
include_once("core/main/template.php");
include_once("core/main/theme.php");
include_once("core/main/page.php");
include_once("core/util/util.php");
include_once("core/main/site.php");
include_once("core/security/user.php");
include_once("core/main/themeLibrary.php");
include_once("core/setup/setup.php");
include_once("site/lib/dkp/globals.php");

/**
 * Sets up the environment for the application. This includes
 * establishing a database connection, loading the logged in
 * user, and registering any needed globals. Once the environment
 * is setup the site class is used to render the page.
 *
 * @author Scott Bailey (scott@zeddic.com)
 */
class director {

  /** The site to render. */
  var $site;

  /** The logged in user. */
  var $siteUser;

  /** The site status. (Setup, Pending Setup, Closed, etc.) */
  var $siteStatus;

  /** The url that was requested by the user. */
  var $url;

  /**
   * Default Constructor. Sets up the environment.
   */
  function director() {
    // Enable sessions.
    if(util::getData("PHPSESSID"))
      session_id(util::getData("PHPSESSID"));
    session_start();

    // Get the url that the user requested.
    $this->url = dispatcher::getUrl();

    // Load the current site status.
    $this->loadSiteStatus();

    // Run setup if this is the first time the site has been used.
    if($this->status->setup == 0) {
      setup::run();
      $this->loadSiteStatus();
    }

    // Load the site.
    $this->site = null;
    $this->site = new site();

    // Load the current user.
    $this->handleUser();

    $this->registerGlobals();
  }

  /**
   * Connects to the database and stores it into a sql global variable.
   */
  function connectToSQL(){
    $sql = new sql();
    $sql->Setup(
        $GLOBALS["DatabaseUsername"],
        $GLOBALS["DatabasePassword"],
        $GLOBALS["DatabaseName"],
        $GLOBALS["DatabaseServer"], // Added - Necrojelly 2009-12-10
        true);
    $GLOBALS["sql"]=$sql;
  }

  /**
   * Loads the current site status. This conatins information about
   * how the site is configured, including the currently active theme.
   **/
  function loadSiteStatus(){
  	$this->status = null;
    $this->status = new siteStatus();
    $this->status->load($this->url);
  }

  /**
   * Registory any additional global variables
   **/
  function registerGlobals(){
    $GLOBALS["siteUser"] = $this->siteUser;
    $GLOBALS["SiteUser"] = $this->siteUser;
    $GLOBALS["theme"] = $this->status->theme;
    $GLOBALS["siteStatus"] = $this->status;
  }

  /**
   * Runs the director, choosing the appropriate page to display.
   **/
  function run(){

    $page = null;
    $url = $this->url;
    $_SERVER["PHP_SELF"] = $GLOBALS["SiteRoot"] . $url;
    $_SERVER["PHP_SELFDIR"] = fileutil::stripFile($_SERVER["PHP_SELF"]) . "/";
    $controlFile = dispatcher::getControlFile($url);

    if ($controlFile) {
      $directory = $_SERVER["PHP_SELFDIR"] . "/bin/";
      unset($template);
      $template = new template($directory . "templates/");
      $template->set("directory",$directory);
      $template->set("PHP_SELF",$_SERVER["PHP_SELF"]);
      include_once($controlFile);
      $page = dispatcher::getControlFilePageInstance($url);
      // If no page class was specified in the file, stop now.
      if ($page == null)
        die();
    } else {
      $page = dispatcher::getControlFilePageInstance("Errors/404");
    }

    //before continuing, make sure that the guild has been setup
    global $guildset;
    if(!$guildset && strtolower($url) != "setup") {
      global $siteRoot;
      util::forward($siteRoot."Setup");
    }

    $this->site->render($page);
  }

  /**
   * Attempts to identify the current user and populate the
   * $this->siteUser member variable. This will handle any of the following
   * conditions:
   * - User logging in
   * - User logging out
   * - User alreayd logged in (data loaded from cookie).
   * If all of these conditions fail, then there is no user that we
   * know about using the site. $siteUser will still be populated
   * in this case, it will just have a isValid flag = to false.
   *
   * Results from either a register or a login are stored in a global
   * variable loginResult and registerResult. These will contain
   * an int corresponding to a static variable in user that can be
   * reached via user::LOGIN_OK, etc.
   **/
  function handleUser(){

  	unset($siteUser);
    $siteUser  = new user();

    $siteUserEvent = util::getData("siteUserEvent");
    // User logging in.
    if($siteUserEvent == "login") {
      $username = $_POST["username"];
      $password = $_POST["password"];
      $loginResult = $siteUser->login($username,$password);
      $GLOBALS["loginResult"] = $loginResult;

    }
    // User logging out.
    else if($siteUserEvent == "logout") {
      $siteUser->loadFromCookie();
      $siteUser->logout();

    }
    // User is already logged in / check to see if they are logged in.
    else {
      $siteUser->loadFromCookie();
    }
    $this->siteUser   = $siteUser;
  }
}
?>
