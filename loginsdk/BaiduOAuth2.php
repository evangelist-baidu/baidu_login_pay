<?php

/***************************************************************************
 *
 * Copyright (c) 2012 Baidu.com, Inc. All Rights Reserved
 *
 **************************************************************************/

require_once(dirname(__FILE__) . '/BaiduUtils.php');

/**
 * Client for Baidu OAuth2.0 service.
 * 
 * @package Baidu
 * @author zhujianting(zhujianting@baidu.com)
 * @version v2.0.0
 */
class BaiduOAuth2
{	
    /**
     * Endpoints for Baidu OAuth2.0.
     */
    public static $BD_OAUTH2_ENDPOINTS = array(
    	'authorize'	=> 'https://openapi.baidu.com/oauth/2.0/authorize',
    	'token'		=> 'https://openapi.baidu.com/oauth/2.0/token',
    );
  
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $display = 'mobile';
    protected $scope = 'basic';
    
    
    /**
     * Constructor
     * 
     * @param string $clientId Client_id of the baidu thirdparty app or access_key of the developer.
     * @param string $clientSecret Client_secret of the baidu thirdparty app or secret_key of the developer.
     */
    public function __construct($clientId, $clientSecret)
    {
    	$this->clientId = $clientId;
    	$this->clientSecret = $clientSecret;
    }
    
    /**
     * Set the redirect uri for the app.
     * 
     * @param $redirectUri Where to redirect after user authorization.
     * @return BaiduOAuth2
     */
    public function setRedirectUri($redirectUri)
    {
    	$this->redirectUri = $redirectUri;
    	return $this;
    }
    
    /**
     * Get the redirect uri for the app.
     * 
     * @return string
     */
    public function getRedirectUri()
    {
    	return $this->redirectUri;
    }
    
    /**
     * Set display parameter for oauth2 authorize page.
     * 
     * @param $display Authorization page style, 'page', 'popup', 'touch' or 'mobile'
     * @return BaiduOAuth2
     */
    public function setDisplay($display)
    {
    	$this->display = $display;
    	return $this;
    }
    
    /**
     * Set scope parameter for oauth2 authorize page.
     * 
     * @param $display value for 'scope' parameter of oauth2 authorize page,
     * blank space separated list of requested extended perms
     * @return BaiduOAuth2
     */
    public function setScope($scope)
    {
    	$this->scope = $scope;
    	return $this;
    }
    
    /**
     * Get url for baidu oauth2's authorize page.
     * 
     * @param $state	value for 'state' parameter
     * @param $loginType value for 'login_type' parameter
     * @param $mobile
     * @param $forceLogin
     * @param $confirmLogin
     */
	public function getAuthorizeUrl($state, $loginType, $mobile, $forceLogin, $confirmLogin)
	{		
		$params = array(
			'client_id'		=> $this->clientId,
			'response_type'	=> 'code',
			'redirect_uri'	=> $this->redirectUri,
			'scope'			=> $this->scope,
			'display'		=> $this->display,
			'state'			=> $state,
			'login_type'	=> $loginType,
			'mobile'		=> $mobile,
			'force_login'	=> $forceLogin,
			'confirm_login'	=> $confirmLogin,
		);
		return self::$BD_OAUTH2_ENDPOINTS['authorize'] . '?' . http_build_query($params, '', '&');
	}
	
	/**
	 * Get access token ifno by authorization code.
	 * 
	 * @param string $code	Authorization code
	 * @return array|false returns access token info if success, or false if failed
	 */
	public function getAccessTokenByAuthorizationCode($code)
	{
		$params = array(
			'grant_type'	=> 'authorization_code',
			'code'			=> $code,
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'redirect_uri'	=> $this->redirectUri,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Get access token info by client credentials.
	 * 
	 * @param string $scope		Extend permissions delimited by blank space
	 * @return array|false returns access token info if success, or false if failed.
	 */
	public function getAccessTokenByClientCredentials($scope = '')
	{
		$params = array(
			'grant_type'	=> 'client_credentials',
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'scope'			=> $scope,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Refresh access token by refresh token.
	 * 
	 * @param string $refreshToken The refresh token
	 * @param string $scope	Extend permissions delimited by blank space
	 * @return array|false returns access token info if success, or false if failed.
	 */
	public function getAccessTokenByRefreshToken($refreshToken, $scope = '')
	{
		$params = array(
			'grant_type'	=> 'refresh_token',
			'refresh_token'	=> $refreshToken,
			'client_id'		=> $this->clientId,
			'client_secret'	=> $this->clientSecret,
			'scope'			=> $scope,
		);
		return $this->makeAccessTokenRequest($params);
	}
	
	/**
	 * Make an oauth access token request
	 * 
	 * The parameters:
	 * - client_id: The client identifier, just use api key
	 * - response_type: 'token' or 'code'
	 * - redirect_uri: the url to go to after a successful login
	 * - scope: The scope of the access request expressed as a list of space-delimited, case sensitive strings.
	 * - state: An opaque value used by the client to maintain state between the request and callback.
	 * - display: login page style, 'page', 'popup', 'touch' or 'mobile'
	 * 
	 * @param array $params	oauth request parameters
	 * @return mixed returns access token info if success, or false if failed
	 */
	public function makeAccessTokenRequest($params)
	{
		$result = BaiduUtils::request(self::$BD_OAUTH2_ENDPOINTS['token'], $params, 'POST');
		if ($result) {
			$result = json_decode($result, true);
			if (isset($result['error_description'])) {
				BaiduUtils::setError($result['error'], $result['error_description']);
				return false;
			}
			return $result;
		}
		
		return false;
	}
}

 
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */