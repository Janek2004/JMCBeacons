<?php
// Register Custom Post Type
function missions_post_type() {

	$labels = array(
		'name'                => _x( 'Missions', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Mission', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Missions', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'add_new_item'        => __( 'Add New Mission', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
	
		'description'         => __( 'iBeacons Missions', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array('title','editor','thumbnail'),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'Mission', $args );
}

// Hook into the 'init' action
add_action( 'init', 'missions_post_type', 0 );

//TODO change description 

?>