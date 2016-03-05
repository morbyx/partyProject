#!/usr/bin/php
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
$SEARCH_LIMIT = 10;
$SEARCH_PATH = '/v2/search/';
$BUSINESS_PATH = '/v2/business/';

//Actual search values
//$TERM = $_POST['term_input'];
$LOCATION = $_POST['location_input'];
$CATEGORY = $_POST['type_of_location'];
$DEFAULT_RADIUS_FILTER = 8000;


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
function search($term, $location) {
    $url_params = array();
    
    //$url_params['term'] = $term ?: $GLOBALS['DEFAULT_TERM'];
    $url_params['location'] = $location?: $GLOBALS['LOCATION'];
	$url_params['category_filter'] = $GLOBALS['CATEGORY'];
	$url_params['radius_filter'] = $GLOBALS['DEFAULT_RADIUS_FILTER'];
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];
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
function query_api($term, $location) {     
    $response = json_decode(search($term, $location));
    $business_id = $response->businesses[0]->id;
	$business_id1 = $response->businesses[1]->id;
	$business_id2 = $response->businesses[2]->id;
	$business_id3 = $response->businesses[3]->id;
	$business_id4 = $response->businesses[4]->id;
	$business_id5 = $response->businesses[5]->id;
	$business_id6 = $response->businesses[6]->id;
	$business_id7 = $response->businesses[7]->id;
	$business_id8 = $response->businesses[8]->id;
	$business_id9 = $response->businesses[9]->id;
	
    
    print sprintf(
        "%d businesses found, querying business info for the top result \"%s\"\n\n",         
        count($response->businesses),
        $business_id
    );
    
	print sprintf(
        "%d businesses found, querying business info for the top result \"%s\"\n\n",         
        count($response->businesses),
        $business_id
    );
    $response = get_business($business_id);
	$response1 = get_business($business_id1);
	$response2 = get_business($business_id2);
    $response3 = get_business($business_id3);
	$response4 = get_business($business_id4);
	$response5 = get_business($business_id5);
	$response6 = get_business($business_id6);
	$response7 = get_business($business_id7);
	$response8 = get_business($business_id8);
	$response9 = get_business($business_id9);
	
    print sprintf("Result for business \"%s\" found:\n", $business_id);
    echo $response->display_address;
	echo $response->postal_code;
	/**
	print sprintf("Result for business \"%s\" found:\n", $business_id1);
	print "$response1\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id2);
	print "$response2\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id3);
	print "$response3\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id4);
	print "$response4\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id5);
	print "$response5\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id6);
	print "$response6\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id7);
	print "$response7\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id8);
	print "$response8\n";
	
	print sprintf("Result for business \"%s\" found:\n", $business_id9);
	print "$response9\n";
	**/
}

/**
 * User input is handled here 
 */
$longopts  = array(
    "term::",
    "location::",
	//"category_filter::",
	//"radius_filter::",
	//"limit::",
);
    
$options = getopt("", $longopts);

$term = $options['term'] ?: '';
$location = $options['location'] ?: '';

query_api($term, $location);

?>
