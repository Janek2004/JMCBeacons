<?php 
global $post;
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

require_once( dirname(__FILE__) . '/events.php' );
require_once( dirname(__FILE__) . '/missions.php' );


/*
	Supported Actions:
	student authentication 
		login (userid, pass)
		logout(userid)
		update_nurse updating nurse status
	
	Scan
		patient barcode
		nurse barcode
	
	Overwriting
		nurse id
	
	Displaying Warnings
	
	Region monitoring
		patient approached/left  //done
		sink	approached/left  //done
	
	Proximity 
		patient  //done
		sink 	 //done
		
*/

//

//header('Content-Type: application/json');
	//get all missions
if(!isset($_REQUEST['action'])) die("Nope");
			
			if($_REQUEST['action']==="login"){
				
				$user_id = $_REQUEST['user'];
				$pass = $_REQUEST['password'];
			
				$creds = array();
				$creds['user_login'] = $user_id;
				$creds['user_password'] = $pass;
				
				$user = wp_signon( $creds, false );
				
				if ( is_wp_error($user) )
				{				
					$errors = array_keys($user->errors);
					$e_m = "";
					foreach($errors as $error){
						$e_m = $e_m. " ".$error;
					}
					echo '{"error_message":'.'"'.$e_m.'"}';
				}
				else{
					$session = loginUser($user->ID);
					if($session!=0){/*
					typedef NS_ENUM(NSUInteger, kWarningStatus) {
						kNoWarnings,
						kPositiveWarnings,
						kNegativeWarnings,
						kAllWarnings
					};
					*/
						echo '{"userid":'.$user->ID. ', "session":'.$session.',"warning_state":1}';
					}
				}
			}
			
			if($_REQUEST['action']==="warning")
			{	
				$nurse = $_REQUEST['nurse'];
				$session = $_REQUEST['session'];
				$date =date('Y-m-d G:i:s');
			    showWarning($nurse, $session,$date); 
			
			}
			
			if($_REQUEST['action']==="updatenurse"){
				$nurse = $_REQUEST['nurse'];
				$session = $_REQUEST['session'];
				$result = updateNurse($session, $nurse);
				
				echo '{"status":1}';
				
			}
			
			if($_REQUEST['action']==="logout"){
				$session = $_REQUEST['session'];	
				logoutUser($session);
			}
			
			if($_REQUEST['action']==="override"){
				$session = $_REQUEST['session'];
				$date =date('Y-m-d G:i:s');	
				$nurse = $_REQUEST['nurse'];				
				
				overrideUser($nurse,$session,$date);
			}
			
			if($_REQUEST['action']==="scan"){
				$nurse = $_REQUEST['nurse'];
				$barcode = $_REQUEST['barcode'];
				$session = $_REQUEST['session'];
				$date =date('Y-m-d G:i:s');
				scanBarcode($nurse,$barcode,$session,$date);
			}
			
			
			if($_REQUEST['action']==="getData"){
	
				$beacon_id = $_REQUEST['beacon_uuid'];	//get uuid
				$beacon_major =$_REQUEST['beacon_major']; //get major
				$beacon_minor =$_REQUEST['beacon_minor']; //get minor
				$array = iBeacon::getBeaconID($beacon_id, $beacon_major, $beacon_minor);
		
			if(count($array)>0){
		
				//take always first one
				$ibeacon = $array[0];
			
				$station_array = Station::getStationsForBeacon($ibeacon);
			
				
			foreach($station_array as $station){
						echo Station::getJSONRepresentation($station);	
				
				}
			}
		 }//based on the id information display message and information		
		 

		 if($_REQUEST['action']==="getMissions"){
					//get missions
					$missions = Mission::getMissions();
			?>
		 {"missions":[
  			<?php
					$record_count = $missions->post_count;
					$counter = 0;
					
					foreach ($missions->posts as $mission){
										$stations = getStationsForMission($mission->ID);
											echo '{"name":"'.$mission->post_title.'",';
											echo '"id":"'.$mission->ID.'",';
											echo '"stations":';
											echo $stations;
											echo '}';	
											if($counter!=$record_count-1){						
												echo ",";
											}
											$counter++;	
								}
	
					?>]}
     
 <?php
			}
			if($_REQUEST['action']==="saveProximity"){
						$beacon_id = $_REQUEST['beacon_uuid'];	//get uuid
						$beacon_major =$_REQUEST['beacon_major']; //get major
						$beacon_minor =$_REQUEST['beacon_minor']; //get minor
						$user =$_REQUEST['user'];
						$date =$_REQUEST['event_date'];
						$proximity =$_REQUEST['proximity'];
						$foreground = $_REQUEST['foreground'];
						$array = iBeacon::getBeaconID($beacon_id, $beacon_major, $beacon_minor);
						
						if(count($array)>0){
								$ibeacon = $array[0];
								addProximityEvent($date,$proximity,$user, $ibeacon->ID, $foreground);	
							}
						else{
							echo "no beacon";
						}
			}
			
			
			
			if($_REQUEST['action']==="saveRegion"){
						$beacon_id = $_REQUEST['beacon_uuid'];	//get uuid
						$beacon_major =$_REQUEST['beacon_major']; //get major
						$beacon_minor =$_REQUEST['beacon_minor']; //get minor
						$user =$_REQUEST['user'];
						$date =$_REQUEST['event_date'];
						$state =$_REQUEST['state'];
						$foreground = $_REQUEST['foreground'];
						$array = iBeacon::getBeaconID($beacon_id, $beacon_major, $beacon_minor);
	
						if(count($array)>0){
								$ibeacon = $array[0];
								addRegionEvent($date,$state,$user, $ibeacon->ID, $foreground);												
						}
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
				'meta_value'=>$id,
				'post_status' => 'publish'
				
				);		
			}
	

		$stations = new WP_Query( $args );	
	
		$json_string = '[';
		$record_count = $stations->post_count;
		$counter = 0;
		
		if($stations ->have_posts()):
			foreach($stations->posts as $station):
					$json_string= $json_string.Station::getJSONRepresentation($station);
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
			'post_status' => 'publish'
			);
			$query = new WP_Query( $args );
			return $query;
	}

?>