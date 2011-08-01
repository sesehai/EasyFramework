<?php
define('APP_ROOT', dirname(__FILE__));//该常量只在后台使用
define('ROOT_PATH', dirname(APP_ROOT));
define('BASE_URL', 'http://mydomain.com/admin/index.php');
require_once ROOT_PATH . '/cfg.php';
require_once ROOT_PATH . '/core/Base.php';
/**
 * 启动
 */
Base::startup(array(
	'default_mod'   =>  'default',
	'default_ctl'   =>  'default',
	'default_act'   =>  'default',
	'ctl_root'      =>  APP_ROOT . '/app',
	'external_libs' =>  array(),
));
?>