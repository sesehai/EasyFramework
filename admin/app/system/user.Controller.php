<?php
require(APP_ROOT .'/app/system/common.Controller.php');
class UserController extends CommonController {
	public function __construct(){
		parent::__construct();
		$this->_check_privs();
		Base::import('utils.lib');
		$this->_utils = new UtilsLib();
		$this->oEasyUser = Base::M('easyUser');
		$this->oEasyUser->setMcEnable($value = false);
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
		$sql = "SELECT * FROM `{$this->oEasyUser->table_name}` WHERE `isdel` = '1' ";
		$where = '';
		$limit = " LIMIT {$this->_start},{$this->_num}";
		$order = " ORDER BY `id` ASC ";
		$sql .= $where.$order.$limit;
		$array_row = $this->oEasyUser->query($sql);
		
		$array_count = $this->oEasyUser->query("SELECT COUNT(*) as total FROM `{$this->oEasyUser->table_name}` WHERE `isdel` = '1' {$where} ");
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
		
		$this->_view->assign('userlist',$array_row);
		$this->_view->assign('baseUrl',BASE_URL);
		$this->_view->assign('tplDir',$this->tplDir);
		$this->_view->display($this->tplDir.'admin/system/user.list.tpl');
	}
	
	/**
	 * 新增
	 *
	 */
	public function addAction(){
		if (!IS_POST){
			/* 显示新增表单 */
			$menu = $this->getMenu();
			
			$array_groups = $this->getGroup();
			$array_department = $this->getDepartment();
			$array_apppartner = $this->getApppartner();
			$array_user = $this->getUser();
			
			$this->_view->assign('menu',$menu);
			$this->_view->assign('groups',$array_groups);
			$this->_view->assign('departments',$array_department);
			$this->_view->assign('apppartners',$array_apppartner);
			$this->_view->assign('users',$array_user);
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/user.add.tpl');
		}else{
			$data = array();
			$data['name'] = $_POST['name'];
			$data['passwd'] = md5($_POST['passwd']);
			$data['realname'] = $_POST['realname'];
			$data['gid'] = $_POST['gid'];
			$data['privs'] = isset($_POST['privs']) && is_array($_POST['privs']) ? implode(',',$_POST['privs']) : '';
			$data['last_ip'] = '';
			$data['lastlogin'] = '';
			$data['status'] = '1';
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());
			$data['ctime'] = date('Y-m-d H:i:s',time());
			
			if( empty($data['name']) ){
				$this->message($msg = array('content'=>"请填写用户名！"));
				return;
			}
			
			if( $this->checkNameIsExist($data['name']) ){
				$this->message($msg = array('content'=>"用户名: {$data['name']} 已存在，请更换其他用户名！"));
				return;
			}
			
			if ( !$result = $this->oEasyUser->insertGetLastId($data) ){
				$this->message($msg = array('content'=>'新增失败！'));
				return;
			}
			
			$privs_data['owner_id'] = $result;
			$privs_data['department_ids'] = isset($_POST['department_ids']) && is_array($_POST['department_ids']) ? implode(',',$_POST['department_ids']) : '';
			$privs_data['user_ids'] = isset($_POST['user_ids']) && is_array($_POST['user_ids']) ? implode(',',$_POST['user_ids']) : '';
			$privs_data['apppartner_ids'] = isset($_POST['apppartner_ids']) && is_array($_POST['apppartner_ids']) ? implode(',',$_POST['apppartner_ids']) : '';
			$privs_data['isdel'] = '1';
			$privs_data['uptime'] = date('Y-m-d H:i:s',time());
			$privs_data['ctime'] = date('Y-m-d H:i:s',time());
			$oMclientDataprivs = Base::M('mclientDataprivs');
			$oMclientDataprivs->insert($privs_data);
			
			$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=user&act=list');
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
			$data     = $this->oEasyUser->get_one($id);
			if (empty($data)){
				$this->message($msg = array('content'=>'没有找到该记录！'));
				return;
			}
			
			
			$array_groups = $this->getGroup();
			
			$menu = $this->getMenu();
			$isChecked = isset($data['privs']) ? explode(',',$data['privs']) : array(); 
			$isall = in_array('all',$isChecked) ? 'isall' : 'notall';
			
			$array_dataprivs = $this->getDataprivsByOwnerid($id);
			$array_department = $this->getDepartment();
			$isChecked_department = isset($array_dataprivs['department_ids']) ? explode(',',$array_dataprivs['department_ids']) : array(); 
			$array_apppartner = $this->getApppartner();
			$isChecked_apppartner = isset($array_dataprivs['apppartner_ids']) ? explode(',',$array_dataprivs['apppartner_ids']) : array(); 
			$array_user = $this->getUser();
			$isChecked_user = isset($array_dataprivs['user_ids']) ? explode(',',$array_dataprivs['user_ids']) : array(); 
			
			$this->_view->assign('department', $array_department);
			$this->_view->assign('isChecked_department', $isChecked_department);
			
			$this->_view->assign('apppartner', $array_apppartner);
			$this->_view->assign('isChecked_apppartner', $isChecked_apppartner);
			
			$this->_view->assign('users', $array_user);
			$this->_view->assign('isChecked_user', $isChecked_user);
			
			$this->_view->assign('isall', $isall);
			$this->_view->assign('menu', $menu);
			$this->_view->assign('isChecked', $isChecked);
			
			$this->_view->assign('groups',$array_groups);
			$this->_view->assign('user', $data);
			$this->_view->assign('baseUrl',BASE_URL);
			$this->_view->assign('tplDir',$this->tplDir);
			$this->_view->display($this->tplDir.'admin/system/user.edit.tpl');
		}else{
			$data = array();
			if( isset($_POST['passwd']) && trim($_POST['passwd']) != '' ){
				$data['passwd'] = md5($_POST['passwd']);
			}
			$data['realname'] = $_POST['realname'];
			$data['gid'] = $_POST['gid'];
			$data['privs'] = isset($_POST['privs']) && is_array($_POST['privs']) ? implode(',',$_POST['privs']) : '';
			$data['status'] = '1';
			$data['isdel'] = '1';
			$data['uptime'] = date('Y-m-d H:i:s',time());

			if ( !$result = $this->oEasyUser->updateById($id,$data) ){
				$this->message($msg = array('content'=>'更新失败！'));
				return;
			}
			$privs_data['department_ids'] = isset($_POST['department_ids']) && is_array($_POST['department_ids']) ? implode(',',$_POST['department_ids']) : '';
			$privs_data['user_ids'] = isset($_POST['user_ids']) && is_array($_POST['user_ids']) ? implode(',',$_POST['user_ids']) : '';
			$privs_data['apppartner_ids'] = isset($_POST['apppartner_ids']) && is_array($_POST['apppartner_ids']) ? implode(',',$_POST['apppartner_ids']) : '';
			$privs_data['uptime'] = date('Y-m-d H:i:s',time());
			$oMclientDataprivs = Base::M('mclientDataprivs');
			$dataprivs = $oMclientDataprivs->getOneByCondition(" `owner_id` = '{$id}' ");
			if( isset($dataprivs) && !empty($dataprivs) ){
				$oMclientDataprivs->updateById($dataprivs['id'],$privs_data);
			}else{
				$privs_data['isdel'] = '1';
				$privs_data['owner_id'] = $id;
				$privs_data['ctime'] = date('Y-m-d H:i:s',time());
				$oMclientDataprivs->insert($privs_data);
			}
			
			
			$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=user&act=list');
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
		if ( !$result = $this->oEasyUser->updateByIds($id,$data) ){
			$this->message($msg = array('content'=>'删除失败！'));
			return;
		}
		$oMclientDataprivs = Base::M('mclientDataprivs');
		$oMclientDataprivs->updateByOwnerIds($id,$data);
		
		$links[] = array('text'=>'列表',
						'href'=>BASE_URL.'?mod=system&ctl=user&act=list');
		$this->message($msg = array('content'=>'删除成功！',
				'links'=>array($links),
					)
				);
	}
	
	/**
	 * 取分组
	 *
	 */
	private function getGroup(){
		$array_group = array();
		$oMclientGroup = Base::M('mclientGroup');
		$oMclientGroup->setMcEnable($value = false);
		$array_group_rows = $oMclientGroup->query("SELECT `id`,`name` FROM `{$oMclientGroup->table_name}` WHERE `isdel` = '1' ");
		if( isset($array_group_rows) ){
			foreach($array_group_rows as $row){
				$array_group[$row['id']]= $row['name'];
			}
		}
		return $array_group;
	}
	
	/**
	 * 取菜单
	 *
	 */
	private function getMenu(){
		$array_menu = array();
		$oMclientMenu = Base::M('mclientMenu');
		$oMclientMenu->setMcEnable($value = false);
		$array_menu_rows = $oMclientMenu->query("SELECT `id`,`text` FROM `{$oMclientMenu->table_name}` WHERE `parent` = '0' AND `isdel` = '1' ");
		if( isset($array_menu_rows) ){
			foreach($array_menu_rows as $row){
				$array_menu[$row['id']]['name']= $row['text'];
				$array_submenu_rows = $oMclientMenu->query("SELECT `id`,`text`,`mod_text`,`ctl_text`,`act_text` FROM `{$oMclientMenu->table_name}` WHERE `parent` = '{$row['id']}' AND `isdel` = '1' ");
				if( isset($array_submenu_rows) ){
					foreach($array_submenu_rows as $row_sub){
						$array_menu[$row['id']]['children']['name'][] = $row_sub['text'];
						$array_menu[$row['id']]['children']['value'][] = $row_sub['id'];
					}
				}
			}
		}
		return $array_menu;
	}
	
	/**
	 * 取部门
	 *
	 */
	private function getDepartment(){
		$array_department = array();
		$oMclientDepartment = Base::M('mclientDepartment');
		$oMclientDepartment->setMcEnable($value = false);
		$array_department_rows = $oMclientDepartment->query("SELECT `id`,`name` FROM `{$oMclientDepartment->table_name}` WHERE `isdel` = '1' ");
		if( isset($array_department_rows) ){
			foreach($array_department_rows as $row){
				$array_department['name'][$row['id']]= $row['name'];
				$array_department['value'][$row['id']]= $row['id'];
			}
		}
		return $array_department;
	}
	
	/**
	 * 取渠道
	 *
	 */
	private function getApppartner(){
		$array_apppartner = array();
		$oMclientApppartner = Base::M('mclientApppartner');
		$oMclientApppartner->setMcEnable($value = false);
		$array_apppartner_rows = $oMclientApppartner->query("SELECT `id`,`pid`,`name` FROM `{$oMclientApppartner->table_name}` WHERE `isdel` = '1' ");
		if( isset($array_apppartner_rows) ){
			foreach($array_apppartner_rows as $row){
				$array_apppartner['name'][$row['id']]= $row['name'];
				$array_apppartner['value'][$row['id']]= $row['pid'];
			}
		}
		return $array_apppartner;
	}
	
	/**
	 * 取用户
	 *
	 */
	private function getUser(){
		$array_user = array();
		$oEasyUser = Base::M('mclientUser');
		$oEasyUser->setMcEnable($value = false);
		$array_user_rows = $oEasyUser->query("SELECT `id`,`name` FROM `{$oEasyUser->table_name}` WHERE `isdel` = '1' ");
		if( isset($array_user_rows) ){
			foreach($array_user_rows as $row){
				$array_user['name'][$row['id']]= $row['name'];
				$array_user['value'][$row['id']]= $row['id'];
			}
		}
		return $array_user;
	}
	
	/**
	 * 取数据权限 根据 owner_id
	 *
	 */
	private function getDataprivsByOwnerid($owner_id){
		$array_dataprivs = array();
		$oMclientDataprivs = Base::M('mclientDataprivs');
		$oMclientDataprivs->setMcEnable($value = false);
		$condition = " `owner_id` = '{$owner_id}' AND `isdel` = '1' ";
		$array_dataprivs = $oMclientDataprivs->getOneByCondition($condition);
		return $array_dataprivs;
	}
	/**
	 * 检查用户是否存在 for ajax
	 *
	 */
	public function check_nameAction(){
		$info['name']    = (isset($_GET['name']) && !empty($_GET['name']))? $_GET['name']:'';
		$array_count = $this->oEasyUser->query("SELECT COUNT(*) as total FROM `{$this->oEasyUser->table_name}` WHERE `name` = '{$info['name'] }'  ");
		$total = isset($array_count[0]['total']) ? $array_count[0]['total'] : 0;
		if($total>=1){
			echo "false";
		}
		else{
			echo "true";
		}
	}
	
	/**
	 * 检查用户是否存在 for app
	 *
	 */
	private function checkNameIsExist($name){
		$result = FALSE;
		$array_count = $this->oEasyUser->query("SELECT COUNT(*) as total FROM `{$this->oEasyUser->table_name}` WHERE `name` = '{$name}'  ");
		$total = isset($array_count[0]['total']) ? $array_count[0]['total'] : 0;
		if($total>=1){
			$result = TRUE;
		}
		return $result;
	}
	
}
?>