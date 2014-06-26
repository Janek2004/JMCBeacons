<?php
class iBeacon{
	public static $JMC_UUID= 'jmcbeacon-uuid';
	public static $JMC_MAJOR= 'jmcbeacon-major';
	public static $JMC_MINOR= 'jmcbeacon-minor';
	public static	function getBeaconID($uuid, $major, $minor)
	{
			$args = array(
   	 'post_type' => 'iBeacon' ,
    	'meta_query' => array(
        array(
            'key' => iBeacon::$JMC_UUID,
            'value' => $uuid,
						'compare'=>'LIKE' 
        ),
        array(
            'key' => iBeacon::$JMC_MAJOR,
            'value' => $major,
           	'compare'=>'LIKE'
        ),
		        array(
            'key' => iBeacon::$JMC_MINOR,
            'value' => $minor,
           	'compare'=>'LIKE'
        )	
    )
 		);
 

			$query = new WP_Query( $args );
			return $query->get_posts();				
	}
	
	
}

// Register Custom Post Type
function iBeacon_post_type() {

	$labels = array(
		'name'                => _x( 'iBeacons', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'iBeacon', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'iBeacons', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'add_new_item'        => __( 'Add New iBeacon', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
	
		'description'         => __( 'iBeacons', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( ),
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
	register_post_type( 'iBeacon', $args );
}

define('JMC_UUID', 'jmcbeacon-uuid'); 
define('JMC_MAJOR', 'jmcbeacon-major'); 
define('JMC_MINOR', 'jmcbeacon-minor'); 
define('JMC_CONTENT', 'jmcbeacon-content'); 

// Hook into the 'init' action
add_action( 'init', 'iBeacon_post_type', 0 );

//change characteristics of beacons with meta boxes
add_action( 'add_meta_boxes', 'jmcbeacons_set_ibeacon_metaboxes' );
add_action( 'save_post', 'jmcbeacons_save_meta', 10, 2 );

function jmcbeacons_set_ibeacon_metaboxes($post){
		add_meta_box(
		iBeacon::$JMC_UUID,		// Unique ID
		 esc_html__( 'Choose iBeacon\'s UUID', 'example' ),	// Title
		'jmcbeacon_UUID_meta_box',		// Callback function
		'iBeacon',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
		add_meta_box(
		iBeacon::$JMC_MAJOR,		// Unique ID
		 esc_html__( 'Choose iBeacon\'s major', 'example' ),	// Title
		'jmcbeacon_major_meta_box',		// Callback function
		'iBeacon',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
		add_meta_box(
		iBeacon::$JMC_MINOR,		// Unique ID
		 esc_html__( 'Choose iBeacon\'s minor', 'example' ),	// Title
		'jmcbeacon_minor_meta_box',		// Callback function
		'iBeacon',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
}



function jmcbeacon_UUID_meta_box($post){
	
		$uuid = get_post_meta( $post->ID,iBeacon::$JMC_UUID , true );
		?>
		<input type="text" name="<?php echo iBeacon::$JMC_UUID;?>" id="<?php echo iBeacon::$JMC_UUID;?>" value="<?php echo $uuid ?>" />
   

	<?php
}

function jmcbeacon_major_meta_box($post){
		$major = get_post_meta( $post->ID,iBeacon::$JMC_MAJOR , true );
	?>
		<input type="text" name="<?php echo iBeacon::$JMC_MAJOR;?>" id="<?php echo iBeacon::$JMC_MAJOR;?>" value="<?php echo $major ?>" />
   
	<?php
}


function jmcbeacon_minor_meta_box($post){
		$minor = get_post_meta( $post->ID,iBeacon::$JMC_MINOR , true );
	?>
		<input type="text" name="<?php echo iBeacon::$JMC_MINOR;?>" id="<?php echo iBeacon::$JMC_MINOR;?>" value="<?php echo $minor ?>" />
   
	<?php
}


function ibeacon_associated_content($post){
	 //get content that is associated with a beacon
		
	
		$content = get_post_meta( $post->ID,iBeacon::$JMC_MINOR , true );
	?>
		<input type="text" name="<?php echo iBeacon::$JMC_MINOR;?>" id="<?php echo iBeacon::$JMC_MINOR;?>" value="<?php echo $minor ?>" />
   
	<?php
}

function jmcbeacons_save_meta($post_id, $post){
	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
	$uuid = $_POST[iBeacon::$JMC_UUID];
	$major = $_POST[iBeacon::$JMC_MAJOR];
	$minor = $_POST[iBeacon::$JMC_MINOR];
	if($uuid){
		$var = update_post_meta( $post_id, iBeacon::$JMC_UUID, $uuid);	
		}	
	if($major){
		update_post_meta( $post_id, iBeacon::$JMC_MAJOR, $major);	
	}
	if($minor){
		update_post_meta( $post_id, iBeacon::$JMC_MINOR, $minor);		
	}
}










?>