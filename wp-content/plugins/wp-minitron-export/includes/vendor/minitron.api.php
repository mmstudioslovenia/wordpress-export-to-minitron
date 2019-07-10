<?php

class mtAPI {
    
    const DEV_URL = 'http://dev.minitron-apps.si/api/';
    const LIVE_URL = 'https://secure.minitron-apps.si/api/';
    
    public function __construct($api_user, $api_hash)
    {
        return $this->mt_api_call('init', array('api_user' => $api_user, 'api_hash' => $api_hash));
	}
    
    public function mt_api_call($action = "", $params = array())
	{	
        //api url
    	$api_url = self::DEV_URL;
    	
    	//session
    	$api_sess = &$_SESSION['mt_api_sess'];
    	
    	//get request params
    	$lnk = "";
    	$lnk .= "&mod=api";
    	if ($action) {$lnk .= "&action=" . $action;}
    	if ($api_sess) {$lnk .= "&sess=" . $api_sess;}
    	$lnk .= "&mode=json";
    	$lnk = substr($lnk, 1);
    	
    	//post request params	
    	$post_request = "";
    	if (is_array($params))
    	{
            foreach(array_keys($params) as $key)
    		{
                $post_request .= "&" . $key . "=" . urlencode($params[$key]);
            }
    	}

    	//prevent cache
    	$api_url .= "?" . $lnk . "&f=" . time();
    	
    	$ch = curl_init($api_url); 
    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch,CURLOPT_POST,true);
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_request);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    	curl_setopt($ch,CURLOPT_TIMEOUT,30);
    	
    	$response = curl_exec($ch);
    	//echo $response;
    	$response_parsed = json_decode($response, true);
    		
    	if (curl_errno($ch) || !$response)
    	{
            $response_parsed["status"] = 0;
        }
    	
    	//session
    	if (!$api_sess) {$api_sess = $response_parsed["sess"];}
    	
    	curl_close($ch);
    	
    	return $response_parsed;
	}
    
    public static function mt_api_stdclass2array($std_object) 
	{
	    if (is_object($std_object)) 
		{
            $std_object = get_object_vars($std_object);
        }
	
	    if (is_array($std_object))
		{
            return array_map(__FUNCTION__, $std_object);
        } else {
            return $std_object;
        }
	}
    
    public function login($api_user, $api_hash)
    {
        return $this->mt_api_call('init', array('api_user' => $api_user, 'api_hash' => $api_hash));
    }
    
    public function get_partners_cats()
    {
        $result = $this->mt_api_call('getPartnersCats');
        
        if ($result['data']) return $result['data'];
        
        return false;
    }
}