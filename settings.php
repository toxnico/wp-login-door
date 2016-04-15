<?php

defined( 'ABSPATH' ) or die( 'Oh well...' );

require_once(ABSPATH . '/wp-admin/includes/template.php');
require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . WPINC . '/pluggable.php');

/**
 * Manages the plugin settings
 */
class WpLoginDoorSettings
{

  public static $defaultKeyName  = "_key";
  public static $defaultKeyValue = "_value";
  public static $defaultErrorMessage = "error";
  public static $defaultDisableXmlRpc = "on";

  private $settingsPageName = 'wp-door-configuration';

  public function __construct()
  {
    add_action('admin_init', array($this, 'admin_init'));
    add_action( 'admin_menu', array($this, 'add_admin_menu' ));
	
	//settings link from the main plugin list
	$pluginPath = "wplogindoor/wp-login-door.php";
	add_filter("plugin_action_links_$pluginPath", array($this, 'create_settings_link' ));
	
	//show the notice when the settings don't exist yet.
	if(get_option('wp-door-keyname', "__unset__") == "__unset__")
		add_action( 'admin_notices', array($this, 'after_activation_notice') );
	
  }

  /**
   * Creates the "settings" link
   */
  function create_settings_link($links) {
	$settings_link = '<a href="options-general.php?page='.$this->settingsPageName.'">Settings</a>'; 
	$links[] = $settings_link;
    
    return $links; 
  }
  
  function after_activation_notice() {
    ?>
	<div class="notice notice-success is-dismissible">
        <p>Thanks for installing Wp Login Door!</p>   
        <p>Please go to the <a href="options-general.php?page=<?php echo $this->settingsPageName?>">settings page</a> before it's too late and you can't login anymore :D</p>
    </div>
	<?php
  }
  
  function after_activation() {
    add_action( 'admin_notices', array($this, 'after_activation_notice') );
  }
  
  /**
   * Add the option menu
   */
  function add_admin_menu() {
  	add_options_page( 'WpLoginDoor configuration', 'WpLoginDoor', 'manage_options', $this->settingsPageName, array($this, 'show_options_page'));
	
  }

  function admin_init() {

    add_settings_section( 'default', 'WpLoginDoor settings', array($this, 'sectionCallback'), $this->settingsPageName);

    add_settings_field( "wp-door-keyname", "Key name", array($this, 'keyname_callback'), $this->settingsPageName, 'default', array() );
    add_settings_field( "wp-door-keyvalue", "Key value", array($this, 'keyvalue_callback'), $this->settingsPageName, 'default', array() );
    add_settings_field( "wp-door-errormessage", "Error message", array($this, 'errormessage_callback'), $this->settingsPageName, 'default', array() );
    add_settings_field( "wp-door-disable-xml-rpc", "Disable XML RPC", array($this, 'disablexmlrpc_callback'), $this->settingsPageName, 'default', array() );

    register_setting( 'default', 'wp-door-keyname', array($this, 'sanitizeKeyCallback'));
    register_setting( 'default', 'wp-door-keyvalue' );
    register_setting( 'default', 'wp-door-errormessage' );
    register_setting( 'default', 'wp-door-disable-xml-rpc' );

  }

  /**
   * Configuration page
   */
  function show_options_page() {
  	if ( !current_user_can( 'manage_options' ) )  {
  		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  	}
  	echo '<div class="wrap">';
    $this->displayForm();
  	//echo '<p>Here is where the form would go if I actually had options.</p>';
  	echo '</div>';
  }

  function sectionCallback() {
    $goodUrl = home_url()."/wp-login.php?".get_option("wp-door-keyname", WpLoginDoorSettings::$defaultKeyName)."=".get_option("wp-door-keyvalue", WpLoginDoorSettings::$defaultKeyValue);
    $badUrl = home_url()."/wp-login.php";
    echo "<p>Your login url is now : <a target=\"_blank\" href=\"$goodUrl\">$goodUrl</a></p>";
    echo "<p>Please click on this link and ensure you see the login page! Then you can bookmark it if you feel like.</p>";
    echo "<p>You can also see what prohibited users will see if they don't provide the key : <a target=\"_blank\" href=\"$badUrl\">$badUrl</a></p>";
  }

  function keyname_callback() {
    echo '<input name="wp-door-keyname" id="wp-door-keyname" type="text" value="'.get_option( 'wp-door-keyname', WpLoginDoorSettings::$defaultKeyName ).'" class="code" />';
  }

  function keyvalue_callback() {
    echo '<input name="wp-door-keyvalue" id="wp-door-keyvalue" type="text" value="'.get_option( 'wp-door-keyvalue', WpLoginDoorSettings::$defaultKeyValue ).'" class="code" />';
  }

  function errormessage_callback() {
    echo '<input name="wp-door-errormessage" id="wp-door-errormessage" type="text" value="'.get_option( 'wp-door-errormessage', WpLoginDoorSettings::$defaultErrorMessage ).'" class="code" />';
  }

  function disablexmlrpc_callback() {
    $checked = get_option( 'wp-door-disable-xml-rpc', WpLoginDoorSettings::$defaultDisableXmlRpc ) == "on" ? 'checked="checked"' : '';
    echo '<input name="wp-door-disable-xml-rpc" id="wp-door-disable-xml-rpc" type="checkbox" '.$checked.' class="code" />';
  }

  function sanitizeKeyCallback($input) {
    if(is_numeric($input))
    {
      add_settings_error('wp-door-keyname', 'invalid-numeric', 'Invalid key name : cannot be a number');
      return "_$input";
    }

    $reserved_words = array("key", "error", "login", "loggedout", "registration", "checkemail");

    if(array_search($input, $reserved_words) !== FALSE)
    {
      add_settings_error('wp-door-keyname', 'invalid-keyname', "Invalid key name : $input is a reserved word");
      return "_$input";
    }

    return $input;

  }

  public function displayForm(){
    ?>
    <form method="POST" action="options.php">
    <?php settings_fields('default');	//section settings

    do_settings_sections( $this->settingsPageName ); 	//pass slug name of page

    submit_button();
    ?>
    </form>
    <?php
  }
}

$settings = new WpLoginDoorSettings();
