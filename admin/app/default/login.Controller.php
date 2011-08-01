<?php
require(APP_ROOT .'/app/default/common.Controller.php');
class LoginController extends CommonController {
	protected $tplDir  = 'android/';
	protected $pageTitle  = '应用系统';
	public function __construct(){
		parent::__construct();
		$this->_init_view();
		$this->_init_visitor();
	}
	
	public function loginAction(){
		if ($this->visitor->has_login){
			$this->message(array('content'=>'Msg:已经登录'));
		}else{
			if (!IS_POST){
				$this->_view->assign('mod',MOD);
				$this->_view->assign('ctl',CTL);
				$this->_view->display($this->tplDir.'admin/login.tpl');
			}else{
				$user_name = trim($_POST['user_name']);
				$password  = trim($_POST['password']);
				$user_id = $this->auth($user_name, $password);
				if (!$user_id){
					/* 未通过验证，提示错误信息 */
					$this->message(array('content'=>'Error:登录失败，请检查用户名密码！'));
					//$this->loginAction();
				}else{
					/* 通过验证，执行登陆操作 */
					if (!$this->_do_login($user_id)){
						$this->message(array('content'=>'Error：登录失败'));
						$this->redirect(BASE_URL."?mod=default&ctl=login&act=login",$direct = true,$msg = '',$time = '');
						//$this->loginAction();
					}else{
						$this->redirect(BASE_URL."?mod=default&ctl=default&act=default",$direct = true,$msg = '',$time = '');
					}
				}
			}
		}
	}
	/**
	 * 退出
	 *
	 */
	public function logoutAction(){
		$this->visitor->logout();
		$this->redirect(BASE_URL."?mod=default&ctl=login&act=login",$direct = true,$msg = '',$time = '');
	}

	/**
	 * 执行登陆操作
	 *
	 * @param int $user_id
	 * @return bool
	 */
	private function _do_login($user_id){
		$result = true;
		$oUser = Base::M('mclientUser');
		$oUser->setMcEnable($value = false);
		$user_info = $oUser->get_one($user_id);
		if (!$user_info['privs']){
			$this->message(array('content'=>'没有权限！'));
			$result = false;
		}
		/* 分派身份 */
		$this->visitor->assign(array(
		'user_id'       => $user_info['id'],
		'user_name'     => $user_info['name'],
		'reg_time'      => $user_info['ctime'],
		'last_login'    => $user_info['lastlogin'],
		'last_ip'       => $user_info['last_ip'],
		'privs'         => $user_info['privs'],
		'privs_str'         => $this->_get_privs_str($user_info['privs']),
		));
		//@todo更新登录信息
		return $result;
	}
	
	/**
	 * 取得权限 判断依据
	 *
	 */
	private function _get_privs_str($privs){
		$privs_str = '';
		$oEasyMenu = Base::M('easyMenu');
		$oEasyMenu->setMcEnable($value = false);
		if( isset($privs) && !empty($privs) ){
			$privs_Arr = explode(',',$privs);
			if( in_array('all',$privs_Arr) ){
				$privs_str = "all";
			}else{
				$privs = str_replace(",","','",$privs);
				$sql = "SELECT * FROM `{$oEasyMenu->table_name}` WHERE `isdel` = '1' AND `id` IN ('{$privs}') ";
				$order = " ORDER BY `order` ASC ";
				$sql .= $order;
				$array_row = $oEasyMenu->query($sql);

				$parent_menu = array();
				$children_menu = array();
				$parentid_array = array();
				foreach($array_row as $row){
					$privs_str .= "{$row['mod_text']}|{$row['ctl_text']}|{$row['act_text']},";
				}
			}
			$privs_str = !empty($privs_str) && strpos($privs_str,',') !==FALSE ? substr($privs_str,0,-1) : $privs_str;
				
		}
		return $privs_str;
	}
	
	/**
	 * 检查用户是否合法
	 *
	 * @param string $user_name
	 * @param string $password
	 */
	private function auth($user_name, $password){
		$result = 0;
		$oUser = Base::M('eayUser');
		$oUser->setMcEnable($value = false);
		$condition = " `isdel` = '1' AND `name` = '{$user_name}' ";
		$user = $oUser->getOneByCondition($condition);
		if( isset($user) && !empty($user) ){
			if( $user['passwd'] == md5($password) ){
				$result = $user['id'];
			}else{
				$result = 0;
			}
		}else{
			$result = 0;
		}
		
		return $result;
	}


}
?>