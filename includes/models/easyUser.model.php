<?php
class EasyModel extends BaseModel {
	public function __construct() {
		parent::__construct();
	}

	public function auth($user_name, $password){
		$result = 0;
		if( $user_name == $this->users[$user_name]['user_name'] && $password == $this->users[$user_name]['passwd'] ){
			$result = $this->users[$user_name]['user_name'];
		}
		return $result;
	}
	
	public function get($user_name){
		return $this->users[$user_name];
	}

}
?>