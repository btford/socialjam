<?php

/**	Social Jam
	Facebook Camp Hackathon Project
	University of Michigan
	3.25 - 3.26 _ 2011
	https://github.com/btford/socialjam
	
	File:	server.php
	Desc:	PHP script to handle communication between client-side and server
*/

$start_time = time();
$end_time = $start_time + 20;
//echo $end_time . '<br />';

// set time limit to 30 seconds
set_time_limit(30);

require 'config.php';
require 'db.php';
require 'facebook.php';

$facebook = new Facebook(array(
  'appId'  => '<APPID HERE>',
  'secret' => '<SECRETHERE>',
  'cookie' => true,
));



// establish connection to the database

$db = new Database();

$link = mysql_connect($mysql_host, $mysql_user, $mysql_pass);
if (!$link) {
	die('Could not connect: ' . mysql_error());
}
if (!mysql_select_db($mysql_name)) {
	die('Could not select database: ' . mysql_error());
}

$action = $_GET['action'];

$reqObj->session = mysql_real_escape_string($_POST['session']);
$reqObj->userid = mysql_real_escape_string($_POST['user']);
$reqObj->timestamp = mysql_real_escape_string($_POST['timestamp']);


if($action == "push"){
	$cont = mysql_real_escape_string(json_encode($_POST['content']));
	$config = mysql_real_escape_string(json_encode($_POST['config']));
	$contJson = json_encode($cont);
	$reqObj->cont = $contJson;
	$reqObj->config = json_encode($config);

	$query = 'UPDATE `usersess` SET `content`=' . stripslashes($reqObj->cont) . ', `config` = ' . stripslashes($reqObj->config) . ' WHERE `userid`=' . $reqObj->userid . ' AND `sessid`="' . $reqObj->session . '"';

	$result = mysql_query($query);
}
else if($action == "pull"){
	
	$num_rows = 0;
	
	while(time()<$end_time){
		$query = 'SELECT * FROM usersess WHERE `userid`!=' . $reqObj->userid . ' AND `sessid`=' . $reqObj->session . ' AND `timestamp`>"' . $reqObj->timestamp . '"';
		$result = mysql_query($query);
		//echo $query . '<br />';
		if (!$result) {
			die('Could not query:' . mysql_error());
		}
		$num_rows = mysql_num_rows($result);
		if($num_rows == 0){
			usleep(1000000);
		}
		else {
			break;
		}
	}
	
	if($num_rows != 0){
		while($row = mysql_fetch_assoc($result)){
			$jsonArr = Array('userid'=>$row['userid'], 'content'=>json_decode($row['content']),'config'=>json_decode($row['config']), 'timestamp'=> date ("Y-m-d H:i:s"));
		}
		
		echo json_encode($jsonArr);
	}
	
}
else if ($action == "init"){
	$num_rows = 0;
	
	$query = 'SELECT * FROM usersess WHERE `sessid`=' . $reqObj->session;;
	$result = mysql_query($query);
	//echo $query . '<br />';
	if (!$result) {
		die('Could not query:' . mysql_error());
	}
	$num_rows = mysql_num_rows($result);
	
	if($num_rows != 0){
		while($row = mysql_fetch_assoc($result)){
			
			if($row['userid'] == 0)
			{
				$name = "New user";	
			}
			else
			{
				$facebookUserData = $facebook->api('/'.$row['userid']);
				$name = $facebookUserData['name'];
			}
			$jsonArr[] = Array('userid'=>$row['userid'], 'content'=>json_decode($row['content']), 'config'=>json_decode($row['config']), 'name'=>$name);
		}
		
		$output['data'] = $jsonArr;
		$output['timestamp'] =  date ("Y-m-d H:i:s");
		$output['doit'] = "FUCK IT, WE'LL DO IT LIVE";
		echo json_encode($output);
	}
}
else if ($action == "fbreq") {
	$reqs = explode(",", $_POST['requests']);
	foreach($reqs as $req)
	{
		if($req != "") {
		$ret[] = $db->createEmptySession($req . "", $_POST['user'], $_POST['title']);		
			
		}
	}
	echo json_encode($ret);
	//print_r($reqs);

}


mysql_close($link);

?>