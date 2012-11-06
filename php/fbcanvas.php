<?php

require 'facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '<APPID HERE>',
  'secret' => '<SECRETHERE>',
  'cookie' => true,
));

$session = $facebook->getSession();

$me = null;

if ($session) {
	if(isset($_GET['req']))
	{
			header('Location: main.php?req=' . $_GET['req']);	
	}

	else if (isset($_GET['request_ids']))
	{
		if(isset($_GET['request_ids']))
			$reqs = explode(",", $_GET['request_ids']);	
		header('Location: main.php?req=' . $reqs[count($reqs) - 1]);	
	}
	else
	{
		header('Location: main.php');	
	}

}

//print_r($_GET);



?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>php-sdk</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
      <link rel="stylesheet" href="css/style.css?v=1">
  </head>
  <body>
    <!--
      We use the JS SDK to provide a richer user experience. For more info,
      look here: http://github.com/facebook/connect-js
    -->
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
        <?php if(isset($_GET['request_ids'])): ?>
            window.location.replace("fbcanvas.php?req=<?=$reqs[0]?>";    
        <?php else: ?>
        	window.location.replace("fbcanvas.php?req=<?=$reqs[0]?>");    
        <?php endif; ?>

        });
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>


    <h1>Social Jam</h1>

    <div>
      Login to Continue: <fb:login-button perms="publish_stream,offline_access"></fb:login-button>
    </div>
    
  </body>
</html>
