<?php

require_once(ROOT_PATH.'/core/log/plog/handler/abstract.php');

class Plog_Handler_File extends Plog_Handler_Abstract
{
	public function save()
	{
		$log_message = $this->_format()."\n";
		$dir = rtrim($this->_local_config['dir'], '/');
		$dest_dir = $dir.'/'.date('Y').'/'.date('m');
		if( is_dir($dest_dir) && !file_exists($dest_dir) )
		{
			mkdir($dest_dir, 0777, true);
		}
		$dest_file = $dest_dir.'/'.date('Y-m-d').'.log';
		$fp = fopen($dest_file , "a");
		if($fp) {
			fputs($fp, $log_message);
			fclose($fp);
		}
	}
}
