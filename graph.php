<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>iBeacon Visualization</title>
</head>

<body>
<?php
// Create connection
$con=mysqli_connect("143.88.2.98","phpapp_user","HVzcdVwnHyXwTpcF","wordpress99");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>


	<div style="margin:auto; width:80%; text-align:center">
  	<h1>iBeacon Station Data Visualisation</h1>
  	<div style="text-align:left">
    <p>Janek</p>
    <p>Beacon id: 1</p>
		</div>

  	<canvas id="canvas" width="800px" height="400px" style="border:1px solid #000000; margin:auto" > </canvas>
	</div>
  
	<script type="text/javascript">
			//steps
			//get total number of time to set on scale 
			//let's say it's 60					
			//once we have total number of time we know how 'wide' a minute is  
			 
			  function BeaconEvent(duration, proximity,start){
						this.duration=duration;
						this.proximity=proximity;
						this.start=start;
						this.style="";
						this.height = 0;
							switch(proximity){ //unknown, immediate, nearby, far
					case 0:{
						 this.height = 50; 
						 this.style="red";
					 	 break;
						}
  				case 1:{
						 this.height = 200; 
						 this.style="blue";
					 	 break;
						}
					case 2:{
						 this.height = 150; 
						 this.style="green";
					 	 break;
						}					
					case 3:{
						 this.height = 100; 
						 this.style="orange";
					 	 break;
						}
					}	
		
				}	
		
				BeaconEvent.prototype.draw = function(x,bottom,duration,proximity){
						
					ctx.fillStyle = this.style;
					ctx.fillRect(x,bottom-this.height,duration, this.height);	
					ctx.strokeRect(x,bottom-this.height,duration, this.height);
						
				}
				
			var b1 = new BeaconEvent(3,1,1);
			console.log(b1.duration);		
			console.log(b1.proximity);
			console.log(b1.start);		
			
			var b2 = new BeaconEvent(3,2,4);		 
			var b3 = new BeaconEvent(4,3,9);
			var b4 = new BeaconEvent(4,0,17);		
			var b5 = new BeaconEvent(8,1,21);
			 
			var events = [b1,b2,b3,b4,b5];				 
			 
			var c = document.getElementById("canvas");
			var ctx = c.getContext("2d");
			
		  var bottomLine = 350;	
			var graph_width = 700;	
			
	  function drawLegend()
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
			
		function drawScale(minutes,events){
		 	
				var one_minute_width = graph_width/minutes;
				
				ctx.moveTo(50,350);
				ctx.lineTo(750,350);
				ctx.moveTo(50,350);
				ctx.lineTo(50,50);
				ctx.stroke();
				
				
				ctx.font = "12px Arial";
				ctx.fillText("Timeline (minutes)",graph_width/2.0-20, bottomLine+20);
				
			
			
				var duration = 0;
				for(var i=0;i<events.length;i++){
							var b = events[i];
							var x = 50 + b.start * one_minute_width;
							var width = b.duration * one_minute_width;
							var proximity = b.proximity;
							b.draw(x, bottomLine, width,proximity);		
							
					}
				}
			
			drawScale(60, events);
			drawLegend();
			
	</script>

</body>
</html>