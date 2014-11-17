<?php
/**
 * @package JMCBeacons
 */
/*
Plugin Name: JMCBeacons
Plugin URI: https://github.com/
Description: A lightweight ibeacons compatible platform built using WordPress
Version: 0.2
Author: Janusz Chudzynski
Author URI: 

JMC Beacons using custom mysql tables, specifically
regions_events - this table is used to store information about region related events
proximity_events - this table is tracking changes in proximity events 

CREATE TABLE region_events (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`entered` INT NOT NULL,
`event_date` TIMESTAMP NOT NULL,
`user` tinytext NOT NULL,
`beacon_id` INT NOT NULL
)ENGINE=MyISAM


*/
ini_set('auto_detect_line_endings', true);
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
date_default_timezone_set('America/Chicago');


//add_action('admin_menu', 'jmcbeacons_admin_menu');
//add_action('parse_request', 'jmcbeacons_parse_request');

register_activation_hook(__FILE__,'jmcbeacons_activate');
register_deactivation_hook(__FILE__,'jmcbeacons_deactivate');

require_once( dirname(__FILE__) . '/includes/missions.php' );
require_once( dirname(__FILE__) . '/includes/stations.php' );
require_once( dirname(__FILE__) . '/includes/ibeacons.php' );
require_once( dirname(__FILE__) . '/includes/students_import.php' );



global $jmcbeacons_db_version;
$jmcbeacons_db_version = "1.15";


add_action('init', 'addStudents');
function addStudents(){
	//$file1 = dirname(__FILE__)."/students_data/junior.csv";
	$file2 = dirname(__FILE__)."/students_data/senior.csv";	
	//csv_to_array($file1,",","junior");
	//csv_to_array($file2,",","senior");
	insertTesters();
}

function testMethods(){
//Unit Tests
//http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/wp-content/plugins/jmcbeacons/unittest.php


}



function jmcbeacons_register_css() {
	wp_register_script('beaconjs', plugins_url('includes/beacon.js',__FILE__ ));
	wp_enqueue_script('beaconjs', plugins_url('includes/beacon.js',__FILE__ ));

	
}
add_action( 'admin_enqueue_scripts', 'jmcbeacons_register_css' );


function templateRedirect()
{
	$missions = get_query_var( 'missions_json' );
	if($missions){
		/*
			missions_json file server role of a basic REST API 
			to send request to it you need to specify an action and pass appropriate paramaters:
			http://localhost/Badges/wp/?missions_json=1&action=saveBeacon&beacon_uuid=223&beacon_minor=42&beacon_major=42
		
		*/
		$template_file = plugin_dir_path( __FILE__ ). '/Includes/missions_json.php';
		include($template_file);	
		exit;
	}		
}
 
// add our function to template_redirect hook
add_action('template_redirect', 'templateRedirect');


//Determine which template should be used:
add_filter('template_include', 'jmcbeacons_template_check' );

function jmcbeacons_template_check() {
	global $template;
	//it doesn't work for some reason
	return $template;
}


add_action('init', 'jmcbeacons_do_output_buffer');
function jmcbeacons_do_output_buffer() {
    //ob_start();
 	//updateJMCDB();	
	// testMethods();		
			
}

function add_query_vars_filter( $vars ){
  $vars[] = "missions_json";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );

function checkForUpdates(){
global $wpdb;
$installed_ver = get_option( "jmcbeacons_db_version" );

	if ( $installed_ver != $jmcbeacons_db_version ) {
		/// do something
	}
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	update_option( "jmcbeacons_db_version", $jmcbeacons_db_version );
}

function jmcbeacons_activate()
{
	// If the current theme does not support post thumbnails, exit install and flash warning
	if(!current_theme_supports('post-thumbnails')) {
		echo "Unable to install plugin, because current theme does not support post-thumbnails. You can fix this by adding the following line to your current theme's functions.php file: add_theme_support( 'post-thumbnails' );";
		exit;
	}
	
 	updateJMCDB();
	global $jmcbeacons_db_version;
 
	add_option("wpbadger_db_version", $jmcbeacons_db_version);

	// Flush rewrite rules
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function jmcbeacons_deactivate()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function updateJMCDB(){
global $wpdb;

/*
 * We'll set the default character set and collation for this table.
 * If we don't do this, some characters could end up being converted 
 * to just ?'s when saved in our table.
 */
$charset_collate = '';

if ( ! empty( $wpdb->charset ) ) {
  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
}

$region_table_name = $wpdb->prefix."_region_events";
$proximity_table_name = $wpdb->prefix."_proximity_events";
$session_table_name = $wpdb->prefix."_session_events";
$overrides_table_name = $wpdb->prefix."_override_events";
$scans_table_name = $wpdb->prefix."_scan_events";
$warning_table_name = $wpdb->prefix."_warning_events";



if ( ! empty( $wpdb->collate ) ) {
  $charset_collate .= " COLLATE {$wpdb->collate}";
}


$sql = "CREATE TABLE  $region_table_name (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`state` INT NOT NULL,
`event_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
`user` INT NOT NULL,
`beacon_id` INT NOT NULL
) $charset_collate; ";

$sql = $sql."CREATE TABLE  $proximity_table_name (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `proximity` INT NOT NULL,
  `event_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  `user` INT NOT NULL,
  `beacon_id` INT NOT NULL
) $charset_collate; ";

$sql = $sql."CREATE TABLE  $session_table_name (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `login_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  `logout_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL ,
  `user` INT NOT NULL,
  `primary_nurse` INT 
) $charset_collate;";

$sql = $sql."CREATE TABLE  $overrides_table_name (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
   `override_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL ,
  `user` INT NOT NULL,
  `session_id` INT 
) $charset_collate;";


$sql = $sql."CREATE TABLE  $warning_table_name (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
   `warning_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL ,
  `user` INT NOT NULL,
  `session_id` INT 
) $charset_collate;";


$sql = $sql."CREATE TABLE  $scans_table_name (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
   `scan_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL ,
  `user` INT NOT NULL,
  `session` INT,
  `barcode_id` INT

 ) $charset_collate";



//echo $sql;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta($sql );

//die();

}



?>
<?php
/*
Plugin Name: Edit only Your Posts and Pages
Version: 1.0
Plugin URI: http://wordpress.org/support/topic/287591?replies=24#post-1214104
Description: Only show pages for current user in edit posts/pages
Author: t31os_
Author URI: http://wordpress.org/support/topic/287591?replies=24#post-1214104
*/

function posts_for_current_author($query) {

	if($query->is_admin) {

		global $user_ID;
		$query->set('author',  $user_ID);
	}
	return $query;
}
//add_filter('pre_get_posts', 'posts_for_current_author');
?>