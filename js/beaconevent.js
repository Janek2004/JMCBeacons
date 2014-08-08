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