 <?php
//register region event
function addRegionEvent( $date, $state, $user, $beacon_id){
	global $wpdb;
	$region_table_name = $wpdb->prefix."_region_events";
	$date = date('Y-m-d H:i:s',$date);
	$wpdb->insert($region_table_name,
		array(
		'state'=>$state,
		
		'user' =>$user,
		'beacon_id' =>$beacon_id	
		)	
	);
	
	echo $date; 
	die();
	
	
}

//registers proximity event
function addProximityEvent( $date,$proximity, $user, $beacon_id){
	global $wpdb;
	$proximity_table_name = $wpdb->prefix."_proximity_events";
		$wpdb->insert($proximity_table_name,
		array(
		'proximity'=>$proximity,
		
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