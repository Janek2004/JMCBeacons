// JavaScript Document
console.log("Testing jquery");
jQuery(document).ready(function($)
{
	
	jQuery('#jmcbeacons-station-immediate-message-id_beacon_delete_images_button').click(function(e) {
    e.preventDefault();
		var check = confirm("Do you really want to delete it?");
		if(check){
	    jQuery('#publish').click();		
		}
	});
	
	jQuery('#jmcbeacons-station-nearby-message-id_beacon_delete_images_button').click(function(e) {
    e.preventDefault();
		var check = confirm("Do you really want to delete it?");
		if(check){
	    jQuery('#publish').click();		
		}
	});
	
});
