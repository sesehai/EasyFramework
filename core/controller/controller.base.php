<?php
/**
 * 控制器基础类
 *
 */
class BaseController extends Object
{
	/**
	 * 建立到视图的链接
	 */
	public $_view = null;

	public function __construct()
	{
		parent::__construct();
		$this->BaseApp();
	}

	public function BaseApp()
	{
		$this->_init_session();
	}

	/**
	 * 初始化视图连接
	 *
	 * @param    none
	 * @return    void
	 */
	protected function _init_view()
	{
		if ($this->_view === null)
		{
			$this->_view =Base::V();
		}
	}
	/**
	 * 运行指定的动作
	 *
	 * @param string $action
	 */
	public function do_action($action)
	{
		if ($action && $action{0} != '_' && method_exists($this, $action))
		{
			/**
			 * 运行动作
			 */
			$this->$action();
		}
		else
		{
			exit('missing_action');
		}
	}
	/**
	 *    初始化Session
	 *
	 * @param    none
	 * @return    void
	 */
	public function _init_session()
	{
		session_start();
		//Base::import('session.lib');
		//$this->_session = new sessionLib();
	}
	/**
	 * 控制器结束运行后执行
	 *
	 */
	public function destruct()
	{
	}
}
?>