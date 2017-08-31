<?php
session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '210057002777946',
  'app_secret' => 'b94d37cef744e00509f97215cf128e89',
  'default_graph_version' => 'v2.8',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['publish_actions']; // optionnal
try {
	if (isset($_SESSION['facebook_access_token'])) {
	$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }
if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	// validating the access token
	try {
		$request = $fb->get('/me');
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		if ($e->getCode() == 190) {
			unset($_SESSION['facebook_access_token']);
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('https://codefros.com/apps/ju/index1.php', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
			exit;
		}
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	// getting basic info about user
	try {
		$requestPicture = $fb->get('/me/picture?redirect=false&width=500&height=500'); //getting user picture
		$requestProfile = $fb->get('/me'); // getting basic info
		$picture = $requestPicture->getGraphUser();
		$profile = $requestProfile->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		unset($_SESSION['facebook_access_token']);
		echo "<script>window.top.location.href='https://codefros.com/apps/ju/index1.php'</script>";
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	// priting basic info about user on the screen
	//print_r($picture);
	$img = __DIR__.'/img1/'.$profile['id'].'.jpg';

	file_put_contents($img, file_get_contents($picture['url']));
  	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']

include_once("ak_php_img_lib_1.0.php");
$target_file = "$img";
$resized_file = 'img1/'.$profile['id'].'.jpg';
$wmax = 512;
$hmax = 520;
ak_img_resize($target_file, $resized_file, $wmax, $hmax);

try {
	$stamp = imagecreatefrompng('ju.png');
$im = imagecreatefromjpeg('img1/'.$profile['id'].'.jpg');

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 0;
$marge_bottom = 0;
$sx = imagesx($stamp);
$sy = imagesy($stamp);

// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

// Output and free memory
//header('Content-type: image/png');
imagepng($im,'img1/'.$profile['id'].'.png');
imagedestroy($im);
unlink($img);

}
 catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

	//echo 'name' .' '. $profile['name'];
if (isset($_POST['submit'])) {


	
	$permissions = $fb->get('/me/permissions');
	$permissions = $permissions->getGraphEdge()->asArray();
	// printing declined and granted permission
	//echo "<pre>";
	//print_r($permissions);
	//echo "</pre>";
	
	foreach ($permissions as $key) {
		if ($key['status'] == 'declined') {
			$declined[] = $key['permission'];
			$loginUrl = $helper->getLoginUrl('https://codefros.com/apps/ju/index1.php', $declined);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
		}
	}

			try {
		// message must come from the user-end
		$data = ['source' => $fb->fileToUpload(__DIR__.'/img1/'.$profile['id'].'.png'), 'message' => $_POST['message']];
		$request = $fb->post('/me/photos', $data);
		$response = $request->getGraphNode()->asArray();

		try {
		$post = $fb->post('/'.$response['id'].'/comments', array('message' => ''));
		$post = $post->getGraphNode()->asArray();
		
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		//echo 'Graph returned an error: ' . $e->getMessage();
		session_destroy();
		//exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	

		echo "<script>window.top.location.href='https://facebook.com/photo.php?fbid=".$response['id']."&makeprofile=1'</script>";
		

	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	//echo $response['id'];
	//$img1 = __DIR__.'/img/'.$profile['id'].'.png';
	//unlink($img1);


	}
	


	?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="description" content="JU">
    <meta name="author" content="CODEFROS">
        
        <title>JU</title>
        <meta property="og:title" content="JU 34th Anniversery" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="https://www.codefros.com/apps/ju/mask_sample.jpg" />
         <meta property="og:site_name" content="JU 34th Anniversery" />
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/style2.css" />
        <script type="text/javascript" src="js/modernizr.custom.86080.js"></script>
         <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <!-- Custom Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="css/animate.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/creative.css" type="text/css">
    
    <link rel="stylesheet" href="css/style.css" type="text/css">
    
    <link rel="stylesheet" href="css/style1.css" type="text/css">
    <link rel="apple-touch-icon" sizes="57x57" href="icon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="icon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="icon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="icon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="icon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="icon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="icon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="icon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="icon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="icon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="icon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="icon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="icon/favicon-16x16.png">
<link rel="manifest" href="icon/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="icon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
  </head> 
  <body id="page">
<ul class="cb-slideshow">
            <li><span>Image 01</span><div><h3></h3></div></li>
            <li><span>Image 02</span><div><h3></h3></div></li>
            <li><span>Image 03</span><div><h3></h3></div></li>
            <li><span>Image 04</span><div><h3></h3></div></li>
            <li><span>Image 05</span><div><h3></h3></div></li>
            <li><span>Image 06</span><div><h3></h3></div></li>
        </ul>
    <header>
        <div class="container">
            <div class="row">
                <div class="col-md-12 top"><img src="img/ju_logo.png"></div>
               <!--<div class="col-md-12 mainhead"><img src="img/green.png"></div>-->
                <!--<div class="col-md-12 mainhead"><h1 style="color: #39B24A;font-size: 50px;"><b>#Green_Campus</b></h1></div>-->
                
                <div class="col-md-offset-3 col-sm-6 col-md-offset-3">  
                    <div class="header-content">
                        <div class="header-content-inner">
                        <form action="" method='POST'>
                            <h2><?php echo '<img style="width:265px;height:265px;border: 4px solid #fff;" src="img1/'.$profile['id'].'.png"/>';?></h2>
                            <!--<a href="" class="btn btn-primary btn-xl page-scroll maskbtn"><i class="fa fa-facebook"></i> &nbsp;&nbsp;    Upload on Facebook</a>-->
                            <button style="width:270px;" class="btn btn-primary btn-xl page-scroll maskbtn" type="submit" name="submit"><i class="fa fa-facebook"></i> &nbsp;&nbsp;    Upload on Facebook</button>
                            </form>
                            <div class="s_text">
                            <p></p>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>   
        </div>
    </header>
    
<div class="foot">
    <div class="container">
        <div class="row">
                    <div class="col-md-3 credit"><p>Developed by: <a href="https://codefros.com" target="_blank">CODEFROS</a></p></div>
            <div class="col-md-9 partner"><a href="https://www.facebook.com/codefros/" target="_blank"><img src="img/codefros.png"></div></a>
            <!--<div class="col-md-9 partner"><h1 style="color: #fff;font-size: 15px;"><b>Daffodil International University</b></h1></div>-->
            
        </div>
<!--     <p>Developed by: <a href="http://etl.com.bd" target="_blank">Ezze Technology Ltd.</a><a href="http://www.prothom-alo.com/"><span class="pull-right">ProthomAlo</span></a></p> -->
    </div>
</div>

         <!-- jQuery -->
    <script src="js/jquery.js"></script>
<!--
    <script src="js/jquery.viewportchecker.js"></script>
    <script src="js/jquery.animateNumber.js"></script>
-->
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
<!--
    <script src="js/jquery.fittext.js"></script>
    <script src="js/wow.min.js"></script>
-->
    <!-- Custom Theme JavaScript -->
    <script src="js/creative.js"></script>
    
</body>
</html>

	<?php


} 
else {
	$helper = $fb->getRedirectLoginHelper();
	$loginUrl = $helper->getLoginUrl('https://codefros.com/apps/ju/index1.php', $permissions);
	echo "<script>window.top.location.href='".$loginUrl."'</script>";
}