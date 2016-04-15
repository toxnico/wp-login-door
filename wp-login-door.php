<?php
/*
Plugin Name: WP Login Door
Plugin URI:  http://dirtymarmotte.net/
Description: Restricts the access to your Wordpress login page with a secret key and disables XMLRPC
Version:     1.0
Author:      Nicolas Simonnet
Author URI:  http://dirtymarmotte.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'Oh well...' );

require_once(dirname(__FILE__)."/settings.php");

class WpLoginDoor
{

  public $keyName;
  public $keyValue;
  public $goHomeOnError = false;
  public $isXmlRpcDisabled = false;

  public function __construct()
  {
    //Overrides the login page
    add_action('login_init', array($this, 'display_login_form'));

    add_action('login_form', array($this, 'add_hidden_field'));

    //Ensure the login key was provided in POST login requests
    add_action('wp_authenticate', array($this, 'before_authenticate'));

    //enables or disables the xml-rpc feature,
    //which is a very popular bruteforce attack vector
    add_filter('xmlrpc_enabled', array($this, 'xmlrpc_filter'));

    //load the options
    $this->keyName = get_option('wp-door-keyname', WpLoginDoorSettings::$defaultKeyName);
    $this->keyValue = get_option('wp-door-keyvalue', WpLoginDoorSettings::$defaultKeyValue);
	$this->isXmlRpcDisabled = get_option('wp-door-disable-xml-rpc', WpLoginDoorSettings::$defaultDisableXmlRpc) == "on";

  }

  /**
   * Checks the presence and validity of the login key
   * before displaying the login form
   */
  function display_login_form()
  {
    //if we're sending login and password, let it go
    //it means that the form has already been displayed,
    //and thus our login key has been validated
		if(!empty($_REQUEST['log']))
			return;

		//if we're logging out, let it go
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout')
			return;

		//Check if our key is provided in the query string
		if(isset($_REQUEST[$this->keyName]) && $_REQUEST[$this->keyName] == $this->keyValue)
      return;

    //Finally, display the error page
		die($this->getErrorPage());

  }

  /**
   * This adds a hidden field in the login form. It will be sent along login/password  and checked before authentication
   */
  public function add_hidden_field()
  {
    echo '<input type="hidden" name="'.$this->keyName.'" value="'.$this->keyValue.'"/>';
  }

  /**
   * This method is called before the user is authenticated.
   * Here we ensure the key was provided from the login form.
   * Else, anyone could just send a POST request and attempt to login
   * without using the login form. That's what robots do, anyway.
   */
  function before_authenticate()
  {

    //if we're not sending the login form, let it go
		if(empty($_REQUEST['log']))
			return;

    //is a key supplied?
    if(!isset($_REQUEST[$this->keyName]))
      die($this->getErrorPage());

    //check the validity of posted key
		if($_REQUEST[$this->keyName] != $this->keyValue)
			die($this->getErrorPage());

  }

  /**
   * Enables or disables the XML-RPC feature.
   * Returns false if xmlrpc is disabled.
   */
  function xmlrpc_filter()
  {
    return !$this->isXmlRpcDisabled;
  }

  /**
   * Returns the error message
   */
  function getErrorPage()
  {
    if($this->goHomeOnError)
      header("Location: ".home_url());
    else
      return get_option("wp-door-errormessage");
  }
}

new WpLoginDoor();
