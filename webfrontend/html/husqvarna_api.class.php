<?php
 require_once "loxberry_log.php";
 
class husqvarna_api {

	protected $url_api_im = 'https://iam-api.dss.husqvarnagroup.net/api/v3/';
	protected $url_api_track = 'https://amc-api.dss.husqvarnagroup.net/app/v1/';
	protected $username;
	protected $password;
	protected $token;
	protected $provider;
	
	var $automoweractivity= array (
			  	"UNKNOWN" 				=>0,  // Unbekannt
				"NOT_APPLICABLE" 		=>1,  // ??
				"MOWING" 				=>2,  // Mähen
				"GOING_HOME" 			=>3,  // Fährt zurück zur Ladestation
				"CHARGING"				=>4,  // Laden
				"LEAVING"				=>5,  // Verlässt Ladestation
				"PARKED_IN_CS"			=>6   // Geparkt in der Latestation
				);
				
	var $automowerstate= array (
			  	"UNKNOWN" 				=>0,  // 
				"NOT_APPLICABLE" 		=>1,  // 
				"PAUSED" 				=>2,  // 
				"IN_OPERATION" 			=>3,  //
				"WAIT_UPDATING"			=>4,  //
				"WAIT_POWER_UP"			=>5,  //
				"RESTRICTED"			=>6,  //
				"OFF" 					=>7,  //
				"STOPPED"				=>8,  //
				"ERROR"					=>10, //
				"FATAL_ERROR"			=>10, //
				"ERROR_AT_POWER_UP"		=>10  //
				);


    function login($username, $password)
	{
		
        LOGDEB("Calling Logon to Husqvarna API");	
		
        $this->username = $username;
        $this->password = $password;
		$fields["data"]["attributes"]["username"] = $this->username;
		$fields["data"]["attributes"]["password"] = $this->password;
		$fields["data"]["type"] = "token";
		
		$result = $this->post_api("token", $fields);
		//LOGOK("Data received from Husqvarna Connect API:".json_encode($result));
		if ( $result == false )
	 	{
	        LOGCRIT("Husqvarna URL not reachable, terminating");
	        LOGEND("Processing terminated");
	        return false;
		}
		else
		{
			if ($result->errors !== NULL) 
			{
				if ($result->errors[0]->code === "invalid.login") 
				{
					LOGCRIT("Wrong username ". $username . " or password " . $password . " for Husqvarna Connect API, terminating");
				}
			    else 
				{
			    	LOGCRIT("Other Problem in getting access to Husqvarna Connect API, terminating");
				}
				LOGEND("Processing terminated");
				return false;
			}
			else 
			{
				LOGOK("Getting access to Husqvarna Connect API, successfull");   
			}
			if ($result == NULL) 
			{
				LOGCRIT("No data from Husqvarna Connect API, terminating");
				LOGEND("Processing terminated");
				return false;
			}
			else {
				LOGOK("Data from Husqvarna Connect API received");
			}
			
			$this->token = $result->data->id;
			$this->provider = $result->data->attributes->provider;
			return true;
		}
		
	}

	private function get_headers($fields = null)
	{
		if ( isset($this->token) )
		{
			$generique_headers = array(
			   'Content-type: application/json',
			   'Accept: application/json',
				'Authorization: Bearer '.$this->token,
				'Authorization-Provider: '.$this->provider
			);
		}
		else
		{
			$generique_headers = array(
			   'Content-type: application/json',
			   'Accept: application/json'
			   );
		}
		if ( isset($fields) )
		{
			$custom_headers = array('Content-Length: '.strlen(json_encode ($fields)));
		}
		else
		{
			$custom_headers = array();
		}
		return array_merge($generique_headers, $custom_headers);
	}

	private function post_api($page, $fields = null)
	{
		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $this->url_api_im . $page);
		curl_setopt($session, CURLOPT_HTTPHEADER, $this->get_headers($fields));
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		if ( isset($fields) )
		{
			curl_setopt($session, CURLOPT_POSTFIELDS, json_encode ($fields));
		}
		$json = curl_exec($session);
		curl_close($session);
//		throw new Exception(__('La livebox ne repond pas a la demande de cookie.', __FILE__));
		return json_decode($json);
	}

	private function get_api($page, $fields = null)
	{
		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $this->url_api_track . $page);
		curl_setopt($session, CURLOPT_HTTPHEADER, $this->get_headers($fields));
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		if ( isset($fields) )
		{
			curl_setopt($session, CURLOPT_POSTFIELDS, json_encode($fields));
		}
		$json = curl_exec($session);
		curl_close($session);
//		throw new Exception(__('La livebox ne repond pas a la demande de cookie.', __FILE__));
		return json_decode($json);
	}

	private function del_api($page)
	{
		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $this->url_api_im . $page);
		curl_setopt($session, CURLOPT_HTTPHEADER, $this->get_headers());
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($session);
		curl_close($session);
//		throw new Exception(__('La livebox ne repond pas a la demande de cookie.', __FILE__));
		return json_decode($json);
	}

    function logout()
	{
		$result = $this->del_api("token/".$this->token);
		if ( $result !== false )
		{
			unset($this->token);
			unset($this->provider);
			return true;
		}
		return false;
	}
	
	function list_robots()
	{
		$list_robot = array();
		foreach ($this->get_api("mowers") as $robot)
		{
			$list_robot[$robot->id] = $robot;
		}
		return $list_robot;
	}
	
	function get_robot()
	{
		return $this->get_api("mowers");
	}

	function get_status($mover_id)
	{
		
		return $this->get_api("mowers/".$mover_id."/status");
	}

	function get_geofence($mover_id)
	{
		
		return $this->get_api("mowers/".$mover_id."/geofence");
	}

	function control($mover_id, $command)
	{
		if ( in_array($command, array('park', 'pause') ) )
		{
			//echo"DEBUG CM2D:".$command;
			return $this->get_api("mowers/".$mover_id."/control/".$command,array("period" => 180));
		}
		if ( in_array($command, array('start3h') ) )
		{
			return $this->get_api("mowers/".$mover_id."/control/start/override/period", array("period" => 180));
		}
	}
}
?>