<?php

/**Log's in user and returns id of the row that can be used as a session id */
function loginUser($user){
	global $wpdb;
	$wpdb->show_errors();
	$session_table_name = $wpdb->prefix."_session_events";
	$date =date('Y-m-d G:i:s');
	$wpdb->insert($session_table_name,
	array(
		'login_date'=>$date,
		'user'=>$user
	 )
	);
	return $wpdb->insert_id;	
}


/*Overrides Warning */
function overrideUser($user,$session,$date){
	global $wpdb;
	$wpdb->show_errors();
	$date =date('Y-m-d G:i:s');
	$overrides_table_name = $wpdb->prefix."_override_events";
	$wpdb->insert($overrides_table_name,
	array(
		'override_date'=>$date,
		'user'=>$user,
		'session_id'=>$session
	 ));
}

/* Log out User */

function logoutUser($session){
	global $wpdb;
	$wpdb->show_errors();
	$date =date('Y-m-d G:i:s');
	$session_table_name = $wpdb->prefix."_session_events";
	$result = $wpdb->update( 
		$session_table_name,
		array('logout_date'=>$date),
		array('id'=>$session)
	);
	return true;
}


/**Updates the session with a status of primary/secondary nurse*/
function scanBarcode($nurse, $barcode,$session, $date){
	global $wpdb;
	$wpdb->show_errors();
	
	$session_table_name = $wpdb->prefix."_scan_events";
	$result = $wpdb->insert( 
		$session_table_name,
		array(
			'user'=>$nurse,
			'session'=>$session,
			'barcode_id'=>$barcode,
			'scan_date' =>$date	
		)
	);
	return true;
}



/**Updates the session with a status of primary/secondary nurse */

function updateNurse($session, $nurse){
	global $wpdb;
	$wpdb->show_errors();
	
	$session_table_name = $wpdb->prefix."_session_events";
	$result = $wpdb->update( 
		$session_table_name,
		array('primary_nurse'=>$nurse),
		array('id'=>$session)
	);

	return true;
}

//register region event
function addRegionEvent( $date, $state, $user, $beacon_id){
	global $wpdb;
	$region_table_name = $wpdb->prefix."_region_events";
	$date = date('Y-m-d H:i:s',$date);
	$wpdb->insert($region_table_name,
		array(
		'state'=>$state,
		'event_date'=>$date,
		'user' =>$user,
		'beacon_id' =>$beacon_id	
		)	
	);
}

//registers proximity event
function addProximityEvent( $date,$proximity, $user, $beacon_id){
	global $wpdb;
	$wpdb->show_errors();
	$date = date('Y-m-d H:i:s',$date);
	
	$proximity_table_name = $wpdb->prefix."_proximity_events";
		$wpdb->insert($proximity_table_name,
		array(
		'proximity'=>$proximity,
		'event_date'=>$date,
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