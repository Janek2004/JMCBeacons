<?php
error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
date_default_timezone_set('America/New_York');

$ROW_HEIGHT = 100;

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

</head>
<body>

<?php

// Create connection
//$con=mysqli_connect("atcwebapp.argo.uwf.edu","phpapp_user","HVzcdVwnHyXwTpcF","wordpress99");
//$con=mysqli_connect('http://djmobilesoftware.com','janek211_ibeacons','stany','janek211_ibeacons');
$con=mysqli_connect('ns2.oxen.arvixe.com','janek211_ibeacon','stany','janek211_ibeacons');


// Check connection
if (mysqli_connect_errno()) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }
if(!isset($_REQUEST["start"])&&!isset($_REQUEST["stop"])){
  $datetime = new DateTime('2000-01-01');
  $timeline_start=$datetime->format('Y-m-d');
  $datetime = new DateTime('tomorrow');
  $timeline_stop = $datetime->format('Y-m-d');
}

if(isset($_REQUEST["start"])&&isset($_REQUEST["stop"])){
	 $timeline_start = $_REQUEST["start"];
	 $timeline_stop = $_REQUEST["stop"];
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
	
		$arrayofusers = mysqli_fetch_all($users_result);		
		return $arrayofusers;		
	}
	
	//returns list of the beacons
	function getIbeacons(){
		global $con;		
		$ibeacons_result;
		try{
			$ibeacons_result = mysqli_query($con,"SELECT `ID`,`post_title` FROM `wp_posts` WHERE `post_type` = 'ibeacon'");
		}	
		catch(Exception $e){
			echo $e->getMessage();
		}
		$arrayofibeacons = mysqli_fetch_all($ibeacons_result);
		return $arrayofibeacons;				
	}
	
	
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
	
	//get list of the region events	
	function getRegionEvents($user){
		global $con;	
		$regions_result;
		try{
			$regions_result = mysqli_query($con,"SELECT * FROM `wp__region_events` WHERE user = $user");
		}	
		catch(Exception $e){
			echo $e->getMessage();
		}
	
		$regions = mysqli_fetch_all($regions_result);		
		return $regions;			
	}	

	//get list of the overrides
	function getOverrides(){
		
		
	}
	
	function getSessionEvents($user){
		global $con;	
		$regions_result;
		try{
			$regions_result = mysqli_query($con,"SELECT * FROM `wp__session_events` WHERE user = $user");
		}	
		catch(Exception $e){
			echo $e->getMessage();
		}
	
		$regions = mysqli_fetch_all($regions_result);		
		return $regions;				
	}
	
	
	function getArray($con, $query){
		$result;
		try{
			$result = mysqli_query($con,$query);
		}	
		catch(Exception $e){
			echo $e->getMessage();
		}
		$array = mysqli_fetch_all($result,MYSQLI_ASSOC);		
		return $array;						
	}
	
	
	$arrayofusers = getUsers();
	$arrayofibeacons = getIbeacons();
	$proximityArray = getProximityEvents($timeline_start, $user);
	$regionsArray = getRegionEvents($user);
	$sessionArray = getArray($con,"SELECT * FROM `wp__session_events` WHERE user = $user");
	$scansArray =  getArray($con,"SELECT * FROM `wp__scans_events` WHERE user = $user");
	$overrideArray =  getArray($con,"SELECT * FROM `wp__override_events` WHERE user = $user");
	
	/*
		print_r($overrideArray);
		print_r($scansArray);
		print_r($sessionArray);
	*/
	
	
	$all_events = array_merge($regionsArray,$sessionArray,$overrideArray);
	
	//sort by date
	function compare($arr1,  $arr2){
	//array_key_exists		
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
	console.log("Level is: "+ ibeacons[2].title);
	</script>
	
	<?php
	mysqli_close($con);
	
	$totalHeight = count($arrayofibeacons) * $ROW_HEIGHT + 50;
	$totalWidth = 800;
	?>
	
<script type="text/javascript">
	var events = [];
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
		$duration =  timeDifference($date_start,$date_end);
		$start =     timeDifference($timeline_start,$date_start);
		$type = $proximityArray[$i][4];
		
		?> 
		//duration,proximity,start,type	 
		events.push(new BeaconEvent(<?php echo $duration.",".$proximity.",".$start.",".$type; ?>));
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
				ctx.lineTo(50,50);
				
				
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
				
				var one_minute_width = graph_width/minutes;
			
				ctx.lineWidth = 0.4;
				var duration = 0;
				for(var i=0;i<events.length;i++){
							var b = events[i];
							var x = 50 + b.start * one_minute_width;
							var width = b.duration * one_minute_width;
							var proximity = b.proximity;
							
							//depending on beacon change the bottom line
							var level = getLevel(b.beaconId);
							b.draw(x, bottomLine -level*100, width,proximity);		
							
					}
				}

</script>


	<div style="margin:auto; width:80%; text-align:center">
  	<h1>iBeacon Station Data Visualisation</h1>
  	<div style="text-align:left">
    <form action="graph.php">
		<p>Select Start Date:  <input class="datetime" name="start" value="<?php echo $timeline_start; ?>"></p>
		<p>Select Stop Date:   <input class="datetime"  name="stop" value="<?php echo $timeline_stop; ?>"></p>

	<input type="submit">
	</form>
		<select> <option value="Beacon Id">Beacon Id: 1 </option> </select>  
		//$arrayofusers = mysqli_fetch_all($users_result);
		<select id="user" name="userSelect">
		<option value="Select User">Select an User</option>

		<?php
		for($i=0;$i<mysqli_num_rows($users_result);$i++){
			
			 ?> 
			 <option value="<?php echo $arrayofusers[$i][0]; ?>"><?php echo $arrayofusers[$i][1]; ?></option>
			<?php
			}	
			?>
		</select>	
	
  	<canvas id="canvas" width="<?php echo $totalWidth;?>" height="<?php echo $totalHeight;?>" style="border:1px solid #000000; margin:auto" ></canvas>
	</div>
	

	
  <script type="text/javascript">


	$(document).ready(function(){
		//canvas
		 c =  document.getElementById("canvas");
		 ctx = c.getContext("2d");
		
		drawScale(ctx);
		drawEvents(ctx,<?php echo $number_of_minutes;?>, events);
		drawLegend(ctx);
		drawSegments(ctx);
		$(".datetime").css("color:red");
		jQuery(".datetime").datetimepicker();
		jQuery('#datetimepicker').datetimepicker();
		
		console.log("ready");
		
	})
	</script>
	


</body>
</html>