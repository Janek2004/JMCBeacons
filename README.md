# JMCBeacons

## Overview
JMCBeacons is a simple WordPress plugin for iBeacons

## Installation

## Details
iOS app sends HTTP request to the endpoint
endpoint distincts different request by querying 'action' url paramater $_REQUEST['action'] in missions_json.php (subject to change).

API distincs several scenarios:
*$_REQUEST['action']==="saveRegion"

Paramaters
 -date
 -entered
 -user
 -beacon id, minor, major

*$_REQUEST['action']==="saveProximity"

Paramaters
-date
-proximity
-user
-beacon id, minor, major


*$_REQUEST['action']==="getData"
