<?php
class EasyGroupModel extends BaseModel {
	public function __construct() {
		parent::__construct();
		$this->table_name = $this->_prefix.'group';
		$this->dbw = $this->getDb('dbw');
		$this->dbr = $this->getDb('dbr');
	}

	public function insert($data){
		$result = '';
		if( isset($data) ){
			$sql = "INSERT INTO `{$this->table_name}`(`name`,`status`,`home`,`isdel`,`uptime`,`ctime`) ";
			$sql .= " VALUES('{$data['name']}','{$data['status']}','{$data['home']}','{$data['isdel']}','{$data['uptime']}','{$data['ctime']}')";
			$result = $this->dbw->query($sql);
		}
		return $result;
	}

	public function query($sql){
		$row = $this->dbr->fetchAll($sql);
		return $row;
	}

	public function get_one($id){
		$sql = "SELECT * FROM `{$this->table_name}` WHERE `id` = '$id'";
		$row = $this->dbr->fetchRow($sql);
		return $row;
	}

	public function getRecordByCondition($condition){
		$sql = "SELECT * FROM `{$this->table_name}` WHERE  $condition ";
		$row = $this->dbr->fetchAll($sql);
		return $row;
	}

	public function getOneByCondition($condition){
		$sql = "SELECT * FROM `{$this->table_name}` WHERE  $condition ";
		$row = $this->dbr->fetchRow($sql);
		return $row;
	}
	
	public function updateById($id,$data){
		$updateDataStr = ' SET ';
		if( isset($data) && !empty($data) ){
			foreach($data as $key=>$value){
				$updateDataStr .= " `{$key}` = '{$value}' ,";
			}
			$updateDataStr = substr($updateDataStr,0,-1);
		}
		$sql = "UPDATE `{$this->table_name}` {$updateDataStr} WHERE `id` = '{$id}'";
		$result = $this->dbw->query($sql);
		return $result;
	}
	
	public function updateByIds($id,$data){
		$updateDataStr = ' SET ';
		if( isset($data) && !empty($data) ){
			foreach($data as $key=>$value){
				$updateDataStr .= " `{$key}` = '{$value}' ,";
			}
			$updateDataStr = substr($updateDataStr,0,-1);
		}
		$sql = "UPDATE `{$this->table_name}` {$updateDataStr} WHERE `id` IN ('{$id}')";
		$result = $this->dbw->query($sql);
		return $result;
	}

	public function deleteById($id){
		$sql = "DELETE FROM `{$this->table_name}` WHERE `id` = '{$id}'";
		$result = $this->dbw->query($sql);
		return $result;
	}
	
	public function deleteByIds($ids){
		$sql = "DELETE FROM `{$this->table_name}` WHERE `id` IN ('{$ids}')";
		$result = $this->dbw->query($sql);
		return $result;
	}
	
}
?>