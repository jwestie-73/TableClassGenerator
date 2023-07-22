<?php
$ip_address = $_SERVER['REMOTE_ADDR'];
$session_id = session_id();
$_SESSION['hitcounter']['session_id'] = $session_id;
$useDatalogger=true;
$useGeolocation = false;


// Download Geolocation Data if allowed
//if (GEOLOCATION) {
if (GEOLOCATION && !isset($_SESSION['geolocation'])) {
	// ^^^ for live version use the lower of the two lines.
	$api_key = '2b142048ffb649db9b12a7c388eec2e0';
	$api_url = 'https://api.ipgeolocation.io/ipgeo?apiKey=' . $api_key . '&ip=' . $ip_address;

	// Initialize CURL:
	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Store the dataas JSON
	$json = curl_exec($ch);
	curl_close($ch);

	// decode JSON into an array
	$geoloc = json_decode($json, true);
	$useGeolocation = true;

} else if (!GEOLOCATION) {
	// set defaults for geolocation
	$geoloc = [
		'continent_name' => '',
		'country_name' => '',
		'region_name' => '',
		'city' => '',
		'latitude' => '',
		'longitude' => '',
		'country_flag' => '',
		'isp' => '',
		'connection_type' => '',
		'organization' => '',
	];
}

// Write Browser Data if turned on
if (BROWSER_DATA) {
	$_SESSION['browser']['name'] = $browser->getName();
	$_SESSION['browser']['version'] = $browser->getVersion();
	$_SESSION['browser']['platform'] = $browser->getPlatformVersion();
	$_SESSION['browser']['is64bit'] = $browser->is64bitPlatform();
	$_SESSION['browser']['isRobot'] = $browser->isRobot();
	$_SESSION['browser']['isMobile'] = $browser->isMobile();
	$_SESSION['browser']['ipAddress'] = $ip_address;
    $_SESSION['browser']['rootURL'] = $_SERVER['SERVER_NAME']; // <-- There are two places this link can come from
} else {
	$_SESSION['browser']['name'] = '';
	$_SESSION['browser']['version'] = '';
	$_SESSION['browser']['platform'] = '';
	$_SESSION['browser']['is64bit'] = 0;
	$_SESSION['browser']['isRobot'] = 0;
	$_SESSION['browser']['isMobile'] = 0;
	$_SESSION['browser']['ipAddress'] = $ip_address;
	$_SESSION['browser']['rootURL'] = $_SERVER['SERVER_NAME']; // <-- There are two places this link can come from
}

// If the geolocation session does not already exist, create it
if (!isset($_SESSION['geolocation'])) {
	$_SESSION['geolocation']['continent']  = $geoloc['continent_name'];
	$_SESSION['geolocation']['country']  = $geoloc['country_name'];
	$_SESSION['geolocation']['region']  = $geoloc['region_name'];
	$_SESSION['geolocation']['city']  = $geoloc['city'];
	$_SESSION['geolocation']['latitude']  = $geoloc['latitude'];
	$_SESSION['geolocation']['longitude']  = $geoloc['longitude'];
	$_SESSION['geolocation']['flag']  = $geoloc['country_flag'];
	$_SESSION['geolocation']['isp']  = $geoloc['isp'];
	$_SESSION['geolocation']['connection']  = $geoloc['connection_type'];
	$_SESSION['geolocation']['organization']  = $geoloc['organization'];
}


// If Geolocation is on
if ($useGeolocation) {
	$hitdata['geoloc'] = $geoloc;
	$hitdata['server'] = $_SESSION['browser'];

	// if Geofencing is turned on
	if (GEOFENCING) {
		// Geofencing does not hide the website, only hide the out of area hits from the main data log
		$gf_continent = $geoloc['continent_name'];
		$gf_country = $geoloc['country_name'];
		if ($gf_country !== 'United Kingdom') { // Open to UK ONLY
			//if ($gf_continent != 'Europe' && $gf_continent != "North America") { // Open to EU and US/Canada
			$geo = new geofencing($hitdata);
			$useDatalogger = false;
		}
	}

	//If Anonymous Browsing is not allowed
	if (!ANONYMOUSBROWSING) {
		// Again, log anonymous browsing in a separate area
		if (!$_SESSION['browser']['isRobot'] || $geoloc['isp'] != 'Krystal Hosting Limited') {
			if($_SESSION['browser']['name'] == 'unknown' || $_SESSION['browser']['platform']== 'unknown') {
				$geo = new geofencing($hitdata);
				$useDatalogger = false;
			}
		}
	}

	// calculate distance from home - only works for geolocation data on
	if (strlen(geoloc['continent_name']) >1) {
		// Distance
		$lon1 = $geoloc['longitude'];
		$lat1 = $geoloc['latitude'];
		$lon2 = -0.05496;
		$lat2 = 53.57159;


		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$miles = number_format($miles, 0, '.', '');
		$_SESSION['geolocation']['distance'] = $miles;
	} else {
		$_SESSION['geolocation']['distance'] = 0;
	}


}

/**
 * Cookies are disabled at the moment until I can work out what I want to do with them
 */

//if (isset($_COOKIE[COOKIE_UNIQUE_NAME]) || isset($_SESSION['hitcounter']['unique_visitor'])) {
	// A set cookie can also be a unique visitor, so nothing goes here yet
//	datalogger::updateSession($session_id);
	// can use $validated = datalogger::validateSession($session_id) to see if session has been logged and has not expired as a security check
//} else {
	// No unique value cookie
//	$_SESSION['hitcounter']['unique_visitor'] = true;
//	datalogger::newSession($session_id);
//	setcookie(COOKIE_UNIQUE_NAME, $session_id, time()+ (int)COOKIE_UNIQUE_EXPIRE);
//}

// Write the data log.
if ($useDatalogger) {
	datalogger::datalog("PAGE", 1000, "PAGE LOAD");
}
