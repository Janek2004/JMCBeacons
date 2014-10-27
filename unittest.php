<?php
error_reporting(E_ALL);
function testNetworkCalls(){
	//Login

	$request = 'http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/?missions_json=1&action=login&user=jabbey&password=2731';
	$response = file_get_contents($request);
	echo $response;
	
	$obj = json_decode($response);
	
	$user = $obj->{'userid'};
	$session = $obj->{'session'};
	
	//print $session;
	echo '<br> User is:'.$user;
	echo '<br> Session is:'.$session;
	
	//change status
	$request = 'http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/?missions_json=1&action=updatenurse&nurse=1&session='.$session;
	$response = file_get_contents($request);	
	echo '<br>';
	echo $response;	
	
	//logout
	$request = 'http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/?missions_json=1&action=logout&session=1';
	$response = file_get_contents($request);	
	echo $response;	
	echo '<br>';
	//scan 
	$request = 'http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/?missions_json=1&action=scan&barcode=1&nurse='.$user.'&session='.$session;
	$response = file_get_contents($request);	
	echo $response;	
	echo '<br>';
	
	//override
	$request = 'http://atcwebapp.argo.uwf.edu/trainingstations/wp_trainingstations/?missions_json=1&action=override&nurse='.$user.'&session='.$session;
	$response = file_get_contents($request);	
	echo $response;	
	echo '<br>';
	
}

testNetworkCalls();

?>