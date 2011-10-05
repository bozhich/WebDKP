<?php

/**
 * A template is an HTML file with place holders where dynamic
 * content should be filled in. This class will render a template
 * file, passing the supplied variables to the template, then
 * returning the rendering content as a string.
 *
 * Templates are used to seperate the logic of the application from
 * the actual rendered HTML. Using this class PHP is allowed to
 * be used in template files, but it should be done so sparingly.
 *
 * @author Scott Bailey (scott@zeddic.com)
 */
class template {

  // Variables to send to the template.
  var $vars = array();

  // The directory of the template file.
  var $directory;

  // The template file.
  var $file;

  // Sets how many tabs to place in front of all
  // lines form the template. Use this to make
  // templates within templates look cleaner
  // in the source
  var $depth = 0;

  /**
   * Creates a new template.
   */
  function template($directory = "", $file= "") {
    if($directory!="")
      $this->directory = $directory;
    if($file!="")
      $this->file = $file;
  }

  /**
   * Sets the directory where the template file can be found.
   **/
  function setDirectory($directory) {
    $this->directory = $directory;
  }
  /**
   * Sets the file where the template can be found.
   */
  function setFile($file) {
    $this->file = $file;
  }

  /**
   * Sets a variable that should be accessible in the tempalte with
   * the given value. The template has access to this variable by using
   * <?=$name?>
   */
  function set($name, $value) {
    $this->vars[$name] = $value;
  }

  /**
   * Sets an array of variables at once to the template. The variables
   * must be in an associative array, with the variable names as keys.
   * An easy way to do this is with the compact() function in php.
   *
   * @param array $vars Array of the variables and their values to set
   * @param bool $clear If true any current variables that have been set will be
   *     erased.
   */
  function setVars($vars, $clear = false) {
    if($clear) {
      $this->vars = $vars;
    }
    else {
      if(is_array($vars)) $this->vars = array_merge($this->vars, $vars);
    }
  }

  /**
   * Returns true if the given template exists. (the given directory
   * and file name is assumed. If a file name is passed it is used
   * instead of the current $this->file instance.
   **/
  function exists($file = "") {
    $directory = $this->getDirectory();
    if($file!="")
      $filename = $directory.$file;
    else
      $filename = $directory.$this->file;
    return fileutil::file_exists_incpath($filename);
  }

  /**
   * Returns the current directory that this template is pointing too.
   */
  function getDirectory() {
    global $theme;
    $directory = $this->directory;
    if($directory == "")
      $directory = $theme->getDirectory();
    return $directory;
  }


  /**
   * Fetches the template. This will open up the template file,
   * then pass all the variables to it. It will render the template
   * than return the results as an html string.
   */
  function fetch($page = "", $depth = null) {
    if($depth != null) {
      $this->depth = $depth;
    }
    //every template has a reference to the theme put in
    global $theme;
    if($page != "")
      $this->file = $page;
    //if($this->directory == "")
    //  $this->directory = $theme->getDirectory();
    if(is_a($theme,"theme"))
      $this->set("theme",$theme);
    $this->set("PHP_SELF",$_SERVER["PHP_SELF"]);
    $this->set("PHP_SELFDIR",$_SERVER["PHP_SELFDIR"]);
    $this->set("SiteRoot",$GLOBALS["SiteRoot"]);
    $this->set("siteRoot",$GLOBALS["SiteRoot"]);
    $this->set("siteUser",$GLOBALS["siteUser"]);
    $this->set("SiteUser",$GLOBALS["siteUser"]);
    $this->set("theme",$GLOBALS["theme"]);
    $file = $this->directory . $this->file;
    if(!fileutil::file_exists_incpath($file)) {
      return "The template engine could not locate the file $file";
    }
    extract($this->vars);          // Extract the vars to local namespace
    ob_start();                // Start output buffering
    include($file);  // Include the file
    $contents = ob_get_contents(); // Get the contents of the buffer
    ob_end_clean();                // End buffering and discard


    $useIndents = $GLOBALS["Framework_UseTemplateIndents"];
    if($useIndents) {
      if($this->depth != 0) {
        $temp = "";
        for($i = 0 ; $i < $this->depth ; $i++)
          $temp.="\t";
        $contents = str_replace("\r\n","\r\n$temp",$contents);
      }
    }
    return $contents.="\r\n";              // Return the contents
  }
}
?>
