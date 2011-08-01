<?php
define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/cfg.php';
require_once ROOT_PATH . '/core/Base.php';
/**
 * 启动
 */
Base::startup(array(
	'default_mod'   =>  'default',
	'default_ctl'   =>  'default',
	'default_act'   =>  'index',
	'ctl_root'      =>  ROOT_PATH . '/app',
	'external_libs' =>  array(
		ROOT_PATH . '/includes/tools.php',
		ROOT_PATH . '/includes/lib/Util.php'
	),
));
?>