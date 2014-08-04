 <?php
//register region event
function addRegionEvent( $date, $entered, $user, $beacon_id){
	global $wpdb;
	$region_table_name = $wpdb->prefix."_region_events";
	
	$wpdb->insert($region_table_name,
		array(
		'entered'=>$entered,
		'event_date' =>$date,
		'user' =>$user,
		'beacon_id' =>$beacon_id
		
		)	
	);
}

//registers proximity event
function addProximityEvent( $date,$proximity, $user, $beacon_id){
	global $wpdb;
	$proximity_table_name = $wpdb->prefix."_proximity_events";
		$wpdb->insert($proximity_table_name,
		array(
		'proximity'=>$proximity,
		'event_date' =>$date,
		'user' =>$user,
		'beacon_id' =>$beacon_id
		
		)	
	);
}

function getRegionEvent(){
	
}

function getProximityEvents(){
	
}






?>