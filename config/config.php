<?php
date_default_timezone_set( 'PRC' );
header('P3P: CP="CAO CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

define('IS_DEBUG','1');
if (defined('IS_DEBUG') && IS_DEBUG){
	if (version_compare(PHP_VERSION,'5.0','>=')){
		error_reporting(E_ALL &~ E_STRICT);
	}else{
		error_reporting(E_ALL);
	}
	@ini_set('display_errors', 1);
}else{
	error_reporting(0); //? E_ERROR | E_WARNING | E_PARSE
	@ini_set('display_errors', 0);
}
require_once ROOT_PATH .'/config/app.config.php';

?>
