<?php global $post;
header('Content-Type: application/json');
	//get all missions
	if(isset($_REQUEST['beacon_uuid'])){
		
			$beacon_id = $_REQUEST['beacon_uuid'];	//get uuid
			$beacon_major =$_REQUEST['beacon_major']; //get major
			$beacon_minor =$_REQUEST['beacon_minor']; //get minor
		
			$array = iBeacon::getBeaconID($beacon_id, $beacon_major, $beacon_minor);
		
			if(count($array)>0){
		
				//take always first one
				$ibeacon = $array[0];
				//print_r($ibeacon);
			
				$station_array = Station::getStationsForBeacon($ibeacon);
			//	print_r($station_array);
				
			foreach($station_array as $station){
						echo Station::getJSONRepresentation($station);	
				
				}
			}
			//based on the id information display message and information		
	}
	else{
	
	$missions=getMissions();
	?>{"missions":[
  <?php
	$record_count = $missions->post_count;
	$counter = 0;
	//echo $count;
	//var_dump($missions);
	
	foreach ($missions->posts as $mission){
							$stations = getStationsForMission($mission->ID);
						echo '{"name":"'.$mission->post_title.'",';
						echo '"stations":';
						//stations
						echo $stations;
						echo ',"id":"'.$mission->ID.'"}';	
						if($counter!=$record_count-1){						
							echo ",";
						}
						$counter++;
						
				}
				
	
?>]}
<?php }
wp_reset_postdata();
	function getBeacons(){
		
	}

	function getStationsForMission($id=NULL){
			$station_object = new Station();
			
			$args;
			if($id===NULL){
				$args = array(
					'post_type' => 'Station',
					'post_status' => 'publish'
				);			
			}
			else{
							$args = array(
						'post_type' => 'Station',
						'meta_key'=>Station::$station_parent_id_key,
						'meta_value'=>$id
				
				);		
			}

		$stations = new WP_Query( $args );		
		$json_string = '[';
		$record_count = $stations->post_count;
		$counter = 0;
		
		if($stations ->have_posts()):
			foreach($stations->posts as $station):
					$json_string=$json_string."{";
						$json_string=$json_string.'"id":"'.$station->ID.'",';
						$json_string=$json_string.'"name":"'.$station->title.'",';
						$json_string=$json_string.'"immediate_message":"'.$station_object->getImmediateMessage($station->ID).'",';
						$json_string=$json_string.'"nearby_message":"'.$station_object->getNearbyMessage($station->ID).'"';
					
						$json_string=$json_string."}";
						if($counter != $record_count-1){
							$json_string=$json_string.",";	
						 }
				$counter++;
			endforeach;	
		endif;
		$json_string = $json_string.']';
		
		return $json_string;
	}
	
	

	
	function getMissions(){
			$args = array(
			'post_type' => 'Mission',
			'post_status'      => 'publish'
			);
			$query = new WP_Query( $args );
			return $query;
	}
	/*
	 array(61) {
    ["post_type"]=>
    string(7) "mission"
    ["error"]=>
    string(0) ""
    ["m"]=>
    string(0) ""
    ["p"]=>
    int(0)
    ["post_parent"]=>
    string(0) ""
    ["subpost"]=>
    string(0) ""
    ["subpost_id"]=>
    string(0) ""
    ["attachment"]=>
    string(0) ""
    ["attachment_id"]=>
    int(0)
    ["name"]=>
    string(0) ""
    ["static"]=>
    string(0) ""
    ["pagename"]=>
    string(0) ""
    ["page_id"]=>
    int(0)
    ["second"]=>
    string(0) ""
    ["minute"]=>
    string(0) ""
    ["hour"]=>
    string(0) ""
    ["day"]=>
    int(0)
    ["monthnum"]=>
    int(0)
    ["year"]=>
    int(0)
    ["w"]=>
    int(0)
    ["category_name"]=>
    string(0) ""
    ["tag"]=>
    string(0) ""
    ["cat"]=>
    string(0) ""
    ["tag_id"]=>
    string(0) ""
    ["author"]=>
    string(0) ""
    ["author_name"]=>
    string(0) ""
    ["feed"]=>
    string(0) ""
    ["tb"]=>
    string(0) ""
    ["paged"]=>
    int(0)
    ["comments_popup"]=>
    string(0) ""
    ["meta_key"]=>
    string(0) ""
    ["meta_value"]=>
    string(0) ""
    ["preview"]=>
    string(0) ""
    ["s"]=>
    string(0) ""
    ["sentence"]=>
    string(0) ""
    ["fields"]=>
    string(0) ""
    ["menu_order"]=>
    string(0) ""
    ["category__in"]=>
    array(0) {
    }
    ["category__not_in"]=>
    array(0) {
    }
    ["category__and"]=>
    array(0) {
    }
    ["post__in"]=>
    array(0) {
    }
    ["post__not_in"]=>
    array(0) {
    }
    ["tag__in"]=>
    array(0) {
    }
    ["tag__not_in"]=>
    array(0) {
    }
    ["tag__and"]=>
    array(0) {
    }
    ["tag_slug__in"]=>
    array(0) {
    }
    ["tag_slug__and"]=>
    array(0) {
    }
    ["post_parent__in"]=>
    array(0) {
    }
    ["post_parent__not_in"]=>
    array(0) {
    }
    ["author__in"]=>
    array(0) {
    }
    ["author__not_in"]=>
    array(0) {
    }
    ["ignore_sticky_posts"]=>
    bool(false)
    ["suppress_filters"]=>
    bool(false)
    ["cache_results"]=>
    bool(true)
    ["update_post_term_cache"]=>
    bool(true)
    ["update_post_meta_cache"]=>
    bool(true)
    ["posts_per_page"]=>
    int(10)
    ["nopaging"]=>
    bool(false)
    ["comments_per_page"]=>
    string(2) "50"
    ["no_found_rows"]=>
    bool(false)
    ["order"]=>
    string(4) "DESC"
  }
  ["tax_query"]=>
  object(WP_Tax_Query)#255 (2) {
    ["queries"]=>
    array(0) {
    }
    ["relation"]=>
    string(3) "AND"
  }
  ["meta_query"]=>
  object(WP_Meta_Query)#254 (2) {
    ["queries"]=>
    array(0) {
    }
    ["relation"]=>
    NULL
  }
  ["date_query"]=>
  bool(false)
  ["post_count"]=>
  int(2)
  ["current_post"]=>
  int(-1)
  ["in_the_loop"]=>
  bool(false)
  ["comment_count"]=>
  int(0)
  ["current_comment"]=>
  int(-1)
  ["found_posts"]=>
  string(1) "2"
  ["max_num_pages"]=>
  float(1)
  ["max_num_comment_pages"]=>
  int(0)
  ["is_single"]=>
  bool(false)
  ["is_preview"]=>
  bool(false)
  ["is_page"]=>
  bool(false)
  ["is_archive"]=>
  bool(false)
  ["is_date"]=>
  bool(false)
  ["is_year"]=>
  bool(false)
  ["is_month"]=>
  bool(false)
  ["is_day"]=>
  bool(false)
  ["is_time"]=>
  bool(false)
  ["is_author"]=>
  bool(false)
  ["is_category"]=>
  bool(false)
  ["is_tag"]=>
  bool(false)
  ["is_tax"]=>
  bool(false)
  ["is_search"]=>
  bool(false)
  ["is_feed"]=>
  bool(false)
  ["is_comment_feed"]=>
  bool(false)
  ["is_trackback"]=>
  bool(false)
  ["is_home"]=>
  bool(true)
  ["is_404"]=>
  bool(false)
  ["is_comments_popup"]=>
  bool(false)
  ["is_paged"]=>
  bool(false)
  ["is_admin"]=>
  bool(false)
  ["is_attachment"]=>
  bool(false)
  ["is_singular"]=>
  bool(false)
  ["is_robots"]=>
  bool(false)
  ["is_posts_page"]=>
  bool(false)
  ["is_post_type_archive"]=>
  bool(false)
  ["query_vars_hash"]=>
  string(32) "1ead775ed9af4fde37eaa59a3ab04fd7"
  ["query_vars_changed"]=>
  bool(false)
  ["thumbnails_cached"]=>
  bool(false)
  ["stopwords":"WP_Query":private]=>
  NULL
  ["query"]=>
  array(1) {
    ["post_type"]=>
    string(7) "Mission"
  }
  ["request"]=>
  string(257) "SELECT SQL_CALC_FOUND_ROWS  wp_badgesposts.ID FROM wp_badgesposts  WHERE 1=1  AND wp_badgesposts.post_type = 'mission' AND (wp_badgesposts.post_status = 'publish' OR wp_badgesposts.post_status = 'private')  ORDER BY wp_badgesposts.post_date DESC LIMIT 0, 10"
  ["posts"]=>
  array(2) {
    [0]=>
    object(WP_Post)#256 (24) {
      ["ID"]=>
      int(103)
      ["post_author"]=>
      string(1) "1"
      ["post_date"]=>
      string(19) "2014-06-04 19:38:06"
      ["post_date_gmt"]=>
      string(19) "2014-06-04 19:38:06"
      ["post_content"]=>
      string(0) ""
      ["post_title"]=>
      string(9) "Mission 2"
      ["post_excerpt"]=>
      string(0) ""
      ["post_status"]=>
      string(7) "publish"
      ["comment_status"]=>
      string(6) "closed"
      ["ping_status"]=>
      string(6) "closed"
      ["post_password"]=>
      string(0) ""
      ["post_name"]=>
      string(9) "mission-2"
      ["to_ping"]=>
      string(0) ""
      ["pinged"]=>
      string(0) ""
      ["post_modified"]=>
      string(19) "2014-06-04 19:38:06"
      ["post_modified_gmt"]=>
      string(19) "2014-06-04 19:38:06"
      ["post_content_filtered"]=>
      string(0) ""
      ["post_parent"]=>
      int(0)
      ["guid"]=>
      string(56) "http://localhost/Badges/wp/?post_type=mission&#038;p=103"
      ["menu_order"]=>
      int(0)
      ["post_type"]=>
      string(7) "mission"
      ["post_mime_type"]=>
      string(0) ""
      ["comment_count"]=>
      string(1) "0"
      ["filter"]=>
      string(3) "raw"
    }
    [1]=>
    object(WP_Post)#266 (24) {
      ["ID"]=>
      int(100)
      ["post_author"]=>
      string(1) "1"
      ["post_date"]=>
      string(19) "2014-06-04 17:49:30"
      ["post_date_gmt"]=>
      string(19) "2014-06-04 17:49:30"
      ["post_content"]=>
      string(22) "Description goes here."
      ["post_title"]=>
      string(9) "Mission 1"
      ["post_excerpt"]=>
      string(0) ""
      ["post_status"]=>
      string(7) "publish"
      ["comment_status"]=>
      string(6) "closed"
      ["ping_status"]=>
      string(6) "closed"
      ["post_password"]=>
      string(0) ""
      ["post_name"]=>
      string(9) "mission-1"
      ["to_ping"]=>
      string(0) ""
      ["pinged"]=>
      string(0) ""
      ["post_modified"]=>
      string(19) "2014-06-04 17:49:30"
      ["post_modified_gmt"]=>
      string(19) "2014-06-04 17:49:30"
      ["post_content_filtered"]=>
      string(0) ""
      ["post_parent"]=>
      int(0)
      ["guid"]=>
      string(56) "http://localhost/Badges/wp/?post_type=mission&#038;p=100"
      ["menu_order"]=>
      int(0)
      ["post_type"]=>
      string(7) "mission"
      ["post_mime_type"]=>
      string(0) ""
      ["comment_count"]=>
      string(1) "0"
      ["filter"]=>
      string(3) "raw"
    }
  }
  */
?>