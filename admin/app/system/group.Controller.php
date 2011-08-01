<?php
require(APP_ROOT .'/app/system/common.Controller.php');
class GroupController extends CommonController {
	public function __construct(){
		parent::__construct();
		$this->_check_privs();
		Base::import('utils.lib');
		$this->_utils = new UtilsLib();
		$this->oEasyGroup = Base::M('easyGroup');
		$this->oEasyGroup->setMcEnable($value = false);
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
		$sql = "SELECT * FROM `{$this->oEasyGroup->table_name}` WHERE `isdel` = '1' ";
		$where = '';
		$limit = " LIMIT {$this->_start},{$this->_num}";
		$order = " ORDER BY `id` ASC ";
		$sql .= $where.$order.$limit;
		$array_row = $this->oEasyGroup->query($sql);
		
		$array_count = $this->oEasyGroup->query("SELECT COUNT(*) as total FROM `{$this->oEasyGroup->table_name}` WHERE `isdel` = '1' {$where} ");
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
		
		$this->_view->assign('pager',$pager);
		$this->_view->assign('start',$this->_start);
		$this->_view->assign('limit',$this->_num);
		$this->_view->assign('total',$total);
		$this->_view->assign('page',$this->_page);
		
		$this->_view->assign('grouplist',$array_row);
		$this->_view->assign('baseUrl',BASE_URL);
		$this->_view->assign('tplDir',$this->tplDir);
		$this->_view->display($this->tplDir.'admin/system/group.list.tpl');
	}
	
	/**
	 * 新增
	 *
	 */
	public function addAction(){
		if (!IS_POST){
			/* 显示新增表单 */
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/group.add.tpl');
		}else{
			$data = array();
			$data['name'] = $_POST['name'];
			$data['home'] = $_POST['home'];
			$data['status'] = '1';
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());
			$data['ctime'] = date('Y-m-d H:i:s',time());
			if ( !$result = $this->oEasyGroup->insert($data) ){
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
			$data     = $this->oEasyGroup->get_one($id);
			if (empty($data)){
				$this->message($msg = array('content'=>'没有找到该记录！'));
				return;
			}
			$this->_view->assign('group', $data);
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/group.edit.tpl');
		}else{
			$data = array();
			$data['name'] = $_POST['name'];
			$data['home'] = $_POST['home'];
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());

			if ( !$result = $this->oEasyGroup->updateById($id,$data) ){
				$this->message($msg = array('content'=>'更新失败！'));
				return;
			}
			$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=group&act=list');
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
		if ( !$result = $this->oEasyGroup->updateByIds($id,$data) ){
			$this->message($msg = array('content'=>'删除失败！'));
			return;
		}
		$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=group&act=list');
		$this->message($msg = array('content'=>'删除成功！',
				'links'=>array($links),
					)
				);
	}
	
	
}
?>