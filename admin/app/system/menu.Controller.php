<?php
require(APP_ROOT .'/app/system/common.Controller.php');
class MenuController extends CommonController {
	public function __construct(){
		parent::__construct();
		$this->_check_privs();
		Base::import('utils.lib');
		$this->_utils = new UtilsLib();
		$this->oEasyMenu = Base::M('easyMenu');
		$this->oEasyMenu->setMcEnable($value = false);
		$this->initParams($_GET);
	}

	/**
	 * 初始化调用是的参数
	 */
	public function initParams($param){
		$this->_page    = (isset($_GET['page']) && !empty($_GET['page']))? $_GET['page']:1;
		$this->_num     = 10;
		$this->_start   = ($this->_page-1)*$this->_num;
	}
	
	/**
	 * 列表信息
	 *
	 */
	public function listAction(){
		//列表
		$parent_id = isset($_GET['parent_id']) && !empty($_GET['parent_id']) ? $_GET['parent_id'] : '0';
		$menu = array();
		if( !empty($parent_id) ){
			$menu = $this->getMenu($parent_id);
		}
		$sql = "SELECT * FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1' ";
		$where = " AND `parent`='{$parent_id}' ";
		$limit = " LIMIT {$this->_start},{$this->_num}";
		$order = " ORDER BY `order` ASC ";
		$sql .= $where.$order.$limit;
		$array_row = $this->oEasyMenu->query($sql);
		
		$array_count = $this->oEasyMenu->query("SELECT COUNT(*) as total FROM `{$this->oEasyMenu->table_name}` WHERE `isdel` = '1' {$where} ");
		$total = isset($array_count[0]['total']) ? $array_count[0]['total'] : 0;
		
		//分页
		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')   === false ? 'http' : 'https';
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['SCRIPT_NAME'];
		parse_str($_SERVER['QUERY_STRING'],$params);
		unset($params['page']);
		$this->_baseUrl = $protocol . '://' . $host . $script . '?';
		foreach($params as $name=>$value){
			$this->_baseUrl .= $name.'='.$value.'&';
		}
		$this->_baseUrl = $this->_baseUrl.'page=';
		$params=array(
			'start'   => $this->_start,
			'limit'   => $this->_num,
			'total'   => $total,
			'baseUrl' => $this->_baseUrl,
		    'pageBlock' =>5,
			'style'   => '3',
		);
		$pager=$this->_utils->pager($params);
		
		$this->_view->assign('menu',$menu);
		$this->_view->assign('parent_id',$parent_id);
		$this->_view->assign('pager',$pager);
		$this->_view->assign('start',$this->_start);
		$this->_view->assign('limit',$this->_num);
		$this->_view->assign('total',$total);
		$this->_view->assign('page',$this->_page);
		
		$this->_view->assign('menulist',$array_row);
		$this->_view->assign('baseUrl',BASE_URL);
		$this->_view->assign('tplDir',$this->tplDir);
		$this->_view->display($this->tplDir.'admin/system/menu.list.tpl');
	}
	
	/**
	 * 新增
	 *
	 */
	public function addAction(){
		if (!IS_POST){
			/* 显示新增表单 */
			$parent_id = isset($_GET['parent_id']) && !empty($_GET['parent_id']) ? $_GET['parent_id'] : '0';
			$parent_menu = $this->getParentMenu();
			
			$isshow['name'] = array('是','否');
			$isshow['value'] = array('1','2');
			$isshow['isshow'] = '1';
			
			$this->_view->assign('isshow',$isshow);
			$this->_view->assign('parent_menu',$parent_menu);
			$this->_view->assign('parent_id',$parent_id);
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/menu.add.tpl');
		}else{
			$data = array();
			$data['menuid'] = $_POST['menuid'];
			$data['text'] = $_POST['text'];
			$data['subtext'] = isset($_POST['subtext']) && !empty($_POST['subtext']) ? $_POST['subtext'] : '';
			$data['default'] = isset($_POST['default']) && !empty($_POST['default']) ? $_POST['default'] : '';
			$data['parent'] = $_POST['parent'];
			$data['url'] = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : '';
			$data['mod_text'] = isset($_POST['mod_text']) && !empty($_POST['mod_text']) ? $_POST['mod_text'] : '';
			$data['ctl_text'] = isset($_POST['ctl_text']) && !empty($_POST['ctl_text']) ? $_POST['ctl_text'] : '';
			$data['act_text'] = isset($_POST['act_text']) && !empty($_POST['act_text']) ? $_POST['act_text'] : '';
			$data['order'] = $_POST['order'];
			$data['isshow'] = $_POST['isshow'];
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());
			$data['ctime'] = date('Y-m-d H:i:s',time());
			if ( !$result = $this->oEasyMenu->insert($data) ){
				$this->message($msg = array('content'=>'新增失败！'));
				return;
			}
			$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=group&act=list');
			$this->message($msg = array('content'=>'新增成功！',
					'links'=>array($links),
						)
					);
		}
	}
	
	/**
	 * 修改
	 *
	 */
	public function editAction(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if (!$id){
			$this->message($msg = array('content'=>'没有找到该记录！'));
			return;
		}
		if (!IS_POST){
			$data     = $this->oEasyMenu->get_one($id);
			if (empty($data)){
				$this->message($msg = array('content'=>'没有找到该记录！'));
				return;
			}
			$parent_menu = $this->getParentMenu();
			
			$isshow['name'] = array('是','否');
			$isshow['value'] = array('1','2');
			$this->_view->assign('isshow',$isshow);
			
			$this->_view->assign('parent_menu',$parent_menu);
			
			$this->_view->assign('menu', $data);
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/menu.edit.tpl');
		}else{
			$data = array();
			$data['menuid'] = $_POST['menuid'];
			$data['text'] = $_POST['text'];
			$data['subtext'] = isset($_POST['subtext']) && !empty($_POST['subtext']) ? $_POST['subtext'] : '';
			$data['default'] = isset($_POST['default']) && !empty($_POST['default']) ? $_POST['default'] : '';
			$data['parent'] = $_POST['parent'];
			$data['url'] = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : '';
			$data['mod_text'] = isset($_POST['mod_text']) && !empty($_POST['mod_text']) ? $_POST['mod_text'] : '';
			$data['ctl_text'] = isset($_POST['ctl_text']) && !empty($_POST['ctl_text']) ? $_POST['ctl_text'] : '';
			$data['act_text'] = isset($_POST['act_text']) && !empty($_POST['act_text']) ? $_POST['act_text'] : '';
			$data['order'] = $_POST['order'];
			$data['isshow'] = $_POST['isshow'];
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());

			if ( !$result = $this->oEasyMenu->updateById($id,$data) ){
				$this->message($msg = array('content'=>'更新失败！'));
				return;
			}
			$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=menu&act=list');
			$this->message($msg = array('content'=>'更新成功！',
					'links'=>array($links),
						)
					);
		}
	}
	
	/**
	 * 删除
	 *
	 */
	public function deleteAction(){
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		if (!$id){
			$this->message($msg = array('content'=>'没有找到该记录！'));
			return;
		}
		$id = str_replace(",","','",$id);
		$data = array('isdel'=>'2');
		if ( !$result = $this->oEasyMenu->updateByIds($id,$data) ){
			$this->message($msg = array('content'=>'删除失败！'));
			return;
		}
		$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=menu&act=list');
		$this->message($msg = array('content'=>'删除成功！',
				'links'=>array($links),
					)
				);
	}
	
	/**
	 * 取得父菜单
	 *
	 */
	private function getParentMenu(){
		$array_pmenu = array();
		$array_pmenu_rows = $this->oEasyMenu->query("SELECT `id`,`text` FROM `{$this->oEasyMenu->table_name}` WHERE `parent` = '0' AND `isdel` = '1' ");
		if( isset($array_pmenu_rows) ){
			foreach($array_pmenu_rows as $row){
				$array_pmenu[$row['id']]= $row['text'];
			}
		}
		return $array_pmenu;
	}
	
	/**
	 * 根据id 取得菜单
	 *
	 */
	private function getMenu($id){
		$menu = $this->oEasyMenu->get_one($id);
		return $menu;
	}
	
}
?>