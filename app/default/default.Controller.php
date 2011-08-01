<?php
require(ROOT_PATH .'/app/external/common.Controller.php');
class DefaultController extends CommonController {
	public function __construct(){
		parent::__construct();
		Base::import('utils.lib');
		$this->_utils = new UtilsLib();
		$this->oDefault = Base::M('default');
		$this->oDefault->setMcEnable($value = false);
		$this->initParams($_GET);
	}

	/**
	 * 初始化调用是的参数
	 */
	public function initParams($param){
		$this->_id = (isset($param['id']) && !empty($param['id']))? $param['id']:0;
	}
	/**
	 * 打印 列表数据
	 *
	 */
	public function listAction(){
		

	}
}
?>