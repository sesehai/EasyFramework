<?php
require(APP_ROOT .'/app/default/common.Controller.php');
class DefaultController extends CommonController {
	protected $tplDir  = 'easy/';
	protected $pageTitle  = '应用系统';
	public function __construct(){
		parent::__construct();
		$this->_check_privs();
	}

	public function defaultAction(){
		//$back_nav = $menu = $this->_get_menu();
		$back_nav = $menu = $this->_get_menu_fromdb_foruser();
		$this->_view->assign('menu', $menu);
		$this->_view->assign('menu_json',json_encode($menu));
		$this->_view->assign('back_nav', $back_nav);
		$this->_view->assign('visitor', $this->visitor->info);
		$this->_view->display($this->tplDir.'admin/index.tpl');
	}

	public function wellcomeAction(){
		$this->_view->assign('baseUrl',BASE_URL);
		$this->_view->assign('tplDir',$this->tplDir);
		$this->_view->assign('visitor', $this->visitor->info);
		$this->_view->display($this->tplDir.'admin/wellcome.tpl');
	}

	private function _get_menu()
	{
		$menu = include(APP_ROOT . '/includes/menu.inc.php');

		return $menu;
	}

	private function _get_menu_fromdb()
	{
		$menu = array();

		$this->oEasyMenu = Base::M('easyMenu');
		$this->oEasyMenu->setMcEnable($value = false);

		$privs = $this->visitor->get('privs');

		$sql = "SELECT * FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1' ";
		$order = " ORDER BY `order` ASC ";
		$sql .= $order;
		$array_row = $this->oEasyMenu->query($sql);

		$parent_menu = array();
		$children_menu = array();
		foreach($array_row as $row){
			if( $row['parent'] == '0' ){
				$parent_menu[$row['id']][$row['menuid']] = array(
				'text'      => $row['text'],
				'subtext'   => $row['subtext'],
				'default'   => $row['default'],
				'children'  => array(),
				);
			}else{
				$children_menu[$row['parent']][$row['menuid']] = array(
				'text'      => $row['text'],
				'url'   => $row['url'],
				);
			}
		}

		foreach($parent_menu as $key=>$pmu){
			$pmu[key($pmu)]['children'] = $children_menu[$key];
			$menu = array_merge($menu,$pmu);
		}

		return $menu;
	}

	private function _get_menu_fromdb_foruser()
	{
		$menu = array();

		$this->oEasyMenu = Base::M('easyMenu');
		$this->oEasyMenu->setMcEnable($value = false);

		$privs = $this->visitor->get('privs');

		if( isset($privs) && !empty($privs) ){
			if( 'all' != $privs ){
				//子菜单
				$privs = str_replace(",","','",$privs);
				$sql = "SELECT * FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1' AND `isshow` = '1' AND `id` IN ('{$privs}') ";
				$order = " ORDER BY `order` ASC ";
				$sql .= $order;
				$array_row = $this->oEasyMenu->query($sql);

				$parent_menu = array();
				$children_menu = array();
				$parentid_array = array();
				foreach($array_row as $row){
					$parentid_array[] = $row['parent'];
					$children_menu[$row['parent']][$row['menuid']] = array(
					'text'      => $row['text'],
					'url'   => $row['url'],
					);
					if( isset($children_default[$row['parent']]['default']) && !empty($children_default[$row['parent']]['default']) ){
						continue;
					}else{
						$children_default[$row['parent']]['default'] = $row['menuid'];
					}
				}
				//父菜单
				$privs_parent = implode("','",$parentid_array);
				$sql_parent = "SELECT * FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1'  AND `isshow` = '1' AND `id` IN ('{$privs_parent}') ";
				$order_parent = " ORDER BY `order` ASC ";
				$sql_parent .= $order_parent;
				$array_parent_row = $this->oEasyMenu->query($sql_parent);
				foreach($array_parent_row as $row){
					$parent_menu[$row['id']][$row['menuid']] = array(
					'text'      => $row['text'],
					'subtext'   => $row['subtext'],
					'default'   => $row['default'],
					'children'  => array(),
					);
				}
				
				foreach($parent_menu as $key=>$pmu){
					$pmu[key($pmu)]['children'] = $children_menu[$key];
					$pmu[key($pmu)]['default'] = $children_default[$key]['default'];
					$menu = array_merge($menu,$pmu);
				}
			}else{
				$sql = "SELECT * FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1'  AND `isshow` = '1' ";
				$order = " ORDER BY `order` ASC ";
				$sql .= $order;
				$array_row = $this->oEasyMenu->query($sql);

				$parent_menu = array();
				$children_menu = array();
				foreach($array_row as $row){
					if( $row['parent'] == '0' ){
						$parent_menu[$row['id']][$row['menuid']] = array(
						'text'      => $row['text'],
						'subtext'   => $row['subtext'],
						'default'   => $row['default'],
						'children'  => array(),
						);
					}else{
						$children_menu[$row['parent']][$row['menuid']] = array(
						'text'      => $row['text'],
						'url'   => $row['url'],
						);
					}
				}
				foreach($parent_menu as $key=>$pmu){
					$pmu[key($pmu)]['children'] = $children_menu[$key];
					$menu = array_merge($menu,$pmu);
				}
			}
				
		}else{
			$menu = array();
		}

		return $menu;
	}

}
?>