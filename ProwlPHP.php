<?php

class Prowl {

	private $_version = '0.1';
	private $_obj_curl = null;
	private $_verified = false;

	// PROWL API URIs
	private $_api_key = null;
	private $_api_domain = 'https://prowl.weks.net/publicapi';
	private $_url_verify = '/verify?apikey=%s';
	private $_url_post = '/add?application=%s&event=%s&description=%s&apikey=%s';

	public function __construct($apikey)
	{
		$this->_api_key = $apikey;
		
		// CURL
		$url_verify = sprintf($this->_url_verify, $apikey);
		$return = $this->_execute($url_verify);
		
		// SIMPLEXML - VERIFY REPSONSE
		$sxe = new SimpleXMLElement($return);
		$this->_verified = $this->_response($sxe->success['code']);	
	}
	
	public function post($application=null, $event=null, $description=null)
	{
		if(!$this->_verified)
			return 'Auth Failed';
		
		$application = urlencode($application);
		$event = urlencode($event);
		$description = urlencode($description);
		
		// CURL
		$url_post = sprintf($this->_url_post, $application, $event, $description, $this->_api_key);
		$return = $this->_execute($url_post);
		
		// SIMPLEXML - VERIFY REPSONSE
		$sxe = new SimpleXMLElement($return);
		return $this->_response($sxe->success['code']);	
	}
	
	private function _execute($url)
	{
		$this->_obj_curl = curl_init($this->_api_domain . $url);
		curl_setopt($this->_obj_curl, CURLOPT_HEADER, 0);
        curl_setopt($this->_obj_curl, CURLOPT_USERAGENT, "ProwlPHP/" . $this->_version);
		curl_setopt($this->_obj_curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->_obj_curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_obj_curl, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($this->_obj_curl);
	    curl_close($this->_obj_curl);
	    return $return;
	}
	
	private function _response($code)
	{
		switch($code)
		{
			case 200: 	return true; 
			break;
			case 401: 	return false;
			break;
			default:	return false;
			break;
		}
	}
}

$prowl = new Prowl('294a0303cea3b5e9b13dd99bb080394e1c383c21');
$prowl->post('badger','fish','tree');

?>