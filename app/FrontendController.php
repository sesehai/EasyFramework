<?php
class FrontendController extends BaseController {
	protected $tplDir  = 'android/';
	protected $pageTitle  = '乐视应用系统';
	/**
	 * 客户端产品类型配置:
	 */
	protected $_ptypeArr = array(
		'iphone'=>'c1',
		'ipad'=>'c2',
		'androidphone'=>'c3',
		'pc'=>'c4',
		'sonypad'=>'c5',
		'lepad'=>'c6',
		'hisensetv'=>'c7',
		'greatwallpad'=>'c8',
		'3lepad'=>'c9',
		'android21'=>'c10',
		'epg'=>'c11',
		'hipad'=>'c12',
		'iphone21'=>'c13',
		'letoupad'=>'c14',
		'oppopad'=>'c15',
		'xoompad'=>'c16',
	);
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