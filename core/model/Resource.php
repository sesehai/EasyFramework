<?php
/**
 * 资源管理基础类
 */
class Resource {

	protected $_debug = false;
	protected $_mcEnable = true;

	/**
	 * 采用缓冲池技术，有效避免多次生成对象实例的开销
	 */
	protected static $dbPool = array();     #db对象池
	protected static $mcPool = array();     #memcache对象池


	/**
	 * 若子类不提供日志文件属性, 默认将日志写到这个文件
	 */
	protected $_logFile = '/tmp/resource.log';

	/**
	 * 为便于调试, 提供一种便利的禁用 mc 的方式
	 */
	public function __construct() {
		if (isset( $_GET['mcDisable'] ) || !Config::MC_ENABLE ) {
			$this->_mcEnable = false;
		}
	}


	/**
	 * 指定 mcString 得到对应的 ExtMemcache 对象, 极其常用
	 *
	 * @param string $mcString
	 * @return object of ExtMemcache
	 */
	public function getMc($mcString = 'mcMain') {
		if (isset(self::$mcPool[$mcString])) {
			return self::$mcPool[$mcString];
		}
		require_once ROOT_PATH .'/core/cache/ExtMemcache.php';
		return self::$mcPool[$mcString] = new ExtMemcache( Config::$mc[$mcString] );
	}

	/**
	 * 指定 dbString 得到对应的 Zend_Db_Adapter_Pdo_Mysql 对象, 极其常用
	 *
	 * @param string $dbString
	 * @return object of Zend_Db_Adapter_Pdo_Mysql
	 */
	public function getDb($dbString='dbr', $real=false) {

		if($real) {
			require_once ROOT_PATH .'/includes/plugins/Zend/Db.php';
			return self::$dbPool[$dbString] = Zend_Db::factory('PDO_MYSQL', Config::$db[$dbString]);
		}

		if (isset(self::$dbPool[$dbString])) {
			return self::$dbPool[$dbString];
		}

		require_once ROOT_PATH .'/includes/plugins/Zend/Db.php';
		if($dbString == 'dbr' || $dbString == 'dbcloner') {
			$dbParam = Config::$db[$dbString];
			$hostAry = $dbParam['host'];
			$dbParam['host'] = $hostAry[mt_rand(0, sizeof($hostAry) - 1)];
			return self::$dbPool[$dbString] = Zend_Db::factory('PDO_MYSQL', $dbParam);
		}
		return self::$dbPool[$dbString] = Zend_Db::factory('PDO_MYSQL', Config::$db[$dbString]);
	}


	/**
	 * 指定 objString 得到对应的对象实例， 极为常用
	 * @param string $objString
	 * @return object of $objString
	 */
	public function getObj($objString) {
		if(isset(self::$objPool[$objString])) {
			return self::$objPool[$objString];
		}
		require_once self::$availableObj[$objString];
		return self::$objPool[$objString] = new $objString();
	}

	/**
	 * 启用 memcache, 默认 memcache 就是启用的
	 */
	public function setMcEnable() {
		$this->_mcEnable = true;
	}

	/**
	 * 禁用 memcache
	 */
	public function setMcDisable() {
		$this->_mcEnable = false;
	}

	/**
	 * 记录日志到 $this->_logFile, 为子类提供方便的日志功能
	 *
	 * @param string $msg
	 */
	public function log($msg) {
		Util::log($msg, $this->_logFile);
	}

	public function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>