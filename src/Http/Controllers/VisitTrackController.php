<?php

namespace Qatras\Surfdata\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Qatras\Surfdata\Models\VisitTrack;





/**
 * The visitorTracking class
 *
 * This PHP class gathers detailed visitor information,
 * and stores the visit in a database.
 */

class VisitTrackController extends Controller
{
	/**
	 * Stores thisVisit array
	 */
	var $visit = null;

	/**
	 * The constructor method
	 *
	 * This method calls the track method, which get the data and
	 * insert it in the database table
	 */
	public function __construct()
	{
		//Call the track method
		$this->track();
	}


	/**
	 * This method redirects to the displaytrackdata page
	 *
	 * @return View
	 */

	public function display()
	{
		return view('surfdata::displaytrackdata');
	}


   	/**
	 * Tracking Method
	 *
	 * This is the main tracking method. It gathers all of the
	 * other methods in the class in to an array, and then inserts
	 * the array in to the database. If a connection to the database
	 * cannot be established, an error is shown to the user.
	 *
	 * @access protected
	 */
	protected function track()
	{
		//TODO: rewrite geoCheckIP function, consolidate variables with array.

		//Prepare variables
		$visitor_ip 		= $this->getIP();
		$ip_location		= $this->geoCheckIPAPP($this->getIP());
		if ($ip_location) {
			$visitor_country	= $ip_location['countryCode'];
			$visitor_region		= $ip_location['region'];
			$visitor_city		= $ip_location['city'];
			$visitor_zip		= $ip_location['zip'];
			$visitor_lat		= $ip_location['lat'];
			$visitor_lon		= $ip_location['lon'];
			$visitor_timezone	= $ip_location['timezone'];
			$visitor_isp		= $ip_location['isp'];
			$visitor_org		= $ip_location['org'];
			$visitor_as			= $ip_location['as'];
		}
		$browser = $this->getBrowser();
		$visitor_userAgent			= $browser['userAgent'];
		$visitor_userAgentPattern	= $browser['pattern'];
		$visitor_browser			= $browser['browser'];
		$visitor_browserVersion		= $browser['browserVersion'];
		$platform = $this->getPlatform();
		$visitor_platform			= $platform['platform'];
		$visitor_platformVersion	= $platform['platformVersion'];
		$visitor_date				= $this->getDate("Y-m-d h:i:sA");
		$visitor_day		= $this->getDate("d");
		$visitor_month		= $this->getDate("m");
		$visitor_year		= $this->getDate("Y");
		$visitor_hour		= $this->getDate("h");
		$visitor_minute		= $this->getDate("i");
		$visitor_seconds	= $this->getDate("s");
		$visitor_referer	= $this->getReferer();
		$visitor_page		= $this->getRequestURI();
		$visitor_closed		= null;

		//Gather variables in array
		$visitor = array(
			'visitor_ip' 				=> $visitor_ip,
			'visitor_country' 			=> $visitor_country,
			'visitor_region' 			=> $visitor_region,
			'visitor_city' 				=> $visitor_city,
			'visitor_zip' 				=> $visitor_zip,
			'visitor_latitude' 			=> $visitor_lat,
			'visitor_longitude' 		=> $visitor_lon,
			'visitor_timezone' 			=> $visitor_timezone,
			'visitor_isp'	 			=> $visitor_isp,
			'visitor_org'	 			=> $visitor_org,
			'visitor_as'	 			=> $visitor_as,
			'visitor_userAgent' 		=> $visitor_userAgent,
			'visitor_userAgentPattern'	=> $visitor_userAgentPattern,
			'visitor_browser' 			=> $visitor_browser,
			'visitor_browserVersion' 	=> $visitor_browserVersion,
			'visitor_platform' 			=> $visitor_platform,
			'visitor_platformVersion'	=> $visitor_platformVersion,
			'visitor_date' 				=> $visitor_date,
			'visitor_day' 				=> $visitor_day,
			'visitor_month' 			=> $visitor_month,
			'visitor_year' 				=> $visitor_year,
			'visitor_hour' 				=> $visitor_hour,
			'visitor_minute' 			=> $visitor_minute,
			'visitor_seconds' 			=> $visitor_seconds,
			'visitor_referer' 			=> $visitor_referer,
			'visitor_page' 				=> $visitor_page,
			'visitor_closed' 			=> $visitor_closed
		);

		//Prepare variables for inserting in database table
		$visit = new VisitTrack();
		$visit->visitor_ip 				= $visitor_ip;
		$visit->visitor_country			= $visitor_country;
		$visit->visitor_region			= $visitor_region;
		$visit->visitor_city			= $visitor_city;
		$visit->visitor_zip				= $visitor_zip;
		$visit->visitor_latitude		= $visitor_lat;
		$visit->visitor_longitude		= $visitor_lon;
		$visit->visitor_timezone		= $visitor_timezone;
		$visit->visitor_isp				= $visitor_isp;
		$visit->visitor_org				= $visitor_org;
		$visit->visitor_as				= $visitor_as;
		$visit->visitor_userAgent		= $visitor_userAgent;
		$visit->visitor_userAgentPattern= $visitor_userAgentPattern;
		$visit->visitor_browser			= $visitor_browser;
		$visit->visitor_browserVersion	= $visitor_browserVersion;
		$visit->visitor_platform		= $visitor_platform;
		$visit->visitor_platformVersion	= $visitor_platformVersion;
		$visit->visitor_date			= $visitor_date;
		$visit->visitor_day				= $visitor_day;
		$visit->visitor_month			= $visitor_month;
		$visit->visitor_year			= $visitor_year;
		$visit->visitor_hour			= $visitor_hour;
		$visit->visitor_minute			= $visitor_minute;
		$visit->visitor_seconds			= $visitor_seconds;
		$visit->visitor_referer			= $visitor_referer;
		$visit->visitor_page			= $visitor_page;
		$visit->visitor_closed			= $visitor_closed;
		if ($visit->save()) {
			$visitor['id'] = $visit->id;
			$this->visit = $visitor;
			return true;
		}
		else {
			return false;
		}

	}


	/**
	 * Get visitor IP address
	 *
	 * This method tests rigorously for the current users IP address
	 * It tests the $_SERVER vars to find IP addresses even if they
	 * are being proxied, forwarded, or otherwise obscured.
	 *
	 * @param boolean $getHostByAddr the IP address with hostname
	 * @return string $ip the formatted IP address
	 */
	private function getIP()
	{
		// Get real visitor IP behind CloudFlare network
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if (filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}

	/**
	 * Geo-locate visitor IP address
	 *
	 * This method accepts an IP address. It then tests the address
	 * for validity, connects with Guzzle to Google Api, and queries on ip address
	 * The method then returns an array of the geolocation information.
	 *
	 * @param string $ip the IPv4 address to lookup on netip.de
	 * @return array geolocation data: country, region, city, ...
	 */
	private function geoCheckIPGoogle($ip)
	{
		//check, if the provided ip is valid
		if (!filter_var($ip, FILTER_VALIDATE_IP) || $ip == 'localhost') {
			//throw new InvalidArgumentException("IP is not valid");
			return false;
		}

		$address = '';
		$client = new Client();
		//Google Geolocation
		$params = [ 'form_params' => ['key' => 'AIzaSyAkiVjlr_3eCiw2l6d1kpL-tlC9N6EMLVo' ] ];
		$result =(string) $client->post('https://maps.googleapis.com/maps/api/geocode/json?address='.$address, $params   )->getBody();

		$data = [];
		return $data;
	}


	/**
	 * Geo-locate visitor IP address
	 *
	 * This method accepts an IP address. It then tests the address
	 * for validity, connects with Guzzle to ip-api.com geo server, and queries on ip address
	 * The method then returns an array of the geolocation information.
	 *
	 * @param string $ip the IPv4 address to lookup on netip.de
	 * @return array geolocation data: country, region, city, ...
	 */
	private function geoCheckIPAPP($ip)
	{

		//check, if the provided ip is valid
		if(!filter_var($ip, FILTER_VALIDATE_IP) || $ip == 'localhost')
		{
			//throw new InvalidArgumentException("IP is not valid");
			return false;
		}

		//connecy to ip-server
		$client = new Client();
		$result =(string) $client->post('http://ip-api.com/json/212.102.45.123')->getBody();
		$data = json_decode($result, true);
		
		if ( $data['status']<>'success' ) {
			//throw new InvalidArgumentException("Error contacting Geo-IP-Server");
			return false;
		}
		return $data;

	}


	/**
	 * Get country flag
	 *
	 * This method accepts a 2-charcter, lowercase, country code.
	 * It then matches the code to the corresponding image file
	 * in the includes/famfamfam-countryflags directory. Finally,
	 * it returns a complete HTML IMG tag.
	 *
	 * @param string $countryCode the two character country code from geoCheckIP
	 * @return string $flag the finished img tag containing country flag
	 */
	private function getFlag($countryCode)
	{
		$flag = '<img src="src/assets/famfamfam-countryflags/' . strtolower($countryCode) . '.gif" height="15px" width="25px"/>';
		return $flag;
	}


	/**
	 * Get the visitor browser-type and platform
	 *
	 * This method tests the $_SERVER vars for an HTTP_USER_AGENT entry.
	 * Through a series of if statements, preg_match, and regex patterns,
	 * a browser-type will be returned. If a browser-type is unable to be
	 * determined 'other' will be used in it's place.
	 *
	 * @return array|null $userAgent, $name, $version, $platform, $pattern
	 */
	private function getBrowser()
	{

		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$version= "";

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}elseif(preg_match('/Firefox/i',$u_agent)){
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}elseif(preg_match('/OPR/i',$u_agent)){
			$bname = 'Opera';
			$ub = "Opera";
		}elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
			$bname = 'Apple Safari';
			$ub = "Safari";
		}elseif(preg_match('/Netscape/i',$u_agent)){
			$bname = 'Netscape';
			$ub = "Netscape";
		}elseif(preg_match('/Edge/i',$u_agent)){
			$bname = 'Edge';
			$ub = "Edge";
		}elseif(preg_match('/Trident/i',$u_agent)){
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}else {
				$version= $matches['version'][1];
			}
		}else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent'			=> $u_agent,
			'pattern'    		=> $pattern,
			'browser'  			=> $bname,
			'browserVersion'	=> $version
			
		);

	}

	
	/**
	 * Get the visitor browser-type and platform
	 *
	 * This method tests the $_SERVER vars for an HTTP_USER_AGENT entry.
	 * Through a series of if statements, preg_match, and regex patterns,
	 * a browser-type will be returned. If a browser-type is unable to be
	 * determined 'other' will be used in it's place.
	 *
	 * @return array|null $userAgent, $name, $version, $platform, $pattern
	 */
	private function getPlatform()
	{

		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$platform = 'Unknown';
		$platformVersion = "";

		//First get the platform
		$os_array	=	array(
			'/windows NT 10.0/i'	=>  ['Windows', '10.0'],
			'/windows nt 6.3/i'     =>  ['Windows', '8.1'],
			'/windows nt 6.2/i'     =>  ['Windows', '8'],
			'/windows nt 6.1/i'     =>  ['Windows', '7'],
			'/windows nt 6.0/i'     =>  ['Windows', 'Vista'],
			'/windows nt 5.2/i'     =>  ['Windows', 'Server 2003/XP x64'],
			'/windows nt 5.1/i'     =>  ['Windows', 'XP'],
			'/windows xp/i'         =>  ['Windows', 'XP'],
			'/windows nt 5.0/i'     =>  ['Windows', '2000'],
			'/windows me/i'         =>  ['Windows', 'ME'],
			'/win98/i'              =>  ['Windows', '98'],
			'/win95/i'              =>  ['Windows', '95'],
			'/win16/i'              =>  ['Windows', '3.11'],
			'/macintosh|mac os x/i' =>  ['Mac', 'OS X'],
			'/mac_powerpc/i'        =>  ['Mac', 'OS 9'],
			'/linux/i'              =>  ['Linux', ''],
			'/ubuntu/i'             =>  ['Ubuntu', ''],
			'/iphone/i'             =>  ['iPhone', ''],
			'/ipod/i'               =>  ['iPod', ''],
			'/ipad/i'               =>  ['iPad', ''],
			'/android/i'            =>  ['Android', ''],
			'/blackberry/i'         =>  ['BlackBerry', ''],
			'/webos/i'              =>  ['Mobile', '']
		);

		foreach ($os_array as $regex => $value) {
			if (preg_match($regex, $u_agent)) {
				$platform = $value[0];
				$platformVersion = $value[1];
			}
		}

		return array(
			'platform' 			=> $platform,
			'platformVersion'	=> $platformVersion,
		);
	}


	/**
	 * Get the date
	 *
	 * This method accepts a PHP gmdate() value. It is Identical to the date()
	 * function except that the time returned is Greenwich Mean Time (GMT).
	 * This is used to prevent timezone errors and inconsistencies.
	 *
	 * @param string $i the requested gmdate() character
	 * @return string $date the formatted gmdate date
	 */
	private function getDate($i)
	{

		//get the requested date
		$date = gmdate($i);

		//return the date
		return $date;

	}

	/**
	 * Get the referring page
	 *
	 * This method tests the $_SERVER vars for an HTTP_REFERER entry.
	 * If a referer is present, it will be returned. Otherwise, null
	 * will the the response.
	 *
	 * @param null
	 * @return string|null $ref the path to the refering page
	 */
	private function getReferer()
	{

		if ( ! empty( $_SERVER['HTTP_REFERER'] ) )
		{
			$ref = $_SERVER['HTTP_REFERER'];

			return $ref;
		}

		return false;

	}

	/**
	 * Get the requested page
	 *
	 * This method tests the $_SERVER vars for an REQUEST_URI entry.
	 * If the requested page is recorded by the server, it will be
	 * retuened. Otherwise, null will be the response.
	 *
	 * @param null
	 * @return string|null $uri the path to the requested page
	 */
	private function getRequestURI() {

		if ( ! empty( $_SERVER['REQUEST_URI'] ) )
		{
			$uri = $_SERVER['REQUEST_URI'];

			return $uri;
	 	}

	 	return false;

	}

	/**
	* Return the current visit array
	*
	* This method simply returns the compiled visitor information
	* in an array. You can use this to capture the current visit data
	* and display it, or use it for another purpose in your application.
	*
	* @param null
	* @return array $this->thisVisit() the compiled visitor information
	*/
	public function getVisit()
	{

		return($this->visit);

	}

	/**
	 * Display the current visit array
	 *
	 * This method is identical to the getThisVisit method. The key
	 * difference is that this method is already wrapped in a print_r
	 * statement. This is used in the class examples.
	 *
	 * @param null
	 * @return array $this->thisVisit() the formatted and compiled visitor information
	 */
	public function displayVisit()
	{

		print_r($this->visit);

	}

	/**
	 * Display Visitors
	 *
	 * This method queries the database for all entries in the visitors table,
	 * it then paginates the results, and outputs a unstyled HTML table. This
	 * method is used in the class examples.
	 *
	 * @param null
	 * @return array $this->displayVisitors() the html output from the database
	 */
	public function displayVisitors()
	{

		/**
		 * Retrieving a single row of data
		 */
		$query = $this->link->query("SELECT COUNT(*) AS `count` FROM `visitors`");
		if( $query->num_rows > 0 )
		{
			list( $numrows ) = $query->fetch_row();

			// number of rows to show per page
			$rowsperpage = 10;

			// find out total pages
			$totalpages = ceil($numrows / $rowsperpage);

			// get the current page or set a default
			if (isset($_GET['paginate']) && is_numeric($_GET['paginate']))
			{
			   // cast var as int
			   $paginate = (int) $_GET['paginate'];
			}
			else
			{
			   // default page num
			   $paginate = 1;
			}

			// if current page is greater than total pages...
			if ($paginate > $totalpages)
			{
			   // set current page to last page
			   $paginate = $totalpages;
			} // end if
			// if current page is less than first page...
			if ($paginate < 1)
			{
			   // set current page to first page
			   $paginate = 1;
			} // end if

			// the offset of the list, based on current page
			$offset = ($paginate - 1) * $rowsperpage;

		}

		echo '
		<table id="visitor" class="table">
			<thead>
				<th>IP Address</th>
				<th>Browser</th>
				<th>OS</th>
				<th>City</th>
				<th>State</th>
				<th>Country</th>
				<th>Date</th>
				<th>Page</th>
				<th>Referer</th>
			</thead>
			<tbody>
		';

		$results = $this->link->query( "SELECT * FROM `visitors` ORDER BY `id` DESC LIMIT $offset, $rowsperpage" );

		if( $this->link->error )
		{
			return false;
		}
		else
		{
			$row = array();
			while( $r = $results->fetch_assoc() )
			{
				echo
				'
				<tr>
					<td width="20%">' . $r['visitor_ip'] . '</td>
					<td width="10%">' . $r['visitor_browser'] . '</td>
					<td width="10%">' . $r['visitor_OS'] . '</td>
					<td width="10%">' . $r['visitor_city'] . '</td>
					<td width="10%">' . $r['visitor_state'] . '</td>
					<td width="10%">' . $r['visitor_flag'] . ' ' . $r['visitor_country'] . '</td>
					<td width="10%">' . $r['visitor_date'] . '</td>
					<td width="10%">' . $r['visitor_page'] . '</td>
					<td width="10%">' . $r['visitor_referer'] . '</td>
				</tr>
				';
			}
		}

		echo'

			</tbody>
		</table>

		<br>
		';

		echo'
		<div class="pagination" style="display:table;">
		';

			/******  build the pagination links ******/
			// range of num links to show
			$range = 3;

			// if not on page 1, don't show back links
			if ($paginate > 1)
			{
			   // show << link to go back to page 1
			   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=1'> First </a>";
			   // get previous page num
			   $prevpage = $paginate - 1;
			   // show < link to go back to 1 page
			   echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$prevpage'> < </a>";
			} // end if

			// loop to show links to range of pages around current page
			for ($x = ($paginate - $range); $x < (($paginate + $range) + 1); $x++)
			{
				// if it's a valid page number...
				if (($x > 0) && ($x <= $totalpages))
				{
					// if we're on current page...
					if ($x == $paginate) {
						// 'highlight' it but don't make a link
						echo "<a>$x</a>";
					// if not current page...
					}
					else {
					 // make it a link
					 echo "<a href='{$_SERVER['PHP_SELF']}?&paginate=$x'>$x</a>";
					} // end else
				} // end if
			} // end for

			// if not on last page, show forward and last page links
			if ($paginate != $totalpages)
			{
				// get next page
				$nextpage = $paginate + 1;
				// echo forward link for next page
				echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$nextpage'> > </a>";
				// echo forward link for lastpage
				echo "<a href='{$_SERVER['PHP_SELF']}?paginate=$totalpages'> Last </a>";
			} // end if
			/****** end build pagination links ******/

		echo'
		</div>
		';
	}

}
