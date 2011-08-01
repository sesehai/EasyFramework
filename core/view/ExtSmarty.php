<?php
if(defined('SMARTY_TEMPLATE_DIR')) {
	require_once SMARTY_DIR . 'Smarty.class.php';
	//对 smarty 进行简单扩展
	class ExtSmarty extends Smarty {
		public function __construct() {
			parent::__construct();
			$this->template_dir    = SMARTY_TEMPLATE_DIR;
			$this->compile_dir     = SMARTY_COMPILE_DIR;
			$this->config_dir      = SMARTY_CONFIG_DIR;
			$this->cache_dir       = SMARTY_CACHE_DIR;
			$this->left_delimiter  = SMARTY_LEFT_DELIMITER;
			$this->right_delimiter = SMARTY_RIGHT_DELIMITER;
			$this->debugging       = isset($_GET['smartydebug']) ? true : false;
			$this->compile_id      = $_SERVER['SERVER_NAME'];
		}
	}
} else {
	require_once Config::SMARTY_DIR . 'Smarty.class.php';
	//对 smarty 进行简单扩展
	class ExtSmarty extends Smarty {
		public function __construct() {
			parent::__construct();
			$this->template_dir    = Config::SMARTY_TEMPLATE_DIR;
			$this->compile_dir     = Config::SMARTY_COMPILE_DIR;
			$this->config_dir      = Config::SMARTY_CONFIG_DIR;
			$this->cache_dir       = Config::SMARTY_CACHE_DIR;
			$this->left_delimiter  = Config::SMARTY_LEFT_DELIMITER;
			$this->right_delimiter = Config::SMARTY_RIGHT_DELIMITER;
			$this->debugging       = isset($_GET['smartydebug']) ? true : false;
			$this->compile_id      = $_SERVER['SERVER_NAME'];
		}
	}
}

?>