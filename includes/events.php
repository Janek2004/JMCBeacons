<?php

/**Log's in user and returns id of the row that can be used as a session id */
function loginUser($user){
	global $wpdb;
	//$wpdb->show_errors();
	$session_table_name = $wpdb->prefix."_session_events";
	$date =time();
	$wpdb->insert($session_table_name,
	array(
		'login_date'=>$date,
		'user'=>$user
	 )
	);
	return $wpdb->insert_id;	
}

/**Updates the session with a status of primary/secondary nurse*/
function updateNurse($session, $nurse){
	global $wpdb;
	$session_table_name = $wpdb->prefix."_session_events";
	$result = $wpdb->update( 
		$session_table_name,
		array('nurse'=>nurse),
		array('id'=>$session)
	);
	
	return  (false != $result);
}

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
	
	//echo $date; 
	//die();
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