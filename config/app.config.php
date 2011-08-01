<?php
class Config {
	#smarty 有关常量
	const SMARTY_DIR          = '/Easy/includes/plugins/smarty/libs/';
	const SMARTY_TEMPLATE_DIR = '/Easy/tpl/';
	const SMARTY_CONFIG_DIR   = '/Easy/config/';
	const SMARTY_COMPILE_DIR  = '/Easy/compile/';
	const SMARTY_CACHE_DIR    = '/Easy/cache/';
	const SMARTY_LEFT_DELIMITER  = '{{';
	const SMARTY_RIGHT_DELIMITER = '}}';

	const DB_MAX_INT = 4200000000;          #数据库整数最大值
	const GROUP_CONCAT_SQL = 'SET SESSION group_concat_max_len = 360000';  #group_concat 最大长度
	/**
	 * db server param
	 */
	public static $db = array(
		'dbw' => array (
			'host' => '111.111.111.111',
			'port' => '3306',
			'dbname' => 'demo',
			'username' => 'admin',
			'password' => 'adminpwd',
			'driver_options' => array(
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				PDO::ATTR_EMULATE_PREPARES => true,
			),
		),
		'dbr' => array (
			'host' =>'111.111.111.112',
			'port' => '3306',
			'dbname' => 'demo',
			'username' => 'admin',
			'password' => 'adminpwd',
			'driver_options' => array(
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				PDO::ATTR_EMULATE_PREPARES => true,
			),
		),

	);

	const MC_ENABLE         = true;
	const MC_KEYS_LIMIT     = 500; #一次memcache连接最多可获取的对象数
	const MC_LIFETIME_SHORT = 10; #防攻击过期时间

	/**
	 * memcache server param
	 */
	public static $mc = array(
		'mcMain' => array(
			array('host' => '111.111.111.113', 'port' => '11111', 'weight' => 1),
		),
	);
}

?>
