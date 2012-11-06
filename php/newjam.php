<?php



require 'facebook.php';

require 'db.php';



$db = new Database();

// Create our Application instance (replace this with your appId and secret).

$facebook = new Facebook(array(

  'appId'  => '<APPID HERE>',
  'secret' => '<SECRETHERE>',

  'cookie' => true,

));





$session = $facebook->getSession();



$me = null;

// Session based API call.

if ($session) {



$fbid = $session['uid'];



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



//print_r($friends);





if(isset($_POST['title']))

{

	//$friends;

	foreach($_POST['friend'] as $name=>$value)

	{

		if($name != '') $friends1[] = $name;

	}

	

	$friends1[] = $fbid;

	$sessionId = $db->createJam($fbid, $friends1, $_POST['title']);

	header("Location: index.html?" . $fbid . "&" . $sessionId);

 	//echo "Your new jam is.... " . 

}

else

{



?>

  <link rel="stylesheet" href="css/style.css?v=1">
  
<div id="mainCont">

<h2>Title Your Jam</h2>

<form action="newjam.php" method="POST" />

<input name="title" id="title" /><br /><br /><br />

<div id="fb-root"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

<script src="http://connect.facebook.net/en_US/all.js"></script>

<script>
FB.init({ 
	appId:'163551770367358', cookie:true, status:true, xfbml:true 
});

$(document).ready(function() {
	$('#invite-button').click(function() {
		FB.ui(
		{ method: 'apprequests', 
		message: 'Come jam with me bro!',
		title: 'Select a friend to jam with!'},
		
		function(response) {
		console.log(response);
		
		var requestIdList = "";
		
		for (i = 0; i < response.request_ids.length; i += 1) {
                    requestIdList = requestIdList + response.request_ids[i] + ",";
                }
		
		var json = {
		        "requests": requestIdList,
		        "user": "<?=$fbid?>",
		        "title": $('#title').val()
   			 };
		    $.ajax({
		        url: "server.php?action=fbreq",
		        type: "POST",
		        data: json,
		        success: function (data) {
		        	var responseData = $.parseJSON(data);
		        	console.log(responseData[0]);
		            ////$('#result').html("<pre>" + data + "</pre>");
		            window.location.replace("index.html?<?=$fbid?>&" + responseData[0]);
		            //alert('Fuck yeah!');
		        }
		    });	   	
		}
		
		);
	});
});
</script>

<h2>Select a friend (if you have any)</h2>
<input type="button" value="Jam It!" id="invite-button" />

</form>

<br /><br /><br /><br />

<a href="main.php">Home</a>

</div>

<?php } ?>