<?php



require 'facebook.php';

require 'db.php';



// Create our Application instance (replace this with your appId and secret).

$facebook = new Facebook(array(

  'appId'  => '<APPID HERE>',
  'secret' => '<SECRETHERE>',

  'cookie' => true,

));



$db = new Database();



$session = $facebook->getSession();



//print_r($db->getJamList(1));



//echo $db->createJam('3', Array(1, 2, 3), "testSession");

$me = null;

// Session based API call.

if ($session) {



$fbid = $session['uid'];



$savedJams = $db->getJamList($fbid);



//print_r($savedJams);

  try {

    $uid = $facebook->getUser();

    $me = $facebook->api('/me');

    

    $friends = $facebook->api('/me/friends');



    

  } catch (FacebookApiException $e) {

    error_log($e);

  }	

} else {

	header('Location: fbcanvas.php');

	exit();

}



if(isset($_GET['req']))

{

	$sessid = $db->updateSession($_GET['req'], $fbid);

	//echo 'Location: index.html?' . $fbid . '&' . $sessid;

	header('Location: index.html?' . $fbid . '&' . $sessid); 

}



//print_r($_GET);



?>

  <link rel="stylesheet" href="css/style.css?v=1">

<div id="mainCont">

<a href="newjam.php" />Start a new Jam</a><br /><br /><br />

<h2>My Saved Jams</h2>

<?php foreach($savedJams as $jam): ?>

<a href="index.html?<?=$fbid?>&<?=$jam->sessid?>"><?=$jam->desc ?> </a><br />



<?php endforeach; ?>

</div>


	    

</body>

</html>



