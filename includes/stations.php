<?php
error_reporting(-1);

// Same as error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);


class Station{
	//public static 	
	public static $associated_beacon_key = 'associated-jmcbeacon-id';
	public static $station_parent_id_key = 'jmcbeacons-station-parent-id';
	public static $immediate_message_key = 'jmcbeacons-station-immediate-message-id_text';
	public static $nearby_message_key 	 = 'jmcbeacons-station-nearby-message-id_text';
	public static	$immediate_radio_key = 'jmcbeacons-station-immediate-message-id_text_delete_image_attachment_radio';
	public static	$nearby_radio_key		 = 'jmcbeacons-station-nearby-message-id_text_delete_image_attachment_radio';
	public static	$immediate_attachment_key = 'jmcbeacons-station-immediate-message-id_text_attachment';
	public static	$nearby_attachment_key = 'jmcbeacons-station-nearby-message-id_text_attachment';
	public static $jmc_associated_beacon_key = 'associated-jmcbeacon-id';

	public static function getImmediateMessage($id){
		return get_post_meta( $id, Station::$immediate_message_key, true );
	}
	
	public static function getNearbyMessage($id){
		return get_post_meta( $id, Station::$nearby_message_key, true );
	}
	
	public static function getNearbyAttachment($id){
		return get_post_meta( $id, Station::$nearby_attachment_key, false );
	}
	
	public static function getImmediateAttachment($id){
		return get_post_meta( $id, Station::$immediate_attachment_key, false );
	}

	public static function getStationsForBeacon($beacon){
		$args= array(
				'post_type'=>'Station',
				'post_status' => 'publish',
				'meta_query' => array(
        array(
            'key' => Station::$associated_beacon_key,
            'value' => $beacon->ID,
						'compare'=>'LIKE'
						
						)
					)
			 );				
		$query  = new WP_Query($args);	
		$posts = $query->get_posts();
	
		
		return $posts;	
	}
	
	public static function getJSONRepresentation($station){
		//print_r($station);
		//station needs to be Post type
			$json_string = '{';
					$json_string=$json_string.'"id":"'.$station->ID.'",';
					$json_string=$json_string.'"name":"'.$station->post_title.'",';
					$json_string=$json_string.'"immediate_message":"'.Station::getImmediateMessage($station->ID).'",';
					$json_string=$json_string.'"nearby_message":"'.Station::getNearbyMessage($station->ID).'",';
					//getting attachments
					$json_string=$json_string.'"nearby_attachments":[';
			
					$nearby_attachments = Station::getNearbyAttachment($station->ID);
					$nearby_attachments=$nearby_attachments[0];
					$counter = 0;
					$att_count = count($nearby_attachments);
					foreach($nearby_attachments as $attachment_id)
					{	
							
							$json_string=$json_string.'"'.wp_get_attachment_url($attachment_id).'"';
							if($att_count-1 !== $counter){
									$json_string=$json_string.',';	
							}
						$counter++;
					}
					$json_string=$json_string.'],';
					$json_string=$json_string.'"immediate_attachments":[';
			
					$immediate_attachments = Station::getImmediateAttachment($station->ID);
					
					if(!empty($immediate_attachments)){
						$immediate_attachments=$immediate_attachments[0];
						$counter = 0;
						$att_count = count($immediate_attachments);
						foreach($immediate_attachments as $attachment_id)
						{	
							$json_string=$json_string.'"'.wp_get_attachment_url($attachment_id).'"';
							if($att_count-1 !== $counter){
									$json_string=$json_string.',';	
						}
					  	$counter++;
					}					
				}
						$json_string=$json_string.']';				
			$json_string = $json_string.'}';
		return $json_string;
	}
}

global $user_ID;
global $post;
// Register Custom Post Type
if (!function_exists('stations_post_type')){
function stations_post_type() {

	$labels = array(
		'name'                => _x( 'Stations', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Station', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Stations', 'text_domain' ),
		'parent_item_colon'   => __( 'Mission:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'add_new_item'        => __( 'Add New Station', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'description'         => __( 'iBeacons Stations', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array('page-attributes','title','editor','thumbnail'),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => true,
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
		'capability_type'     => 'post'
	
	);
	
	if(!post_type_exists('Station')){
				register_post_type( 'Station', $args );	
	}
}
}

if (!function_exists('edit_form_type_wpse_98375')){
function edit_form_type_wpse_98375() {
    echo ' enctype="multipart/form-data"';
	
}
}

	// Hook into the 'init' action
 	add_action( 'init', 'stations_post_type', 0 );
 	add_action( 'add_meta_boxes', 'jmcbeacons_set_stations_metaboxes' );
	add_action( 'save_post', 'jmcbeacons_save_station_meta', 10, 2 );
	add_action('post_edit_form_tag','edit_form_type_wpse_98375');

if (!function_exists('jmcbeacons_set_stations_metaboxes')){
function jmcbeacons_set_stations_metaboxes($post){
	add_meta_box(
		Station::$station_parent_id_key,		// Unique ID
		 esc_html__( 'Settings', 'example' ),	// Title
		'station_parent_meta_box',		// Callback function
		'Station',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
	//immediate
	add_meta_box(
		Station::$immediate_message_key,		// Unique ID
		 esc_html__( 'Choose Immediate Message', 'example' ),	// Title
		'station_message_meta_box',		// Callback function
		'Station',						// Admin page (or post type)
		'side',							// Context
		'default',						// Priority
		 array('beaconid'=>Station::$immediate_message_key)
	);
	
	//nearby
	add_meta_box(
		Station::$nearby_message_key,		// Unique ID
		 esc_html__( 'Choose Nearby Message', 'example' ),	// Title
		'station_message_meta_box',		// Callback function
		'Station',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
		// array('jmcbeacons-station-nearby-message-id')
	);
	
	
	 remove_meta_box( 'postimagediv', 'Station', 'side' );
   add_meta_box('postimagediv', __('Set Image to Show'), 'post_thumbnail_meta_box', 'Station', 'normal', 'high');
	
	add_meta_box(
		'jmcbeacons-station-time-id',		// Unique ID
		 esc_html__( 'Choose Time', 'example' ),	// Title
		'station_time_meta_box',		// Callback function
		'Station',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
		);
	}
}


//Saving station
if (!function_exists('jmcbeacons_save_station_meta')){
function jmcbeacons_save_station_meta($post_id, $post ){
	global $_FILES;
	
	$post_type = get_post_type_object( $post->post_type );
	
	
	//post_id
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
		//updating beacon
	
		$station_beacon = $_POST[Station::$associated_beacon_key];
		update_post_meta( $post_id, Station::$associated_beacon_key, $station_beacon);
			
		//updating parent
		$station_parent_id = $_POST[Station::$station_parent_id_key];
		update_post_meta( $post_id, Station::$station_parent_id_key, $station_parent_id);
	
		//updating messages
		$immediate_message = $_POST[Station::$immediate_message_key];
		$nearby_message = $_POST[Station::$nearby_message_key];
		
		update_post_meta( $post_id, Station::$nearby_message_key, $nearby_message);
		update_post_meta( $post_id, Station::$immediate_message_key, $immediate_message);
			
		$nearby_radio_key = Station::$nearby_radio_key;
		$immediate_key = Station::$immediate_attachment_key;
		$nearby_key = Station::$nearby_attachment_key;
			
		if(isset($_POST[Station::$nearby_radio_key])){
				if(!empty($_POST[Station::$nearby_radio_key])){
						deleteFiles($post, Station::$nearby_radio_key,Station::$nearby_attachment_key);
						
					}	
				}
		
		if(isset($_POST[Station::$immediate_radio_key])){
				if(!empty($_POST[Station::$immediate_radio_key])){
						deleteFiles($post, Station::$immediate_radio_key,Station::$immediate_attachment_key);
				}		
			
		}
		
		if(isset($_FILES)){			
			if(!empty($_FILES[Station::$immediate_attachment_key]['name'])){
				 addFiles($post, Station::$immediate_attachment_key);
			}	
			if(!empty($_FILES[Station::$nearby_attachment_key]['name'])){
				 addFiles($post, Station::$nearby_attachment_key);
			 }	
		}
}
}




/*Handles removing files*/
if (!function_exists('deleteFiles')){
function deleteFiles($post, $radio_key, $key){
	$values = $_POST[$radio_key];
	$meta_attachment_ids = get_post_meta( $post->ID, $key , false );
	$meta_array = 	$meta_attachment_ids[0]; 			
	
	//print_r($values);
	//echo print_r($meta_array);
		
	if(is_array($values)){
		
			foreach($values as $value){
			  wp_delete_attachment($value,true);
		  	delete_post_meta($post->ID, $key,$value);
	
				$check = array_search($value, $meta_array);
				print_r($check);				
					if($check!==false){
						unset($meta_array[$check]);											
						print_r($meta_array);									
						}
					}									
				}
	 else{
				wp_delete_attachment($values,true);
				$check = array_search($values, $meta_array);
				print_r($meta_array);
				
				if($check!==false){
						unset($meta_array[$check]);	
				}	
	}
	
		delete_post_meta($post->ID, $key);	
		update_post_meta($post->ID, $key,$meta_array);
	}
}


if (!function_exists('addFiles')){
/*Handles uploading files*/
function addFiles($post, $key){
		$attachment_id = media_handle_upload($key, $post->ID);						
		$meta_attachment_ids = get_post_meta( $post->ID, $key , false ); 	
		$meta_attachment_ids= $meta_attachment_ids[0];
	
		if(is_null($meta_attachment_ids)||empty($meta_attachment_ids)){
				$meta_attachment_ids = array();
		}
		array_push($meta_attachment_ids,$attachment_id);
		update_post_meta( $post->ID, $key, $meta_attachment_ids);
	}
}

if (!function_exists('getAttachment')){
function getAttachment($metaid){
	$attachment = $metaid."_attachment";
	$meta_attachment_id = get_post_meta( $post->ID, $attachment , true );
}
}

if (!function_exists('station_message_meta_box')){
function station_message_meta_box($post,$meta) {
	//get existing content and display it
	$metaid =	$meta['id'];
	$meta_message = get_post_meta( $post->ID, $metaid, true );
			
?>

<textarea id="<?php echo $metaid;?>" name="<?php echo $metaid;?>"><?php echo $meta_message ?></textarea>
<br />
<input  type="file" name="<?php echo $metaid.'_attachment';?>" id="<?php echo $metaid.'_attachment';?>" />
</input>
<?php 

	$attachment = $metaid."_attachment";
	$meta_attachment_ids = get_post_meta( $post->ID, $attachment , false );
	
	//deleteAllAttachments();
	//delete_post_meta($post->ID, $attachment);
	//print_r($meta_attachment_ids);
	//echo $attachment;
	//echo $post->ID;
	
	
	?>
<?php
	if(!empty($meta_attachment_ids[0])){
					foreach($meta_attachment_ids[0] as $att_id){
							$attachment_url = wp_get_attachment_url($att_id);		
						   $type = get_post_mime_type($att_id);
							 
							 ?>
					 <br />
					<input type="checkbox" name="<?php echo $metaid; ?>_delete_image_attachment_radio[]" value="<?php echo $att_id;?>" />
					 
					<?php  if ($type ==='image/jpeg' || $type ==='image/png')
						{
					 ?> 				
							<img alt="Attachment" src="<?php echo $attachment_url;?>" alt="" style="max-width:200px;" /><br />

					<?php }
					else{
							echo '<a href="'.$attachment_url.'">'.$attachment_url.' </a>';
						}
					
					
					    ?>
<?php
						}
	}
		
	if(!empty($meta_attachment_ids[0]))
	{
	?>
  <input type="submit" class="<?php echo $metaid; ?>_beacon_delete_images_button button button-primary button-large" value="Delete Selected" id="<?php echo $metaid; ?>_beacon_delete_images_button" />
<?php
	}
}
}

if (!function_exists('station_time_meta_box')){
function station_time_meta_box($post) {
  
	
	}
}

if (!function_exists('station_parent_meta_box')){
function station_parent_meta_box($post) {

	$args = array(
		'post_type' => 'Mission'
	);
	
	$query = new WP_Query( $args );
	
	$args = array(
		'post_type' => 'iBeacon'
	);
	
	$ibeacon_query = new WP_Query( $args );
	
	$count = 0;
	while ($ibeacon_query ->have_posts()){
	$ibeacon_query->the_post();
	
	$selected='';
			

	$beacon_ids = get_post_meta( $post->ID, Station::$associated_beacon_key, false );
	$beacon_ids= $beacon_ids[0];
		
	foreach ($beacon_ids as $beacon_id){
		if(intval($beacon_id) === get_the_ID()){
			$selected ='checked';	
			$count++;
		}	
	}
		
	?>
 		<input type="checkbox" name="<?php echo Station::$associated_beacon_key;?>[]" value="<?php echo get_the_ID();?>" <?php echo $selected; ?> /><?php echo get_the_title();?><br />
	
        

<?php
	}
	if(!$count>0){
		?>
	<p style="color:#FF0000">No beacon selected for this station.</p>
<?php
  }
	
	wp_reset_postdata();
	?>

<select id="<?php echo Station::$station_parent_id_key;?>" name="<?php echo Station::$station_parent_id_key;?>">
  <?php 
			$count = 0;
			while ($query ->have_posts()){
					$selected='';
					$query->the_post();
					$parent_id = intval(get_post_meta( $post->ID, Station::$station_parent_id_key, true ));
					if($parent_id === get_the_ID()){
						$selected ='selected';	
						$count++;
					}
					echo '<option value="'.get_the_ID().'" id="'.get_the_ID().'" '.$selected.' >' . get_the_title() . '</option>';
			}
		?>
        
</select>
<?php
	if(!$count>0){
		?>
<p style="color:#FF0000">No mission selected for this station.</p>
<?php
  }
	
	wp_reset_postdata();
 }
}
if (!function_exists('deleteAllAttachments')){
	
function deleteAllAttachments(){
	$args = array(
	'post_parent' => $post->ID,
	'post_type'   => 'attachment', 
	'posts_per_page' => -1,
	'post_status' => 'any' 
	);
	
	$children = get_children( $args);
	foreach($children as $child)
	{
	  echo " ". $child->ID;
		wp_delete_attachment($child->ID,true);	
	}
		
	delete_post_meta($post->ID,Station::$immediate_attachment_key);
	delete_post_meta($post->ID, Station::$nearby_attachment_key);

	//die("deleting");
	//print_r($children);
	//die();
}
}

?>
