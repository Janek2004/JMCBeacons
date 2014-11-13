/** Shows Legend */
function drawLegend(graph_width, ctx)
{     var b0 = new BeaconEvent(0,0,0);
      var b1 = new BeaconEvent(0,1,0);
      var b2 = new BeaconEvent(0,2,0);
      var b3 = new BeaconEvent(0,3,0);

      var height = 20;
      var width = 50;
      var y = 5;
      var x_offset = 140;
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

function BeaconEvent(duration, proximity,start,id){
 
this.duration=duration; this.proximity=proximity; this.start=start;
this.style="";
this.beaconId=id;
this.height = 0;

switch(proximity){ //unknown, immediate, nearby, far
case 0:{
 this.height = 20; this.style="red"; break;
} case 1:{
 this.height = 90; this.style="blue"; break;
} case 2:{
 this.height = 50; this.style="green"; break;
} case 3:{
 this.height = 70; this.style="orange"; break;
} }

}

function BeaconRegionEvent(duration, state,start,id){
 
this.duration=duration;
this.state=state;
this.start=start;
this.style="";
this.beaconId=id;
this.height = 0;

switch(state){ //unknown, inside, outside

case 0:{
 this.height = 10; this.style="red"; break;
} case 1:{
 this.height = 100; this.style="yellow"; break;
} case 2:{
 this.height = 100; this.style="green"; break;
}
}

}

BeaconEvent.prototype.draw = function(x,bottom,duration){

              ctx.fillStyle = this.style;
              ctx.fillRect(x,bottom-this.height,duration, this.height);
              ctx.strokeRect(x,bottom-this.height,duration, this.height);

      }
  
BeaconRegionEvent.prototype.draw = function(x,bottom,duration){

              ctx.fillStyle = this.style;
              ctx.fillRect(x,bottom-this.height,duration, this.height);
              ctx.strokeRect(x,bottom-this.height,duration, this.height);

      }

			      