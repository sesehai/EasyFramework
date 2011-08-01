<?php
/**
 * 根据PHP自带得Memcached类来扩展的Memcached操作类
 */
class ExtMemcache extends Memcache {
	private $_expire = 0;               //默认所有缓存均不过期
	private $_debug = false;
	private $_traced = false;
	private $_serverAry = array();

	/**
	 * 构造函数，初始化全局参数
	 * @param array $serverAry 二维数组
	 * @return  NULL
	 */
	private $_profiler = null;
	public  function __construct( $serverAry ) {
		$this->_serverAry = $serverAry;
		if(defined('DEBUG') && DEBUG) {
			$this->_debug = true;
			global $Profiler;
			$this->_profiler = $Profiler;
		}
		foreach ($serverAry AS $server) {
			if (! isset($server['host'], $server['port']) ) {
				die('MC Server Param Error' . var_export($server, true));
			}
			#以下参数程序写死
			$server['persistent'] = false;
			$server['timeout']    = 1;
			$server['retry']      = -1;
			$server['status']     = 1;
			//添加MC服务器到连接池，官方文档：addServer并不会去立即连接服务器
			/*
			$this->addServer(
			$server['host'],
			$server['port'],
			$_persistent=false,
			$server['weight'],
			$_timeout=1,
			$_retry=-1,
			$_status=1
			);
			*/
			$this->addServer(
			$server['host'],
			$server['port'],
			$_persistent=false
			);
		}
	}

	/**
	 * 重写memcached类的set接口，简化set参数
	 *
	 * @param string $key
	 * @param mixed $val
	 * @param int $expire
	 * @param string $compress
	 * @return bool MEMCACHE_COMPRESSED
	 */
	public function set($key, $val, $expire=0, $needCompress=false)
	{
		if( empty($key) ){
			Util::log("======key empty=======>set to mc failed! server is:key=>".$key."val=>".var_export($val, true), '/tmp/lgm_empty_memcache_fail.log');
			return false;
		}

		$ret = parent::set($key, $val, $needCompress, $expire);

		if($ret) {
			return $ret;
		} else {
			Util::log("set to mc failed! server is:".var_export($this->_serverAry, true), '/tmp/memcache_fail_set.log');

		}

		if ($this->_debug && $this->_profiler) {
			$backtrace = debug_backtrace();
			$file = basename($backtrace[1]['file']);
			$line = $backtrace[1]['line'];
			if (false === $ret) {
				$this->_profiler->setMarker(microtime(true) . "  set($key) false. file: $file line: $line");
			}
			else {
				$this->_profiler->setMarker(microtime(true) . " set($key) true. file: $file line: $line");
				if(!$this->_traced) {
					try {
						throw new Exception('debug');
					}
					catch (Exception $e) {
						Util::print_r('file:' . $e->getFile() . ' line:' . $e->getLine()
						. $e->getMessage() . $e->getTraceAsString());
					}
					$this->_traced = true;
				}

			}
		}
		return $ret;
	}

	public function get($cacheid) {
		$ret = false;
		if(!empty($cacheid)){
			$start_time = date("Y-m-d H:i:s");
			$ret = parent::get($cacheid);
				
			if (false === $ret ){
				$end_time = date('Y-m-d H:i:s');
				Util::log("get to mc failed! server is:{$cacheid} start_time:{$start_time} end_time:{$end_time}".Util::getRealIp(), '/tmp/memcache_fail_get.log');
			}
				
			if ($this->_debug && $this->_profiler) {
				$backtrace = debug_backtrace();
				$file = basename($backtrace[1]['file']);
				$line = $backtrace[1]['line'];
				if (false === $ret) {
					$this->_profiler->setMarker(microtime(true) . " get($cacheid) false. file: $file line: $line");
				}
				else {
					$this->_profiler->setMarker(microtime(true) . " get($cacheid) true. file: $file line: $line");
				}
			}
		}
		return $ret;
	}

	/**
	 * 新增incrementEx方法
	 * 支持如下两个变化
	 * 1. 如果key不存在，自动重新创建
	 * 2. 支持负数和正数，自动根据符号判断
	 *
	 * @param string $key
	 * @param int $value
	 * @return int
	 */
	public function incrementEx($key, $value=1) {
		//如果key不存在，直接设置
		if (false === parent::get($key)) {
			return parent::set($key, $value);
		}
		//判断value的正负分别处理
		if ($value > 0 ) {
			return parent::increment($key, $value);
		} else {
			return parent::decrement($key, -$value);
		}
	}
}
?>