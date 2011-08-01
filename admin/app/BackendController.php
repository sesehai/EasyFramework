<?php
require(APP_ROOT .'/app/AdminVisitor.php');
class BackendController extends BaseController {
	protected $tplDir  = 'easy/';
	protected $pageTitle  = '应用系统';
	public function __construct(){
		parent::__construct();
		$this->_init_view();
		$this->_init_visitor();
	}
	
	public function _init_visitor(){
		$this->visitor =Base::env('visitor', new AdminVisitor());
	}
	
	public function _check_privs(){
		//判断是否登录
		if($this->visitor->has_login){
			/* 登录后判断是否有权限 */
			if (!$this->visitor->i_can('do_action', $this->visitor->get('privs_str'))){
				$this->message(array('content'=>'您没有权限,请联系管理员！'));
				exit;
			}
		}else{
			$this->redirect(BASE_URL."?mod=default&ctl=login&act=login",$direct = true,$msg='',$time=0);
			exit;
		}
	}

	/**
	 * 提示处理
	 *
	 * @param array $msg
	 * @param string $errno
	 */
	public function message($msg, $errno=null){
		if ($errno == E_USER_ERROR || $errno == E_ERROR || $errno == E_WARNING){
			$icon = "error";
		}else if ($errno == E_USER_WARNING){
			$icon = "warning";
		}else{
			$icon = "notice";
		}
		$msg['redirect'] = '';
		$msg['back'] = '1';
		//$message = nl2br($msg['content']);
		
		$this->_view->assign('message',$msg);
		$this->_view->assign('baseUrl',BASE_URL);
		$this->_view->assign('tplDir',$this->tplDir);
		$this->_view->display($this->tplDir.'admin/message.tpl');
	}
	
	/**
	 * 跳转
	 *
	 * @param array $msg
	 * @param string $errno
	 */
	public function redirect($url,$direct = true,$msg,$time){
		if($direct){
			header("location:".$url."");
		}
	}

}
?>