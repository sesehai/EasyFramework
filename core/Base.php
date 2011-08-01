<?php
class Base
{
	/**
	 * 模型数组
	 */
	public static $_models = array();
	/**
	 * http request 对象
	 *
	 */
	public static $_request = null;

	public function __construct(){

	}
	/**
	 * 启动入口
	 * @param array $config
	 */
	public static function startup($config = array())
	{
		Base::initConfig();
		/**
		 * 基础控制器类
		 */
		require(ROOT_PATH . '/core/controller/controller.base.php');
		/**
		 * 模型基础类
		 */
		require(ROOT_PATH . '/core/model/model.base.php');

		if (!empty($config['external_libs']))
		{
			foreach ($config['external_libs'] as $lib)
			{
				require($lib);
			}
		}
		
		self::$_request = Base::initRouter();
		/**
		 * 数据过滤
		 */
		if (!get_magic_quotes_gpc())
		{
			$_GET   = Base::addslashes_deep($_GET);
			$_POST  = Base::addslashes_deep($_POST);
			$_COOKIE= Base::addslashes_deep($_COOKIE);
		}

		/**
		 * 请求转发
		 */
		$default_mod = $config['default_mod'] ? $config['default_mod'] : 'default';
		$default_ctl = $config['default_ctl'] ? $config['default_ctl'] : 'default';
		$default_act = $config['default_act'] ? $config['default_act'] : 'index';

		$modName = self::$_request->getModuleName();
		$ctlName = self::$_request->getControllerName();
		$actName = self::$_request->getActionName();
		$mod    = isset($modName) ? trim($modName) : $default_mod;
		$ctl    = isset($ctlName) ? trim($ctlName) : $default_ctl;
		$act    = isset($actName) ? trim($actName) : $default_act;
		define('MOD', $mod);
		define('CTL', $ctl);
		define('ACT', $act);
		$act   .= 'Action';
		$ctl_file = "{$config['ctl_root']}/{$mod}/{$ctl}.Controller.php";
		if (!is_file($ctl_file))
		{
			exit("Missing controller:{$mod}/{$ctl}.Controller.php");
		}

		require($ctl_file);
		$ctl_class_name = ucfirst($ctl) . 'Controller';

		/**
		 * 实例化控制器
		 */
		$ctl     = new $ctl_class_name();
		/**
		 * 转发至对应的Action
		 */
		$ctl->do_action($act);
		$ctl->destruct();
	}

	public static function initConfig(){
		/**
		 * 记录程序启动时间
		 */
		define('START_TIME', Base::util_microtime());
		/**
		 * 数据提交方式
		 */
		define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));
		/**
		 * 定义PHP_SELF常量
		 */
		define('PHP_SELF',  htmlentities(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
		/**
		 * 以下是PHP在不同版本，不同服务器上的兼容处理
		 * 在部分IIS上会没有REQUEST_URI变量
		 */
		$query_string = isset($_SERVER['argv'][0]) ? $_SERVER['argv'][0] : $_SERVER['QUERY_STRING'];
		if (!isset($_SERVER['REQUEST_URI']))
		{
			$_SERVER['REQUEST_URI'] = PHP_SELF . '?' . $query_string;
		}
		else
		{
			if (strpos($_SERVER['REQUEST_URI'], '?') === false && $query_string)
			{
				$_SERVER['REQUEST_URI'] .= '?' . $query_string;
			}
		}

	}
	
	/**
	 * 路由器 初始化
	 */
	public static function initRouter(){
		require_once(ROOT_PATH.'/core/router/Rewrite.php');
		require_once(ROOT_PATH.'/core/request/Http.php');
		$request = new RequestHttp();
		$router = new RouterRewrite();
		$router->addDefaultRoutes( $request );
		$request = $router->route($request);
		return $request;
	}

	/**
	 *  获取一个模型
	 *
	 *  @param  string $model_name
	 *  @param  array  $params
	 *  @param  book   $is_new
	 *  @return object
	 */
	public static function M($model_name, $params = array(), $is_new = false)
	{
		$model_hash = md5($model_name . var_export($params, true));
		if ($is_new || !isset( self::$_models[$model_hash] ))
		{
			$model_file = ROOT_PATH . '/includes/models/' . $model_name . '.model.php';
			if (!is_file($model_file))
			{
				/**
				 * 不存在该文件，则无法获取模型
				 */
				return false;
			}
			include_once($model_file);
			$model_name = ucfirst($model_name) . 'Model';
			if ($is_new)
			{
				return new $model_name($params);
			}
			self::$_models[$model_hash] = new $model_name($params);
		}

		return self::$_models[$model_hash];
	}

	/**
	 *    获取视图链接
	 *
	 *    @param     string $engine
	 *    @return    object
	 */
	public static function V($is_new = false, $engine = 'default')
	{
		include_once(ROOT_PATH . '/core/view/view.base.php');
		if ($is_new)
		{
			return new BaseView();
		}
		else
		{
			static $v = null;
			if ($v === null)
			{
				switch ($engine)
				{
					case 'default':
						$v = new BaseView();
						break;
				}
			}

			return $v;
		}
	}

	/**
	 * 获取环境变量
	 *
	 * @param     string $key
	 * @param     mixed  $val
	 * @return    mixed
	 */
	public function env($key, $val = null){
		$result = '';
		$vkey = '$GLOBALS[\'LETV_ANDROID_ENV\']'.'[\''.$key.'\']';
		if ($val === null){
			/* 返回该指定环境变量 */
			$result = eval('return ' . $vkey . ';');
		}else{
			/* 设置指定环境变量 */
			eval($vkey . ' = $val;');
			$result = $val;
		}
		return $val;
	}


	/**
	 *  格式化显示出变量
	 *  @param  any
	 *  @return void
	 */
	public function rdump($arr)
	{
		echo '<pre>';
		array_walk(func_get_args(), create_function('&$item, $key', 'print_r($item);'));
		echo '</pre>';
		exit();
	}

	/**
	 * 递归方式的对变量中的特殊字符进行转义
	 * @access  public
	 * @param   mix     $value
	 * @return  mix
	 */
	public function addslashes_deep($value)
	{
		if (empty($value))
		{
			return $value;
		}
		else
		{
			return is_array($value) ? array_map(array('Base','addslashes_deep'), $value) : addslashes($value);
		}
	}

	/**
	 * 递归方式的对变量中的特殊字符去除转义
	 * @access  public
	 * @param   mix     $value
	 * @return  mix
	 */
	public function stripslashes_deep($value)
	{
		if (empty($value))
		{
			return $value;
		}
		else
		{
			return is_array($value) ? array_map(array('Base','stripslashes_deep'), $value) : stripslashes($value);
		}
	}
	/**
	 * 获取当前时间的微秒数
	 *
	 * @return float
	 */
	public function util_microtime()
	{
		if (PHP_VERSION >= 5.0)
		{
			return microtime(true);
		}
		else
		{
			list($usec, $sec) = explode(" ", microtime());

			return ((float)$usec + (float)$sec);
		}
	}

	/**
	 *导入一个类
	 *@return    void
	 */
	public static function import()
	{
		$c = func_get_args();
		if (empty($c))
		{
			return;
		}
		array_walk($c, create_function('$item, $key', 'include_once(ROOT_PATH . \'/includes/lib/\' . $item . \'.php\');'));
	}

	public static function log($level,$msg){
		require(ROOT_PATH .'/core/log/plog/plog.php');
		require(ROOT_PATH .'/core/log/plog/config.php');
		Plog::set_config($log_config);
		$log = Plog::factory(__FILE__);
		$log->$level($msg);
	}

}

/**
 *所有类的基础类
 *@usage    none
 */
class Object
{
	public $_errors = array();
	public $_errnum = 0;

	public function __construct()
	{
		$this->Object();
	}
	public function Object()
	{
		#TODO
	}
	/**
	 * 触发错误
	 *
	 * @param string $msg
	 * @param unknown_type $obj
	 */
	public function _error($msg, $obj = '')
	{
		if(is_array($msg))
		{
			$this->_errors = array_merge($this->_errors, $msg);
			$this->_errnum += count($msg);
		}
		else
		{
			$this->_errors[] = compact('msg', 'obj');
			$this->_errnum++;
		}
	}

	/**
	 * 检查是否存在错误
	 * @return unknown
	 */
	public function has_error()
	{
		return $this->_errnum;
	}

	/**
	 * 获取错误列表
	 *
	 * @return unknown
	 */
	public function get_error()
	{
		return $this->_errors;
	}
}

?>
