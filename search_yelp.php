<?php

/**
 * Yelp API v2.0 code sample.
 *
 * This program demonstrates the capability of the Yelp API version 2.0
 * by using the Search API to query for businesses by a search term and location,
 * and the Business API to query additional information about the top result
 * from the search query.
 * 
 * Please refer to http://www.yelp.com/developers/documentation for the API documentation.
 * 
 * This program requires a PHP OAuth2 library, which is included in this branch and can be
 * found here:
 *      http://oauth.googlecode.com/svn/code/php/
 * 
 * Sample usage of the program:
 * `php sample.php --term="bars" --location="San Francisco, CA"`
 */

// Enter the path that the oauth library is in relation to the php file
require_once('lib/OAuth.php');

// Set your OAuth credentials here  
// These credentials can be obtained from the 'Manage API Access' page in the
// developers documentation (http://www.yelp.com/developers)
$CONSUMER_KEY = 'PXNfnoLhbhY-T6dKXxcCZA';
$CONSUMER_SECRET = 'gPbauYq0s7QyIRgi2ki6goKNwIA';
$TOKEN = 'khH1ELGUY0sRgbOk49eOyEH1hg6iPQJU';
$TOKEN_SECRET = 'Mxz7KKRPAGOCmVzYJdMUXHQRu8s';


$API_HOST = 'api.yelp.com';
$DEFAULT_TERM = 'dinner';
$DEFAULT_LOCATION = 'San Francisco, CA';
$SEARCH_LIMIT = 5;
$SEARCH_PATH = '/v2/search/';
$BUSINESS_PATH = '/v2/business/';

//Actual search values
$LOCATION_DEFAULT = $_POST['location_input'];
$location = $_POST['location_input'];
$CATEGORY = $_POST['type_of_location'];
$category_filter = $_POST['type_of_location'];
$DEFAULT_RADIUS_FILTER = 8000;
$radius_filter = 8000;
$limit = 5;

/** 
 * Makes a request to the Yelp API and returns the response
 * 
 * @param    $host    The domain host of the API 
 * @param    $path    The path of the APi after the domain
 * @return   The JSON response from the request      
 */
function request($host, $path) {
    $unsigned_url = "https://" . $host . $path;

    // Token object built using the OAuth library
    $token = new OAuthToken($GLOBALS['TOKEN'], $GLOBALS['TOKEN_SECRET']);

    // Consumer object built using the OAuth library
    $consumer = new OAuthConsumer($GLOBALS['CONSUMER_KEY'], $GLOBALS['CONSUMER_SECRET']);

    // Yelp uses HMAC SHA1 encoding
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    $oauthrequest = OAuthRequest::from_consumer_and_token(
        $consumer, 
        $token, 
        'GET', 
        $unsigned_url
    );
    
    // Sign the request
    $oauthrequest->sign_request($signature_method, $consumer, $token);
    
    // Get the signed URL
    $signed_url = $oauthrequest->to_url();
    
    // Send Yelp API Call
    try {
        $ch = curl_init($signed_url);
        if (FALSE === $ch)
            throw new Exception('Failed to initialize');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);

        if (FALSE === $data)
            throw new Exception(curl_error($ch), curl_errno($ch));
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($data, $http_status);

        curl_close($ch);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
    
    return $data;
}

/**
 * Query the Search API by a search term and location 
 * 
 * @param    $term        The search term passed to the API 
 * @param    $location    The search location passed to the API 
 * @return   The JSON response from the request 
 */
//function search($term, $location) {
function search($location, $category_filter, $radius_filter, $limit) {
    $url_params = array();
    $url_params['location'] = $location?: $GLOBALS['LOCATION_DEFAULT'];
	$url_params['category_filter'] = $category_filter?: $GLOBALS['CATEGORY'];
	$url_params['radius_filter'] = $radius_filter?: $GLOBALS['DEFAULT_RADIUS_FILTER'];
    $url_params['limit'] = $limit?: $GLOBALS['SEARCH_LIMIT'];
    $search_path = $GLOBALS['SEARCH_PATH'] . "?" . http_build_query($url_params);
    
    return request($GLOBALS['API_HOST'], $search_path);
}

/**
 * Query the Business API by business_id
 * 
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request 
 */
function get_business($business_id) {
    $business_path = $GLOBALS['BUSINESS_PATH'] . $business_id;
    
    return request($GLOBALS['API_HOST'], $business_path);
}

/**
 * Queries the API by the input values from the user 
 * 
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */
//function query_api($term, $location) { 
function query_api($location, $category_filter, $radius_filter, $limit) {     
    
	global $business1, $business2, $business3, $business4, $business5;
	global $hospital1, $hospital2, $hospital3, $hospital4, $hospital5;
	global $police1, $police2, $police3, $police4, $police5;
	global $culoare1, $culoare2, $culoare3, $culoare4, $culoare5; 
	global $fail1, $fail2, $fail3, $fail4, $fail5;
	$response = json_decode(search($location, $category_filter, $radius_filter, $limit), true);
	
	$business1 = $response['businesses'][0];
	$response_hospital1 = json_decode(search($business1['location']['postal_code'], 'hospitals', 8000, 1), true);
	$hospital1 = $response_hospital1['businesses'][0];
	$response_police1 = json_decode(search($business1['location']['postal_code'], 'policedepartments', 8000, 1), true);
	$police1 = $response_police1['businesses'][0];
	if (($hospital1['name'] == "") && ($police1['name'] != ""))
	{
		$fail1 = 1;
		$culoare1 = 'FF6600';
	}
	else if (($hospital1['name'] != "") && ($police1['name'] == ""))
	{
		$fail1 = 2;
		$culoare1 = 'FF6600';
	}
	else if (($hospital1['name'] == "") && ($police1['name'] == ""))
	{
		$fail1 = 3;
		$culoare1 = '#B33A3A';
	}
	else
	 	$culoare1 = '#96C447';
	
	
	$business2 = $response['businesses'][1];
	$response_hospital2 = json_decode(search($business2['location']['postal_code'], 'hospitals', 8000, 1), true);
	$hospital2 = $response_hospital2['businesses'][0];
	$response_police2 = json_decode(search($business2['location']['postal_code'], 'policedepartments', 8000, 1), true);
	$police2 = $response_police2['businesses'][0];
	if (($hospital1['name'] == "") && ($police1['name'] != ""))
	{
		$fail2 = 1;
		$culoare2 = 'FF6600';
	}
	else if (($hospital2['name'] != "") && ($police2['name'] == ""))
	{
		$fail2 = 2;
		$culoare2 = 'FF6600';
	}
	else if (($hospital2['name'] == "") && ($police2['name'] == ""))
	{
		$fail2 = 3;
		$culoare2 = '#B33A3A';
	}
	else
	 	$culoare2 = '#96C447';
	
	
	$business3 = $response['businesses'][2];
	$response_hospital3 = json_decode(search($business3['location']['postal_code'], 'hospitals', 8000, 1), true);
	$hospital3 = $response_hospital3['businesses'][0];
	$response_police3 = json_decode(search($business3['location']['postal_code'], 'policedepartments', 8000, 1), true);
	$police3 = $response_police3['businesses'][0];
	if (($hospital3['name'] == "") && ($police3['name'] != ""))
	{
		$fail3 = 1;
		$culoare3 = 'FF6600';
	}
	else if (($hospital3['name'] != "") && ($police3['name'] == ""))
	{
		$fail3 = 2;
		$culoare3 = 'FF6600';
	}
	else if (($hospital3['name'] == "") && ($police3['name'] == ""))
	{
		$fail3 = 3;
		$culoare3 = '#B33A3A';
	}
	else
	 	$culoare3 = '#96C447';
	
	$business4 = $response['businesses'][3];
	$response_hospital4 = json_decode(search($business4['location']['postal_code'], 'hospitals', 8000, 1), true);
	$hospital4 = $response_hospital4['businesses'][0];
	$response_police4 = json_decode(search($business4['location']['postal_code'], 'policedepartments', 8000, 1), true);
	$police4 = $response_police4['businesses'][0];
	if (($hospital4['name'] == "") && ($police4['name'] != ""))
	{
		$fail4 = 1;
		$culoare4 = 'FF6600';
	}
	else if (($hospital4['name'] != "") && ($police4['name'] == ""))
	{
		$fail4 = 2;
		$culoare4 = 'FF6600';
	}
	else if (($hospital4['name'] == "") && ($police4['name'] == ""))
	{
		$fail4 = 3;
		$culoare4 = '#B33A3A';
	}
	else
	 	$culoare4 = '#96C447';
	
	$business5 = $response['businesses'][4];
	$response_hospital5 = json_decode(search($business5['location']['postal_code'], 'hospitals', 1000, 1), true);
	$hospital5 = $response_hospital5['businesses'][0];
	$response_police5 = json_decode(search($business5['location']['postal_code'], 'policedepartments', 1000, 1), true);
	$police5 = $response_police5['businesses'][0];
	if (($hospital5['name'] == "") && ($police5['name'] != ""))
	{
		$fail5 = 1;
		$culoare5 = 'FF6600';
	}
	else if (($hospital5['name'] != "") && ($police5['name'] == ""))
	{
		$fail5 = 2;
		$culoare5 = 'FF6600';
	}
	else if (($hospital5['name'] == "") && ($police5['name'] == ""))
	{
		$fail5 = 3;
		$culoare5 = '#B33A3A';
	}
	else
	 	$culoare5 = '#96C447';
}
query_api($location, $category_filter, $radius_filter, $limit);

$culoare = '#0F0';
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Search</title>
​
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
​
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body style="padding: 150px 100px 0px 100px;">
​
​
​
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne"   style="background-color:<?php echo $culoare1?>">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <h2><?php echo $business1['name']; ?></h2>
          <p><?php echo $business1['location']['display_address'][0]; echo "\n"; echo $business1['location']['display_address'][1]; ?></p>
          <p>You can call them at: <?php echo $business1['phone']; ?></p>
          <p>The rating is: <?php echo $business1['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
        <h2><?php echo $hospital1['name']; ?></h2>
          <p><?php echo $hospital1['location']['display_address'][0]; echo "\n"; echo $hospital1['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail1 == 1) || ($fail1 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$hospital1['phone']."</br>"; 
			  echo 'The rating is: '.$hospital1['rating'];
          }
		  ?>
          </p>
        </div>
        <div class="col-md-6">
        <h2><?php echo $police1['name']; ?></h2>
          <p><?php echo $police1['location']['display_address'][0]; echo "\n"; echo $police1['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail1 == 2) || ($fail1 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$police1['phone']."</br>"; 
			  echo 'The rating is: '.$police1['rating'];
          }
		  ?>
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo" style="background-color:<?php echo $culoare2?>">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          <h2><?php echo $business2['name']; ?></h2>
          <p><?php echo $business2['location']['display_address'][0]; echo "\n"; echo $business2['location']['display_address'][1];?></p>
          <p>You can call them at: <?php echo $business2['phone']; ?></p>
          <p>The rating is: <?php echo $business2['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
        <h2><?php echo $hospital2['name']; ?></h2>
          <p><?php echo $hospital2['location']['display_address'][0]; echo "\n"; echo $hospital2['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail2 == 1) || ($fail2 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$hospital2['phone']."</br>"; 
			  echo 'The rating is: '.$hospital2['rating'];
          }
		  ?>
          </p>
        </div>
        <div class="col-md-6">
        <h2><?php echo $police2['name']; ?></h2>
          <p><?php echo $police2['location']['display_address'][0]; echo "\n"; echo $police2['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail2 == 2) || ($fail2 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$police2['phone']."</br>"; 
			  echo 'The rating is: '.$police2['rating'];
          }
		  ?>
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree" style="background-color:<?php echo $culoare3?>" >
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          <h2><?php echo $business3['name']; ?></h2>
          <p><?php echo $business3['location']['display_address'][0]; echo "\n"; echo $business3['location']['display_address'][1];?></p>
          <p>You can call them at: <?php echo $business3['phone']; ?></p>
          <p>The rating is: <?php echo $business3['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
        <h2><?php echo $hospital3['name']; ?></h2>
          <p><?php echo $hospital3['location']['display_address'][0]; echo "\n"; echo $hospital3['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail3 == 1) || ($fail3 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$hospital3['phone']."</br>"; 
			  echo 'The rating is: '.$hospital3['rating'];
          }
		  ?>
          </p>
        </div>
        <div class="col-md-6">
        <h2><?php echo $police3['name']; ?></h2>
          <p><?php echo $police3['location']['display_address'][0]; echo "\n"; echo $police3['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail3 == 2) || ($fail3 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$police3['phone']."</br>"; 
			  echo 'The rating is: '.$police3['rating'];
          }
		  ?>
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFour" style="background-color:<?php echo $culoare4?>">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
          <h2><?php echo $business4['name']; ?></h2>
          <p><?php echo $business4['location']['display_address'][0]; echo "\n"; echo $business4['location']['display_address'][1];?></p>
          <p>You can call them at: <?php echo $business4['phone']; ?></p>
          <p>The rating is: <?php echo $business4['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
      <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
        <h2><?php echo $hospital4['name']; ?></h2>
          <p><?php echo $hospital4['location']['display_address'][0]; echo "\n"; echo $hospital4['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail4 == 1) || ($fail4 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$hospital4['phone']."</br>"; 
			  echo 'The rating is: '.$hospital4['rating'];
          }
		  ?>
          </p>
        </div>
        <div class="col-md-6">
        <h2><?php echo $police4['name']; ?></h2>
          <p><?php echo $police4['location']['display_address'][0]; echo "\n"; echo $police4['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail4 == 2) || ($fail4 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$police4['phone']."</br>"; 
			  echo 'The rating is: '.$police4['rating'];
          }
		  ?>
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFive" style="background-color:<?php echo $culoare5?>">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
          <h2><?php echo $business5['name']; ?></h2>
          <p><?php echo $business5['location']['display_address'][0]; echo "\n"; echo $business5['location']['display_address'][1];?></p>
          <p>You can call them at: <?php echo $business5['phone']; ?></p>
          <p>The rating is: <?php echo $business5['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
      <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
        <h2><?php echo $hospital5['name']; ?></h2>
          <p><?php echo $hospital5['location']['display_address'][0]; echo "\n"; echo $hospital5['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail5 == 1) || ($fail5 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$hospital5['phone']."</br>"; 
			  echo 'The rating is: '.$hospital5['rating'];
          }
		  ?>
          </p>
        </div>
        <div class="col-md-6">
        <h2><?php echo $police5['name']; ?></h2>
          <p><?php echo $police5['location']['display_address'][0]; echo "\n"; echo $police5['location']['display_address'][1];?></p>
          <p>
		  <?php 
		  if (($fail5 == 2) || ($fail5 == 3))
		  {
		  	echo "<img src=exclamare.png>";
		  }
		  else 
		  {
			  echo "You can call them at: ".$police5['phone']."</br>"; 
			  echo 'The rating is: '.$police5['rating'];
          }
		  ?>
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>
​
​
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
