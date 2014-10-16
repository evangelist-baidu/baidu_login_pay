<?php

/***************************************************************************
 *
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 *
 **************************************************************************/


/**
 * Utils class for Baidu OpenAPI2.0 SDK.
 * 
 * @package Baidu
 * @author zhujianting(zhujianting@baidu.com)
 * @version v2.0.0
 */
class BaiduUtils
{
	private static $errno = 0;
    private static $errmsg = '';
    private static $isDebug = false;
    
    private static $boundary = '';
    private static $fileinfoDb;
    
    /**
     * Set the gloable error number and error message.
     * 
     * @param int $errno Error code
     * @param string $errmsg Error message
     * @return void
     */
    public static function setError($errno, $errmsg)
    {
    	self::$errno = $errno;
    	self::$errmsg = $errmsg;
    	self::errorLog($errmsg);
    }
    
    /**
     * Get the gloable errno.
     * 
     * @return int
     */
    public static function errno()
    {
    	return self::$errno;
    }
    
    /**
     * Get the gloable error message.
     * 
     * @return string
     */
    public static function errmsg()
    {
    	return self::$errmsg;
    }
    
    /**
     * Whether to set the debug mode of the Baidu OpenAPI SDK or not.
     * 
     * @param bool $on true or false
     * @return void
     */
    public static function setDebugMode($on = true)
    {
    	self::$isDebug = $on;
    }
    
    /**
     * Whether the debug mode of the Baidu OpenAPI SDK is on or off.
     * 
     * @return bool
     */
    public static function isDebugMode()
    {
    	return self::$isDebug;
    }
    
	/**
     * Request for a http/https resource
     * 
     * @param string $url Url to request
     * @param array $params Parameters for the request
     * @param string $httpMethod Http method, 'GET' or 'POST'
     * @param bool $multi Whether it's a multipart POST request
     * @return string|false Returns string if success, or false if failed
     */
	public static function request($url, $params = array(), $httpMethod = 'GET', $multi = false)
    {
    	$ch = curl_init();
  		
    	$curl_opts = array(
			CURLOPT_CONNECTTIMEOUT	=> 3,
			CURLOPT_TIMEOUT			=> 5,
			CURLOPT_USERAGENT		=> 'lightapp-sdk-php-1.0',
	    	CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1,
	    	CURLOPT_RETURNTRANSFER	=> true,
	    	CURLOPT_HEADER			=> false,
	    	CURLOPT_FOLLOWLOCATION	=> false,
		);

		if (stripos($url, 'https://') === 0) {
			$curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
		}
		
		if (strtoupper($httpMethod) === 'GET') {
			$query = http_build_query($params, '', '&');
			$delimiter = strpos($url, '?') === false ? '?' : '&';
    		$curl_opts[CURLOPT_URL] = $url . $delimiter . $query;
    		$curl_opts[CURLOPT_POST] = false;
		} else {
			$headers = array();
			if ($multi && is_array($params) && !empty($params)) {
				$body = self::buildHttpMultipartBody($params);
				$headers[] = 'Content-Type: multipart/form-data; boundary=' . self::$boundary;
			} else {
				$body = http_build_query($params, '', '&');
			}
			$curl_opts[CURLOPT_URL] = $url;
    		$curl_opts[CURLOPT_POSTFIELDS] = $body;
    		$curl_opts[CURLOPT_HTTPHEADER] = $headers;
		}
    
    	curl_setopt_array($ch, $curl_opts);
        $result = curl_exec($ch);
        
    	if ($result === false) {
    		self::setError(curl_errno($ch), curl_error($ch));
            curl_close($ch);
            return false;
    	} elseif (empty($result)) {
    		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    		if ($http_code != 200) {
    			self::setError($http_code, 'http response status code: ' . $http_code);
    			curl_close($ch);
    			return false;
    		}
    	}
        
    	curl_close($ch);
    	
    	return $result;
    }
    
	/**
	 * Prints to the error log if you aren't in command line mode.
	 *
	 * @param String log message
	 */
	public static function errorLog($msg)
	{
		// disable error log if we are running in a CLI environment
		if (php_sapi_name() != 'cli') {
			error_log($msg);
		}
		
		// Set the debug mode if you want to see the errors on the page
		if (self::$isDebug) {
			echo 'error_log: '.$msg."\n";
		}
	}
	
	public static function decrypt($ciphertext, $secret)
	{
		$ciphertext = base64_decode($ciphertext);
		if (empty($ciphertext)) {
			return false;
		}
		$md5_v = md5($secret);
		//Open the cipher
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', '');
		//Create key and IV，不满16 字符后面补\0
		$key = substr($md5_v, 0, 16);
		$iv = strrev(substr($md5_v, 0, 16));
		//Intialize encryption
		mcrypt_generic_init($td, $key, $iv);
		//Encrypt data
		$plaintext = mdecrypt_generic($td, $ciphertext);
		//Terminate encryption handler and close module
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		
		return trim($plaintext);
	}
	
	/**
	 * Generate the signature for passed parameters.
	 * 
	 * @param array $params Array of parameters to be signatured
	 * @param string $secret Secret key for signature
	 * @param string $namespace The parameter which will be excluded when calculate the signature
	 * @return string Signature of the parameters
	 */
	public static function generateSign($params, $secret, $namespace = 'sign')
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
        	if ($k != $namespace) {
        		$str .= "$k=$v";
        	}
        }
        $str .= $secret;
        return md5($str);
    }
    
    /**
     * Build the multipart body for file uploaded request.
     * @param array $params Parameters for the request
     * @return string
     */
    private static function buildHttpMultipartBody($params)
    {
    	$body = '';
		$pairs = array();
		self::$boundary = $boundary = md5('lightapp-sdk-php-1.0' . microtime(true));
		
		foreach ($params as $key => $value) {
			if ($value{0} == '@') {
				$url = ltrim($value, '@');
				$content = file_get_contents($url);
				$array = explode('?', basename($url));
				$filename = $array[0];

				$body .= '--' . $boundary . "\r\n";
				$body .= 'Content-Disposition: form-data; name="' . $key . '"; filename="' . $filename . '"'. "\r\n";
				$body .= 'Content-Type: ' . self::detectMimeType($url) . "\r\n\r\n";
				$body .= $content . "\r\n";
			} else {
				$body .= '--' . $boundary  . "\r\n";
				$body .= 'Content-Disposition: form-data; name="' . $key . "\"\r\n\r\n";
				$body .= $value . "\r\n";
			}
		}

		$body .= '--' . $boundary . '--';
		return $body;
    }
    
 	/**
    * Tries to detect MIME type of a file
    *
    * The method will try to use fileinfo extension if it is available,
    * deprecated mime_content_type() function in the other case. If neither
    * works, default 'application/octet-stream' MIME type is returned
    *
    * @param    string  filename
    * @return   string  file MIME type
    */
    private static function detectMimeType($filename)
    {
        // finfo extension from PECL available
        if (function_exists('finfo_open')) {
            if (!isset(self::$fileinfoDb)) {
                self::$fileinfoDb = finfo_open(FILEINFO_MIME);
            }
            if (self::$fileinfoDb) {
                $info = finfo_file(self::$fileinfoDb, $filename);
            }
        }
        // (deprecated) mime_content_type function available
        if (empty($info) && function_exists('mime_content_type')) {
            $info = mime_content_type($filename);
        }
        return empty($info)? 'application/octet-stream': $info;
    }
}

 
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */