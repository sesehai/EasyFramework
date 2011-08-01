<?php
/**
 * model 控制基础类
 * @author luqi
 */
require_once 'Resource.php';
class BaseModel extends Resource {
	/**
	 * 表前缀
	 * @var string
	 */
	public $_prefix = 'easy_';
	public function __construct() {
		parent::__construct();
		/**
		 * 查询缓存时间 15分钟
		 */
		$this->_query_expire_time  = (15*60);
		/**
		 * 是否支持缓存
		 */
		$this->_mcEnable = true;
	}
	
	/**
	 * 设置是否缓存 查询
	 *
	 * @param boolean $value
	 */
	public function setMcEnable($value = true){
		$this->_mcEnable = $value;
	}
	
}
?>