<?php
error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

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

<script type="text/javascript">
	var events = [];
	var c; 
	var ctx; 
	var bottomLine = 350;	
	var graph_width = 700;	
</script>

<?php

// Create connection
$con=mysqli_connect("localhost","phpapp_user","HVzcdVwnHyXwTpcF","wordpress99");

// Check connection
if (mysqli_connect_errno()) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }
  $datetime = new DateTime('2000-01-01');
  $timeline_start=$datetime->format('Y-m-d');
  $datetime = new DateTime('tomorrow');
  $timeline_stop = $datetime->format('Y-m-d');

if(isset($_REQUEST["start"])&&isset($_REQUEST["stop"])){
	 $timeline_start = $_REQUEST["start"];
	 $timeline_stop = $_REQUEST["stop"];
}	

	$result;
	if($timeline_start){
	try{
		$result = mysqli_query($con,"SELECT * FROM wp__proximity_events WHERE event_date > '$timeline_start' ORDER BY event_date ");
	}
	catch(Exception $e){
		echo $e->getMessage();

		}
	}
	
  	$arrayofrows = mysqli_fetch_all($result);
	mysqli_close($con);
	?>
	<script type="text/javascript">
	<?php
	//$result->data_seek(0);
for($i=0;$i<mysqli_num_rows($result);$i++){
	 $duration = 0;
	 $start = 0;
	 $proximity = $arrayofrows[$i][1];
	 
	 if($i<	mysqli_num_rows($result)-1)
	 {
		$date_start = $arrayofrows[$i][2];
		$date_end = $arrayofrows[$i+1][2]; 
		$duration =  timeDifference($date_start,$date_end);
		$start = 	 timeDifference($timeline_start,$date_start);
		?> 
		//duration, proximity,start	 
		events.push(new BeaconEvent(<?php echo $duration.",".$proximity.",".$start; ?>));
<?php
	 }
}
		
?>
</script>
   <?php

 function timeDifference($date_start, $date_end){
	$to_time = strtotime($date_end);
	$from_time = strtotime($date_start);
	
	$number_of_minutes =  round(abs($to_time - $from_time) / 60,2);
	return $number_of_minutes;
 }
 
//determine number of minutes
if(mysqli_num_rows($result)>0){
	//print_r( $arrayofrows[0]);
//	$date_start =  $timeline_start;
//	$date_end = $arrayofrows[mysqli_num_rows($result)-1][2];		
}
$number_of_minutes = timeDifference($timeline_start, $timeline_stop);

?>

			
<script type="text/javascript">
	
	/** Shows Legend */	
	  function drawLegend(ctx)
		{	
				var b0 = new BeaconEvent(0,0,0);
				var b1 = new BeaconEvent(0,1,0);
				var b2 = new BeaconEvent(0,2,0);
				var b3 = new BeaconEvent(0,3,0);

				var height = 20;
				var width = 50;
				var y = 50;
				var x_offset = 100;
				ctx.fillStyle = b0.style;
				ctx.fillRect(graph_width- x_offset,y,width,height);
				
				ctx.fillStyle = "black";
				ctx.fillText("Unknown",graph_width- x_offset+width + 10, y+height/2.0);
				
				ctx.fillStyle = b1.style;
				ctx.fillRect(graph_width- x_offset,y+height,width,height);
				ctx.fillStyle = "black";
				ctx.fillText("Immediate",graph_width- x_offset+width + 10, y+height+height/2.0);
				
				
				ctx.fillStyle = b2.style;
				ctx.fillRect(graph_width- x_offset,y+2*height,width,height);
				ctx.fillStyle = "black";
				ctx.fillText("Nearby",graph_width- x_offset+width + 10, y+2*height+height/2.0);
				
				ctx.fillStyle = b3.style;
				ctx.fillRect(graph_width- x_offset,y+3*height,width,height);
				ctx.fillStyle = "black";
				ctx.fillText("Far",graph_width- x_offset+width + 10, y+3*height+height/2.0);
		
				ctx.strokeRect(graph_width- x_offset,y,width + 80,height *4);
				
		}
			
		function drawScale(ctx){
		 	
				ctx.moveTo(50,350);
				ctx.lineTo(750,350);
				ctx.moveTo(50,350);
				ctx.lineTo(50,50);
				
				
				for(var i=0; i< 700;i=i+20)
				{
					ctx.moveTo(i+50,340);
					ctx.lineTo(i+50,350);
					
				}
				ctx.stroke();
				ctx.fillText("<?php echo($timeline_start); ?>" ,50, 360 );
				ctx.fillText("<?php echo($timeline_stop); ?>" ,700, 360 );
				
		}
		function drawEvents(ctx, minutes,events){			
				ctx.font = "12px Arial";
				ctx.fillText("Timeline (<?php echo $number_of_minutes;?> minutes)",graph_width/2.0-20, bottomLine+20);
				
				
				var one_minute_width = graph_width/minutes;
			
				ctx.lineWidth = 0.4;
				var duration = 0;
				for(var i=0;i<events.length;i++){
							var b = events[i];
							var x = 50 + b.start * one_minute_width;
							var width = b.duration * one_minute_width;
							var proximity = b.proximity;
							b.draw(x, bottomLine, width,proximity);		
							
					}
				}

</script>


	<div style="margin:auto; width:80%; text-align:center">
  	<h1>iBeacon Station Data Visualisation</h1>
  	<div style="text-align:left">
    <form action="graph.php">
		<p>Select Start Date:  <input class="datetime" name="start" value="<?php echo $timeline_start; ?>"></p>
		<p>Select Stop Date:  <input class="datetime"  name="stop" value="<?php echo $timeline_stop; ?>"></p>

	<input type="submit">
	</form>
		<select> <option value="Beacon Id">Beacon Id: 1 </option> </select>  
	</div>

  	<canvas id="canvas" width="800" height="400" style="border:1px solid #000000; margin:auto" ></canvas>
	</div>
	

	
  <script type="text/javascript">


	$(document).ready(function(){
		//canvas
		 c =  document.getElementById("canvas");
		 ctx = c.getContext("2d");
		
		drawScale(ctx);
		drawEvents(ctx,<?php echo $number_of_minutes;?>, events);
		drawLegend(ctx);
		$(".datetime").css("color:red");
		jQuery(".datetime").datetimepicker();
		jQuery('#datetimepicker').datetimepicker();
		
		console.log("ready");
		
	})
	</script>
	


</body>
</html>