<?php
/**
 * The dispatcher class is a utility class that helps map between
 * urls and files in the control / codebehind directory.
 * It includes static methods to detect if a control file exists
 * as well as to detect if the control file implements a page class
 * correctly.
 *
 * @author Scott Bailey (scott@zeddic.com)
 **/
class dispatcher {

  /**
   * Returns the url that the user requested but which was not
   * physically found in web root. This will strip any trailing
   * "/" from the url
   **/
  function getUrl() {
    $url = strtolower($_GET["url"]);

    if ($url == "") {
      $url = "index";
    }

    if (strrpos($url,"/") !== false && strrpos($url,"/") == strlen($url)-1 ) {
      $url = substr($url,0,strlen($url)-1);
    }
    return $url;
  }

  /**
   * Returns the url that the user requested raw.
   **/
  function getRawUrl($acceptEmpty = false) {
    $url = strtolower($_GET["url"]);

    if ($url == "" && !$acceptEmpty) {
      $temp = "index";
      $find = "site/webroot/";
      $start = strpos($temp,$find);
      if($start !== false) {
        $start += strlen($find);
        $url = substr($temp,$start,strlen($temp)-$start);
      }
      else
        $url = $temp;

    }
    return $url;
  }

  /**
   * Checks to see if there is a codebehind file for the given url. This will
   * check in the site/control directory. If a valid control file is found
   * its path is returned. If no control file is found, false is returned.
   **/
  function getControlFile($url) {
    $url = dispatcher::cleanUrl($url);
    $ext = fileutil::getExt($url);
    $url = fileutil::stripExt($url);
    $url = strtolower($url);

    //Try 1: the url is a path to a file
    $path = "control/$url.php";
    //echo("searching for control at $path <br />");
    if (fileutil::file_exists_incpath($path)) {
      return $path;
    }

    //Try 2: athe url is a path to a directory
    $path = "control/$url/index.php";
    //echo("searching for control at $path <br />");
    if (fileutil::file_exists_incpath($path)) {
      return $path;
    }

    return false;
  }
  /**
   * Returns a page class instance if it is available in the control
   * file identified by the given url. If not available or the given url
   * does not have a valid control file, null is returned.
   **/
  function getControlFilePageInstance($url) {

    // Find the control file for the given url.
    $path = dispatcher::getControlFile($url);
    if (!$path)
      return null;
    include_once($path);

    // Now to determine the name of the class that defines the page
    // Ussually this is just the name of the file with "Page" in front.
    // Example: PageIndex, PagePhotos, etc.
    // However, we also allow the path to be used to name a page (in order to
    // avoid naming conflicts when multiple control files are included).
    // Example: PageAdminContentNewsIndex corresponding to a page at
    // admin\content\news\index
    // Either all, or part of the path (right) can be used. Example:
    // PageNewsIndex in place of what was above.

    // Split up the path.
    $parts = explode("/",fileutil::stripExt($path));
    $buffer = "";

    // Run through the combinations of the path, trying to find a match.
    for ( $i = sizeof($parts) - 1 ; $i >= 0 ; $i--) {
      $buffer = ucfirst($parts[$i]) . $buffer;
      $page = dispatcher::createPageClass($buffer, $url, $path);
      if($page!=null) {
        //found a match
        return $page;
      }
    }

    // As a final check, see if "PageGeneral" was used.
    $page = dispatcher::createPageClass("General",$url,$path);
    if($page != null)
      return $pat;

    return null;
  }

  /**
   * Internal helper method for getControlFilePageInstance.
   * Given a name, will check to see if a page class of that name is
   * currently defined. If it is, it will be instantiated and returned.
   * If not, null is returned.
   * Parameters:
   * $name - the name of the class to check to see if it exists
   * $url - the url that this control file is bound to
   * $path - the actual path to the control file where the page class
   *     should be defined in
   **/
  function createPageClass($name, $url, $path) {
    $className = "page".ucfirst($name);
    if(class_exists($className)) {
   	  unset($page);
      $page = new $className;
      $page->url = $url;
      $page->controlFile = $path;
      $page->calculatePaths();
      $page->init();
      return $page;
    }
    return null;
  }

  /**
   * Cleans the url, such as removing the "/" from the end
   */
  function cleanUrl($url) {
    if(strrpos($url,"/")!== false && strrpos($url,"/") == strlen($url)-1 ){
      $url = substr($url,0,strlen($url)-1);
    }
    return $url;
  }
}
?>
