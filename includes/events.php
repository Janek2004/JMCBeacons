<?php
date_default_timezone_set('America/Chicago');
/**Log's in user and returns id of the row that can be used as a session id */
function getConnection()
{
	$servername = "localhost";
	$username = "wordpressuser99";
	$password = "hO4m_w8Or-w7";
	$dbname = "wordpress99";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	return $conn;
}

function loginUser($user){
	
	//check unfinished sessions
	$session_id = getLastSessionForUser($user);
	if(!is_null($session_id))	logoutUser($session_id);
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

/**

$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();*/


//get last session
function getLastSessionForUser($user){
//	global $wpdb;
//	$wpdb->show_errors();
	$session_table_name = "wp__session_events";
//	$result =  $wpdb->get_results( "SELECT * FROM $session_table_name WHERE user = $user AND logout_date IS NULL");
	
	$connection = getConnection();
	$results = $connection->query( "SELECT * FROM $session_table_name WHERE user = $user AND logout_date IS NULL");
	$sessions = $results->fetch_array(MYSQLI_ASSOC);

	if($sessions){
		$last = end($sessions);
		if(is_array($last)){
				if(!is_null($last)) return $last['id'];
		}
		else{
			return $sessions["id"];
		}
	}
}

/*
<?php
//Open a new connection to the MySQL server
$mysqli = new mysqli('host','username','password','database_name');

//Output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

//MySqli Select Query
$results = $mysqli->query("SELECT id, product_code, product_desc, price FROM products");

print '<table border="1">';
while($row = $results->fetch_assoc()) {
    print '<tr>';
    print '<td>'.$row["id"].'</td>';
    print '<td>'.$row["product_code"].'</td>';
    print '<td>'.$row["product_name"].'</td>';
    print '<td>'.$row["product_desc"].'</td>';
    print '<td>'.$row["price"].'</td>';
    print '</tr>';
}  
print '</table>';

// Frees the memory associated with a result
$results->free();

// close connection 
$mysqli->close();
?>
*/


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
function showWarning($nurse, $session, $date){
	global $wpdb;
	$wpdb->show_errors();
	
	$table_name = $wpdb->prefix."_warning_events";
	$result = $wpdb->insert( 
		$table_name,
		array(
			'user'=>$nurse,
			'session'=>$session,
			'warning_date' =>$date	
		)
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
function addRegionEvent( $date, $state, $user, $beacon_id, $app_state){
	global $wpdb;
	$region_table_name = $wpdb->prefix."_region_events";
	$date = date('Y-m-d H:i:s',$date);
	$wpdb->insert($region_table_name,
		array(
		'state'=>$state,
		'event_date'=>$date,
		'user' =>$user,
		'beacon_id' =>$beacon_id,
		'application_state'=>$app_state
		)	
	);
}

//registers proximity event
function addProximityEvent( $date,$proximity, $user, $beacon_id, $app_state){
	global $wpdb;
	$wpdb->show_errors();
	$date = date('Y-m-d H:i:s',$date);
	
	$proximity_table_name = $wpdb->prefix."_proximity_events";
		$wpdb->insert($proximity_table_name,
		array(
		'proximity'=>$proximity,
		'event_date'=>$date,
		'user' =>$user,
		'beacon_id' =>$beacon_id,
		'application_state'=>$app_state
		)	
	);
	

}

function getRegionEvent(){
	
}

function getProximityEvents(){
	
}






?>