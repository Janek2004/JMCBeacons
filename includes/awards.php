<?php
require_once("constants.php");

//Creates post programmatically
function createAwardProgrammatically($badge_id, $email)
{
		global $user_ID;
		$new_post = array(
		'post_title' => 'New Award',
		'post_status' => 'publish',
		'post_date' => date('Y-m-d H:i:s'),
		'post_author' => $user_ID,
		'post_type' => 'award',
);
$post_id = wp_insert_post($new_post);
if($post_id)
{
	add_post_meta($post_id, 'wpbadger-award-choose-badge', $badge_id);
	add_post_meta($post_id, 'wpbadger-award-email-address',  $email);
	add_post_meta($post_id, 'wpbadger-award-status',  'Awarded');
	//add_post_meta($post_id, 'wpbadger-award-expiration-date',  'Awarded');
	//	die("email is: ".$email);

	wp_redirect(get_permalink( $post_id));
	exit;
}
}

function isAwarded($badge_id, $email){
	
}

class WPbadger_Award_Schema {
	private $post_type_name;

	function __construct() {
		$this->set_post_type_name();

		add_action( 'init', array( &$this, 'register_post_type' ) );
	}
	
	public function get_post_type_name() {
		return $this->post_type_name;
	}

	private function set_post_type_name() {
		$this->post_type_name = apply_filters( 'wpbadger_award_post_type_name', 'award' );
	}

	function register_post_type() {
		$labels = array(
			'name' => _x('Awards', 'post type general name'),
			'singular_name' => _x('Award', 'post type singular name'),
			'add_new' => _x('Add New', 'award'),
			'add_new_item' => __('Add New Award'),
			'edit_item' => __('Edit Award'),
			'new_item' => __('New Award'),
			'all_items' => __('All Awards'),
			'view_item' => __('View Award'),
			'search_items' => __('Search Awards'),
			'not_found' =>  __('No awards found'),
			'not_found_in_trash' => __('No award found in Trash'),
			'parent_item_colon' => '',
			'menu_name' => 'Awards'
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'query_var' => true,
			'rewrite'      => array(
				'slug'       => 'awards',
				'with_front' => false
			),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'supports' => false
		);

		register_post_type( $this->get_post_type_name(), $args );
		$this->add_rewrite_tags();
	}

	/**
	 * Add rewrite tags
	 *
	 * @since 1.2
	 */
	function add_rewrite_tags() {
		add_rewrite_tag( '%%accept%%', '([1]{1,})' );
		add_rewrite_tag( '%%json%%', '([1]{1,})' );
		add_rewrite_tag( '%%issuer%%', '([1]{1,})' );
		add_rewrite_tag( '%%badge_json%%', '([1]{1,})' );
		add_rewrite_tag( '%%reject%%', '([1]{1,})' );
	}

	/**
	 * Generates custom rewrite rules
	 *
	 * @since 1.2
	 */
	function generate_rewrite_rules( $wp_rewrite ) {
		$rules = array(
			// Create rewrite rules for each action
			'awards/([^/]+)/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ),
			'awards/([^/]+)/accept/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ) . '&accept=1',
			'awards/([^/]+)/json/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ) . '&json=1',			
			'awards/([^/]+)/issuer/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ) . '&issuer=1',
	
			'awards/([^/]+)/badge_json/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ) . '&badge_json=1',
	
			'awards/([^/]+)/reject/?$' =>
				'index.php?post_type=' . $this->get_post_type_name() . '&name=' . $wp_rewrite->preg_index( 1 ) . '&reject=1',
		);

		// Merge new rewrite rules with existing
		$wp_rewrite->rules = array_merge( $rules, $wp_rewrite->rules );
			
		return $wp_rewrite;
	}
}
new WPbadger_Award_Schema();

add_action( 'load-post.php', 'wpbadger_awards_meta_boxes_setup' );
add_action( 'load-post-new.php', 'wpbadger_awards_meta_boxes_setup' );

function wpbadger_awards_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'wpbadger_add_award_meta_boxes' );
	add_action( 'save_post', 'wpbadger_save_award_meta', 10, 2 );
}

// Create metaboxes for post editor
function wpbadger_add_award_meta_boxes() {

/*
		add_meta_box(
		EVIDENCE_META_BOX,		// Unique ID
		 esc_html__( 'Evidence (Optional)', 'example' ),	// Title
		'wpbadger_evidence_meta_box',		// Callback function
		'award',						// Admin page (or post type)
		'advanced',							// Context
		'high'						// Priority
	);
*/

	add_meta_box(
		'wpbadger-award-choose-badge',		// Unique ID
		esc_html__( 'Choose Badge', 'example' ),	// Title
		'wpbadger_award_choose_badge_meta_box',		// Callback function
		'award',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
	add_meta_box(
		'wpbadger-award-email-address',		// Unique ID
		esc_html__( 'Email Address', 'example' ),	// Title
		'wpbadger_award_email_address_meta_box',		// Callback function
		'award',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);

	add_meta_box(
		'wpbadger-award-status',		// Unique ID
		esc_html__( 'Award Status', 'example' ),	// Title
		'wpbadger_award_status_meta_box',		// Callback function
		'award',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
	
	add_meta_box(
		'wpbadger-award-expiration-date',			// Unique ID
		 esc_html__( 'Expiration Date', 'example' ),		// Title
		'wpbadger_expiration_date_meta_box',		// Callback function
		'award',						// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);	
}

function wpbadger_evidence_meta_box($post, $args) {
	?>
	<p>Enter here the evidence</p>
	
<?php
}


function wpbadger_expiration_date_meta_box($post, $args) {
    $metabox_id = $args['args']['id'];
    global $post, $wp_locale;
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'ep_eventposts_nonce' );
    $time_adj = current_time( 'timestamp' );
    $month = get_post_meta( $post->ID, $metabox_id . '_month', true );
    if ( empty( $month ) ) {
        $month = gmdate( 'm', $time_adj );
    }
    $day = get_post_meta( $post->ID, $metabox_id . '_day', true );
    if ( empty( $day ) ) {
        $day = gmdate( 'd', $time_adj );
    }
    $year = get_post_meta( $post->ID, $metabox_id . '_year', true );
    if ( empty( $year ) ) {
        $year = gmdate( 'Y', $time_adj );
    }
		$year +=80;
		
		
    $hour = get_post_meta($post->ID, $metabox_id . '_hour', true);
    if ( empty($hour) ) {
        $hour = gmdate( 'H', $time_adj );
    }
    $min = get_post_meta($post->ID, $metabox_id . '_minute', true);
    if ( empty($min) ) {
        $min = '00';
    }
    $month_s = '<select name="' . $metabox_id . '_month">';
    for ( $i = 1; $i < 13; $i = $i +1 ) {
        $month_s .= "\t\t\t" . '<option value="' . zeroise( $i, 2 ) . '"';
        if ( $i == $month )
            $month_s .= ' selected="selected"';
        $month_s .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
    }
    $month_s .= '</select>';
    echo $month_s;
    echo '<input type="text" name="' . $metabox_id . '_day" value="' . $day  . '" size="2" maxlength="2" />';
    echo '<input type="text" name="' . $metabox_id . '_year" value="' . $year . '" size="4" maxlength="4" /> @ ';
    echo '<input type="text" name="' . $metabox_id . '_hour" value="' . $hour . '" size="2" maxlength="2"/>:';
    echo '<input type="text" name="' . $metabox_id . '_minute" value="' . $min . '" size="2" maxlength="2" />';
}


// Display metaboxes
function wpbadger_award_choose_badge_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'wpbadger_award_nonce' ); ?>
	
	<?php $chosen_badge_id = get_post_meta( $object->ID, 'wpbadger-award-choose-badge', true );?>
	
  
	
  <p>
	<select name="wpbadger-award-choose-badge" id="wpbadger-award-choose-badge">
	
	<?php 	
	$query = new WP_Query( array( 'post_type' => 'badge' ) );
	
	while ( $query->have_posts() ) : $query->the_post();
		$badge_title_version = the_title(null, null, false) . " (" . get_post_meta(get_the_ID(), 'wpbadger-badge-version', true) . ")";

		// As we iterate through the list of badges, if the chosen badge has the same ID then mark it as selected
		if ($chosen_badge_id == get_the_ID()) { 
			$selected = " selected";
		} else {
			$selected = "";
		}
		echo "<option name='wpbadger-award-choose-badge' value='" . get_the_ID() . "'". $selected . ">";
		echo $badge_title_version . "</option>";
	endwhile;
	?>
	
	</select>
	</p>
<?php }

function wpbadger_award_email_address_meta_box( $object, $box ) { ?>

	<p>
		<input class="widefat" type="text" name="wpbadger-award-email-address" id="wpbadger-award-email-address" value="<?php echo esc_attr( get_post_meta( $object->ID, 'wpbadger-award-email-address', true ) ); ?>" size="30" />
	</p>
<?php }

function wpbadger_award_status_meta_box( $object, $box ) { ?>
	<p>
	<select name="wpbadger-award-status" id="wpbadger-award-status">

	<?php
	$award_status = get_post_meta( $object->ID, 'wpbadger-award-status', true );
	$award_status_options = array('Awarded', 'Accepted', 'Rejected');
	
	foreach ($award_status_options as $status_option) {

		// Mark the 
		if ($status_option == $award_status) { 
			$selected = " selected";
		} else {
			$selected = "";
		}
		
		echo "<option name='wpbadger-award-status'" . $selected . ">" . $status_option . "</option>";
	}
	?>
	
	</select>
	</p>

<?php }

function wpbadger_save_award_meta( $post_id, $post ) {

	if ( !isset( $_POST['wpbadger_award_nonce'] ) || !wp_verify_nonce( $_POST['wpbadger_award_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	$chosen_badge_new_meta_value = $_POST['wpbadger-award-choose-badge'];
	$chosen_badge_meta_key = 'wpbadger-award-choose-badge';
	$chosen_badge_meta_value = get_post_meta( $post_id, $chosen_badge_meta_key, true );

	$email_new_meta_value = $_POST['wpbadger-award-email-address'];
	$email_meta_key = 'wpbadger-award-email-address';
	$email_meta_value = get_post_meta( $post_id, $email_meta_key, true );

	$status_new_meta_value = $_POST['wpbadger-award-status'];
	$status_meta_key = 'wpbadger-award-status';
	$status_meta_value = get_post_meta( $post_id, $status_meta_key, true );
	
	$salt = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 8)), 0, 8);

	if ( $chosen_badge_new_meta_value ) {
		update_post_meta( $post_id, $chosen_badge_meta_key, $chosen_badge_new_meta_value );
	} elseif ( '' == $chosen_badge_new_meta_value ) {
		delete_post_meta( $post_id, $chosen_badge_meta_key, $chosen_badge_meta_value );
	}
	
	if ( $email_new_meta_value && '' == $email_meta_value ) {
		add_post_meta( $post_id, $email_meta_key, $email_new_meta_value, true );
	} elseif ( $email_new_meta_value && $email_new_meta_value != $email_meta_value ) {
		update_post_meta( $post_id, $email_meta_key, $email_new_meta_value );
	} elseif ( '' == $email_new_meta_value && $email_meta_value ) {
		delete_post_meta( $post_id, $email_meta_key, $email_meta_value );	
	}
	
	if ( $status_new_meta_value && '' == $status_meta_value ) {
		add_post_meta( $post_id, $status_meta_key, $status_new_meta_value, true );
	} elseif ( $status_new_meta_value && $status_new_meta_value != $status_meta_value ) {
		update_post_meta( $post_id, $status_meta_key, $status_new_meta_value );
	} elseif ( '' == $status_new_meta_value && $status_meta_value ) {
		delete_post_meta( $post_id, $status_meta_key, $status_meta_value );	
	}
	
	// Add the salt only the first time, and do not update if already exists
	if (get_post_meta($post_id, 'wpbadger-award-salt', true) == false) {
		add_post_meta($post_id, 'wpbadger-award-salt', $salt);
	}
}



add_action( 'wp_insert_post', 'wpbadger_award_send_email' );

function wpbadger_award_send_email( $post_id ) {
	// Verify that post has been published, and is an award
	
	if ((get_post_type($post_id) == 'award') && (get_post_status($post_id) == 'publish') && (get_post_meta($post_id, 'wpbadger-award-status', true) == 'Awarded')) {
		$email_address = get_post_meta($post_id, 'wpbadger-award-email-address', true);
		$badge = get_the_title(get_post_meta($post_id, 'wpbadger-award-choose-badge', true));

		$post_title = get_the_title( $post_id );
		$post_url = get_permalink( $post_id );
		$subject = "Congratulations! You have been awarded the " . $badge . " badge!";
	
	  $post_thumbnail_id = get_post_thumbnail_id( $post_id);
	  $attachment_url = wp_get_attachment_thumb_url($post_thumbnail_id);
	if (get_option('wpbadger_config_award_email_text')) {
			$message = get_option('wpbadger_config_award_email_text') . "\n\n";
	} else {
			$message = "<div style='float:left;'>Congratulations, " . get_option('wpbadger_issuer_org') . " has awarded you a badge. Please visit the link below to redeem it.<br />".$post_url." </div><br />";			
		}
		
		$chosen_badge_id = get_post_meta($post_id, 'wpbadger-award-choose-badge', true);
		$post_thumbnail_id = get_post_thumbnail_id($chosen_badge_id); 
		$thumbnail_url = wp_get_attachment_url( $post_thumbnail_id ); 
		$message .="<a href='".$post_url."'  style='margin:auto;' ><img src='".$thumbnail_url."'/></a>";
		


		$headers = 'Content-type: text/html';
		wp_mail( $email_address, $subject, $message,$headers );

	}
}

add_filter( 'user_can_richedit', 'wpbadger_disable_wysiwyg_for_awards' );

function wpbadger_disable_wysiwyg_for_awards( $default ) {
    global $post;
    if ( 'award' == get_post_type( $post ) )
        return false;
    return $default;
}

// Runs before saving a new post, and filters the post data
add_filter('wp_insert_post_data', 'wpbadger_award_save_title', '99', 2);

function wpbadger_award_save_title($data, $postarr) {
	if ($_POST['post_type'] == 'award') {
		$data['post_title'] = "Badge Awarded: " . get_the_title($_POST['wpbadger-award-choose-badge']);
	}
	return $data;
}

// Generate the award slug. Shared by interface to award single badges, as well as bulk
function wpbadger_award_generate_slug() {
	$slug = rand(100000000000000, 999999999999999);
	
	return $slug;
}

// Runs before saving a new post, and filters the post slug
add_filter('name_save_pre', 'wpbadger_award_save_slug');

function wpbadger_award_save_slug($my_post_slug) {
	if ($_POST['post_type'] == 'award') {
		$my_post_slug = wpbadger_award_generate_slug();		
	}
	return $my_post_slug;
}
?>