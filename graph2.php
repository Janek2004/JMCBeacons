<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
date_default_timezone_set('America/Chicago');

$ROW_HEIGHT = 105;


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>iBeacon Visualization</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/beaconevent.js"></script>
<link rel="stylesheet" type="text/css" href="js/jquery.datetimepicker.css"/ >
<script src="js/jquery.datetimepicker.js"></script>
<style type="text/css">
ol.simple-list {
    list-style-type: none;
    list-style-type: decimal !ie; /*IE 7- hack*/
     
    margin: 0;
    margin-left: 3em;
    padding: 0;
     
    counter-reset: li-counter;
}
ol.simple-list > li{
    position: relative;
    margin-bottom: 20px;
    padding-left: 0.5em;
    min-height: 3em;
    border-left: 2px solid #CCCCCC;
}

p{
  font: 200 12px/1.5 Georgia, Times New Roman, serif;
   display:block;
   background-color:#E8F4FF;
   padding:0.2em;   
   position: relative;
   
   padding-left: 0.2em;
    min-height: 1.5em;
  }

p.login{
    padding:0.5em;   
	position: relative;
    margin-bottom: 20px;
    padding-left: 0.5em;
    min-height: 3em;
    border-left: 2px solid #CCCCCC;
	border-top: 2px solid #CCCCCC;
	display:block;
	background-color:#E8F4FF;
	
}
p.logout{
    position: relative;
    margin-bottom: 20px;
    padding:0.5em;
	padding-left: 0.5em;
	
    min-height: 3em;
    border-left: 2px solid #CCCCCC;
	border-bottom: 2px solid #CCCCCC;
	display:block;
	background-color:#E8F4FF;
	
}

ol.simple-list > li:before {
    position: absolute;
    top: 0;
    left: -1em;
    width: 0.8em;
     
    font-size: 3em;
    line-height: 1;
    font-weight: bold;
    text-align: right;
    color: #464646;
 
    content: counter(li-counter);
    counter-increment: li-counter;
}

</style>
</head>
<body>

<?php

// Create connection
$con=mysqli_connect("localhost","phpapp_user","HVzcdVwnHyXwTpcF","wordpress99");

// Check connection
if (mysqli_connect_errno()) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }
if(!isset($_REQUEST["start"])&&!isset($_REQUEST["stop"])){
  $datetime = new DateTime('2000-01-01');
  $timeline_start=$datetime->format('Y-m-d');
  $datetime = new DateTime('tomorrow');
  $timeline_stop = $datetime->format('Y-m-d');
}
$clear = false;
if(isset($_REQUEST["ccc"])){
	$clear = true;
}

if(isset($_REQUEST["start"])&&isset($_REQUEST["stop"])){
	 $timestamp1= $_REQUEST["start"];
	 $dt1 = new DateTime($_REQUEST["start"]);
	 $timeline_start = $dt1->format('Y-m-d H:i:s');;
	
	 $dt2 = new DateTime($_REQUEST["stop"]);
	 $timeline_stop = $dt2->format('Y-m-d H:i:s');;
		
	// print_r($_REQUEST);
		
	
	 $user = $_REQUEST['user'];
}

//get list of the users
	function getUsers(){
		global $con;
		$users_result;
		try{
			$users_result = mysqli_query($con,"SELECT * FROM `wp_users`");
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
		

		if(mysqli_num_rows($users_result)){
			$arrayofusers = mysqli_fetch_all($users_result);	
			return $arrayofusers;
		}
		return null;//new Array();
				
	}
	
	
	
	function clearTables(){
		global $con;
		try{
		 mysqli_multi_query($con,"DELETE FROM `wp__region_events` WHERE 1; DELETE FROM `wp__override_events` WHERE 1; DELETE FROM `wp__proximity_events` WHERE 1; DELETE FROM `wp__scan_events` WHERE 1;DELETE FROM `wp__session_events` WHERE 1");
		}
		catch(Exception $e){
			echo $e->getMessage();
			die("Didnt work");
		}
		die("tabula rasa");
	}
	
	if($clear)	clearTables();
	
	
	//get proximity events
	function getProximityEvents($timeline_start,$user){
		global $con;
		$result;
		if($timeline_start){
		try{
			$result = mysqli_query($con,"SELECT * FROM wp__proximity_events WHERE event_date > '$timeline_start' AND user ='$user' ORDER BY event_date ");
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
		}	
	  	$proximityArray = mysqli_fetch_all($result);				
		return $proximityArray;
	}

	function getArray($con, $query){
		$result;
		try{
			$result = mysqli_query($con,$query);
		}	
		catch(Exception $e){
			echo $e->getMessage();
		}
		$array = mysqli_fetch_all($result, MYSQLI_BOTH);		
		return $array;						
	}
	
	
	$arrayofusers = getArray($con,"SELECT * FROM `wp_users`"); ///getUsers();
	$arrayofibeacons =  getArray($con,"SELECT `ID`,`post_title` FROM `wp_posts` WHERE `post_type` = 'ibeacon'");
	
	$proximityArray =getArray($con,"SELECT * FROM wp__proximity_events WHERE event_date > '$timeline_start' AND event_date <'$timeline_stop' AND user ='$user' ORDER BY event_date ");//  getProximityEvents($timeline_start, $user);
	$regionsArray = getArray($con,"SELECT * FROM `wp__region_events`  WHERE event_date > '$timeline_start'  AND user ='$user' ORDER BY event_date");
	$sessionArray = getArray($con,"SELECT * FROM `wp__session_events`  WHERE login_date > '$timeline_start' AND user ='$user'");
	$scansArray =  getArray($con,"SELECT * FROM `wp__scan_events` WHERE user = $user AND scan_date >  '$timeline_start'");
	$overrideArray =  getArray($con,"SELECT * FROM `wp__override_events` WHERE user = $user AND override_date >  '$timeline_start'");
	

	$all_events = array_merge($regionsArray,$sessionArray,$overrideArray,$scansArray);	

	
	function getEventDate($arr1){
		$date1;

		if(array_key_exists("override_date", $arr1))
		{
			$date1 = $arr1["override_date"];
		}
		
		if(array_key_exists("scan_date", $arr1))
		{
			$date1 = $arr1["scan_date"];
		}
		
		if(array_key_exists("login_date", $arr1))
		{
			$date1 = $arr1["login_date"];
		}
		if(array_key_exists("event_date", $arr1))
		{
			$date1 = $arr1["event_date"];
		}
				
		return $date1;	
	}
	
	
	//sort by date
	function compare($arr1,  $arr2){
		$date1;
		$date2;
		if(array_key_exists("override_date", $arr1))
		{
			$date1 = $arr1["override_date"];
		}
		if(array_key_exists("override_date", $arr2))
		{
			$date2 = $arr2["override_date"];
		}
		
		if(array_key_exists("scan_date", $arr1))
		{
			$date1 = $arr1["scan_date"];
		}
		if(array_key_exists("scan_date", $arr2))
		{
			$date2 = $arr2["scan_date"];
		}
		
		if(array_key_exists("login_date", $arr1))
		{
			$date1 = $arr1["login_date"];
		}
		if(array_key_exists("login_date", $arr2))
		{
			$date2 = $arr2["login_date"];
		}
		if(array_key_exists("event_date", $arr1))
		{
			$date1 = $arr1["event_date"];
		}
		if(array_key_exists("event_date", $arr2))
		{
			$date2 = $arr2["event_date"];
		}
		
		if(!isset($date2)){
			echo "<h1>Error</h1>";
			print_r($arr2);
			return;
		}
				
		if($date1==$date2) return 0;

		return ($date1<$date2) ? -1:1;
	}
		usort($all_events, "compare");

/** Displays Events */
		function printEvents($events,$arrayofibeacons)
		{
				
			$last_logout=null;
			
			for($i=0; $i<count($events)-1;$i++){
				
				$event= $events[$i];
				$nextEvent=$events[$i+1];		
				$date1 = getEventDate($event);
				$date2 = getEventDate($nextEvent);
		
				if(array_key_exists("login_date", $event)){
					
						$primary = $event["primary_nurse"];
						$primaryText= ($primary===1)?": as primary nurse":":"; 
					?>
			
						<p class="login"><?php echo $event["login_date"]; ?> Logged in<?php echo $primaryText; ?></p>
						
					<?php
					if(!empty($event["logout_date"]))
						{  
							$last_logout = $event["logout_date"];?>
						
						<?php
						}
				}
		
		
				if(array_key_exists("scan_date", $event)){
					?>
						<p><?php echo $event["scan_date"]; ?>Scanned Barcode: <?php echo $event["barcode_id"]; ?></p>
					<?php
				}
				
				if(array_key_exists("event_date", $event)){
					$state = $event["state"];
					$beacon = $event["beacon_id"];
					$title = getIbeaconTitle($beacon, $arrayofibeacons);
					$state_text = "";	
					
					switch  ($state){
					/*
						case 0:{
							$state_text = "unknown";
							return;
							break;
						}
						*/
						case 1:{
							$state_text = " is inside ";
								break;
						}						
						case 2:{
							$state_text = " is outside ";
							$state_text = "";
							break;
							}												
					}
					
						if(!empty($state_text)){
					?>
						<p><?php echo $event["event_date"];?> Nurse  <?php echo $state_text." region: ".$title; ?></p>
					<?php
					}
				}

					if(isset( $last_logout)){
					
					if(($last_logout>=$date1)&& ($last_logout<=$date2))
					{ 
					
						?>
				
						<p class="logout"><?php echo $last_logout; ?> Logged out: </p>
						<?php
					}
				}
				
					if(array_key_exists("override_date", $event)){
					?>
						<p> <?php echo $event["override_date"]; ?> Manual Override:</p>
					<?php
				}
				
					if($i == count($events)-2){
					
					if(array_key_exists("scan_date", $nextEvent)){
					?>
						<p><?php echo $nextEvent["scan_date"]; ?>Scanned Barcode: <?php echo $nextEvent["barcode_id"]; ?></p>
					<?php
				    }
					if(array_key_exists("override_date", $nextEvent)){
					?>
						<p> <?php echo $nextEvent["override_date"]; ?> Manual Override:</p>
					<?php
					}
					
					if(array_key_exists("logout_date",$nextEvent))
					{
						if(!is_null($nextEvent["logout_date"])){
						
						}
					}
					if(array_key_exists("event_date", $nextEvent)){
					$state = $nextEvent["state"];
					$beacon = $nextEvent["beacon_id"];
					$title = getIbeaconTitle($beacon, $arrayofibeacons);
					$state_text = "";	
					
					//$state = 1;
					
					switch  ($state){
					/*
						case 0:{
							$state_text = "unknown";
							return;
							break;
						}
						*/
						case 1:{
							$state_text = " is inside ";
								break;
						}						
						case 2:{
							$state_text = " is outside ";
							$state_text = "";
					
						break;
							}												
					}
					
						if(!empty($state_text)){
					?>
						<p><?php echo $nextEvent["event_date"];?> Nurse  <?php echo $state_text." region: ".$title; ?></p>
					<?php
					}
				}
					if(!is_null($last_logout)){
						if($last_logout > $date2){
					?>
					
						<p class="logout"><?php echo $last_logout; ?> Logged out: </p>
						<?php
					}
				  }
				}	
				
			}
		}
		
		function getIbeaconTitle($bid,$arrayofibeacons){
			for($i=0;$i<count($arrayofibeacons);$i++)
		{
			$id= $arrayofibeacons[$i][0];	
			$title= $arrayofibeacons[$i][1];
			if($id == $bid){
				return $title;
			}
		}
			return "Beacon";
		}
		
	?>
	
	<script type="text/javascript">
		var ibeacons = [];
	<?php
		for($i=0;$i<count($arrayofibeacons);$i++)
		{
			$id= $arrayofibeacons[$i][0];	
			$title= $arrayofibeacons[$i][1];
		      	?>		
				ibeacons.push({"level":<?php echo $i;?>,"ID":<?php echo $id;?>,"title":"<?php echo $title;?>"});
			<?php
		}
	?>

	
	
	
	</script>
	
	<?php
	mysqli_close($con);
	
	$totalHeight = count($arrayofibeacons) * $ROW_HEIGHT + 50;
	$totalWidth = 900;
	?>
	
<script type="text/javascript">
	var events = [];
	var region_events =[];
	var c; 
	var ctx; 
	var bottomLine = <?php echo count($arrayofibeacons) * $ROW_HEIGHT;?>;	
	var graph_width = <?php echo $totalWidth; ?>;	
	
	<?php

//getting all proximity events	to javascript array
for($i=0;$i<count($proximityArray);$i++){
	 $duration = 0;
	 $start = 0;
	 $proximity = $proximityArray[$i][1];
	 
	 if($i<	count($proximityArray)-1)
	 {
		$date_start = $proximityArray[$i][2];
		$date_end = $proximityArray[$i+1][2]; 
	//	$duration =  timeDifference($date_start,$date_end);
		$start =     timeDifference($timeline_start,$date_start);
		$type = $proximityArray[$i][4];
		
		$duration = 5/60;
		
		?> 
		//duration,proximity,start,type	 
		events.push(new BeaconEvent(<?php echo $duration.",".$proximity.",".$start.",".$type; ?>));
<?php
	 }
}

for($i=0;$i<count($regionsArray );$i++){

	 $start = 0;
	 $state = $regionsArray [$i][1];
	 $duration = 5/60;
		
	 if($i<	count($regionsArray)-1)
	 {
		$date_start = $regionsArray[$i][2];
		$type = $regionsArray[$i][4];
		$start =     timeDifference($timeline_start,$date_start);		
		?> 
	   //duration, state,start,id
		region_events.push(new BeaconRegionEvent(<?php echo $duration.",".$state.",".$start.",".$type; ?>));
<?php
	 }
}


		
?>
</script>
   <?php
//determine number of minutes
 function timeDifference($date_start, $date_end){
	$to_time = strtotime($date_end);
	$from_time = strtotime($date_start);
	
	$number_of_minutes =  round(abs($to_time - $from_time) / 60,2);
	return $number_of_minutes;
 }
 
$number_of_minutes = timeDifference($timeline_start, $timeline_stop);

?>

			
<script type="text/javascript">
	
		function drawSegments(ctx){
			ctx.moveTo(50,bottomLine);
			for(var i =0; i<ibeacons.length;i++){
				ctx.moveTo(50,bottomLine - i *100);
				ctx.lineTo(750,bottomLine - i*100);
				ctx.stroke();
				ctx.fillText(ibeacons[i].title, 50, bottomLine - i*100-85);
				
				/*
				ctx.save();
				ctx.rotate(Math.PI/2.0);
				ctx.textAlign="center";
				cotxfillText("Your Label Here", 100, 0);
				ctx.restore();
				*/
			}
		}
		
			
		function drawScale(ctx){
				 	
				ctx.moveTo(50,bottomLine+20);
				ctx.lineTo(750,bottomLine+20);
				ctx.moveTo(50,bottomLine+20);
				ctx.lineTo(50,0);
				ctx.moveTo(750,bottomLine+20);
				ctx.lineTo(750,0);
				
				for(var i=0; i< 700;i=i+20)
				{
					ctx.moveTo(i+50,bottomLine+20-10);
					ctx.lineTo(i+50,bottomLine+20);
					
				}
				ctx.stroke();
				ctx.fillText("<?php echo($timeline_start); ?>" ,50, bottomLine+30 );
				ctx.fillText("<?php echo($timeline_stop); ?>" ,700, bottomLine+30);	
		}
		
		function getLevel(beaconID){
			for(var i=0; i< ibeacons.length;i++)
			{
				var ibeacon = ibeacons[i];
				if(ibeacon.ID == beaconID){
					return ibeacon.level;
				}
			}
		}
		
		function drawEvents(ctx, minutes,events){			
				ctx.font = "12px Arial";
				ctx.fillText("Timeline (<?php echo $number_of_minutes;?> minutes)",graph_width/2.0-20, bottomLine+30);
				
				var one_minute_width = 750/minutes;
			
				ctx.lineWidth = 0.4;
				var duration = 0;
				for(var i=0;i<events.length;i++){
							var b = events[i];
							var x = 50 + b.start * one_minute_width;
							var width = b.duration * one_minute_width;
							var proximity = b.proximity;
							
							//depending on beacon change the bottom line
							var level = getLevel(b.beaconId);
							if(x<750) b.draw(x, bottomLine -level*100, width,proximity);		
							
					}
				}
				
		function drawRegionEvents(ctx, minutes,regionEvents){							
				var one_minute_width = 750/minutes;			
				ctx.lineWidth = 0.4;
				var duration = 0;
				//console.log(events);
				
				for(var i=0;i<regionEvents.length;i++){
							var b = regionEvents[i];
							//console.log(i+ " " + b);
							//console.log(b);
							var x = 50 + b.start * one_minute_width;
							var width = b.duration * one_minute_width;					
							//depending on beacon change the bottom line
							var level = getLevel(b.beaconId);
		
							try{
								if(x<750) {
									//console.log(b.state);
									if(b.state === 1)	b.draw(x, bottomLine -level*100, width);
								}									
							}
							catch(exception){
								console.log(exception);
							}
							finally{
							
							}

					}
				}
				
				

</script>


	<div style="margin:auto; width:80%; text-align:center">
  	<h1>iBeacon Data</h1>
  	<div style="text-align:left">
		
    <form action="graph2.php">
		Data for: <select id="user" name="user">
		<option value="Select User">Select an User</option>

		<?php
		for($i=0;$i<count($arrayofusers);$i++){
			
			 ?> 
			 <option value="<?php echo $arrayofusers[$i][0]; ?>" <?php if($arrayofusers[$i][0]==$_REQUEST["user"]){echo " selected";}?> ><?php echo $arrayofusers[$i][1];?></option>
			<?php
			}	
			?>
		</select>
		<p>Select Start Date:  <input class="datetime" name="start" value="<?php echo $timeline_start; ?>"></p>
		<p>Select Stop Date:   <input class="datetime"  name="stop" value="<?php echo $timeline_stop; ?>"></p>

	<input type="submit">
	</form>
		
	
  	<canvas id="canvas" width="<?php echo $totalWidth;?>" height="<?php echo $totalHeight;?>" style="border:1px solid #000000; margin:auto" ></canvas>
	<h4>Events </h4>
	<?php 
		printEvents($all_events,$arrayofibeacons)
		
	?>

	</div>

	
	
	
	
  <script type="text/javascript">


	$(document).ready(function(){
		//canvas
		 c =  document.getElementById("canvas");
		 ctx = c.getContext("2d");
		
		drawScale(ctx);
		drawEvents(ctx,<?php echo $number_of_minutes;?>, events);
		drawRegionEvents(ctx,<?php echo $number_of_minutes;?>, region_events);		
		
		drawLegend(graph_width,ctx);
		drawSegments(ctx);
		$(".datetime").css("color:red");
	//	jQuery(".datetime").datetimepicker();
	//	jQuery('#datetimepicker').datetimepicker();
		
		console.log("ready");
		
	});
	</script>
	


</body>
</html>