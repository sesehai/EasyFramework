<?php
/**
 * 访问者基础类，集合了当前访问用户的操作
 *
 * @return void
 */
class AdminVisitor {
	public $has_login = false;
	public $info      = null;
	public $privilege = null;
	public $_info_key = 'user';
	public function __construct(){
		$this->BaseVisitor();
	}
	public function BaseVisitor(){
		if (!empty($_SESSION[$this->_info_key]['user_id'])){
			$this->info = $_SESSION[$this->_info_key];
			$this->has_login = true;
		}else{
			$this->info = array(
			'user_id'   => 0,
			'user_name' => 'guest'
			);
			$this->has_login = false;
		}
	}
	public function assign($user_info){
		$_SESSION[$this->_info_key] = $user_info;
	}
	
	public function get($key = null){
		return isset($_SESSION[$this->_info_key][$key]) ? $_SESSION[$this->_info_key][$key] : '';
	}
	/**
	 *登出
	 *
	 *@return void
	 */
	public function logout(){
		unset($_SESSION[$this->_info_key]);
	}
	public function i_can($event, $privileges = array()){
		$fun_name = 'check_' . $event;
		return $this->$fun_name($privileges);
	}

	public function check_do_action($privileges){
		$result = false;
		$mp = MOD.'|'.CTL.'|'.ACT;
		if ($privileges == 'all'){
			/* 拥有所有权限 */
			$result = true;
		}else{
			/* 查看当前操作是否在白名单中，如果在，则允许，否则不允许 */
			$privs = explode(',', $privileges);
			if ( in_array($mp, $privs) || in_array('all',$privs) ){
				$result = true;
			}
		}
		return $result;
	}

}
?>