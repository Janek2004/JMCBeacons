<?php
/**
 * @package JMCBeacons
 */
/*
Plugin Name: JMCBeacons
Plugin URI: https://github.com/davelester/WPBadger
Description: A lightweight ibeacons compatible platform built using WordPress
Version: 0.1
Author: Janusz Chudzynski
Author URI: http://www.davelester.org
*/

//add_action('admin_menu', 'jmcbeacons_admin_menu');
add_action('parse_request', 'jmcbeacons_parse_request');

register_activation_hook(__FILE__,'jmcbeacons_activate');
register_deactivation_hook(__FILE__,'jmcbeacons_deactivate');

require_once( dirname(__FILE__) . '/includes/missions.php' );
require_once( dirname(__FILE__) . '/includes/stations.php' );
require_once( dirname(__FILE__) . '/includes/ibeacons.php' );


global $jmcbeacons_db_version;
$jmcbeacons_db_version = "0.6.2";


//add_action( 'wp_head', 'favicon_link' );

function jmcbeacons_register_css() {
	wp_register_script('beaconjs', plugins_url('includes/beacon.js',__FILE__ ));
	wp_enqueue_script('beaconjs', plugins_url('includes/beacon.js',__FILE__ ));

}
add_action( 'admin_enqueue_scripts', 'jmcbeacons_register_css' );


function templateRedirect()
{
	$missions = get_query_var( 'missions_json' );
	if($missions){
		$template_file = plugin_dir_path( __FILE__ ). '/Includes/missions_json.php';
    include($template_file);	
		exit;
	}		
}
 
// add our function to template_redirect hook
add_action('template_redirect', 'templateRedirect');


//Determin which template should be used:
add_filter('template_include', 'jmcbeacons_template_check' );

function jmcbeacons_template_check() {
	global $template;
	//it doesn't work for some reason
	return $template;
}


add_action('init', 'do_output_buffer');
function jmcbeacons_do_output_buffer() {
      //  ob_start();
}

function add_query_vars_filter( $vars ){
  $vars[] = "missions_json";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );




function jmcbeacons_parse_request($wp) {
  
}


function jmcbeacons_activate()
{
	// If the current theme does not support post thumbnails, exit install and flash warning
	if(!current_theme_supports('post-thumbnails')) {
		echo "Unable to install plugin, because current theme does not support post-thumbnails. You can fix this by adding the following line to your current theme's functions.php file: add_theme_support( 'post-thumbnails' );";
		exit;
	}

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