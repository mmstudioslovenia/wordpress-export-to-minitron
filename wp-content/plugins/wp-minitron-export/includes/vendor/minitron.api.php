<?php

class WPMT_API {
    
    const DEV_URL = 'http://dev.minitron-apps.si/api/';
    const LIVE_URL = 'https://secure.minitron-apps.si/api/';
    
    public $is_connected;
    public $is_demo;
    
    public function __construct($api_user, $api_hash)
    {
        $api = $this->mt_api_call('init', array('api_user' => $api_user, 'api_hash' => $api_hash));
        $this->is_connected = $api['data']['ok'];
        
        require_once WPMT_PATH.'includes/wpmt-admin.class.php';
        $this->is_demo = WPMT_Admin::get_option('api_demo');
	}
    
    public function is_connected()
    {
        return $this->is_connected;
    }
    
    public function mt_api_call($action = "", $params = array(), $type = '')
	{	
        //api url
        $api_url = (!$this->is_demo) ? self::DEV_URL : self::LIVE_URL;
    	
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
        
        switch ($type)
        {
            case 'json':
                $post_request = json_encode($params);
            break;
            default:
                $post_request = "";
                if (is_array($params))
                {
                    foreach(array_keys($params) as $key)
                    {
                        $post_request .= "&" . $key . "=" . urlencode($params[$key]);
                    }
                }
            break;
        }

    	//prevent cache
    	$api_url .= "?" . $lnk . "&f=" . time();
    	
    	$ch = curl_init($api_url); 
    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch,CURLOPT_POST,true);
    	curl_setopt($ch,CURLOPT_POSTFIELDS, $post_request);
        if ($type == 'json')
        {
            curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    	curl_setopt($ch,CURLOPT_TIMEOUT,30);
    	
    	$response = curl_exec($ch);
        
        //echo $response . PHP_EOL;
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
    
    public function set_partner($subscriber)
    {
        $result = $this->mt_api_call('setPartner', $subscriber);
        
        if ($result)
        {
            echo json_encode($result['data']);
            die();
        }
        return false;
    }
    
    public function set_partners($subscribers)
    {
        $result = $this->mt_api_call('setPartners', array('partners' => $subscribers), 'json');
        
        if ($result)
        {
            echo json_encode($result['data']);
            die();
        }
        return false;
    }
}