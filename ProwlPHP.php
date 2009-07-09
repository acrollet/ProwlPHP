<?php

class Prowl
{
	private $_version = '0.2';
	private $_obj_curl = null;
	private $_verified = false;
	private $_error_code = null;

	private $_api_key = null;
	private $_api_domain = 'https://prowl.weks.net/publicapi';
	private $_url_verify = '/verify?apikey=%s';
	private $_url_push = '/add';
	
	private $_params = array(		// Accessible params [key => maxsize]
		'apikey' => 40,			// User API Key.
		'priority' => 2,		// Range from -2 to 2.
		'application' => 254,		// Name of the app.
		'event' => 1024,		// Name of the event.
		'description' => 10000,		// Description of the event.
	);
	
	public function __construct($apikey)
	{
		$this->_api_key = $apikey;
		
		$url = sprintf($this->_url_verify, $apikey);
		$return = $this->_execute($url);
		
		if($return===false)
		{
			$this->_error_code=500;
			return false;
		}
		
		$resp = new SimpleXMLElement($return);
		$this->_verified = $this->_response($resp);
	}
	
	public function push($params, $is_post=false)
	{
		if(!$this->_verified)
			return 'Auth Failed';
		
		if(!$is_post)
		{
			$url = $is_post ? $this->_url_push : $this->_url_push . '?';
			$post_params = '';
		}
		
		$params = func_get_args();
		$params[0]['apikey'] = $this->_api_key;

		foreach($params[0] as $k => $v)
		{
			if(!isset($this->_params[$k]))
			{
				$this->_error_code = 400;
				return false;
			}
			if(strlen($v) > $this->_params[$k])
			{
				$this->_error_code = 10001;
				return false;
			}
			if(!$is_post)
			{
				$url .= $k . '=' . urlencode($v) . '&';
			}
				else
			{
				$post_params .= $k . '=' . urlencode($v) . '&';
			}
		}
		
		if(!$is_post)
		{
			$url = substr($url, 0, strlen($url)-1);
		}
			else
		{
			$params = substr($post_params, 0, strlen($post_params)-1);
		}
		
		$return = $this->_execute($url, $is_post ? true : false, $params);	
		$resp = new SimpleXMLElement($return);
		return $this->_response($resp);	
	}
	
	public function getError()
	{
		switch($this->_error_code)
		{
			case 200: 	return 'Pushed Successfully';	break;
			case 400:	return 'Bad request, the parameters you provided did not validate.';	break;
			case 401: 	return 'The API key given is not valid, and does not correspond to a user.';	break;
			case 405:	return 'Method not allowed, you attempted to use a non-SSL connection to Prowl.';	break;
			case 500:	return 'Internal server error, something failed to execute properly on the Prowl side.';	break;
			case 10001:	return 'Parameter value exceeds the maximum byte size.';	break;
			default:	return false;	break;
		}
	}
	
	private function _execute($url, $is_post=false, $params=null)
	{
		$this->_obj_curl = curl_init($this->_api_domain . $url);
		curl_setopt($this->_obj_curl, CURLOPT_HEADER, 0);
		curl_setopt($this->_obj_curl, CURLOPT_USERAGENT, "ProwlPHP/" . $this->_version);
		curl_setopt($this->_obj_curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($this->_obj_curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->_obj_curl, CURLOPT_RETURNTRANSFER, 1);
		
		if($is_post)
		{
			curl_setopt($this->_obj_curl, CURLOPT_POST, 1);
			curl_setopt($this->_obj_curl, CURLOPT_POSTFIELDS, $params);
		}
		
		$return = curl_exec($this->_obj_curl);
		curl_close($this->_obj_curl);
		return $return;
	}
	
	private function _response($response)
	{
		if(isset($response->success))
		{
			$code = $response->success['code'];
		}
			else
		{
			$code = $response->error['code'];
		}
		$this->_error_code = $code;
		
		switch($code)
		{
			case 200: 	return true;	break;
			default:	return false;	break;
		}
	}
}

?>