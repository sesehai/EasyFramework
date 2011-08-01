<?php
class FrontendController extends BaseController {
	protected $tplDir  = 'android/';
	protected $pageTitle  = '应用系统';
	
	public function __construct(){
		parent::__construct();
		$this->_init_view();
	}
	
	
	/**
	 * 提示处理
	 *
	 * @param array $msg
	 * @param string $errno
	 */
	public function message($msg, $errno=null){
		if ($errno == E_USER_ERROR || $errno == E_ERROR || $errno == E_WARNING){
			$this->icon = "error";
		}else if ($errno == E_USER_WARNING){
			$this->icon = "warning";
		}else{
			$this->icon = "notice";
		}
		foreach ($msg['links'] AS $key=>$val){
			$this->links[] = array('text' => $val['text'], 'href' => $val['href']);
			if ($this->icon == 'notice' && $this->redirect == ''){
				$this->redirect = (strstr($val['href'], 'javascript:') !== false) ? $val['href'] : "location.href='{$val['href']}'";
			}
		}
		$this->message = nl2br($arr['content']);
	}
	
}
?>