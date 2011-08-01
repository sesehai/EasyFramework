<?php

class Plog_Formatter 
{
	public $args;

	public function getTime()
	{
		static $time;
		empty($time) && $time = date('Y/m/d H:i:s');
		return $time;
	}

	public function getUri()
	{
		$php_self = htmlentities(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
		$query_string = isset($_SERVER['argv'][0]) ? $_SERVER['argv'][0] : $_SERVER['QUERY_STRING'];
		if (!isset($_SERVER['REQUEST_URI']))
		{
			$uri = $php_self . '?' . $query_string;
		}
		else
		{
			if (strpos($_SERVER['REQUEST_URI'], '?') === false && $query_string)
			{
				 $uri = $_SERVER['REQUEST_URI'].'?' . $query_string;
			}else{
				$uri = $_SERVER['REQUEST_URI'];
			}
		}
		return $_SERVER['REQUEST_URI'];
	}

	public function getIp()
	{
		$clientIp = '0.0.0.0';
		if (isset($_SERVER['CLIENT_IP']))
		{
			$clientIp = $_SERVER['CLIENT_IP'];
		}
		elseif (isset($_SERVER['X_FORWARDED_FOR']))
		{
			$clientIp = $_SERVER['X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			$clientIp = $_SERVER['REMOTE_ADDR'];
		}

		return $clientIp;
	}

	public function getLevel()
	{
		return $this->args['level'];
	}

	public function getLogger()
	{
		return $this->args['logger'];
	}

	public function getMessage()
	{
		return $this->args['message'];
	}
	
	public function getTrack(){
		$date = date("Y-m-d");
		$m = '[' . date('Y-m-d H:i', time()) . "]\n";
		$d = debug_backtrace();
		foreach($d as $trace) {
			$m .= isset($trace['file']) && isset($trace['line']) ? $trace['file'] . ' : ' . $trace['line'] . "\n" : '';
		}
		return $m;
	}
}
