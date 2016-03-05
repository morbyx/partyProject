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
    //$response = json_decode(search($term, $location));
	global $business1, $business2, $business3, $business4, $business5;
	global $hospital1, $hospital2, $hospital3, $hospital4, $hospital5;
	global $police1, $police2, $police3, $police4, $police5;
	$response = json_decode(search($location, $category_filter, $radius_filter, $limit), true);
	
	$business1 = $response['businesses'][0];
	$response_hospital1 = json_decode(search($business1['location']['postal_code'], 'hospitals', $radius_filter, 1), true);
	$hospital1 = $response_hospital1['businesses'][0];
	$response_police1 = json_decode(search($business1['location']['postal_code'], 'policedepartments', $radius_filter, 1), true);
	$police1 = $response_police1['businesses'][0];
	//$response_pharmacy1 = json_decode(search($business1['location']['postal_code'], 'pharmacy', $radius_filter, 1), true);
	//$pharmacy1 = $response_pharmacy1['businesses'][0];
	
	$business2 = $response['businesses'][1];
	$response_hospital2 = json_decode(search($business2['location']['postal_code'], 'hospitals', $radius_filter, 1), true);
	$hospital2 = $response_hospital2['businesses'][0];
	$response_police2 = json_decode(search($business2['location']['postal_code'], 'policedepartments', $radius_filter, 1), true);
	$police2 = $response_police2['businesses'][0];
	//$response_pharmacy2 = json_decode(search($business2['location']['postal_code'], 'pharmacy', $radius_filter, 1), true);
	//$pharmacy2 = $response_pharmacy2['businesses'][0];
	
	$business3 = $response['businesses'][2];
	$response_hospital3 = json_decode(search($business3['location']['postal_code'], 'hospitals', $radius_filter, 1), true);
	$hospital3 = $response_hospital3['businesses'][0];
	$response_police3 = json_decode(search($business3['location']['postal_code'], 'policedepartments', $radius_filter, 1), true);
	$police3 = $response_police3['businesses'][0];
	//$response_pharmacy3 = json_decode(search($business3['location']['postal_code'], 'pharmacy', $radius_filter, 1), true);
	//$pharmacy3 = $response_pharmacy3['businesses'][0];
	
	$business4 = $response['businesses'][3];
	$response_hospital4 = json_decode(search($business4['location']['postal_code'], 'hospitals', $radius_filter, 1), true);
	$hospital4 = $response_hospital4['businesses'][0];
	$response_police4 = json_decode(search($business4['location']['postal_code'], 'policedepartments', $radius_filter, 1), true);
	$police4 = $response_police4['businesses'][0];
	//$response_pharmacy4 = json_decode(search($business4['location']['postal_code'], 'pharmacy', $radius_filter, 1), true);
	//$pharmacy4 = $response_pharmacy4['businesses'][0];
	
	$business5 = $response['businesses'][4];
	$response_hospital5 = json_decode(search($business5['location']['postal_code'], 'hospitals', $radius_filter, 1), true);
	$hospital5 = $response_hospital5['businesses'][0];
	$response_police5 = json_decode(search($business5['location']['postal_code'], 'policedepartments', $radius_filter, 1), true);
	$police5 = $response_police5['businesses'][0];
	//$response_pharmacy5 = json_decode(search($business5['location']['postal_code'], 'pharmacy', $radius_filter, 1), true);
	//$pharmacy5 = $response_pharmacy5['businesses'][0];
}
query_api($location, $category_filter, $radius_filter, $limit);

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
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <h2><?php echo $business1['name']; ?></h2>
          <p>Address<?php echo $business1['location']['display_address']; ?></p>
          <p>You can call them at: <?php echo $business1['phone']; ?></p>
          <p>The rating is: <?php echo $business1['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          <h2><?php echo $business2['name']; ?></h2>
          <p>Address<?php echo $business2['location']['display_address']; ?></p>
          <p>You can call them at: <?php echo $business2['phone']; ?></p>
          <p>The rating is: <?php echo $business2['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          <h2><?php echo $business3['name']; ?></h2>
          <p>Address<?php echo $business3['location']['display_address']; ?></p>
          <p>You can call them at: <?php echo $business3['phone']; ?></p>
          <p>The rating is: <?php echo $business3['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFour">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
          <h2><?php echo $business4['name']; ?></h2>
          <p>Address<?php echo $business4['location']['display_address']; ?></p>
          <p>You can call them at: <?php echo $business4['phone']; ?></p>
          <p>The rating is: <?php echo $business4['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFive">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
          <h2><?php echo $business5['name']; ?></h2>
          <p>Address<?php echo $business5['location']['display_address']; ?></p>
          <p>You can call them at: <?php echo $business5['phone']; ?></p>
          <p>The rating is: <?php echo $business5['rating']; ?></p>
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
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
