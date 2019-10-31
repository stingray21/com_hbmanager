<?php
/*
http://handball.local/ticker_feed.php?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getGameInfo
http://handball.local/ticker_feed.php?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getTickerCount
http://handball.local/ticker_feed.php?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getTickerMessage&index=0
*/

$numNewEvents = abs(rand(0,5));
// $numNewEvents = 10;

$token = (isset($_GET["token"])) ? htmlspecialchars($_GET["token"]) : '' ;
$appid = (isset($_GET["appid"])) ? htmlspecialchars($_GET["appid"]) : '' ;
$cmd = (isset($_GET["cmd"])) ? htmlspecialchars($_GET["cmd"]) : '' ;
$index = (isset($_GET["index"])) ? htmlspecialchars($_GET["index"]) : '' ;

$folder = './';
$file = 'ticker_test';

$data = file_get_contents($folder.$file.'.json');
$data = json_decode($data);
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($data);echo'</pre>';

$testdata_gameInfo = $data->gameInfo;
$testdata_events = $data->events;

	// {"status":2,"status_descripion":"No message found"}
	
	// Tor für die Heimmannschaft durch die Nummer 2
	// Mannschafts-Auszeit der Gastmannschaft
	// Verwarnung für die Nummer 17 der Gastmannschaft
	// 2-min Strafe für die Nummer 5 der Gastmannschaft
	// Spielstand 1. Halbzeit
	// Spielstand 2. Halbzeit
	// 7m-Wurf für die Gastmannschaft: Kein Treffer durch die Nummer 5
	// Erfolgreicher 7m-Wurf für die Gastmannschaft durch die Nummer 7


switch ($cmd) {
	case 'getGameInfo':
		$result = getGameInfo($testdata_gameInfo);
		break;
		
		case 'getTickerCount':
		$result = getTickerCount($index, $testdata_events, $numNewEvents);
		break;
		
		case 'getTickerMessage':
		$result = getTickerMessage($index, $testdata_events);
		break;
	
		default:
		$result = '';
		break;
}

header("Content-type: application/json");
echo $result;
 

function getGameInfo($gameInfo) {
	// $url_request = "?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getGameInfo";
	// $response = '{"class_lname":"Geislingen bei Balingen","guest_lname":"HSG Baar 4","home_lname":"HK Ostdorf\/Geislingen 3","datetime":"2018-10-07 17:00:00","gym_name":"Schloßparkhalle","gym_town":"Geislingen bei Balingen","report":{"refereeA":{"name":"Gagesch","prename":"Oliver"},"refereeB":{"name":null,"prename":null}},"status":0,"status_descripion":"OK"}';
	
	$response = json_encode($gameInfo);

	return $response;
}

function getTickerCount($index, $events, $numNewEvents) {
	// $url_request = "?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getTickerCount";
	// $response = '{"count":35,"ticker_update":25,"status":0,"status_descripion":"OK"}';

	// $id = ($index > 0) ? $index-1 : 0; 
	$id = $index; 
	$id = $id+$numNewEvents; 
	$max = count($events);
	if ($id > $max) $id = $max;

	$count = '{"count":'.($id).',"ticker_update":50,"status":0,"status_descripion":"OK"}';

	$data = json_decode($count);
	$response = json_encode($data);

	return $response;
}

function getTickerMessage($index, $events) {
	// $url_request = "?token=0cfc85a55beb2db4522f8c0791209800&appid=&cmd=getTickerMessage&index=0";
	// $response = '{"game_time":"0","home_score":"0","guest_score":"0","message":"Spielzeit gestartet","stops_time":false,"status":0,"status_descripion":"OK"}';
	
	$response = json_encode($events[$index]);

	return $response;
}

